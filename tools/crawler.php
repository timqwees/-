<?php
declare(strict_types=1);

/**
 * =============================================================================
 *  STUDIO AUDIT CRAWLER — «платный» аудит сайта в одном файле
 * =============================================================================
 *
 *  НАЗНАЧЕНИЕ
 *  Полный автономный краулер-аудитор: обходит сайт (локальный стенд или любой
 *  сайт в интернете), разбирает каждую страницу как DOM, строит граф внутренних
 *  ссылок и выдаёт ТОЧНЫЙ отчёт с явным указанием:
 *      — где проблемы ЕСТЬ (с URL, деталями, критичностью и подсказкой),
 *      — где проблем НЕТ (каждая проверка отображается явно, включая ✅).
 *
 *  ЗАПУСК
 *      php tools/crawler.php <url>
 *
 *      Одной команды достаточно. Сценарий выбирается автоматически по адресу:
 *        • локальный (localhost, 127.0.0.1, ::1, приватные сети, .local/.test/.dev)
 *          — глубокий быстрый обход без пауз, HTTPS-проверки отключены,
 *            проверка служебных файлов включена;
 *        • публичный (любой другой адрес)
 *          — вежливый обход с паузами, HTTPS/TLS-проверки включены,
 *            сканирование чужих служебных файлов отключено.
 *      Без адреса аудитится локальный стенд http://127.0.0.1:8899/
 *
 *  ДОПОЛНИТЕЛЬНЫЕ ОПЦИИ (необязательны, нужны только для нестандартных случаев)
 *      --depth=N          максимальная глубина обхода в кликах
 *      --limit=N          максимум страниц
 *      --concurrency=N    параллельных запросов
 *      --delay=MS         пауза между запросами к одному хосту
 *      --timeout=MS       таймаут запроса
 *      --ua=STRING        User-Agent краулера
 *      --only=PATH        обход только по префиксу пути
 *      --exclude=RE       исключать URL по PCRE (можно повторять)
 *      --respect-robots   не заходить на URL, запрещённые robots.txt
 *      --no-robots        не читать robots.txt
 *      --external[=N]     проверять внешние ссылки (0 — выкл.)
 *      --no-external      не проверять внешние ссылки
 *      --no-probe         не проверять служебные файлы (.env, .git, …)
 *      --json=FILE        сохранить JSON-отчёт
 *      --html=FILE        сохранить автономный HTML-отчёт
 *      --pages            показать полную таблицу страниц
 *      --no-color         без ANSI-цвета
 *      --quiet            только итоговые строки
 *      --help             справка
 *
 *  ЧТО ПРОВЕРЯЕТСЯ (152 правила в 11 категориях — полный список в Audit::rules())
 *      Каждая проверка опирается на первоисточники: Google Search Central,
 *      Яндекс.Вебмастер, web.dev, W3C/WCAG, Schema.org, RFC, OWASP, llmstxt.org,
 *      документация ботов OpenAI/Anthropic/Perplexity, arXiv (KDD 2024).
 *      • Технические ошибки  — коды ответа, таймауты, цепочки и петли редиректов,
 *                             soft-404, HTTP→HTTPS, смешанный контент, гигиена URL
 *                             (длина, регистр, подчёркивания, не-ASCII, слеш)
 *      • Индексация          — robots.txt/robots meta/X-Robots-Tag, canonical, hreflang,
 *                             сироты, глубина, пагинация, sitemap ↔ обход ↔ /pages, дубли,
 *                             плюс Яндекс-специфичное: Clean-param (только у Яндекса),
 *                             директива Host, лимиты robots.txt (500 КБ), percent-encoding
 *                             не-ASCII путей, Crawl-delay (Яндекс игнорирует с 2018),
 *                             блокировки роботов Яндекса/Google/Bing, теги подтверждения
 *      • Мета-теги           — title/description/H1/lang/viewport/OG/Twitter, дубли, длины,
 *                             иерархия заголовков, favicon, конфликт robots-сигналов
 *      • Разметка            — JSON-LD: валидность, @type, дубли «одиночных» типов,
 *                             обязательные свойства (name/headline/datePublished/…)
 *      • Контент            — тонкий контент, дубли, PHP-warning'и в выводе,
 *                             соответствие языка, плотность текста, переспам терминов
 *      • Ссылки              — битые внутренние и внешние, редиректы, nofollow, пустые
 *                             и шаблонные анкоры, якоря (#id), ссылки на noindex, дубли анкоров
 *      • Изображения         — alt, width/height (CLS), lazy, битые, вес, LCP-прокси
 *      • Скорость            — TTFB, вес HTML, сжатие, кэш-заголовки, блокирующие
 *                             ресурсы, размер DOM, HTTP-версия и HTTP/2, font-display
 *      • Безопасность        — HTTPS/HSTS, CSP, X-Frame-Options, nosniff, Referrer-Policy,
 *                             баннеры сервера, утечка служебных файлов, флаги cookie,
 *                             срок действия и алгоритм TLS-сертификата
 *      • Доступность        — label у полей, дубли id, lang, viewport, iframe-title,
 *                             landmark-разметка, skip-link, доступные имена кнопок
 *      • AI и GEO            — /llms.txt и /llms-full.txt, манифесты для агентов
 *                             (ai-plugin.json, agents.json), правила robots.txt для
 *                             цитатных и обучающих AI-краулеров (OAI-SearchBot,
 *                             ClaudeBot, PerplexityBot, Google-Extended, GPTBot,
 *                             CCBot, Amazonbot, Bingbot, Applebot, Meta AI),
 *                             структура для цитирования (списки, вопросы, FAQPage),
 *                             определение сущности и «быстрый ответ» в начале,
 *                             E-E-A-T (автор/дата/dateModified), внешние источники
 *                             и цитаты (методы KDD 2024), sameAs, зависимость от JS
 *      • AI и GEO (проверка цитируемости по методам Aggarwal et al., KDD 2024) —
 *                             источники, статистика, цитаты с атрибуцией.
 *
 *  АНАЛИТИЧЕСКИЙ СЛОЙ (не только проверки, но и продвижение)
 *      --page=URL         разбор конкретной страницы: семантическое ядро, собранное
 *                         из её текста, заголовков, description, slug и входящих
 *                         анкоров; покрытие ядра по блокам (title/H1/description/текст);
 *                         пробелы — термины, которые есть в окружении, но нет на
 *                         странице; черновики title и description из реальных
 *                         терминов; диагностика «почему страница может не расти»
 *                         (техника, каннибализация, вес, глубина, объём текста);
 *                         список страниц, откуда добавить ссылки; чек-лист
 *                         готовности к топ-10.
 *      --clusters         карта тематических кластеров сайта: какие страницы про
 *                         что, размер кластера и его пробелы.
 *      --competitors=URLS сравнение с конкурентами теми же правилами: что у них
 *                         проходит, а у нас нет — это и есть измеримый разрыв.
 *
 *  ЧЕСТНОСТЬ ПРЕТЕНЗИЙ
 *      Инструмент доказывает ТЕХНИЧЕСКУЮ готовность (индексация, уникальность,
 *      семантическое ядро, ссылки, скорость, разметка, доступность) — то есть
 *      полный набор условий, без которых позиция в топ-3/топ-10 невозможна.
 *      Позицию определяет также авторитет домена (внешние ссылки, бренд, история),
 *      который HTTP-обходом не измеряется: такие пункты помечены в отчёте как
 *      требующие внешних данных (Яндекс.Вебмастер, Ahrefs/Semrush, Wordstat).
 *
 *  ПОЧЕМУ КАЖДАЯ ПРОВЕРКА ВАЖНА
 *      У каждого правила есть блок «Влияет» — что конкретно теряет или получает
 *      сайт из-за этой ошибки (без воды) — и ссылки на официальные источники:
 *      Google Search Central, Яндекс.Вебмастер, web.dev (Core Web Vitals),
 *      W3C/WCAG, Schema.org, RFC 9110/8446, OWASP, llmstxt.org, документация
 *      ботов OpenAI/Anthropic/Perplexity. Справочник — Audit::impacts(),
 *      ссылки — Audit::SRC.
 *
 *  ТЕХНИЧЕСКИЕ ДЕТАЛИ
 *      • HTTP-клиент на curl_multi: параллельные запросы, тайминги (TTFB, connect, total),
 *        ручное следование редиректам (полная цепочка), вежливые паузы между пачками,
 *        автоматический повтор неудачных соединений (однопоточный сервер не рвёт обход).
 *      • Разбор HTML через DOMDocument + XPath (не регулярки) — корректная работа с кириллицей,
 *        вложенностью и любым порядком атрибутов.
 *      • Percent-encoding не-ASCII в URL до разбора (parse_url портит кириллицу в query).
 *      • robots.txt: полноценный парсер (Allow/Disallow, *, $, Sitemap, Host, Clean-param)
 *        с правилом «самое длинное совпадение выигрывает».
 *      • Sitemap: urlset + sitemapindex (с учётом XML-namespace), lastmod/changefreq/priority,
 *        сверка с обходом; отдельно — файл-список /pages.
 *      • Граф ссылок: BFS с учётом глубины, входящие/исходящие, сироты, хабы, внешние хосты.
 *      • LCP-прокси: главное изображение страницы проверяется на fetchpriority/size/srcset.
 *      • Дубли контента: Jaccard по шинглам триграмм основного текста (без шапки/футера)
 *        через инвертированный индекс — масштабируется на тысячи страниц.
 *      • Выход: цветной консольный отчёт (✅ для пройденных проверок и ❌ для проблем),
 *        JSON (машиночитаемый) и автономный HTML (без внешних зависимостей) с баллами,
 *        оглавлением, фильтром по категориям и поиском.
 *      • Код возврата: 0 — критичных и высоких замечаний нет, 1 — есть замечания, 2 — ошибка.
 *
 *  ОГРАНИЧЕНИЯ
 *      Инструмент ничего не меняет на сайте: только читает (HEAD/GET).
 *      Разрешён один файл — tools/crawler.php.
 *
 *  @version 2.1.0
 * =============================================================================
 */

namespace Studio\Audit;

if (PHP_SAPI !== 'cli') {
	http_response_code(403);
	exit("CLI only.\n");
}

// Краулер держит в памяти разобранные страницы: на больших сайтах стандартных
// 128 МБ не хватает, а в CLI ограничение памяти мешать не должно.
if (ini_get('memory_limit') !== '-1') {
	@ini_set('memory_limit', '1024M');
}

/**
 * Конфигурация запуска.
 */
final class Cfg
{
	public string $start = 'http://127.0.0.1:8899/';
	public int $depth = 6;
	public int $limit = 500;
	public int $concurrency = 6;
	public int $delayMs = 0;
	public int $timeoutMs = 20000;
	public string $ua = 'StudioAuditBot/1.0 (SEO/technical audit; single-file crawler)';
	public bool $respectRobots = false;
	public bool $readRobots = true;
	public bool $external = true;
	public int $externalLimit = 200;
	public int $externalTimeoutMs = 8000;
	public int $externalBudgetSec = 45;
	public bool $probe = true;
	public ?string $jsonFile = null;
	public ?string $htmlFile = null;
	public bool $noReport = false;
	/** Страница для детального плана продвижения. */
	public ?string $pageUrl = null;
	/** @var list<string> URL конкурентов для сравнения */
	public array $competitors = [];
	/** Показать карту тематических кластеров. */
	public bool $clusters = false;
	public bool $showPages = false;
	public bool $quiet = false;
	public bool $color = true;
	/** Режим работы: local | global (определяется автоматически). */
	public string $mode = 'auto';
	/** @var list<string> опции, заданные пользователем явно (их сценарий не перекрывает) */
	public array $explicit = [];
	/** @var list<string> */
	public array $only = [];
	/** @var list<string> */
	public array $exclude = [];
	/** @var list<string> */
	public array $errors = [];
	/** @var array<string,string> */
	public array $map = [];

	/**
	 * Локальный это адрес или публичный?
	 * localhost, 127.0.0.0/8, ::1, приватные сети, .local/.test/.dev/.internal, имя без домена.
	 */
	public function detectLocal(): bool
	{
		$host = strtolower(trim((string)parse_url($this->start, PHP_URL_HOST)));
		if ($host === '') {
			return false;
		}
		if (in_array($host, ['localhost', '127.0.0.1', '0.0.0.0', '::1', 'host.docker.internal'], true)) {
			return true;
		}
		if (str_starts_with($host, '127.')) {
			return true;
		}
		foreach (['.local', '.localhost', '.test', '.dev', '.internal', '.lan', '.home', '.localdomain'] as $suffix) {
			if (str_ends_with($host, $suffix)) {
				return true;
			}
		}
		if (filter_var($host, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
			if (str_starts_with($host, '10.') || str_starts_with($host, '192.168.') || str_starts_with($host, '169.254.')) {
				return true;
			}
			if (preg_match('~^172\.(1[6-9]|2\d|3[01])\.~', $host)) {
				return true;
			}
		}
		if (filter_var($host, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)) {
			return str_starts_with($host, '::1')
				|| str_starts_with($host, 'fc')
				|| str_starts_with($host, 'fd')
				|| preg_match('~^fe[89ab]~', $host) === 1;
		}
		return !str_contains($host, '.');   // имя хоста без домена
	}

	public function isLocal(): bool
	{
		return $this->detectLocal();
	}

	/**
	 * Авто-выбор сценария под тип адреса.
	 *
	 * Локальный стенд: обход глубже и быстрее, HTTPS-проверки выключены,
	 * проверка служебных файлов включена.
	 * Публичный сайт: вежливые паузы, меньше глубины, HTTPS/TLS-проверки включены,
	 * сканирование чужих служебных файлов отключено.
	 */
	public function applyScenario(): void
	{
		$local = $this->detectLocal();
		$this->mode = $local ? 'local' : 'global';

		$preset = $local
			? [
				'depth' => 10, 'limit' => 3000, 'concurrency' => 10, 'delayMs' => 0, 'timeoutMs' => 20000,
				'externalLimit' => 300, 'externalTimeoutMs' => 5000, 'externalBudgetSec' => 30, 'probe' => true,
			]
			: [
				'depth' => 5, 'limit' => 500, 'concurrency' => 4, 'delayMs' => 80, 'timeoutMs' => 25000,
				'externalLimit' => 120, 'externalTimeoutMs' => 8000, 'externalBudgetSec' => 40, 'probe' => false,
			];

		foreach ($preset as $key => $value) {
			if (!in_array($key, $this->explicit, true)) {
				$this->{$key} = $value;
			}
		}
	}

	/** Название режима для отчёта. */
	public function modeLabel(): string
	{
		return $this->mode === 'local' ? 'локальный стенд' : 'публичный сайт';
	}
}

/**
 * Терминальный UI: цвета, линии, полосы, таблицы.
 */
final class Ui
{
	private bool $color;

	/** @var array<string,string> */
	private const STYLES = [
		'reset'   => "0",
		'bold'    => "1",
		'dim'     => "2",
		'italic'  => "3",
		'under'   => "4",
		'red'     => "31",
		'green'   => "32",
		'yellow'  => "33",
		'blue'    => "34",
		'magenta' => "35",
		'cyan'    => "36",
		'white'   => "37",
		'gray'    => "90",
		'brred'   => "91",
		'brgreen' => "92",
		'bryellow'=> "93",
		'brblue'  => "94",
		'brcyan'  => "96",
		'bgred'   => "41",
		'bggreen' => "42",
		'bgyellow'=> "43",
		'bgblue'  => "44",
		'bgmagenta' => "45",
		'bgcyan'  => "46",
		'bggray'  => "100",
		'bgwhite' => "47",
	];

	public function __construct(bool $color = true)
	{
		$this->color = $color && getenv('NO_COLOR') === false && getenv('TERM') !== 'dumb';
	}

	public function c(string $text, string $style = ''): string
	{
		if (!$this->color || $style === '') {
			return $text;
		}
		$parts = explode('|', $style);
		$codes = [];
		foreach ($parts as $p) {
			if (isset(self::STYLES[$p])) {
				$codes[] = self::STYLES[$p];
			}
		}
		if (!$codes) {
			return $text;
		}
		return "\033[" . implode(';', $codes) . 'm' . $text . "\033[0m";
	}

	public function line(string $s = ''): void
	{
		echo $s, PHP_EOL;
	}

	/**
	 * Выравнивание по символам (не по байтам) — иначе кириллица «разъезжается».
	 */
	public function pad(string $s, int $len, bool $right = true): string
	{
		$cur = mb_strlen($s);
		if ($cur >= $len) {
			return mb_substr($s, 0, $len);
		}
		$spaces = str_repeat(' ', $len - $cur);
		return $right ? $s . $spaces : $spaces . $s;
	}

	public function title(string $text): void
	{
		$w = 78;
		$this->line($this->c('╔' . str_repeat('═', $w) . '╗', 'brcyan'));
		$this->line($this->c('║', 'brcyan') . $this->c(' ', 'bgcyan')
			. $this->c('  ' . $this->pad($text, $w - 3), 'bold|white') . $this->c('║', 'brcyan'));
		$this->line($this->c('╚' . str_repeat('═', $w) . '╝', 'brcyan'));
	}

	public function section(string $text, string $style = 'brcyan'): void
	{
		$this->line();
		$this->line($this->c('── ' . $text . ' ' . str_repeat('─', max(0, 74 - mb_strlen($text))), $style));
	}

	public function sub(string $text): void
	{
		$this->line($this->c('  ' . $text, 'bold'));
	}

	public function item(string $text): void
	{
		$this->line('    ' . $text);
	}

	public function kv(string $key, string $value): void
	{
		$this->line('  ' . $this->c($this->pad($key, 15) . ': ', 'gray') . $value);
	}

	public function bar(float $pct, int $w = 22): string
	{
		$pct = max(0.0, min(100.0, $pct));
		$fill = (int)round($pct / 100 * $w);
		$style = $pct >= 85 ? 'green' : ($pct >= 65 ? 'yellow' : 'red');
		return $this->c(str_repeat('█', $fill), $style) . $this->c(str_repeat('░', max(0, $w - $fill)), 'gray');
	}

	/** Цвет по критичности. */
	public function sev(string $sev): string
	{
		return match ($sev) {
			'critical' => $this->c('КРИТИЧНО', 'bold|brred'),
			'high'     => $this->c('ВЫСОКАЯ', 'red'),
			'medium'   => $this->c('СРЕДНЯЯ', 'yellow'),
			'low'      => $this->c('НИЗКАЯ', 'blue'),
			default    => $this->c('ИНФО', 'gray'),
		};
	}

	public function ok(string $text = 'ОК'): string
	{
		return $this->c('✓', 'green') . ' ' . $text;
	}

	public function fail(string $text = 'ПРОБЛЕМА'): string
	{
		return $this->c('✗', 'red') . ' ' . $text;
	}

	/**
	 * Цветная плашка (бейдж) с обратным видео: « КРИТИЧНО ».
	 */
	public function chip(string $text, string $style = 'bggray'): string
	{
		return $this->c(' ' . $text . ' ', $style . '|bold|white');
	}

	/**
	 * Бейдж критичности: цвет фона зависит от уровня.
	 */
	public function sevBadge(string $sev): string
	{
		$label = match ($sev) {
			'critical' => 'КРИТИЧНО',
			'high'     => 'ВЫСОКАЯ',
			'medium'   => 'СРЕДНЯЯ',
			'low'      => 'НИЗКАЯ',
			default    => 'ИНФО',
		};
		$style = match ($sev) {
			'critical' => 'bgred',
			'high'     => 'bgyellow',
			'medium'   => 'bgblue',
			'low'      => 'bgmagenta',
			default    => 'bggray',
		};
		return $this->chip($this->pad($label, 8), $style);
	}

	/**
	 * Стабильный цвет для категории (одна и та же категория — всегда один цвет).
	 */
	public function catStyle(string $cat): string
	{
		$palette = ['brcyan', 'bryellow', 'brgreen', 'brblue', 'brred', 'magenta', 'cyan', 'yellow'];
		return $palette[abs(crc32($cat)) % count($palette)];
	}

	/**
	 * Итоговая строка-вердикт: цвет зависит от оценки.
	 */
	public function verdict(float $score, string $text): string
	{
		$style = $score >= 85 ? 'bold|brgreen' : ($score >= 65 ? 'bold|bryellow' : 'bold|brred');
		return $this->c('  ИТОГ  ', $style) . ' ' . $text;
	}

	/**
	 * Горизонтальная 100%-ная полоса со стеком сегментов (например, доля проблем).
	 *
	 * @param array<string,float> $parts метка => доля (0..1)
	 * @param array<string,string> $styles метка => стиль
	 */
	public function stackBar(array $parts, array $styles, int $w = 60): string
	{
		$out = '';
		$used = 0;
		$keys = array_keys($parts);
		$i = 0;
		foreach ($parts as $label => $share) {
			$len = (int)round($share * $w);
			if ($i === count($keys) - 1) {
				$len = max(0, $w - $used);
			}
			$used += $len;
			if ($len > 0) {
				$out .= $this->c(str_repeat('█', $len), $styles[$label] ?? 'gray');
			}
			$i++;
		}
		return $out;
	}
}

/**
 * URL: нормализация, разрешение относительных ссылок (RFC 3986), сравнение хостов.
 */
final class Url
{
	/**
	 * Percent-encoding не-ASCII и пробелов в URL.
	 *
	 * Зачем: PHP-парсер parse_url() портит не-ASCII в query (кириллица в адресе
	 * превращается в «_»), а curl не может отправить кириллицу в строке запроса.
	 * Поэтому кодируем URL ДО любого разбора — дальше работаем только с ASCII.
	 */
	public static function encode(string $url): string
	{
		return (string)preg_replace_callback('~[^\x21-\x7E]~', static function (array $m): string {
			$out = '';
			foreach (str_split(bin2hex($m[0]), 2) as $byte) {
				$out .= '%' . strtoupper($byte);
			}
			return $out;
		}, $url);
	}

	/** @return array{url:string,scheme:string,host:string,port:?int,path:string,query:?string,fragment:?string}|null */
	public static function parse(string $url): ?array
	{
		$parts = parse_url(self::encode($url));
		if ($parts === false || !isset($parts['scheme'], $parts['host'])) {
			return null;
		}
		return [
			'url'      => $url,
			'scheme'   => strtolower($parts['scheme']),
			'host'     => strtolower($parts['host']),
			'port'     => isset($parts['port']) ? (int)$parts['port'] : null,
			'path'     => $parts['path'] ?? '/',
			'query'    => $parts['query'] ?? null,
			'fragment' => $parts['fragment'] ?? null,
		];
	}

	public static function hostOf(string $url): string
	{
		$p = self::parse($url);
		return $p === null ? '' : $p['host'] . ($p['port'] ? ':' . $p['port'] : '');
	}

	/** Путь+query — то, что показываем в отчёте. */
	public static function displayPath(string $url): string
	{
		$p = self::parse($url);
		if ($p === null) {
			return $url;
		}
		return $p['path'] . ($p['query'] !== null && $p['query'] !== '' ? '?' . $p['query'] : '');
	}

	/** Схема://хост — для сравнения «тот же домен». */
	public static function origin(string $url): string
	{
		$p = self::parse($url);
		if ($p === null) {
			return '';
		}
		return $p['scheme'] . '://' . $p['host'] . ($p['port'] ? ':' . $p['port'] : '');
	}

	/** Тот же сайт (www и без www — один сайт). */
	public static function sameSite(string $a, string $b): bool
	{
		$ha = self::hostOf($a);
		$hb = self::hostOf($b);
		if ($ha === '' || $hb === '') {
			return false;
		}
		$strip = static fn(string $h): string => preg_replace('~^www\.~i', '', $h);
		return $strip($ha) === $strip($hb);
	}

	public static function sameHost(string $a, string $b): bool
	{
		return self::hostOf($a) === self::hostOf($b) && self::hostOf($a) !== '';
	}

	/** Нормализация абсолютного URL: схема/хост в нижний регистр, без фрагмента, без :80/:443. */
	public static function normalize(string $url): ?string
	{
		$p = self::parse($url);
		if ($p === null) {
			return null;
		}
		$port = $p['port'];
		if (($p['scheme'] === 'http' && $port === 80) || ($p['scheme'] === 'https' && $port === 443)) {
			$port = null;
		}
		$path = $p['path'] !== '' ? $p['path'] : '/';
		$out = $p['scheme'] . '://' . $p['host'] . ($port ? ':' . $port : '') . $path;
		if ($p['query'] !== null && $p['query'] !== '') {
			$out .= '?' . $p['query'];
		}
		return $out;
	}

	/** Убирает . и .. из пути. */
	public static function removeDotSegments(string $path): string
	{
		$out = [];
		foreach (explode('/', $path) as $seg) {
			if ($seg === '' || $seg === '.') {
				continue;
			}
			if ($seg === '..') {
				array_pop($out);
				continue;
			}
			$out[] = $seg;
		}
		$res = '/' . implode('/', $out);
		if (str_ends_with($path, '/') && $res !== '/') {
			$res .= '/';
		}
		return $res;
	}

	/**
	 * Разрешение ссылки относительно базы (RFC 3986, раздел 5).
	 * Возвращает null для не-HTTP схем (mailto:, tel:, javascript:, data: …).
	 */
	public static function resolve(string $ref, string $base): ?string
	{
		$ref = trim($ref);
		if ($ref === '') {
			return null;
		}
		if (preg_match('~^(mailto|tel|sms|javascript|data|blob|about|ftp|ftps|ws|wss|file|magnet|intent|whatsapp|viber|telegram|skype|news):~i', $ref)) {
			return null;
		}
		// Любая другая схема (news:, geo:, callto: …) — не наш ресурс
		if (preg_match('~^[a-z][a-z0-9+.\-]*:~i', $ref) && !preg_match('~^https?://~i', $ref)) {
			return null;
		}
		$ref = self::encode($ref);
		$bp = self::parse($base);
		if ($bp === null) {
			return null;
		}
		if (str_starts_with($ref, '//')) {
			$ref = $bp['scheme'] . ':' . $ref;
		}
		if (preg_match('~^https?://~i', $ref)) {
			return self::normalize($ref);
		}

		$path = $bp['path'];
		$query = $bp['query'];
		$fragment = null;

		if (str_starts_with($ref, '#')) {
			$fragment = substr($ref, 1);
		} elseif (str_starts_with($ref, '?')) {
			$query = substr($ref, 1);
		} elseif (str_starts_with($ref, '/')) {
			$path = $ref;
			$query = null;
		} else {
			$slash = strrpos($path, '/');
			$dir = $slash === false ? '/' : substr($path, 0, $slash + 1);
			$rel = $dir . $ref;
			if (($q = strpos($rel, '?')) !== false) {
				$query = substr($rel, $q + 1);
				$rel = substr($rel, 0, $q);
			}
			if (($h = strpos($rel, '#')) !== false) {
				$fragment = substr($rel, $h + 1);
				$rel = substr($rel, 0, $h);
			}
			$path = $rel;
		}
		if ($path === '') {
			$path = '/';
		}
		$path = self::removeDotSegments($path);

		$out = $bp['scheme'] . '://' . $bp['host'] . ($bp['port'] ? ':' . $bp['port'] : '') . $path;
		if ($query !== null && $query !== '') {
			$out .= '?' . $query;
		}
		if ($fragment !== null && $fragment !== '') {
			$out .= '#' . $fragment;
		}
		return self::encode($out);
	}

	/** Разделяет URL на «без фрагмента» и сам фрагмент. */
	public static function splitFragment(string $url): array
	{
		$pos = strpos($url, '#');
		if ($pos === false) {
			return [$url, null];
		}
		return [substr($url, 0, $pos), substr($url, $pos + 1)];
	}

	/** Ссылка «без query» — для сравнения дублей (utm, ref и т.п.). */
	public static function withoutQuery(string $url): string
	{
		$pos = strpos($url, '?');
		return $pos === false ? $url : substr($url, 0, $pos);
	}

	/** Ключ нормализации для сравнения URL (регистр пути НЕ меняем — он значим). */
	public static function key(string $url): string
	{
		$u = self::withoutQuery(self::normalize($url) ?? $url);
		return rtrim($u, '/') === '' ? $u : $u;
	}
}

/**
 * Один HTTP-запрос: тело, заголовки, тайминги, цепочка редиректов.
 */
final class Req
{
	public string $url = '';
	public string $final = '';
	public int $status = 0;
	/** @var array<string,string> заголовки финального ответа (ключ в нижнем регистре) */
	public array $headers = [];
	/** @var list<string> все заголовки всех хопов */
	public array $headerLog = [];
	public string $body = '';
	/** @var list<array{from:string,to:string,status:int}> */
	public array $redirects = [];
	public ?string $error = null;
	public float $timeTotal = 0.0;
	public float $ttfb = 0.0;
	public float $connect = 0.0;
	public float $namelookup = 0.0;
	public int $bytes = 0;
	public string $httpVersion = '';
	public int $hops = 0;

	public function header(string $name, string $default = ''): string
	{
		return $this->headers[strtolower($name)] ?? $default;
	}

	public function hasHeader(string $name): bool
	{
		return isset($this->headers[strtolower($name)]);
	}
}

/**
 * HTTP-клиент на curl_multi: параллельные запросы, ручное следование редиректам.
 */
final class Http
{
	private const MAX_REDIRECTS = 10;

	/**
	 * Один запрос (с цепочкой редиректов).
	 */
	public static function fetch(string $url, Cfg $cfg, string $method = 'GET'): Req
	{
		$out = self::fetchMany([$url], $cfg, $method);
		return $out[$url] ?? new Req();
	}

	/**
	 * Параллельные запросы. Возвращает массив Req по исходным URL.
	 *
	 * @param list<string> $urls
	 * @return array<string,Req>
	 */
	public static function fetchMany(array $urls, Cfg $cfg, string $method = 'GET', bool $keepBody = true, ?int $timeoutMs = null): array
	{
		$results = [];
		if (!$urls) {
			return $results;
		}
		$multi = curl_multi_init();
		$active = [];   // handle => Req
		$pending = array_values($urls);
		$queue = [];

		foreach ($pending as $u) {
			$req = new Req();
			$req->url = $u;
			$req->final = $u;
			$h = self::makeHandle($req, $u, $cfg, $method, $keepBody, $timeoutMs);
			if ($h === null) {
				$req->error = 'Некорректный URL';
				$results[$u] = $req;
				continue;
			}
			curl_multi_add_handle($multi, $h);
			$active[(int)$h] = $req;
		}

		do {
			$status = curl_multi_exec($multi, $running);
			if ($running > 0) {
				curl_multi_select($multi, 1.0);
			}
			while (($info = curl_multi_info_read($multi)) !== false) {
				$h = $info['handle'];
				$req = $active[(int)$h] ?? null;
				if ($req === null) {
					curl_multi_remove_handle($multi, $h);
					continue;
				}
				$code = (int)curl_getinfo($h, CURLINFO_RESPONSE_CODE);
				$err = curl_error($h);
				$req->status = $code;
				$req->final = (string)(curl_getinfo($h, CURLINFO_EFFECTIVE_URL) ?: $req->final);
				$req->timeTotal = (float)curl_getinfo($h, CURLINFO_TOTAL_TIME);
				$req->ttfb = (float)curl_getinfo($h, CURLINFO_STARTTRANSFER_TIME);
				$req->connect = (float)curl_getinfo($h, CURLINFO_CONNECT_TIME);
				$req->namelookup = (float)curl_getinfo($h, CURLINFO_NAMELOOKUP_TIME);
				$req->bytes = (int)curl_getinfo($h, CURLINFO_SIZE_DOWNLOAD);
				$req->httpVersion = (string)curl_getinfo($h, CURLINFO_HTTP_VERSION);

				if ($err !== '' && $code === 0) {
					$req->error = $err;
				}

				$location = $req->header('location');
				$isRedirect = in_array($code, [301, 302, 303, 307, 308], true) && $location !== '';

				if ($isRedirect && $req->hops < self::MAX_REDIRECTS) {
					$next = Url::resolve($location, $req->final);
					if ($next === null) {
						$next = $location;
					}
					$req->redirects[] = ['from' => $req->final, 'to' => $next, 'status' => $code];
					$req->hops++;
					curl_multi_remove_handle($multi, $h);
					unset($active[(int)$h]);
					$req->final = $next;
					$req->headers = [];
					$req->body = '';   // тело заглушки редиректа не должно попасть в разбор страницы
					$nh = self::makeHandle($req, $next, $cfg, ($code === 303 ? 'GET' : $method), $keepBody, $timeoutMs);
					if ($nh !== null) {
						curl_multi_add_handle($multi, $nh);
						$active[(int)$nh] = $req;
					} else {
						$req->error = 'Некорректный URL редиректа';
					}
					continue;
				}
				if ($isRedirect) {
					$req->error = 'Слишком много редиректов (' . self::MAX_REDIRECTS . ')';
				}
				$results[$req->url] = $req;
				curl_multi_remove_handle($multi, $h);
				unset($active[(int)$h]);
			}
		} while ($running > 0 || $active !== []);

		// Незавершённые (таймаут) — фиксируем как ошибку
		foreach ($active as $req) {
			$req->error = $req->error ?? ('Превышен таймаут ' . ($cfg->timeoutMs / 1000) . ' с');
			$results[$req->url] = $req;
		}
		curl_multi_close($multi);
		return $results;
	}

	private static function makeHandle(Req $req, string $url, Cfg $cfg, string $method, bool $keepBody, ?int $timeoutMs = null): ?\CurlHandle
	{
		$ch = curl_init();
		if ($ch === false) {
			return null;
		}
		$timeout = $timeoutMs ?? $cfg->timeoutMs;
		curl_setopt_array($ch, [
			CURLOPT_URL            => Url::encode($url),
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_FOLLOWLOCATION => false,
			CURLOPT_MAXREDIRS      => 0,
			CURLOPT_TIMEOUT_MS     => $timeout,
			CURLOPT_CONNECTTIMEOUT_MS => min($timeout, 10000),
			CURLOPT_USERAGENT      => $cfg->ua,
			CURLOPT_ACCEPT_ENCODING => 'gzip, deflate, br',
			CURLOPT_SSL_VERIFYPEER => true,
			CURLOPT_SSL_VERIFYHOST => 2,
			CURLOPT_ENCODING       => '',
			CURLOPT_HEADER         => false,
			CURLOPT_NOBODY         => $method === 'HEAD',
		]);
		curl_setopt($ch, CURLOPT_HEADERFUNCTION, function ($ch, string $line) use ($req): int {
			$len = strlen($line);
			$trim = trim($line);
			if ($trim !== '') {
				$req->headerLog[] = $trim;
				if (str_contains($trim, ':')) {
					[$k, $v] = explode(':', $trim, 2);
					$req->headers[strtolower(trim($k))] = trim($v);
				}
			}
			return $len;
		});
		if ($keepBody) {
			curl_setopt($ch, CURLOPT_WRITEFUNCTION, function ($ch, string $chunk) use ($req): int {
				$req->body .= $chunk;
				return strlen($chunk);
			});
		}
		return $ch;
	}
}

/**
 * robots.txt: парсер правил Allow/Disallow (с *, $), Sitemap, Host, Clean-param.
 */
final class Robots
{
	/** @var array<string,list<array{allow:bool,path:string,raw:string}>> */
	public array $groups = [];
	/** @var list<string> */
	public array $sitemaps = [];
	public ?string $host = null;
	/** @var list<string> */
	public array $errors = [];
	public int $status = 0;
	public bool $exists = false;
	/** @var array<string,list<int>> */
	public array $cleanParams = [];
	/** @var array<string,float> User-agent => crawl-delay в секундах */
	public array $crawlDelay = [];

	/**
	 * @param array<string,string> $headers
	 */
	public static function parse(string $text): self
	{
		$r = new self();
		$lines = preg_split('~\R~', $text) ?: [];
		$currentAgents = [];
		$lastWasAgent = false;

		foreach ($lines as $line) {
			$line = trim(preg_replace('~\s+~u', ' ', $line) ?? '');
			if ($line === '' || str_starts_with($line, '#')) {
				$lastWasAgent = false;
				continue;
			}
			$pos = strpos($line, ':');
			if ($pos === false) {
				$r->errors[] = 'Строка без разделителя «:»: ' . mb_substr($line, 0, 60);
				continue;
			}
			$field = strtolower(trim(substr($line, 0, $pos)));
			$value = trim(substr($line, $pos + 1));

			switch ($field) {
				case 'user-agent':
					if (!$lastWasAgent) {
						$currentAgents = [];
					}
					$currentAgents[] = strtolower($value);
					$lastWasAgent = true;
					foreach ($currentAgents as $ua) {
						if (!isset($r->groups[$ua])) {
							$r->groups[$ua] = [];
						}
					}
					break;
				case 'allow':
				case 'disallow':
					$allow = $field === 'allow';
					if ($value === '' && !$allow) {
						break; // пустой Disallow = разрешено всё
					}
					foreach ($currentAgents as $ua) {
						$r->groups[$ua][] = ['allow' => $allow, 'path' => $value, 'raw' => $field . ': ' . $value];
					}
					$lastWasAgent = false;
					break;
				case 'sitemap':
					if ($value !== '') {
						$r->sitemaps[] = $value;
					}
					$lastWasAgent = false;
					break;
				case 'host':
					$r->host = $value;
					$lastWasAgent = false;
					break;
				case 'clean-param':
					$parts = preg_split('~\s+~', $value) ?: [];
					$ref = array_shift($parts) ?? '';
					if ($ref !== '') {
						$r->cleanParams[$ref] = $parts;
					}
					$lastWasAgent = false;
					break;
				case 'crawl-delay':
					$delay = (float)str_replace(',', '.', $value);
					foreach ($currentAgents as $ua) {
						$r->crawlDelay[$ua] = $delay;
					}
					$lastWasAgent = false;
					break;
				default:
					$r->errors[] = 'Неизвестная директива: ' . $field;
					$lastWasAgent = false;
			}
		}
		return $r;
	}

	/** Разрешён ли путь (с query) для указанного агента. */
	public function allowed(string $pathAndQuery, string $agent = '*'): bool
	{
		$rules = $this->rulesFor($agent);
		if (!$rules) {
			return true;
		}
		$best = null;   // ['len'=>int,'allow'=>bool]
		foreach ($rules as $rule) {
			if ($this->match($rule['path'], $pathAndQuery)) {
				$len = strlen(str_replace(['*', '$'], '', $rule['path']));
				if ($best === null || $len > $best['len'] || ($len === $best['len'] && $rule['allow'])) {
					$best = ['len' => $len, 'allow' => $rule['allow']];
				}
			}
		}
		return $best === null ? true : $best['allow'];
	}

	/** @return list<array{allow:bool,path:string,raw:string}> */
	public function rulesFor(string $agent): array
	{
		$agent = strtolower($agent);
		$out = $this->groups[$agent] ?? [];
		if (!$out) {
			$out = $this->groups['*'] ?? [];
		}
		return $out;
	}

	private function match(string $pattern, string $path): bool
	{
		if ($pattern === '') {
			return true;
		}
		$regex = '';
		$len = strlen($pattern);
		for ($i = 0; $i < $len; $i++) {
			$ch = $pattern[$i];
			if ($ch === '*') {
				$regex .= '.*';
			} elseif ($ch === '$' && $i === $len - 1) {
				$regex .= '$';
			} else {
				$regex .= preg_quote($ch, '#');
			}
		}
		return (bool)preg_match('#^' . $regex . '#', $path);
	}
}

/**
 * Карта сайта: urlset + sitemapindex (+ .gz), сверка с фактическим обходом.
 */
final class SitemapSet
{
	/** @var array<string,array{lastmod:?string,changefreq:?string,priority:?string,source:string}> */
	public array $urls = [];
	/** @var list<string> */
	public array $sitemaps = [];
	/** @var list<string> */
	public array $errors = [];
	public int $status = 0;
	public bool $exists = false;
	/** @var array<string,int> статус кода по URL из карты */
	public array $statuses = [];

	/**
	 * Скачивает и разбирает карту (и индекс карт).
	 */
	public function load(string $origin, Cfg $cfg): void
	{
		$start = rtrim($origin, '/') . '/sitemap.xml';
		$this->loadFile($start, $cfg, 0);
		if (!$this->exists) {
			$idx = rtrim($origin, '/') . '/sitemap-index.xml';
			$this->loadFile($idx, $cfg, 0);
		}
	}

	private function loadFile(string $url, Cfg $cfg, int $level): void
	{
		if ($level > 4 || count($this->sitemaps) > 60) {
			return;
		}
		$req = Http::fetch($url, $cfg);
		$this->status = $req->status;
		if ($req->status !== 200) {
			$this->errors[] = $url . ' → HTTP ' . ($req->status ?: ($req->error ?? 'нет ответа'));
			return;
		}
		$this->exists = true;
		$this->sitemaps[] = $url;
		$body = $req->body;
		if (str_contains($req->header('content-type'), 'gzip') || str_starts_with($url, '') && str_ends_with($url, '.gz')) {
			$decoded = @gzdecode($body);
			if ($decoded !== false) {
				$body = $decoded;
			}
		}
		$doc = new \DOMDocument();
		libxml_use_internal_errors(true);
		$ok = $doc->loadXML(trim($body), LIBXML_NOWARNING | LIBXML_NOERROR | LIBXML_NONET);
		libxml_clear_errors();
		if (!$ok || $doc->documentElement === null) {
			$this->errors[] = $url . ' → невалидный XML';
			return;
		}
		$xp = new \DOMXPath($doc);
		$root = strtolower($doc->documentElement->nodeName);

		if ($root === 'sitemapindex') {
			foreach ($xp->query('//*[local-name()="sitemap"]') ?: [] as $node) {
				$loc = trim((string)($xp->query('./*[local-name()="loc"]', $node)?->item(0)?->nodeValue ?? ''));
				if ($loc === '') {
					continue;
				}
				$this->loadFile($loc, $cfg, $level + 1);
			}
			return;
		}
		foreach ($xp->query('//*[local-name()="url"]') ?: [] as $node) {
			$loc = trim((string)($xp->query('./*[local-name()="loc"]', $node)?->item(0)?->nodeValue ?? ''));
			if ($loc === '') {
				continue;
			}
			$norm = Url::normalize($loc) ?? $loc;
			$lastmod = trim((string)($xp->query('./*[local-name()="lastmod"]', $node)?->item(0)?->nodeValue ?? ''));
			$cf = trim((string)($xp->query('./*[local-name()="changefreq"]', $node)?->item(0)?->nodeValue ?? ''));
			$pr = trim((string)($xp->query('./*[local-name()="priority"]', $node)?->item(0)?->nodeValue ?? ''));
			$this->urls[$norm] = [
				'lastmod'    => $lastmod !== '' ? $lastmod : null,
				'changefreq' => $cf !== '' ? $cf : null,
				'priority'   => $pr !== '' ? $pr : null,
				'source'     => $url,
			];
		}
	}
}

/**
 * Разбор HTML: DOM + XPath (регулярки не используются — иначе ломается
 * кириллица, вложенность и произвольный порядок атрибутов).
 */
final class Html
{
	/** Размер bottom-k эскиза (кандидаты для поиска дублей). */
	private const SKETCH_SIZE = 128;

	/** @return array<string,mixed> */
	public static function parse(string $html, string $contentType = '', string $charsetHint = ''): array
	{
		$html = self::toUtf8($html, $contentType, $charsetHint);
		if (trim($html) === '') {
			return self::emptyResult('');
		}

		$doc = new \DOMDocument();
		$prev = libxml_use_internal_errors(true);
		$enc = mb_encode_numericentity($html, [0x80, 0x10FFFF, 0, 0x1FFFFF], 'UTF-8');
		$ok = $doc->loadHTML($enc, LIBXML_NOWARNING | LIBXML_NOERROR | LIBXML_NONET | LIBXML_COMPACT);
		libxml_clear_errors();
		libxml_use_internal_errors($prev);
		if (!$ok) {
			return self::emptyResult($html);
		}
		$xp = new \DOMXPath($doc);

		// --- title ---
		$title = trim(preg_replace('~\s+~u', ' ', (string)($xp->query('//head/title')->item(0)?->textContent ?? '')));

		// --- meta-теги ---
		$meta = [];
		foreach ($xp->query('//head/meta') ?: [] as $m) {
			$name = strtolower(trim((string)$m->getAttribute('name')));
			$prop = strtolower(trim((string)$m->getAttribute('property')));
			$httpEquiv = strtolower(trim((string)$m->getAttribute('http-equiv')));
			$content = trim((string)$m->getAttribute('content'));
			$key = $name !== '' ? $name : ($prop !== '' ? $prop : $httpEquiv);
			if ($key === '') {
				continue;
			}
			if (!isset($meta[$key])) {
				$meta[$key] = $content;
			}
		}

		// --- link-теги ---
		$canonical = null;
		$hreflang = [];
		$icons = [];
		$stylesheets = [];
		$preloads = 0;
		foreach ($xp->query('//head/link') ?: [] as $l) {
			$rel = strtolower(trim((string)$l->getAttribute('rel')));
			$href = trim((string)$l->getAttribute('href'));
			$parts = preg_split('~\s+~', $rel) ?: [];
			if (in_array('canonical', $parts, true) && $canonical === null) {
				$canonical = $href;
			}
			if (in_array('alternate', $parts, true) && $l->hasAttribute('hreflang')) {
				$hreflang[] = ['lang' => trim((string)$l->getAttribute('hreflang')), 'href' => $href];
			}
			if (in_array('icon', $parts, true) || in_array('shortcut', $parts, true) || in_array('apple-touch-icon', $parts, true)) {
				$icons[] = $href;
			}
			if (in_array('stylesheet', $parts, true)) {
				$stylesheets[] = $href;
			}
			if (in_array('preload', $parts, true)) {
				$preloads++;
			}
		}

		// --- заголовки ---
		$headings = [];
		$headingCounts = [1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0, 6 => 0];
		$h1Texts = [];
		foreach ($xp->query('//h1|//h2|//h3|//h4|//h5|//h6') ?: [] as $h) {
			$level = (int)substr(strtolower($h->nodeName), 1);
			$txt = trim(preg_replace('~\s+~u', ' ', (string)$h->textContent));
			$headingCounts[$level]++;
			$headings[] = ['level' => $level, 'text' => $txt];
			if ($level === 1) {
				$h1Texts[] = $txt;
			}
		}

		// --- зоны страницы: где именно на странице стоит элемент ---
		// (текст статьи, меню, подвал, шапка, боковая колонка) — нужно, чтобы в
		// отчёте было видно МЕСТО находки, а не только URL
		$zoneOf = [];
		$zoneQueries = [
			'//main'    => 'текст страницы',
			'//article' => 'текст статьи',
			'//nav'     => 'меню',
			'//header'  => 'шапка',
			'//footer'  => 'подвал',
			'//aside'   => 'боковая колонка',
		];
		foreach ($zoneQueries as $q => $zone) {
			foreach ($xp->query($q) ?: [] as $box) {
				foreach ($xp->query('.//a[@href]|.//area[@href]', $box) as $node) {
					$zoneOf[spl_object_id($node)] = $zone;
				}
				foreach ($xp->query('.//img', $box) as $node) {
					$zoneOf[spl_object_id($node)] = $zone;
				}
			}
		}

		// --- ссылки ---
		$links = [];
		foreach ($xp->query('//a[@href]|//area[@href]') ?: [] as $a) {
			$raw = trim((string)$a->getAttribute('href'));
			$text = trim(preg_replace('~\s+~u', ' ', (string)$a->textContent));
			$linkTitle = trim((string)$a->getAttribute('title'));
			$rel = strtolower(trim((string)$a->getAttribute('rel')));
			$links[] = [
				'raw'    => $raw,
				'text'   => $text !== '' ? $text : $linkTitle,
				'rel'    => $rel,
				'target' => strtolower(trim((string)$a->getAttribute('target'))),
				'title'  => $linkTitle,
				'hidden' => strtolower(trim((string)$a->getAttribute('aria-hidden'))) === 'true',
				'zone'   => $zoneOf[spl_object_id($a)] ?? 'вне основных блоков',
			];
		}

		// --- изображения ---
		$images = [];
		foreach ($xp->query('//img') ?: [] as $im) {
			$src = trim((string)$im->getAttribute('src'));
			$alt = $im->hasAttribute('alt') ? (string)$im->getAttribute('alt') : null;
			$w = $im->hasAttribute('width') ? (int)$im->getAttribute('width') : null;
			$h = $im->hasAttribute('height') ? (int)$im->getAttribute('height') : null;
			$images[] = [
				'src'     => $src,
				'alt'     => $alt,
				'w'       => ($w !== null && $w > 0) ? $w : null,
				'h'       => ($h !== null && $h > 0) ? $h : null,
				'loading' => strtolower(trim((string)$im->getAttribute('loading'))),
				'srcset'  => $im->hasAttribute('srcset') && trim((string)$im->getAttribute('srcset')) !== '',
				'fetchprio'=> strtolower(trim((string)$im->getAttribute('fetchpriority'))),
				'zone'    => $zoneOf[spl_object_id($im)] ?? 'вне основных блоков',
			];
		}

		// --- подгружаемые ресурсы (mixed content, вес страницы) ---
		$resources = [];
		foreach ($xp->query('//img[@src]|//script[@src]|//iframe[@src]|//source[@src]|//video[@src]|//audio[@src]|//embed[@src]|//object[@data]') ?: [] as $r) {
			$u = trim((string)($r->getAttribute('src') ?: $r->getAttribute('data')));
			if ($u !== '') {
				$resources[] = ['url' => $u, 'tag' => strtolower($r->nodeName)];
			}
		}
		foreach ($xp->query('//link[@href]') ?: [] as $l) {
			$rel = strtolower(trim((string)$l->getAttribute('rel')));
			if (preg_match('~stylesheet|preload|prefetch|icon|manifest~', $rel)) {
				$u = trim((string)$l->getAttribute('href'));
				if ($u !== '') {
					$resources[] = ['url' => $u, 'tag' => 'link'];
				}
			}
		}

		// --- JSON-LD ---
		$jsonld = [];
		foreach ($xp->query('//script[@type="application/ld+json"]') ?: [] as $s) {
			$raw = trim((string)$s->textContent);
			$entry = ['raw' => $raw, 'ok' => false, 'types' => [], 'error' => null, 'nodes' => 0];
			if ($raw === '') {
				$entry['error'] = 'Пустой блок JSON-LD';
			} else {
				$decoded = json_decode($raw, true);
				if (json_last_error() !== JSON_ERROR_NONE) {
					$entry['error'] = 'Некорректный JSON: ' . json_last_error_msg();
				} else {
					$entry['ok'] = true;
					$entry['nodes'] = self::countJsonLdNodes($decoded);
					foreach (self::collectJsonLdTypes($decoded) as $t) {
						$entry['types'][$t] = ($entry['types'][$t] ?? 0) + 1;
					}
				}
			}
			$jsonld[] = $entry;
		}

		// --- формы ---
		$forms = ['count' => 0, 'noLabel' => 0, 'noName' => 0, 'noAction' => 0, 'fields' => 0, 'bad' => []];
		foreach ($xp->query('//form') ?: [] as $f) {
			$forms['count']++;
			if (trim((string)$f->getAttribute('action')) === '') {
				$forms['noAction']++;
			}
			foreach ($xp->query('.//input|.//select|.//textarea', $f) ?: [] as $field) {
				$type = strtolower((string)$field->getAttribute('type'));
				if ($type === 'hidden' || $type === 'submit' || $type === 'button' || $type === 'reset' || $type === 'image') {
					continue;
				}
				$forms['fields']++;
				if (trim((string)$field->getAttribute('name')) === '') {
					$forms['noName']++;
				}
				$id = trim((string)$field->getAttribute('id'));
				$hasLabel = $id !== '' && $xp->query(sprintf('//label[@for=%s]', self::xpathLiteral($id)))->length > 0;
				if (!$hasLabel) {
					$hasLabel = $xp->query('.//label', $field)->length > 0;
				}
				if (!$hasLabel) {
					$hasLabel = $xp->query('ancestor::label', $field)->length > 0;   // <label>…<input>…</label>
				}
				if (!$hasLabel) {
					$hasLabel = $field->hasAttribute('aria-label') || $field->hasAttribute('aria-labelledby');
				}
				if (!$hasLabel) {
					$forms['noLabel']++;
					$forms['bad'][] = [
						'name'   => trim((string)$field->getAttribute('name')) ?: '(без name)',
						'type'   => $type !== '' ? $type : $field->nodeName,
						'action' => trim((string)$f->getAttribute('action')) ?: '(action не задан)',
						'ph'     => trim((string)$field->getAttribute('placeholder')),
					];
				}
			}
		}

		// --- iframe ---
		$iframes = ['count' => 0, 'noTitle' => 0];
		foreach ($xp->query('//iframe') ?: [] as $fr) {
			$iframes['count']++;
			if (trim((string)$fr->getAttribute('title')) === '' && !$fr->hasAttribute('aria-label')) {
				$iframes['noTitle']++;
			}
		}

		// --- id ---
		$ids = [];
		foreach ($xp->query('//*[@id]') ?: [] as $n) {
			$ids[] = (string)$n->getAttribute('id');
		}
		$idCount = array_count_values($ids);
		$dupIds = array_keys(array_filter($idCount, static fn($c) => $c > 1));

		// --- скрипты/стили ---
		$scripts = $xp->query('//script')->length;
		$inlineScripts = $xp->query('//script[not(@src)]')->length;
		$styles = $xp->query('//style')->length;
		$blocking = $stylesheets;
		$blockingJs = 0;
		foreach ($xp->query('//head/script[@src]') ?: [] as $s) {
			$async = strtolower(trim((string)$s->getAttribute('async'))) !== 'false';
			$defer = strtolower(trim((string)$s->getAttribute('defer'))) !== 'false';
			$type = strtolower(trim((string)$s->getAttribute('type')));
			if (!$async && !$defer && $type !== 'module') {
				$blockingJs++;
			}
		}

		// --- видимый текст ---
		foreach (['//script', '//style', '//noscript', '//template', '//svg', '//iframe'] as $xpPath) {
			foreach ($xp->query($xpPath) ?: [] as $n) {
				$n->parentNode?->removeChild($n);
			}
		}
		$body = $xp->query('//body')->item(0);
		$text = (string)($body?->textContent ?? '');
		$text = trim(preg_replace('~\s+~u', ' ', $text) ?? '');
		$words = preg_match_all('~[\p{L}\p{N}]+~u', $text) ?: 0;

		// --- «основной» текст без навигации/футера/шапки (для поиска дублей) ---
		foreach (['//nav', '//header', '//footer', '//aside'] as $xpPath) {
			foreach ($xp->query($xpPath) ?: [] as $n) {
				$n->parentNode?->removeChild($n);
			}
		}
		$bodyMain = $xp->query('//body')->item(0);
		$mainText = (string)($bodyMain?->textContent ?? '');
		$mainText = trim(preg_replace('~\s+~u', ' ', $mainText) ?? '');
		if (mb_strlen($mainText) < 200) {
			$mainText = $text;   // нет явного main — берём весь текст
		}

		// --- сигналы цитируемости (GEO) ---
		$contentLinks = $xp->query('//main//a[@href]|//article//a[@href]')->length;
		$firstPara = '';
		$fp = $xp->query('//main//p|//article//p|//body//p')->item(0);
		if ($fp !== null) {
			$firstPara = trim(preg_replace('~\s+~u', ' ', (string)$fp->textContent) ?? '');
		}
		$statsCount = preg_match_all('~\b\d[\d\s.,]{0,8}\s?(?:%|‰|руб|₽|млн|млрд|тыс|см|м|м²|м3|кг|т|лет|дн|шт)~u', $mainText) ?: 0;
		$quoteCount = preg_match_all('~[«"„]~u', $mainText) ?: 0;

		// --- частотность слов по ПОЛНОМУ тексту (без усечения) ---
		$topKeywords = [];
		$wordList = preg_split('~[^\p{L}\p{N}]+~u', mb_strtolower($mainText), -1, PREG_SPLIT_NO_EMPTY) ?: [];
		$wordTotal = count($wordList);
		if ($wordTotal >= 150) {
			$stop = ['и','в','на','с','по','для','не','что','это','как','мы','вы','наш','ваш','к','а','из','до','за','то','же','бы','их','при','более','всего','также','или','если','чтобы','этот'];
			$counts = array_count_values($wordList);
			arsort($counts);
			$seen = [];
			foreach ($counts as $w => $n) {
				$w = (string)$w;
				if (mb_strlen($w) < 4 || in_array($w, $stop, true) || isset($seen[$w])) {
					continue;
				}
				$seen[$w] = true;
				$topKeywords[] = ['w' => $w, 'n' => $n, 'share' => $n / $wordTotal];
				if (count($topKeywords) >= 3) {
					break;
				}
			}
		}

		// --- язык ---
		$htmlNode = $xp->query('//html')->item(0);
		$lang = $htmlNode !== null ? strtolower(trim((string)$htmlNode->getAttribute('lang'))) : '';
		$script = self::detectScript($text);

		// --- viewport / charset ---
		$viewport = $meta['viewport'] ?? null;
		$charset = null;
		foreach ($xp->query('//head/meta') ?: [] as $m) {
			if (strtolower(trim((string)$m->getAttribute('charset'))) !== '') {
				$charset = trim((string)$m->getAttribute('charset'));
			}
		}
		if ($charset === null && preg_match('~<meta[^>]+charset=[\'"]([^\'"]+)~i', $html, $cm)) {
			$charset = $cm[1];
		}

		// --- ошибки PHP в выводе ---
		// Ловим только настоящий формат ошибки: «Warning: текст in /path/file.php on line 12».
		// Без «on line N» это обычно текст документации, а не ошибка сайта.
		$phpErrors = [];
		$phpPatterns = [
			'~(?<!\w)(Warning|Notice|Deprecated|Fatal error|Parse error|Recoverable fatal error):\s[^\n<]{3,160}\s+in\s+[^\n<]*(?:[/\x5c]|\.php\b)[^\n<]{0,120}\s+on\s+line\s+\d+~i',
			'~(?<!\w)(Uncaught)\s+\w*(Error|Exception)\b[^\n<]{0,160}~i',
			'~(?<!\w)(Warning|Notice|Deprecated):\s*(?:Неопределенн|неопределенн)[^\n<]{0,120}~iu',
		];
		foreach ($phpPatterns as $pat) {
			if (preg_match_all($pat, $html, $mm)) {
				foreach (array_slice(array_unique($mm[0]), 0, 3) as $hit) {
					$phpErrors[] = trim(preg_replace('~\s+~u', ' ', $hit) ?? $hit);
				}
			}
		}

		// --- шинглы для поиска дублей (по основному тексту) ---
		$shingles = self::shingles($mainText);
		// «эскиз» bottom-k: k наименьших хешей — дешёвый кандидат-поиск пар-дублей
		$sketch = $shingles;
		sort($sketch);
		$sketch = array_slice($sketch, 0, self::SKETCH_SIZE);

		// --- доступность и структура ---
		$nodes = $doc->getElementsByTagName('*')->length;
		$hasMain = $xp->query('//main')->length > 0;
		$navCount = $xp->query('//nav')->length;
		$navLinks = $xp->query('//nav//a[@href]')->length;
		$hasSkipLink = false;
		foreach ($xp->query('//a[@href]') ?: [] as $a) {
			$href = strtolower(trim((string)$a->getAttribute('href')));
			$cls = strtolower((string)$a->getAttribute('class'));
			if (str_starts_with($href, '#') && (str_contains($cls, 'skip') || in_array(ltrim($href, '#'), ['content', 'main', 'main-content', 'maincontent', 'content-wrap'], true))) {
				$hasSkipLink = true;
				break;
			}
		}
		$namelessControls = 0;
		foreach ($xp->query('//button|//a|//input[@type="submit"]|//input[@type="button"]') ?: [] as $ctrl) {
			$txt = trim(preg_replace('~\s+~u', ' ', (string)$ctrl->textContent) ?? '');
			$hasName = $txt !== ''
				|| trim((string)$ctrl->getAttribute('title')) !== ''
				|| trim((string)$ctrl->getAttribute('aria-label')) !== ''
				|| $ctrl->getAttribute('aria-labelledby') !== ''
				|| $xp->query('.//img[@alt]', $ctrl)->length > 0;
			if (!$hasName) {
				$namelessControls++;
			}
		}

		// --- структура, удобная для цитирования (GEO) ---
		$lists = $xp->query('//ul|//ol')->length;
		$articles = $xp->query('//article')->length;
		$questions = 0;
		foreach ($xp->query('//h2|//h3|//dt') ?: [] as $q) {
			$t = trim((string)$q->textContent);
			if (str_ends_with($t, '?') || str_ends_with($t, '？')) {
				$questions++;
			}
		}
		$paras = $xp->query('//p')->length;
		$shortParas = 0;
		$longParas = 0;
		foreach ($xp->query('//p') ?: [] as $pnode) {
			$len = mb_strlen(trim((string)$pnode->textContent));
			if ($len >= 40 && $len <= 260) {
				$shortParas++;
			} elseif ($len > 600) {
				$longParas++;
			}
		}
		$hasAuthor = ($meta['author'] ?? '') !== ''
			|| $xp->query('//*[@rel="author"]')->length > 0
			|| $xp->query('//*[contains(@class,"author")]')->length > 0
			|| stripos((string)($xp->query('//body')->item(0)?->textContent ?? ''), 'автор') !== false;
		$hasDate = $xp->query('//time[@datetime]')->length > 0
			|| ($meta['article:published_time'] ?? '') !== ''
			|| ($meta['article:modified_time'] ?? '') !== ''
			|| ($meta['date'] ?? '') !== '';
		$sameAs = 0;
		foreach ($xp->query('//script[@type="application/ld+json"]') ?: [] as $s) {
			$sameAs += substr_count((string)$s->textContent, '"sameAs"');
		}
		$relNext = false;
		$relPrev = false;
		foreach ($xp->query('//link') ?: [] as $l) {
			$rel = strtolower(trim((string)$l->getAttribute('rel')));
			if ($rel === 'next') {
				$relNext = true;
			}
			if ($rel === 'prev') {
				$relPrev = true;
			}
		}
		$fontNoDisplay = 0;
		$inlineCss = '';
		foreach ($xp->query('//style') ?: [] as $st) {
			$inlineCss .= (string)$st->textContent;
		}
		if (preg_match_all('~@font-face\s*\{[^}]*\}~i', $inlineCss, $ff)) {
			foreach ($ff[0] as $block) {
				if (!preg_match('~font-display\s*:~i', $block)) {
					$fontNoDisplay++;
				}
			}
		}
		$faqSchema = false;
		foreach ($xp->query('//script[@type="application/ld+json"]') ?: [] as $s) {
			if (stripos((string)$s->textContent, 'FAQPage') !== false) {
				$faqSchema = true;
			}
		}

		return [
			'title'        => $title,
			'titleLen'     => mb_strlen($title),
			'description'  => $meta['description'] ?? '',
			'descLen'      => mb_strlen((string)($meta['description'] ?? '')),
			'robotsMeta'   => strtolower((string)($meta['robots'] ?? '')),
			'meta'         => $meta,
			'googlebot'    => strtolower((string)($meta['googlebot'] ?? '')),
			'canonical'    => $canonical,
			'hreflang'     => $hreflang,
			'icons'        => $icons,
			'viewport'     => $viewport,
			'charset'      => $charset,
			'lang'         => $lang,
			'og'           => [
				'title'       => $meta['og:title'] ?? '',
				'description' => $meta['og:description'] ?? '',
				'image'       => $meta['og:image'] ?? '',
				'url'         => $meta['og:url'] ?? '',
				'type'        => $meta['og:type'] ?? '',
			],
			'twitter'      => [
				'card'  => $meta['twitter:card'] ?? '',
				'title' => $meta['twitter:title'] ?? '',
				'image' => $meta['twitter:image'] ?? '',
			],
			'jsonld'       => $jsonld,
			'headings'     => $headings,
			'headingCount' => $headingCounts,
			'h1'           => $h1Texts,
			'links'        => $links,
			'images'       => $images,
			'resources'    => $resources,
			'forms'        => $forms,
			'iframes'      => $iframes,
			'ids'          => ['count' => count($ids), 'dupes' => $dupIds],
			'idList'       => array_slice($ids, 0, 300),
			'nodes'        => $nodes,
			'hasMain'      => $hasMain,
			'hasSkipLink'  => $hasSkipLink,
			'navCount'     => $navCount,
			'navLinks'     => $navLinks,
			'namelessControls' => $namelessControls,
			'lists'        => $lists,
			'articles'     => $articles,
			'questions'    => $questions,
			'paras'        => $paras,
			'shortParas'   => $shortParas,
			'longParas'    => $longParas,
			'hasAuthor'    => $hasAuthor,
			'hasDate'      => $hasDate,
			'sameAs'       => $sameAs,
			'relNext'      => $relNext,
			'relPrev'      => $relPrev,
			'fontNoDisplay'=> $fontNoDisplay,
			'faqSchema'    => $faqSchema,
			'contentLinks' => $contentLinks,
			'firstPara'    => mb_substr($firstPara, 0, 600),
			'firstParaLen'=> mb_strlen($firstPara),
			'statsCount'   => $statsCount,
			'topKeywords'  => $topKeywords,
			'quoteCount'   => $quoteCount,
			'scripts'      => $scripts,
			'inlineScripts'=> $inlineScripts,
			'styles'       => $styles,
			'blocking'     => $blocking,
			'blockingJs'   => $blockingJs,
			'preloads'     => $preloads,
			'text'         => mb_substr($text, 0, 4000),
			'textLen'      => mb_strlen($text),
			'mainTextLen'  => mb_strlen($mainText),
			'textHash'     => md5($mainText),
			'words'        => $words,
			'script'       => $script,
			'phpErrors'    => $phpErrors,
			'shingles'     => $shingles,
			'sketch'       => $sketch,
			'htmlBytes'    => strlen($html),
		];
	}

	/** @return array<string,mixed> */
	private static function emptyResult(string $html): array
	{
		return [
			'title' => '', 'titleLen' => 0, 'description' => '', 'descLen' => 0, 'robotsMeta' => '',
			'googlebot' => '', 'canonical' => null, 'hreflang' => [], 'icons' => [], 'viewport' => null,
			'charset' => null, 'lang' => '', 'og' => ['title' => '', 'description' => '', 'image' => '', 'url' => '', 'type' => ''],
			'twitter' => ['card' => '', 'title' => '', 'image' => ''], 'jsonld' => [], 'headings' => [],
			'headingCount' => [1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0, 6 => 0], 'h1' => [], 'links' => [],
			'images' => [], 'resources' => [],
			'forms' => ['count' => 0, 'noLabel' => 0, 'noName' => 0, 'noAction' => 0, 'fields' => 0],
			'iframes' => ['count' => 0, 'noTitle' => 0], 'ids' => ['count' => 0, 'dupes' => []],
			'idList' => [], 'nodes' => 0, 'hasMain' => false, 'hasSkipLink' => false,
			'navCount' => 0, 'navLinks' => 0, 'namelessControls' => 0,
			'lists' => 0, 'articles' => 0, 'questions' => 0, 'paras' => 0, 'shortParas' => 0, 'longParas' => 0,
			'hasAuthor' => false, 'hasDate' => false, 'sameAs' => 0, 'relNext' => false, 'relPrev' => false,
			'fontNoDisplay' => 0, 'faqSchema' => false, 'topKeywords' => [],
			'contentLinks' => 0, 'firstPara' => '', 'firstParaLen' => 0, 'statsCount' => 0, 'quoteCount' => 0,
			'scripts' => 0, 'inlineScripts' => 0, 'styles' => 0, 'blocking' => 0, 'blockingJs' => 0, 'preloads' => 0,
			'text' => '', 'textLen' => 0, 'mainTextLen' => 0, 'textHash' => '', 'words' => 0, 'script' => 'unknown', 'phpErrors' => [],
			'shingles' => [], 'sketch' => [], 'htmlBytes' => strlen($html),
		];
	}

	private static function toUtf8(string $html, string $contentType, string $charsetHint): string
	{
		$charset = '';
		if ($charsetHint !== '') {
			$charset = $charsetHint;
		} elseif (preg_match('~charset=([\w\-]+)~i', $contentType, $m)) {
			$charset = $m[1];
		} elseif (preg_match('~<meta[^>]+charset=[\'"]([^\'"]+)~i', substr($html, 0, 2000), $m)) {
			$charset = $m[1];
		}
		$charset = strtoupper(trim($charset));
		if ($charset === '' || $charset === 'UTF-8' || $charset === 'UTF8') {
			return $html;
		}
		if (in_array($charset, ['WINDOWS-1251', 'CP1251'], true)) {
			$converted = @mb_convert_encoding($html, 'UTF-8', 'Windows-1251');
			return $converted === false ? $html : $converted;
		}
		if (function_exists('iconv')) {
			$converted = @iconv($charset, 'UTF-8//IGNORE', $html);
			if ($converted !== false) {
				return $converted;
			}
		}
		return $html;
	}

	/** @return list<string> */
	public static function collectJsonLdTypes(mixed $node): array
	{
		$types = [];
		if (is_array($node)) {
			if (isset($node['@type'])) {
				$t = $node['@type'];
				foreach (is_array($t) ? $t : [$t] as $one) {
					if (is_string($one)) {
						$types[] = $one;
					}
				}
			}
			if (isset($node['@graph']) && is_array($node['@graph'])) {
				foreach ($node['@graph'] as $child) {
					$types = array_merge($types, self::collectJsonLdTypes($child));
				}
			}
			foreach ($node as $k => $v) {
				if ($k === '@type' || $k === '@graph') {
					continue;
				}
				if (is_array($v) && (isset($v['@type']) || isset($v['@graph']))) {
					$types = array_merge($types, self::collectJsonLdTypes($v));
				}
			}
		}
		return $types;
	}

	public static function countJsonLdNodes(mixed $node): int
	{
		if (!is_array($node)) {
			return 0;
		}
		if (isset($node['@graph']) && is_array($node['@graph'])) {
			return count($node['@graph']);
		}
		return 1;
	}

	/** Определение доминирующего алфавита текста. */
	public static function detectScript(string $text): string
	{
		$sample = mb_substr($text, 0, 4000);
		$cyr = preg_match_all('~[\p{Cyrillic}]~u', $sample) ?: 0;
		$lat = preg_match_all('~[A-Za-z]~u', $sample) ?: 0;
		if ($cyr === 0 && $lat === 0) {
			return 'unknown';
		}
		return $cyr >= $lat ? 'cyrillic' : 'latin';
	}

	/** @return list<int> хеши триграмм слов (для поиска дублей). */
	public static function shingles(string $text): array
	{
		$text = mb_strtolower($text);
		$words = preg_split('~[^\p{L}\p{N}]+~u', $text, -1, PREG_SPLIT_NO_EMPTY) ?: [];
		if (count($words) < 3) {
			return [];
		}
		$set = [];
		$count = count($words);
		$step = 1;
		if ($count > 1200) {
			$step = (int)ceil($count / 1200);
		}
		for ($i = 0; $i + 2 < $count; $i += $step) {
			$gram = $words[$i] . ' ' . $words[$i + 1] . ' ' . $words[$i + 2];
			$set[crc32($gram)] = true;
		}
		return array_map('intval', array_keys($set));
	}

	public static function xpathLiteral(string $s): string
	{
		if ($s === '' || !preg_match('~[^\w\-]~u', $s)) {
			return "'" . $s . "'";
		}
		if (str_contains($s, "'")) {
			return "concat('" . str_replace("'", "',\"'\",'", $s) . "')";
		}
		return "'" . $s . "'";
	}
}

/**
 * Краулер: BFS-обход, граф внутренних ссылок, внешние проверки, зондирование.
 */
final class Crawler
{
	/** Расширения, которые не обходятся (статика/медиа). */
	private const SKIP_EXT = [
		'jpg', 'jpeg', 'png', 'gif', 'webp', 'avif', 'svg', 'ico', 'bmp', 'tiff',
		'css', 'js', 'mjs', 'map', 'json', 'rss', 'atom',
		'woff', 'woff2', 'ttf', 'otf', 'eot',
		'mp4', 'webm', 'ogv', 'mov', 'avi', 'mp3', 'wav', 'ogg',
		'pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx',
		'zip', 'rar', 'gz', 'tar', '7z', 'iso', 'dmg',
		'csv', 'txt', 'log', 'bak', 'swf', 'apk', 'exe', 'dll',
	];

	/** Пути, которые не должны быть доступны извне. */
	private const SENSITIVE_PATHS = [
		'/.env',
		'/.env.local',
		'/.env.production',
		'/.git/config',
		'/.git/HEAD',
		'/.gitignore',
		'/.htaccess',
		'/.htpasswd',
		'/.DS_Store',
		'/composer.json',
		'/composer.lock',
		'/package.json',
		'/package-lock.json',
		'/phpinfo.php',
		'/info.php',
		'/server-status',
		'/server-info',
		'/.svn/entries',
		'/.well-known/security.txt',
		'/backup.sql',
		'/dump.sql',
		'/config.php',
		'/wp-config.php.bak',
	];

	public Cfg $cfg;
	public Ui $ui;
	/** @var array<string,array<string,mixed>> страницы по URL */
	public array $pages = [];
	/** @var array<string,array{count:int,from:array<string,int>,anchors:array<string,int>}> */
	public array $inlinks = [];
	/** @var array<string,array<string,int>> */
	public array $outlinks = [];
	/** @var array<string,array{count:int,from:array<string,int>}> */
	public array $externalLinks = [];
	/** @var array<string,int> внешние хосты → число ссылок */
	public array $externalHosts = [];
	/** @var array<string,array{status:int,error:?string}> */
	public array $externalStatus = [];
	/** @var array<string,array{status:int,error:?string}> внутренние не-HTML ссылки */
	public array $assetStatus = [];
	/** @var array<string,int> внутренние изображения (src) → число вхождений */
	public array $imageUrls = [];
	/** @var array<string,array{status:int,error:?string}> */
	public array $probeStatus = [];
	/** @var array<string,mixed> данные TLS-сертификата */
	public array $tls = [];
	/** @var array<string,int> URL из sitemap → код ответа */
	public array $sitemapStatus = [];
	/** @var list<string> URL из файла-списка /pages (если есть) */
	public array $urlList = [];
	/** @var array<string,mixed> содержимое /llms.txt (если есть) */
	public array $llmsTxt = [];
	/** @var array<string,mixed> манифесты для AI: ai-plugin.json, .well-known/agents.json */
	public array $aiManifests = [];
	public ?Robots $robots = null;
	public SitemapSet $sitemaps;
	/** @var list<string> */
	public array $notes = [];
	public float $duration = 0.0;
	public int $requests = 0;
	public int $retries = 0;
	/** @var array<string,float> тайминги фаз (сек) */
	public array $timings = [];

	public function __construct(Cfg $cfg, Ui $ui)
	{
		$this->cfg = $cfg;
		$this->ui = $ui;
		$this->sitemaps = new SitemapSet();
	}

	public function run(): void
	{
		$t0 = microtime(true);
		$start = $this->cfg->start;

		// --- robots.txt ---
		$phase = microtime(true);
		if ($this->cfg->readRobots) {
			$req = Http::fetch(rtrim(Url::origin($start), '/') . '/robots.txt', $this->cfg);
			$this->requests++;
			if ($req->status === 200) {
				$this->robots = Robots::parse($req->body);
				$this->robots->status = 200;
				$this->robots->exists = true;
			} else {
				$this->robots = new Robots();
				$this->robots->status = $req->status;
				$this->robots->exists = false;
				if ($req->status !== 404) {
					$this->notes[] = 'robots.txt: HTTP ' . ($req->status ?: 'нет ответа');
				}
			}
		}
		$this->timings['robots'] = round(microtime(true) - $phase, 2);

		// --- sitemap ---
		$phase = microtime(true);
		$this->sitemaps->load(Url::origin($start), $this->cfg);
		$this->requests += count($this->sitemaps->sitemaps) + 1;
		$this->timings['sitemap'] = round(microtime(true) - $phase, 2);

		// --- список URL /pages (для Screaming Frog и ручных проверок) ---
		$phase = microtime(true);
		$listReq = Http::fetch(rtrim(Url::origin($start), '/') . '/pages', $this->cfg);
		if ($listReq->status === 200 && $listReq->body !== '') {
			foreach (preg_split('~\R~', $listReq->body) ?: [] as $line) {
				$line = trim($line);
				if ($line === '' || !preg_match('~^https?://~i', $line)) {
					continue;
				}
				$norm = Url::normalize($line);
				if ($norm !== null) {
					$this->urlList[] = $norm;
				}
			}
			$this->urlList = array_values(array_unique($this->urlList));
		}
		$this->timings['url_list'] = round(microtime(true) - $phase, 2);

		// --- AI/GEO: llms.txt и манифесты ---
		$phase = microtime(true);
		$origin = Url::origin($start);
		$llms = Http::fetch($origin . '/llms.txt', $this->cfg);
		if ($llms->status === 200) {
			$this->llmsTxt = [
				'status'  => 200,
				'len'     => strlen($llms->body),
				'links'   => preg_match_all('~https?://[^\s\)\]]+~i', $llms->body) ?: 0,
				'hasTitle'=> (bool)preg_match('~^#\s+\S+~m', $llms->body),
				'body'    => mb_substr($llms->body, 0, 4000),
			];
		} else {
			$this->llmsTxt = ['status' => $llms->status];
		}
		foreach (['/ai-plugin.json', '/.well-known/agents.json'] as $mf) {
			$r = Http::fetch($origin . $mf, $this->cfg);
			$this->aiManifests[$mf] = [
				'status' => $r->status,
				'ctype'  => strtolower($r->header('content-type')),
				'json'   => $r->status === 200 ? json_decode($r->body, true) : null,
				'len'    => strlen($r->body),
			];
		}
		$this->timings['ai'] = round(microtime(true) - $phase, 2);

		// --- обход ---
		$phase = microtime(true);
		$this->crawl($start);
		$this->timings['crawl'] = round(microtime(true) - $phase, 2);

		// --- внешние ссылки ---
		$phase = microtime(true);
		if ($this->cfg->external && $this->cfg->externalLimit > 0) {
			$this->checkExternal();
		}
		$this->timings['external'] = round(microtime(true) - $phase, 2);
		// --- внутренние не-HTML ссылки (pdf, css, картинки) ---
		$phase = microtime(true);
		$this->checkAssets();
		$this->timings['assets'] = round(microtime(true) - $phase, 2);
		// --- URL из sitemap, до которых не дошли ---
		$phase = microtime(true);
		$this->checkSitemapUrls();
		$this->timings['sitemap_check'] = round(microtime(true) - $phase, 2);
		// --- служебные пути ---
		$phase = microtime(true);
		if ($this->cfg->probe) {
			$this->probeSensitive();
		} elseif ($this->cfg->mode === 'global') {
			$this->notes[] = 'Проверка служебных файлов (.env, .git) отключена для публичных сайтов — включается флагом --probe';
		}
		$this->timings['probe'] = round(microtime(true) - $phase, 2);

		// --- TLS-сертификат (только для https) ---
		$phase = microtime(true);
		$this->tls = $this->checkTls();
		$this->timings['tls'] = round(microtime(true) - $phase, 2);

		$this->duration = microtime(true) - $t0;
	}

	/**
	 * Данные TLS-сертификата: срок действия, соответствие имени, алгоритм.
	 *
	 * @return array<string,mixed>
	 */
	private function checkTls(): array
	{
		$origin = $this->cfg->start;
		if (!str_starts_with($origin, 'https://')) {
			return [];
		}
		$parts = parse_url($origin);
		$host = $parts['host'] ?? '';
		$port = (int)($parts['port'] ?? 443);
		if ($host === '') {
			return [];
		}
		$ctx = stream_context_create([
			'ssl' => [
				'capture_peer_cert' => true,
				'verify_peer'       => false,
				'verify_peer_name'  => false,
			],
		]);
		$errno = 0;
		$errstr = '';
		$fp = @stream_socket_client(
			'ssl://' . $host . ':' . $port,
			$errno,
			$errstr,
			10,
			STREAM_CLIENT_CONNECT,
			$ctx
		);
		if ($fp === false) {
			return ['error' => 'Не удалось установить TLS-соединение: ' . $errstr];
		}
		$params = stream_context_get_params($fp);
		$cert = $params['options']['ssl']['peer_certificate'] ?? null;
		fclose($fp);
		if ($cert === null || $cert === '') {
			return ['error' => 'Сертификат не получен'];
		}
		$info = @openssl_x509_parse($cert);   // PHP 8: принимает OpenSSLCertificate
		if (!is_array($info)) {
			return ['error' => 'Сертификат не удалось разобрать'];
		}
		$to = (int)($info['validTo_time_t'] ?? 0);
		$from = (int)($info['validFrom_time_t'] ?? 0);
		$names = [];
		if (!empty($info['extensions']['subjectAltName'])) {
			foreach (explode(',', (string)$info['extensions']['subjectAltName']) as $n) {
				$n = trim($n);
				if (str_starts_with($n, 'DNS:')) {
					$names[] = substr($n, 4);
				}
			}
		}
		$cn = $info['subject']['CN'] ?? '';
		if ($cn !== '' && !in_array($cn, $names, true)) {
			$names[] = $cn;
		}
		$hostOk = false;
		foreach ($names as $n) {
			if (str_starts_with($n, '*.')) {
				$base = substr($n, 2);
				if (str_ends_with(strtolower($host), '.' . strtolower($base))) {
					$hostOk = true;
					break;
				}
			} elseif (strcasecmp($n, $host) === 0) {
				$hostOk = true;
				break;
			}
		}
		return [
			'valid_from'  => $from,
			'valid_to'    => $to,
			'days_left'   => $to > 0 ? (int)floor(($to - time()) / 86400) : null,
			'expired'     => $to > 0 && $to < time(),
			'host_ok'     => $hostOk,
			'names'       => $names,
			'issuer'      => (string)($info['issuer']['CN'] ?? ($info['issuer']['O'] ?? '—')),
			'signature'   => (string)($info['signatureTypeSN'] ?? '—'),
			'self_signed' => isset($info['issuer']['CN'], $info['subject']['CN']) && $info['issuer']['CN'] === $info['subject']['CN'],
			'key_size'    => (int)($info['publicKey']['bits'] ?? 0),
		];
	}

	/**
	 * BFS-обход.
	 */
	private function crawl(string $start): void
	{
		$cfg = $this->cfg;
		$queue = [$start => 0];
		$seen = [];
		$querySeen = 0;
		$origin = Url::origin($start);
		$done = 0;

		while ($queue !== [] && count($this->pages) < $cfg->limit) {
			$batch = [];
			while (count($batch) < $cfg->concurrency && $queue !== []) {
				$url = array_key_first($queue);
				$depth = $queue[$url];
				unset($queue[$url]);
				if (isset($seen[$url])) {
					continue;
				}
				$seen[$url] = true;

				if ($depth > $cfg->depth) {
					continue;
				}
				if (str_contains($url, '?') && ++$querySeen > 60) {
					continue;
				}
				if ($this->skipUrl($url)) {
					continue;
				}
				$batch[$url] = $depth;
			}
			if ($batch === []) {
				break;
			}

			$results = Http::fetchMany(array_keys($batch), $cfg);
			$this->requests += count($batch);
			$this->pace();
			// Неудачные соединения (перегруженный однопоточный сервер) — повторяем по одному
			$retried = $this->retryFailed($results, array_keys($batch));
			if ($retried > 0) {
				$this->retries += $retried;
			}

			foreach ($results as $url => $req) {
				$depth = $batch[$url];
				$page = $this->buildPage($url, $req, $depth, $origin);
				$this->pages[$url] = $page;

				if ($req->status >= 200 && $req->status < 400 && $req->error === null) {
					$isHtml = $this->isHtml($req, $page);
					if ($isHtml) {
						$this->extractLinks($page, $origin, $queue, $depth, $seen);
					}
				}
			}

			$done += count($batch);
			// DOMDocument содержит циклические ссылки — без сборки циклов память растёт
			gc_collect_cycles();
			$this->progress($done, count($this->pages));
		}
		$this->progressDone();
	}

	/** @return array<string,mixed> */
	private function buildPage(string $url, Req $req, int $depth, string $origin): array
	{
		$contentType = $req->header('content-type');
		$charset = '';
		if (preg_match('~charset=([\w\-]+)~i', $contentType, $m)) {
			$charset = $m[1];
		}
		$html = ($req->status >= 200 && $req->status < 400) ? $req->body : '';
		$parsed = $html !== '' ? Html::parse($html, $contentType, $charset) : Html::parse('', $contentType, $charset);

		return [
			'url'         => $url,
			'path'        => Url::displayPath($url),
			'status'      => $req->status,
			'final'       => $req->final,
			'redirects'   => $req->redirects,
			'depth'       => $depth,
			'headers'     => $req->headers,
			'headerLog'   => $req->headerLog,
			'bytes'       => $req->bytes,
			'bodySize'    => strlen($req->body),
			'timeTotal'   => $req->timeTotal,
			'ttfb'        => $req->ttfb,
			'connect'     => $req->connect,
			'httpVersion' => $req->httpVersion,
			'contentType' => $contentType,
			'error'       => $req->error,
			'html'        => $parsed,
		];
	}

	private function isHtml(Req $req, array $page): bool
	{
		$ct = strtolower($page['contentType']);
		if ($ct !== '' && !str_contains($ct, 'html')) {
			return false;
		}
		$body = ltrim(substr($req->body, 0, 200));
		return $body === '' || str_starts_with($body, '<');
	}

	/**
	 * Разбор ссылок страницы: внутренние → входящие/исходящие, внешние → список.
	 */
	private function extractLinks(array $page, string $origin, array &$queue, int $depth, array $seen): void
	{
		$from = $page['url'];
		$links = $page['html']['links'];

		// изображения (<img src>) — тоже проверяем на доступность
		foreach ($page['html']['images'] as $im) {
			$src = trim((string)$im['src']);
			if ($src === '' || str_starts_with($src, 'data:')) {
				continue;
			}
			$abs = Url::resolve($src, $from);
			if ($abs === null) {
				continue;
			}
			[$abs] = Url::splitFragment($abs);
			$abs = Url::normalize($abs) ?? $abs;
			if (Url::hostOf($abs) === Url::hostOf($origin)) {
				$this->imageUrls[$abs] = ($this->imageUrls[$abs] ?? 0) + 1;
			}
		}

		// og:image тоже проверяем на доступность
		{
			$src = trim((string)($page['html']['og']['image'] ?? ''));
			if ($src !== '' && !str_starts_with($src, 'data:')) {
				$abs = Url::resolve($src, $from);
				if ($abs !== null) {
					[$abs] = Url::splitFragment($abs);
					$abs = Url::normalize($abs) ?? $abs;
					if (Url::hostOf($abs) === Url::hostOf($origin)) {
						$this->imageUrls[$abs] = ($this->imageUrls[$abs] ?? 0) + 1;
					}
				}
			}
		}

		foreach ($links as $link) {
			$raw = $link['raw'];
			$abs = Url::resolve($raw, $from);
			if ($abs === null) {
				continue;
			}
			[$abs, $fragment] = Url::splitFragment($abs);
			$abs = Url::normalize($abs) ?? $abs;
			if ($abs === '' || !preg_match('~^https?://~i', $abs)) {
				continue;
			}
			$isInternal = Url::hostOf($abs) === Url::hostOf($origin) || Url::sameSite($abs, $origin);

			if ($isInternal) {
				$this->inlinks[$abs] ??= ['count' => 0, 'from' => [], 'anchors' => [], 'src' => []];
				$this->inlinks[$abs]['count']++;
				$this->inlinks[$abs]['from'][$from] = ($this->inlinks[$abs]['from'][$from] ?? 0) + 1;
				$anchor = trim($link['text']);
				if ($anchor !== '') {
					$this->inlinks[$abs]['anchors'][$anchor] = ($this->inlinks[$abs]['anchors'][$anchor] ?? 0) + 1;
				}
				// откуда именно найдена ссылка: страница, текст ссылки, зона страницы
				$srcList = &$this->inlinks[$abs]['src'];
				if (isset($srcList[$from])) {
					$srcList[$from]['n']++;
				} elseif (count($srcList) < 8) {
					// детализируем до 8 разных источников на цель — этого хватает для отчёта
					$srcList[$from] = ['n' => 1, 'anchors' => [], 'zones' => []];
				}
				if (isset($srcList[$from])) {
					if ($anchor !== '') {
						$srcList[$from]['anchors'][$anchor] = ($srcList[$from]['anchors'][$anchor] ?? 0) + 1;
					}
					$zone = (string)($link['zone'] ?? '');
					if ($zone !== '') {
						$srcList[$from]['zones'][$zone] = ($srcList[$from]['zones'][$zone] ?? 0) + 1;
					}
				}
				unset($srcList);

				$this->outlinks[$from] ??= [];
				$this->outlinks[$from][$abs] = ($this->outlinks[$from][$abs] ?? 0) + 1;

				if (!isset($seen[$abs]) && !isset($queue[$abs]) && $depth + 1 <= $this->cfg->depth) {
					if (!$this->skipUrl($abs)) {
						$queue[$abs] = $depth + 1;
					}
				}
				continue;
			}

			// внешняя ссылка
			$this->externalLinks[$abs] ??= ['count' => 0, 'from' => []];
			$this->externalLinks[$abs]['count']++;
			$this->externalLinks[$abs]['from'][$from] = ($this->externalLinks[$abs]['from'][$from] ?? 0) + 1;
			$host = Url::hostOf($abs);
			$this->externalHosts[$host] = ($this->externalHosts[$host] ?? 0) + 1;
		}
	}

	private function skipUrl(string $url): bool
	{
		$path = parse_url($url, PHP_URL_PATH) ?: '/';
		$ext = strtolower((string)pathinfo($path, PATHINFO_EXTENSION));
		if ($ext !== '' && in_array($ext, self::SKIP_EXT, true)) {
			return true;
		}
		foreach ($this->cfg->exclude as $pattern) {
			if (@preg_match($pattern, $url) === 1) {
				return true;
			}
		}
		if ($this->cfg->only !== []) {
			$ok = false;
			foreach ($this->cfg->only as $prefix) {
				if (str_starts_with($path, $prefix) || $path === $prefix) {
					$ok = true;
					break;
				}
			}
			if (!$ok) {
				return true;
			}
		}
		if ($this->cfg->respectRobots && $this->robots !== null) {
			$pathAndQuery = $path . (parse_url($url, PHP_URL_QUERY) ? '?' . parse_url($url, PHP_URL_QUERY) : '');
			if (!$this->robots->allowed($pathAndQuery)) {
				return true;
			}
		}
		return false;
	}

	/**
	 * Пауза между пачками запросов (вежливость к серверу).
	 */
	private function pace(): void
	{
		if ($this->cfg->delayMs > 0) {
			usleep($this->cfg->delayMs * 1000);
		}
	}

	/**
	 * Повтор неудачных запросов по одному.
	 *
	 * Зачем: однопоточный сервер (в т.ч. php -S) не выдерживает параллельные
	 * запросы — часть соединений отваливается по таймауту. Без повтора такие
	 * сбои выглядят как «битые ссылки», что неверно. Повторяем последовательно.
	 *
	 * @param array<string,Req> $results
	 * @param list<string>       $urls
	 */
	private function retryFailed(array &$results, array $urls, string $method = 'GET', bool $keepBody = false): int
	{
		$failed = [];
		foreach ($urls as $u) {
			$r = $results[$u] ?? null;
			if ($r !== null && $r->status === 0) {
				$failed[] = $u;
			}
		}
		if (!$failed) {
			return 0;
		}
		$fixed = 0;
		foreach ($failed as $u) {
			$this->pace();
			$r = Http::fetch($u, $this->cfg, $method, $keepBody);
			if ($r->status > 0) {
				$results[$u] = $r;
				$fixed++;
			}
		}
		return $fixed;
	}

	/**
	 * Запросы пачками по concurrency — нельзя открывать сотни соединений сразу.
	 *
	 * @param list<string> $urls
	 * @return array<string,Req>
	 */
	private function fetchBatched(array $urls, string $method = 'GET', bool $keepBody = false, ?int $timeoutMs = null): array
	{
		$out = [];
		$chunks = array_chunk($urls, max(1, $this->cfg->concurrency));
		foreach ($chunks as $chunk) {
			$this->pace();
			$res = Http::fetchMany($chunk, $this->cfg, $method, $keepBody, $timeoutMs);
			$this->retryFailed($res, $chunk, $method, $keepBody);
			foreach ($res as $url => $req) {
				$out[$url] = $req;
			}
		}
		return $out;
	}

	/** Проверка внешних ссылок (HEAD, при отказе — GET). */
	private function checkExternal(): void
	{
		$urls = array_slice(array_keys($this->externalLinks), 0, $this->cfg->externalLimit);
		if (!$urls) {
			return;
		}
		$start = microtime(true);
		$skipped = [];
		$retry = [];
		$chunks = array_chunk($urls, max(1, $this->cfg->concurrency));
		foreach ($chunks as $chunk) {
			if ((microtime(true) - $start) > $this->cfg->externalBudgetSec) {
				$skipped = array_merge($skipped, $chunk);
				continue;
			}
			$this->pace();
			foreach (Http::fetchMany($chunk, $this->cfg, 'HEAD', false, $this->cfg->externalTimeoutMs) as $url => $req) {
				if ($req->status === 405 || $req->status === 403 || $req->status === 501 || $req->status === 0) {
					$retry[] = $url;
				} else {
					$this->externalStatus[$url] = ['status' => $req->status, 'error' => $req->error];
				}
			}
		}
		if ($retry) {
			$this->pace();
			foreach (Http::fetchMany(array_slice($retry, 0, 40), $this->cfg, 'GET', false, $this->cfg->externalTimeoutMs) as $url => $req) {
				$this->externalStatus[$url] = ['status' => $req->status, 'error' => $req->error];
			}
		}
		if ($skipped) {
			$this->notes[] = 'Внешних ссылок не проверено из-за лимита времени: ' . count($skipped);
		}
	}

	/** Проверка внутренних ссылок на не-HTML ресурсы (картинки, pdf, css). */
	private function checkAssets(): void
	{
		$targets = [];
		foreach ($this->inlinks as $url => $info) {
			if (isset($this->pages[$url])) {
				continue;
			}
			$targets[] = $url;
		}
		foreach (array_keys($this->imageUrls) as $img) {
			$targets[] = $img;
		}
		$targets = array_values(array_unique($targets));
		$targets = array_slice($targets, 0, 200);
		if (!$targets) {
			return;
		}
		$results = $this->fetchBatched($targets, 'HEAD', false, 5000);
		// Некоторые серверы не поддерживают HEAD (405/501) — такие цели проверяем GET'ом,
		// иначе они ошибочно считались бы битыми ссылками.
		$retry = [];
		foreach ($results as $url => $req) {
			if ($req->status === 405 || $req->status === 501) {
				$retry[] = $url;
			}
		}
		if ($retry) {
			foreach ($this->fetchBatched(array_slice($retry, 0, 120), 'GET', false, 8000) as $url => $req) {
				$results[$url] = $req;
			}
		}
		foreach ($results as $url => $req) {
			$this->assetStatus[$url] = [
				'status' => $req->status,
				'error'  => $req->error,
				'length' => (int)$req->header('content-length'),
			];
		}
	}

	/** Проверка URL из sitemap, до которых краулер не дошёл. */
	private function checkSitemapUrls(): void
	{
		$missing = array_values(array_diff(array_keys($this->sitemaps->urls), array_keys($this->pages)));
		if (!$missing) {
			return;
		}
		$results = $this->fetchBatched(array_slice($missing, 0, 60), 'HEAD', false, 8000);
		foreach ($results as $url => $req) {
			$this->sitemapStatus[$url] = $req->status;
		}
	}

	/** Проверка доступности служебных файлов. */
	private function probeSensitive(): void
	{
		$origin = Url::origin($this->cfg->start);
		$targets = array_map(static fn(string $p): string => $origin . $p, self::SENSITIVE_PATHS);
		$results = $this->fetchBatched($targets, 'GET', true, 6000);
		foreach ($results as $url => $req) {
			$this->probeStatus[$url] = [
				'status' => $req->status,
				'error'  => $req->error,
				'ctype'  => strtolower($req->header('content-type')),
				'hash'   => md5($req->body),
				'len'    => strlen($req->body),
				'isHtml' => stripos($req->body, '<html') !== false,
			];
		}
		$this->requests += count($targets);
	}

	private function progress(int $done, int $pages): void
	{
		if ($this->cfg->quiet) {
			return;
		}
		$bar = str_repeat('█', min(20, (int)round($done / max(1, $this->cfg->limit) * 20)));
		fwrite(STDERR, "\r  " . $this->ui->c('Обход', 'gray') . ' ' . $this->ui->c($bar, 'brcyan')
			. ' ' . $this->ui->c(str_pad((string)$done, 5, ' ', STR_PAD_LEFT), 'bold|white')
			. $this->ui->c(' стр. · в графе ' . $pages, 'gray') . '   ');
	}

	private function progressDone(): void
	{
		if (!$this->cfg->quiet) {
			fwrite(STDERR, "\r" . str_repeat(' ', 60) . "\r");
		}
	}
}

/**
 * Контекст аудита: данные краулера + вспомогательные выборки.
 */
final class Ctx
{
	public Cfg $cfg;
	public Crawler $cr;

	public function __construct(Cfg $cfg, Crawler $cr)
	{
		$this->cfg = $cfg;
		$this->cr = $cr;
	}

	/** @return array<string,array<string,mixed>> */
	public function pages(): array
	{
		return $this->cr->pages;
	}

	public function origin(): string
	{
		return Url::origin($this->cfg->start);
	}

	public function host(): string
	{
		return Url::hostOf($this->cfg->start);
	}

	/** Локальный стенд (localhost/127.0.0.1) — HTTPS-проверки к нему неприменимы. */
	public function isLocal(): bool
	{
		return $this->cfg->isLocal();
	}

	/** Внутренняя ссылка (тот же сайт, www и без www — один сайт). */
	public function isInternal(string $url): bool
	{
		return Url::sameSite($url, $this->cfg->start);
	}

	public function path(string $url): string
	{
		return Url::displayPath($url);
	}

	/** @return array<string,string> путь → URL (для сверки с sitemap) */
	public function pathMap(): array
	{
		static $cache = null;
		if ($cache !== null) {
			return $cache;
		}
		$cache = [];
		foreach ($this->cr->pages as $url => $page) {
			$cache[$this->path($url)] = $url;
		}
		return $cache;
	}

	/** Код ответа по любому известному URL (страница, ассет, внешний, sitemap). */
	public function statusOf(string $url): ?int
	{
		$url = Url::normalize($url) ?? $url;
		if (isset($this->cr->pages[$url])) {
			return (int)$this->cr->pages[$url]['status'];
		}
		if (isset($this->cr->assetStatus[$url])) {
			return (int)$this->cr->assetStatus[$url]['status'];
		}
		if (isset($this->cr->externalStatus[$url])) {
			return (int)$this->cr->externalStatus[$url]['status'];
		}
		if (isset($this->cr->sitemapStatus[$url])) {
			return (int)$this->cr->sitemapStatus[$url];
		}
		// финальный URL после редиректа
		foreach ($this->cr->pages as $page) {
			if (($page['final'] ?? '') === $url) {
				return (int)$page['status'];
			}
		}
		return null;
	}

	/** @return array{status:int,error:?string,length:int} */
	public function headOf(string $url): array
	{
		$norm = Url::normalize($url) ?? $url;
		foreach ([$this->cr->assetStatus[$norm] ?? null, $this->cr->externalStatus[$norm] ?? null] as $row) {
			if ($row !== null) {
				return ['status' => (int)$row['status'], 'error' => $row['error'] ?? null, 'length' => (int)($row['length'] ?? 0)];
			}
		}
		return ['status' => 0, 'error' => null, 'length' => 0];
	}

	/**
	 * Где именно найден проблемный адрес: список источников со страницей,
	 * текстом ссылки и зоной страницы (меню/текст/подвал).
	 *
	 * @return list<array{from:string,anchor:string,zone:string,n:int,total:int}>
	 */
	public function foundOn(string $target, int $limit = 6): array
	{
		$entry = $this->cr->inlinks[$target] ?? null;
		if ($entry === null) {
			return [];
		}
		$total = (int)($entry['count'] ?? 0);
		$out = [];
		foreach (($entry['src'] ?? []) as $from => $info) {
			$anchors = array_keys($info['anchors'] ?? []);
			$zones = array_keys($info['zones'] ?? []);
			$out[] = [
				'from'   => (string)$from,
				'anchor' => (string)($anchors[0] ?? ''),
				'zone'   => (string)($zones[0] ?? ''),
				'n'      => (int)($info['n'] ?? 1),
				'total'  => $total,
			];
		}
		usort($out, static fn(array $a, array $b): int => $b['n'] <=> $a['n']);
		return array_slice($out, 0, $limit);
	}

	/** Нормализованный путь (без завершающего слэша) — для сравнения URL. */
	public static function pathKey(string $url): string
	{
		$p = parse_url($url, PHP_URL_PATH) ?: '/';
		$q = parse_url($url, PHP_URL_QUERY);
		$p = '/' . trim(rawurldecode($p), '/');
		return $p . ($q ? '?' . $q : '');
	}
}

/**
 * Движок аудита: набор правил, их выполнение, подсчёт баллов.
 */
final class Audit
{
	/**
	 * Официальные источники, на которые опирается каждая проверка.
	 * Ключи используются в справочнике Audit::impacts().
	 */
	public const SRC = [
		'title'    => 'https://developers.google.com/search/docs/appearance/title-link',
		'snippet'  => 'https://developers.google.com/search/docs/appearance/snippet',
		'canon'    => 'https://developers.google.com/search/docs/crawling-indexing/consolidate-duplicate-urls',
		'robots'   => 'https://developers.google.com/search/docs/crawling-indexing/robots-meta-tag',
		'robottxt' => 'https://developers.google.com/search/docs/crawling-indexing/robots/intro',
		'sitemap'  => 'https://developers.google.com/search/docs/crawling-indexing/sitemaps/build-sitemap',
		'links'    => 'https://developers.google.com/search/docs/crawling-indexing/links-crawlable',
		'images'   => 'https://developers.google.com/search/docs/appearance/google-images',
		'sd'       => 'https://developers.google.com/search/docs/appearance/structured-data/intro-structured-data',
		'crawl'    => 'https://developers.google.com/search/docs/crawling-indexing/overview',
		'js'       => 'https://developers.google.com/search/docs/crawling-indexing/javascript/javascript-seo-basics',
		'cwv'      => 'https://web.dev/articles/vitals',
		'perf'     => 'https://web.dev/articles/optimize-lcp',
		'hreflang' => 'https://developers.google.com/search/docs/specialty/international/localized-versions',
		'aifeat'   => 'https://developers.google.com/search/docs/appearance/ai-features',
		'yrobot'   => 'https://yandex.ru/support/webmaster/robot-workings/robots-txt.html',
		'ymeta'    => 'https://yandex.ru/support/webmaster/meta-tags/meta-tag.html',
		'ysitem'   => 'https://yandex.ru/support/webmaster/sitemap/working-with-sitemap.html',
		'yquality' => 'https://yandex.ru/support/webmaster/quality/',
		'wcag'     => 'https://www.w3.org/WAI/WCAG22/quickref/',
		'html'     => 'https://www.w3.org/TR/html52/',
		'schema'   => 'https://schema.org/docs/gs.html',
		'rfc'      => 'https://www.rfc-editor.org/rfc/rfc9110.html',
		'owasp'    => 'https://owasp.org/www-project-secure-headers/',
		'cookies'  => 'https://developer.mozilla.org/en-US/docs/Web/HTTP/Cookies',
		'tls'      => 'https://www.rfc-editor.org/rfc/rfc8446.html',
		'llmstxt'  => 'https://llmstxt.org/',
		'gptbot'   => 'https://platform.openai.com/docs/bots',
		'claudebot'=> 'https://support.anthropic.com/en/articles/8896518',
		'perplex'  => 'https://docs.perplexity.ai/guides/bots',
		'gcommon'  => 'https://developers.google.com/search/docs/crawling-indexing/google-common-crawlers',
		'rfc9111'  => 'https://www.rfc-editor.org/rfc/rfc9111.html',
		'rfc9113'  => 'https://www.rfc-editor.org/rfc/rfc9113.html',
		'rfc6797'  => 'https://www.rfc-editor.org/rfc/rfc6797.html',
		'owasph'   => 'https://cheatsheetseries.owasp.org/cheatsheets/HTTP_Headers_Cheat_Sheet.html',
		'fontdisp' => 'https://web.dev/articles/font-display',
		'kdd'      => 'https://arxiv.org/abs/2311.09735',
		'yclean'   => 'https://yandex.ru/support/webmaster/robot-workings/clean-param.html',
		'ycrawl'   => 'https://yandex.ru/support/webmaster/robot-workings/crawl-delay.html',
		'ysnippet' => 'https://yandex.ru/support/webmaster/search-results/title-and-description.html',
		'ymicro'   => 'https://yandex.ru/support/webmaster/schema-org/',
		'yquality2'=> 'https://yandex.ru/support/webmaster/quality/',
	];

	/**
	 * Для каждой проверки: на что она влияет (факт, без воды) и источники.
	 *
	 * @return array<string,array{i:string,s:list<string>}>
	 */
	public static function impacts(): array
	{
		return [
			// --- технические ошибки ---
			'http.error' => ['i' => 'Критично: страница недоступна — краулер выпадает из обхода, а пользователь видит ошибку. Потеря позиций и конверсии.', 's' => ['crawl']],
			'http.status' => ['i' => 'Коды 4xx/5xx не индексируются и обрывают путь обхода: поисковик тратит краулинговый бюджет впустую.', 's' => ['crawl', 'rfc']],
			'http.redirect' => ['i' => 'Редирект — лишний запрос и задержка: цепочка увеличивает время ответа и расходует краулинговый бюджет. Сигналы при этом не теряются (302 тоже их передаёт) — важна длина цепочки и постоянный 301 для переезда.', 's' => ['crawl', 'cwv']],
			'http.redirect_loop' => ['i' => 'Петля редиректов полностью выбрасывает страницу из индекса: краулер не может добраться до контента.', 's' => ['crawl']],
			'http.redirect_to_error' => ['i' => 'Редирект в ошибку = потерянный путь: и пользователь, и робот упираются в 404/500.', 's' => ['crawl', 'rfc']],
			'http.redirect_cross_host' => ['i' => 'Внешний редирект передаёт сигналы и вес другому домену и запускает цепочку за пределами контроля.', 's' => ['crawl', 'canon']],
			'http.soft404' => ['i' => 'Ответ 200 с пустым содержанием индексируется как обычная страница: плодит дубли и размывает релевантность.', 's' => ['crawl', 'canon']],
			'http.empty' => ['i' => 'Пустой ответ 200 — признак сбоя шаблона или кэша: страница выглядит как «голая» и не несёт сигналов.', 's' => ['crawl']],
			'http.https_available' => ['i' => 'Без HTTPS браузер помечает сайт как небезопасный, а поисковики понижают доверие к домену и не включают защищённые функции.', 's' => ['owasp', 'crawl']],
			'http.http_to_https' => ['i' => 'Без принудительного 301 http→https создаются две копии сайта, метрики и ссылочный вес делятся между ними.', 's' => ['canon', 'rfc']],
			'http.mixed_content' => ['i' => 'Браузер блокирует http-ресурсы на https-странице: ломаются картинки/скрипты, растёт риск безопасности и падает LCP.', 's' => ['cwv', 'owasp']],

			// --- индексация ---
			'index.noindex_meta' => ['i' => 'noindex полностью исключает страницу из поиска и из передачи ссылочного веса. Снимают только осознанно.', 's' => ['robots', 'yrobot']],
			'index.noindex_header' => ['i' => 'X-Robots-Tag действует сильнее meta-тега: при конфликте поисковик ориентируется на заголовок.', 's' => ['robots']],
			'index.nofollow' => ['i' => 'nofollow на странице останавливает передачу веса по её ссылкам и говорит роботу не индексировать цели.', 's' => ['robots']],
			'index.canonical_missing' => ['i' => 'Без canonical дубли (www, слеш, параметры, регистр) конкурируют за одну выдачу, а сигналы делятся. Для Яндекса canonical — основной способ указать главный адрес дубля (Clean-param работает только с параметрами).', 's' => ['canon', 'yclean']],
			'index.canonical_mismatch' => ['i' => 'Canonical указывает на другую страницу — она выпадает из индекса в пользу целевой, и весь вес уходит туда.', 's' => ['canon']],
			'index.canonical_broken' => ['i' => 'Canonical на 404 или ошибку = страница не индексируется вообще, даже если ссылается на неё весь сайт.', 's' => ['canon']],
			'index.canonical_cross_domain' => ['i' => 'Междоменный canonical передаёт индексацию другому сайту — это осознанный инструмент, а не ошибка верстки.', 's' => ['canon']],
			'index.canonical_noindex' => ['i' => 'noindex с canonical — противоречащие друг другу сигналы: страница закрыта, но ссылочный вес уходит на целевую.', 's' => ['canon', 'robots']],
			'index.sitemap_format' => ['i' => 'Некорректные lastmod/priority делают данные карты непригодными: поисковик игнорирует подсказки о свежести.', 's' => ['sitemap']],
			'index.hreflang' => ['i' => 'Без hreflang языковые версии конкурируют за один запрос и не знают, какую показывать региону.', 's' => ['hreflang']],
			'index.hreflang_broken' => ['i' => 'Ссылка на несуществующую языковую версию отключает всю группу hreflang, а не только одну страницу.', 's' => ['hreflang']],
			'index.hreflang_on_canonical' => ['i' => 'hreflang на странице, канонизированной на другой URL, игнорируется: языковые сигналы теряются.', 's' => ['hreflang', 'canon']],
			'index.hreflang_not_return' => ['i' => 'Без обратной ссылки hreflang-пара не принимается: языковые версии не связываются в группу.', 's' => ['hreflang']],
			'index.robots_txt_missing' => ['i' => 'Без robots.txt нельзя управлять обходом, а роботы не получают карту сайта и инструкции по коду.', 's' => ['robottxt', 'yrobot']],
			'index.robots_blocks' => ['i' => 'Disallow на индексируемом URL закрывает его и от поисковика, и от внутренней перелинковки — вес не доходит.', 's' => ['robottxt', 'links']],
			'index.robots_syntax' => ['i' => 'Опечатка в директиве делает правило бессмысленным: робот читает robots.txt построчно и не поймёт опечатку.', 's' => ['robottxt', 'yrobot']],
			'index.orphan' => ['i' => 'Страница без входящих ссылок почти не индексируется: робот находит её только по sitemap, а вес она не получает.', 's' => ['links', 'crawl']],
			'index.depth' => ['i' => '«3 клика» — отраслевой ориентир, а не правило Google: по словам Google, регулярный краулинг вообще не является условием ранжирования. Практический смысл: глубокие страницы обходятся реже и получают меньше ссылок, поэтому их стоит поднимать в хабы и перелинковывать.', 's' => ['links', 'crawl']],
			'index.sitemap_missing' => ['i' => 'Карта сайта — самый дешёвый способ показать все URL: без неё часть страниц не попадает в обход вообще.', 's' => ['sitemap', 'ysitem']],
			'index.sitemap_vs_crawl' => ['i' => 'Расхождение карты и обхода = либо «сироты» без ссылок, либо страницы вне карты, о которых робот не знает.', 's' => ['sitemap', 'links']],
			'index.url_list' => ['i' => 'Файл-список используют для ручных проверок и Screaming Frog: расхождение с картой скрывает реальные страницы от проверки.', 's' => ['sitemap']],
			'index.sitemap_bad_status' => ['i' => 'Битые URL в карте — прямой сигнал о неряшливом сайте: поисковик тратит обход впустую и снижает доверие.', 's' => ['sitemap', 'crawl']],
			'index.sitemap_noindex' => ['i' => 'URL с noindex в карте — противоречие: страницу закрывают от индекса, но одновременно показывают как важную.', 's' => ['sitemap', 'robots']],
			'index.sitemap_duplicates' => ['i' => 'Дубли в карте размывают приоритеты и расходуют лимит обхода на повторные URL.', 's' => ['sitemap']],
			'index.sitemap_lastmod' => ['i' => 'lastmod в будущем или сильно устаревший обесценивает всю карту: робот перестаёт доверять датам обновления.', 's' => ['sitemap']],
			'index.duplicate_content' => ['i' => 'Дубли конкурируют за один запрос: поисковик выбирает одну версию, остальные выпадают, а краулинг тратится впустую.', 's' => ['canon', 'yquality']],

			// --- мета-теги ---
			'meta.title_missing' => ['i' => 'Title и description — основные элементы сниппета: без них поисковик берёт произвольный фрагмент страницы. По документации Яндекса, их качество влияет на CTR, а не на ранжирование.', 's' => ['title', 'ymeta', 'ysnippet']],
			'meta.title_length' => ['i' => 'Google не ограничивает длину title: длина не является фактором ранжирования. Обрезка в выдаче идёт по пикселям (≈600px на десктопе, заметно меньше на мобильном) — длинный заголовок теряет часть текста и снижает CTR.', 's' => ['title']],
			'meta.title_duplicate' => ['i' => 'Одинаковые title: поисковик не знает, какую страницу показать по запросу, и выдача становится случайной.', 's' => ['title', 'yquality']],
			'meta.desc_missing' => ['i' => 'Description не участвует в ранжировании (ни в Google, ни в Яндексе), но задаёт текст сниппета и влияет на CTR: без него поисковик берёт произвольный фрагмент страницы.', 's' => ['snippet', 'ymeta', 'ysnippet']],
			'meta.desc_length' => ['i' => 'Лимита длины description у Google нет; сниппет обрезается примерно на 155–160 символах (рекомендация Google: 50–160 символов). Влияет на CTR, не на ранжирование.', 's' => ['snippet']],
			'meta.desc_duplicate' => ['i' => 'Одинаковые description показывают поисковику одинаковую релевантность для разных страниц и снижают CTR.', 's' => ['snippet', 'yquality']],
			'meta.h1_missing' => ['i' => 'По официальной позиции Google (John Mueller) H1 не обязателен и не имеет ни верхнего, ни нижнего ограничения: это вопрос семантики и доступности, а не ранжирования. Один осмысленный H1 всё же полезен пользователю и скринридеру.', 's' => ['title', 'wcag']],
			'meta.h1_duplicate' => ['i' => 'Повторяющийся H1 на разных страницах — признак шаблонных страниц. На ранжирование это не влияет напрямую, но размывает семантику и снижает качество страницы.', 's' => ['yquality', 'html']],
			'meta.heading_skip' => ['i' => 'Пропуск уровней заголовков ломает структуру документа: навигация по разделам и скринридеры теряют иерархию.', 's' => ['html', 'wcag']],
			'meta.lang_missing' => ['i' => 'Атрибут lang определяет язык для поисковика, переводчиков и озвучки — без него страница «неязыковая».', 's' => ['html', 'hreflang']],
			'meta.lang_mismatch' => ['i' => 'Несовпадение языка (lang="en" при русском тексте) мешает определить язык и ранжировать по языковым запросам.', 's' => ['hreflang', 'html']],
			'meta.charset_missing' => ['i' => 'Без charset кириллица ломается у части клиентов — это прямой удар по поведенческим факторам и доверию.', 's' => ['html']],
			'meta.viewport_missing' => ['i' => 'Без viewport мобильный браузер покажет « shrunk » версию: показатели мобильного трафика и Core Web Vitals ухудшаются.', 's' => ['cwv', 'ymeta']],
			'meta.viewport_restricted' => ['i' => 'user-scalable=no ломает доступность для слабовидящих и может ухудшать мобильные метрики.', 's' => ['wcag', 'cwv']],
			'meta.og_missing' => ['i' => 'OG-теги задают вид ссылки в мессенджерах и соцсетях (часть AI-ассистентов тоже использует их для превью). На ранжирование не влияют, но напрямую работают на CTR и узнаваемость бренда.', 's' => ['snippet', 'aifeat']],
			'meta.og_url_mismatch' => ['i' => 'Расхождение og:url и canonical создаёт «два адреса» одной страницы в соцсетях и в сохранённых данных.', 's' => ['canon']],
			'meta.og_image_broken' => ['i' => 'Битая og:image = ссылка без превью в соцсетях, в мессенджерах и в AI-карточках: CTR падает.', 's' => ['aifeat', 'images']],
			'meta.twitter_missing' => ['i' => 'Twitter/X использует свои теги, а не OG: без них превью в твитере собирается случайно.', 's' => ['snippet']],
			'meta.favicon' => ['i' => 'Иконка сайта отображается в закладках, в поиске и в панелях браузера — влияет на узнаваемость и поведение.', 's' => ['images']],

			// --- разметка ---
			'schema.missing' => ['i' => 'Разметка Schema.org даёт расширенные сниппеты: Яндекс показывает в них адрес, телефон, быстрые ссылки и данные организации, Google — отзывы, цены и FAQ. Для AI-поиска разметка позволяет машинно извлечь суть страницы.', 's' => ['sd', 'schema', 'ymicro', 'aifeat']],
			'schema.invalid' => ['i' => 'Битый JSON в разметке игнорируется целиком: вместо rich snippet остаётся обычный.', 's' => ['sd', 'schema']],
			'schema.no_type' => ['i' => 'Объект без @type не интерпретируется: поисковик не понимает, что это за сущность, и не показывает её в ответных блоках.', 's' => ['schema', 'sd']],
			'schema.duplicate_type' => ['i' => 'Два конкурирующих объекта одного типа на странице (например, две Organization) — поисковик выберет один наугад.', 's' => ['sd', 'schema']],
			'schema.required' => ['i' => 'Без обязательных свойств (headline, datePublished, name) разметка не даёт rich snippet и хуже извлекается AI-поиском.', 's' => ['sd', 'schema']],

			// --- контент ---
			'content.thin' => ['i' => 'Порога в 300 слов у Google нет (Danny Sullivan: «лучшее количество слов для успеха — не существует»). 300 — отраслевой ориентир для коммерческих страниц; реальная проблема — когда страница не отвечает на запрос вообще.', 's' => ['yquality', 'aifeat']],
			'content.php_errors' => ['i' => 'Технический текст в HTML виден пользователю: падает доверие, растёт bounce-rate, портится вёрстка. Яндекс прямо относит страницы с ошибками к некачественным.', 's' => ['yquality']],
			'content.text_ratio' => ['i' => 'Страница, почти целиком состоящая из разметки, воспринимается как пустая: меньше текста на ту же страницу.', 's' => ['yquality']],
			'content.encoding_ok' => ['i' => 'Неверный charset в ответе ломает вывод кириллицы у роботов и агентов — часть страницы выглядит мусором.', 's' => ['html']],
			'content.robots_meta_conflict' => ['i' => 'Конфликт robots-сигналов (meta против заголовка) делает индексацию непредсказуемой.', 's' => ['robots']],
			'content.keyword_density' => ['i' => 'Плотность термина сама по себе не является фактором ранжирования (John Mueller). Значение выше 3,5% — индикатор переспама: текст выглядит роботизированным, ухудшается поведение и читаемость.', 's' => ['yquality', 'aifeat']],

			// --- ссылки ---
			'links.broken_internal' => ['i' => 'Битая внутренняя ссылка обрывает путь пользователя и передачу веса: на неё уходит краулинговый бюджет.', 's' => ['links', 'crawl']],
			'links.redirect_internal' => ['i' => 'Ссылка на редирект = лишний запрос и задержка для робота и пользователя. Длинные цепочки съедают краулинговый бюджет, поэтому внутренние ссылки должны вести сразу на конечный адрес.', 's' => ['links', 'crawl']],
			'links.broken_external' => ['i' => 'Битые внешние ссылки подрывают доверие к ресурсу и ухудшают репутацию домена в глазах поисковика.', 's' => ['links', 'yquality']],
			'links.nofollow_internal' => ['i' => 'nofollow на внутренней ссылке обрывает передачу веса и говорит: эта страница не важна.', 's' => ['links', 'robots']],
			'links.empty_anchor' => ['i' => 'Пустой анкор (иконка без текста) не передаёт тему ссылки ни роботу, ни пользователю.', 's' => ['links', 'wcag']],
			'links.generic_anchor' => ['i' => 'Анкор «подробнее» не несёт запроса: страница-получатель не получает текстового сигнала по релевантности.', 's' => ['links', 'title']],
			'links.broken_fragment' => ['i' => 'Битый якорь (#id) уводит в никуда: пользователь не находит блок, растёт недовольство и возвраты на поиск.', 's' => ['links', 'wcag']],
			'links.to_noindex' => ['i' => 'Вес на noindex-страницу не передаётся: внутренние ссылки перестают работать как канал веса.', 's' => ['links', 'robots']],
			'links.extensions' => ['i' => 'Ссылки на .php/.html плохо переживают смену ЧПУ и часто ломаются при переносе — риск для обхода.', 's' => ['links', 'yrobot']],
			'links.many_links' => ['i' => 'Огромное число ссылок на странице размывает внутренний вес: значимыми остаются единицы.', 's' => ['links', 'yquality']],
			'links.js_navigation' => ['i' => 'Меню, которое появляется только после JS, краулер может не увидеть: часть страниц выпадает из обхода.', 's' => ['js', 'crawl']],
			'links.ambiguous_anchor' => ['i' => 'Одинаковый анкор на разные адреса размывает сигнал ссылки и путает пользователя при выборе.', 's' => ['links']],

			// --- изображения ---
			'img.missing_alt' => ['i' => 'alt нужен для поиска по картинкам, для доступности и для извлечения смысла картинки AI-поиском.', 's' => ['images', 'wcag', 'aifeat']],
			'img.empty_alt' => ['i' => 'alt="" у контентной картинки скрывает её и от поиска по изображениям, и от AI-извлечения сути.', 's' => ['images', 'wcag']],
			'img.no_dimensions' => ['i' => 'Без width/height браузер резервирует место после загрузки: растёт CLS — метрика Core Web Vitals.', 's' => ['cwv']],
			'img.lazy_loading' => ['i' => 'Без lazy ниже fold загружаются лишние байты: растёт LCP и трафик мобильных пользователей.', 's' => ['cwv', 'perf']],
			'img.broken' => ['i' => 'Битая картинка выглядит как недоделка, снижает доверие и ухудшает поведенческие факторы.', 's' => ['images', 'yquality']],
			'img.lcp' => ['i' => 'LCP-картинка — самый заметный элемент первого экрана: без приоритета и размеров она тормозит главную метрику.', 's' => ['cwv', 'perf']],
			'img.oversize' => ['i' => 'Тяжёлые картинки съедают мобильный трафик и время до LCP: Google оценивает в том числе вес главного контента.', 's' => ['cwv', 'perf']],

			// --- скорость ---
			'perf.ttfb' => ['i' => 'TTFB — первый порог Core Web Vitals: медленный ответ сервера ухудшает LCP и удержание. Google называет CWV не главным фактором ранжирования, но тай-брейкером — и прямое влияние на UX.', 's' => ['cwv', 'perf']],
			'perf.page_size' => ['i' => 'Большой HTML замедляет парсинг и стилизацию: растёт время до первого контента и INP.', 's' => ['cwv', 'perf']],
			'perf.compression' => ['i' => 'Без gzip/br HTML передаётся в 4–5 раз тяжелее: напрямую бьёт по LCP и трафику.', 's' => ['cwv', 'perf']],
			'perf.cache_control' => ['i' => 'Без Cache-Control (RFC 9111) и ETag каждый визит — полная перерисовка: растёт TTFB и нагрузка на сервер, а повторный обход страницы тратит краулинговый бюджет.', 's' => ['cwv', 'perf', 'rfc9111']],
			'perf.blocking' => ['i' => 'Рендер-блокирующие CSS/JS задерживают первую отрисовку: прямое влияние на LCP и FCP.', 's' => ['cwv', 'perf']],
			'perf.dom_size' => ['i' => 'Тяжёлый DOM замедляет парсинг и стилизацию на слабых телефонах: растёт INP и TTFB.', 's' => ['cwv', 'perf']],
			'perf.http_version' => ['i' => 'HTTP/1.0 без keep-alive открывает новое соединение на каждый ресурс — лишние RTT.', 's' => ['rfc', 'cwv']],
			'perf.assets_weight' => ['i' => 'Суммарный вес статики определяет, сколько данных грузит мобильный пользователь до первого экрана.', 's' => ['cwv', 'perf']],

			// --- безопасность ---
			'sec.https' => ['i' => 'HTTPS — базовая гигиена: без неё браузер предупреждает, а поисковики не доверяют домену и не дают расширенных возможностей.', 's' => ['owasp', 'crawl']],
			'sec.hsts' => ['i' => 'HSTS закрывает окно SSL-stripping и запрещает браузеру уйти в http. RFC 6797: max-age от 1 года (31536000); OWASP рекомендует 63072000 (2 года) с includeSubDomains и preload.', 's' => ['owasp', 'owasph', 'rfc6797', 'tls']],
			'sec.csp' => ['i' => 'CSP ограничивает выполнение скриптов: главный рубеж против XSS и кражи сессий.', 's' => ['owasp']],
			'sec.xfo' => ['i' => 'Защита от iframe останавливает clickjacking: наложение чужой страницы поверх вашей кнопки.', 's' => ['owasp', 'wcag']],
			'sec.nosniff' => ['i' => 'nosniff запрещает браузеру угадывать тип файла: базовая защита от MIME-конфликтов и подмены JS.', 's' => ['owasp']],
			'sec.referrer_policy' => ['i' => 'Referrer-Policy убирает utm-метки и внутренние пути из чужих логов: меньше утечки и точнее аналитика.', 's' => ['owasp', 'mdn']],
			'sec.permissions_policy' => ['i' => 'Permissions-Policy выключает неиспользуемые API (камера, микрофон, геолокация) — уменьшает поверхность атаки.', 's' => ['owasp']],
			'sec.banners' => ['i' => 'Версии nginx/PHP в заголовках упрощают подбор эксплойтов: баннер стоит скрыть.', 's' => ['owasp']],
			'sec.sensitive_files' => ['i' => 'Критично: открытый .env или .git раскрывает пароли, ключи и всю структуру проекта.', 's' => ['owasp']],
			'sec.tls_expired' => ['i' => 'Просроченный сертификат = предупреждение браузера для всех посетителей и потеря доверия к домену.', 's' => ['tls', 'rfc']],
			'sec.tls_quality' => ['i' => 'Сертификат на чужое имя и слабые алгоритмы (SHA-1/MD5) приводят к предупреждениям и понижают доверие.', 's' => ['tls', 'owasp']],
			'sec.cookies' => ['i' => 'Cookie без Secure/HttpOnly/SameSite утекает в JS и перехватывается по http: это вектор кражи сессии.', 's' => ['cookies', 'owasp']],

			// --- доступность ---
			'a11y.form_label' => ['i' => 'Без label поле недоступно с клавиатуры и для скринридеров: теряется часть трафика, WCAG 2.1 A.', 's' => ['wcag']],
			'a11y.field_name' => ['i' => 'Поле без name не отправляется и не восстанавливается: часть заявок теряется молча.', 's' => ['wcag', 'html']],
			'a11y.duplicate_ids' => ['i' => 'Дубли id ломают якоря и скрипты: кликабельные элементы перестают работать корректно.', 's' => ['html', 'wcag']],
			'a11y.iframe_title' => ['i' => 'Без title iframe невозможно отличить в содержимом для скринридера: WCAG 2.1 A.', 's' => ['wcag']],
			'a11y.landmark' => ['i' => 'Отдельного критерия WCAG «наличие <main>» нет, но landmark-разметка входит в SC 1.3.1 (связи в разметке, уровень A) и помогает скринридерам и поисковику понять структуру страницы.', 's' => ['wcag', 'html']],
			'a11y.skip_link' => ['i' => 'Skip-link экономит клавиатурным пользователям проход по меню на каждой странице — WCAG 2.4 A.', 's' => ['wcag']],
			'a11y.button_label' => ['i' => 'Иконочная кнопка без имени недоступна: часть пользователей не может завершить действие.', 's' => ['wcag']],
			// --- AI/GEO, URL-гигиена, пагинация, шрифты, HTTP/2 ---
			'index.sitemap_directive' => ['i' => 'Директива Sitemap: — самый надёжный способ сообщить поисковику о карте: robots.txt читают все роботы, а карту ищут не всегда.', 's' => ['robottxt', 'sitemap']],
			// --- Яндекс-специфичные проверки ---
			'index.robots_bots' => ['i' => 'Disallow: / для робота Яндекса (или Googlebot) означает, что сайт не индексируется этой системой вообще. Правила для конкретного бота важнее общих User-agent: *.', 's' => ['yrobot', 'robottxt']],
			'index.clean_param' => ['i' => 'Clean-param читает только робот Яндекса: он склеивает адреса, отличающиеся этими параметрами, переносит накопленные метрики на основной URL и не обходит дубли заново. Опасно вносить туда параметры, меняющие содержимое (фильтры, сортировки): робот посчитает разные страницы одинаковыми. Google директиву не поддерживает — для него нужны canonical и noindex.', 's' => ['yclean', 'yrobot']],
			'index.robots_encoding' => ['i' => 'Яндекс прямо указывает: пути в Disallow с не-ASCII нужно писать в percent-encoding (например /%D0%BA%D0%BE%D1%80%D0%B7%D0%B8%D0%BD%D0%B0). Буквальная кириллица в директиве не сработает, и раздел останется открытым для обхода.', 's' => ['yrobot']],
			'index.robots_limits' => ['i' => 'Требование Яндекса к robots.txt: файл в корне, отдаёт 200 OK, размер не больше 500 КБ. При превышении лимита робот читает файл частично — часть правил просто не применяется.', 's' => ['yrobot']],
			'index.robots_host' => ['i' => 'Директива Host работает только у робота Яндекса: указывает основное зеркало, если сайт доступен по нескольким адресам. Директива должна быть одна — при нескольких учитывается первая.', 's' => ['yrobot']],
			'index.crawl_delay' => ['i' => 'С 22 февраля 2018 года Яндекс не учитывает Crawl-delay: скорость обхода задаётся только в Вебмастере. Директива в robots.txt остаётся подсказкой для других роботов, но на Яндекс не влияет.', 's' => ['ycrawl']],
			'index.verification' => ['i' => 'Подтверждение прав в Вебмастере и Search Console даёт доступ к переобходу, отчётам и инструментам диагностики — без него часть проверок и инструментов недоступна.', 's' => ['yquality2', 'ymicro']],
			'ai.llms_txt' => ['i' => 'llms.txt — предложение сообщества (llmstxt.org), а не официальный стандарт: Google и OpenAI поддержку не объявляли, peer-reviewed замеров эффекта нет. Полезен как машиночитаемый указатель для агентов, но ставить его нужно осознанно, а не «потому что модно».', 's' => ['llmstxt', 'aifeat']],
			'ai.plugin_manifest' => ['i' => 'Манифест (ai-plugin.json / agents.json) — это машиночитаемое описание того, что сайт умеет и где брать данные: точка входа для агентов. Формат предложен OpenAI, официальной поддержки поисковыми системами нет — полезен как гигиена, а не как фактор ранжирования.', 's' => ['gptbot', 'aifeat']],
			'ai.robots_crawlers' => ['i' => 'Disallow для GPTBot/ClaudeBot/PerplexityBot/Google-Extended прямо исключает контент из ответов AI-поиска. Это бизнес-решение, но оно должно быть явным.', 's' => ['robottxt', 'gptbot', 'perplex', 'aifeat']],
			'ai.answer_structure' => ['i' => 'Списки, вопросы в заголовках и FAQPage дают движку «готовые единицы» для цитирования и одновременно работают на сниппеты и featured snippets. Строгого измеренного порога нет — это отраслевая практика, подтверждённая чек-листами и самой постановкой задачи RAG.', 's' => ['aifeat', 'sd']],
			'ai.eeat' => ['i' => 'Автор и дата — опорные сигналы E-E-A-T и прозрачности источника. Официального измеренного эффекта на цитирование именно по автору нет, но без них материал анонимный: его сложнее проверить и сложнее цитировать, а в части поисковых систем это прямой сигнал качества.', 's' => ['aifeat', 'sd']],
			'ai.answer_first' => ['i' => 'Единица ответа генеративной модели — короткий фрагмент: длинная «простыня» режется на куски и часто цитируется частично или не попадает в ответ. Короткие абзацы (40–260 символов) — эвристика извлекаемости, а не измеренный порог.', 's' => ['aifeat', 'kdd']],
			'ai.entity_sameas' => ['i' => 'sameAs связывает сущность «компания» с её профилями и базами знаний: так система понимает, кто вы, и не приписывает вам чужое.', 's' => ['schema', 'aifeat']],
			'url.too_long' => ['i' => 'URL длиннее ~100 символов обрезается в выдаче и в соцсетях, часть агентов и систем отбрасывает его целиком.', 's' => ['links', 'crawl']],
			'url.mixed_case' => ['i' => 'Регистр в пути создаёт дубли: /Blog и /blog — разные адреса для робота, хотя пользователь видит одну страницу.', 's' => ['canon', 'links']],
			'url.underscore' => ['i' => 'Подчёркивание хуже дефиса воспринимается движками и разделителями слов, из-за чего часть запросов не распознаётся.', 's' => ['links', 'yrobot']],
			'url.non_ascii' => ['i' => 'Не-ASCII в URL ломает копирование, аналитику и часть агентов; транслитерация в адресе убирает эти риски.', 's' => ['links', 'yrobot']],
			'url.slash_consistency' => ['i' => 'Смешение /blog и /blog/ порождает дубли: робот обходит оба, а вес делится между ними вместо концентрации.', 's' => ['canon', 'links']],
			'index.pagination' => ['i' => 'rel=next/prev задаёт роботу последовательность пагинации и снижает риск, что страницы 2, 3, 4 будут считаны дублями первой.', 's' => ['links', 'crawl']],
			'perf.font_display' => ['i' => 'Без font-display браузер ждёт шрифт и рисует текст позже: растут FOUT и CLS — метрики Core Web Vitals (web.dev).', 's' => ['cwv', 'perf', 'fontdisp']],
			'perf.http2' => ['i' => 'HTTP/2 (RFC 9113) мультиплексирует запросы в одном соединении: меньше RTT на десятки ресурсов — быстрее первая отрисовка.', 's' => ['rfc9113', 'cwv']],

			// --- углублённые проверки ---
			'meta.default_title' => ['i' => 'Авто-заголовок («Главная», «Home», «Page 2») не несёт темы страницы: поисковик берёт её вместо вашего текста.', 's' => ['title', 'yquality']],
			'img.alt_placeholder' => ['i' => 'alt из имени файла формально закрывает проверку, но не даёт ни ранжирования по картинкам, ни доступности — это «обман» а не описание.', 's' => ['images', 'wcag']],
			'links.content_links' => ['i' => 'Ссылки только из меню и футера не передают контекст: робот не понимает, какие темы раскрывает страница, и хабы не укрепляются.', 's' => ['links', 'yquality']],
			'perf.cdn' => ['i' => 'CDN разгружает origin, ускоряет отдачу статики по всему миру и добавляет защиту от DDoS — это влияет и на скорость, и на доступность.', 's' => ['cwv', 'perf']],
			'index.faceted_nav' => ['i' => 'Фильтры и сортировки плодят URL, которые конкурируют с основными страницами. Закрывайте их noindex и не пускайте в карту.', 's' => ['canon', 'sitemap']],
			'schema.local_business' => ['i' => 'Organization/LocalBusiness с address и geo привязывают компанию к месту в локальной выдаче и картах.', 's' => ['schema', 'sd']],
			'index.host_variants' => ['i' => 'Каждый живой вариант домена (http/https, www) — отдельная копия сайта: робот обходит их вместо полезных страниц.', 's' => ['canon', 'crawl']],
			'ai.citation_bots' => ['i' => 'Цитатные AI-краулеры (OAI-SearchBot, ClaudeBot, PerplexityBot, Google-Extended) — это те, кто формирует ссылки в ответах ИИ; блокируя их, вы закрываете канал трафика из AI-ответов. Обучение моделей (GPTBot, CCBot, Amazonbot) — отдельный канал, решение о нём независимое и не влияет на цитирование.', 's' => ['gptbot', 'perplex', 'gcommon', 'aifeat']],
			'ai.entity_definition' => ['i' => '«Определение сущности в первом абзаце 40–50 слов» — это рекомендация отраслевых GEO-чеклистов (Alice Labs, 2026), а не измеренный результат исследования. В самой работе Aggarwal et al. (KDD\'24) такого порога нет — там проверялись цитаты, источники и статистика.', 's' => ['aifeat', 'kdd']],
			'ai.quick_answer' => ['i' => '«Блок с коротким ответом до 160 символов в начале страницы» — практика из GEO-чеклистов (Alice Labs, 2026), не измеренный фактор: в peer-reviewed работе Aggarwal et al. такого порога нет. Проверка полезна как эвристика извлекаемости.', 's' => ['aifeat', 'kdd']],
			'ai.sources_cited' => ['i' => 'В эксперименте Aggarwal et al. (KDD\'24) Cite Sources давал от −30% (уже топовые страницы) до +115% (страницы 5-й позиции) относительной видимости в ответах генеративных движков. Эффект зависит от текущей позиции, поэтому для сильных страниц добавление ссылок может не помочь. Критический обзор GEO (2026) отмечает: коммерческие обещания GEO опережают данные.', 's' => ['kdd', 'aifeat']],
			'ai.statistics' => ['i' => 'Statistics Addition в Aggarwal et al. (KDD\'24) давал существенный прирост видимости (до ~+40% для слабых по позиции страниц) и около −20% для уже топовой страницы. Статистика и даты — то, что генеративная модель может процитировать дословно.', 's' => ['kdd', 'aifeat']],
			'ai.quotes' => ['i' => 'Quotation Addition — лучший метод в исследовании Aggarwal et al. (KDD\'24): рост взвешенной видимости примерно на 30–40% (показатель PAWC вырос с 19,3 до 27,2). Поздние работы (FeatGEO, ACL 2026) показывают, что изолированные текстовые правки не всегда дают устойчивый эффект.', 's' => ['kdd', 'aifeat']],
			'ai.js_dependency' => ['i' => 'AI-краулеры не выполняют JavaScript: контент, который рендерится только на клиенте, для них пуст. Для Яндекса это отдельное требование — основной текст должен присутствовать в исходном HTML («текстовая версия»). Для Google индексация JS — отдельная задача, для GPTBot/ClaudeBot/Perplexity потеря контента полная.', 's' => ['aifeat', 'js', 'gptbot', 'yquality2']],
			'ai.date_modified' => ['i' => 'dateModified — машиночитаемый сигнал свежести: Google использует даты публикации/обновления в сниппетах, а без разметки дата остаётся неоднозначной для роботов и агентов.', 's' => ['aifeat', 'sd']],
			'ai.llms_full' => ['i' => 'llms-full.txt — развёрнутая версия карты (предложение сообщества llmstxt.org). Официальной поддержки и измеренного эффекта нет; полезен тем, кто отдаёт контент агентам напрямую.', 's' => ['llmstxt']],
		];
	}
	public const CAT_WEIGHTS = [
		'Технические ошибки'    => 18,
		'Индексация'            => 20,
		'Мета-теги'             => 12,
		'Разметка Schema.org'    => 8,
		'Контент'               => 10,
		'Ссылки'                => 14,
		'Изображения'           => 6,
		'Скорость'              => 8,
		'Безопасность'          => 8,
		'Доступность'           => 6,
		'AI и GEO'               => 8,
	];

	/** Вес правила по критичности. */
	public const SEV_WEIGHT = [
		'critical' => 10.0,
		'high'     => 6.0,
		'medium'   => 3.0,
		'low'      => 1.0,
		'info'     => 0.4,
	];

	/** @var list<array<string,mixed>> */
	private array $results = [];

	public function run(Ctx $c): array
	{
		$this->results = [];
		$impacts = self::impacts();
		foreach (self::rules() as $rule) {
			/** @var list<array{url:?string,detail:string}> $violations */
			$violations = ($rule['check'])($c);
			$scope = $rule['scope'] ?? 'page';
			$checked = $scope === 'site' ? 1 : max(1, count($c->pages()));
			$failed = 0;
			if ($scope === 'site') {
				$failed = $violations === [] ? 0 : 1;
			} else {
				$urls = [];
				foreach ($violations as $v) {
					$key = $v['url'] ?? '__site__';
					$urls[$key] = true;
				}
				$failed = count($urls);
			}
			$imp = $impacts[$rule['id']] ?? ['i' => '', 's' => []];
			$this->results[] = [
				'id'         => $rule['id'],
				'cat'        => $rule['cat'],
				'sev'        => $rule['sev'],
				'title'      => $rule['title'],
				'hint'       => $rule['hint'] ?? '',
				'impact'     => $imp['i'],
				'sources'    => array_map(static fn(string $k): string => self::SRC[$k] ?? '', $imp['s']),
				'scope'      => $scope,
				'checked'    => $checked,
				'failed'     => $failed,
				'violations' => $violations,
				'pass'       => $failed === 0,
			];
		}
		return $this->results;
	}

	/** @return list<array<string,mixed>> */
	public function results(): array
	{
		return $this->results;
	}

	/**
	 * Баллы: 0–100 по категориям и общий.
	 *
	 * @return array{score:float,total:float,byCat:array<string,array{score:float,weight:float,rules:int,failed:int}>}
	 */
	public function score(): array
	{
		$byCat = [];
		$total = 0.0;
		$weightSum = 0.0;
		foreach (self::CAT_WEIGHTS as $cat => $weight) {
			$byCat[$cat] = ['score' => 0.0, 'weight' => 0.0, 'catWeight' => (float)$weight, 'rules' => 0, 'failed' => 0, 'penalty' => 0.0];
		}
		foreach ($this->results as $r) {
			$cat = $r['cat'];
			if (!isset($byCat[$cat])) {
				$byCat[$cat] = ['score' => 0.0, 'weight' => 0.0, 'catWeight' => 5.0, 'rules' => 0, 'failed' => 0, 'penalty' => 0.0];
			}
			$w = self::SEV_WEIGHT[$r['sev']] ?? 1.0;
			$ratio = $r['checked'] > 0 ? max(0.0, 1.0 - $r['failed'] / $r['checked']) : 1.0;
			$byCat[$cat]['score'] += $ratio * $w;
			$byCat[$cat]['weight'] += $w;   // вес правила по критичности
			$byCat[$cat]['rules']++;
			if (!$r['pass']) {
				$byCat[$cat]['failed']++;
				// фиксированный штраф за само наличие нарушения: одна критичная
				// проблема не должна «размазываться» на сотни корректных страниц
				$byCat[$cat]['penalty'] += $w * 0.7;
			}
		}
		$sum = 0.0;
		$wsum = 0.0;
		foreach ($byCat as $cat => $data) {
			$raw = $data['weight'] > 0 ? 100 * $data['score'] / $data['weight'] : 100;
			$score = (int)max(0, min(100, round($raw - $data['penalty'])));
			$byCat[$cat]['score'] = (float)$score;
			$catWeight = $data['catWeight'];
			$sum += $score * $catWeight;
			$wsum += $catWeight;
			$total += $data['weight'];
		}
		return [
			'score'  => $wsum > 0 ? round($sum / $wsum, 1) : 0.0,
			'total'  => round($total, 1),
			'byCat'  => $byCat,
		];
	}

	/**
	 * Все правила аудита.
	 *
	 * @return list<array<string,mixed>>
	 */
	public static function rules(): array
	{
		return array_merge(
			self::rulesTechnical(),
			self::rulesIndexing(),
			self::rulesMeta(),
			self::rulesSchema(),
			self::rulesContent(),
			self::rulesLinks(),
			self::rulesImages(),
			self::rulesPerformance(),
			self::rulesSecurity(),
			self::rulesA11y(),
			self::rulesAiGeo(),
			self::rulesUrlHygiene(),
			self::rulesDeep(),
		);
	}

	// =====================================================================
	// 12. УГЛУБЛЁННЫЕ ПРОВЕРКИ (по опубликованным чек-листам аудита)
	// =====================================================================
	private static function rulesDeep(): array
	{
		return [
			// --- SEO: мета-теги ---
			[
				'id' => 'meta.default_title', 'cat' => 'Мета-теги', 'sev' => 'low', 'scope' => 'page',
				'title' => 'Заголовок не авто-генерированный',
				'hint' => '«Главная», «Home», «Page 2» — дефолтные заголовки CMS: поисковик не понимает тему страницы. Пишите осмысленный заголовок с ключевым запросом.',
				'check' => static function (Ctx $c): array {
					$out = [];
					$defaults = ['главная', 'home', 'index', 'страница', 'page', 'без названия', 'untitled', 'заголовок', 'документ'];
					foreach ($c->pages() as $url => $p) {
						$t = mb_strtolower(trim((string)$p['html']['title']));
						if ($t === '') {
							continue;
						}
						$first = mb_strtolower(preg_split('~[\s|—–\-:]+~u', $t, 2)[0] ?? '');
						if (in_array($first, $defaults, true)) {
							$out[] = ['url' => $url, 'detail' => 'заголовок начинается с «' . $first . '»: ' . mb_substr((string)$p['html']['title'], 0, 50)];
						}
					}
					return $out;
				},
			],
			// --- SEO: изображения ---
			[
				'id' => 'img.alt_placeholder', 'cat' => 'Изображения', 'sev' => 'low', 'scope' => 'page',
				'title' => 'alt описывает картинку, а не имя файла',
				'hint' => 'alt="DSC_0042.jpg" или alt="image" формально присутствуют, но не дают ни пользы поиску по картинкам, ни доступности. Опишите, что на картинке.',
				'check' => static function (Ctx $c): array {
					$out = [];
					$generic = ['image', 'img', 'photo', 'picture', 'фото', 'картинка', 'изображение', 'без названия', 'pic', 'dsc', 'screenshot', 'окно'];
					foreach ($c->pages() as $url => $p) {
						$bad = [];
						foreach ($p['html']['images'] as $im) {
							$alt = mb_strtolower(trim((string)($im['alt'] ?? '')));
							if ($alt === '') {
								continue;
							}
							$isFile = (bool)preg_match('~\.(jpe?g|png|gif|webp|avif|svg|bmp)$~i', $alt);
							$isGeneric = in_array($alt, $generic, true);
							if ($isFile || $isGeneric) {
								$bad[] = basename((string)$im['src']) . ': alt="' . mb_substr($alt, 0, 30) . '" (' . $im['zone'] . ')';
							}
						}
						if ($bad) {
							$out[] = ['url' => $url, 'detail' => count($bad) . ' шт.: ' . implode('; ', array_slice($bad, 0, 4))];
						}
					}
					return $out;
				},
			],
			// --- SEO: внутренние ссылки ---
			[
				'id' => 'links.content_links', 'cat' => 'Ссылки', 'sev' => 'low', 'scope' => 'page',
				'title' => 'Страницы связаны ссылками из текста, а не только из меню',
				'hint' => 'Ссылки только из меню и футера не передают контекст: робот не понимает, о чём страница и какие темы она раскрывает. Добавляйте ссылки внутри текста.',
				'check' => static function (Ctx $c): array {
					$out = [];
					foreach ($c->pages() as $url => $p) {
						$h = $p['html'];
						$total = count($h['links']);
						$content = (int)$h['contentLinks'];
						if ($total < 15 || (int)$h['words'] < 200) {
							continue;
						}
						if ($content === 0) {
							$out[] = ['url' => $url, 'detail' => $total . ' ссылок, из них 0 из основного контента'];
						}
					}
					return $out;
				},
			],
			// --- SEO: инфраструктура ---
			[
				'id' => 'perf.cdn', 'cat' => 'Скорость', 'sev' => 'info', 'scope' => 'site',
				'title' => 'Сайт отдаётся через CDN',
				'hint' => 'CDN снимает нагрузку с origin, ускоряет отдачу статики по всему миру и добавляет защиту от DDoS.',
				'check' => static function (Ctx $c): array {
					$out = [];
					$marks = ['cf-ray', 'x-amz-cf-id', 'x-fastly', 'x-akamai-transformed', 'x-served-by', 'x-vercel-id', 'x-now', 'x-nf-request-id', 'x-cdn', 'x-cache-hits'];
					$found = [];
					foreach ($c->pages() as $url => $p) {
						foreach ($marks as $m) {
							if (!empty($p['headers'][$m])) {
								$found[$m] = true;
							}
						}
					}
					if (!$found) {
						$out[] = ['url' => null, 'detail' => 'Признаков CDN в заголовках ответов нет'];
					}
					return $out;
				},
			],
			[
				'id' => 'index.faceted_nav', 'cat' => 'Индексация', 'sev' => 'info', 'scope' => 'site',
				'title' => 'Фасетная навигация не плодит индексируемые URL',
				'hint' => 'Фильтры и сортировки создают URL, которые не должны индексироваться: закрывайте их noindex и не пускайте в sitemap.',
				'check' => static function (Ctx $c): array {
					$out = [];
					$params = [];
					foreach ($c->cr->pages as $url => $p) {
						$q = parse_url($url, PHP_URL_QUERY);
						if (!$q) {
							continue;
						}
						foreach (explode('&', $q) as $pair) {
							$name = explode('=', $pair, 2)[0];
							if ($name === '' || preg_match('~^(utm_|yclid|gclid|fbclid|_openstat|ref|page)$~i', $name)) {
								continue;
							}
							$params[$name] = ($params[$name] ?? 0) + 1;
						}
					}
					if (count($params) >= 3) {
						$out[] = ['url' => null, 'detail' => 'параметры фильтрации (' . implode(', ', array_keys($params)) . ') дают ' . array_sum($params) . ' URL — проверьте, что они закрыты noindex'];
					}
					return $out;
				},
			],
			[
				'id' => 'schema.local_business', 'cat' => 'Разметка Schema.org', 'sev' => 'low', 'scope' => 'site',
				'title' => 'У организации задан адрес и гео-координаты',
				'hint' => 'LocalBusiness/Organization с address и geo нужны для локальной выдачи и карт: без них бизнес не привязывается к месту.',
				'check' => static function (Ctx $c): array {
					$out = [];
					$withAddress = 0;
					$total = 0;
					foreach ($c->pages() as $url => $p) {
						foreach ($p['html']['jsonld'] as $block) {
							if (!$block['ok']) {
								continue;
							}
							$raw = (string)$block['raw'];
							if (stripos($raw, 'LocalBusiness') === false && stripos($raw, '"Organization"') === false && stripos($raw, "'Organization'") === false) {
								continue;
							}
							$total++;
							if (stripos($raw, '"address"') !== false || stripos($raw, '"geo"') !== false) {
								$withAddress++;
							}
						}
					}
					if ($total > 0 && $withAddress === 0) {
						$out[] = ['url' => null, 'detail' => 'Разметка организации есть в ' . $total . ' местах, но без address/geo'];
					}
					return $out;
				},
			],
			[
				'id' => 'index.host_variants', 'cat' => 'Индексация', 'sev' => 'medium', 'scope' => 'site',
				'title' => 'Все варианты домена (http/https, www) ведут на канонический хост за один хоп',
				'hint' => 'Каждый лишний вариант домена — отдельная копия сайта для робота. Нужен один 301-редирект на канонический хост.',
				'check' => static function (Ctx $c): array {
					$out = [];
					if ($c->isLocal()) {
						return $out;
					}
					$canonical = $c->origin();
					$host = Url::hostOf($canonical);
					$bare = preg_replace('~^www\.~i', '', $host);
					$variants = array_unique([
						'http://' . $host . '/',
						'http://' . $bare . '/',
						'https://' . $bare . '/',
					]);
					foreach ($variants as $v) {
						if ($v === $canonical) {
							continue;
						}
						$req = Http::fetch($v, $c->cfg, 'HEAD');
						$hops = count($req->redirects);
						$finalHost = Url::hostOf($req->final);
						if ($req->status >= 200 && $req->status < 300 && $hops === 0) {
							$out[] = ['url' => null, 'detail' => $v . ' отдаёт 200 без редиректа — это вторая копия сайта'];
						} elseif ($hops > 1) {
							$out[] = ['url' => null, 'detail' => $v . ' → ' . $hops . ' редиректа (нужен один)'];
						} elseif ($hops === 1 && $finalHost !== Url::hostOf($canonical)) {
							$out[] = ['url' => null, 'detail' => $v . ' ведёт на другой хост: ' . $finalHost];
						}
					}
					return $out;
				},
			],
			// --- GEO: цитатные боты ---
			[
				'id' => 'ai.citation_bots', 'cat' => 'AI и GEO', 'sev' => 'medium', 'scope' => 'site',
				'title' => 'AI-краулеры, которые цитируют ответы, не заблокированы',
				'hint' => 'Цитатные боты (OAI-SearchBot, ClaudeBot, PerplexityBot, Google-Extended) приносят трафик из ответов ИИ. Блокировка = вы не попадаете в ответы.',
				'check' => static function (Ctx $c): array {
					$out = [];
					$citation = [
						'oai-searchbot'   => 'OpenAI (поиск ChatGPT)',
						'claudebot'        => 'Anthropic (Claude)',
						'perplexitybot'    => 'Perplexity',
						'google-extended'  => 'Google (AI Overviews, Gemini)',
						'chatgpt-user'     => 'OpenAI (пользовательский агент)',
						'perplexity-user'  => 'Perplexity (пользовательский агент)',
						'claude-user'      => 'Anthropic (пользовательский агент)',
						'bingbot'          => 'Bing / Copilot',
						'applebot'         => 'Apple Intelligence / Siri',
						'meta-externalagent'=> 'Meta AI',
					];
					$r = $c->cr->robots;
					if ($r === null || !$r->exists) {
						return $out;
					}
					$blocked = [];
					$undecided = [];
					foreach ($citation as $bot => $who) {
						$rules = $r->groups[$bot] ?? null;
						if ($rules === null) {
							$undecided[] = $bot;
							continue;
						}
						foreach ($rules as $rule) {
							if (!$rule['allow'] && ($rule['path'] === '/' || $rule['path'] === '')) {
								$blocked[] = $bot . ' (' . $who . ')';
								break;
							}
						}
					}
					if ($blocked) {
						$out[] = ['url' => null, 'detail' => 'Заблокированы цитатные боты: ' . implode(', ', $blocked)];
					}
					if ($undecided && !$blocked) {
						$out[] = ['url' => null, 'detail' => 'Нет явных правил для ' . count($undecided) . ' цитатных ботов (берётся общий User-agent: *)'];
					}
					return $out;
				},
			],
			// --- GEO: цитируемость ---
			[
				'id' => 'ai.entity_definition', 'cat' => 'AI и GEO', 'sev' => 'low', 'scope' => 'page',
				'title' => 'В начале страницы — определение сущности (30–80 слов)',
				'hint' => 'Исследование Aggarwal et al. (KDD 2024): определение сущности в первом абзаце повышает цитируемость в генеративных ответах. Первое предложение должно отвечать «что это и для кого».',
				'check' => static function (Ctx $c): array {
					$out = [];
					foreach ($c->pages() as $url => $p) {
						$h = $p['html'];
						if ((int)$h['words'] < 200 || (int)$h['firstParaLen'] === 0) {
							continue;
						}
						$words = preg_match_all('~[\p{L}\p{N}]+~u', (string)$h['firstPara']) ?: 0;
						if ($words < 20 || $words > 100) {
							$out[] = ['url' => $url, 'detail' => 'первый абзац — ' . $words . ' слов (нужно 30–80 для определения сущности)'];
						}
					}
					return $out;
				},
			],
			[
				'id' => 'ai.quick_answer', 'cat' => 'AI и GEO', 'sev' => 'low', 'scope' => 'page',
				'title' => 'В начале есть короткий готовый ответ (до 160 символов)',
				'hint' => '«Быстрый ответ» в первом блоке — то, что AI-системы цитируют дословно. Дайте ответ до длинных объяснений.',
				'check' => static function (Ctx $c): array {
					$out = [];
					foreach ($c->pages() as $url => $p) {
						$h = $p['html'];
						if ((int)$h['words'] < 200) {
							continue;
						}
						$len = (int)$h['firstParaLen'];
						if ($len > 160) {
							$out[] = ['url' => $url, 'detail' => 'первый абзац — ' . $len . ' символов, короткого ответа нет'];
						}
					}
					return $out;
				},
			],
			[
				'id' => 'ai.sources_cited', 'cat' => 'AI и GEO', 'sev' => 'low', 'scope' => 'page',
				'title' => 'Страница ссылается на внешние источники',
				'hint' => 'Самый сильный метод цитируемости в KDD-2024: ссылки на авторитетные источники внутри текста (+30–115% видимости). Без них контент выглядит голословным.',
				'check' => static function (Ctx $c): array {
					$out = [];
					foreach ($c->pages() as $url => $p) {
						$h = $p['html'];
						if ((int)$h['words'] < 300) {
							continue;
						}
						$ext = 0;
						foreach ($h['links'] as $l) {
							$abs = Url::resolve($l['raw'], $url);
							if ($abs === null) {
								continue;
							}
							if (!Url::sameSite($abs, $url)) {
								$ext++;
							}
						}
						if ($ext === 0) {
							$out[] = ['url' => $url, 'detail' => 'ни одной внешней ссылки на источник'];
						}
					}
					return $out;
				},
			],
			[
				'id' => 'ai.statistics', 'cat' => 'AI и GEO', 'sev' => 'info', 'scope' => 'page',
				'title' => 'В тексте есть цифры и факты со статистикой',
				'hint' => 'Цитаты со статистикой поднимают видимость в генеративных ответах (до +40% по Aggarwal et al., 2024). Голословный текст цитируют реже.',
				'check' => static function (Ctx $c): array {
					$out = [];
					foreach ($c->pages() as $url => $p) {
						$h = $p['html'];
						if ((int)$h['words'] < 300) {
							continue;
						}
						if ((int)$h['statsCount'] === 0) {
							$out[] = ['url' => $url, 'detail' => 'ни одной цифры со статистикой (%, ₽, м², срок) в тексте'];
						}
					}
					return $out;
				},
			],
			[
				'id' => 'ai.quotes', 'cat' => 'AI и GEO', 'sev' => 'info', 'scope' => 'page',
				'title' => 'В тексте есть цитаты с атрибуцией',
				'hint' => 'Прямая цитата с именем, ролью и годом — один из методов цитируемости (+30–40%): подтверждает экспертность материала.',
				'check' => static function (Ctx $c): array {
					$out = [];
					foreach ($c->pages() as $url => $p) {
						$h = $p['html'];
						if ((int)$h['words'] < 300) {
							continue;
						}
						if ((int)$h['quoteCount'] === 0) {
							$out[] = ['url' => $url, 'detail' => 'в тексте нет прямых цитат'];
						}
					}
					return $out;
				},
			],
			[
				'id' => 'ai.js_dependency', 'cat' => 'AI и GEO', 'sev' => 'medium', 'scope' => 'page',
				'title' => 'Контент не зависит от JS-рендеринга',
				'hint' => 'AI-краулеры не выполняют JavaScript. Если текст появляется только после JS, ассистент увидит пустую страницу.',
				'check' => static function (Ctx $c): array {
					$out = [];
					foreach ($c->pages() as $url => $p) {
						$h = $p['html'];
						// только основной контент: текст меню и футера не говорит о JS-зависимости
						$text = (int)($h['mainTextLen'] ?? $h['textLen']);
						$nodes = (int)$h['nodes'];
						if ($text < 400 && $nodes > 200) {
							$out[] = ['url' => $url, 'detail' => 'в HTML ' . $text . ' символов текста при ' . $nodes . ' узлах DOM — контент, вероятно, рендерится JS'];
						}
					}
					return $out;
				},
			],
			[
				'id' => 'ai.date_modified', 'cat' => 'AI и GEO', 'sev' => 'info', 'scope' => 'page',
				'title' => 'У страницы указана дата обновления (dateModified)',
				'hint' => 'dateModified — сигнал свежести, который читают и поисковики, и AI-системы. Без него материал выглядит неактуальным.',
				'check' => static function (Ctx $c): array {
					$out = [];
					foreach ($c->pages() as $url => $p) {
						$found = false;
						foreach ($p['html']['jsonld'] as $block) {
							if ($block['ok'] && stripos((string)$block['raw'], 'dateModified') !== false) {
								$found = true;
								break;
							}
						}
						if (!$found) {
							$out[] = ['url' => $url, 'detail' => 'нет dateModified в JSON-LD'];
						}
					}
					return $out;
				},
			],
			[
				'id' => 'ai.llms_full', 'cat' => 'AI и GEO', 'sev' => 'info', 'scope' => 'site',
				'title' => 'Есть /llms-full.txt — развёрнутая карта для моделей',
				'hint' => 'llms-full.txt — расширенная версия карты сайта со всем содержимым. Модели читают её целиком при построении ответов.',
				'check' => static function (Ctx $c): array {
					$out = [];
					$req = Http::fetch(Url::origin($c->cfg->start) . '/llms-full.txt', $c->cfg, 'HEAD');
					if ($req->status !== 200) {
						$out[] = ['url' => null, 'detail' => '/llms-full.txt не найден (HTTP ' . ($req->status ?: 'нет ответа') . ')'];
					}
					return $out;
				},
			],
		];
	}

	// =====================================================================
	// 11. AI И GEO (попадание в ответы AI-поиска и ассистентов)
	// =====================================================================
	private static function rulesAiGeo(): array
	{
		$aiBots = [
			'gptbot'        => 'OpenAI (ChatGPT, поиск)',
			'oai-searchbot' => 'OpenAI (поисковый индекс ChatGPT)',
			'claudebot'     => 'Anthropic (Claude)',
			'perplexitybot' => 'Perplexity',
			'google-extended'=> 'Google (обучение Gemini, AI Overviews)',
			'ccbot'         => 'Common Crawl (датасеты обучения)',
			'bytespider'    => 'ByteDance (Doubao)',
			'amazonbot'     => 'Amazon (Alexa, Rufus)',
		];

		return [
			[
				'id' => 'ai.llms_txt', 'cat' => 'AI и GEO', 'sev' => 'low', 'scope' => 'site',
				'title' => 'Есть /llms.txt — карта сайта для LLM',
				'hint' => 'llms.txt — открытый стандарт (llmstxt.org): краткая карта ключевых страниц для языковых моделей. Без неё ассистент не знает, что цитировать.',
				'check' => static function (Ctx $c): array {
					$out = [];
					$t = $c->cr->llmsTxt;
					if ($t === []) {
						return $out;
					}
					if ((int)($t['status'] ?? 0) !== 200) {
						$out[] = ['url' => null, 'detail' => '/llms.txt не найден (HTTP ' . ($t['status'] ?: 'нет ответа') . ')'];
						return $out;
					}
					if (empty($t['hasTitle'])) {
						$out[] = ['url' => null, 'detail' => 'В /llms.txt нет заголовка «# Название» — формат не соблюдён'];
					}
					if ((int)($t['links'] ?? 0) === 0) {
						$out[] = ['url' => null, 'detail' => 'В /llms.txt нет ссылок на страницы'];
					}
					return $out;
				},
			],
			[
				'id' => 'ai.plugin_manifest', 'cat' => 'AI и GEO', 'sev' => 'low', 'scope' => 'site',
				'title' => 'Есть манифест для AI-агентов (ai-plugin.json / agents.json)',
				'hint' => 'Манифест описывает, что умеет и где брать данные вашего сайта — это вход для агентов и плагинов. Формат задаётся OpenAI/Google.',
				'check' => static function (Ctx $c): array {
					$out = [];
					$found = false;
					foreach ($c->cr->aiManifests as $path => $m) {
						if ((int)($m['status'] ?? 0) === 200) {
							$found = true;
							if (!is_array($m['json'] ?? null)) {
								$out[] = ['url' => null, 'detail' => $path . ' отдаётся, но это не валидный JSON'];
							}
						}
					}
					if (!$found) {
						$out[] = ['url' => null, 'detail' => 'Нет ни /ai-plugin.json, ни /.well-known/agents.json'];
					}
					return $out;
				},
			],
			[
				'id' => 'ai.robots_crawlers', 'cat' => 'AI и GEO', 'sev' => 'medium', 'scope' => 'site',
				'title' => 'robots.txt не блокирует AI-краулеры без нужды',
				'hint' => 'Disallow для GPTBot/ClaudeBot/PerplexityBot означает, что контент не попадёт в ответы AI-поиска. Это осознанное решение, но оно должно быть осознанным.',
				'check' => static function (Ctx $c) use ($aiBots): array {
					$out = [];
					$r = $c->cr->robots;
					if ($r === null || !$r->exists) {
						return $out;
					}
					$blocked = [];
					$allowed = [];
					$undecided = [];
					foreach ($aiBots as $bot => $who) {
						$rules = $r->groups[$bot] ?? null;
						if ($rules === null) {
							$undecided[] = $bot;
							continue;
						}
						$isBlocked = false;
						foreach ($rules as $rule) {
							if (!$rule['allow'] && ($rule['path'] === '/' || $rule['path'] === '')) {
								$isBlocked = true;
							}
						}
						if ($isBlocked) {
							$blocked[] = $bot . ' (' . $who . ')';
						} else {
							$allowed[] = $bot;
						}
					}
					if ($blocked) {
						$out[] = ['url' => null, 'detail' => 'Заблокированы: ' . implode(', ', $blocked)];
					}
					if ($undecided) {
						$out[] = ['url' => null, 'detail' => 'Нет явных правил (берётся общий User-agent: *): ' . implode(', ', $undecided)];
					}
					if ($allowed && !$blocked) {
						// явно разрешены — это хорошо, но стоит зафиксировать
						$out[] = ['url' => null, 'detail' => 'AI-краулеры разрешены явно: ' . implode(', ', $allowed)];
					}
					return $out;
				},
			],
			[
				'id' => 'ai.answer_structure', 'cat' => 'AI и GEO', 'sev' => 'low', 'scope' => 'page',
				'title' => 'Страница пригодна для цитирования в AI-ответах',
				'hint' => 'AI-ответы собираются из коротких абзацев, списков, FAQ-разметки и прямых ответов. Страница без структуры цитируется реже.',
				'check' => static function (Ctx $c): array {
					$out = [];
					foreach ($c->pages() as $url => $p) {
						$h = $p['html'];
						$signals = [];
						if ((int)$h['lists'] === 0) {
							$signals[] = 'нет списков';
						}
						if ((int)$h['questions'] === 0) {
							$signals[] = 'нет вопросов в заголовках';
						}
						if (empty($h['faqSchema'])) {
							$signals[] = 'нет FAQPage-разметки';
						}
						if (count($signals) === 3) {
							$out[] = ['url' => $url, 'detail' => 'нет структуры для цитирования: ' . implode(', ', $signals)];
						}
					}
					return $out;
				},
			],
			[
				'id' => 'ai.eeat', 'cat' => 'AI и GEO', 'sev' => 'low', 'scope' => 'page',
				'title' => 'У страницы указан автор и дата (E-E-A-T для AI)',
				'hint' => 'AI-системы и поисковики оценивают, кто и когда создал контент. Автор и дата повышают доверие и вероятность попасть в ответ.',
				'check' => static function (Ctx $c): array {
					$out = [];
					foreach ($c->pages() as $url => $p) {
						$h = $p['html'];
						$miss = [];
						if (empty($h['hasAuthor'])) {
							$miss[] = 'автор';
						}
						if (empty($h['hasDate'])) {
							$miss[] = 'дата';
						}
						if ($miss) {
							$out[] = ['url' => $url, 'detail' => 'не указано: ' . implode(', ', $miss)];
						}
					}
					return $out;
				},
			],
			[
				'id' => 'ai.answer_first', 'cat' => 'AI и GEO', 'sev' => 'info', 'scope' => 'page',
				'title' => 'Ответы в тексте оформлены короткими абзацами',
				'hint' => 'Короткий абзац (40–260 символов) — это то, что AI цитирует дословно. Огромные «простыни» цитируются хуже.',
				'check' => static function (Ctx $c): array {
					$out = [];
					foreach ($c->pages() as $url => $p) {
						$h = $p['html'];
						$paras = (int)$h['paras'];
						if ($paras < 3) {
							continue;
						}
						if ((int)$h['shortParas'] === 0) {
							$out[] = ['url' => $url, 'detail' => $paras . ' абзацев, ни одного короткого (40–260 симв.)'];
						}
					}
					return $out;
				},
			],
			[
				'id' => 'ai.entity_sameas', 'cat' => 'AI и GEO', 'sev' => 'info', 'scope' => 'site',
				'title' => 'sameAs связывает сущность с профилями и источниками',
				'hint' => 'sameAs в JSON-LD связывает компанию с её страницами в соцсетях и базах знаний: так AI-системы понимают, кто вы.',
				'check' => static function (Ctx $c): array {
					$out = [];
					$with = 0;
					foreach ($c->pages() as $url => $p) {
						if ((int)$p['html']['sameAs'] > 0) {
							$with++;
						}
					}
					$total = count($c->pages());
					if ($total > 0 && $with === 0) {
						$out[] = ['url' => null, 'detail' => 'Ни на одной из ' . $total . ' страниц нет sameAs'];
					}
					return $out;
				},
			],
		];
	}

	// =====================================================================
	//  URL-ГИГИЕНА (добавляется к техническим проверкам)
	// =====================================================================
	private static function rulesUrlHygiene(): array
	{
		return [
			[
				'id' => 'url.too_long', 'cat' => 'Технические ошибки', 'sev' => 'low', 'scope' => 'page',
				'title' => 'Длина URL не превышает 100 символов',
				'hint' => 'Длинные URL обрезаются в выдаче и неудобны в шаринге; часть систем отсекает их на 100+ символах.',
				'check' => static function (Ctx $c): array {
					$out = [];
					foreach ($c->pages() as $url => $p) {
						$len = strlen(Ctx::pathKey($url));
						if ($len > 100) {
							$out[] = ['url' => $url, 'detail' => $len . ' симв.'];
						}
					}
					return $out;
				},
			],
			[
				'id' => 'url.mixed_case', 'cat' => 'Технические ошибки', 'sev' => 'low', 'scope' => 'page',
				'title' => 'В пути URL нет заглавных букв',
				'hint' => 'Регистр в URL создаёт дубли: /Blog и /blog — разные адреса для робота, но одна страница для пользователя.',
				'check' => static function (Ctx $c): array {
					$out = [];
					foreach ($c->pages() as $url => $p) {
						$path = (string)(parse_url($url, PHP_URL_PATH) ?: '/');
						if (preg_match('~[A-Z]~', $path)) {
							$out[] = ['url' => $url, 'detail' => 'заглавные буквы в пути'];
						}
					}
					return $out;
				},
			],
			[
				'id' => 'url.underscore', 'cat' => 'Технические ошибки', 'sev' => 'low', 'scope' => 'page',
				'title' => 'В URL использован дефис, а не подчёркивание',
				'hint' => 'Подчёркивание в URL не всегда корректно читается движками и хуже разделяет слова, чем дефис.',
				'check' => static function (Ctx $c): array {
					$out = [];
					foreach ($c->pages() as $url => $p) {
						$path = (string)(parse_url($url, PHP_URL_PATH) ?: '/');
						if (str_contains($path, '_')) {
							$out[] = ['url' => $url, 'detail' => 'подчёркивание в пути'];
						}
					}
					return $out;
				},
			],
			[
				'id' => 'url.non_ascii', 'cat' => 'Технические ошибки', 'sev' => 'info', 'scope' => 'page',
				'title' => 'URL не содержит не-ASCII символов',
				'hint' => 'Кириллица в адресе усложняет копирование, аналитику и канонизацию; часть систем и агентов ломаются на таких URL.',
				'check' => static function (Ctx $c): array {
					$out = [];
					foreach ($c->pages() as $url => $p) {
						$path = (string)(parse_url($url, PHP_URL_PATH) ?: '/');
						if (preg_match('~[^\x21-\x7E]~', $path)) {
							$out[] = ['url' => $url, 'detail' => 'не-ASCII в пути: ' . $path];
						}
					}
					return $out;
				},
			],
			[
				'id' => 'url.slash_consistency', 'cat' => 'Технические ошибки', 'sev' => 'low', 'scope' => 'site',
				'title' => 'Слеш в конце URL используется единообразно',
				'hint' => '/blog и /blog/ — разные адреса для робота. Смешивание создаёт дубли и размывает внутренний вес.',
				'check' => static function (Ctx $c): array {
					$out = [];
					$with = 0;
					$without = 0;
					foreach ($c->cr->inlinks as $url => $info) {
						if (isset($c->cr->pages[$url])) {
							continue;
						}
						$path = (string)(parse_url($url, PHP_URL_PATH) ?: '/');
						if ($path === '/') {
							continue;
						}
						if (str_ends_with($path, '/')) {
							$with++;
						} else {
							$without++;
						}
					}
					if ($with > 0 && $without > 0) {
						$out[] = ['url' => null, 'detail' => 'внутренние ссылки смешивают варианты: со слешем — ' . $with . ', без слеша — ' . $without];
					}
					return $out;
				},
			],
			[
				'id' => 'index.pagination', 'cat' => 'Индексация', 'sev' => 'low', 'scope' => 'page',
				'title' => 'Пагинация размечена rel=next / rel=prev',
				'hint' => 'rel=next/prev помогает роботу понять последовательность страниц и не размножать дубли пагинации.',
				'check' => static function (Ctx $c): array {
					$out = [];
					foreach ($c->pages() as $url => $p) {
						$h = $p['html'];
						$isPaginated = (bool)preg_match('~(?:/page/\d+|/p=\d+|[?&]page=\d+)~i', (string)(parse_url($url, PHP_URL_QUERY) ?: parse_url($url, PHP_URL_PATH)));
						if (!$isPaginated || $h['relNext'] || $h['relPrev']) {
							continue;
						}
						$out[] = ['url' => $url, 'detail' => 'страница пагинации без rel=next/prev'];
					}
					return $out;
				},
			],
			[
				'id' => 'perf.font_display', 'cat' => 'Скорость', 'sev' => 'low', 'scope' => 'page',
				'title' => 'В @font-face задан font-display: swap',
				'hint' => 'Без font-display браузер блокирует текст до загрузки шрифта — растут FOUT и CLS.',
				'check' => static function (Ctx $c): array {
					$out = [];
					foreach ($c->pages() as $url => $p) {
						$n = (int)($p['html']['fontNoDisplay'] ?? 0);
						if ($n > 0) {
							$out[] = ['url' => $url, 'detail' => $n . ' @font-face без font-display (в инлайн-стилях)'];
						}
					}
					return $out;
				},
			],
			[
				'id' => 'perf.http2', 'cat' => 'Скорость', 'sev' => 'info', 'scope' => 'site',
				'title' => 'Сервер поддерживает HTTP/2 или новее',
				'hint' => 'HTTP/2 мультиплексирует запросы в одном соединении: меньше RTT и быстрее первая отрисовка.',
				'check' => static function (Ctx $c): array {
					$out = [];
					if ($c->isLocal()) {
						return $out;
					}
					$http1 = 0;
					$total = 0;
					foreach ($c->pages() as $url => $p) {
						$v = (int)$p['httpVersion'];
						if ($v === 0) {
							continue;
						}
						$total++;
						if ($v < 2) {
							$http1++;
						}
					}
					if ($total > 0 && $http1 === $total) {
						$out[] = ['url' => null, 'detail' => 'Все ' . $total . ' ответов пришли по HTTP/1.1 — HTTP/2 не используется'];
					}
					return $out;
				},
			],
			[
				'id' => 'content.keyword_density', 'cat' => 'Контент', 'sev' => 'info', 'scope' => 'page',
				'title' => 'Нет перегруза терминами (ориентир 3,5%)',
				'hint' => 'Плотность одного термина выше 3,5% — признак переспама: поисковик понижает такие страницы.',
				'check' => static function (Ctx $c): array {
					$out = [];
					foreach ($c->pages() as $url => $p) {
						$top = $p['html']['topKeywords'] ?? [];
						if (!$top) {
							continue;
						}
						$kw = $top[0];
						if ((float)$kw['share'] > 0.035) {
							$out[] = ['url' => $url, 'detail' => '«' . $kw['w'] . '» — ' . round((float)$kw['share'] * 100, 1) . '% текста (' . $kw['n'] . ' вхождений)'];
						}
					}
					return $out;
				},
			],
		];
	}

	// =====================================================================
	// 1. ТЕХНИЧЕСКИЕ ОШИБКИ
	// =====================================================================
	private static function rulesTechnical(): array
	{
		return [
			[
				'id' => 'http.error', 'cat' => 'Технические ошибки', 'sev' => 'critical', 'scope' => 'page',
				'title' => 'Страницы открываются без сетевых ошибок',
				'hint' => 'Проверьте логи сервера: таймаут/обрыв соединения, неверный DNS, отказ в соединении.',
				'check' => static function (Ctx $c): array {
					$out = [];
					foreach ($c->pages() as $url => $p) {
						if (!empty($p['error'])) {
							$out[] = ['url' => $url, 'detail' => 'Ошибка соединения: ' . $p['error']];
						}
					}
					return $out;
				},
			],
			[
				'id' => 'http.status', 'cat' => 'Технические ошибки', 'sev' => 'critical', 'scope' => 'page',
				'title' => 'Все страницы отдают код 2xx',
				'hint' => '404/500 на индексируемых URL ломают обход и репутацию домена. Проверьте роутинг и .htaccess.',
				'check' => static function (Ctx $c): array {
					$out = [];
					foreach ($c->pages() as $url => $p) {
						$s = (int)$p['status'];
						if ($s < 200 || $s >= 300) {
							$out[] = ['url' => $url, 'detail' => 'HTTP ' . ($s ?: 'нет ответа')];
						}
					}
					return $out;
				},
			],
			[
				'id' => 'http.redirect', 'cat' => 'Технические ошибки', 'sev' => 'medium', 'scope' => 'page',
				'title' => 'Нет лишних редиректов',
				'hint' => 'Каждый редирект — лишний запрос и потеря веса. Оставьте только канонизацию (http→https, www) и 301 для переезда.',
				'check' => static function (Ctx $c): array {
					$out = [];
					foreach ($c->pages() as $url => $p) {
						$r = $p['redirects'] ?? [];
						if (!$r) {
							continue;
						}
						$chain = [];
						foreach ($r as $hop) {
							$chain[] = $hop['status'] . ' → ' . Ctx::pathKey($hop['to']);
						}
						$out[] = ['url' => $url, 'detail' => count($r) . ' редирект(ов): ' . implode('; ', $chain)];
					}
					return $out;
				},
			],
			[
				'id' => 'http.redirect_loop', 'cat' => 'Технические ошибки', 'sev' => 'critical', 'scope' => 'page',
				'title' => 'Нет петель и длинных цепочек редиректов',
				'hint' => 'Петля редиректов краулер не может разрешить — страница выпадает из индекса.',
				'check' => static function (Ctx $c): array {
					$out = [];
					foreach ($c->pages() as $url => $p) {
						$r = $p['redirects'] ?? [];
						if (count($r) < 2) {
							continue;
						}
						$seen = [];
						$loop = false;
						foreach ($r as $hop) {
							$k = Ctx::pathKey($hop['to']);
							if (isset($seen[$k])) {
								$loop = true;
							}
							$seen[$k] = true;
						}
						if ($loop || count($r) >= 4) {
							$out[] = ['url' => $url, 'detail' => $loop ? 'Обнаружена петля редиректов' : 'Цепочка длиннее 3 редиректов'];
						}
					}
					return $out;
				},
			],
			[
				'id' => 'http.redirect_to_error', 'cat' => 'Технические ошибки', 'sev' => 'critical', 'scope' => 'page',
				'title' => 'Редиректы ведут на существующие страницы',
				'hint' => 'Редирект в 404/500 обрывает путь пользователя. Уберите редирект или создайте целевую страницу.',
				'check' => static function (Ctx $c): array {
					$out = [];
					foreach ($c->pages() as $url => $p) {
						if (empty($p['redirects'])) {
							continue;
						}
						$s = (int)$p['status'];
						if ($s >= 400) {
							$out[] = ['url' => $url, 'detail' => 'После редиректа — HTTP ' . $s];
						}
					}
					return $out;
				},
			],
			[
				'id' => 'http.redirect_cross_host', 'cat' => 'Технические ошибки', 'sev' => 'medium', 'scope' => 'page',
				'title' => 'Редиректы не уводят на другой домен',
				'hint' => 'Редирект на внешний домен передаёт сигналы и вес другому сайту. Проверьте, так ли задумано.',
				'check' => static function (Ctx $c): array {
					$out = [];
					foreach ($c->pages() as $url => $p) {
						foreach (($p['redirects'] ?? []) as $hop) {
							if (!Url::sameSite($hop['to'], $url)) {
								$out[] = ['url' => $url, 'detail' => 'Внешний редирект на ' . $hop['to']];
								break;
							}
						}
					}
					return $out;
				},
			],
			[
				'id' => 'http.soft404', 'cat' => 'Технические ошибки', 'sev' => 'high', 'scope' => 'page',
				'title' => 'Нет «мягких 404» (страница с кодом 200 и пустым содержанием)',
				'hint' => 'Верните настоящий код 404 — так страница не попадёт в индекс как дубль.',
				'check' => static function (Ctx $c): array {
					$out = [];
					foreach ($c->pages() as $url => $p) {
						if ((int)$p['status'] !== 200) {
							continue;
						}
						$h = $p['html'];
						$text = mb_strtolower((string)$h['text']);
						$looks404 = (bool)preg_match('~не найден|не существует|страница не|404|not found|page doesn\'?t exist~u', $text);
						if ($looks404 && (int)$h['words'] < 120) {
							$out[] = ['url' => $url, 'detail' => 'Код 200, но текст похож на 404 (' . $h['words'] . ' слов)'];
						}
					}
					return $out;
				},
			],
			[
				'id' => 'http.empty', 'cat' => 'Технические ошибки', 'sev' => 'medium', 'scope' => 'page',
				'title' => 'Страницы не пустые',
				'hint' => 'Пустой ответ с кодом 200 — признак сбоя шаблона или кэша.',
				'check' => static function (Ctx $c): array {
					$out = [];
					foreach ($c->pages() as $url => $p) {
						if ((int)$p['status'] === 200 && (int)$p['bodySize'] < 200) {
							$out[] = ['url' => $url, 'detail' => 'Тело ответа ' . $p['bodySize'] . ' байт'];
						}
					}
					return $out;
				},
			],
			[
				'id' => 'http.https_available', 'cat' => 'Технические ошибки', 'sev' => 'high', 'scope' => 'site',
				'title' => 'Сайт доступен по HTTPS',
				'hint' => 'Без HTTPS нельзя включить HSTS, браузеры помечают сайт как небезопасный, а Яндекс/Google понижают доверие.',
				'check' => static function (Ctx $c): array {
					$out = [];
					$origin = $c->origin();
					if (str_starts_with($origin, 'https://') || $c->isLocal()) {
						return $out;
					}
					$https = preg_replace('~^http://~i', 'https://', $origin) . '/';
					$req = Http::fetch($https, $c->cfg, 'HEAD');
					if ($req->status === 0) {
						$out[] = ['url' => null, 'detail' => 'HTTPS-версия не отвечает (' . ($req->error ?: 'нет ответа') . ')'];
					} elseif ($req->status >= 500) {
						$out[] = ['url' => null, 'detail' => 'HTTPS-версия отдаёт HTTP ' . $req->status];
					}
					return $out;
				},
			],
			[
				'id' => 'http.http_to_https', 'cat' => 'Технические ошибки', 'sev' => 'medium', 'scope' => 'site',
				'title' => 'HTTP перенаправляется на HTTPS (301)',
				'hint' => 'Добавьте принудительный редирект http→https одним шагом, без связок цепочек.',
				'check' => static function (Ctx $c): array {
					$out = [];
					$origin = $c->origin();
					if (str_starts_with($origin, 'https://') || $c->isLocal()) {
						return $out;
					}
					$https = preg_replace('~^http://~i', 'https://', $origin) . '/';
					$req = Http::fetch($https, $c->cfg, 'HEAD');
					if ($req->status === 0 || $req->status >= 400) {
						return $out; // HTTPS не работает — другая проблема
					}
					$reqHttp = Http::fetch($origin . '/', $c->cfg, 'HEAD');
					$redirectsToHttps = false;
					foreach ($reqHttp->redirects as $hop) {
						if (str_starts_with($hop['to'], 'https://')) {
							$redirectsToHttps = true;
						}
					}
					if (!$redirectsToHttps && $reqHttp->status === 200) {
						$out[] = ['url' => null, 'detail' => 'http:// отдаёт 200 без редиректа на https'];
					}
					return $out;
				},
			],
			[
				'id' => 'http.mixed_content', 'cat' => 'Технические ошибки', 'sev' => 'high', 'scope' => 'page',
				'title' => 'Нет смешанного контента (http:// ресурсы на https-странице)',
				'hint' => 'Браузер блокирует такие ресурсы — «битые» картинки/скрипты и предупреждение в консоли.',
				'check' => static function (Ctx $c): array {
					$out = [];
					$httpsSite = str_starts_with($c->origin(), 'https://');
					if (!$httpsSite) {
						return $out;
					}
					foreach ($c->pages() as $url => $p) {
						$bad = [];
						// Смешанный контент = только подгружаемые ресурсы, а не гиперссылки
						foreach ($p['html']['resources'] as $res) {
							if (str_starts_with(strtolower($res['url']), 'http://')) {
								$bad[] = $res['tag'] . ' ' . $res['url'];
							}
						}
						if ($bad) {
							$out[] = ['url' => $url, 'detail' => count($bad) . ' шт.: ' . implode(', ', array_slice(array_unique($bad), 0, 4))];
						}
					}
					return $out;
				},
			],
		];
	}

	// =====================================================================
	// 2. ИНДЕКСАЦИЯ
	// =====================================================================
	private static function rulesIndexing(): array
	{
		return [
			[
				'id' => 'index.noindex_meta', 'cat' => 'Индексация', 'sev' => 'info', 'scope' => 'page',
				'title' => 'Страницы, закрытые от индекса (noindex) — список на проверку',
				'hint' => 'Если эти страницы должны быть в индексе — снимите noindex. Если нет — оставьте, но не ставьте их в sitemap и не собирайте на них основной трафик.',
				'check' => static function (Ctx $c): array {
					$out = [];
					foreach ($c->pages() as $url => $p) {
						$robots = strtolower((string)$p['html']['robotsMeta']);
						if ($robots !== '' && str_contains($robots, 'noindex')) {
							$out[] = ['url' => $url, 'detail' => 'meta robots: ' . $robots];
						}
					}
					return $out;
				},
			],
			[
				'id' => 'index.noindex_header', 'cat' => 'Индексация', 'sev' => 'high', 'scope' => 'page',
				'title' => 'Нет X-Robots-Tag: noindex',
				'hint' => 'Заголовок важнее meta-тега: поисковик увидит noindex даже при корректном <meta>.',
				'check' => static function (Ctx $c): array {
					$out = [];
					foreach ($c->pages() as $url => $p) {
						$h = strtolower((string)($p['headers']['x-robots-tag'] ?? ''));
						if ($h !== '' && str_contains($h, 'noindex')) {
							$out[] = ['url' => $url, 'detail' => 'X-Robots-Tag: ' . $h];
						}
					}
					return $out;
				},
			],
			[
				'id' => 'index.nofollow', 'cat' => 'Индексация', 'sev' => 'medium', 'scope' => 'page',
				'title' => 'Страницы не закрыты nofollow',
				'hint' => 'nofollow на всей странице останавливает передачу веса по ссылкам — обычно это ошибка.',
				'check' => static function (Ctx $c): array {
					$out = [];
					foreach ($c->pages() as $url => $p) {
						$robots = strtolower((string)$p['html']['robotsMeta']);
						if ($robots !== '' && str_contains($robots, 'nofollow')) {
							$out[] = ['url' => $url, 'detail' => 'meta robots: ' . $robots];
						}
					}
					return $out;
				},
			],
			[
				'id' => 'index.canonical_missing', 'cat' => 'Индексация', 'sev' => 'medium', 'scope' => 'page',
				'title' => 'У каждой страницы задан canonical',
				'hint' => 'Без canonical дубли (www, слэш, параметры) конкурируют между собой за выдачу.',
				'check' => static function (Ctx $c): array {
					$out = [];
					foreach ($c->pages() as $url => $p) {
						if (empty($p['html']['canonical'])) {
							$out[] = ['url' => $url, 'detail' => 'Тег <link rel="canonical"> отсутствует'];
						}
					}
					return $out;
				},
			],
			[
				'id' => 'index.canonical_mismatch', 'cat' => 'Индексация', 'sev' => 'medium', 'scope' => 'page',
				'title' => 'Canonical ведёт на другую страницу (не на сам URL)',
				'hint' => 'Если canonical ведёт на другую страницу, она выпадает из индекса в пользу целевой. Для дублей (фильтры, пагинация) добавьте к canonical ещё noindex.',
				'check' => static function (Ctx $c): array {
					$out = [];
					foreach ($c->pages() as $url => $p) {
						$canon = (string)($p['html']['canonical'] ?? '');
						if ($canon === '') {
							continue;
						}
						$robots = strtolower((string)$p['html']['robotsMeta']);
						if (str_contains($robots, 'noindex')) {
							continue;   // noindex + canonical на хаб — осознанное решение
						}
						$abs = Url::resolve($canon, $url);
						if ($abs === null) {
							$out[] = ['url' => $url, 'detail' => 'Canonical не разбирается: ' . $canon];
							continue;
						}
						[$abs] = Url::splitFragment($abs);
						if (Ctx::pathKey($abs) === Ctx::pathKey($url)) {
							continue;
						}
						// canonical снимает параметры — штатная практика для фильтров/пагинации
						$base = static fn(string $u): string => explode('?', Ctx::pathKey($u), 2)[0];
						if ($base($abs) === $base($url)) {
							$out[] = ['url' => $url, 'detail' => 'canonical снимает параметры → ' . $base($abs) . ' (для фильтров/пагинации — штатно, добавьте noindex)'];
							continue;
						}
						$out[] = ['url' => $url, 'detail' => 'canonical → ' . Ctx::pathKey($abs)];
					}
					return $out;
				},
			],
			[
				'id' => 'index.canonical_broken', 'cat' => 'Индексация', 'sev' => 'high', 'scope' => 'page',
				'title' => 'Страница, указанная в canonical, доступна',
				'hint' => 'Canonical на 404/ошибку = страница не индексируется вообще.',
				'check' => static function (Ctx $c): array {
					$out = [];
					foreach ($c->pages() as $url => $p) {
						$canon = (string)($p['html']['canonical'] ?? '');
						if ($canon === '') {
							continue;
						}
						$abs = Url::resolve($canon, $url);
						if ($abs === null) {
							continue;
						}
						[$abs] = Url::splitFragment($abs);
						$status = $c->statusOf($abs);
						if ($status !== null && $status >= 400) {
							$out[] = ['url' => $url, 'detail' => 'canonical → ' . Ctx::pathKey($abs) . ' (HTTP ' . $status . ')'];
						}
					}
					return $out;
				},
			],
			[
				'id' => 'index.canonical_cross_domain', 'cat' => 'Индексация', 'sev' => 'high', 'scope' => 'page',
				'title' => 'Canonical не ведёт на чужой домен',
				'hint' => 'Междоменный canonical передаёт индексацию другому сайту.',
				'check' => static function (Ctx $c): array {
					$out = [];
					$host = $c->host();
					foreach ($c->pages() as $url => $p) {
						$canon = (string)($p['html']['canonical'] ?? '');
						if ($canon === '') {
							continue;
						}
						$abs = Url::resolve($canon, $url);
						if ($abs === null) {
							continue;
						}
						if (!Url::sameSite($abs, $url)) {
							$out[] = ['url' => $url, 'detail' => 'canonical ведёт на чужой домен: ' . Url::hostOf($abs)];
						} elseif (Url::hostOf($abs) !== Url::hostOf($url)) {
							$out[] = ['url' => $url, 'detail' => 'canonical ведёт на вариант домена (' . Url::hostOf($abs) . ' вместо ' . Url::hostOf($url) . ')'];
						}
					}
					return $out;
				},
			],
			[
				'id' => 'index.sitemap_format', 'cat' => 'Индексация', 'sev' => 'medium', 'scope' => 'site',
				'title' => 'Формат sitemap.xml корректен (lastmod, priority, changefreq, домен)',
				'hint' => 'Некорректный lastmod или priority заставляют поисковик игнорировать данные карты. Дата — в формате W3C (YYYY-MM-DD или ISO-8601).',
				'check' => static function (Ctx $c): array {
					$out = [];
					$allowedFreq = ['always', 'hourly', 'daily', 'weekly', 'monthly', 'yearly', 'never'];
					$badDate = [];
					$badPriority = [];
					$badFreq = [];
					$foreign = [];
					foreach ($c->cr->sitemaps->urls as $url => $meta) {
						$lm = $meta['lastmod'] ?? null;
						if ($lm !== null && $lm !== '') {
							if (!preg_match('~^\d{4}-\d{2}-\d{2}([T ]\d{2}:\d{2}(:\d{2})?(\.\d+)?(Z|[+\-]\d{2}:?\d{2})?)?$~', $lm)) {
								$badDate[] = $lm . ' (' . Ctx::pathKey($url) . ')';
							}
						}
						$pr = $meta['priority'] ?? null;
						if ($pr !== null && $pr !== '') {
							if (!is_numeric($pr) || (float)$pr < 0 || (float)$pr > 1) {
								$badPriority[] = $pr . ' (' . Ctx::pathKey($url) . ')';
							}
						}
						$cf = $meta['changefreq'] ?? null;
						if ($cf !== null && $cf !== '' && !in_array(strtolower($cf), $allowedFreq, true)) {
							$badFreq[] = $cf . ' (' . Ctx::pathKey($url) . ')';
						}
						if (!$c->isInternal($url)) {
							$foreign[] = $url;
						}
					}
					if ($badDate) {
						$out[] = ['url' => null, 'detail' => 'Некорректный lastmod (' . count($badDate) . '): ' . implode(', ', array_slice($badDate, 0, 4))];
					}
					if ($badPriority) {
						$out[] = ['url' => null, 'detail' => 'Некорректный priority (' . count($badPriority) . '): ' . implode(', ', array_slice($badPriority, 0, 4))];
					}
					if ($badFreq) {
						$out[] = ['url' => null, 'detail' => 'Некорректный changefreq (' . count($badFreq) . '): ' . implode(', ', array_slice($badFreq, 0, 4))];
					}
					if ($foreign) {
						$out[] = ['url' => null, 'detail' => 'В карте URL чужого домена (' . count($foreign) . '): ' . implode(', ', array_slice($foreign, 0, 3))];
					}
					return $out;
				},
			],
			[
				'id' => 'index.canonical_noindex', 'cat' => 'Индексация', 'sev' => 'low', 'scope' => 'page',
				'title' => 'Нет противоречия «canonical + noindex»',
				'hint' => 'noindex с canonical убирает страницу из индекса, но оставляет canonical — сигналы противоречат друг другу. Для служебных страниц (поиск, политика) это обычно безвредно.',
				'check' => static function (Ctx $c): array {
					$out = [];
					foreach ($c->pages() as $url => $p) {
						$robots = strtolower((string)$p['html']['robotsMeta']);
						$header = strtolower((string)($p['headers']['x-robots-tag'] ?? ''));
						$noindex = str_contains($robots, 'noindex') || str_contains($header, 'noindex');
						if ($noindex && !empty($p['html']['canonical'])) {
							$out[] = ['url' => $url, 'detail' => 'noindex + canonical → ' . Ctx::pathKey((string)$p['html']['canonical'])];
						}
					}
					return $out;
				},
			],
			[
				'id' => 'index.hreflang', 'cat' => 'Индексация', 'sev' => 'low', 'scope' => 'site',
				'title' => 'Указаны hreflang для языковых версий',
				'hint' => 'Для одноязычного сайта hreflang не обязателен. Для мультиязычного — нужен и должен быть взаимным (ru ↔ en).',
				'check' => static function (Ctx $c): array {
					$out = [];
					$withLang = 0;
					$total = 0;
					foreach ($c->pages() as $url => $p) {
						$total++;
						if (!empty($p['html']['hreflang'])) {
							$withLang++;
						}
					}
					if ($total > 0 && $withLang === 0) {
						$out[] = ['url' => null, 'detail' => 'Ни на одной из ' . $total . ' страниц нет hreflang'];
					}
					return $out;
				},
			],
			[
				'id' => 'index.hreflang_broken', 'cat' => 'Индексация', 'sev' => 'high', 'scope' => 'page',
				'title' => 'Альтернативы hreflang ведут на существующие страницы',
				'hint' => 'hreflang на 404 или редирект игнорируется поисковиком целиком.',
				'check' => static function (Ctx $c): array {
					$out = [];
					foreach ($c->pages() as $url => $p) {
						foreach ($p['html']['hreflang'] as $al) {
							if (trim((string)$al['href']) === '') {
								$out[] = ['url' => $url, 'detail' => 'hreflang ' . $al['lang'] . ': пустой href'];
								continue;
							}
							$abs = Url::resolve((string)$al['href'], $url);
							if ($abs === null) {
								continue;
							}
							$status = $c->statusOf($abs);
							if ($status !== null && $status >= 400) {
								$out[] = ['url' => $url, 'detail' => 'hreflang ' . $al['lang'] . ' → ' . Ctx::pathKey($abs) . ' (HTTP ' . $status . ')'];
							}
						}
					}
					return $out;
				},
			],
			[
				'id' => 'index.hreflang_on_canonical', 'cat' => 'Индексация', 'sev' => 'low', 'scope' => 'page',
				'title' => 'hreflang не стоит на страницах, канонизированных на другой URL',
				'hint' => 'Если страница отдаёт canonical на другой адрес, её hreflang игнорируется. Для дублей (фильтры, пагинация) уберите hreflang или снимите canonical.',
				'check' => static function (Ctx $c): array {
					$out = [];
					foreach ($c->pages() as $url => $p) {
						$alts = $p['html']['hreflang'];
						if (!$alts) {
							continue;
						}
						$canon = (string)($p['html']['canonical'] ?? '');
						if ($canon === '') {
							continue;
						}
						$cabs = Url::resolve($canon, $url);
						if ($cabs === null) {
							continue;
						}
						[$cabs] = Url::splitFragment($cabs);
						if (Ctx::pathKey($cabs) !== Ctx::pathKey($url)) {
							$out[] = ['url' => $url, 'detail' => 'canonical → ' . Ctx::pathKey($cabs) . ', но hreflang задан (' . count($alts) . ')'];
						}
					}
					return $out;
				},
			],
			[
				'id' => 'index.hreflang_not_return', 'cat' => 'Индексация', 'sev' => 'medium', 'scope' => 'page',
				'title' => 'hreflang взаимный (страница указывает и на себя в ответ)',
				'hint' => 'Без обратной ссылки hreflang-пара не принимается: добавьте alternates на обеих версиях.',
				'check' => static function (Ctx $c): array {
					$out = [];
					foreach ($c->pages() as $url => $p) {
						$alts = $p['html']['hreflang'];
						if (!$alts) {
							continue;
						}
						// Проверяем взаимность только у «настоящих» языковых версий
						// (canonical указывает на саму страницу) — иначе это дубль, а не перевод.
						$canon = (string)($p['html']['canonical'] ?? '');
						if ($canon !== '') {
							$cabs = Url::resolve($canon, $url);
							if ($cabs !== null) {
								[$cabs] = Url::splitFragment($cabs);
								if (Ctx::pathKey($cabs) !== Ctx::pathKey($url)) {
									continue;
								}
							}
						}
						foreach ($alts as $al) {
							$abs = Url::resolve((string)$al['href'], $url);
							if ($abs === null) {
								continue;
							}
							[$abs] = Url::splitFragment($abs);
							$target = null;
							foreach ($c->pages() as $u2 => $p2) {
								if (Ctx::pathKey($u2) === Ctx::pathKey($abs)) {
									$target = $p2;
									break;
								}
							}
							if ($target === null) {
								continue;
							}
							$back = false;
							foreach ($target['html']['hreflang'] as $al2) {
								$abs2 = Url::resolve((string)$al2['href'], (string)($target['url'] ?? ''));
								if ($abs2 !== null && Ctx::pathKey($abs2) === Ctx::pathKey($url)) {
									$back = true;
								}
							}
							if (!$back) {
								$out[] = ['url' => $url, 'detail' => 'hreflang ' . $al['lang'] . ' → ' . Ctx::pathKey($abs) . ' не возвращает обратную ссылку'];
							}
						}
					}
					return $out;
				},
			],
			[
				'id' => 'index.robots_txt_missing', 'cat' => 'Индексация', 'sev' => 'high', 'scope' => 'site',
				'title' => 'robots.txt доступен и отдаёт 200',
				'hint' => 'Без robots.txt вы не управляете обходом, а часть роботов (Yandex, Google) не получает карту сайта.',
				'check' => static function (Ctx $c): array {
					$out = [];
					$r = $c->cr->robots;
					if ($r === null) {
						return $out;
					}
					if (!$r->exists) {
						$out[] = ['url' => null, 'detail' => 'robots.txt не найден (HTTP ' . ($r->status ?: 'нет ответа') . ')'];
					}
					return $out;
				},
			],
			[
				'id' => 'index.robots_blocks', 'cat' => 'Индексация', 'sev' => 'high', 'scope' => 'page',
				'title' => 'robots.txt не запрещает индексируемые страницы',
				'hint' => 'Disallow на важном URL закрывает его и от поисковика, и от внутренней перелинковки.',
				'check' => static function (Ctx $c): array {
					$out = [];
					$r = $c->cr->robots;
					if ($r === null || !$r->exists) {
						return $out;
					}
					foreach ($c->pages() as $url => $p) {
						$robots = strtolower((string)$p['html']['robotsMeta']);
						if (str_contains($robots, 'noindex')) {
							continue;
						}
						$path = parse_url($url, PHP_URL_PATH) ?: '/';
						$q = parse_url($url, PHP_URL_QUERY);
						if (!$r->allowed($path . ($q ? '?' . $q : ''))) {
							$out[] = ['url' => $url, 'detail' => 'Путь запрещён в robots.txt'];
						}
					}
					return $out;
				},
			],
			[
				'id' => 'index.robots_syntax', 'cat' => 'Индексация', 'sev' => 'low', 'scope' => 'site',
				'title' => 'Синтаксис robots.txt корректен',
				'hint' => 'Опечатки в директивах делают правило бессмысленным — робот трактует файл построчно.',
				'check' => static function (Ctx $c): array {
					$out = [];
					$r = $c->cr->robots;
					if ($r === null || !$r->exists) {
						return $out;
					}
					foreach ($r->errors as $e) {
						$out[] = ['url' => null, 'detail' => $e];
					}
					return $out;
				},
			],
			[
				'id' => 'index.orphan', 'cat' => 'Индексация', 'sev' => 'high', 'scope' => 'page',
				'title' => 'Все страницы получают внутренние ссылки',
				'hint' => 'Страница без входящих ссылок — «сирота»: краулер находит её только по sitemap, а вес она не получает.',
				'check' => static function (Ctx $c): array {
					$out = [];
					$home = rtrim($c->origin(), '/') . '/';
					foreach ($c->pages() as $url => $p) {
						if ($url === $home || rtrim($url, '/') === rtrim($home, '/')) {
							continue;
						}
						$in = $c->cr->inlinks[$url]['count'] ?? 0;
						if ($in === 0) {
							$out[] = ['url' => $url, 'detail' => 'Входящих ссылок: 0'];
						}
					}
					return $out;
				},
			],
			[
				'id' => 'index.depth', 'cat' => 'Индексация', 'sev' => 'low', 'scope' => 'page',
				'title' => 'Страницы не глубже 3–4 кликов от главной (отраслевой ориентир)',
				'hint' => 'Страницы глубже 3 кликов плохо индексируются. Добавьте навигацию, хабы и перелинковку.',
				'check' => static function (Ctx $c): array {
					$out = [];
					foreach ($c->pages() as $url => $p) {
						if ((int)$p['depth'] > 3) {
							$out[] = ['url' => $url, 'detail' => 'Глубина: ' . $p['depth'] . ' клик(ов)'];
						}
					}
					return $out;
				},
			],
			[
				'id' => 'index.robots_bots', 'cat' => 'Индексация', 'sev' => 'critical', 'scope' => 'site',
				'title' => 'Основные поисковые роботы не заблокированы',
				'hint' => 'Робот Яндекса (и Googlebot, Bingbot) не должен быть закрыт Disallow: иначе сайт просто не попадёт в выдачу этой системы. Отдельно проверяют Яндекс.Картинки, Яндекс.Видео и Яндекс.Новости.',
				'check' => static function (Ctx $c): array {
					$out = [];
					$r = $c->cr->robots;
					if ($r === null || !$r->exists) {
						return $out;
					}
					$bots = [
						'yandex'         => 'Яндекс (основной робот)',
						'googlebot'      => 'Google',
						'bingbot'        => 'Bing',
						'yandeximages'   => 'Яндекс.Картинки',
						'yandexvideo'    => 'Яндекс.Видео',
						'yandexmedia'    => 'Яндекс.Новости',
						'yandexblogs'    => 'Яндекс.Блоги',
					];
					$blocked = [];
					foreach ($bots as $bot => $who) {
						$rules = $r->groups[$bot] ?? null;
						if ($rules === null) {
							continue;   // действует общее правило User-agent: *
						}
						foreach ($rules as $rule) {
							if (!$rule['allow'] && ($rule['path'] === '/' || $rule['path'] === '')) {
								$blocked[] = $who . ' (' . $bot . ')';
								break;
							}
						}
					}
					if ($blocked) {
						$out[] = ['url' => null, 'detail' => 'Полностью заблокированы: ' . implode(', ', $blocked)];
					}
					return $out;
				},
			],
			[
				'id' => 'index.clean_param', 'cat' => 'Индексация', 'sev' => 'critical', 'scope' => 'site',
				'title' => 'Clean-param не содержит параметров, меняющих содержимое страницы',
				'hint' => 'Clean-param читает только робот Яндекса: он склеивает адреса, отличающиеся этими параметрами, и переносит метрики на основной URL. Вносить туда можно только параметры, НЕ меняющие содержимое (utm-метки, рефереры). Параметры фильтров и сортировки там опасны: робот посчитает разные страницы одинаковыми и уберёт их из выдачи.',
				'check' => static function (Ctx $c): array {
					$out = [];
					$r = $c->cr->robots;
					if ($r === null || !$r->exists || !$r->cleanParams) {
						return $out;
					}
					// параметры, реально встречающиеся в URL сайта: значение → отпечаток содержимого
					$byParam = [];
					foreach ($c->cr->pages as $url => $p) {
						$q = parse_url($url, PHP_URL_QUERY);
						if (!$q) {
							continue;
						}
						foreach (explode('&', $q) as $pair) {
							if (!str_contains($pair, '=')) {
								continue;
							}
							[$name, $value] = explode('=', $pair, 2);
							$byParam[$name][$value] = (string)($p['html']['textHash'] ?? '');
						}
					}
					$clean = [];
					foreach ($r->cleanParams as $ref => $params) {
						foreach ($params as $p) {
							$clean[strtolower($p)] = true;
						}
						if ($ref !== '') {
							$clean[strtolower($ref)] = true;
						}
					}
					// параметр меняет содержимое, если у разных значений разный отпечаток текста
					$contentChanging = [];
					foreach ($byParam as $name => $values) {
						if (count(array_unique($values)) < 2) {
							continue;
						}
						$contentChanging[] = $name;
					}
					$danger = [];
					foreach ($contentChanging as $name) {
						if (isset($clean[strtolower($name)])) {
							$danger[] = $name;
						}
					}
					if ($danger) {
						$out[] = ['url' => null, 'detail' => 'в Clean-param попали параметры, меняющие содержимое: ' . implode(', ', $danger)
							. ' — робот Яндекса склеит разные страницы в одну и уберёт их из выдачи'];
					}
					// служебные параметры, которые не закрыты
					$tracking = [];
					foreach (array_keys($byParam) as $name) {
						if (preg_match('~^(utm_|yclid|gclid|dclid|fbclid|mc_cid|mc_eid|_openstat|ref|referrer)~i', $name)
							&& !isset($clean[strtolower($name)])) {
							$tracking[] = $name;
						}
					}
					if ($tracking) {
						$out[] = ['url' => null, 'detail' => 'служебные параметры не закрыты Clean-param: ' . implode(', ', array_slice($tracking, 0, 8))
							. ' — Яндекс обычно удаляет аналитические метки сам, но явная директива надёжнее'];
					}
					return $out;
				},
			],
			[
				'id' => 'index.robots_encoding', 'cat' => 'Индексация', 'sev' => 'low', 'scope' => 'site',
				'title' => 'Пути в robots.txt записаны в percent-encoding',
				'hint' => 'Яндекс прямо требует кодировать не-ASCII пути в Disallow (например /%D0%BA%D0%BE%D1%80%D0%B7%D0%B8%D0%BD%D0%B0). Запись кириллицейLiteral не сработает и раздел останется открытым для обхода.',
				'check' => static function (Ctx $c): array {
					$out = [];
					$r = $c->cr->robots;
					if ($r === null || !$r->exists) {
						return $out;
					}
					$bad = [];
					foreach ($r->groups as $agent => $rules) {
						foreach ($rules as $rule) {
							if (preg_match('~[^\x21-\x7E]~', $rule['path'])) {
								$bad[] = $agent . ': ' . $rule['raw'];
							}
						}
					}
					if ($bad) {
						$out[] = ['url' => null, 'detail' => 'не-ASCII в директивах (' . count($bad) . '): ' . implode('; ', array_slice($bad, 0, 3)) . ' — нужно percent-encoding'];
					}
					return $out;
				},
			],
			[
				'id' => 'index.robots_limits', 'cat' => 'Индексация', 'sev' => 'info', 'scope' => 'site',
				'title' => 'robots.txt отвечает 200 и не превышает 500 КБ',
				'hint' => 'Требование Яндекса: файл лежит в корне, отдаёт 200 OK и весит не больше 500 КБ. При превышении робот читает файл частично и часть правил не применяет.',
				'check' => static function (Ctx $c): array {
					$out = [];
					$r = $c->cr->robots;
					if ($r === null) {
						return $out;
					}
					if (!$r->exists) {
						$out[] = ['url' => null, 'detail' => 'robots.txt недоступен (HTTP ' . ($r->status ?: 'нет ответа') . ') — робот не получает ни правил, ни адрес карты сайта'];
					}
					return $out;
				},
			],
			[
				'id' => 'index.robots_host', 'cat' => 'Индексация', 'sev' => 'info', 'scope' => 'site',
				'title' => 'Указан Host для выбора основного зеркала',
				'hint' => 'Директива Host — только для робота Яндекса: указывает, какое зеркало считать основным, если сайт доступен по нескольким адресам. Директива может быть только одна.',
				'check' => static function (Ctx $c): array {
					$out = [];
					$r = $c->cr->robots;
					if ($r === null || !$r->exists) {
						return $out;
					}
					if ($r->host === null) {
						$out[] = ['url' => null, 'detail' => 'Host не задан: при нескольких зеркалах робот Яндекса сам выберет основное — и это не обязательно нужное вам'];
					}
					return $out;
				},
			],
			[
				'id' => 'index.crawl_delay', 'cat' => 'Индексация', 'sev' => 'info', 'scope' => 'site',
				'title' => 'Crawl-delay: robots.txt не управляет скоростью Яндекса',
				'hint' => 'С 22 февраля 2018 года Яндекс не учитывает Crawl-delay. Частоту обхода задают только в Вебмастере (Настройки → Скорость обхода). Директива в файле остаётся подсказкой для других роботов.',
				'check' => static function (Ctx $c): array {
					$out = [];
					$r = $c->cr->robots;
					if ($r === null || !$r->exists || $r->crawlDelay === []) {
						return $out;
					}
					foreach ($r->crawlDelay as $agent => $sec) {
						$out[] = ['url' => null, 'detail' => 'Crawl-delay: ' . $sec . ' с для ' . $agent . ' — Яндекс его игнорирует, скорость обхода задаётся в Вебмастере'];
					}
					return $out;
				},
			],
			[
				'id' => 'index.verification', 'cat' => 'Индексация', 'sev' => 'info', 'scope' => 'site',
				'title' => 'Сайт подтверждён в Вебмастере и Search Console',
				'hint' => 'Теги подтверждения (yandex-verification, google-site-verification) или файл на сервере позволяют работать с отчётами, переобходом и инструментами вебмастера.',
				'check' => static function (Ctx $c): array {
					$out = [];
					$found = ['yandex' => false, 'google' => false];
					foreach ($c->cr->pages as $url => $p) {
						foreach ($p['html']['meta'] ?? [] as $k => $v) {
							if (str_contains(strtolower((string)$k), 'yandex-verification')) {
								$found['yandex'] = true;
							}
							if (str_contains(strtolower((string)$k), 'google-site-verification')) {
								$found['google'] = true;
							}
						}
					}
					$miss = [];
					if (!$found['yandex']) {
						$miss[] = 'yandex-verification';
					}
					if (!$found['google']) {
						$miss[] = 'google-site-verification';
					}
					if ($miss) {
						$out[] = ['url' => null, 'detail' => 'нет тегов подтверждения: ' . implode(', ', $miss) . ' (возможна и другая схема подтверждения — файл или DNS)'];
					}
					return $out;
				},
			],
			[
				'id' => 'index.sitemap_directive', 'cat' => 'Индексация', 'sev' => 'low', 'scope' => 'site',
				'title' => 'Карта сайта указана в robots.txt',
				'hint' => 'Директива Sitemap: в robots.txt — самый надёжный способ сообщить поисковику о карте сайта.',
				'check' => static function (Ctx $c): array {
					$out = [];
					$r = $c->cr->robots;
					if ($r === null || !$r->exists) {
						return $out;
					}
					if (!$r->sitemaps) {
						$out[] = ['url' => null, 'detail' => 'В robots.txt нет директивы Sitemap:'];
					}
					return $out;
				},
			],
			[
				'id' => 'index.sitemap_missing', 'cat' => 'Индексация', 'sev' => 'high', 'scope' => 'site',
				'title' => 'Карта сайта доступна',
				'hint' => 'Добавьте /sitemap.xml и укажите его в robots.txt — это самый дешёвый способ показать все URL.',
				'check' => static function (Ctx $c): array {
					$out = [];
					if (!$c->cr->sitemaps->exists) {
						$out[] = ['url' => null, 'detail' => 'sitemap.xml не найден (' . implode('; ', array_slice($c->cr->sitemaps->errors, 0, 2)) . ')'];
					}
					return $out;
				},
			],
			[
				'id' => 'index.sitemap_vs_crawl', 'cat' => 'Индексация', 'sev' => 'medium', 'scope' => 'site',
				'title' => 'Карта сайта совпадает с фактической структурой',
				'hint' => 'URL, которые есть только в карте, — «сироты»: добавьте на них ссылки с сайта. URL вне карты — добавьте в карту.',
				'check' => static function (Ctx $c): array {
					$out = [];
					$smPaths = [];
					foreach (array_keys($c->cr->sitemaps->urls) as $u) {
						$smPaths[Ctx::pathKey($u)] = $u;
					}
					$pagePaths = $c->pathMap();
					$onlySitemap = array_diff_key($smPaths, $pagePaths);
					$onlyCrawl = array_diff_key($pagePaths, $smPaths);
					if ($onlySitemap) {
						$out[] = ['url' => null, 'detail' => 'В карте, но недостижимы по ссылкам (' . count($onlySitemap) . '): ' . implode(', ', array_slice(array_keys($onlySitemap), 0, 8))];
					}
					$withQuery = [];
					$real = [];
					foreach (array_keys($onlyCrawl) as $path) {
						if (str_contains($path, '?')) {
							$withQuery[] = $path;
							continue;
						}
						// noindex-страницы в карте быть не должно — их отсутствие это норма
						$isNoindex = false;
						foreach ($c->pages() as $pu => $pp) {
							if (Ctx::pathKey($pu) === $path) {
								$isNoindex = str_contains(strtolower((string)$pp['html']['robotsMeta']), 'noindex');
								break;
							}
						}
						if (!$isNoindex) {
							$real[] = $path;
						}
					}
					if ($real) {
						$out[] = ['url' => null, 'detail' => 'Реальные страницы вне карты (' . count($real) . '): ' . implode(', ', array_slice($real, 0, 8))];
					}
					if ($withQuery) {
						$out[] = ['url' => null, 'detail' => 'URL с параметрами вне карты (' . count($withQuery) . ') — это нормально, но их не должно быть в индексе: ' . implode(', ', array_slice($withQuery, 0, 5))];
					}
					return $out;
				},
			],
			[
				'id' => 'index.url_list', 'cat' => 'Индексация', 'sev' => 'medium', 'scope' => 'site',
				'title' => 'Файл-список /pages совпадает с картой и обходом',
				'hint' => '/pages используют для ручных проверок и Screaming Frog — он должен совпадать с sitemap.xml и с реальной структурой сайта.',
				'check' => static function (Ctx $c): array {
					$out = [];
					if (!$c->cr->urlList) {
						return $out;
					}
					$pagePaths = $c->pathMap();
					$smPaths = [];
					foreach (array_keys($c->cr->sitemaps->urls) as $u) {
						$smPaths[Ctx::pathKey($u)] = true;
					}
					$notCrawled = [];
					$notInMap = [];
					foreach ($c->cr->urlList as $u) {
						$p = Ctx::pathKey($u);
						if (!isset($pagePaths[$p])) {
							$notCrawled[] = $p;
						}
						$isNoindex = false;
						foreach ($c->pages() as $pu => $pp) {
							if (Ctx::pathKey($pu) === $p) {
								$isNoindex = str_contains(strtolower((string)$pp['html']['robotsMeta']), 'noindex');
								break;
							}
						}
						if (!isset($smPaths[$p]) && !$isNoindex) {
							$notInMap[] = $p;   // noindex-страницы в карте быть не должно
						}
					}
					if ($notCrawled) {
						$out[] = ['url' => null, 'detail' => 'В /pages, но краулер не дошёл (' . count($notCrawled) . '): ' . implode(', ', array_slice($notCrawled, 0, 8))];
					}
					if ($notInMap) {
						$out[] = ['url' => null, 'detail' => 'В /pages, но нет в sitemap.xml (' . count($notInMap) . '): ' . implode(', ', array_slice($notInMap, 0, 8))];
					}
					return $out;
				},
			],
			[
				'id' => 'index.sitemap_bad_status', 'cat' => 'Индексация', 'sev' => 'critical', 'scope' => 'site',
				'title' => 'Все URL из карты сайта отвечают 200',
				'hint' => 'Битые URL в карте — сигнал о неряшливом сайте. Уберите их или восстановите страницы.',
				'check' => static function (Ctx $c): array {
					$out = [];
					foreach ($c->cr->sitemapStatus as $url => $status) {
						if ($status >= 400 || $status === 0) {
							$out[] = ['url' => $url, 'detail' => 'HTTP ' . ($status ?: 'нет ответа')];
						}
					}
					return $out;
				},
			],
			[
				'id' => 'index.sitemap_noindex', 'cat' => 'Индексация', 'sev' => 'high', 'scope' => 'site',
				'title' => 'В карте сайта нет noindex-страниц',
				'hint' => 'URL с noindex в карте — противоречие: уберите их из карты или снимите noindex.',
				'check' => static function (Ctx $c): array {
					$out = [];
					foreach ($c->cr->sitemaps->urls as $url => $meta) {
						$page = null;
						foreach ($c->pages() as $u => $p) {
							if (Ctx::pathKey($u) === Ctx::pathKey($url)) {
								$page = $p;
								break;
							}
						}
						if ($page === null) {
							continue;
						}
						$robots = strtolower((string)$page['html']['robotsMeta']);
						if (str_contains($robots, 'noindex')) {
							$out[] = ['url' => $url, 'detail' => 'noindex, но URL в карте'];
						}
					}
					return $out;
				},
			],
			[
				'id' => 'index.sitemap_duplicates', 'cat' => 'Индексация', 'sev' => 'medium', 'scope' => 'site',
				'title' => 'В карте сайта нет дублей URL',
				'hint' => 'Дубли в карте размывают приоритеты и расходуют лимит обхода.',
				'check' => static function (Ctx $c): array {
					$out = [];
					$seen = [];
					foreach ($c->cr->sitemaps->urls as $url => $meta) {
						$k = Ctx::pathKey($url);
						if (isset($seen[$k])) {
							$out[] = ['url' => $url, 'detail' => 'Дубль пути: ' . $k];
						}
						$seen[$k] = true;
					}
					return $out;
				},
			],
			[
				'id' => 'index.sitemap_lastmod', 'cat' => 'Индексация', 'sev' => 'low', 'scope' => 'site',
				'title' => 'lastmod в карте сайта корректен',
				'hint' => 'Дата в будущем или очень старая дата обесценивает lastmod: поисковик перестаёт ему доверять.',
				'check' => static function (Ctx $c): array {
					$out = [];
					$now = time();
					$old = 0;
					foreach ($c->cr->sitemaps->urls as $url => $meta) {
						$lm = $meta['lastmod'] ?? null;
						if ($lm === null) {
							continue;
						}
						$ts = strtotime($lm);
						if ($ts === false) {
							$out[] = ['url' => $url, 'detail' => 'Нечитаемая дата lastmod: ' . $lm];
							continue;
						}
						if ($ts > $now + 86400) {
							$out[] = ['url' => $url, 'detail' => 'lastmod в будущем: ' . $lm];
						} elseif ($ts < $now - 400 * 86400) {
							$old++;
						}
					}
					if ($old > 0) {
						$out[] = ['url' => null, 'detail' => 'Устаревший lastmod (старше 13 месяцев) у ' . $old . ' URL'];
					}
					return $out;
				},
			],
			[
				'id' => 'index.duplicate_content', 'cat' => 'Индексация', 'sev' => 'high', 'scope' => 'site',
				'title' => 'Нет дублирующихся страниц по содержимому',
				'hint' => 'Дубли (шаблоны, фильтры, пагинация) забирают место у уникальных страниц. Синхронизируйте canonical и содержание.',
				'check' => static function (Ctx $c): array {
					$out = [];
					$pages = $c->pages();
					$ids = array_keys($pages);
					$indexable = [];
					foreach ($ids as $i => $id) {
						$robots = strtolower((string)$pages[$id]['html']['robotsMeta']);
						if (str_contains($robots, 'noindex')) {
							continue;   // закрытые от индекса страницы не конкурируют
						}
						$canon = (string)($pages[$id]['html']['canonical'] ?? '');
						if ($canon !== '') {
							$cabs = Url::resolve($canon, $id);
							if ($cabs !== null) {
								[$cabs] = Url::splitFragment($cabs);
								if (Ctx::pathKey($cabs) !== Ctx::pathKey($id)) {
									continue;   // страница уже канонизирована на другой URL — дубли не конкурируют
								}
							}
						}
						$indexable[$i] = true;
					}
					$index = [];   // эскиз-шингл → [id] (кандидаты)
					foreach ($ids as $i => $id) {
						if (!isset($indexable[$i])) {
							continue;
						}
						foreach (($pages[$id]['html']['sketch'] ?? []) as $sh) {
							$index[$sh][] = $i;
						}
					}
					$candidates = [];
					foreach ($index as $sh => $list) {
						if (count($list) < 2) {
							continue;
						}
						for ($a = 0; $a < count($list); $a++) {
							for ($b = $a + 1; $b < count($list); $b++) {
								$key = $list[$a] . '|' . $list[$b];
								$candidates[$key] = ($candidates[$key] ?? 0) + 1;
							}
						}
					}
					$seenPairs = [];
					foreach ($candidates as $key => $shared) {
						[$a, $b] = explode('|', $key);
						$sa = $pages[$ids[$a]]['html']['shingles'] ?? [];
						$sb = $pages[$ids[$b]]['html']['shingles'] ?? [];
						if (!$sa || !$sb) {
							continue;
						}
						$inter = count(array_intersect($sa, $sb));
						$union = count($sa) + count($sb) - $inter;
						$j = $union > 0 ? $inter / $union : 0;
						if ($j >= 0.85) {
							$pair = $ids[$a] . '~' . $ids[$b];
							if (isset($seenPairs[$pair])) {
								continue;
							}
							$seenPairs[$pair] = true;
							$out[] = ['url' => $ids[$a], 'detail' => 'Похоже на ' . Ctx::pathKey($ids[$b]) . ' — совпадение текста ' . round($j * 100) . '%'];
						}
					}
					return $out;
				},
			],
		];
	}

	// =====================================================================
	// 3. МЕТА-ТЕГИ И ЗАГОЛОВКИ
	// =====================================================================
	private static function rulesMeta(): array
	{
		return [
			[
				'id' => 'meta.title_missing', 'cat' => 'Мета-теги', 'sev' => 'critical', 'scope' => 'page',
				'title' => 'У каждой страницы есть <title>',
				'hint' => 'Title — главный сигнал запроса и заголовок в выдаче. Формула: «Ключевое предложение | Бренд».',
				'check' => static function (Ctx $c): array {
					$out = [];
					foreach ($c->pages() as $url => $p) {
						if (trim((string)$p['html']['title']) === '') {
							$out[] = ['url' => $url, 'detail' => 'Тег <title> пуст или отсутствует'];
						}
					}
					return $out;
				},
			],
			[
				'id' => 'meta.title_length', 'cat' => 'Мета-теги', 'sev' => 'low', 'scope' => 'page',
				'title' => 'Title помещается в выдачу без обрезки (по пикселям, а не по символам)',
				'hint' => 'Google не ограничивает длину title и не считает её фактором ранжирования, но обрезает заголовок по пикселям: на десктопе это ~600px, на мобильном заметно меньше. Ориентир отрасли — 50–60 символов, важные слова — в начало.',
				'check' => static function (Ctx $c): array {
					$out = [];
					foreach ($c->pages() as $url => $p) {
						$len = (int)$p['html']['titleLen'];
						if ($len === 0) {
							continue;
						}
						if ($len < 30) {
							$out[] = ['url' => $url, 'detail' => $len . ' симв. — коротко'];
						} elseif ($len > 65) {
							$out[] = ['url' => $url, 'detail' => $len . ' симв. — обрежется в выдаче'];
						}
					}
					return $out;
				},
			],
			[
				'id' => 'meta.title_duplicate', 'cat' => 'Мета-теги', 'sev' => 'high', 'scope' => 'site',
				'title' => 'Заголовки страниц уникальны',
				'hint' => 'Одинаковые title = поисковик не знает, какую страницу показать. Добавьте релевантный хвост.',
				'check' => static function (Ctx $c): array {
					$out = [];
					$by = [];
					foreach ($c->pages() as $url => $p) {
						$t = trim((string)$p['html']['title']);
						if ($t === '') {
							continue;
						}
						if (str_contains(strtolower((string)$p['html']['robotsMeta']), 'noindex')) {
							continue;   // закрытые от индекса страницы не конкурируют
						}
						$by[$t][] = $url;
					}
					foreach ($by as $t => $urls) {
						if (count($urls) > 1) {
							$out[] = ['url' => $urls[0], 'detail' => '"' . mb_substr($t, 0, 70) . '" — ' . count($urls) . ' стр.: ' . implode(', ', array_map(static fn($u) => Ctx::pathKey($u), array_slice($urls, 0, 6)))];
						}
					}
					return $out;
				},
			],
			[
				'id' => 'meta.desc_missing', 'cat' => 'Мета-теги', 'sev' => 'high', 'scope' => 'page',
				'title' => 'У каждой страницы есть meta description',
				'hint' => 'Description формирует сниппет и влияет на CTR. Пишите 120–160 символов с ключевым запросом и выгодой.',
				'check' => static function (Ctx $c): array {
					$out = [];
					foreach ($c->pages() as $url => $p) {
						if (trim((string)$p['html']['description']) === '') {
							$out[] = ['url' => $url, 'detail' => 'Тег <meta name="description"> отсутствует или пуст'];
						}
					}
					return $out;
				},
			],
			[
				'id' => 'meta.desc_length', 'cat' => 'Мета-теги', 'sev' => 'low', 'scope' => 'page',
				'title' => 'Description укладывается в сниппет (≈155–160 символов)',
				'hint' => 'Лимита длины description у Google нет, но сниппет обрезается примерно на 155–160 символах; рекомендация Google — 50–160 символов. На ранжирование не влияет, работает на CTR.',
				'check' => static function (Ctx $c): array {
					$out = [];
					foreach ($c->pages() as $url => $p) {
						$d = trim((string)$p['html']['description']);
						if ($d === '') {
							continue;
						}
						$len = mb_strlen($d);
						if ($len < 70) {
							$out[] = ['url' => $url, 'detail' => $len . ' симв. — мало'];
						} elseif ($len > 165) {
							$out[] = ['url' => $url, 'detail' => $len . ' симв. — обрежется'];
						}
					}
					return $out;
				},
			],
			[
				'id' => 'meta.desc_duplicate', 'cat' => 'Мета-теги', 'sev' => 'high', 'scope' => 'site',
				'title' => 'Описания страниц уникальны',
				'hint' => 'Одинаковые description снижают CTR и ухудшают релевантность выдачи для этих запросов.',
				'check' => static function (Ctx $c): array {
					$out = [];
					$by = [];
					foreach ($c->pages() as $url => $p) {
						$d = trim((string)$p['html']['description']);
						if ($d === '') {
							continue;
						}
						if (str_contains(strtolower((string)$p['html']['robotsMeta']), 'noindex')) {
							continue;
						}
						$by[$d][] = $url;
					}
					foreach ($by as $d => $urls) {
						if (count($urls) > 1) {
							$out[] = ['url' => $urls[0], 'detail' => '"' . mb_substr($d, 0, 60) . '…" — ' . count($urls) . ' стр.: ' . implode(', ', array_map(static fn($u) => Ctx::pathKey($u), array_slice($urls, 0, 6)))];
						}
					}
					return $out;
				},
			],
			[
				'id' => 'meta.h1_missing', 'cat' => 'Мета-теги', 'sev' => 'low', 'scope' => 'page',
				'title' => 'На странице есть <h1> с осмысленным текстом',
				'hint' => 'H1 задаёт тему страницы. Добавьте один H1 с ключевым запросом — не пустой тег.',
				'check' => static function (Ctx $c): array {
					$out = [];
					foreach ($c->pages() as $url => $p) {
						$count = (int)$p['html']['headingCount'][1];
						if ($count === 0) {
							$out[] = ['url' => $url, 'detail' => 'Тег <h1> отсутствует'];
						} elseif ($count > 1) {
							$out[] = ['url' => $url, 'detail' => $count . ' тегов <h1> (должен быть один)'];
						} else {
							$h1 = trim((string)($p['html']['h1'][0] ?? ''));
							if ($h1 === '') {
								$out[] = ['url' => $url, 'detail' => '<h1> пустой'];
							}
						}
					}
					return $out;
				},
			],
			[
				'id' => 'meta.h1_duplicate', 'cat' => 'Мета-теги', 'sev' => 'medium', 'scope' => 'site',
				'title' => 'Заголовки H1 уникальны',
				'hint' => 'Повторяющийся H1 на разных страницах — признак шаблонных (неуникальных) страниц.',
				'check' => static function (Ctx $c): array {
					$out = [];
					$by = [];
					foreach ($c->pages() as $url => $p) {
						$h1 = trim((string)($p['html']['h1'][0] ?? ''));
						if ($h1 === '') {
							continue;
						}
						if (str_contains(strtolower((string)$p['html']['robotsMeta']), 'noindex')) {
							continue;
						}
						$by[$h1][] = $url;
					}
					foreach ($by as $h => $urls) {
						if (count($urls) > 1) {
							$out[] = ['url' => $urls[0], 'detail' => '"' . mb_substr($h, 0, 60) . '" — ' . count($urls) . ' стр.: ' . implode(', ', array_map(static fn($u) => Ctx::pathKey($u), array_slice($urls, 0, 6)))];
						}
					}
					return $out;
				},
			],
			[
				'id' => 'meta.heading_skip', 'cat' => 'Мета-теги', 'sev' => 'low', 'scope' => 'page',
				'title' => 'Иерархия заголовков без пропусков уровней',
				'hint' => 'Переход h1 → h3 без h2 ломает структуру документа и навигацию скринридеров.',
				'check' => static function (Ctx $c): array {
					$out = [];
					foreach ($c->pages() as $url => $p) {
						$prev = 0;
						$skips = [];
						foreach ($p['html']['headings'] as $h) {
							$lvl = (int)$h['level'];
							if ($prev > 0 && $lvl > $prev + 1) {
								$skips[] = 'h' . $prev . ' → h' . $lvl . ' (' . mb_substr($h['text'], 0, 30) . ')';
							}
							$prev = $lvl;
						}
						if ($skips) {
							$out[] = ['url' => $url, 'detail' => implode('; ', array_slice($skips, 0, 3))];
						}
					}
					return $out;
				},
			],
			[
				'id' => 'meta.lang_missing', 'cat' => 'Мета-теги', 'sev' => 'medium', 'scope' => 'page',
				'title' => 'У <html> указан lang',
				'hint' => 'Атрибут lang нужен поисковику для правильного определения языка и для переводчиков.',
				'check' => static function (Ctx $c): array {
					$out = [];
					foreach ($c->pages() as $url => $p) {
						if (trim((string)$p['html']['lang']) === '') {
							$out[] = ['url' => $url, 'detail' => 'Атрибут lang у <html> отсутствует'];
						}
					}
					return $out;
				},
			],
			[
				'id' => 'meta.lang_mismatch', 'cat' => 'Мета-теги', 'sev' => 'medium', 'scope' => 'page',
				'title' => 'lang соответствует языку содержимого',
				'hint' => 'Несовпадение языка (lang="en" при русском тексте) мешает ранжированию и переводам.',
				'check' => static function (Ctx $c): array {
					$out = [];
					foreach ($c->pages() as $url => $p) {
						$lang = strtolower((string)$p['html']['lang']);
						$script = (string)$p['html']['script'];
						if ($lang === '' || $script === 'unknown' || (int)$p['html']['words'] < 40) {
							continue;
						}
						$isRu = str_starts_with($lang, 'ru');
						$isEn = str_starts_with($lang, 'en');
						if ($isRu && $script === 'latin') {
							$out[] = ['url' => $url, 'detail' => 'lang="' . $lang . '", но текст без кириллицы'];
						} elseif ($isEn && $script === 'cyrillic') {
							$out[] = ['url' => $url, 'detail' => 'lang="' . $lang . '", но текст на кириллице'];
						}
					}
					return $out;
				},
			],
			[
				'id' => 'meta.charset_missing', 'cat' => 'Мета-теги', 'sev' => 'low', 'scope' => 'page',
				'title' => 'Кодировка страницы объявлена и равна UTF-8',
				'hint' => 'Без <meta charset> кириллица может отображаться «кракозябрами» — это критично для поведенческих факторов.',
				'check' => static function (Ctx $c): array {
					$out = [];
					foreach ($c->pages() as $url => $p) {
						$cs = trim((string)$p['html']['charset']);
						if ($cs === '') {
							$out[] = ['url' => $url, 'detail' => 'Кодировка не объявлена'];
						} elseif (!in_array(strtoupper($cs), ['UTF-8', 'UTF8'], true)) {
							$out[] = ['url' => $url, 'detail' => 'Кодировка: ' . $cs];
						}
					}
					return $out;
				},
			],
			[
				'id' => 'meta.viewport_missing', 'cat' => 'Мета-теги', 'sev' => 'high', 'scope' => 'page',
				'title' => 'Задан viewport для мобильных устройств',
				'hint' => 'Без viewport мобильный браузер покажет « shrunk » версию и метрика ухудшится.',
				'check' => static function (Ctx $c): array {
					$out = [];
					foreach ($c->pages() as $url => $p) {
						if (trim((string)$p['html']['viewport']) === '') {
							$out[] = ['url' => $url, 'detail' => 'Тег <meta name="viewport"> отсутствует'];
						}
					}
					return $out;
				},
			],
			[
				'id' => 'meta.viewport_restricted', 'cat' => 'Мета-теги', 'sev' => 'medium', 'scope' => 'page',
				'title' => 'Viewport не запрещает масштабирование',
				'hint' => 'user-scalable=no и фиксированная ширина ухудшают доступность для слабовидящих.',
				'check' => static function (Ctx $c): array {
					$out = [];
					foreach ($c->pages() as $url => $p) {
						$vp = strtolower((string)$p['html']['viewport']);
						if ($vp === '') {
							continue;
						}
						if (str_contains($vp, 'user-scalable=no') || str_contains($vp, 'maximum-scale=1')) {
							$out[] = ['url' => $url, 'detail' => $vp];
						}
					}
					return $out;
				},
			],
			[
				'id' => 'meta.og_missing', 'cat' => 'Мета-теги', 'sev' => 'medium', 'scope' => 'page',
				'title' => 'Заполнены Open Graph-теги (title, description, image, url)',
				'hint' => 'Без OG-тегов ссылка в мессенджерах и соцсетях выглядит как голый URL.',
				'check' => static function (Ctx $c): array {
					$out = [];
					foreach ($c->pages() as $url => $p) {
						$og = $p['html']['og'];
						$missing = [];
						foreach (['title' => 'og:title', 'description' => 'og:description', 'image' => 'og:image', 'url' => 'og:url'] as $k => $tag) {
							if (trim((string)($og[$k] ?? '')) === '') {
								$missing[] = $tag;
							}
						}
						if ($missing) {
							$out[] = ['url' => $url, 'detail' => 'Нет: ' . implode(', ', $missing)];
						}
					}
					return $out;
				},
			],
			[
				'id' => 'meta.og_url_mismatch', 'cat' => 'Мета-теги', 'sev' => 'low', 'scope' => 'page',
				'title' => 'og:url совпадает с canonical',
				'hint' => 'Расхождение og:url и canonical приводит к «двум адресам» одной страницы в соцсетях.',
				'check' => static function (Ctx $c): array {
					$out = [];
					foreach ($c->pages() as $url => $p) {
						$ogUrl = trim((string)($p['html']['og']['url'] ?? ''));
						$canon = trim((string)($p['html']['canonical'] ?? ''));
						if ($ogUrl === '' || $canon === '') {
							continue;
						}
						$a = Url::resolve($ogUrl, $url);
						$b = Url::resolve($canon, $url);
						if ($a === null || $b === null) {
							continue;
						}
						if (Ctx::pathKey($a) !== Ctx::pathKey($b)) {
							$out[] = ['url' => $url, 'detail' => 'og:url → ' . Ctx::pathKey($a) . ', canonical → ' . Ctx::pathKey($b)];
						}
					}
					return $out;
				},
			],
			[
				'id' => 'meta.og_image_broken', 'cat' => 'Мета-теги', 'sev' => 'medium', 'scope' => 'page',
				'title' => 'Картинка og:image доступна',
				'hint' => 'Битая og:image = ссылка без превью в соцсетях и в AI-ответчиках.',
				'check' => static function (Ctx $c): array {
					$out = [];
					foreach ($c->pages() as $url => $p) {
						$img = trim((string)($p['html']['og']['image'] ?? ''));
						if ($img === '') {
							continue;
						}
						$abs = Url::resolve($img, $url);
						if ($abs === null) {
							continue;
						}
						$status = $c->statusOf($abs);
						if ($status !== null && $status >= 400) {
							$out[] = ['url' => $url, 'detail' => 'og:image → ' . Ctx::pathKey($abs) . ' (HTTP ' . $status . ')'];
						}
					}
					return $out;
				},
			],
			[
				'id' => 'meta.twitter_missing', 'cat' => 'Мета-теги', 'sev' => 'low', 'scope' => 'page',
				'title' => 'Заполнены Twitter Card-теги',
				'hint' => 'Twitter/X использует свои теги, а не OG. Минимум: twitter:card, twitter:title, twitter:description, twitter:image.',
				'check' => static function (Ctx $c): array {
					$out = [];
					foreach ($c->pages() as $url => $p) {
						$tw = $p['html']['twitter'];
						$missing = [];
						if (trim((string)($tw['card'] ?? '')) === '') {
							$missing[] = 'twitter:card';
						}
						if (trim((string)($tw['title'] ?? '')) === '') {
							$missing[] = 'twitter:title';
						}
						if (trim((string)($tw['image'] ?? '')) === '') {
							$missing[] = 'twitter:image';
						}
						if ($missing) {
							$out[] = ['url' => $url, 'detail' => 'Нет: ' . implode(', ', $missing)];
						}
					}
					return $out;
				},
			],
			[
				'id' => 'meta.favicon', 'cat' => 'Мета-теги', 'sev' => 'low', 'scope' => 'site',
				'title' => 'Указана иконка сайта (favicon)',
				'hint' => 'Иконка отображается во вкладках, закладках и в поиске Яндекса/Google.',
				'check' => static function (Ctx $c): array {
					$out = [];
					$withIcon = 0;
					foreach ($c->pages() as $url => $p) {
						if (!empty($p['html']['icons'])) {
							$withIcon++;
						}
					}
					$total = count($c->pages());
					if ($total > 0 && $withIcon === 0) {
						$out[] = ['url' => null, 'detail' => 'Ни на одной странице нет <link rel="icon">'];
					}
					return $out;
				},
			],
		];
	}

	// =====================================================================
	// 4. РАЗМЕТКА SCHEMA.ORG
	// =====================================================================
	private static function rulesSchema(): array
	{
		return [
			[
				'id' => 'schema.missing', 'cat' => 'Разметка Schema.org', 'sev' => 'medium', 'scope' => 'page',
				'title' => 'Есть разметка Schema.org (JSON-LD)',
				'hint' => 'Разметка даёт расширенные сниппеты и помогает AI-поиску понять суть страницы. Минимум — WebPage/Organization.',
				'check' => static function (Ctx $c): array {
					$out = [];
					foreach ($c->pages() as $url => $p) {
						if (!$p['html']['jsonld']) {
							$out[] = ['url' => $url, 'detail' => 'Блок <script type="application/ld+json"> отсутствует'];
						}
					}
					return $out;
				},
			],
			[
				'id' => 'schema.invalid', 'cat' => 'Разметка Schema.org', 'sev' => 'high', 'scope' => 'page',
				'title' => 'JSON-LD валиден',
				'hint' => 'Битый JSON в разметке полностью игнорируется поисковиком — проверьте кавычки и запятые.',
				'check' => static function (Ctx $c): array {
					$out = [];
					foreach ($c->pages() as $url => $p) {
						foreach ($p['html']['jsonld'] as $block) {
							if (!$block['ok']) {
								$out[] = ['url' => $url, 'detail' => (string)$block['error']];
							}
						}
					}
					return $out;
				},
			],
			[
				'id' => 'schema.no_type', 'cat' => 'Разметка Schema.org', 'sev' => 'medium', 'scope' => 'page',
				'title' => 'В разметке указан @type',
				'hint' => 'Без @type объект не интерпретируется — укажите конкретный тип (Article, Product, LocalBusiness…).',
				'check' => static function (Ctx $c): array {
					$out = [];
					foreach ($c->pages() as $url => $p) {
						foreach ($p['html']['jsonld'] as $block) {
							if ($block['ok'] && !$block['types']) {
								$out[] = ['url' => $url, 'detail' => 'В JSON-LD нет @type'];
							}
						}
					}
					return $out;
				},
			],
			[
				'id' => 'schema.duplicate_type', 'cat' => 'Разметка Schema.org', 'sev' => 'low', 'scope' => 'page',
				'title' => 'Нет дублей «одиночных» @type в одной разметке',
				'hint' => 'Два Organization / BreadcrumbList / WebSite на одной странице конфликтуют — поисковик выберет один наугад. (Множественные Article/BlogPosting — норма для листингов.)',
				'check' => static function (Ctx $c): array {
					$out = [];
					$singleton = ['Organization', 'LocalBusiness', 'WebSite', 'BreadcrumbList', 'FAQPage'];
					foreach ($c->pages() as $url => $p) {
						$counts = [];
						foreach ($p['html']['jsonld'] as $block) {
							foreach ($block['types'] as $t => $n) {
								if (isset($singleton[$t])) {
									$counts[$t] = ($counts[$t] ?? 0) + $n;
								}
							}
						}
						foreach ($counts as $t => $n) {
							if ($n > 1) {
								$out[] = ['url' => $url, 'detail' => $t . ' — ' . $n . ' шт. в одной разметке'];
							}
						}
					}
					return $out;
				},
			],
			[
				'id' => 'schema.required', 'cat' => 'Разметка Schema.org', 'sev' => 'medium', 'scope' => 'page',
				'title' => 'У ключевых типов заполнены обязательные свойства',
				'hint' => 'Без headline/datePublished/author/name разметка не даёт расширенный сниппет.',
				'check' => static function (Ctx $c): array {
					$out = [];
					$required = [
						'Article'     => ['headline', 'datePublished'],
						'BlogPosting' => ['headline', 'datePublished'],
						'Organization' => ['name', 'url'],
						'LocalBusiness' => ['name', 'address'],
						'Product'     => ['name'],
						'FAQPage'     => ['mainEntity'],
						'BreadcrumbList' => ['itemListElement'],
					];
					foreach ($c->pages() as $url => $p) {
						foreach ($p['html']['jsonld'] as $block) {
							if (!$block['ok']) {
								continue;
							}
							$raw = (string)$block['raw'];
							foreach ($block['types'] as $t => $n) {
								if (!isset($required[$t])) {
									continue;
								}
								foreach ($required[$t] as $prop) {
									if (!preg_match('~"' . preg_quote($prop, '~') . '"\s*:~i', $raw)) {
										$out[] = ['url' => $url, 'detail' => $t . ': нет свойства "' . $prop . '"'];
									}
								}
							}
						}
					}
					return $out;
				},
			],
		];
	}

	// =====================================================================
	// 5. КОНТЕНТ
	// =====================================================================
	private static function rulesContent(): array
	{
		return [
			[
				'id' => 'content.thin', 'cat' => 'Контент', 'sev' => 'low', 'scope' => 'page',
				'title' => 'Объём текста достаточен для раскрытия темы (ориентир от 300 слов)',
				'hint' => 'Тонкие страницы плохо ранжируются. Добавьте пользу: детали, цены, сроки, ответы на вопросы.',
				'check' => static function (Ctx $c): array {
					$out = [];
					foreach ($c->pages() as $url => $p) {
						if (str_contains(strtolower((string)$p['html']['robotsMeta']), 'noindex')) {
							continue;   // служебные (поиск) — тонкий контент там норма
						}
						$w = (int)$p['html']['words'];
						if ($w > 0 && $w < 300) {
							$out[] = ['url' => $url, 'detail' => $w . ' слов'];
						}
					}
					return $out;
				},
			],
			[
				'id' => 'content.php_errors', 'cat' => 'Контент', 'sev' => 'high', 'scope' => 'page',
				'title' => 'В выводе нет PHP-warning/error',
				'hint' => 'Технический текст в HTML виден пользователю и портит вёрстку. Включите display_errors=Off и чините код.',
				'check' => static function (Ctx $c): array {
					$out = [];
					foreach ($c->pages() as $url => $p) {
						foreach ($p['html']['phpErrors'] as $err) {
							$out[] = ['url' => $url, 'detail' => $err];
						}
					}
					return $out;
				},
			],
			[
				'id' => 'content.text_ratio', 'cat' => 'Контент', 'sev' => 'low', 'scope' => 'page',
				'title' => 'Доля текста в HTML не ниже 5%',
				'hint' => 'Страница, почти целиком состоящая из разметки и скриптов, воспринимается как пустая. Вынесите инлайн-стили и скрипты в файлы.',
				'check' => static function (Ctx $c): array {
					$out = [];
					foreach ($c->pages() as $url => $p) {
						$bytes = (int)$p['html']['htmlBytes'];
						$text = (int)$p['html']['textLen'];
						if ($bytes > 10000 && $text / $bytes < 0.05) {
							$out[] = ['url' => $url, 'detail' => round($text / $bytes * 100, 1) . '% текста (' . round($bytes / 1024) . ' КБ HTML)'];
						}
					}
					return $out;
				},
			],
			[
				'id' => 'content.encoding_ok', 'cat' => 'Контент', 'sev' => 'low', 'scope' => 'site',
				'title' => 'Верная кодировка в заголовке ответа',
				'hint' => 'Content-Type без charset или с windows-1251 ломает вывод кириллицы у части клиентов и краулеров.',
				'check' => static function (Ctx $c): array {
					$out = [];
					foreach ($c->pages() as $url => $p) {
						$ct = strtolower((string)$p['contentType']);
						if ($ct === '' || !str_contains($ct, 'html')) {
							continue;
						}
						if (!str_contains($ct, 'charset=')) {
							$out[] = ['url' => $url, 'detail' => 'Content-Type: ' . $ct];
						} elseif (!str_contains($ct, 'utf-8') && !str_contains($ct, 'utf8')) {
							$out[] = ['url' => $url, 'detail' => 'Content-Type: ' . $ct];
						}
					}
					return $out;
				},
			],
			[
				'id' => 'content.robots_meta_conflict', 'cat' => 'Контент', 'sev' => 'low', 'scope' => 'page',
				'title' => 'meta robots и X-Robots-Tag не противоречат друг другу',
				'hint' => 'Конфликт robots-сигналов (meta против заголовка) приводит к непредсказуемой индексации.',
				'check' => static function (Ctx $c): array {
					$out = [];
					foreach ($c->pages() as $url => $p) {
						$meta = strtolower((string)$p['html']['robotsMeta']);
						$head = strtolower((string)($p['headers']['x-robots-tag'] ?? ''));
						if ($meta === '' || $head === '') {
							continue;
						}
						$mi = str_contains($meta, 'noindex');
						$hi = str_contains($head, 'noindex');
						if ($mi !== $hi) {
							$out[] = ['url' => $url, 'detail' => 'meta: ' . $meta . ' | заголовок: ' . $head];
						}
					}
					return $out;
				},
			],
		];
	}

	// =====================================================================
	// 6. ССЫЛКИ
	// =====================================================================
	private static function rulesLinks(): array
	{
		$generic = ['нажмите', 'здесь', 'подробнее', 'читать', 'больше', 'далее', 'ссылка', 'клик', 'more', 'here', 'read more', 'learn more', 'click', 'link', 'подробнее>>', 'смотреть'];
		return [
			[
				'id' => 'links.broken_internal', 'cat' => 'Ссылки', 'sev' => 'critical', 'scope' => 'page',
				'title' => 'Внутренние ссылки ведут на существующие страницы',
				'hint' => 'В отчёте указано, на каких страницах стоит ссылка. В каждой из них: замените href на существующий адрес, снимите ссылку (если блок не нужен) или поставьте 301-редирект со старого адреса на новый. Порядок работ: сначала внутренние битые, затем внешние.',
				'check' => static function (Ctx $c): array {
					$out = [];
					foreach ($c->cr->inlinks as $url => $info) {
						$status = $c->statusOf($url);
						if ($status === null) {
							continue;
						}
						// 405/429/501/503 = сервер не поддерживает HEAD или временный лимит:
						// это не «битая» ссылка
						if (in_array($status, [405, 429, 501, 503], true)) {
							continue;
						}
						if ($status >= 400 || $status === 0) {
							$found = $c->foundOn($url, 5);
							$where = [];
							foreach ($found as $f) {
								$where[] = Ctx::pathKey($f['from']) . ' — «' . mb_substr($f['anchor'] !== '' ? $f['anchor'] : 'без текста', 0, 40) . '» (' . $f['zone'] . ')';
							}
							$out[] = [
								'url'    => $url,
								'detail' => 'HTTP ' . ($status ?: 'нет ответа') . ' · ссылок на этот адрес: ' . count($info['from'])
									. ($where !== [] ? ' → ' . implode('; ', $where) : ''),
								'found'  => $found,
							];
						}
					}
					return $out;
				},
			],
			[
				'id' => 'links.redirect_internal', 'cat' => 'Ссылки', 'sev' => 'medium', 'scope' => 'page',
				'title' => 'Внутренние ссылки не ведут на редиректы',
				'hint' => 'В отчёте указано, на каких страницах стоит ссылка. Замените href в этих местах на конечный адрес страницы (без цепочки редиректов).',
				'check' => static function (Ctx $c): array {
					$out = [];
					foreach ($c->cr->inlinks as $url => $info) {
						$page = $c->cr->pages[$url] ?? null;
						if ($page === null || empty($page['redirects'])) {
							continue;
						}
						$found = $c->foundOn($url, 5);
						$where = [];
						foreach ($found as $f) {
							$where[] = Ctx::pathKey($f['from']) . ' — «' . mb_substr($f['anchor'] !== '' ? $f['anchor'] : 'без текста', 0, 40) . '» (' . $f['zone'] . ')';
						}
						$out[] = [
							'url'    => $url,
							'detail' => count($page['redirects']) . ' редирект(ов) · ссылок: ' . count($info['from'])
								. ($where !== [] ? ' → ' . implode('; ', $where) : ''),
							'found'  => $found,
						];
					}
					return $out;
				},
			],
			[
				'id' => 'links.broken_external', 'cat' => 'Ссылки', 'sev' => 'high', 'scope' => 'page',
				'title' => 'Внешние ссылки работают',
				'hint' => 'В отчёте указано, с какой страницы ведёт битая внешняя ссылка. Замените её на рабочий адрес или снимите — мёртвые ссылки подрывают доверие к ресурсу.',
				'check' => static function (Ctx $c): array {
					$out = [];
					$blocked = [];
					foreach ($c->cr->externalStatus as $url => $info) {
						$status = (int)$info['status'];
						if ($status === 0 || $status >= 400) {
							// 403/405/418/429/503 — обычно анти-бот защита, а не битая ссылка
							if (in_array($status, [403, 405, 418, 429, 503], true)) {
								$blocked[] = $url . ' (HTTP ' . $status . ')';
								continue;
							}
							$found = $c->foundOn($url, 4);
							$where = [];
							foreach ($found as $f) {
								$where[] = Ctx::pathKey($f['from']) . ' — «' . mb_substr($f['anchor'] !== '' ? $f['anchor'] : 'без текста', 0, 40) . '» (' . $f['zone'] . ')';
							}
							$out[] = [
								'url'    => $url,
								'detail' => 'HTTP ' . ($status ?: ($info['error'] ?? 'нет ответа'))
									. ($where !== [] ? ' → найдено на: ' . implode('; ', $where) : ''),
								'found'  => $found,
							];
						}
					}
					if ($blocked) {
						$out[] = ['url' => null, 'detail' => 'Проверка невозможна (защита от ботов): ' . implode(', ', array_slice($blocked, 0, 5))];
					}
					return $out;
				},
			],
			[
				'id' => 'links.nofollow_internal', 'cat' => 'Ссылки', 'sev' => 'medium', 'scope' => 'page',
				'title' => 'Внутренние ссылки без rel=nofollow',
				'hint' => 'nofollow на внутренней ссылке обрывает передачу веса и передаёт сигнал «не важно».',
				'check' => static function (Ctx $c): array {
					$out = [];
					foreach ($c->pages() as $url => $p) {
						$bad = 0;
						foreach ($p['html']['links'] as $l) {
							if (!str_contains($l['rel'], 'nofollow')) {
								continue;
							}
							$abs = Url::resolve($l['raw'], $url);
							if ($abs !== null && $c->isInternal($abs)) {
								$bad++;
							}
						}
						if ($bad > 0) {
							$out[] = ['url' => $url, 'detail' => $bad . ' внутренних ссылок с nofollow'];
						}
					}
					return $out;
				},
			],
			[
				'id' => 'links.empty_anchor', 'cat' => 'Ссылки', 'sev' => 'medium', 'scope' => 'page',
				'title' => 'У ссылок есть текст (анкор)',
				'hint' => 'В отчёте указана зона страницы, где стоят ссылки без текста. Добавьте в эти места анкор (текст ссылки) или aria-label для иконок — иначе ссылка ничего не сообщает ни роботу, ни пользователю.',
				'check' => static function (Ctx $c): array {
					$out = [];
					foreach ($c->pages() as $url => $p) {
						$empty = 0;
						$zones = [];
						foreach ($p['html']['links'] as $l) {
							if (!empty($l['hidden'])) {
								continue;   // декоративная ссылка (aria-hidden) — не ошибка
							}
							if (trim((string)$l['text']) === '' && trim((string)$l['title']) === '') {
								$empty++;
								$zones[$l['zone']] = ($zones[$l['zone']] ?? 0) + 1;
							}
						}
						if ($empty > 0) {
							$where = [];
							foreach ($zones as $z => $n) {
								$where[] = $z . ' × ' . $n;
							}
							$out[] = ['url' => $url, 'detail' => $empty . ' ссылок без анкора (' . implode(', ', $where) . ')'];
						}
					}
					return $out;
				},
			],
			[
				'id' => 'links.generic_anchor', 'cat' => 'Ссылки', 'sev' => 'low', 'scope' => 'page',
				'title' => 'Нет шаблонных анкоров («нажмите здесь», «подробнее»)',
				'hint' => 'Описывающий анкор даёт поисковику тему ссылки. «Нажмите сюда» — нет.',
				'check' => static function (Ctx $c) use ($generic): array {
					$out = [];
					foreach ($c->pages() as $url => $p) {
						$bad = [];
						foreach ($p['html']['links'] as $l) {
							$t = mb_strtolower(trim((string)$l['text']));
							if ($t === '' || mb_strlen($t) > 30) {
								continue;
							}
							foreach ($generic as $g) {
								// слово целиком, а не подстр��да («рассчитать» ≠ «читать»)
								if (preg_match('~(?<![\p{L}\p{N}])' . preg_quote($g, '~') . '(?![\p{L}\p{N}])~iu', $t)) {
									$bad[] = $t;
									break;
								}
							}
						}
						if ($bad) {
							$out[] = ['url' => $url, 'detail' => count($bad) . ' шт.: ' . implode(', ', array_slice(array_unique($bad), 0, 5))];
						}
					}
					return $out;
				},
			],
			[
				'id' => 'links.broken_fragment', 'cat' => 'Ссылки', 'sev' => 'low', 'scope' => 'page',
				'title' => 'Якоря (#id) существуют на целевой странице',
				'hint' => 'Битый якор ведёт в никуда: пользователь не находит нужный блок, поведенческие факторы ухудшаются.',
				'check' => static function (Ctx $c): array {
					$out = [];
					foreach ($c->pages() as $url => $p) {
						$broken = [];
						foreach ($p['html']['links'] as $l) {
							$raw = $l['raw'];
							if (!str_contains($raw, '#')) {
								continue;
							}
							$abs = Url::resolve($raw, $url);
							if ($abs === null) {
								continue;
							}
							[$abs, $frag] = Url::splitFragment($abs);
							if ($frag === null || $frag === '') {
								continue;
							}
							$target = null;
							foreach ($c->cr->pages as $u2 => $p2) {
								if (Ctx::pathKey($u2) === Ctx::pathKey($abs)) {
									$target = $p2;
									break;
								}
							}
							if ($target === null) {
								continue;
							}
							$ids = $target['html']['idList'] ?? [];
							if ($ids && !in_array($frag, $ids, true)) {
								$broken[] = '#' . $frag . ' → ' . Ctx::pathKey($abs) . ' («' . mb_substr($l['text'] !== '' ? $l['text'] : 'без текста', 0, 30) . '», ' . $l['zone'] . ')';
							}
						}
						if ($broken) {
							$out[] = ['url' => $url, 'detail' => count($broken) . ' шт. на этой странице: ' . implode('; ', array_slice($broken, 0, 5))];
						}
					}
					return $out;
				},
			],
			[
				'id' => 'links.to_noindex', 'cat' => 'Ссылки', 'sev' => 'info', 'scope' => 'site',
				'title' => 'Куда ведут ссылки с индексируемых страниц (noindex-цели)',
				'hint' => 'Вес на noindex-страницу не передаётся. Обычно это футер (политика/согласие) — если так, добавьте rel="nofollow", чтобы не тратить краулинговый бюджет.',
				'check' => static function (Ctx $c): array {
					$out = [];
					$targets = [];
					foreach ($c->pages() as $url => $p) {
						if (str_contains(strtolower((string)$p['html']['robotsMeta']), 'noindex')) {
							continue;
						}
						foreach ($p['html']['links'] as $l) {
							$abs = Url::resolve($l['raw'], $url);
							if ($abs === null) {
								continue;
							}
							[$abs] = Url::splitFragment($abs);
							if (!$c->isInternal($abs)) {
								continue;
							}
							$target = $c->cr->pages[$abs] ?? null;
							if ($target === null) {
								continue;
							}
							if (!str_contains(strtolower((string)$target['html']['robotsMeta']), 'noindex')) {
								continue;
							}
							$key = Ctx::pathKey($abs);
							$targets[$key] = ($targets[$key] ?? 0) + 1;
						}
					}
					arsort($targets);
					foreach ($targets as $path => $cnt) {
						$out[] = ['url' => null, 'detail' => $path . ' — ссылок: ' . $cnt];
					}
					return $out;
				},
			],
			[
				'id' => 'links.extensions', 'cat' => 'Ссылки', 'sev' => 'low', 'scope' => 'page',
				'title' => 'Внутренние ссылки ведут на ЧПУ-адреса',
				'hint' => 'Ссылки вида /page.php или /page.html отдают 404 на некоторых хостингах и ухудшают вид URL.',
				'check' => static function (Ctx $c): array {
					$out = [];
					foreach ($c->pages() as $url => $p) {
						$bad = [];
						foreach ($p['html']['links'] as $l) {
							$abs = Url::resolve($l['raw'], $url);
							if ($abs === null) {
								continue;
							}
							[$abs] = Url::splitFragment($abs);
							if (!$c->isInternal($abs)) {
								continue;
							}
							$path = parse_url($abs, PHP_URL_PATH) ?: '';
							if (preg_match('~\.(php|html|htm)$~i', $path)) {
								$bad[] = $path;
							}
						}
						if ($bad) {
							$out[] = ['url' => $url, 'detail' => count($bad) . ' шт.: ' . implode(', ', array_slice(array_unique($bad), 0, 5))];
						}
					}
					return $out;
				},
			],
			[
				'id' => 'links.many_links', 'cat' => 'Ссылки', 'sev' => 'low', 'scope' => 'page',
				'title' => 'Не более 300 ссылок на странице',
				'hint' => 'Огромное количество ссылок размывает внутренний вес и усложняет обход.',
				'check' => static function (Ctx $c): array {
					$out = [];
					foreach ($c->pages() as $url => $p) {
						$n = count($p['html']['links']);
						if ($n > 300) {
							$out[] = ['url' => $url, 'detail' => $n . ' ссылок'];
						}
					}
					return $out;
				},
			],
			[
				'id' => 'links.js_navigation', 'cat' => 'Ссылки', 'sev' => 'info', 'scope' => 'page',
				'title' => 'Навигация реализована обычными ссылками',
				'hint' => 'Ссылки, которые появляются только после JS, краулер может не увидеть. Продублируйте их в <a href>.',
				'check' => static function (Ctx $c): array {
					$out = [];
					foreach ($c->pages() as $url => $p) {
						$h = $p['html'];
						if (($h['navCount'] ?? 0) > 0 && ($h['navLinks'] ?? 0) === 0) {
							$out[] = ['url' => $url, 'detail' => $h['navCount'] . ' блоков <nav> без обычных ссылок'];
						}
					}
					return $out;
				},
			],
			[
				'id' => 'links.ambiguous_anchor', 'cat' => 'Ссылки', 'sev' => 'low', 'scope' => 'page',
				'title' => 'Одинаковый анкор на странице ведёт на разные адреса',
				'hint' => 'Одинаковые анкоры на разные цели размывают сигнал и путают пользователя.',
				'check' => static function (Ctx $c): array {
					$out = [];
					foreach ($c->pages() as $url => $p) {
						$map = [];
						foreach ($p['html']['links'] as $l) {
							$t = trim((string)$l['text']);
							if ($t === '') {
								continue;
							}
							$abs = Url::resolve($l['raw'], $url);
							if ($abs === null) {
								continue;
							}
							[$abs] = Url::splitFragment($abs);
							$map[mb_strtolower($t)][$abs] = true;
						}
						$bad = [];
						foreach ($map as $t => $targets) {
							if (count($targets) > 1) {
								$bad[] = '"' . $t . '" → ' . count($targets) . ' адресов';
							}
						}
						if ($bad) {
							$out[] = ['url' => $url, 'detail' => count($bad) . ' шт.: ' . implode(', ', array_slice($bad, 0, 4))];
						}
					}
					return $out;
				},
			],
		];
	}

	// =====================================================================
	// 7. ИЗОБРАЖЕНИЯ
	// =====================================================================
	private static function rulesImages(): array
	{
		return [
			[
				'id' => 'img.missing_alt', 'cat' => 'Изображения', 'sev' => 'high', 'scope' => 'page',
				'title' => 'У всех изображений заполнен alt',
				'hint' => 'alt нужен для доступности и для поиска по картинкам. Для декоративных — alt="".',
				'check' => static function (Ctx $c): array {
					$out = [];
					foreach ($c->pages() as $url => $p) {
						$bad = [];
						foreach ($p['html']['images'] as $im) {
							if ($im['alt'] === null) {
								$bad[] = basename((string)$im['src']) . ' (' . $im['zone'] . ')';
							}
						}
						if ($bad) {
							$out[] = ['url' => $url, 'detail' => $bad . ' — без атрибута alt (' . count($bad) . ' из ' . count($p['html']['images']) . ' изображений)'];
						}
					}
					return $out;
				},
			],
			[
				'id' => 'img.empty_alt', 'cat' => 'Изображения', 'sev' => 'low', 'scope' => 'page',
				'title' => 'alt="" только у декоративных изображений',
				'hint' => 'Пустой alt у контентного изображения «прячет» его от поиска по картинкам.',
				'check' => static function (Ctx $c): array {
					$out = [];
					foreach ($c->pages() as $url => $p) {
						$bad = [];
						foreach ($p['html']['images'] as $im) {
							if ($im['alt'] === '' && ($im['w'] ?? 0) >= 120) {
								$bad[] = $im['src'];
							}
						}
						if ($bad) {
							$out[] = ['url' => $url, 'detail' => count($bad) . ' шт.: ' . implode(', ', array_slice(array_unique($bad), 0, 4))];
						}
					}
					return $out;
				},
			],
			[
				'id' => 'img.no_dimensions', 'cat' => 'Изображения', 'sev' => 'medium', 'scope' => 'page',
				'title' => 'У изображений заданы width и height (защита от CLS)',
				'hint' => 'Без размеров браузер «прыгает» при загрузке — растёт CLS и падает LCP.',
				'check' => static function (Ctx $c): array {
					$out = [];
					foreach ($c->pages() as $url => $p) {
						$bad = 0;
						foreach ($p['html']['images'] as $im) {
							if ($im['w'] === null || $im['h'] === null) {
								$bad++;
							}
						}
						if ($bad > 0) {
							$out[] = ['url' => $url, 'detail' => $bad . ' из ' . count($p['html']['images']) . ' без width/height'];
						}
					}
					return $out;
				},
			],
			[
				'id' => 'img.lazy_loading', 'cat' => 'Изображения', 'sev' => 'low', 'scope' => 'page',
				'title' => 'Изображения ниже первого экрана подгружаются лениво',
				'hint' => 'loading="lazy" на картинках ниже fold ускоряет отрисовку первого экрана.',
				'check' => static function (Ctx $c): array {
					$out = [];
					foreach ($c->pages() as $url => $p) {
						$h = $p['html'];
						$total = count($h['images']);
						if ($total < 4) {
							continue;
						}
						$eager = [];
						for ($i = 2; $i < $total; $i++) {
							$im = $h['images'][$i];
							if ($im['loading'] !== 'lazy' && $im['fetchprio'] !== 'high') {
								$eager[] = basename((string)$im['src']) . ' (' . $im['zone'] . ')';
							}
						}
						if ($eager) {
							$out[] = ['url' => $url, 'detail' => count($eager) . ' из ' . ($total - 2) . ' без loading="lazy": ' . implode('; ', array_slice($eager, 0, 4))];
						}
					}
					return $out;
				},
			],
			[
				'id' => 'img.broken', 'cat' => 'Изображения', 'sev' => 'high', 'scope' => 'page',
				'title' => 'Изображения загружаются (нет битых картинок)',
				'hint' => 'В отчёте указано, на какой странице и в какой зоне стоит битая картинка. Либо загрузите файл по этому пути, либо исправьте путь в src на существующий.',
				'check' => static function (Ctx $c): array {
					$out = [];
					foreach ($c->pages() as $url => $p) {
						$bad = [];
						foreach ($p['html']['images'] as $im) {
							if (trim((string)$im['src']) === '') {
								continue;
							}
							$abs = Url::resolve($im['src'], $url);
							if ($abs === null) {
								continue;
							}
							$status = $c->statusOf($abs);
							if ($status !== null && ($status >= 400 || $status === 0)) {
								$bad[] = basename((string)$im['src']) . ' (' . $im['zone'] . ') → HTTP ' . $status;
							}
						}
						if ($bad) {
							$out[] = ['url' => $url, 'detail' => count($bad) . ' шт.: ' . implode('; ', array_slice(array_unique($bad), 0, 4))];
						}
					}
					return $out;
				},
			],
			[
				'id' => 'img.lcp', 'cat' => 'Изображения', 'sev' => 'info', 'scope' => 'page',
				'title' => 'Главное изображение страницы (LCP) оптимизировано',
				'hint' => 'LCP-картинка (первая крупная на экране) должна иметь fetchpriority="high", размеры width/height и modern-формат — иначе она тормозит первую отрисовку.',
				'check' => static function (Ctx $c): array {
					$out = [];
					foreach ($c->pages() as $url => $p) {
						$imgs = $p['html']['images'];
						if (!$imgs) {
							continue;
						}
						$lcp = null;
						foreach ($imgs as $im) {
							if (($im['w'] ?? 0) >= 300) {
								$lcp = $im;
								break;
							}
						}
						$lcp ??= $imgs[0];
						$miss = [];
						if ($lcp['fetchprio'] !== 'high') {
							$miss[] = 'fetchpriority=high';
						}
						if ($lcp['w'] === null || $lcp['h'] === null) {
							$miss[] = 'width/height';
						}
						if (!$lcp['srcset']) {
							$miss[] = 'srcset';
						}
						if ($miss) {
							$out[] = ['url' => $url, 'detail' => 'LCP-картинка ' . basename((string)$lcp['src']) . ': нет ' . implode(', ', $miss)];
						}
					}
					return $out;
				},
			],
			[
				'id' => 'img.oversize', 'cat' => 'Изображения', 'sev' => 'info', 'scope' => 'site',
				'title' => 'Нет тяжёлых изображений (от 200 КБ)',
				'hint' => 'Большие картинки тормозят мобильную загрузку. Сжимайте, переводите в WebP/AVIF, задавайте srcset.',
				'check' => static function (Ctx $c): array {
					$out = [];
					foreach ($c->cr->assetStatus as $url => $info) {
						$len = (int)($info['length'] ?? 0);
						if ($len >= 200 * 1024) {
							$out[] = ['url' => $url, 'detail' => round($len / 1024) . ' КБ'];
						}
					}
					return $out;
				},
			],
		];
	}

	// =====================================================================
	// 8. СКОРОСТЬ
	// =====================================================================
	private static function rulesPerformance(): array
	{
		return [
			[
				'id' => 'perf.ttfb', 'cat' => 'Скорость', 'sev' => 'medium', 'scope' => 'page',
				'title' => 'TTFB не превышает 500 мс',
				'hint' => 'Медленный TTFB напрямую упирается в Core Web Vitals. Используйте кэш страниц (ETag/Cache-Control) и OPcache.',
				'check' => static function (Ctx $c): array {
					$out = [];
					foreach ($c->pages() as $url => $p) {
						$t = (float)$p['ttfb'];
						if ($t > 0.5) {
							$out[] = ['url' => $url, 'detail' => round($t * 1000) . ' мс'];
						}
					}
					return $out;
				},
			],
			[
				'id' => 'perf.page_size', 'cat' => 'Скорость', 'sev' => 'medium', 'scope' => 'page',
				'title' => 'Размер HTML не превышает 300 КБ',
				'hint' => 'Огромный HTML замедляет парсинг. Вынесите лишнее в JSON/SSI и уберите inline-дубли.',
				'check' => static function (Ctx $c): array {
					$out = [];
					foreach ($c->pages() as $url => $p) {
						$size = (int)$p['bodySize'];
						if ($size > 300 * 1024) {
							$out[] = ['url' => $url, 'detail' => round($size / 1024) . ' КБ'];
						}
					}
					return $out;
				},
			],
			[
				'id' => 'perf.compression', 'cat' => 'Скорость', 'sev' => 'medium', 'scope' => 'site',
				'title' => 'HTML отдаётся в сжатом виде (gzip/brotli)',
				'hint' => 'Без сжатия HTML весит в 4–5 раз больше. Включите mod_deflate или Brotli на уровне сервера.',
				'check' => static function (Ctx $c): array {
					$out = [];
					$without = [];
					foreach ($c->pages() as $url => $p) {
						$enc = strtolower((string)($p['headers']['content-encoding'] ?? ''));
						if ($enc === '' && (int)$p['bodySize'] > 2000) {
							$without[] = Ctx::pathKey($url);
						}
					}
					if ($without) {
						$out[] = ['url' => null, 'detail' => count($without) . ' страниц без Content-Encoding (примеры: ' . implode(', ', array_slice($without, 0, 5)) . ')'];
					}
					return $out;
				},
			],
			[
				'id' => 'perf.cache_control', 'cat' => 'Скорость', 'sev' => 'medium', 'scope' => 'site',
				'title' => 'Заданы Cache-Control / ETag',
				'hint' => 'Без Cache-Control и ETag каждый визит — полная перерисовка страницы и нагрузка на сервер.',
				'check' => static function (Ctx $c): array {
					$out = [];
					$noCache = [];
					$noEtag = 0;
					foreach ($c->pages() as $url => $p) {
						$cc = (string)($p['headers']['cache-control'] ?? '');
						if ($cc === '') {
							$noCache[] = Ctx::pathKey($url);
						}
						if (empty($p['headers']['etag']) && empty($p['headers']['last-modified'])) {
							$noEtag++;
						}
					}
					if ($noCache) {
						$out[] = ['url' => null, 'detail' => count($noCache) . ' страниц без Cache-Control (примеры: ' . implode(', ', array_slice($noCache, 0, 5)) . ')'];
					}
					if ($noEtag > 0) {
						$out[] = ['url' => null, 'detail' => $noEtag . ' страниц без ETag/Last-Modified'];
					}
					return $out;
				},
			],
			[
				'id' => 'perf.blocking', 'cat' => 'Скорость', 'sev' => 'low', 'scope' => 'page',
				'title' => 'Не более 4 блокирующих ресурсов в <head>',
				'hint' => 'Каждый блокирующий CSS/JS задерживает первую отрисовку. Критичные — инлайн, остальные — async/defer.',
				'check' => static function (Ctx $c): array {
					$out = [];
					foreach ($c->pages() as $url => $p) {
						$css = (int)$p['html']['blocking'];
						$js = (int)($p['html']['blockingJs'] ?? 0);
						$n = $css + $js;
						if ($n > 4) {
							$out[] = ['url' => $url, 'detail' => $n . ' блокирующих ресурсов (CSS: ' . $css . ', JS: ' . $js . ')'];
						}
					}
					return $out;
				},
			],
			[
				'id' => 'perf.dom_size', 'cat' => 'Скорость', 'sev' => 'low', 'scope' => 'page',
				'title' => 'DOM не перегружен (до 1500 узлов)',
				'hint' => 'Тяжёлый DOM замедляет парсинг и стилизацию, особенно на слабых телефонах.',
				'check' => static function (Ctx $c): array {
					$out = [];
					foreach ($c->pages() as $url => $p) {
						$n = (int)($p['html']['nodes'] ?? 0);
						if ($n > 1500) {
							$out[] = ['url' => $url, 'detail' => $n . ' узлов DOM'];
						}
					}
					return $out;
				},
			],
			[
				'id' => 'perf.http_version', 'cat' => 'Скорость', 'sev' => 'info', 'scope' => 'site',
				'title' => 'Используется HTTP/1.1 или новее',
				'hint' => 'HTTP/1.0 без keep-alive = отдельное TCP-соединение на каждый ресурс.',
				'check' => static function (Ctx $c): array {
					$out = [];
					$old = 0;
					foreach ($c->pages() as $url => $p) {
						$v = (string)$p['httpVersion'];
						if ($v !== '' && (int)$v < 2) {
							$old++;
						}
					}
					if ($old > 0) {
						$out[] = ['url' => null, 'detail' => $old . ' страниц отдано по HTTP/1.x без HTTP/2'];
					}
					return $out;
				},
			],
			[
				'id' => 'perf.assets_weight', 'cat' => 'Скорость', 'sev' => 'info', 'scope' => 'site',
				'title' => 'Суммарный вес ресурсов в норме',
				'hint' => 'Большой вес статики = медленный первый экран. Считается по Content-Length найденных ассетов.',
				'check' => static function (Ctx $c): array {
					$out = [];
					$sum = 0;
					$count = 0;
					foreach ($c->cr->assetStatus as $url => $info) {
						$len = (int)($info['length'] ?? 0);
						if ($len > 0) {
							$sum += $len;
							$count++;
						}
					}
					if ($count > 0 && $sum > 5 * 1024 * 1024) {
						$out[] = ['url' => null, 'detail' => 'Суммарно ' . round($sum / 1024 / 1024, 1) . ' МБ на ' . $count . ' ресурсов'];
					}
					return $out;
				},
			],
		];
	}

	// =====================================================================
	// 9. БЕЗОПАСНОСТЬ
	// =====================================================================
	private static function rulesSecurity(): array
	{
		return [
			[
				'id' => 'sec.https', 'cat' => 'Безопасность', 'sev' => 'high', 'scope' => 'site',
				'title' => 'Сайт работает по HTTPS',
				'hint' => 'Без HTTPS браузер предупреждает посетителя, а поисковик понижает доверие к домену.',
				'check' => static function (Ctx $c): array {
					$out = [];
					if (!str_starts_with($c->origin(), 'https://') && !$c->isLocal()) {
						$out[] = ['url' => null, 'detail' => 'Основной адрес без HTTPS: ' . $c->origin()];
					}
					return $out;
				},
			],
			[
				'id' => 'sec.hsts', 'cat' => 'Безопасность', 'sev' => 'medium', 'scope' => 'site',
				'title' => 'Отправляется Strict-Transport-Security',
				'hint' => 'HSTS запрещает браузеру уйти в незашифрованный HTTP. RFC 6797: max-age от 31536000 (1 год); OWASP рекомендует 63072000 (2 года) с includeSubDomains и preload.',
				'check' => static function (Ctx $c): array {
					$out = [];
					if (!str_starts_with($c->origin(), 'https://')) {
						return $out;
					}
					$missing = [];
					foreach ($c->pages() as $url => $p) {
						if (empty($p['headers']['strict-transport-security'])) {
							$missing[] = Ctx::pathKey($url);
						}
					}
					if ($missing) {
						$out[] = ['url' => null, 'detail' => 'Заголовок отсутствует на ' . count($missing) . ' из ' . count($c->pages()) . ' страниц (например: ' . implode(', ', array_slice($missing, 0, 4)) . ')'];
					}
					return $out;
				},
			],
			[
				'id' => 'sec.csp', 'cat' => 'Безопасность', 'sev' => 'low', 'scope' => 'site',
				'title' => 'Настроена Content-Security-Policy',
				'hint' => 'CSP — главная защита от XSS. Начните с report-only и постепенно ужесточайте.',
				'check' => static function (Ctx $c): array {
					$out = [];
					$missing = [];
					foreach ($c->pages() as $url => $p) {
						if (empty($p['headers']['content-security-policy'])) {
							$missing[] = Ctx::pathKey($url);
						}
					}
					if ($missing) {
						$out[] = ['url' => null, 'detail' => 'Заголовок отсутствует на ' . count($missing) . ' из ' . count($c->pages()) . ' страниц'];
					}
					return $out;
				},
			],
			[
				'id' => 'sec.xfo', 'cat' => 'Безопасность', 'sev' => 'medium', 'scope' => 'site',
				'title' => 'Установлен X-Frame-Options (или frame-ancestors в CSP)',
				'hint' => 'Без защиты от iframe сайт можно встроить на чужую страницу (clickjacking).',
				'check' => static function (Ctx $c): array {
					$out = [];
					$missing = [];
					foreach ($c->pages() as $url => $p) {
						$xfo = (string)($p['headers']['x-frame-options'] ?? '');
						$csp = strtolower((string)($p['headers']['content-security-policy'] ?? ''));
						if ($xfo === '' && !str_contains($csp, 'frame-ancestors')) {
							$missing[] = Ctx::pathKey($url);
						}
					}
					if ($missing) {
						$out[] = ['url' => null, 'detail' => 'Нет X-Frame-Options и frame-ancestors на ' . count($missing) . ' из ' . count($c->pages()) . ' страниц'];
					}
					return $out;
				},
			],
			[
				'id' => 'sec.nosniff', 'cat' => 'Безопасность', 'sev' => 'medium', 'scope' => 'site',
				'title' => 'Установлен X-Content-Type-Options: nosniff',
				'hint' => 'nosniff запрещает браузеру угадывать тип файла — базовая защита от MIME-sniffing.',
				'check' => static function (Ctx $c): array {
					$out = [];
					$missing = [];
					foreach ($c->pages() as $url => $p) {
						if (strtolower((string)($p['headers']['x-content-type-options'] ?? '')) !== 'nosniff') {
							$missing[] = Ctx::pathKey($url);
						}
					}
					if ($missing) {
						$out[] = ['url' => null, 'detail' => 'Заголовок отсутствует или не nosniff на ' . count($missing) . ' из ' . count($c->pages()) . ' страниц'];
					}
					return $out;
				},
			],
			[
				'id' => 'sec.referrer_policy', 'cat' => 'Безопасность', 'sev' => 'low', 'scope' => 'site',
				'title' => 'Задан Referrer-Policy',
				'hint' => 'Без политики полный URL (с utm и параметрами) утекает на сторонние сайты.',
				'check' => static function (Ctx $c): array {
					$out = [];
					$missing = [];
					foreach ($c->pages() as $url => $p) {
						if (trim((string)($p['headers']['referrer-policy'] ?? '')) === '') {
							$missing[] = Ctx::pathKey($url);
						}
					}
					if ($missing) {
						$out[] = ['url' => null, 'detail' => 'Заголовок отсутствует на ' . count($missing) . ' из ' . count($c->pages()) . ' страниц'];
					}
					return $out;
				},
			],
			[
				'id' => 'sec.permissions_policy', 'cat' => 'Безопасность', 'sev' => 'info', 'scope' => 'site',
				'title' => 'Задан Permissions-Policy',
				'hint' => 'Отключите неиспользуемые API (камера, микрофон, геолокация) для всех страниц.',
				'check' => static function (Ctx $c): array {
					$out = [];
					$missing = [];
					foreach ($c->pages() as $url => $p) {
						if (trim((string)($p['headers']['permissions-policy'] ?? '')) === '') {
							$missing[] = Ctx::pathKey($url);
						}
					}
					if ($missing) {
						$out[] = ['url' => null, 'detail' => 'Заголовок отсутствует на ' . count($missing) . ' из ' . count($c->pages()) . ' страниц'];
					}
					return $out;
				},
			],
			[
				'id' => 'sec.banners', 'cat' => 'Безопасность', 'sev' => 'low', 'scope' => 'site',
				'title' => 'Версии ПО не раскрываются в заголовках',
				'hint' => 'Server: nginx/1.24.7 и X-Powered-By упрощают подбор уязвимостей. Отключите или скройте.',
				'check' => static function (Ctx $c): array {
					$out = [];
					$found = [];
					foreach ($c->pages() as $url => $p) {
						foreach (['server' => 'Server', 'x-powered-by' => 'X-Powered-By'] as $k => $label) {
							$v = trim((string)($p['headers'][$k] ?? ''));
							if ($v !== '' && preg_match('~[\d]+\.[\d]+~', $v)) {
								$found[$label . ': ' . $v] = true;
							}
						}
					}
					foreach (array_keys($found) as $f) {
						$out[] = ['url' => null, 'detail' => $f];
					}
					return $out;
				},
			],
			[
				'id' => 'sec.sensitive_files', 'cat' => 'Безопасность', 'sev' => 'critical', 'scope' => 'site',
				'title' => 'Служебные файлы недоступны извне',
				'hint' => 'Критично: .env, .git и composer.json не должны отдаваться наружу. Закройте правилами сервера.',
				'check' => static function (Ctx $c): array {
					$out = [];
					foreach ($c->cr->probeStatus as $url => $info) {
						$status = (int)$info['status'];
						$path = parse_url($url, PHP_URL_PATH) ?: '/';
						if (str_ends_with($path, '/.well-known/security.txt')) {
							continue;
						}
						if ($status < 200 || $status >= 300) {
							continue;
						}
						// Если сервер отдал HTML-страницу (catch-all/404) — это не сам файл.
						if (!empty($info['isHtml'])) {
							continue;
						}
						$out[] = ['url' => $url, 'detail' => 'Файл отдаётся с кодом ' . $status
							. ' (тип: ' . ($info['ctype'] !== '' ? $info['ctype'] : 'неизвестен')
							. ', размер: ' . round((int)($info['len'] ?? 0) / 1024, 1) . ' КБ)'];
					}
					return $out;
				},
			],
			[
				'id' => 'sec.tls_expired', 'cat' => 'Безопасность', 'sev' => 'critical', 'scope' => 'site',
				'title' => 'TLS-сертификат действителен',
				'hint' => 'Просроченный сертификат = красный замок в браузере для всех посетителей. Продлите сертификат заранее (Let\'s Encrypt — автопродление).',
				'check' => static function (Ctx $c): array {
					$out = [];
					$tls = $c->cr->tls;
					if (!$tls) {
						return $out;
					}
					if (isset($tls['error'])) {
						$out[] = ['url' => null, 'detail' => (string)$tls['error']];
						return $out;
					}
					if (!empty($tls['expired'])) {
						$out[] = ['url' => null, 'detail' => 'Сертификат истёк ' . date('d.m.Y', (int)$tls['valid_to'])];
					} elseif ((int)($tls['days_left'] ?? 999) <= 30) {
						$out[] = ['url' => null, 'detail' => 'Истекает через ' . $tls['days_left'] . ' дн. (' . date('d.m.Y', (int)$tls['valid_to']) . ')'];
					}
					return $out;
				},
			],
			[
				'id' => 'sec.tls_quality', 'cat' => 'Безопасность', 'sev' => 'high', 'scope' => 'site',
				'title' => 'TLS-сертификат выдан на домен и использует надёжный алгоритм',
				'hint' => 'Сертификат на чужое имя или самоподписанный вызывает предупреждение браузера. Алгоритм SHA-1/MD5 считается небезопасным.',
				'check' => static function (Ctx $c): array {
					$out = [];
					$tls = $c->cr->tls;
					if (!$tls || isset($tls['error'])) {
						return $out;
					}
					if (empty($tls['host_ok'])) {
						$out[] = ['url' => null, 'detail' => 'Сертификат выдан на: ' . implode(', ', array_slice((array)$tls['names'], 0, 3))];
					}
					if (preg_match('~sha1|md5~i', (string)$tls['signature'])) {
						$out[] = ['url' => null, 'detail' => 'Слабый алгоритм подписи: ' . $tls['signature']];
					}
					if (!empty($tls['self_signed'])) {
						$out[] = ['url' => null, 'detail' => 'Самоподписанный сертификат'];
					}
					if ((int)($tls['key_size'] ?? 0) > 0 && (int)$tls['key_size'] < 2048) {
						$out[] = ['url' => null, 'detail' => 'Слабый ключ: ' . $tls['key_size'] . ' бит'];
					}
					return $out;
				},
			],
			[
				'id' => 'sec.cookies', 'cat' => 'Безопасность', 'sev' => 'medium', 'scope' => 'site',
				'title' => 'Cookies выставлены с Secure/HttpOnly/SameSite',
				'hint' => 'Без флагов cookie утекает в JS и перехватывается по HTTP.',
				'check' => static function (Ctx $c): array {
					$out = [];
					$problems = [];
					foreach ($c->pages() as $url => $p) {
						foreach ($p['headerLog'] as $line) {
							if (!stripos($line, 'set-cookie:')) {
								continue;
							}
							$cookie = trim(substr($line, strlen('set-cookie:')));
							$miss = [];
							if (!preg_match('~;\s*secure~i', $cookie)) {
								$miss[] = 'Secure';
							}
							if (!preg_match('~;\s*httponly~i', $cookie)) {
								$miss[] = 'HttpOnly';
							}
							if (!preg_match('~;\s*samesite~i', $cookie)) {
								$miss[] = 'SameSite';
							}
							if ($miss) {
								$problems[$cookie] = implode(', ', $miss);
							}
						}
					}
					foreach ($problems as $cookie => $miss) {
						$out[] = ['url' => null, 'detail' => 'нет флагов (' . $miss . '): ' . mb_substr($cookie, 0, 60)];
					}
					return $out;
				},
			],
		];
	}

	// =====================================================================
	// 10. ДОСТУПНОСТЬ
	// =====================================================================
	private static function rulesA11y(): array
	{
		return [
			[
				'id' => 'a11y.form_label', 'cat' => 'Доступность', 'sev' => 'medium', 'scope' => 'page',
				'title' => 'У полей форм есть подписи (label/aria-label)',
				'hint' => 'Откройте указанную страницу, найдите форму (в отчёте — количество полей) и добавьте к каждому полю <label for="id">Текст</label> либо aria-label. Поля без подписи не заполняются с клавиатуры и теряются для скринридеров.',
				'check' => static function (Ctx $c): array {
					$out = [];
					foreach ($c->pages() as $url => $p) {
						$n = (int)$p['html']['forms']['noLabel'];
						if ($n > 0) {
							$bad = $p['html']['forms']['bad'] ?? [];
							$names = [];
							foreach (array_slice($bad, 0, 4) as $b) {
								$names[] = $b['name'] . ($b['ph'] !== '' ? ' (placeholder: «' . mb_substr($b['ph'], 0, 20) . '»)' : '');
							}
							$action = $bad[0]['action'] ?? '';
							$out[] = ['url' => $url, 'detail' => $n . ' полей без подписи: ' . implode('; ', $names)
								. ($action !== '' ? ' · форма action=' . $action : '')];
						}
					}
					return $out;
				},
			],
			[
				'id' => 'a11y.field_name', 'cat' => 'Доступность', 'sev' => 'medium', 'scope' => 'page',
				'title' => 'У полей форм есть атрибут name',
				'hint' => 'Без name поле не отправляется на сервер и не сохраняется после перезагрузки.',
				'check' => static function (Ctx $c): array {
					$out = [];
					foreach ($c->pages() as $url => $p) {
						$n = (int)$p['html']['forms']['noName'];
						if ($n > 0) {
							$out[] = ['url' => $url, 'detail' => $n . ' полей без name'];
						}
					}
					return $out;
				},
			],
			[
				'id' => 'a11y.duplicate_ids', 'cat' => 'Доступность', 'sev' => 'medium', 'scope' => 'page',
				'title' => 'Идентификаторы (id) на странице уникальны',
				'hint' => 'Дубли id ломают якорные ссылки и скрипты, работающие с DOM.',
				'check' => static function (Ctx $c): array {
					$out = [];
					foreach ($c->pages() as $url => $p) {
						$dupes = $p['html']['ids']['dupes'] ?? [];
						if ($dupes) {
							$out[] = ['url' => $url, 'detail' => 'Повторяются: ' . implode(', ', array_slice($dupes, 0, 6))];
						}
					}
					return $out;
				},
			],
			[
				'id' => 'a11y.iframe_title', 'cat' => 'Доступность', 'sev' => 'low', 'scope' => 'page',
				'title' => 'У iframe есть title/aria-label',
				'hint' => 'Без названия iframe невозможно отличить в содержимом для скринридера.',
				'check' => static function (Ctx $c): array {
					$out = [];
					foreach ($c->pages() as $url => $p) {
						$n = (int)$p['html']['iframes']['noTitle'];
						if ($n > 0) {
							$out[] = ['url' => $url, 'detail' => $n . ' iframe без заголовка'];
						}
					}
					return $out;
				},
			],
			[
				'id' => 'a11y.landmark', 'cat' => 'Доступность', 'sev' => 'low', 'scope' => 'page',
				'title' => 'У страницы есть landmark-разметка <main>',
				'hint' => 'Landmarks позволяют скринридерам и поисковику понять структуру страницы. Добавьте <main> вокруг основного содержимого.',
				'check' => static function (Ctx $c): array {
					$out = [];
					foreach ($c->pages() as $url => $p) {
						if (empty($p['html']['hasMain'])) {
							$out[] = ['url' => $url, 'detail' => 'Нет тега <main>'];
						}
					}
					return $out;
				},
			],
			[
				'id' => 'a11y.skip_link', 'cat' => 'Доступность', 'sev' => 'low', 'scope' => 'site',
				'title' => 'Есть ссылка «перейти к содержимому»',
				'hint' => 'Skip-link позволяет клавиатурным пользователям не проходить длинное меню на каждой странице.',
				'check' => static function (Ctx $c): array {
					$out = [];
					foreach ($c->pages() as $url => $p) {
						if (!empty($p['html']['hasSkipLink'])) {
							continue;
						}
					}
					$with = 0;
					foreach ($c->pages() as $url => $p) {
						if (!empty($p['html']['hasSkipLink'])) {
							$with++;
						}
					}
					$total = count($c->pages());
					if ($total > 0 && $with === 0) {
						$out[] = ['url' => null, 'detail' => 'Skip-link не найден ни на одной странице'];
					}
					return $out;
				},
			],
			[
				'id' => 'a11y.button_label', 'cat' => 'Доступность', 'sev' => 'low', 'scope' => 'page',
				'title' => 'У кнопок и ссылок есть доступное имя',
				'hint' => 'Иконочная кнопка без подписи (текста, title, aria-label) недоступна для скринридера.',
				'check' => static function (Ctx $c): array {
					$out = [];
					foreach ($c->pages() as $url => $p) {
						$n = (int)($p['html']['namelessControls'] ?? 0);
						if ($n > 0) {
							$out[] = ['url' => $url, 'detail' => $n . ' элементов без доступного имени'];
						}
					}
					return $out;
				},
			],
		];
	}
}

/**
 * Аналитический слой: семантическое ядро страницы, тематические кластеры,
 * план продвижения конкретной страницы, сравнение с конкурентами и
 * модель готовности к топ-10. Всё опирается на реальные данные обхода:
 * собственный текст, заголовки, входящие анкоры, граф ссылок и правила аудита.
 */
final class Insight
{
	/** Служебные слова — не участвуют в семантическом ядре. */
	private const STOP = [
		'и', 'в', 'на', 'с', 'по', 'для', 'не', 'что', 'это', 'как', 'мы', 'вы', 'наш', 'ваш', 'их', 'его',
		'к', 'а', 'из', 'до', 'за', 'то', 'же', 'бы', 'при', 'более', 'всего', 'также', 'или', 'если', 'чтобы',
		'этот', 'эта', 'эти', 'того', 'тоже', 'там', 'тут', 'уже', 'еще', 'ещё', 'быть', 'есть', 'который',
		'которые', 'которое', 'свой', 'свои', 'своих', 'может', 'можно', 'нужно', 'надо', 'очень', 'более',
		'меня', 'тебя', 'него', 'нее', 'них', 'вам', 'нам', 'вас', 'них', 'том', 'тем', 'той', 'этом', 'той',
		'который', 'новый', 'старый', 'главный', 'первый', 'последний', 'другой', 'следующий', 'весь', 'всё',
		'свойство', 'страница', 'странице', 'сайт', 'сайта', 'раздел', 'услуги', 'услуга', 'компания', 'клиент',
		'заявка', 'заявки', 'форма', 'меню', 'footer', 'header', 'main', 'nav', 'div', 'span', 'http', 'https', 'www',
		'index', 'php', 'html', 'href', 'src', 'class', 'alt', 'title', 'meta', 'link', 'script', 'style',
	];

	/** Частотные слова сайта: термин → число страниц, где встречается. */
	public static function siteLexicon(Ctx $c): array
	{
		$lex = [];
		foreach ($c->pages() as $url => $p) {
			foreach (self::termsOf((string)($p['html']['mainText'] ?? $p['html']['text'] ?? '')) as $t) {
				$lex[$t] = ($lex[$t] ?? 0) + 1;
			}
		}
		arsort($lex);
		return $lex;
	}

	/**
	 * Разбор текста на термины: слова и устойчивые словосочетания (биграммы).
	 *
	 * @return array<string,int>
	 */
	public static function termsOf(string $text): array
	{
		$text = mb_strtolower($text);
		$words = preg_split('~[^\p{L}\p{N}]+~u', $text, -1, PREG_SPLIT_NO_EMPTY) ?: [];
		$terms = [];
		$prev = '';
		foreach ($words as $w) {
			// только слова с буквами: чистые числа («2025», «000») — не семантика
			if (mb_strlen($w) >= 3 && !in_array($w, self::STOP, true) && preg_match('~[\p{L}]~u', $w)) {
				$terms[$w] = ($terms[$w] ?? 0) + 1;
				if ($prev !== '') {
					$bg = $prev . ' ' . $w;
					$terms[$bg] = ($terms[$bg] ?? 0) + 1;
				}
				$prev = $w;
			} else {
				$prev = '';
			}
		}
		return $terms;
	}

	/**
	 * Аналитический разбор: ядро страницы, кластеры, конкуренты.
	 *
	 * @param array<string,mixed> $insight
	 */
	public static function renderInsightConsole(Ui $ui, array $insight, Ctx $c, Crawler $cr): void
	{

		// ---------- План продвижения страницы ----------
		if (!empty($insight['page'])) {
			$plan = $insight['page'];
			$core = $plan['core'];
			$ui->section('СЕМАНТИЧЕСКОЕ ЯДРО И ПЛАН ПРОДВИЖЕНИЯ · ' . Ctx::pathKey($plan['url']));

			$ui->line('  ' . $ui->c('Ядро страницы', 'bold|white')
				. '  ' . $ui->c('основной термин: ', 'gray') . $ui->c($core['primary'], 'bold|brcyan'));
			foreach (array_slice($core['support'], 0, 6) as $s) {
				$blocks = [];
				foreach ($s['blocks'] as $b => $n) {
					$blocks[] = $b . ' × ' . $n;
				}
				$ui->line('    ' . $ui->c('· ' . $s['term'], 'white') . $ui->c('  (' . implode(', ', array_slice($blocks, 0, 3)) . ')', 'gray'));
			}

			$ui->line();
			$ui->line('  ' . $ui->c('Покрытие ядра по блокам страницы', 'bold|white'));
			$ui->line('    ' . $ui->c(str_pad('термин', 30, ' ', STR_PAD_RIGHT) . 'title    h1        description  текст', 'gray'));
			foreach ($core['coverage'] as $t => $cov) {
				$mark = static fn(bool $ok): string => $ok ? $ui->c('✓', 'brgreen') : $ui->c('·', 'red');
				$ui->line('    ' . $ui->c($ui->pad($t, 30), 'white')
					. $mark((bool)$cov['title']) . '       ' . $mark((bool)$cov['h1']) . '        '
					. $mark((bool)$cov['description']) . '          ' . $mark((bool)($cov['текст'] ?? false)));
			}

			if (!empty($core['gap'])) {
				$ui->line();
				$ui->line('  ' . $ui->c('Пробелы: термины из тематического окружения, которых нет на странице', 'bold|white'));
				foreach ($core['gap'] as $g) {
					$ui->line('    ' . $ui->c('· ' . $g['term'], 'bryellow')
						. $ui->c('  встречается в анкорах/заголовках ссылающихся страниц: ' . implode(', ', array_slice($g['from'], 0, 2)), 'gray'));
				}
			}

			$ui->line();
			$ui->line('  ' . $ui->c('Черновики (собраны из реальных терминов страницы)', 'bold|white'));
			$ui->line('    ' . $ui->c('title      ', 'gray') . $ui->c($core['titleDraft'], 'white') . $ui->c('  (' . $core['titleLen'] . ' симв.)', 'gray'));
			$ui->line('    ' . $ui->c('description ', 'gray') . $ui->c(mb_substr($core['descDraft'], 0, 120), 'white') . $ui->c('  (' . $core['descLen'] . ' симв.)', 'gray'));
			$ui->line('    ' . $ui->c('текущий title: ', 'gray') . $core['currentTitle']);
			$ui->line('    ' . $ui->c('текущий desc : ', 'gray') . ($core['currentDesc'] !== '' ? mb_substr($core['currentDesc'], 0, 120) : $ui->c('— отсутствует —', 'red')));

			if (!empty($plan['diagnostics'])) {
				$ui->line();
				$ui->line('  ' . $ui->c('ПОЧЕМУ СТРАНИЦА МОЖЕТ НЕ РАСТИ', 'bold|brcyan'));
				foreach ($plan['diagnostics'] as $d) {
					$ui->line('    ' . $ui->c('• ' . $d['why'], 'bryellow'));
					$ui->line('        ' . $ui->c('факт: ', 'gray') . $d['fact']);
					$ui->line('        ' . $ui->c('действие: ', 'brgreen') . $d['fix']);
				}
			}

			if (!empty($plan['blockers'])) {
				$ui->line();
				$ui->line('  ' . $ui->c('Технические блокеры страницы', 'bold|brcyan'));
				foreach (array_slice($plan['blockers'], 0, 10) as $b) {
					$ui->line('    ' . $ui->sevBadge($b['sev']) . ' ' . $ui->c($b['rule'], 'white') . ' — ' . $b['detail']);
				}
			}

			if (!empty($plan['linkSources'])) {
				$ui->line();
				$ui->line('  ' . $ui->c('Откуда добавить ссылки (тематически близкие страницы, которые ещё не ссылаются)', 'bold|white'));
				foreach ($plan['linkSources'] as $ls) {
					$ui->line('    ' . $ui->c('· ' . $ls['url'], 'brcyan') . $ui->c('  — ' . mb_substr($ls['title'], 0, 50), 'gray'));
				}
			}

			$r = $plan['readiness'];
			$ui->line();
			$ui->line('  ' . $ui->c('ГОТОВНОСТЬ К ТОП-10', 'bold|white') . '  ' . $ui->c($r['ready'] . '%', 'bold|brcyan')
				. $ui->c('  (' . $r['tech'] . ' из ' . $r['techTotal'] . ' технических условий выполнено)', 'gray'));
			foreach ($r['items'] as $i) {
				$mark = $i['external'] ? $ui->c('◐', 'bryellow') : ($i['ok'] ? $ui->c('✓', 'brgreen') : $ui->c('✗', 'red'));
				$ui->line('    ' . $mark . ' ' . $i['title']);
				$ui->line('        ' . $ui->c('факт: ', 'gray') . $i['fact']);
				if (!$i['ok']) {
					$ui->line('        ' . $ui->c('действие: ', 'brgreen') . $i['fix']);
				}
			}
			$ui->line();
			$ui->line('  ' . $ui->c('Важно: ', 'bryellow') . $ui->c('техническая готовность ≠ позиция. Топ-3/топ-10 определяет авторитет домена '
				. '(внешние ссылки, бренд, возраст домена) — это измеряется в Яндекс.Вебмастере и Ahrefs/Semrush, а не обходом. '
				. 'Инструмент закрывает ту часть, которую можно доказать и починить.', 'gray'));
		}

		// ---------- Кластеры ----------
		if (!empty($insight['clusters'])) {
			$ui->section('ТЕМАТИЧЕСКИЕ КЛАСТЕРЫ САЙТА');
			foreach (array_slice($insight['clusters'], 0, 12) as $i => $cl) {
				$ui->line('  ' . $ui->c(($i + 1) . '. ', 'bold|brcyan')
					. $ui->c('кластер из ' . $cl['size'] . ' стр. ', 'bold|white')
					. $ui->c('· ' . implode(', ', array_slice($cl['terms'], 0, 6)), 'gray'));
				$ui->line('     ' . $ui->c('страницы: ', 'gray') . implode(', ', array_slice(array_map(static fn($u) => Ctx::pathKey($u), $cl['pages']), 0, 6)));
				if ($cl['gaps']) {
					$ui->line('     ' . $ui->c('пробелы кластера (есть в других страницах): ', 'gray')
						. $ui->c(implode(', ', array_slice(array_keys($cl['gaps']), 0, 8)), 'bryellow'));
				}
			}
		}

		// ---------- Конкуренты ----------
		if (!empty($insight['competitors'])) {
			$ui->section('СРАВНЕНИЕ С КОНКУРЕНТАМИ (те же 152 правила)');
			foreach ($insight['competitors'] as $comp) {
				$ui->line('  ' . $ui->c($comp['url'], 'bold|brcyan')
					. $ui->c('  страниц: ' . $comp['pages'] . ' · оценка: ' . $comp['score'] . '/100', 'gray'));
				if ($comp['better']) {
					$ui->line('    ' . $ui->c('у конкурента лучше (проверки, которые он проходит, а мы — нет):', 'bryellow'));
					foreach (array_slice($comp['better'], 0, 15) as $id) {
						$ui->line('      · ' . $id);
					}
				}
				if ($comp['worse']) {
					$ui->line('    ' . $ui->c('у нас лучше:', 'brgreen'));
					foreach (array_slice($comp['worse'], 0, 10) as $id) {
						$ui->line('      · ' . $id);
					}
				}
			}
		}
	}

	/**
	 * Семантическое ядро страницы: ядро, поддержка, пробелы и черновики.
	 *
	 * @return array<string,mixed>
	 */
	public static function semanticCore(Ctx $c, string $url): array
	{
		$page = $c->cr->pages[$url] ?? null;
		if ($page === null) {
			return [];
		}
		$h = $page['html'];

		// 1. Термины по блокам страницы с весами
		$blocks = [
			'title'       => [(string)($h['title'] ?? ''), 4],
			'h1'          => [(string)($h['h1'][0] ?? ''), 4],
			'h2-h3'       => [implode(' ', array_map(
				static fn(array $x): string => (string)$x['text'],
				array_filter($h['headings'] ?? [], static fn(array $x): bool => (int)$x['level'] >= 2 && (int)$x['level'] <= 3)
			)), 2],
			'description' => [(string)($h['description'] ?? ''), 2],
			'текст'       => [(string)($h['mainText'] ?? $h['text'] ?? ''), 1],
		];
		$score = [];
		$where = [];
		foreach ($blocks as $bname => [$text, $weight]) {
			foreach (self::termsOf((string)$text) as $t => $n) {
				$score[$t] = ($score[$t] ?? 0) + $n * $weight;
				if (!isset($where[$t])) {
					$where[$t] = [];
				}
				$where[$t][$bname] = ($where[$t][$bname] ?? 0) + $n;
			}
		}

		// 2. Входящие анкоры — реальный сигнал темы страницы внутри сайта
		$anchors = $c->cr->inlinks[$url]['anchors'] ?? [];
		foreach ($anchors as $anchor => $n) {
			foreach (self::termsOf((string)$anchor) as $t => $cnt) {
				$score[$t] = ($score[$t] ?? 0) + $cnt * $n * 3;
				$where[$t]['входящие анкоры'] = ($where[$t]['входящие анкоры'] ?? 0) + $cnt * $n;
			}
		}

		// 3. Слова из slug (путь URL) — тоже сигнал темы
		$slug = trim((string)(parse_url($url, PHP_URL_PATH) ?? ''), '/');
		foreach (self::termsOf(str_replace('-', ' ', $slug)) as $t => $n) {
			$score[$t] = ($score[$t] ?? 0) + $n * 2;
			$where[$t]['путь URL'] = ($where[$t]['путь URL'] ?? 0) + $n;
		}

		arsort($score);
		$core = [];
		foreach ($score as $t => $s) {
			if (count($core) >= 12) {
				break;
			}
			$core[] = [
				'term'   => (string)$t,
				'score'  => (float)$s,
				'blocks' => $where[$t] ?? [],
			];
		}

		// 4. Ядро страницы без учёта bigram-шумов: только одиночные слова
		$single = array_values(array_filter($core, static fn(array $x): bool => !str_contains($x['term'], ' ')));
		$primary = $single[0]['term'] ?? ($core[0]['term'] ?? '');
		$support = array_slice($single, 1, 7);

		// 5. Пробелы: термины из страниц, которые ссылаются на эту, но их нет на ней
		$have = array_map(static fn(string $t): bool => true, array_keys($score));
		$haveTerms = [];
		foreach (array_keys($score) as $t) {
			$haveTerms[$t] = true;
		}
		$gap = [];
		foreach ($c->cr->inlinks[$url]['from'] ?? [] as $src => $n) {
			$sp = $c->cr->pages[$src] ?? null;
			if ($sp === null) {
				continue;
			}
			foreach (self::termsOf((string)($sp['html']['title'] ?? '')) as $t => $cnt) {
				if (isset($haveTerms[$t]) || in_array($t, self::STOP, true) || mb_strlen($t) < 4) {
					continue;
				}
				if (!isset($gap[$t])) {
					$gap[$t] = ['term' => $t, 'count' => 0, 'from' => []];
				}
				$gap[$t]['count'] += $cnt * $n;
				if (count($gap[$t]['from']) < 3) {
					$gap[$t]['from'][] = Ctx::pathKey($src);
				}
			}
		}
		uasort($gap, static fn(array $a, array $b): int => $b['count'] <=> $a['count']);
		$gap = array_slice($gap, 0, 8, true);

		// 6. Покрытие ядра по блокам
		$coverage = [];
		foreach (array_slice($single, 0, 5) as $item) {
			$t = (string)$item['term'];
			$coverage[$t] = [
				'title'       => (bool)preg_match('~' . preg_quote($t, '~') . '~iu', (string)($h['title'] ?? '')),
				'h1'          => (bool)preg_match('~' . preg_quote($t, '~') . '~iu', (string)($h['h1'][0] ?? '')),
				'description' => (bool)preg_match('~' . preg_quote($t, '~') . '~iu', (string)($h['description'] ?? '')),
				'текст'       => (bool)preg_match('~' . preg_quote($t, '~') . '~iu', (string)($h['mainText'] ?? $h['text'] ?? '')),
			];
		}

		// 7. Бренд и гео для черновиков
		$brand = self::brandOf($c);
		$lang = (string)($h['lang'] ?? 'ru');
		$geo = str_starts_with($lang, 'ru') ? '' : '';

		// 8. Черновики title и description на основе реальных терминов
		$sup = array_slice(array_column($support, 'term'), 0, 2);
		$titleDraft = trim(ucfirst($primary) . ($sup !== [] ? ' — ' . implode(', ', $sup) : '') . ($brand !== '' ? ' | ' . $brand : ''));
		$descDraft = trim(
			ucfirst($primary) . ' — ' . ($sup !== [] ? implode(', ', $sup) . '. ' : '')
			. 'Подробности, сроки и стоимость: уточните детали у менеджера.'
		);

		return [
			'url'         => $url,
			'primary'     => $primary,
			'support'     => $support,
			'core'        => $core,
			'gap'         => $gap,
			'coverage'    => $coverage,
			'inlinks'     => $c->cr->inlinks[$url]['count'] ?? 0,
			'outlinks'    => count($c->cr->outlinks[$url] ?? []),
			'words'       => (int)($h['words'] ?? 0),
			'brand'       => $brand,
			'titleDraft'  => $titleDraft,
			'titleLen'    => mb_strlen($titleDraft),
			'descDraft'   => $descDraft,
			'descLen'     => mb_strlen($descDraft),
			'currentTitle'=> (string)($h['title'] ?? ''),
			'currentDesc' => (string)($h['description'] ?? ''),
		];
	}

	/** Название бренда: og:site_name, og:title или «хвост» заголовка главной. */
	public static function brandOf(Ctx $c): string
	{
		$home = rtrim($c->origin(), '/') . '/';
		$page = $c->cr->pages[$home] ?? null;
		if ($page === null) {
			foreach ($c->cr->pages as $p) {
				$page = $p;
				break;
			}
		}
		if ($page === null) {
			return '';
		}
		$og = (string)($page['html']['og']['site_name'] ?? '');
		if ($og !== '') {
			return $og;
		}
		$title = (string)($page['html']['title'] ?? '');
		if (preg_match('~[|—–\-]\s*([^|—–\-]{2,30})$~u', $title, $m)) {
			return trim($m[1]);
		}
		return '';
	}

	/**
	 * Тематические кластеры сайта: какие страницы про что и где пробелы.
	 *
	 * @return list<array{terms:list<string>,pages:list<string>,size:int,gaps:array<string,int>}>
	 */
	public static function clusters(Ctx $c, float $threshold = 0.28): array
	{
		$docs = [];
		foreach ($c->cr->pages as $url => $p) {
			$terms = self::termsOf((string)($p['html']['title'] ?? '') . ' ' . (string)($p['html']['h1'][0] ?? '') . ' ' . (string)($p['html']['mainText'] ?? $p['html']['text'] ?? ''));
			if (!$terms) {
				continue;
			}
			arsort($terms);
			$vec = [];
			$norm = 0.0;
			foreach (array_slice($terms, 0, 25, true) as $t => $n) {
				$vec[$t] = (float)$n;
				$norm += $n * $n;
			}
			$docs[$url] = ['vec' => $vec, 'norm' => sqrt($norm)];
		}
		$used = [];
		$clusters = [];
		foreach ($docs as $u1 => $d1) {
			if (isset($used[$u1])) {
				continue;
			}
			$used[$u1] = true;
			$members = [$u1];
			$terms = $d1['vec'];
			foreach ($docs as $u2 => $d2) {
				if ($u1 === $u2 || isset($used[$u2]) || $d1['norm'] === 0.0 || $d2['norm'] === 0.0) {
					continue;
				}
				$dot = 0.0;
				foreach ($d2['vec'] as $t => $v) {
					if (isset($d1['vec'][$t])) {
						$dot += $d1['vec'][$t] * $v;
					}
				}
				$cos = $dot / ($d1['norm'] * $d2['norm']);
				if ($cos >= $threshold) {
					$used[$u2] = true;
					$members[] = $u2;
					foreach ($d2['vec'] as $t => $v) {
						$terms[$t] = ($terms[$t] ?? 0) + $v;
					}
				}
			}
			arsort($terms);
			$clusterTerms = array_slice(array_keys($terms), 0, 10);
			// пробелы кластера: термины, которые есть в кластере, но не в первой странице
			$first = $docs[$members[0]]['vec'];
			$gaps = [];
			foreach ($terms as $t => $v) {
				if (!isset($first[$t]) && !str_contains((string)$t, ' ')) {
					$gaps[(string)$t] = (int)$v;
				}
			}
			arsort($gaps);
			$clusters[] = [
				'terms' => $clusterTerms,
				'pages' => $members,
				'size'  => count($members),
				'gaps'  => array_slice($gaps, 0, 8, true),
			];
		}
		usort($clusters, static fn(array $a, array $b): int => $b['size'] <=> $a['size']);
		return $clusters;
	}

	/**
	 * План продвижения конкретной страницы: что мешает расти и что делать.
	 *
	 * @return array<string,mixed>
	 */
	public static function pagePlan(Ctx $c, string $url, array $auditResults): array
	{
		$core = self::semanticCore($c, $url);
		if ($core === []) {
			return [];
		}
		$page = $c->cr->pages[$url];
		$h = $page['html'];

		// 1. Технические блокеры по этой странице
		$blockers = [];
		foreach ($auditResults as $r) {
			if ($r['pass']) {
				continue;
			}
			foreach ($r['violations'] as $v) {
				if (($v['url'] ?? null) === $url) {
					$blockers[] = [
						'rule'   => $r['id'],
						'sev'    => $r['sev'],
						'title'  => $r['title'],
						'detail' => $v['detail'],
						'hint'   => $r['hint'],
						'impact' => $r['impact'],
					];
				}
			}
		}
		usort($blockers, static fn(array $a, array $b): int => ['critical' => 0, 'high' => 1, 'medium' => 2, 'low' => 3, 'info' => 4][$a['sev']] <=> ['critical' => 0, 'high' => 1, 'medium' => 2, 'low' => 3, 'info' => 4][$b['sev']]);

		// 2. Диагностика «почему не растёт»
		$diagnostics = [];
		$title = (string)($h['title'] ?? '');
		$h1 = (string)($h['h1'][0] ?? '');
		$desc = (string)($h['description'] ?? '');
		$primary = (string)$core['primary'];
		$inlinks = (int)$core['inlinks'];
		$words = (int)$core['words'];

		if ($primary !== '' && !preg_match('~' . preg_quote($primary, '~') . '~iu', $title)) {
			$diagnostics[] = [
				'why'    => 'Основной термин страницы не вынесен в title',
				'fact'   => 'ядро: «' . $primary . '», в title: ' . ($title !== '' ? '«' . mb_substr($title, 0, 60) . '»' : '—'),
				'fix'    => 'Поставить «' . $primary . '» в начало title — это и запрос, и обещание страницы.',
			];
		}
		if ($primary !== '' && !preg_match('~' . preg_quote($primary, '~') . '~iu', $h1)) {
			$diagnostics[] = [
				'why'    => 'H1 не содержит основной термин',
				'fact'   => 'ядро: «' . $primary . '», H1: «' . mb_substr($h1, 0, 60) . '»',
				'fix'    => 'Синхронизировать H1 с ядром страницы.',
			];
		}
		if (trim($desc) === '') {
			$diagnostics[] = [
				'why'  => 'Нет description — сниппет собирается поисковиком случайно',
				'fact' => 'description отсутствует',
				'fix'  => 'Написать description по черновику из блока «Черновики».',
			];
		}
		if ($words < 300) {
			$diagnostics[] = [
				'why'    => 'Мало содержания: страница не может закрыть запрос целиком',
				'fact'   => $words . ' слов (ориентир от 300)',
				'fix'    => 'Дополнить разделами по подтверждённым подтемам кластера и фактами с цифрами.',
			];
		}
		if ($inlinks === 0) {
			$diagnostics[] = [
				'why'    => 'Страница не получает внутреннего веса',
				'fact'   => 'входящих ссылок: 0',
				'fix'    => 'Добавить ссылки из тематически близких страниц (список ниже).',
			];
		} elseif ($inlinks < 3) {
			$diagnostics[] = [
				'why'    => 'Мало входящих ссылок для устойчивого ранжирования',
				'fact'   => 'входящих ссылок: ' . $inlinks,
				'fix'    => 'Довести до 5–10 ссылок с релевантными анкорами из тематических соседей.',
			];
		}
		if ((int)$page['depth'] > 3) {
			$diagnostics[] = [
				'why'    => 'Страница глубже 3 кликов — краулер и пользователь добираются до неё дольше',
				'fact'   => 'глубина: ' . $page['depth'],
				'fix'    => 'Поднять страницу в хаб или в меню второго уровня.',
			];
		}
		// каннибализация: другая страница сайта про то же
		$cannibal = [];
		foreach ($c->cr->pages as $u2 => $p2) {
			if ($u2 === $url) {
				continue;
			}
			$t2 = (string)($p2['html']['title'] ?? '');
			if ($t2 === '' || $t2 === $title) {
				continue;
			}
			$t2l = mb_strtolower($t2);
			if ($primary !== '' && mb_strpos($t2l, $primary) !== false) {
				$cannibal[] = Ctx::pathKey($u2) . ' — «' . mb_substr($t2, 0, 50) . '»';
			}
		}
		if ($cannibal !== []) {
			$diagnostics[] = [
				'why'    => 'Каннибализация: ту же тему закрывают другие страницы сайта',
				'fact'   => implode('; ', array_slice($cannibal, 0, 3)),
				'fix'    => 'Развести страницы по подтемам или объединить их и оставить один канонический адрес.',
			];
		}

		// 3. Откуда добавить ссылки: тематически близкие страницы, которые не ссылаются
		$linkSources = [];
		$targetTerms = array_column($core['support'], 'term');
		$targetTerms[] = (string)$core['primary'];
		foreach ($c->cr->pages as $u2 => $p2) {
			if ($u2 === $url) {
				continue;
			}
			if (isset($c->cr->inlinks[$u2]['from'][$url])) {
				continue;   // уже ссылается
			}
			$t2 = mb_strtolower((string)($p2['html']['title'] ?? '') . ' ' . (string)($p2['html']['h1'][0] ?? ''));
			$hits = 0;
			foreach ($targetTerms as $t) {
				if ($t !== '' && mb_strpos($t2, $t) !== false) {
					$hits++;
				}
			}
			if ($hits > 0) {
				$linkSources[] = ['url' => Ctx::pathKey($u2), 'hits' => $hits, 'title' => (string)($p2['html']['title'] ?? '')];
			}
		}
		usort($linkSources, static fn(array $a, array $b): int => $b['hits'] <=> $a['hits']);
		$linkSources = array_slice($linkSources, 0, 8);

		// 4. Готовность к топ-10
		$readiness = self::readiness($c, $url, $auditResults, $core, $blockers);

		return [
			'url'         => $url,
			'core'        => $core,
			'blockers'    => $blockers,
			'diagnostics' => $diagnostics,
			'linkSources' => $linkSources,
			'readiness'   => $readiness,
			'clusters'    => self::clusters($c),
		];
	}

	/**
	 * Модель готовности страницы к попаданию в топ-10.
	 * Технические условия проверяем сами; авторитет (ссылки, бренд) — нет,
	 * и это честно отмечено как требующее внешних данных.
	 *
	 * @return array{items:list<array{title:string,ok:bool,fact:string,fix:string,external:bool}>,ready:float}
	 */
	public static function readiness(Ctx $c, string $url, array $auditResults, array $core, array $blockers): array
	{
		$items = [];
		$blocked = array_column($blockers, 'rule');
		$page = $c->cr->pages[$url];
		$h = $page['html'];

		$items[] = [
			'title'    => 'Страница доступна для индексации (нет noindex/robots, canonical ведёт на себя)',
			'ok'       => !array_intersect($blocked, ['index.noindex_meta', 'index.noindex_header', 'index.canonical_mismatch', 'index.canonical_broken']),
			'fact'     => 'robots meta: ' . (trim((string)$h['robotsMeta']) !== '' ? $h['robotsMeta'] : '—') . ' · canonical: ' . (trim((string)($h['canonical'] ?? '')) !== '' ? $h['canonical'] : '—'),
			'fix'      => 'Снять noindex и выставить self-canonical.',
			'external' => false,
		];
		$items[] = [
			'title'    => 'Контент уникален (нет дублей по всему сайту)',
			'ok'       => !in_array('index.duplicate_content', $blocked, true),
			'fact'     => 'слов на странице: ' . (int)$h['words'] . ' · страниц в обходе: ' . count($c->pages()),
			'fix'      => 'Убрать совпадения: переписать текст или объединить страницы.',
			'external' => false,
		];
		$items[] = [
			'title'    => 'Семантическое ядро вынесено в title и H1',
			'ok'       => (bool)($core['coverage'][$core['primary']]['title'] ?? false) && (bool)($core['coverage'][$core['primary']]['h1'] ?? false),
			'fact'     => 'ядро: «' . $core['primary'] . '» · title: ' . (($core['coverage'][$core['primary']]['title'] ?? false) ? 'есть' : 'нет')
				. ' · H1: ' . (($core['coverage'][$core['primary']]['h1'] ?? false) ? 'есть' : 'нет'),
			'fix'      => 'Использовать черновики title/description из отчёта.',
			'external' => false,
		];
		$items[] = [
			'title'    => 'Есть description для сниппета',
			'ok'       => trim((string)$h['description']) !== '',
			'fact'     => trim((string)$h['description']) !== '' ? mb_substr((string)$h['description'], 0, 80) : 'отсутствует',
			'fix'      => 'Написать description по черновику.',
			'external' => false,
		];
		$items[] = [
			'title'    => 'Достаточно входящих внутренних ссылок',
			'ok'       => (int)$core['inlinks'] >= 3,
			'fact'     => 'входящих ссылок: ' . (int)$core['inlinks'] . ' (медиана по сайту: ' . self::medianInlinks($c) . ')',
			'fix'      => 'Добавить ссылки с тематически близких страниц.',
			'external' => false,
		];
		$items[] = [
			'title'    => 'Страница не глубже 3 кликов',
			'ok'       => (int)$page['depth'] <= 3,
			'fact'     => 'глубина: ' . (int)$page['depth'],
			'fix'      => 'Поднять страницу в хаб или меню.',
			'external' => false,
		];
		$items[] = [
			'title'    => 'Скорость в норме (TTFB и вес страницы)',
			'ok'       => !in_array('perf.ttfb', $blocked, true) && !in_array('perf.page_size', $blocked, true),
			'fact'     => 'TTFB: ' . round((float)$page['ttfb'] * 1000) . ' мс · HTML: ' . round((int)$page['bodySize'] / 1024, 1) . ' КБ',
			'fix'      => 'Кэш страниц, сжатие, оптимизация запросов.',
			'external' => false,
		];
		$items[] = [
			'title'    => 'Мобильная версия и viewport корректны',
			'ok'       => !in_array('meta.viewport_missing', $blocked, true),
			'fact'     => 'viewport: ' . (trim((string)$h['viewport']) !== '' ? $h['viewport'] : 'отсутствует'),
			'fix'      => 'Добавить meta viewport.',
			'external' => false,
		];
		$items[] = [
			'title'    => 'Есть разметка Schema.org',
			'ok'       => !empty($h['jsonld']),
			'fact'     => 'блоков JSON-LD: ' . count($h['jsonld']),
			'fix'      => 'Добавить разметку под тип страницы.',
			'external' => false,
		];
		$items[] = [
			'title'    => 'Разметка Open Graph (превью в соцсетях и AI-ответах)',
			'ok'       => !in_array('meta.og_missing', $blocked, true),
			'fact'     => !in_array('meta.og_missing', $blocked, true) ? 'заполнена' : 'заполнена не полностью',
			'fix'      => 'Дописать og:title, og:description, og:image, og:url.',
			'external' => false,
		];
		$items[] = [
			'title'    => 'Изображения с alt и размерами (нет битых, CLS под контролем)',
			'ok'       => !array_intersect($blocked, ['img.missing_alt', 'img.broken', 'img.no_dimensions']),
			'fact'     => 'изображений: ' . count($h['images']),
			'fix'      => 'Заполнить alt, задать width/height, починить пути.',
			'external' => false,
		];
		$items[] = [
			'title'    => 'АВТОРИТЕТ ДОМЕНА (внешние ссылки, бренд, история)',
			'ok'       => false,
			'fact'     => 'внешние данные: не измеряются HTTP-обходом',
			'fix'      => 'Проверить в Яндекс.Вебмастере (входящие ссылки, запросы, позиции) и в Ahrefs/Semrush (referring domains, DR). '
				. 'Ключевой вопрос: у конкурентов, которые выше, больше ссылок с авторитетных доменов — если да, технические правки не поднимут страницу в топ.',
			'external' => true,
		];

		$tech = 0;
		$techTotal = 0;
		foreach ($items as $i) {
			if ($i['external']) {
				continue;
			}
			$techTotal++;
			if ($i['ok']) {
				$tech++;
			}
		}
		return [
			'items' => $items,
			'ready' => $techTotal > 0 ? round($tech / $techTotal * 100) : 0,
			'tech'  => $tech,
			'techTotal' => $techTotal,
		];
	}

	/** Медиана входящих ссылок по сайту — база для сравнения. */
	public static function medianInlinks(Ctx $c): int
	{
		$counts = [];
		foreach ($c->cr->pages as $url => $p) {
			$counts[] = $c->cr->inlinks[$url]['count'] ?? 0;
		}
		if (!$counts) {
			return 0;
		}
		sort($counts);
		$mid = intdiv(count($counts), 2);
		return count($counts) % 2 === 0
			? (int)round(($counts[$mid - 1] + $counts[$mid]) / 2)
			: $counts[$mid];
	}

	/**
	 * Сравнение с конкурентами: те же 152 правила против их страниц.
	 * Показывает, что именно у конкурента сделано лучше — это и есть разрыв,
	 * который мешает занять позицию.
	 *
	 * @return list<array{url:string,pages:int,score:float,better:list<string>,worse:list<string>,rules:array<string,bool>}>
	 */
	public static function compareCompetitors(Cfg $cfg, array $ourResults): array
	{
		$our = [];
		foreach ($ourResults as $r) {
			$our[$r['id']] = $r['pass'];
		}
		$out = [];
		foreach ($cfg->competitors as $url) {
			$ccfg = new Cfg();
			$ccfg->start = $url;
			$ccfg->depth = 2;
			$ccfg->limit = 40;
			$ccfg->concurrency = 3;
			$ccfg->delayMs = 100;
			$ccfg->external = false;
			$ccfg->probe = false;
			$ccfg->quiet = true;
			$ccfg->ua = $cfg->ua;

			$cr = new Crawler($ccfg, new Ui(false));
			$cr->run();
			$c = new Ctx($ccfg, $cr);
			$a = new Audit();
			$res = $a->run($c);
			$their = [];
			foreach ($res as $r) {
				$their[$r['id']] = $r['pass'];
			}
			$better = [];
			$worse = [];
			foreach ($their as $id => $pass) {
				if (!isset($our[$id]) || $our[$id] === $pass) {
					continue;
				}
				if ($pass) {
					$better[] = $id;
				} else {
					$worse[] = $id;
				}
			}
			$out[] = [
				'url'   => $url,
				'pages' => count($cr->pages),
				'score' => (float)$a->score()['score'],
				'better'=> $better,
				'worse' => $worse,
				'rules' => $their,
			];
		}
		return $out;
	}
}
final class Report
{
	public function __construct(private Ui $ui, private Cfg $cfg)
	{
	}

	// -----------------------------------------------------------------
	//  Консоль
	// -----------------------------------------------------------------
	public function renderConsole(Ctx $c, Crawler $cr, Audit $audit, array $score): void
	{
		$ui = $this->ui;
		$results = $audit->results();

		$bySev = ['critical' => 0, 'high' => 0, 'medium' => 0, 'low' => 0, 'info' => 0];
		$passed = 0;
		foreach ($results as $r) {
			if ($r['pass']) {
				$passed++;
			} else {
				$bySev[$r['sev']]++;
			}
		}

		$ui->title('STUDIO AUDIT · SEO · ТЕХНИКА · СКОРОСТЬ · БЕЗОПАСНОСТЬ · ДОСТУПНОСТЬ');
		$ui->line();
		$ui->kv('Сайт', $ui->c($c->origin() . '/', 'bold|brcyan'));
		$ui->kv('Режим', $ui->c($this->cfg->modeLabel(), 'bold|white') . $ui->c(' (определён автоматически)', 'gray'));
		$ui->kv('Обход', count($cr->pages) . ' URL · глубина ≤ ' . $this->cfg->depth . ' · ' . round($cr->duration, 2) . ' с · ' . $this->cfg->concurrency . ' параллельных запросов');
		$inTotal = array_sum(array_map(static fn($i) => $i['count'], $cr->inlinks));
		$ui->kv('Правил', count($results) . ' проверок · страниц в обходе: ' . count($cr->pages) . ' · внутренних ссылок: ' . $inTotal . ' · внешних: ' . count($cr->externalLinks));
		if ($cr->timings) {
			$phases = [];
			foreach ($cr->timings as $name => $sec) {
				$phases[] = $name . ' ' . round($sec, 2) . ' с';
			}
			$ui->kv('Этапы', implode(' · ', $phases) . ' · запросов: ' . $cr->requests . ($cr->retries > 0 ? ' · повторов: ' . $cr->retries : '') . ' · память: ' . round(memory_get_peak_usage(true) / 1048576, 1) . ' МБ');
		}
		if ($cr->notes) {
			$ui->kv('Заметки', $ui->c(implode('; ', array_slice($cr->notes, 0, 3)), 'bryellow'));
		}

		// --- дашборд: оценка + распределение по критичности ---
		$scoreVal = (float)$score['score'];
		$scoreColor = $scoreVal >= 85 ? 'brgreen' : ($scoreVal >= 65 ? 'bryellow' : 'brred');
		$ui->line();
		$ui->line('  ' . $ui->c('ОБЩАЯ ОЦЕНКА', 'bold|white')
			. '  ' . $ui->c(number_format($scoreVal, 1), 'bold|' . $scoreColor)
			. $ui->c(' / 100', 'gray')
			. '   ' . $ui->bar($scoreVal, 26)
			. '  ' . $ui->c($this->scoreWord($scoreVal), $scoreColor));
		$ui->line();

		// --- плитки по критичности ---
		$sevStyle = ['critical' => 'bgred', 'high' => 'bgyellow', 'medium' => 'bgblue', 'low' => 'bgmagenta', 'info' => 'bggray'];
		$tiles = '';
		foreach (['critical', 'high', 'medium', 'low', 'info'] as $sev) {
			$label = ['critical' => 'КРИТИЧНО', 'high' => 'ВЫСОКАЯ', 'medium' => 'СРЕДНЯЯ', 'low' => 'НИЗКАЯ', 'info' => 'ИНФО'][$sev];
			$tiles .= $ui->chip($ui->pad($label, 8), $sevStyle[$sev]) . ' ' . $ui->c((string)$bySev[$sev], 'bold|white') . '   ';
		}
		$tiles .= $ui->chip($ui->pad('ПРОЙДЕНО', 8), 'bggreen') . ' ' . $ui->c((string)$passed, 'bold|brgreen');
		$ui->line('  ' . $tiles);
		$totalRules = max(1, count($results));
		$parts = [];
		foreach (['critical', 'high', 'medium', 'low', 'info'] as $sev) {
			$parts[$sev] = $bySev[$sev] / $totalRules;
		}
		$ui->line('  ' . $ui->stackBar($parts, $sevStyle, 62) . ' ' . $ui->c(' доля проблем по критичности', 'gray'));
		$ui->line();

		// --- оценка по направлениям ---
		foreach ($score['byCat'] as $cat => $data) {
			$sc = (int)$data['score'];
			$catColor = $ui->catStyle((string)$cat);
			$failed = $data['failed'] > 0
				? $ui->c(' ⚠ ' . $data['failed'] . ' с замечаниями', 'bryellow')
				: $ui->c(' ✓ без замечаний', 'brgreen');
			$ui->line('  ' . $ui->c('▸', $catColor) . ' ' . $ui->c($ui->pad((string)$cat, 22), $catColor)
				. $ui->c($ui->pad((string)$sc, 3, false), 'bold') . $ui->c('/100', 'gray')
				. '  ' . $ui->bar((float)$sc, 18) . $failed);
		}

		// --- сводка ---
		$ui->section('СВОДКА');
		$ui->line('  ' . $ui->c('Проверок пройдено без замечаний: ', 'gray') . $ui->c($passed . ' из ' . count($results), 'bold|brgreen')
			. ($passed === count($results)
				? $ui->c('   ✓ все проверки пройдены', 'brgreen')
				: $ui->c('   с замечаниями: ' . (count($results) - $passed), 'bryellow')));

		// --- ТОП проблем: сначала то, что нужно закрыть сегодня ---
		$top = [];
		foreach (['critical', 'high', 'medium'] as $sev) {
			foreach ($results as $r) {
				if (!$r['pass'] && $r['sev'] === $sev) {
					$top[] = $r;
				}
			}
		}
		if ($top) {
			$ui->section('ЧТО ЗАКРЫТЬ В ПЕРВУЮ ОЧЕРЕДЬ', 'bryellow');
			foreach (array_slice($top, 0, 8) as $r) {
				$first = $r['violations'][0] ?? null;
				$where = $first !== null && $first['url'] !== null ? Ctx::pathKey($first['url']) : 'сайт целиком';
				$ui->line('  ' . $ui->sevBadge($r['sev']) . ' ' . $ui->c($ui->pad($r['id'], 30), 'bold|white')
					. ' ' . $ui->c(mb_substr($r['title'], 0, 46), 'gray'));
				$ui->line('      ' . $ui->c('→ ', 'cyan') . $ui->c(mb_substr($where, 0, 60), 'brcyan')
					. ($first !== null ? $ui->c('  ' . mb_substr((string)$first['detail'], 0, 70), 'gray') : ''));
				if ($r['impact'] !== '') {
					$ui->line('      ' . $ui->c('◎ ', 'brcyan') . $ui->c(mb_substr($r['impact'], 0, 120), 'gray'));
				}
			}
			if (count($top) > 8) {
				$ui->line('  ' . $ui->c('… ещё ' . (count($top) - 8) . ' — см. разделы ниже', 'gray'));
			}
		}

		// --- проблемы по критичности ---
		foreach (['critical', 'high', 'medium', 'low', 'info'] as $sev) {
			$group = array_values(array_filter($results, static fn($r) => !$r['pass'] && $r['sev'] === $sev));
			if (!$group) {
				continue;
			}
			$ui->section($ui->sevBadge($sev) . ' ' . $ui->c('· ' . count($group) . ' ' . $this->plural(count($group), 'проблема', 'проблемы', 'проблем'), 'bold|white'),
				$sev === 'critical' ? 'brred' : ($sev === 'high' ? 'bryellow' : 'brcyan'));
			foreach ($group as $r) {
				$ui->line('  ' . $ui->c('✗', 'red') . ' ' . $ui->c($r['id'], 'bold|white') . '  ' . $ui->c($r['title'], 'brcyan'));
				$ui->line('    ' . $ui->c('затронуто: ' . $r['failed'] . ' из ' . $r['checked'] . ' (' . $r['scope'] . ') · ' . $r['cat'], 'gray'));
				foreach (array_slice($r['violations'], 0, 6) as $v) {
					$where = $v['url'] !== null ? Ctx::pathKey($v['url']) : 'сайт целиком';
					$ui->line('      ' . $ui->c('•', 'cyan') . ' ' . $ui->c($where, 'white') . ' ' . $ui->c('—', 'gray') . ' ' . $v['detail']);
					if (!empty($v['found'])) {
						foreach ($v['found'] as $f) {
							$ui->line('          ' . $ui->c('└ найдено на ', 'gray') . $ui->c(Ctx::pathKey($f['from']), 'brcyan')
								. ($f['anchor'] !== '' ? $ui->c(' — «' . mb_substr($f['anchor'], 0, 40) . '»', 'gray') : '')
								. $ui->c(' (' . $f['zone'] . ')', 'gray'));
						}
					}
				}
				if (count($r['violations']) > 6) {
					$ui->line('      ' . $ui->c('… и ещё ' . (count($r['violations']) - 6), 'gray'));
				}
				if ($r['hint'] !== '') {
					$ui->line('    ' . $ui->c('▸ Что делать: ', 'brgreen') . $r['hint']);
				}
				if ($r['impact'] !== '') {
					$ui->line('    ' . $ui->c('◎ Влияет: ', 'brcyan') . $ui->c($r['impact'], 'white'));
				}
				if ($r['sources']) {
					$ui->line('    ' . $ui->c('↳ Источник: ', 'gray') . $ui->c(implode('  ·  ', $r['sources']), 'gray'));
				}
				$ui->line();
			}
		}

		// --- что проверено и проблем нет ---
		$passGroups = [];
		foreach ($results as $r) {
			if ($r['pass']) {
				$passGroups[$r['cat']][] = $r;
			}
		}
		$ui->section($ui->chip('ПРОЙДЕНО', 'bggreen') . ' ' . $ui->c('· замечаний нет (' . $passed . ')', 'bold|brgreen'), 'brgreen');
		foreach ($passGroups as $cat => $rules) {
			$ui->line('  ' . $ui->c('▸', $ui->catStyle((string)$cat)) . ' ' . $ui->c((string)$cat, 'bold|' . $ui->catStyle((string)$cat)));
			foreach ($rules as $r) {
				$ui->line('    ' . $ui->c('✓', 'brgreen') . ' ' . $ui->c($r['title'], 'gray') . $ui->c('  (' . $r['checked'] . ' шт.)', 'gray'));
			}
		}

		// --- карта сайта и граф ---
		$this->renderGraph($c, $cr);

		// --- таблица страниц ---
		if ($this->cfg->showPages) {
			$this->renderPageTable($c, $cr);
		}

		// --- финальный вердикт ---
		$ui->line();
		$ui->line($ui->verdict($scoreVal, number_format($scoreVal, 1) . '/100 · ' . $this->verdictText($bySev, $passed, count($results))));
	}

	/** Текстовая оценка по баллу. */
	private function scoreWord(float $score): string
	{
		return match (true) {
			$score >= 90 => 'отлично',
			$score >= 80 => 'хорошо',
			$score >= 65 => 'нормально, есть что чинить',
			$score >= 45 => 'слабо, много замечаний',
			default      => 'критично, нужен полный ремонт',
		};
	}

	/** Итоговая фраза отчёта. */
	private function verdictText(array $bySev, int $passed, int $total): string
	{
		if (($bySev['critical'] ?? 0) > 0) {
			return 'закрыть ' . $bySev['critical'] . ' критичн. и ' . ($bySev['high'] ?? 0) . ' высоких — остальное вторично';
		}
		if (($bySev['high'] ?? 0) > 0) {
			return 'критичных нет, закрыть ' . $bySev['high'] . ' высоких замечаний';
		}
		return 'критичных замечаний нет · пройдено ' . $passed . ' из ' . $total . ' проверок';
	}

	private function renderGraph(Ctx $c, Crawler $cr): void
	{
		$ui = $this->ui;
		$ui->section('ГРАФ САЙТА');
		$depths = [];
		foreach ($cr->pages as $p) {
			$d = (int)$p['depth'];
			$depths[$d] = ($depths[$d] ?? 0) + 1;
		}
		ksort($depths);
		$max = $depths ? max($depths) : 1;
		foreach ($depths as $d => $cnt) {
			$bar = str_repeat('█', (int)round($cnt / $max * 30));
			// чем глубже — тем «тревожнее» цвет
			$style = $d <= 1 ? 'brgreen' : ($d === 2 ? 'bryellow' : ($d === 3 ? 'yellow' : 'red'));
			$ui->line('  ' . $ui->c($ui->pad('глубина ' . $d, 16), 'gray')
				. $ui->c($ui->pad((string)$cnt, 4, false) . ' стр. ', 'white')
				. $ui->c($bar, $style)
				. '  ' . $ui->c($d > 3 ? 'глубже 3 кликов — риск для индексации' : '', 'gray'));
		}

		$in = [];
		foreach ($cr->inlinks as $url => $info) {
			$in[] = ['url' => $url, 'count' => $info['count'], 'from' => count($info['from'])];
		}
		usort($in, static fn($a, $b) => $b['count'] <=> $a['count']);
		$ui->line();
		$ui->line('  ' . $ui->c('СТРАНИЦЫ С НАИБОЛЬШИМ ВНУТРЕННИМ ВЕСОМ', 'bold|brcyan'));
		foreach (array_slice($in, 0, 8) as $row) {
			$ui->line('    ' . $ui->c($ui->pad((string)$row['count'], 5, false), 'bold|brcyan')
				. $ui->c(' ссылок', 'gray')
				. '  ' . $ui->c('← из ' . $row['from'] . ' стр.', 'gray')
				. '  ' . $ui->c(Ctx::pathKey($row['url']), 'white'));
		}
		if (!$in) {
			$ui->line('    ' . $ui->c('нет данных', 'gray'));
		}

		if ($cr->externalHosts) {
			arsort($cr->externalHosts);
			$ui->line();
			$ui->line('  ' . $ui->c('ВНЕШНИЕ ХОСТЫ', 'bold|brcyan'));
			foreach (array_slice($cr->externalHosts, 0, 8, true) as $host => $cnt) {
				$ui->line('    ' . $ui->c($ui->pad((string)$cnt, 5, false), 'bold|white')
					. $ui->c(' ссылок', 'gray')
					. '  ' . $ui->c($host, 'brcyan'));
			}
		}
	}

	private function renderPageTable(Ctx $c, Crawler $cr): void
	{
		$ui = $this->ui;
		$ui->section('СТРАНИЦЫ');
		$head = 'ПУТЬ' . str_repeat(' ', 38) . ' КОД   ВХ  ГЛ   СЛОВ     БАЙТ   TTFB  TITLE';
		$ui->line('  ' . $ui->c($head, 'bold|gray'));
		$ui->line('  ' . $ui->c(str_repeat('─', 96), 'gray'));
		foreach ($cr->pages as $url => $p) {
			$in = $cr->inlinks[$url]['count'] ?? 0;
			$code = (int)$p['status'];
			$codeStr = $code === 200
				? $ui->c('200', 'brgreen')
				: ($code >= 400 ? $ui->c((string)$code, 'bold|brred') : $ui->c((string)$code, 'bryellow'));
			$title = mb_substr(trim((string)$p['html']['title']), 0, 40);
			$ttfb = (int)round((float)$p['ttfb'] * 1000);
			$ttfbStr = $ttfb > 500 ? $ui->c($ui->pad($ttfb . 'мс', 6, false), 'bryellow')
				: $ui->c($ui->pad($ttfb . 'мс', 6, false), 'gray');
			$ui->line('  ' . $ui->c($ui->pad(mb_substr(Ctx::pathKey($url), 0, 40), 41), 'white')
				. ' ' . $codeStr
				. '  ' . $ui->c($ui->pad((string)$in, 3, false), 'brcyan')
				. '  ' . $ui->c($ui->pad((string)$p['depth'], 2, false), 'gray')
				. '  ' . $ui->c($ui->pad((string)$p['html']['words'], 5, false), 'gray')
				. '  ' . $ui->c($ui->pad((string)(int)$p['bodySize'], 7, false), 'gray')
				. '  ' . $ttfbStr
				. '  ' . $ui->c($title, 'gray'));
		}
	}

	// -----------------------------------------------------------------
	//  JSON
	// -----------------------------------------------------------------
	public function renderJson(Ctx $c, Crawler $cr, Audit $audit, array $score): string
	{
		$results = $audit->results();
		$issues = [];
		$bySev = ['critical' => 0, 'high' => 0, 'medium' => 0, 'low' => 0, 'info' => 0];
		$passed = 0;
		foreach ($results as $r) {
			if ($r['pass']) {
				$passed++;
				continue;
			}
			$bySev[$r['sev']]++;
			foreach ($r['violations'] as $v) {
				$issues[] = [
					'rule'      => $r['id'],
					'category'  => $r['cat'],
					'severity'  => $r['sev'],
					'title'     => $r['title'],
					'hint'      => $r['hint'],
					'impact'    => $r['impact'],
					'sources'   => $r['sources'],
					'url'       => $v['url'],
					'path'      => $v['url'] !== null ? Ctx::pathKey($v['url']) : null,
					'detail'    => $v['detail'],
					'found'     => $v['found'] ?? [],
				];
			}
		}
		$pages = [];
		foreach ($cr->pages as $url => $p) {
			$pages[] = [
				'url'       => $url,
				'path'      => Ctx::pathKey($url),
				'status'    => (int)$p['status'],
				'final'     => $p['final'],
				'redirects' => count($p['redirects'] ?? []),
				'depth'     => (int)$p['depth'],
				'inlinks'   => $cr->inlinks[$url]['count'] ?? 0,
				'outlinks'  => count($cr->outlinks[$url] ?? []),
				'title'     => $p['html']['title'],
				'description' => $p['html']['description'],
				'canonical' => $p['html']['canonical'],
				'robots'    => $p['html']['robotsMeta'],
				'lang'      => $p['html']['lang'],
				'words'     => (int)$p['html']['words'],
				'bytes'     => (int)$p['bodySize'],
				'ttfb_ms'   => (int)round((float)$p['ttfb'] * 1000),
				'total_ms'  => (int)round((float)$p['timeTotal'] * 1000),
				'http_version' => $p['httpVersion'],
				'content_type'  => $p['contentType'],
				'images'    => count($p['html']['images']),
				'links'     => count($p['html']['links']),
				'jsonld'    => count($p['html']['jsonld']),
			];
		}
		$checks = [];
		foreach ($results as $r) {
			$checks[] = [
				'id' => $r['id'], 'category' => $r['cat'], 'severity' => $r['sev'],
				'title' => $r['title'], 'hint' => $r['hint'], 'impact' => $r['impact'],
				'sources' => $r['sources'], 'scope' => $r['scope'],
				'checked' => $r['checked'], 'failed' => $r['failed'], 'pass' => $r['pass'],
				'violations' => $r['violations'],
			];
		}
		$data = [
			'tool'     => 'Studio Audit Crawler',
			'version'  => '2.0.0',
			'target'   => $c->origin() . '/',
			'generated' => date('Y-m-d H:i:s'),
			'duration_sec' => round($cr->duration, 2),
			'config'   => [
				'depth' => $this->cfg->depth, 'limit' => $this->cfg->limit,
				'concurrency' => $this->cfg->concurrency, 'timeout_ms' => $this->cfg->timeoutMs,
				'user_agent' => $this->cfg->ua, 'external_check' => $this->cfg->external,
				'external_limit' => $this->cfg->externalLimit, 'probe' => $this->cfg->probe,
			],
			'score'   => [
				'total'       => (float)$score['score'],
				'by_category' => $score['byCat'],
			],
			'summary' => [
				'pages'          => count($cr->pages),
				'rules'          => count($results),
				'passed'         => $passed,
				'failed'         => count($results) - $passed,
				'issues'         => count($issues),
				'by_severity'    => $bySev,
				'internal_links' => array_sum(array_map(static fn($i) => $i['count'], $cr->inlinks)),
				'external_links' => count($cr->externalLinks),
				'http_requests'  => $cr->requests,
			],
			'checks'  => $checks,
			'issues'  => $issues,
			'pages'   => $pages,
			'robots'  => [
				'exists'   => $cr->robots?->exists ?? false,
				'status'   => $cr->robots?->status ?? 0,
				'sitemaps' => $cr->robots?->sitemaps ?? [],
				'host'     => $cr->robots?->host,
				'errors'   => $cr->robots?->errors ?? [],
			],
			'sitemap' => [
				'exists' => $cr->sitemaps->exists,
				'files'  => $cr->sitemaps->sitemaps,
				'urls'   => count($cr->sitemaps->urls),
				'errors' => $cr->sitemaps->errors,
			],
		];
		return (string)json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
	}

	// -----------------------------------------------------------------
	//  HTML
	// -----------------------------------------------------------------
	public function renderHtml(Ctx $c, Crawler $cr, Audit $audit, array $score, array $insight = []): string
	{
		$results = $audit->results();
		$bySev = ['critical' => 0, 'high' => 0, 'medium' => 0, 'low' => 0, 'info' => 0];
		$passed = 0;
		$issues = [];
		foreach ($results as $r) {
			if ($r['pass']) {
				$passed++;
				continue;
			}
			$bySev[$r['sev']]++;
			foreach ($r['violations'] as $v) {
				$issues[] = [
					'sev'    => $r['sev'],
					'cat'    => $r['cat'],
					'rule'   => $r['id'],
					'title'  => $r['title'],
					'hint'   => $r['hint'],
					'impact' => $r['impact'],
					'sources'=> $r['sources'],
					'path'   => $v['url'] !== null ? Ctx::pathKey($v['url']) : 'сайт целиком',
					'url'    => $v['url'],
					'detail' => $v['detail'],
					'found'  => $v['found'] ?? [],
				];
			}
		}
		usort($issues, static function (array $a, array $b): int {
			$order = ['critical' => 0, 'high' => 1, 'medium' => 2, 'low' => 3, 'info' => 4];
			return [$order[$a['sev']], $a['cat'], $a['rule']] <=> [$order[$b['sev']], $b['cat'], $b['rule']];
		});

		$sevLabel = ['critical' => 'Критично', 'high' => 'Высокая', 'medium' => 'Средняя', 'low' => 'Низкая', 'info' => 'Инфо'];
		$cats = array_keys($score['byCat']);

		// Приоритет 1: правила (не отдельные находки), которые надо закрыть первыми
		$priority = [];
		foreach (['critical', 'high', 'medium'] as $sev) {
			foreach ($results as $r) {
				if (!$r['pass'] && $r['sev'] === $sev) {
					$priority[] = $r;
				}
			}
		}

		$h = '<!DOCTYPE html><html lang="ru"><head><meta charset="utf-8">';
		$h .= '<meta name="viewport" content="width=device-width, initial-scale=1">';
		$h .= '<title>Studio Audit — ' . htmlspecialchars($c->origin(), ENT_QUOTES) . '</title>';
		$h .= '<style>' . $this->css() . '</style></head><body>';
		$h .= '<div class="wrap">';

		$h .= '<header class="hero">';
		$h .= '<div class="hero-top">';
		$h .= '<span class="eyebrow"><i></i>Studio Audit · SEO · Техника · Скорость · Безопасность</span>';
		$h .= '<span class="eyebrow quiet">' . date('d.m.Y H:i') . '</span>';
		$h .= '</div>';
		$h .= '<div class="hero-grid"><div>';
		$h .= '<h1>' . htmlspecialchars($c->origin(), ENT_QUOTES) . '</h1>';
		$h .= '<div class="meta">Обойдено <b>' . count($cr->pages) . '</b> URL за <b>' . round($cr->duration, 2) . ' с</b> · выполнено <b>' . count($results) . '</b> проверок · пройдено <b>' . $passed . '</b> · запросов <b>' . $cr->requests . '</b></div>';
		$h .= '<div class="meta">Режим: <b>' . htmlspecialchars($this->cfg->modeLabel(), ENT_QUOTES) . '</b> · глубина ' . $this->cfg->depth . ' · параллельность ' . $this->cfg->concurrency . '</div>';
		$h .= '</div>';
		$h .= '<div class="score-box"><div class="score-num">' . number_format((float)$score['score'], 1) . ' <small>/ 100</small></div>';
		$h .= '<div class="score-note">' . htmlspecialchars($this->scoreWord((float)$score['score']), ENT_QUOTES) . '</div>';
		$h .= '<div class="score-bar"><i style="width:' . max(2, min(100, (float)$score['score'])) . '%"></i></div>';
		$h .= '<div class="verdict">' . $this->verdictText($bySev, $passed, count($results)) . '</div>';
		$h .= '</div></div></header>';

		// --- навигация (липкая) ---
		$h .= '<nav class="nav"><a href="#priority">Приоритет 1</a><a href="#cats">По направлениям</a><a href="#issues">Все замечания</a><a href="#passed">Пройдено</a><a href="#pages">Страницы</a><span class="sp"></span><input id="search" placeholder="Поиск по правилу, URL, тексту…"></nav>';

		// --- плитки: клик = перейти к замечаниям этой критичности ---
		$h .= '<div class="tiles">';
		foreach (['critical', 'high', 'medium', 'low', 'info'] as $sev) {
			$h .= '<a class="tile sev-' . $sev . '" data-sev="' . $sev . '" href="#issues" title="Показать замечания критичности «' . $sevLabel[$sev] . '»">'
				. '<div class="tile-num">' . $bySev[$sev] . '</div><div class="tile-lbl">' . $sevLabel[$sev] . '</div></a>';
		}
		$h .= '<a class="tile sev-ok" href="#passed" title="Показать пройденные проверки"><div class="tile-num">' . $passed . '</div><div class="tile-lbl">Проверок пройдено</div></a>';
		$h .= '</div>';

		// --- оценка по направлениям: клик = перейти к разделу с этими ошибками ---
		$h .= '<section class="card" id="cats"><div class="sec-top"><p class="eyebrow"><i></i>01 / Оценка по направлениям</p><span class="muted">нажмите на раздел — перейдёте к его замечаниям</span></div><h2>Где слабые места</h2><div class="cats">';
		$catIndex = [];
		$ci = 0;
		foreach ($score['byCat'] as $cat => $data) {
			$sc = (int)$data['score'];
			$cls = $sc >= 85 ? 'g' : ($sc >= 65 ? 'y' : 'r');
			$catIndex[(string)$cat] = $ci++;
			$failed = (int)$data['failed'];
			$h .= '<a class="cat" data-cat="' . htmlspecialchars((string)$cat, ENT_QUOTES) . '" href="#cat-' . $catIndex[(string)$cat] . '">';
			$h .= '<div class="cat-h"><span>' . htmlspecialchars((string)$cat, ENT_QUOTES) . '</span><b>' . $sc . '/100</b></div>';
			$h .= '<div class="bar"><i class="' . $cls . '" style="width:' . max(2, $sc) . '%"></i></div>';
			$h .= '<div class="cat-f">' . $data['rules'] . ' проверок · '
				. ($failed > 0
					? '<span class="bad">' . $failed . ' ' . $this->plural($failed, 'правило', 'правила', 'правил') . ' с замечаниями →</span>'
					: '<span class="good">без замечаний</span>')
				. '</div></a>';
		}
		$h .= '</div></section>';

		// --- приоритет 1: с чего начать устранение ---
		$h .= '<section class="card" id="priority"><div class="sec-top"><p class="eyebrow"><i></i>02 / Приоритет 1 — с чего начать</p>'
			. '<button class="copy" id="copyList" type="button">Скопировать план</button></div><h2>Что закрыть в первую очередь <span class="muted">(' . count($priority) . ')</span></h2>';
		if (!$priority) {
			$h .= '<div class="empty">Критичных и высоких замечаний нет — сайт в порядке.</div>';
		} else {
			$h .= '<div class="plan">';
			$planText = [];
			foreach (array_slice($priority, 0, 20) as $n => $r) {
				$first = $r['violations'][0] ?? null;
				$where = $first !== null && $first['url'] !== null ? Ctx::pathKey($first['url']) : 'сайт целиком';
				$h .= '<a class="plan-item sev-' . $r['sev'] . '" id="rule-' . htmlspecialchars($r['id'], ENT_QUOTES) . '" href="#cat-' . ($catIndex[$r['cat']] ?? 0) . '">';
				$h .= '<div class="plan-h"><span class="n">' . ($n + 1) . '</span>' . $this->sevBadgeHtml($r['sev'])
					. '<code>' . htmlspecialchars($r['id'], ENT_QUOTES) . '</code>'
					. '<span class="muted">' . htmlspecialchars((string)$r['cat'], ENT_QUOTES) . '</span></div>';
				$h .= '<div class="plan-t">' . htmlspecialchars((string)$r['title'], ENT_QUOTES) . '</div>';
				$h .= '<div class="plan-w"><b>Где:</b> ' . htmlspecialchars((string)$where, ENT_QUOTES)
					. ' <span class="muted">· затронуто ' . $r['failed'] . ' из ' . $r['checked'] . '</span></div>';
				$found = $r['violations'][0]['found'] ?? [];
				if ($found) {
					$f = $found[0];
					$h .= '<div class="plan-w"><b>Найдено на:</b> <span class="path">' . htmlspecialchars(Ctx::pathKey($f['from']), ENT_QUOTES) . '</span>'
						. ($f['anchor'] !== '' ? ' <span class="muted">— «' . htmlspecialchars(mb_substr($f['anchor'], 0, 40), ENT_QUOTES) . '»</span>' : '')
						. ' <span class="muted">(' . htmlspecialchars((string)$f['zone'], ENT_QUOTES) . ')</span></div>';
				}
				if ($r['impact'] !== '') {
					$h .= '<div class="plan-i"><b>Влияет:</b> ' . htmlspecialchars((string)$r['impact'], ENT_QUOTES) . '</div>';
				}
				if ($r['hint'] !== '') {
					$h .= '<div class="plan-f"><b>Что делать:</b> ' . htmlspecialchars((string)$r['hint'], ENT_QUOTES) . '</div>';
				}
				if ($r['sources']) {
					$h .= '<div class="srcs">';
					foreach ($r['sources'] as $src) {
						$h .= '<a href="' . htmlspecialchars($src, ENT_QUOTES) . '" target="_blank" rel="noopener">источник ↗</a>';
					}
					$h .= '</div>';
				}
				$h .= '</a>';
				$planText[] = sprintf(
					"[%s] %s — %s\n  Где: %s (%d из %d)\n  Влияет: %s\n  Что делать: %s\n  Источник: %s",
					$sevLabel[$r['sev']],
					$r['id'],
					$r['title'],
					$where,
					$r['failed'],
					$r['checked'],
					$r['impact'],
					$r['hint'],
					implode(' ', $r['sources'])
				);
			}
			$h .= '</div>';
			$h .= '<textarea id="planText" hidden>' . htmlspecialchars(implode("\n\n", $planText), ENT_QUOTES) . '</textarea>';
		}
		$h .= '</section>';

		// --- замечания по разделам ---
		$h .= '<section class="card" id="issues"><div class="sec-top"><p class="eyebrow"><i></i>03 / Все замечания</p><span class="muted">' . count($issues) . ' находок</span></div><h2>Разбор по разделам</h2>';
		$h .= '<div class="filters"><button class="fbtn active" data-f="all" type="button">Все</button>';
		foreach ($cats as $cat) {
			$h .= '<button class="fbtn" data-f="' . htmlspecialchars((string)$cat, ENT_QUOTES) . '" type="button">' . htmlspecialchars((string)$cat, ENT_QUOTES) . '</button>';
		}
		$h .= '<span class="sp"></span><span class="muted" id="fstate">показано ' . count($issues) . '</span>'
			. '<button class="fbtn" id="toggleFull" type="button" title="Развернуть все списки на всю длину">Полный вид</button></div>';

		$byCatIssues = [];
		foreach ($issues as $i) {
			$byCatIssues[$i['cat']][] = $i;
		}
		foreach ($cats as $cat) {
			$list = $byCatIssues[$cat] ?? [];
			$h .= '<div class="cat-block" data-catblock="' . htmlspecialchars((string)$cat, ENT_QUOTES) . '">';
			$h .= '<h3 class="h3" id="cat-' . ($catIndex[(string)$cat] ?? 0) . '">' . htmlspecialchars((string)$cat, ENT_QUOTES)
				. ' <span class="muted">· ' . count($list) . ' ' . $this->plural(count($list), 'замечание', 'замечания', 'замечаний') . '</span></h3>';
			if (!$list) {
				$h .= '<div class="empty small">В этом разделе замечаний нет.</div></div>';
				continue;
			}
			$h .= '<div class="scrollbox"><table class="tbl issues"><thead><tr><th></th><th>Критичность</th><th>Правило</th><th>Что не так</th><th>Где</th><th>Влияет</th></tr></thead><tbody>';
			foreach ($list as $i) {
				$h .= '<tr class="irow" data-cat="' . htmlspecialchars($i['cat'], ENT_QUOTES) . '" data-sev="' . $i['sev'] . '"'
					. ' data-text="' . htmlspecialchars(strtolower($i['rule'] . ' ' . $i['path'] . ' ' . $i['detail'] . ' ' . $i['title']), ENT_QUOTES) . '">';
				$h .= '<td class="exp"><span class="chev">▸</span></td>';
				$h .= '<td><span class="badge sev-' . $i['sev'] . '">' . $sevLabel[$i['sev']] . '</span></td>';
				$h .= '<td><code>' . htmlspecialchars($i['rule'], ENT_QUOTES) . '</code><div class="muted">' . htmlspecialchars($i['title'], ENT_QUOTES) . '</div></td>';
				$h .= '<td>' . htmlspecialchars($i['detail'], ENT_QUOTES) . '</td>';
				$h .= '<td class="path">' . htmlspecialchars($i['path'], ENT_QUOTES) . '</td>';
				$h .= '<td class="impact">' . htmlspecialchars($i['impact'], ENT_QUOTES) . '</td>';
				$h .= '</tr>';
				$h .= '<tr class="detail" hidden><td colspan="6"><div class="det-grid">';
				$h .= '<div><b>Как исправить:</b> ' . htmlspecialchars($i['hint'], ENT_QUOTES) . '</div>';
				if (!empty($i['found'])) {
					$h .= '<div class="found"><b>Найдено на страницах:</b><ul>';
					foreach ($i['found'] as $f) {
						$h .= '<li><span class="path">' . htmlspecialchars(Ctx::pathKey($f['from']), ENT_QUOTES) . '</span>'
							. ($f['anchor'] !== '' ? ' <span class="muted">— «' . htmlspecialchars(mb_substr($f['anchor'], 0, 50), ENT_QUOTES) . '»</span>' : '')
							. ' <span class="muted">(' . htmlspecialchars((string)$f['zone'], ENT_QUOTES) . ')</span></li>';
					}
					$h .= '</ul></div>';
				}
				if ($i['sources']) {
					$h .= '<div class="srcs">';
					foreach ($i['sources'] as $src) {
						$h .= '<a href="' . htmlspecialchars($src, ENT_QUOTES) . '" target="_blank" rel="noopener">источник ↗</a>';
					}
					$h .= '</div>';
				}
				$h .= '<div class="muted">нажмите на строку ещё раз, чтобы свернуть</div>';
				$h .= '</div></td></tr>';
			}
			$h .= '</tbody></table></div></div>';
		}
		$h .= '</section>';

		// --- аналитический разбор: ядро, кластеры, конкуренты ---
		if (!empty($insight['page'])) {
			$plan = $insight['page'];
			$core = $plan['core'];
			$h .= '<section class="card" id="plan"><div class="sec-top"><p class="eyebrow"><i></i>06 / Семантическое ядро и план продвижения</p><span class="muted">' . htmlspecialchars(Ctx::pathKey($plan['url']), ENT_QUOTES) . '</span></div>';
			$h .= '<h2>Ядро страницы и что делать</h2>';
			$h .= '<div class="core-head"><div class="core-primary"><span class="muted">основной термин</span><b>' . htmlspecialchars($core['primary'], ENT_QUOTES) . '</b></div>';
			$h .= '<div class="core-sup">';
			foreach (array_slice($core['support'], 0, 8) as $s) {
				$h .= '<span class="chip-term" title="' . htmlspecialchars(implode(', ', array_map(static fn($b) => $b . ' × ' . $s['blocks'][$b], array_keys($s['blocks']))), ENT_QUOTES) . '">' . htmlspecialchars($s['term'], ENT_QUOTES) . '</span>';
			}
			$h .= '</div></div>';

			$h .= '<div class="two-col">';
			$h .= '<div><h4 class="mini">Покрытие ядра по блокам</h4><table class="tbl sm"><thead><tr><th>Термин</th><th>title</th><th>H1</th><th>description</th><th>текст</th></tr></thead><tbody>';
			foreach ($core['coverage'] as $t => $cov) {
				$h .= '<tr><td>' . htmlspecialchars((string)$t, ENT_QUOTES) . '</td>';
				foreach (['title', 'h1', 'description', 'текст'] as $k) {
					$h .= '<td class="ctr">' . (!empty($cov[$k]) ? '<span class="ok">✓</span>' : '<span class="no">✗</span>') . '</td>';
				}
				$h .= '</tr>';
			}
			$h .= '</tbody></table></div>';
			$h .= '<div><h4 class="mini">Черновики (из реальных терминов)</h4>';
			$h .= '<div class="draft"><span>title</span><p>' . htmlspecialchars($core['titleDraft'], ENT_QUOTES) . ' <em>' . $core['titleLen'] . ' симв.</em></p></div>';
			$h .= '<div class="draft"><span>description</span><p>' . htmlspecialchars(mb_substr($core['descDraft'], 0, 160), ENT_QUOTES) . ' <em>' . $core['descLen'] . ' симв.</em></p></div>';
			$h .= '<div class="draft muted"><span>сейчас</span><p>' . htmlspecialchars(mb_substr($core['currentTitle'], 0, 90), ENT_QUOTES) . '</p>';
			$h .= '<p>' . htmlspecialchars(mb_substr($core['currentDesc'], 0, 120), ENT_QUOTES) . '</p></div>';
			$h .= '</div></div>';

			if (!empty($core['gap'])) {
				$h .= '<h4 class="mini">Пробелы: термины, которых нет на странице, но есть в окружении</h4><div class="chips">';
				foreach ($core['gap'] as $g) {
					$h .= '<span class="chip-term warn" title="' . htmlspecialchars(implode(', ', $g['from']), ENT_QUOTES) . '">' . htmlspecialchars($g['term'], ENT_QUOTES) . '</span>';
				}
				$h .= '</div>';
			}

			if (!empty($plan['diagnostics'])) {
				$h .= '<h4 class="mini">Почему страница может не расти</h4><div class="diag">';
				foreach ($plan['diagnostics'] as $d) {
					$h .= '<div class="diag-item"><b>' . htmlspecialchars($d['why'], ENT_QUOTES) . '</b>'
						. '<div class="muted">факт: ' . htmlspecialchars($d['fact'], ENT_QUOTES) . '</div>'
						. '<div class="fix">действие: ' . htmlspecialchars($d['fix'], ENT_QUOTES) . '</div></div>';
				}
				$h .= '</div>';
			}

			if (!empty($plan['readiness'])) {
				$r = $plan['readiness'];
				$h .= '<h4 class="mini">Готовность к топ-10: ' . $r['ready'] . '% (' . $r['tech'] . ' из ' . $r['techTotal'] . ' технических условий)</h4>';
				$h .= '<table class="tbl sm"><tbody>';
				foreach ($r['items'] as $i) {
					$mark = $i['external'] ? '<span class="ext">◐</span>' : ($i['ok'] ? '<span class="ok">✓</span>' : '<span class="no">✗</span>');
					$h .= '<tr><td class="ctr">' . $mark . '</td><td><b>' . htmlspecialchars($i['title'], ENT_QUOTES) . '</b><div class="muted">' . htmlspecialchars($i['fact'], ENT_QUOTES) . '</div>';
					if (!$i['ok']) {
						$h .= '<div class="fix">действие: ' . htmlspecialchars($i['fix'], ENT_QUOTES) . '</div>';
					}
					$h .= '</td></tr>';
				}
				$h .= '</tbody></table>';
				$h .= '<p class="note">Техническая готовность ≠ позиция в выдаче. Топ-3/топ-10 определяет авторитет домена: внешние ссылки с авторитетных доменов,brand-запросы, возраст и история домена. Это измеряется в Яндекс.Вебмастере и Ahrefs/Semrush, а не HTTP-обходом — инструмент закрывает ту часть, которую можно доказать и починить.</p>';
			}

			if (!empty($plan['linkSources'])) {
				$h .= '<h4 class="mini">Откуда добавить ссылки</h4><ul class="link-src">';
				foreach ($plan['linkSources'] as $ls) {
					$h .= '<li><span class="path">' . htmlspecialchars($ls['url'], ENT_QUOTES) . '</span> <span class="muted">— ' . htmlspecialchars(mb_substr($ls['title'], 0, 60), ENT_QUOTES) . '</span></li>';
				}
				$h .= '</ul>';
			}
			$h .= '</section>';
		}

		if (!empty($insight['clusters'])) {
			$h .= '<section class="card" id="clusters"><div class="sec-top"><p class="eyebrow"><i></i>07 / Тематические кластеры</p><span class="muted">' . count($insight['clusters']) . ' групп</span></div><h2>Карта тем сайта</h2><div class="clusters">';
			foreach (array_slice($insight['clusters'], 0, 20) as $i => $cl) {
				$h .= '<div class="cluster"><div class="cl-h"><b>' . ($i + 1) . '.</b> <span>' . $cl['size'] . ' стр.</span></div>';
				$h .= '<div class="chips">' . implode('', array_map(static fn($t) => '<span class="chip-term">' . htmlspecialchars((string)$t, ENT_QUOTES) . '</span>', array_slice($cl['terms'], 0, 8))) . '</div>';
				$h .= '<div class="muted cl-pages">' . implode(' · ', array_slice(array_map(static fn($u) => htmlspecialchars(Ctx::pathKey($u), ENT_QUOTES), $cl['pages']), 0, 6)) . '</div>';
				if ($cl['gaps']) {
					$h .= '<div class="muted">пробелы: ' . htmlspecialchars(implode(', ', array_slice(array_keys($cl['gaps']), 0, 6)), ENT_QUOTES) . '</div>';
				}
				$h .= '</div>';
			}
			$h .= '</div></section>';
		}

		if (!empty($insight['competitors'])) {
			$h .= '<section class="card" id="competitors"><div class="sec-top"><p class="eyebrow"><i></i>08 / Сравнение с конкурентами</p><span class="muted">те же 152 правила</span></div><h2>Разрыв с конкурентами</h2>';
			foreach ($insight['competitors'] as $comp) {
				$h .= '<h4 class="mini">' . htmlspecialchars($comp['url'], ENT_QUOTES) . ' — ' . $comp['pages'] . ' стр., оценка ' . $comp['score'] . '/100</h4>';
				if ($comp['better']) {
					$h .= '<div class="cmp-better"><b>у конкурента лучше:</b> ' . htmlspecialchars(implode(', ', array_slice($comp['better'], 0, 20)), ENT_QUOTES) . '</div>';
				}
				if ($comp['worse']) {
					$h .= '<div class="cmp-worse"><b>у нас лучше:</b> ' . htmlspecialchars(implode(', ', array_slice($comp['worse'], 0, 12)), ENT_QUOTES) . '</div>';
				}
			}
			$h .= '</section>';
		}

		$h .= '<section class="card" id="passed"><div class="sec-top"><p class="eyebrow"><i></i>04 / Проверено без замечаний</p><span class="muted">' . $passed . ' проверок</span></div><h2>Что в порядке</h2><div class="passed">';
		foreach ($results as $r) {
			if (!$r['pass']) {
				continue;
			}
			$h .= '<div class="ok-row" data-cat="' . htmlspecialchars((string)$r['cat'], ENT_QUOTES) . '" title="' . htmlspecialchars((string)$r['impact'], ENT_QUOTES) . '"><span class="tick">✓</span><span class="ok-title">' . htmlspecialchars((string)$r['title'], ENT_QUOTES) . '</span><span class="muted">' . htmlspecialchars((string)$r['cat'], ENT_QUOTES) . ' · ' . $r['checked'] . ' шт.</span></div>';
		}
		$h .= '</div></section>';

		$h .= '<section class="card" id="pages"><div class="sec-top"><p class="eyebrow"><i></i>05 / Страницы</p><span class="muted">' . count($cr->pages) . ' URL</span></div><h2>Обойдённые страницы</h2><div class="scrollbox"><table class="tbl pages"><thead><tr><th>Путь</th><th>Код</th><th>Глубина</th><th>Вход</th><th>Слов</th><th>КБ</th><th>TTFB</th><th>Title</th></tr></thead><tbody>';
		foreach ($cr->pages as $url => $p) {
			$code = (int)$p['status'];
			$cls = $code === 200 ? 'g' : ($code >= 400 ? 'r' : 'y');
			$h .= '<tr><td class="path">' . htmlspecialchars(Ctx::pathKey($url), ENT_QUOTES) . '</td>';
			$h .= '<td><span class="code ' . $cls . '">' . $code . '</span></td>';
			$h .= '<td>' . (int)$p['depth'] . '</td>';
			$h .= '<td>' . ($cr->inlinks[$url]['count'] ?? 0) . '</td>';
			$h .= '<td>' . (int)$p['html']['words'] . '</td>';
			$h .= '<td>' . round((int)$p['bodySize'] / 1024, 1) . '</td>';
			$h .= '<td>' . (int)round((float)$p['ttfb'] * 1000) . ' мс</td>';
			$h .= '<td class="muted">' . htmlspecialchars(mb_substr((string)$p['html']['title'], 0, 90), ENT_QUOTES) . '</td></tr>';
		}
		$h .= '</tbody></table></div></section>';

		$h .= '<footer class="foot">Studio Audit Crawler 2.0.0 · сгенерировано ' . date('d.m.Y H:i:s') . ' · проверок выполнено: ' . count($results) . '</footer>';
		$h .= '</div>';
		$h .= '<script>' . $this->js() . '</script></body></html>';
		return $h;
	}

	/** Бейдж критичности для HTML-отчёта. */
	private function sevBadgeHtml(string $sev): string
	{
		$label = match ($sev) {
			'critical' => 'КРИТИЧНО',
			'high'     => 'ВЫСОКАЯ',
			'medium'   => 'СРЕДНЯЯ',
			'low'      => 'НИЗКАЯ',
			default    => 'ИНФО',
		};
		return '<span class="badge sev-' . $sev . '">' . $label . '</span>';
	}


	private function css(): string
	{
		return <<<'CSS'
/* ==========================================================================
   Studio Audit — оформление в стилистике сайта (тёплая палитра, бронза,
   пилюли-eyebrow, крупные скругления, мягкие тени). Шрифты и цвета — те же,
   что на сайте: Manrope / DM Sans, --brand #876b55, --ink #171310.
   ========================================================================== */
:root{
  --brand-50:#f8f4ef; --brand-100:#f0e6da; --brand-200:#e3d1bd; --brand-300:#d2b89d;
  --brand-400:#c7ab94; --brand-500:#b1907a; --brand-600:#9a7c66; --brand-700:#876b55;
  --brand-800:#6d5744; --brand-900:#554433; --brand-950:#3f3227;
  --ink:#171310; --ink-soft:#44403C; --ink-faint:#7a736c;
  --line:#e7e2d9; --card:#fff; --card-2:#fdfbf8;
  --r-sm:14px; --r-md:20px; --r-lg:30px; --r-pill:999px;
  --sh-card:0 12px 32px rgba(23,19,16,.07);
  --sh-card-lg:0 22px 54px rgba(23,19,16,.10);
  --sh-brand:0 10px 28px rgba(135,107,85,.22);
  --tr:.25s cubic-bezier(.4,0,.2,1);
  --sev-critical:#a8452f; --sev-high:#876b55; --sev-medium:#b1907a; --sev-low:#9a7c66; --sev-info:#c7ab94;
}
*{box-sizing:border-box}
html{scroll-behavior:smooth;scrollbar-color:var(--brand-700) var(--brand-50);scrollbar-width:thin}
body{
  margin:0; color:var(--ink); background:var(--brand-50);
  font:16px/1.65 'DM Sans Variable','DM Sans',Manrope,Inter,-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,Helvetica,Arial,sans-serif;
  -webkit-font-smoothing:antialiased; text-rendering:optimizeLegibility;
}
::selection{background:var(--brand-200);color:#2b1c10}
::-webkit-scrollbar{width:10px}
::-webkit-scrollbar-track{background:var(--brand-50)}
::-webkit-scrollbar-thumb{background:linear-gradient(180deg,var(--brand-700),var(--brand-950));border-radius:var(--r-pill);border:2px solid var(--brand-50)}
a{color:inherit}
.wrap{max-width:1280px;margin:0 auto;padding:28px 22px 64px}

/* ---------- шапка ---------- */
.hero{
  position:relative; overflow:hidden; background:var(--card);
  border:1px solid var(--line); border-radius:var(--r-lg); padding:38px 40px;
  box-shadow:var(--sh-card);
}
.hero::after{
  content:''; position:absolute; right:-90px; top:-120px; width:420px; height:420px; border-radius:50%;
  background:radial-gradient(circle at 30% 30%,rgba(226,205,182,.55),rgba(248,244,239,0) 68%); pointer-events:none;
}
.hero-top{display:flex;flex-wrap:wrap;gap:14px;align-items:center;justify-content:space-between;margin-bottom:26px}
.eyebrow{
  display:inline-flex; align-items:center; gap:9px; background:var(--card);
  border:1px solid var(--brand-200); color:var(--brand-900); border-radius:var(--r-pill);
  padding:7px 16px; font-size:11px; font-weight:700; letter-spacing:.12em; text-transform:uppercase;
  box-shadow:0 2px 8px rgba(135,107,85,.08);
}
.eyebrow i{width:7px;height:7px;border-radius:50%;background:var(--brand-500);font-style:normal}
.eyebrow.quiet{background:var(--brand-50);box-shadow:none;color:var(--ink-soft)}
.hero h1{margin:0 0 10px;font-size:clamp(28px,3.4vw,44px);font-weight:800;letter-spacing:-.022em;line-height:1.06}
.hero .meta{color:var(--ink-soft);font-size:14px}
.hero .meta b{color:var(--brand-800);font-weight:600}
.hero-grid{display:grid;grid-template-columns:1.35fr .65fr;gap:34px;align-items:center}
.score-box{background:var(--brand-50);border:1px solid var(--brand-200);border-radius:var(--r-md);padding:22px 24px}
.score-num{font-size:clamp(40px,5vw,62px);font-weight:800;letter-spacing:-.03em;line-height:1;color:var(--brand-800)}
.score-num small{font-size:16px;font-weight:600;color:var(--ink-faint);letter-spacing:0}
.score-note{margin-top:8px;color:var(--ink-soft);font-size:14px}
.score-bar{margin-top:16px;height:6px;border-radius:var(--r-pill);background:var(--brand-200);overflow:hidden}
.score-bar i{display:block;height:100%;border-radius:var(--r-pill);background:linear-gradient(90deg,var(--brand-500),var(--brand-800))}
.verdict{margin-top:14px;font-size:13.5px;color:var(--ink-soft)}
.verdict b{color:var(--ink)}

/* ---------- навигация ---------- */
.nav{
  position:sticky; top:0; z-index:20; display:flex; gap:8px; align-items:center; flex-wrap:wrap;
  margin:18px 0 22px; padding:10px 12px; background:rgba(248,244,239,.92); backdrop-filter:blur(10px);
  border:1px solid var(--line); border-radius:var(--r-pill); box-shadow:0 6px 18px rgba(23,19,16,.05);
}
.nav a{
  text-decoration:none; color:var(--ink-soft); font-size:13.5px; font-weight:600; padding:8px 15px;
  border-radius:var(--r-pill); transition:var(--tr);
}
.nav a:hover{background:var(--card);color:var(--ink);box-shadow:0 2px 10px rgba(23,19,16,.07)}
.nav .sp{flex:1}
.nav input{
  flex:0 1 300px; background:var(--card); border:1px solid var(--line); border-radius:var(--r-pill);
  padding:9px 16px; font:inherit; font-size:13.5px; color:var(--ink); transition:var(--tr);
}
.nav input:focus{outline:none;border-color:var(--brand-400);box-shadow:0 0 0 3px rgba(135,107,85,.12)}

/* ---------- секции ---------- */
.card{background:var(--card);border:1px solid var(--line);border-radius:var(--r-lg);padding:30px 32px;margin-bottom:22px;box-shadow:var(--sh-card)}
.sec-top{display:flex;align-items:center;justify-content:space-between;gap:16px;flex-wrap:wrap;margin-bottom:22px}
.card h2{margin:0;font-size:clamp(22px,2.4vw,32px);font-weight:800;letter-spacing:-.02em;line-height:1.12}
.card h2 .muted{font-weight:500;font-size:14px;letter-spacing:0}
.h3{font-size:17px;font-weight:700;letter-spacing:-.01em;margin:26px 0 10px;display:flex;gap:10px;align-items:baseline;scroll-margin-top:86px}
.h3 .muted{font-weight:500;font-size:13px}
.muted{color:var(--ink-faint)}

/* ---------- плитки критичности ---------- */
.tiles{display:grid;grid-template-columns:repeat(6,1fr);gap:14px;margin-bottom:22px}
.tile{
  display:block; text-decoration:none; color:inherit; background:var(--card); border:1px solid var(--line);
  border-top:3px solid var(--brand-300); border-radius:var(--r-md); padding:20px 18px; text-align:center;
  transition:var(--tr); box-shadow:var(--sh-card);
}
.tile:hover{transform:translateY(-3px);box-shadow:var(--sh-card-lg);border-color:var(--brand-300)}
.tile-num{font-size:32px;font-weight:800;letter-spacing:-.03em;line-height:1;color:var(--ink)}
.tile-lbl{margin-top:8px;color:var(--ink-soft);font-size:11px;font-weight:700;letter-spacing:.12em;text-transform:uppercase}
.tile.sev-critical{border-top-color:var(--sev-critical)} .tile.sev-critical .tile-num{color:var(--sev-critical)}
.tile.sev-high{border-top-color:var(--sev-high)} .tile.sev-high .tile-num{color:var(--sev-high)}
.tile.sev-medium{border-top-color:var(--sev-medium)} .tile.sev-medium .tile-num{color:var(--sev-medium)}
.tile.sev-low{border-top-color:var(--sev-low)} .tile.sev-low .tile-num{color:var(--sev-low)}
.tile.sev-info{border-top-color:var(--sev-info)} .tile.sev-info .tile-num{color:var(--sev-info)}
.tile.sev-ok{border-top-color:var(--brand-600)} .tile.sev-ok .tile-num{color:var(--brand-700)}

/* ---------- направления ---------- */
.cats{display:grid;grid-template-columns:repeat(auto-fit,minmax(272px,1fr));gap:14px}
.cat{
  display:block; text-decoration:none; color:inherit; background:linear-gradient(180deg,#fff 0,#fdfbf8 100%);
  border:1px solid var(--line); border-radius:var(--r-lg); padding:20px 22px; transition:var(--tr);
}
.cat:hover{border-color:var(--brand-300);transform:translateY(-2px);box-shadow:var(--sh-card-lg)}
.cat-h{display:flex;justify-content:space-between;align-items:baseline;gap:10px;font-weight:700;letter-spacing:-.01em}
.cat-h b{font-variant-numeric:tabular-nums;color:var(--brand-800);font-size:15px}
.bar{height:5px;background:var(--brand-100);border-radius:var(--r-pill);overflow:hidden;margin:12px 0 9px}
.bar i{display:block;height:100%;border-radius:var(--r-pill);background:linear-gradient(90deg,var(--brand-400),var(--brand-700))}
.bar i.y{background:linear-gradient(90deg,var(--brand-300),var(--brand-500))}
.bar i.r{background:linear-gradient(90deg,var(--brand-500),var(--brand-900))}
.cat-f{font-size:12.5px;color:var(--ink-faint)}
.cat-f .bad{color:var(--sev-critical);font-weight:600}
.cat-f .good{color:var(--brand-700);font-weight:600}

/* ---------- план устранения ---------- */
.copy{
  background:var(--card); border:1px solid var(--brand-300); color:var(--brand-800); border-radius:var(--r-pill);
  padding:8px 18px; font:inherit; font-size:12.5px; font-weight:700; letter-spacing:.04em; cursor:pointer; transition:var(--tr);
}
.copy:hover{background:var(--brand-700);border-color:var(--brand-700);color:#fff;box-shadow:var(--sh-brand)}
.plan{display:grid;grid-template-columns:repeat(auto-fit,minmax(380px,1fr));gap:16px}
.plan-item{
  display:block; text-decoration:none; color:inherit; background:var(--card-2); border:1px solid var(--line);
  border-left:3px solid var(--brand-500); border-radius:var(--r-md); padding:20px 22px; transition:var(--tr);
}
.plan-item:hover{border-color:var(--brand-300);border-left-color:var(--brand-700);background:var(--card);box-shadow:var(--sh-card);transform:translateX(2px)}
.plan-item.sev-critical{border-left-color:var(--sev-critical)}
.plan-item.sev-high{border-left-color:var(--sev-high)}
.plan-h{display:flex;align-items:center;gap:10px;flex-wrap:wrap;margin-bottom:9px}
.plan-h .n{
  width:24px;height:24px;border-radius:var(--r-pill);background:var(--brand-700);color:#fff;
  font-size:11.5px;font-weight:800;display:inline-flex;align-items:center;justify-content:center;letter-spacing:0;
}
.plan-t{font-weight:700;margin-bottom:8px;letter-spacing:-.01em}
.plan-w,.plan-i,.plan-f{font-size:13px;color:var(--ink-soft);margin-bottom:5px;line-height:1.55}
.plan-i{color:var(--ink-soft)}
.plan-f{color:var(--brand-800)}
.plan-item b{color:var(--ink);font-weight:600}
code{background:var(--brand-50);border:1px solid var(--line);border-radius:8px;padding:2px 8px;font-size:12px;color:var(--brand-800);font-family:ui-monospace,SFMono-Regular,Menlo,monospace}

/* ---------- фильтры ---------- */
.filters{display:flex;gap:8px;flex-wrap:wrap;align-items:center;margin-bottom:18px}
.fbtn{
  background:var(--card); border:1px solid var(--line); color:var(--ink-soft); border-radius:var(--r-pill);
  padding:8px 16px; font:inherit; font-size:12.5px; font-weight:600; cursor:pointer; transition:var(--tr);
}
.fbtn:hover{border-color:var(--brand-300);color:var(--ink)}
.fbtn.active{background:var(--brand-700);border-color:var(--brand-700);color:#fff;box-shadow:var(--sh-brand)}
.filters .sp{flex:1}

/* ---------- таблицы ---------- */
.tbl{width:100%;border-collapse:collapse;font-size:13.5px}
.tbl th{
  text-align:left; color:var(--ink-faint); font-weight:700; font-size:11px; letter-spacing:.1em; text-transform:uppercase;
  border-bottom:1px solid var(--line); padding:12px 12px; background:var(--card-2);
}
.tbl th:first-child{border-radius:var(--r-sm) 0 0 0}
.tbl th:last-child{border-radius:0 var(--r-sm) 0 0}
.tbl td{border-bottom:1px solid #f1ece4;padding:13px 12px;vertical-align:top}
.tbl tbody tr:last-child td{border-bottom:none}
.tbl tr:hover td{background:#fdfbf8}
.badge{display:inline-block;padding:4px 11px;border-radius:var(--r-pill);font-size:11px;font-weight:700;letter-spacing:.06em;white-space:nowrap}
.badge.sev-critical{background:#f7e9e4;color:var(--sev-critical)}
.badge.sev-high{background:var(--brand-100);color:var(--brand-800)}
.badge.sev-medium{background:var(--brand-50);color:var(--brand-700)}
.badge.sev-low{background:#fbf8f4;color:var(--ink-soft)}
.badge.sev-info{background:#f7f4f0;color:var(--ink-faint)}
.path{font-family:ui-monospace,SFMono-Regular,Menlo,monospace;color:var(--ink);word-break:break-all;font-size:12.5px}
.impact{color:var(--ink-soft);max-width:340px;line-height:1.5}
.hint{color:var(--brand-800)}
.code{font-weight:700;font-variant-numeric:tabular-nums}
.code.g{color:var(--brand-700)}.code.y{color:var(--brand-600)}.code.r{color:var(--sev-critical)}
.srcs{margin-top:7px;display:flex;gap:6px;flex-wrap:wrap}
.srcs a{
  font-size:11px; text-decoration:none; color:var(--brand-700); border:1px solid var(--brand-200);
  background:var(--brand-50); border-radius:var(--r-pill); padding:3px 10px; transition:var(--tr);
}
.srcs a:hover{background:var(--brand-700);border-color:var(--brand-700);color:#fff}

/* ---------- раскрытие строки ---------- */
tr.irow{cursor:pointer}
tr.irow td.exp{width:26px;color:var(--brand-400)}
tr.irow .chev{display:inline-block;transition:var(--tr)}
tr.irow.open .chev{transform:rotate(90deg);color:var(--brand-700)}
tr.detail td{background:var(--brand-50);padding:16px 20px 18px}
.det-grid{font-size:13.5px;line-height:1.6;color:var(--ink-soft)}
.det-grid b{color:var(--ink)}
.found{margin-top:10px}
.found ul{margin:6px 0 0;padding-left:18px}
.found li{margin-bottom:4px}

/* ---------- аналитический слой ---------- */
.core-head{display:flex;flex-wrap:wrap;gap:20px;align-items:center;margin-bottom:18px}
.core-primary{display:flex;flex-direction:column;gap:4px;background:var(--brand-50);border:1px solid var(--brand-200);border-radius:var(--r-md);padding:12px 20px}
.core-primary span{font-size:11px;letter-spacing:.1em;text-transform:uppercase;color:var(--ink-faint)}
.core-primary b{font-size:22px;letter-spacing:-.01em;color:var(--brand-800)}
.core-sup,.chips{display:flex;flex-wrap:wrap;gap:7px}
.chip-term{display:inline-block;background:var(--card);border:1px solid var(--brand-200);color:var(--brand-800);border-radius:var(--r-pill);padding:5px 13px;font-size:12.5px;font-weight:600}
.chip-term.warn{border-color:#e3c9b8;color:var(--sev-critical);background:#fdf6f3}
.two-col{display:grid;grid-template-columns:1fr 1fr;gap:22px;margin-bottom:20px}
.mini{font-size:12px;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:var(--ink-faint);margin:22px 0 10px}
.tbl.sm{font-size:12.5px}
.tbl.sm th,.tbl.sm td{padding:8px 10px}
.tbl.sm td.ctr{text-align:center;width:74px}
.ok{color:var(--brand-700);font-weight:700}
.no{color:var(--sev-critical);font-weight:700}
.ext{color:var(--brand-600);font-weight:700}
.draft{background:var(--brand-50);border:1px solid var(--line);border-radius:var(--r-sm);padding:11px 14px;margin-bottom:9px}
.draft span{display:block;font-size:10.5px;letter-spacing:.1em;text-transform:uppercase;color:var(--ink-faint);margin-bottom:4px}
.draft p{margin:0;font-size:13.5px;color:var(--ink)}
.draft em{font-style:normal;color:var(--ink-faint);font-size:12px}
.diag{display:grid;grid-template-columns:repeat(auto-fit,minmax(320px,1fr));gap:12px}
.diag-item{background:var(--card-2);border:1px solid var(--line);border-left:3px solid var(--brand-500);border-radius:var(--r-sm);padding:13px 16px}
.diag-item b{display:block;margin-bottom:5px}
.fix{color:var(--brand-800);font-size:13px;margin-top:3px}
.note{margin-top:14px;background:var(--brand-50);border:1px solid var(--brand-200);border-radius:var(--r-sm);padding:13px 16px;font-size:13px;color:var(--ink-soft)}
.link-src{margin:0;padding-left:18px;font-size:13px}
.link-src li{margin-bottom:4px}
.clusters{display:grid;grid-template-columns:repeat(auto-fit,minmax(300px,1fr));gap:14px}
.cluster{background:var(--card-2);border:1px solid var(--line);border-radius:var(--r-md);padding:16px 18px}
.cl-h{display:flex;gap:8px;align-items:baseline;margin-bottom:10px}
.cl-h b{color:var(--brand-700)}
.cl-pages{margin-top:9px;font-size:12px;word-break:break-word}
.cmp-better{background:#fdf6f3;border:1px solid #e3c9b8;border-radius:var(--r-sm);padding:12px 15px;font-size:13px;margin-bottom:9px}
.cmp-worse{background:var(--brand-50);border:1px solid var(--brand-200);border-radius:var(--r-sm);padding:12px 15px;font-size:13px}

/* ---------- прокручиваемые блоки ---------- */
.scrollbox{
  max-height:min(52vh,460px); overflow:auto; overscroll-behavior:contain;
  border:1px solid var(--line); border-radius:var(--r-md); background:var(--card);
  scrollbar-width:thin; scrollbar-color:var(--brand-400) var(--brand-50);
}
.scrollbox .tbl{min-width:780px}
.scrollbox .tbl th{position:sticky;top:0;z-index:2;background:var(--card-2);box-shadow:0 1px 0 var(--line)}
.scrollbox::-webkit-scrollbar{width:9px;height:9px}
.scrollbox::-webkit-scrollbar-track{background:var(--brand-50);border-radius:var(--r-pill)}
.scrollbox::-webkit-scrollbar-thumb{background:var(--brand-300);border-radius:var(--r-pill)}
.scrollbox::-webkit-scrollbar-thumb:hover{background:var(--brand-500)}
#issues.full .scrollbox{max-height:none}
#issues.full .scrollbox .tbl th{position:static;box-shadow:none}
section{scroll-margin-top:88px}

/* ---------- пройденные проверки ---------- */
.passed{max-height:min(52vh,460px);overflow:auto;overscroll-behavior:contain;padding-right:6px}
.ok-row{display:flex;gap:12px;align-items:baseline;padding:10px 0;border-bottom:1px solid #f4efe8}
.ok-row:last-child{border-bottom:none}
.tick{color:var(--brand-600);font-weight:800}
.ok-title{flex:1}
.ok-row .muted{font-size:12.5px}

/* ---------- блок раздела ---------- */
.cat-block{margin-bottom:26px}
.cat-block h3{scroll-margin-top:92px}
.empty{padding:26px;text-align:center;color:var(--brand-700);border:1px dashed var(--brand-300);border-radius:var(--r-md);background:var(--brand-50)}
.empty.small{padding:14px;font-size:13.5px}
.foot{color:var(--ink-faint);font-size:12.5px;text-align:center;padding:26px 0 10px}
.hidden{display:none}
@media(max-width:1080px){
  .hero-grid{grid-template-columns:1fr}
  .tiles{grid-template-columns:repeat(3,1fr)}
  .two-col{grid-template-columns:1fr}
}
@media(max-width:640px){
  .wrap{padding:16px 14px 44px}
  .hero{padding:26px 22px}
  .card{padding:22px 18px}
  .tiles{grid-template-columns:repeat(2,1fr)}
  .nav{border-radius:var(--r-md);padding:8px}
  .nav a{padding:7px 12px;font-size:12.5px}
  .nav #search{flex:1 1 100%}
  .scrollbox{max-height:60vh}
  .core-head{gap:12px}
  .core-primary b{font-size:19px}
}
CSS;
	}

	private function js(): string
	{
		return <<<'JS'
(function(){
  var rows    = [].slice.call(document.querySelectorAll('tr.irow'));
  var blocks  = [].slice.call(document.querySelectorAll('.cat-block'));
  var fbtns   = [].slice.call(document.querySelectorAll('.fbtn'));
  var tiles   = [].slice.call(document.querySelectorAll('.tile[data-sev]'));
  var cats    = [].slice.call(document.querySelectorAll('.cat[data-cat]'));
  var okrows  = [].slice.call(document.querySelectorAll('.ok-row'));
  var search  = document.getElementById('search');
  var fstate  = document.getElementById('fstate');
  var curCat  = 'all', curSev = '', q = '';

  function matchRow(r){
    var okCat = (curCat === 'all') || (r.getAttribute('data-cat') === curCat);
    var okSev = (!curSev) || (r.getAttribute('data-sev') === curSev);
    var okTxt = (!q) || ((r.getAttribute('data-text') || '').indexOf(q) > -1);
    return okCat && okSev && okTxt;
  }
  function apply(){
    var shown = 0;
    rows.forEach(function(r){
      var ok = matchRow(r);
      r.classList.toggle('hidden', !ok);
      var d = r.nextElementSibling;
      if (d && d.classList.contains('detail')) { if (!ok) { d.hidden = true; r.classList.remove('open'); } }
      if (ok) shown++;
    });
    // прячем пустые разделы и их заголовки
    blocks.forEach(function(b){
      var vis = b.querySelectorAll('tr.irow:not(.hidden)').length;
      b.classList.toggle('hidden', vis === 0);
      var h = b.querySelector('h3');
      if (h) h.style.display = vis === 0 ? 'none' : '';
    });
    okrows.forEach(function(r){
      var okCat = (curCat === 'all') || (r.getAttribute('data-cat') === curCat);
      var t = (r.textContent || '').toLowerCase();
      r.classList.toggle('hidden', !(okCat && (!q || t.indexOf(q) > -1)));
    });
    if (fstate) fstate.textContent = 'показано ' + shown + ' из ' + rows.length;
  }
  function setFilter(cat, sev){
    curCat = cat || 'all';
    curSev  = sev || '';
    fbtns.forEach(function(b){ b.classList.toggle('active', b.getAttribute('data-f') === curCat); });
    apply();
  }
  fbtns.forEach(function(b){
    b.addEventListener('click', function(){ setFilter(b.getAttribute('data-f'), ''); });
  });
  // клик по плитке критичности — переход к замечаниям этой критичности
  tiles.forEach(function(t){
    t.addEventListener('click', function(ev){
      ev.preventDefault();
      setFilter('all', t.getAttribute('data-sev'));
      document.getElementById('issues').scrollIntoView({behavior:'smooth', block:'start'});
    });
  });
  // клик по направлению — переход к его разделу с замечаниями
  cats.forEach(function(c){
    c.addEventListener('click', function(ev){
      ev.preventDefault();
      var cat = c.getAttribute('data-cat');
      setFilter(cat, '');
      var blk = document.querySelector('.cat-block[data-catblock="' + cat.replace(/"/g,'') + '"]');
      if (blk) { blk.scrollIntoView({behavior:'smooth', block:'start'}); }
    });
  });
  // раскрытие строки с деталями
  rows.forEach(function(r){
    r.addEventListener('click', function(){
      var d = r.nextElementSibling;
      if (!d || !d.classList.contains('detail')) return;
      var open = d.hidden;
      d.hidden = !open;
      r.classList.toggle('open', open);
    });
  });
  if (search){
    search.addEventListener('input', function(){ q = (search.value || '').toLowerCase().trim(); apply(); });
  }
  // компактный / полный вид списков
  var toggle = document.getElementById('toggleFull');
  if (toggle){
    toggle.addEventListener('click', function(){
      var sec = document.getElementById('issues');
      if (!sec) return;
      var full = sec.classList.toggle('full');
      toggle.textContent = full ? 'Компактный вид' : 'Полный вид';
      toggle.classList.toggle('active', full);
    });
  }
  // копирование плана устранения
  var copy = document.getElementById('copyList');
  if (copy){
    copy.addEventListener('click', function(){
      var ta = document.getElementById('planText');
      if (!ta) return;
      var txt = ta.value;
      var done = function(){ copy.textContent = 'Скопировано ✓'; setTimeout(function(){ copy.textContent = 'Скопировать план'; }, 1600); };
      if (navigator.clipboard && navigator.clipboard.writeText) {
        navigator.clipboard.writeText(txt).then(done, function(){ fallback(ta, done); });
      } else { fallback(ta, done); }
      function fallback(ta, done){
        ta.removeAttribute('hidden'); ta.select();
        try { document.execCommand('copy'); done(); } catch(e) { window.prompt('Скопируйте план:', ta.value); }
        ta.setAttribute('hidden','');
      }
    });
  }
  apply();
})();
JS;
	}

	private function plural(int $n, string $one, string $few, string $many): string
	{
		$n = abs($n) % 100;
		$n1 = $n % 10;
		if ($n > 10 && $n < 20) {
			return $many;
		}
		if ($n1 > 1 && $n1 < 5) {
			return $few;
		}
		if ($n1 === 1) {
			return $one;
		}
		return $many;
	}
}

/**
 * Справка по запуску.
 */
function usage(): string
{
	return <<<'TXT'
STUDIO AUDIT CRAWLER 2.1 — полный SEO/технический/performance-аудит сайта в одном файле

  php tools/crawler.php <url>

  Этого достаточно. Больше ничего указывать не нужно.

  Режим выбирается автоматически по адресу:
    • ЛОКАЛЬНЫЙ (localhost, 127.0.0.1, ::1, приватные сети 10/172.16-31/192.168,
      домены .local/.test/.dev/.internal) — глубокий быстрый обход без пауз,
      HTTPS-проверки отключены (у стенда нет TLS), проверка служебных файлов включена;
    • ПУБЛИЧНЫЙ (любой другой адрес) — вежливый обход с паузами и мягкими таймаутами,
      HTTPS/TLS-проверки включены, сканирование чужих служебных файлов отключено.

  Без адреса аудитится локальный стенд http://127.0.0.1:8899/

  Дополнительные параметры (необязательны — нужны только для нестандартных случаев):
    --depth=N          максимальная глубина обхода в кликах
    --limit=N          максимум страниц
    --concurrency=N    параллельных запросов
    --delay=MS         пауза между запросами к одному хосту
    --timeout=MS       таймаут запроса
    --ua=STRING        User-Agent краулера
    --only=PATH        обход только по префиксу пути (через запятую)
    --exclude=RE       исключать URL по PCRE (можно повторять)
    --respect-robots   не заходить на URL, запрещённые robots.txt
    --no-robots        не читать robots.txt
    --external[=N]     проверять внешние ссылки (0 — выкл.)
    --no-external      не проверять внешние ссылки
    --no-probe         не проверять служебные файлы (.env, .git, …)
    --probe            проверять служебные файлы и на публичных сайтах
    --json=FILE        сохранить JSON-отчёт
    --html=FILE        сохранить HTML-отчёт (по умолчанию tools/audit-report.html)
    --no-report        не создавать HTML-отчёт
    --pages            показать полную таблицу страниц
    --page=URL         план продвижения конкретной страницы (ядро, пробелы, черновики)
    --clusters         карта тематических кластеров сайта
    --competitors=URLS сравнение с конкурентами (те же правила, через запятую)
    --no-color         без ANSI-цвета
    --quiet            только итоговые строки
    --help             эта справка

  Примеры
    php tools/crawler.php https://example.com
    php tools/crawler.php https://example.com --html=/tmp/audit.html --json=/tmp/audit.json
    php tools/crawler.php https://example.com --depth=8 --limit=2000

  Коды возврата
    0 — критичных и высоких замечаний нет
    1 — есть замечания
    2 — ошибка запуска/аудита
TXT;
}

/**
 * Разбор аргументов командной строки.
 */
function parseArgs(array $argv): Cfg
{
	$cfg = new Cfg();
	$args = array_slice($argv, 1);
	$urlGiven = false;

	foreach ($args as $arg) {
		if ($arg === '' || $arg[0] !== '-') {
			if (!$urlGiven) {
				$cfg->start = $arg;
				$urlGiven = true;
			}
			continue;
		}
		$body = ltrim($arg, '-');
		$val = null;
		if (str_contains($body, '=')) {
			[$body, $val] = explode('=', $body, 2);
		}
		switch ($body) {
			case 'help':
			case 'h':
				echo usage(), PHP_EOL;
				exit(0);
			case 'depth':
				$cfg->depth = max(0, (int)$val);
				$cfg->explicit[] = 'depth';
				break;
			case 'limit':
				$cfg->limit = max(1, (int)$val);
				$cfg->explicit[] = 'limit';
				break;
			case 'concurrency':
				$cfg->concurrency = max(1, min(32, (int)$val));
				$cfg->explicit[] = 'concurrency';
				break;
			case 'delay':
				$cfg->delayMs = max(0, (int)$val);
				$cfg->explicit[] = 'delayMs';
				break;
			case 'timeout':
				$cfg->timeoutMs = max(1000, (int)$val);
				$cfg->explicit[] = 'timeoutMs';
				break;
			case 'ua':
				$cfg->ua = (string)$val;
				break;
			case 'only':
				foreach (explode(',', (string)$val) as $part) {
					$part = trim($part);
					if ($part !== '') {
						$cfg->only[] = $part;
					}
				}
				break;
			case 'exclude':
				if ($val !== null) {
					$cfg->exclude[] = $val;
				}
				break;
			case 'respect-robots':
				$cfg->respectRobots = true;
				break;
			case 'no-robots':
				$cfg->readRobots = false;
				break;
			case 'external':
				$cfg->external = true;
				if ($val !== null && $val !== '') {
					$cfg->externalLimit = max(0, (int)$val);
					$cfg->external = $cfg->externalLimit > 0;
					$cfg->explicit[] = 'externalLimit';
				}
				break;
			case 'no-external':
				$cfg->external = false;
				break;
			case 'no-probe':
				$cfg->probe = false;
				$cfg->explicit[] = 'probe';
				break;
			case 'probe':
				$cfg->probe = true;
				$cfg->explicit[] = 'probe';
				break;
			case 'json':
				$cfg->jsonFile = (string)$val;
				break;
			case 'html':
				$cfg->htmlFile = (string)$val;
				break;
			case 'no-report':
				$cfg->noReport = true;
				break;
			case 'page':
				$cfg->pageUrl = (string)$val;
				break;
			case 'competitors':
				foreach (explode(',', (string)$val) as $u) {
					$u = trim($u);
					if ($u !== '') {
						$cfg->competitors[] = $u;
					}
				}
				break;
			case 'clusters':
				$cfg->clusters = true;
				break;
			case 'pages':
				$cfg->showPages = true;
				break;
			case 'no-color':
				$cfg->color = false;
				break;
			case 'quiet':
			case 'q':
				$cfg->quiet = true;
				break;
			default:
				fwrite(STDERR, "Неизвестная опция: {$arg}\n");
				echo usage(), PHP_EOL;
				exit(2);
		}
	}

	// нормализация адреса
	$start = trim($cfg->start);
	if ($start === '') {
		$start = 'http://127.0.0.1:8899/';
	}
	if (!preg_match('~^[a-z][a-z0-9+.\-]*://~i', $start)) {
		$start = 'http://' . ltrim($start, '/');
	}
	$parsed = parse_url($start);
	if ($parsed === false || !isset($parsed['host'])) {
		fwrite(STDERR, "Некорректный адрес: {$cfg->start}\n");
		exit(2);
	}
	$scheme = strtolower($parsed['scheme'] ?? 'http');
	$host = strtolower($parsed['host']);
	$port = isset($parsed['port']) ? ':' . (int)$parsed['port'] : '';
	$path = $parsed['path'] ?? '/';
	if ($path === '') {
		$path = '/';
	}
	$query = isset($parsed['query']) && $parsed['query'] !== '' ? '?' . $parsed['query'] : '';
	$cfg->start = $scheme . '://' . $host . $port . $path . $query;

	// Сценарий подбирается автоматически: локальный стенд или публичный сайт.
	// Явно заданные пользователем опции при этом не перекрываются.
	$cfg->applyScenario();
	return $cfg;
}

/**
 * Точка входа.
 */
function main(array $argv): int
{
	$cfg = parseArgs($argv);
	$ui = new Ui($cfg->color && !$cfg->quiet);

	if (!$cfg->quiet) {
		fwrite(STDERR, "Studio Audit Crawler · цель: {$cfg->start}\n");
	}

	$crawler = new Crawler($cfg, $ui);
	$crawler->run();

	$ctx = new Ctx($cfg, $crawler);
	$audit = new Audit();
	$results = $audit->run($ctx);
	$score = $audit->score();

	// --- аналитический слой: ядро, кластеры, план страницы, конкуренты ---
	$insight = [];
	if ($cfg->pageUrl !== null && $cfg->pageUrl !== '') {
		$abs = Url::resolve($cfg->pageUrl, $cfg->start) ?? $cfg->pageUrl;
		$insight['page'] = Insight::pagePlan($ctx, $abs, $results);
	}
	if ($cfg->clusters || isset($insight['page'])) {
		$insight['clusters'] = Insight::clusters($ctx);
	}
	if ($cfg->competitors !== []) {
		$insight['competitors'] = Insight::compareCompetitors($cfg, $results);
	}

	$report = new Report($ui, $cfg);
	if ($cfg->quiet) {
		$sev = ['critical' => 0, 'high' => 0, 'medium' => 0, 'low' => 0, 'info' => 0];
		foreach ($audit->results() as $r) {
			if (!$r['pass']) {
				$sev[$r['sev']]++;
			}
		}
		echo "Оценка: ", number_format((float)$score['score'], 1), "/100  |  ",
			"критично: {$sev['critical']}  высокая: {$sev['high']}  средняя: {$sev['medium']}  низкая: {$sev['low']}  инфо: {$sev['info']}\n",
			"Страниц: ", count($crawler->pages), "  |  Правил: ", count($audit->results()),
			"  |  Замечаний: ", array_sum($sev), "\n";
	} else {
		$report->renderConsole($ctx, $crawler, $audit, $score);
	}
	if ($insight !== []) {
		Insight::renderInsightConsole($ui, $insight, $ctx, $crawler);
	}

	if ($cfg->jsonFile !== null && $cfg->jsonFile !== '') {
		$json = $report->renderJson($ctx, $crawler, $audit, $score);
		$written = @file_put_contents($cfg->jsonFile, $json);
		if ($written === false) {
			fwrite(STDERR, "Не удалось записать JSON в {$cfg->jsonFile}\n");
		} else {
			echo PHP_EOL, 'JSON-отчёт: ', $cfg->jsonFile, ' (', number_format($written / 1024, 1), ' КБ)', PHP_EOL;
		}
	}
	if (!$cfg->noReport) {
		$htmlPath = $cfg->htmlFile !== null && $cfg->htmlFile !== ''
			? $cfg->htmlFile
			: __DIR__ . DIRECTORY_SEPARATOR . 'audit-report.html';
		$html = $report->renderHtml($ctx, $crawler, $audit, $score, $insight);
		$written = @file_put_contents($htmlPath, $html);
		if ($written === false) {
			fwrite(STDERR, "Не удалось записать HTML в {$htmlPath}\n");
		} else {
			echo PHP_EOL, 'HTML-отчёт: ', $htmlPath, ' (', number_format($written / 1024, 1), ' КБ)', PHP_EOL;
		}
	}

	$critical = 0;
	$high = 0;
	foreach ($audit->results() as $r) {
		if ($r['pass']) {
			continue;
		}
		if ($r['sev'] === 'critical') {
			$critical++;
		} elseif ($r['sev'] === 'high') {
			$high++;
		}
	}
	return ($critical > 0 || $high > 0) ? 1 : 0;
}

// Запуск как самостоятельный скрипт. Если файл подключён (require) — main() не вызывается,
// классы можно переиспользовать в тестах.
if (isset($_SERVER['SCRIPT_FILENAME']) && realpath((string)$_SERVER['SCRIPT_FILENAME']) === __FILE__) {
	exit(main($argv ?? []));
}
