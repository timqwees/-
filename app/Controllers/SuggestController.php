<?php declare(strict_types=1);

namespace App\Controllers;

/**
 * Подсказки по локации (город / район) для поля «Где объект».
 *
 * Источники (все опрашиваются ПАРАЛЛЕЛЬНО, ответ = время самого быстрого):
 *   1) DaData Suggestions API — как в проекте mandomemori:
 *      POST https://suggestions.dadata.ru/suggestions/api/4_1/rs/suggest/address
 *      Authorization: Token <DADATA_API_KEY>
 *      Работает, если у токена включён feature SUGGESTIONS.
 *   2) Open-Meteo Geocoding — бесплатно, без ключа, отлично ищет города по префиксу.
 *   3) Nominatim (OpenStreetMap) — бесплатно, без ключа, дополнительные населённые пункты.
 *
 * Токен DaData остаётся на сервере и не отдаётся в браузер.
 *
 * Эндпоинт: POST /api/suggest/location  {"query":"Хим"}
 * Ответ:    {"suggestions":[{"value":"Химки, Московская область","label":"Химки","hint":"Россия"}]}
 *
 * Ускорение:
 *   - параллельные запросы (curl_multi) вместо последовательных;
 *   - кэш ответов на 1 час + stale-while-revalidate (старый кэш отдаём сразу);
 *   - жёсткие таймауты (2.5 с), провайдер не может «зависнуть» на всю страницу;
 *   - мгновенный локальный подбор по словарю городов на стороне клиента.
 */
class SuggestController
{
	private const DADATA_URL = 'https://suggestions.dadata.ru/suggestions/api/4_1/rs/suggest/address';
	private const NOMINATIM_URL = 'https://nominatim.openstreetmap.org/search';
	private const OPEN_METEO_URL = 'https://geocoding-api.open-meteo.com/v1/search';
	private const PHOTON_URL = 'https://photon.komoot.io/api/';

	private const TIMEOUT = 2.5;         // секунд на провайдера по умолчанию
	private const TIMEOUT_OPEN_METEO = 2.0; // open-meteo иногда принимает соединение и не отвечает
	private const TIMEOUT_NOMINATIM = 2.5;
	private const TOTAL_BUDGET = 1.0;    // общий бюджет ожидания ответа провайдеров, сек
	private const GRACE = 0.4;           // добор времени после ответа основного провайдера, сек
	private const BUDGET_ADDRESS = 3.0;  // для адресных запросов ждём дольше (улицы ищем долго)
	private const CACHE_TTL = 3600;      // 1 час
	private const CACHE_NEG_TTL = 300;   // 5 минут для пустых ответов (опечатки)
	private const CACHE_STALE_MAX = 604800; // 7 дней — старый кэш отдаём сразу, обновляем в фоне
	private const RATE_WINDOW = 60;      // окно rate-limit, сек
	private const RATE_MAX = 120;        // обращений к провайдерам с одного IP за окно
	private const COUNT = 10;            // сколько подсказок отдаём
	private const NOMINATIM_MIN_GAP = 1.05; // политика OSM: не чаще 1 запроса в секунду

	/** @var bool Nominatim пропущен из-за троттлинга — результат неполный */
	private static bool $partial = false;

	/** @var array<int,array{0:string,1:string}>|null Словарь городов */
	private static ?array $cities = null;

	/**
	 * POST /api/suggest/location
	 */
	public static function location(): void
	{
		$query = self::readQuery();
		if ($query === '') {
			self::respond(['suggestions' => []]);
			return;
		}

		$cacheKey = md5('loc|' . mb_strtolower($query));

		// Свежий кэш — отдаём мгновенно (единицы миллисекунд)
		$cached = self::cacheGet($cacheKey);
		if ($cached !== null) {
			self::respond(['suggestions' => $cached]);
			return;
		}

		// Устаревший кэш — отдаём сразу, обновляем в фоне (stale-while-revalidate)
		$stale = self::cacheGetStale($cacheKey);
		if ($stale !== null) {
			self::respond(['suggestions' => $stale]);
			self::refreshInBackground($query, $cacheKey);
			return;
		}

		// Ограничение — только на обращения к внешним провайдерам (кэш не считаем)
		if (!self::allowRequest()) {
			self::respond(['suggestions' => [], 'error' => 'rate_limited'], 429);
			return;
		}

		$suggestions = self::fetchAll($query);
		// Если Nominatim пропустили по троттлингу — список неполный,
		// кэшируем ненадолго, чтобы следующий запрос мог собрать его полнее
		self::cacheSet($cacheKey, $suggestions, self::$partial ? self::CACHE_NEG_TTL : self::CACHE_TTL);

		self::respond(['suggestions' => $suggestions]);
	}

	/**
	 * Параллельный сбор подсказок: все провайдеры опрашиваются одновременно,
	 * поэтому задержка = максимум по провайдерам, а не сумма.
	 *
	 * @return array<int,array{value:string,label:string,hint:string}>
	 */
	private static function fetchAll(string $query): array
	{
		$specs = [];

		$token = self::dadataToken();
		if ($token !== '' && !self::daDataDisabled()) {
			$specs['dadata'] = [
				'url' => self::DADATA_URL,
				'payload' => ['query' => $query, 'count' => self::COUNT],
				'headers' => [
					'Content-Type: application/json',
					'Accept: application/json',
					'Authorization: Token ' . $token,
				],
			];
		}

		$specs['openmeteo'] = [
			'url' => self::OPEN_METEO_URL . '?' . http_build_query([
				'name' => $query,
				'count' => self::COUNT,
				'language' => 'ru',
				'format' => 'json',
			]),
			'payload' => null,
			'headers' => ['Accept: application/json'],
			'timeout' => self::TIMEOUT_OPEN_METEO,
		];

		// Photon — ещё один бесплатный источник без ключа (подстраховка)
		$parsed = self::parseQuery($query);
		$photonQuery = $parsed['clean'] !== '' ? $parsed['clean'] : $query;
		$specs['photon'] = [
			'url' => self::PHOTON_URL . '?' . http_build_query(['q' => $photonQuery, 'limit' => self::COUNT]),
			'payload' => null,
			'headers' => ['Accept: application/json'],
			'timeout' => self::TIMEOUT,
		];

		// Nominatim не чаще 1 запроса в секунду. Для обычных запросов — просто
		// пропускаем (это подстраховка), для адресных — ждём: там он единственный
		// бесплатный источник улиц и домов.
		self::$partial = false;
		if (self::nominatimAllowed($parsed['isAddress'])) {
			$specs['nominatim'] = [
				'url' => self::NOMINATIM_URL . '?' . http_build_query(self::nominatimParams($query)),
				'payload' => null,
				'headers' => ['Accept: application/json', 'Accept-Language: ru'],
				'timeout' => self::TIMEOUT_NOMINATIM,
			];
		}

		// Локальный словарь — отдаём сразу, он не зависит от сети
		$items = self::itemsLocal($query);

		// Если по словарю уже есть точный ответ или несколько подходящих городов —
		// сеть не трогаем вообще (ответ мгновенный)
		if (self::localIsEnough($items, $query) && !$parsed['isAddress']) {
			return self::finalize($items);
		}

		// Для адреса ждём дольше: улицы ищем только в Nominatim/Photon
		$raw = self::requestMulti($specs, $parsed['isAddress'] ? self::BUDGET_ADDRESS : self::TOTAL_BUDGET);

		if (isset($raw['dadata'])) {
			$status = (int)($raw['dadata']['status'] ?? 0);
			if ($status === 401 || $status === 403) {
				self::markDaDataDisabled();
			} else {
				$items = array_merge($items, self::itemsDaData($raw['dadata']['json'] ?? null));
			}
		}

		$items = array_merge($items, self::itemsOpenMeteo($raw['openmeteo']['json'] ?? null));
		$items = array_merge($items, self::itemsNominatim($raw['nominatim']['json'] ?? null, $parsed['isAddress']));
		$items = array_merge($items, self::itemsPhoton($raw['photon']['json'] ?? null));

		return self::finalize($items);
	}

	/**
	 * Обновление кэша в фоне: пользователь уже получил ответ,
	 * процесс дорабатывает данные для следующего запроса.
	 */
	private static function refreshInBackground(string $query, string $cacheKey): void
	{
		if (function_exists('fastcgi_finish_request')) {
			@fastcgi_finish_request();
		} else {
			@flush();
			@ob_flush();
		}
		ignore_user_abort(true);

		$suggestions = self::fetchAll($query);
		self::cacheSet($cacheKey, $suggestions);
	}

	// ── Источники ──────────────────────────────────────────────────────────────

	/**
	 * Разбор запроса на город / улицу / дом.
	 *
	 * «москва большая черемушкинская дом 19» →
	 *   city   = «Москва»
	 *   street = «большая черемушкинская 19»
	 *   clean  = «Большая Черемушкинская 19, Москва»
	 *
	 * @return array{city:string,street:string,clean:string,isAddress:bool}
	 */
	private static function parseQuery(string $query): array
	{
		$city = '';
		$rest = $query;

		// Город из локального словаря — самый длинный подходящий префикс
		$cities = self::cities();
		$lower = mb_strtolower($query);
		foreach ($cities as $item) {
			$name = mb_strtolower((string)($item[0] ?? ''));
			if ($name === '' || mb_strlen($name) < 3) {
				continue;
			}
			if (str_starts_with($lower, $name . ' ') || $lower === $name) {
				if (mb_strlen($name) > mb_strlen($city)) {
					$city = (string)$item[0];
					$rest = trim(mb_substr($query, mb_strlen($name)));
				}
			}
		}

		// Шумные слова убираем — геокодеры их не понимают
		$noise = ['дом', 'д.', 'д', 'кв', 'кв.', 'квартира', 'стр', 'строение', 'корпус', 'корп', 'к', 'подъезд', 'под'];
		$parts = preg_split('/[\s,]+/u', $rest) ?: [];
		$cleanParts = [];
		foreach ($parts as $part) {
			$lowerPart = mb_strtolower($part);
			if ($lowerPart === '' || in_array($lowerPart, $noise, true)) {
				continue;
			}
			$cleanParts[] = $part;
		}
		$street = trim(implode(' ', $cleanParts));

		// Если город уже есть в начале — переносим его в конец: «улица, город»
		if ($city !== '' && $street !== '') {
			$clean = $street . ', ' . $city;
		} else {
			$clean = $street !== '' ? $street : $query;
		}

		$isAddress = $street !== '' && (
			str_contains($query, ' ') || mb_strlen($street) > 2
		);

		return [
			'city' => $city,
			'street' => $street,
			'clean' => $clean,
			'isAddress' => $isAddress,
		];
	}

	/**
	 * Достаточно ли локального словаря, чтобы не ходить в сеть:
	 * есть точное совпадение или несколько городов, начинающихся с запроса.
	 *
	 * @param array<int,array<string,mixed>> $items
	 */
	private static function localIsEnough(array $items, string $query): bool
	{
		$exact = 0;
		$prefix = 0;
		foreach ($items as $item) {
			$label = (string)($item['label'] ?? '');
			if (mb_strtolower($label) === mb_strtolower($query)) {
				$exact++;
			}
			if (($item['_rank'] ?? 3) === 0) {
				$prefix++;
			}
		}
		return $exact > 0 || $prefix >= 3;
	}

	/**
	 * Локальный словарь городов (app/Data/suggest_cities.php) — мгновенный
	 * ответ без обращения к внешним API. Актуально, когда провайдеры тормозят.
	 *
	 * @return array<int,array<string,mixed>>
	 */
	private static function itemsLocal(string $query): array
	{
		$cities = self::cities();
		if ($cities === []) {
			return [];
		}

		$q = mb_strtolower($query);
		$items = [];
		foreach ($cities as $city) {
			$name = (string)($city[0] ?? '');
			$region = (string)($city[1] ?? '');
			if ($name === '') {
				continue;
			}
			$q = mb_strtolower($query);
			if (mb_stripos($name, $query) === false) {
				// Адресный запрос («москва ул. Мира 1») — предлагаем сам город
				if (mb_strlen($q) > mb_strlen($name) && str_starts_with($q, mb_strtolower($name) . ' ')) {
					$items[] = [
						'value' => ($region !== '' && $region !== $name) ? $name . ', ' . $region : $name,
						'label' => $name,
						'hint' => ($region !== '' && $region !== $name) ? $region : '',
						'_rank' => 2,
					];
				}
				continue;
			}

			// Точное начало названия — приоритетнее вхождения в середине
			$prefix = mb_strtolower($name);
			$rank = str_starts_with($prefix, $q) ? 0 : 3;

			$items[] = [
				'value' => ($region !== '' && $region !== $name) ? $name . ', ' . $region : $name,
				'label' => $name,
				'hint' => ($region !== '' && $region !== $name) ? $region : '',
				'_rank' => $rank,
			];
			if (count($items) >= self::COUNT) {
				break;
			}
		}
		// Точные совпадения — первыми
		usort($items, static fn(array $a, array $b): int => ($a['_rank'] ?? 3) <=> ($b['_rank'] ?? 3));

		return $items;
	}

	/**
	 * Словарь городов (кеш в статике).
	 *
	 * @return array<int,array{0:string,1:string}>
	 */
	private static function cities(): array
	{
		if (self::$cities !== null) {
			return self::$cities;
		}

		$file = dirname(__DIR__, 2) . '/app/Data/suggest_cities.php';
		if (is_file($file)) {
			$data = require $file;
			if (is_array($data)) {
				self::$cities = $data;
			}
		}
		if (self::$cities === null) {
			self::$cities = [];
		}

		return self::$cities;
	}

	/**
	 * DaData Suggestions API (как в mandomemori).
	 *
	 * @return array<int,array<string,mixed>>
	 */
	private static function itemsDaData(?array $json): array
	{
		$raw = $json['suggestions'] ?? [];
		if (!is_array($raw)) {
			return [];
		}

		$items = [];
		foreach ($raw as $s) {
			if (!is_array($s)) {
				continue;
			}
			$value = trim((string)($s['value'] ?? ''));
			if ($value === '') {
				continue;
			}
			$data = is_array($s['data'] ?? null) ? $s['data'] : [];

			$city = trim((string)($data['city'] ?? ''));
			$settlement = trim((string)($data['settlement'] ?? ''));
			$district = trim((string)($data['city_district'] ?? ''));
			$region = trim((string)($data['region'] ?? ''));
			$street = trim((string)($data['street'] ?? ''));
			$house = trim((string)($data['house'] ?? ''));

			$label = $city !== '' ? $city : ($settlement !== '' ? $settlement : ($district !== '' ? $district : $value));

			// Что попадёт в поле «Где объект»
			$parts = [];
			if ($city !== '') {
				$parts[] = $city;
			} elseif ($settlement !== '') {
				$parts[] = $settlement;
			}
			if ($street !== '') {
				$parts[] = trim($street . ' ' . $house);
			} elseif ($district !== '' && $district !== $label) {
				$parts[] = $district;
			}
			$valueOut = implode(', ', array_filter($parts));
			if ($valueOut === '') {
				$valueOut = $value;
			}

			// Подпись: регион + «внутри» (улица/дом), чтобы город и адрес не путались
			$hintParts = [];
			if ($region !== '' && $region !== $city && $region !== $label) {
				$hintParts[] = $region;
			}
			if ($street !== '') {
				$hintParts[] = trim($street . ' ' . $house);
			}

			$items[] = [
				'value' => $valueOut,
				'label' => $label,
				'hint' => implode(' · ', array_filter($hintParts)),
				'_rank' => self::rank($city !== '' || $settlement !== '', $street !== '', $house !== ''),
			];
		}

		return $items;
	}

	/**
	 * Open-Meteo Geocoding — основной источник для городов и районов.
	 * Бесплатный, без ключа, ищет населённые пункты по префиксу на русском.
	 *
	 * @return array<int,array<string,mixed>>
	 */
	private static function itemsOpenMeteo(?array $json): array
	{
		$results = $json['results'] ?? [];
		if (!is_array($results)) {
			return [];
		}

		$items = [];
		foreach ($results as $r) {
			if (!is_array($r)) {
				continue;
			}
			$name = trim((string)($r['name'] ?? ''));
			if ($name === '') {
				continue;
			}
			$region = trim((string)($r['admin1'] ?? ''));
			$district = trim((string)($r['admin2'] ?? ''));
			$country = trim((string)($r['country'] ?? ''));
			$countryCode = strtolower(trim((string)($r['country_code'] ?? '')));
			$code = strtoupper(trim((string)($r['feature_code'] ?? '')));

			// В поле «Где объект» — город и область, чтобы менеджеру было понятно, откуда заявка
			$valueOut = $name;
			if ($region !== '' && mb_strtolower($region) !== mb_strtolower($name)) {
				$valueOut .= ', ' . $region;
			}

			$hintParts = [];
			if ($district !== '' && mb_strtolower($district) !== mb_strtolower($name)) {
				$hintParts[] = $district;
			}
			if ($country !== '' && mb_strtolower($country) !== mb_strtolower($region)) {
				$hintParts[] = $country;
			}

			// Города России — в начало, остальные страны — после
			$isCity = str_starts_with($code, 'PPL');
			$rank = $isCity ? ($countryCode === 'ru' ? 0 : 1) : 2;

			$items[] = [
				'value' => $valueOut,
				'label' => $name,
				'hint' => implode(' · ', array_filter($hintParts)),
				'_rank' => $rank,
			];
		}

		return $items;
	}

	/**
	 * Nominatim (OpenStreetMap) — дополнительные населённые пункты и районы.
	 *
	 * @return array<int,array<string,mixed>>
	 */
	private static function itemsNominatim(?array $json, bool $allowAddresses = false): array
	{
		if (!is_array($json)) {
			return [];
		}

		$items = [];
		foreach ($json as $row) {
			if (!is_array($row)) {
				continue;
			}
			$addr = is_array($row['address'] ?? null) ? $row['address'] : [];
			$name = trim((string)($row['name'] ?? ''));
			$type = trim((string)($row['type'] ?? ''));

			// Нас интересуют только населённые пункты и районы — реки, улицы и прочее отсекаем.
			// Для адресного запроса дополнительно берём улицы и дома.
			$isPlace = in_array($type, ['city', 'town', 'village', 'municipality', 'hamlet', 'county', 'state', 'administrative', 'suburb', 'borough', 'quarter'], true);
			$isAddressRow = in_array($type, ['house', 'building', 'apartments', 'residential', 'road', 'secondary', 'primary', 'tertiary', 'unclassified', 'living_street', 'pedestrian', 'footway', 'square', 'place', 'address'], true);
			if (!$isPlace && !($allowAddresses && $isAddressRow)) {
				continue;
			}

			$city = '';
			foreach (['city', 'town', 'village', 'municipality', 'hamlet'] as $key) {
				if (!empty($addr[$key])) {
					$city = trim((string)$addr[$key]);
					break;
				}
			}
			$district = '';
			foreach (['city_district', 'suburb', 'borough', 'county'] as $key) {
				if (!empty($addr[$key])) {
					$candidate = trim((string)$addr[$key]);
					// «городской округ Химки» при городе «Химки» — лишний шум
					if ($city !== '' && (mb_stripos($candidate, $city) !== false || mb_stripos($city, $candidate) !== false)) {
						continue;
					}
					$district = $candidate;
					break;
				}
			}
			$state = trim((string)($addr['state'] ?? ''));
			$country = trim((string)($addr['country'] ?? ''));
			$street = trim((string)($addr['road'] ?? ''));
			$house = trim((string)($addr['house_number'] ?? ''));

			// Для улицы/дома подпись — сама улица (+ дом), город уходит в подпись
			if (!$isPlace && ($street !== '' || $name !== '')) {
				$streetLabel = $street !== '' ? $street : $name;
				$label = $house !== '' ? trim($streetLabel . ' ' . $house) : $streetLabel;
				$valueOut = ($city !== '' ? $city . ', ' : '') . $label;

				$hintParts = [];
				if ($district !== '' && $district !== $label) {
					$hintParts[] = $district;
				}
				if ($state !== '' && $state !== $label && $state !== $city) {
					$hintParts[] = $state;
				}
				if ($country !== '' && $country !== $state) {
					$hintParts[] = $country;
				}

				$items[] = [
					'value' => $valueOut,
					'label' => $label,
					'hint' => implode(' · ', array_filter($hintParts)),
					'_rank' => $house !== '' ? 1 : 2,
				];
				continue;
			}

			$label = $city !== '' ? $city : ($name !== '' ? $name : $district);
			if ($label === '') {
				continue;
			}

			$parts = [];
			if ($city !== '' && $city !== $name) {
				$parts[] = $city;
			}
			if ($street !== '') {
				$parts[] = trim($street . ' ' . $house);
			} elseif ($district !== '' && $district !== $label) {
				$parts[] = $district;
			}
			if ($parts === [] && $name !== '') {
				$parts[] = $name;
			}
			$valueOut = implode(', ', array_filter($parts));

			$hintParts = [];
			if ($state !== '' && $state !== $label) {
				$hintParts[] = $state;
			}
			if ($country !== '' && $country !== $state) {
				$hintParts[] = $country;
			}
			if ($street !== '') {
				$hintParts[] = trim($street . ' ' . $house);
			}

			$isCity = in_array($type, ['city', 'town', 'village', 'municipality', 'county', 'state', 'administrative', 'suburb', 'hamlet'], true);
			$items[] = [
				'value' => $valueOut !== '' ? $valueOut : $label,
				'label' => $label,
				'hint' => implode(' · ', array_filter($hintParts)),
				'_rank' => self::rank($isCity, $street !== '', $house !== ''),
			];
		}

		return $items;
	}

	/**
	 * Photon (OpenStreetMap) — ещё один бесплатный источник без ключа.
	 *
	 * @return array<int,array<string,mixed>>
	 */
	private static function itemsPhoton(?array $json): array
	{
		$features = $json['features'] ?? [];
		if (!is_array($features)) {
			return [];
		}

		$items = [];
		foreach ($features as $f) {
			$props = is_array($f['properties'] ?? null) ? $f['properties'] : [];
			$name = trim((string)($props['name'] ?? ''));
			$city = trim((string)($props['city'] ?? ''));
			$district = trim((string)($props['district'] ?? ''));
			$county = trim((string)($props['county'] ?? ''));
			$state = trim((string)($props['state'] ?? ''));
			$street = trim((string)($props['street'] ?? ''));
			$house = trim((string)($props['housenumber'] ?? ''));
			$type = trim((string)($props['type'] ?? ''));

			if ($name === '' && $city === '') {
				continue;
			}

			$label = $city !== '' ? $city : $name;

			$parts = [];
			if ($city !== '' && $city !== $name) {
				$parts[] = $city;
			}
			if ($street !== '') {
				$parts[] = trim($street . ' ' . $house);
			} elseif ($district !== '' && $district !== $label) {
				$parts[] = $district;
			} elseif ($county !== '' && $county !== $label) {
				$parts[] = $county;
			}
			if ($parts === [] && $name !== '') {
				$parts[] = $name;
			}

			$hintParts = [];
			if ($state !== '' && $state !== $label) {
				$hintParts[] = $state;
			}
			if ($street !== '') {
				$hintParts[] = trim($street . ' ' . $house);
			}

			$isCity = in_array($type, ['city', 'town', 'village', 'district', 'county', 'state', 'locality', 'suburb'], true);
			$items[] = [
				'value' => implode(', ', array_filter($parts)) !== '' ? implode(', ', array_filter($parts)) : $name,
				'label' => $label,
				'hint' => implode(' · ', array_filter($hintParts)),
				'_rank' => self::rank($isCity, $street !== '', $house !== ''),
			];
		}

		return $items;
	}

	// ── Утилиты ───────────────────────────────────────────────────────────────

	/**
	 * Ранг подсказки: сначала города/населённые пункты, потом улицы, потом дома.
	 */
	private static function rank(bool $isCity, bool $hasStreet, bool $hasHouse): int
	{
		if ($isCity) {
			return 0;
		}
		if ($hasStreet && $hasHouse) {
			return 2;
		}
		return 1;
	}

	/**
	 * Сортировка по рангу + удаление дублей по label.
	 *
	 * @param array<int,array<string,mixed>> $items
	 * @return array<int,array{value:string,label:string,hint:string}>
	 */
	private static function finalize(array $items): array
	{
		usort($items, static fn(array $a, array $b): int => ($a['_rank'] ?? 3) <=> ($b['_rank'] ?? 3));

		$out = [];
		$seen = [];
		foreach ($items as $item) {
			$label = (string)($item['label'] ?? '');
			if ($label === '') {
				continue;
			}
			$key = mb_strtolower($label);
			if (isset($seen[$key])) {
				continue;
			}
			$seen[$key] = true;
			$out[] = [
				'value' => (string)$item['value'],
				'label' => $label,
				'hint' => (string)($item['hint'] ?? ''),
			];
			if (count($out) >= self::COUNT) {
				break;
			}
		}

		return $out;
	}

	/**
	 * Параллельные HTTP-запросы (curl_multi) — все провайдеры опрашиваются
	 * одновременно, поэтому задержка = максимум по провайдерам, а не сумма.
	 *
	 * @param array<string,array{url:string,payload:array<string,mixed>|null,headers:array<int,string>,timeout?:float}> $specs
	 * @return array<string,array{status:int,json:array<string,mixed>|null}>
	 */
	private static function requestMulti(array $specs, float $budget = self::TOTAL_BUDGET): array
	{
		if (!function_exists('curl_multi_init')) {
			return [];
		}

		$mh = curl_multi_init();
		$handles = [];

		foreach ($specs as $key => $spec) {
			$ch = curl_init($spec['url']);
			if ($ch === false) {
				continue;
			}
			curl_setopt_array($ch, [
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_TIMEOUT_MS => (int)(((float)($spec['timeout'] ?? self::TIMEOUT)) * 1000),
				CURLOPT_CONNECTTIMEOUT_MS => 2500,
				CURLOPT_HTTPHEADER => $spec['headers'],
				CURLOPT_USERAGENT => 'fss-site/1.0 (+https://xn--q1aah.xn--p1acf; location suggest)',
				CURLOPT_FOLLOWLOCATION => false,
				CURLOPT_ENCODING => '',
			]);
			if (!empty($spec['payload'])) {
				curl_setopt($ch, CURLOPT_POST, true);
				curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($spec['payload'], JSON_UNESCAPED_UNICODE));
			}
			curl_multi_add_handle($mh, $ch);
			$handles[$key] = $ch;
		}

		if ($handles === []) {
			curl_multi_close($mh);
			return [];
		}

		// Ждём не всех, а главного: как только основной провайдер ответил —
		// собираем результат (остальным даём короткий «добор» времени).
		$deadline = microtime(true) + $budget;
		$grace = 0.0;
		$primary = ['openmeteo', 'dadata'];
		$running = null;

		do {
			curl_multi_exec($mh, $running);

			$primaryDone = false;
			foreach ($primary as $key) {
				if (isset($handles[$key]) && (int)curl_getinfo($handles[$key], CURLINFO_RESPONSE_CODE) > 0) {
					$primaryDone = true;
					break;
				}
			}

			if ($primaryDone) {
				if ($grace === 0.0) {
					$grace = microtime(true) + self::GRACE;
				}
			}

			$stop = $primaryDone
				? (microtime(true) >= $grace)
				: (microtime(true) >= $deadline);

			if ($running > 0 && !$stop) {
				curl_multi_select($mh, 0.05);
			}
		} while ($running > 0 && !$stop);

		$out = [];
		foreach ($handles as $key => $ch) {
			$body = curl_multi_getcontent($ch);
			$status = (int)curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
			$json = null;
			if (is_string($body) && $status >= 200 && $status < 300) {
				$decoded = json_decode($body, true);
				$json = is_array($decoded) ? $decoded : null;
			}
			$out[$key] = ['status' => $status, 'json' => $json];
			curl_multi_remove_handle($mh, $ch);
			curl_close($ch);
		}
		curl_multi_close($mh);

		return $out;
	}

	/**
	 * Токен DaData из окружения.
	 */
	private static function dadataToken(): string
	{
		return (string)($_ENV['DADATA_API_KEY'] ?? $_SERVER['DADATA_API_KEY'] ?? getenv('DADATA_API_KEY') ?? '');
	}

	/**
	 * Токен DaData не работает (нет feature SUGGESTIONS / невалиден) —
	 * запоминаем это на сутки, чтобы не делать заведомо бесполезный запрос.
	 */
	private static function daDataDisabled(): bool
	{
		$file = self::cacheDir() . 'dadata-disabled';
		if (!is_file($file)) {
			return false;
		}
		if ((time() - (int)filemtime($file)) > 86400) {
			@unlink($file);
			return false;
		}
		return true;
	}

	private static function markDaDataDisabled(): void
	{
		$dir = self::cacheDir();
		if (!is_dir($dir)) {
			@mkdir($dir, 0755, true);
		}
		@file_put_contents($dir . 'dadata-disabled', (string)time());
	}

	/**
	 * Параметры запроса к Nominatim.
	 *
	 * @return array<string,string>
	 */
	private static function nominatimParams(string $query): array
	{
		$parsed = self::parseQuery($query);
		$params = [
			'q' => $parsed['clean'] !== '' ? $parsed['clean'] : $query,
			'format' => 'jsonv2',
			'addressdetails' => 1,
			'namedetails' => 1,
			'limit' => self::COUNT,
			'accept-language' => 'ru',
		];

		// По умолчанию ищем по России (у компании только российские гео-страницы),
		// код можно переопределить: FSS_SUGGEST_COUNTRY=ru,by
		$country = (string)($_ENV['FSS_SUGGEST_COUNTRY'] ?? getenv('FSS_SUGGEST_COUNTRY') ?? 'ru');
		if ($country !== '') {
			$params['countrycodes'] = $country;
		}

		return $params;
	}

	/**
	 * Nominatim: не больше 1 запроса в секунду (политика OSM).
	 * Если пауза не выдержана — провайдер просто пропускается, ждать не будем.
	 */
	private static function nominatimAllowed(bool $wait = false): bool
	{
		$file = self::nominatimStampFile();
		if (!is_file($file)) {
			@file_put_contents($file, (string)microtime(true));
			return true;
		}

		$last = (float)file_get_contents($file);
		$gap = microtime(true) - $last;
		if ($gap >= self::NOMINATIM_MIN_GAP) {
			@file_put_contents($file, (string)microtime(true));
			return true;
		}

		// Для адресных запросов Nominatim — единственный бесплатный источник улиц,
		// поэтому ждём недостающую паузу, а не пропускаем его
		if ($wait) {
			usleep((int)((self::NOMINATIM_MIN_GAP - $gap) * 1000000));
			@file_put_contents($file, (string)microtime(true));
			return true;
		}

		return false;
	}

	private static function nominatimStampFile(): string
	{
		$dir = sys_get_temp_dir() . '/fss-nominatim/';
		if (!is_dir($dir)) {
			@mkdir($dir, 0755, true);
		}
		return $dir . 'last.txt';
	}

	/**
	 * Тело запроса: JSON {"query":"..."} — так же, как отправляет JS.
	 */
	private static function readQuery(): string
	{
		$query = '';
		$raw = (string)file_get_contents('php://input');
		if ($raw !== '') {
			$decoded = json_decode($raw, true);
			if (is_array($decoded)) {
				$query = (string)($decoded['query'] ?? '');
			}
		}
		if ($query === '') {
			$query = (string)($_POST['query'] ?? $_GET['query'] ?? '');
		}

		$query = trim(strip_tags($query));
		if (mb_strlen($query, 'UTF-8') > 120) {
			$query = mb_substr($query, 0, 120, 'UTF-8');
		}

		return $query;
	}

	/**
	 * Простой rate-limit по IP (антибот-слой, как в FormHandler).
	 */
	private static function allowRequest(): bool
	{
		$ip = (string)($_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR'] ?? 'unknown');
		$ip = trim(explode(',', $ip)[0]);
		$dir = sys_get_temp_dir() . '/fss-ratelimit/';
		if (!is_dir($dir)) {
			@mkdir($dir, 0755, true);
		}
		$file = $dir . md5('suggest|' . $ip) . '.json';
		$now = time();

		$history = [];
		if (is_file($file)) {
			$history = json_decode((string)file_get_contents($file), true) ?: [];
			$history = array_values(array_filter($history, static fn($t) => ($now - (int)$t) < self::RATE_WINDOW));
		}
		if (count($history) >= self::RATE_MAX) {
			return false;
		}

		$history[] = $now;
		@file_put_contents($file, json_encode($history));

		return true;
	}

	/**
	 * Мини-кэш ответов (уменьшает число обращений к API).
	 *
	 * @return array<int,array{value:string,label:string,hint:string}>|null
	 */
	private static function cacheGet(string $key): ?array
	{
		$file = self::cacheDir() . $key . '.json';
		if (!is_file($file)) {
			return null;
		}
		$age = time() - (int)filemtime($file);
		// Пустой ответ (опечатка) держим недолго, чтобы не спрашивать провайдеров зря
		$ttl = $age > 0 && self::cacheIsEmpty($file) ? self::CACHE_NEG_TTL : self::CACHE_TTL;
		if ($age > $ttl) {
			return null;
		}
		$data = json_decode((string)file_get_contents($file), true);
		return is_array($data) ? $data : null;
	}

	/**
	 * Устаревший, но пригодный кэш — отдаём мгновенно, обновление уходит в фон.
	 *
	 * @return array<int,array{value:string,label:string,hint:string}>|null
	 */
	private static function cacheGetStale(string $key): ?array
	{
		$file = self::cacheDir() . $key . '.json';
		if (!is_file($file)) {
			return null;
		}
		if ((time() - (int)filemtime($file)) > self::CACHE_STALE_MAX) {
			@unlink($file);
			return null;
		}
		$data = json_decode((string)file_get_contents($file), true);
		return is_array($data) ? $data : null;
	}

	private static function cacheIsEmpty(string $file): bool
	{
		$raw = (string)file_get_contents($file);
		return trim($raw) === '[]';
	}

	/**
	 * @param array<int,array{value:string,label:string,hint:string}> $data
	 */
	private static function cacheSet(string $key, array $data, int $ttl = self::CACHE_TTL): void
	{
		$dir = self::cacheDir();
		if (!is_dir($dir)) {
			@mkdir($dir, 0755, true);
		}
		$file = $dir . $key . '.json';
		@file_put_contents($file, json_encode($data, JSON_UNESCAPED_UNICODE));
		// Время жизни храним через mtime: age = now - mtime
		$age = max(0, self::CACHE_TTL - $ttl);
		if ($age > 0) {
			@touch($file, time() - $age);
		}
	}

	private static function cacheDir(): string
	{
		return dirname(__DIR__, 2) . '/var/cache/suggest/';
	}

	/**
	 * @param array<string,mixed> $data
	 */
	private static function respond(array $data, int $code = 200): void
	{
		if (!headers_sent()) {
			http_response_code($code);
			header('Content-Type: application/json; charset=utf-8');
			header('Cache-Control: no-store');
			header('X-Robots-Tag: noindex');
		}
		echo json_encode($data, JSON_UNESCAPED_UNICODE);
	}
}
