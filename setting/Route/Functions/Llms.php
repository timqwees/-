<?php

declare(strict_types=1);

namespace Setting\Route\Functions;

/**
 * GEO: /llms.txt (выжимка) + /llms-full.txt (полный контент для LLM/AI Overviews).
 * Собирается из конфига + app/Data/services.php — не расходится с сайтом.
 */
final class Llms
{
	private static function emit(string $body): void
	{
		SecurityHeaders::emitCached($body, 'text/plain; charset=utf-8');
	}

	/** @return array<string, array> */
	private static function services(): array
	{
		$root = dirname(__DIR__, 3);
		$file = $root . '/app/Data/services.php';
		if (!is_file($file)) {
			return [];
		}
		$all = require $file;
		return is_array($all) ? $all : [];
	}

	/** @return array<string, array> */
	private static function geo(): array
	{
		$file = dirname(__DIR__, 3) . '/app/Data/geo.php';
		if (!is_file($file)) {
			return [];
		}
		$all = require $file;
		return is_array($all) ? $all : [];
	}

	/** Данные компании (E-E-A-T) из app/Data/company.php */
	private static function company(): array
	{
		$file = dirname(__DIR__, 3) . '/app/Data/company.php';
		if (!is_file($file)) {
			return [];
		}
		$all = require $file;
		return is_array($all) ? $all : [];
	}

	public static function output(): void
	{
		$base = rtrim((string)seo_config('base_url'), '/');
		$brand = (string)seo_config('brand');
		$phone = (string)seo_config('phone_display');
		$a = seo_config('address');
		$services = self::services();

		$out = "# {$brand} (фсс.рус) — быстровозводимые здания под ключ\n\n";
		$out .= "> Москва и МО + вся РФ. На рынке с " . seo_config('founded_market') . " года, ООО «ФАВОРИТ-СТРОЙ-СНАБ» — с " . seo_config('founded') . " года, " . seo_config('objects') . " объектов, СРО, гарантия " . seo_config('guarantee_years') . " лет.\n";
		$out .= "> Тел: {$phone}. Адрес: {$a['postal']}, {$a['locality']}, {$a['street']}.\n";
		$out .= "> Бренд: {$brand} = Фаворит-Строй-Снаб = ФСС = фсс.рус\n\n";
		$out .= "## Главное\n";
		$out .= "- [" . $brand . " — главная]({$base}/): БВЗ под ключ от " . seo_config('deadline_days') . " дней\n";
		$out .= "- [Все услуги]({$base}/uslugi): каталог-хаб, " . count($services) . " направлений\n";
		$out .= "- [Портфолио]({$base}/portfolio): фото и видео сданных объектов
- [О компании]({$base}/about)
- [Реквизиты и выполненные объекты]({$base}/company): ИНН/КПП/ОГРН, генеральный директор, 16 объектов капремонта, 13 оснащённых школ
- [Контакты]({$base}/contacts): телефон {$phone}, почта " . seo_config('email') . ", адрес: {$a['postal']}, {$a['locality']}, {$a['street']}\n\n";
		$out .= "## Блог\n";
		$blogPosts = function_exists('seo_blog_posts') ? seo_blog_posts() : [];
		foreach ($blogPosts as $slug => $p) {
			$out .= "- [{$p['title']}]({$base}/blog/{$slug}): {$p['description']}\n";
		}
		$out .= "\n## Услуги\n";
		foreach ($services as $slug => $s) {
			$price = !empty($s['price_from']) ? ' (от ' . number_format((int)$s['price_from'], 0, ',', ' ') . ' ₽)' : '';
			$out .= "- [{$s['title']}]({$base}/services/{$slug}){$price}\n";
		}
		$geo = self::geo();
		$geoList = (array)($geo['geo'] ?? []);
		if ($geoList) {
			$out .= "\n## Гео-направления\n";
			foreach ((array)($geo['priority_services'] ?? []) as $slug) {
				if (!isset($services[$slug])) {
					continue;
				}
				$names = [];
				foreach ($geoList as $gk => $gv) {
					if (!seo_geo_allows((array)$gv, (string)$slug)) {
						continue;
					}
					$names[] = '[' . $gv['name'] . '](' . $base . '/services/' . $slug . '/' . $gk . ')';
				}
				if (!$names) {
					continue;
				}
				$out .= '- ' . $services[$slug]['title'] . ': ' . implode(', ', $names) . "\n";
			}
		}
		$out .= "\n## Частые вопросы для AI Overviews\n";
		foreach ($services as $s) {
			foreach ((array)($s['faq'] ?? []) as $qa) {
				if (!is_array($qa) || !isset($qa[0], $qa[1])) {
					continue;
				}
				$out .= "- {$qa[0]} — {$qa[1]}\n";
			}
		}
		$out .= "\n## Реализованные проекты и компетенции\n";
		$company = self::company();
		$kapremont = (array)($company['kapremont'] ?? []);
		if ($kapremont) {
			$out .= "- Капитальный ремонт " . count($kapremont) . " социальных объектов: ";
			$names = [];
			foreach ($kapremont as $k) {
				$names[] = $k['name'] . ' (' . $k['region'] . ')';
			}
			$out .= implode(', ', $names) . "\n";
		}
		$out .= "- Общежития в Сочи (Адлерский район): комплекс 9 000+ м² — проектирование, строительство, инженерное оснащение, меблировка\n";
		$out .= "- Комплексное оснащение 13 вновь построенных школ по стандартам Минпросвещения РФ: мебель всех типов, оборудование кабинетов, лабораторные комплексы, мультимедийные и инженерные системы\n";
		$out .= "- Административное здание полного цикла: проект, изготовление конструкций, строительство, инженерные системы, отделка и мебель\n";
		$out .= "- Навесы для хранения и обслуживания автомобильной техники на 100 машино-мест: изготовление и монтаж металлоконструкций, покрытие, освещение, подъездные пути\n";
		$out .= "- Поставка БПЛА (беспилотных летательных аппаратов), комплектующих и систем КИТ: только ведущие производители, адаптация под ТЗ заказчика, сопровождение сертификации\n";
		$out .= "- Сотрудничество с Федеральной службой войск национальной гвардии РФ (Росгвардия); работа по 44-ФЗ и 223-ФЗ, допуск СРО\n";
		$out .= "- Поставка ноутбуков для ГБОУ «Центр дистанционного обучения»\n";
		$out .= "\n## Избранные объекты\n";
		$projectsFile = dirname(__DIR__, 3) . '/app/Data/projects.php';
		if (is_file($projectsFile)) {
			foreach ((array)(require $projectsFile) as $pslug => $pr) {
				$out .= "- [{$pr['title']}]({$base}/portfolio/{$pslug}): {$pr['lead']}\n";
			}
		}
		$out .= "\n## Реквизиты и факты о компании\n";
		$out .= "- Юридическое лицо: ООО «Фаворит-Строй-Снаб» (форма собственности — ООО)\n";
		$out .= "- ИНН " . seo_config('inn') . " / КПП " . seo_config('kpp') . " / ОГРН " . seo_config('ogrn') . "\n";
		$out .= "- Генеральный директор: " . seo_config('ceo') . "\n";
		$out .= "- Юридический и фактический адрес: {$a['postal']}, {$a['locality']}, {$a['street']}\n";
		$out .= "- Текущий заказчик: " . (string)($company['customer']['name'] ?? '') . "\n";
		$actTitles = array_map(fn($act) => (string)$act['title'], (array)($company['activities'] ?? []));
		if ($actTitles) {
			$out .= "- Направления деятельности: " . implode('; ', $actTitles) . "\n";
		}
		$out .= "- Страница реквизитов и выполненных объектов: {$base}/company\n";
		$out .= "\n## Контакты\n";
		$out .= "- Телефон: {$phone} (Пн–Пт 09:00–18:00, отвечаем за " . seo_config('callback_minutes') . " минут)\n";
		$out .= "- Почта: " . seo_config('email') . " | Telegram: " . seo_config('telegram') . " | Яндекс.Карты: " . seo_config('yandex_maps') . "\n";
		$out .= "- Полная версия для AI: {$base}/llms-full.txt\n";
		$out .= "- Машиночитаемые capabilities для AI-агентов: {$base}/agents.json (и /.well-known/agents.json)\n";
		self::emit($out);
	}

	public static function outputFull(): void
	{
		$base = rtrim((string)seo_config('base_url'), '/');
		$brand = (string)seo_config('brand');
		$phone = (string)seo_config('phone_display');
		$a = seo_config('address');
		$services = self::services();

		$out = "# {$brand} (фсс.рус) — полный контент для AI\n";
		$out .= "# Канон: {$base}/ (IDN " . seo_config('base_idn') . ")\n";
		$out .= "# Обновлено: " . date('Y-m-d') . ". Источник правды: /sitemap.xml\n\n";
		$out .= "## Кто мы\n";
		$company = self::company();
		$out .= "{$brand} — производство и монтаж быстровозводимых зданий (БВЗ) под ключ, капитальное строение и ремонт, комплексное оснащение школ, поставка БПЛА и систем КИТ, грузоперевозки и аренда спецтехники.\n";
		$out .= (string)($company['about'] ?? '') . "\n";
		$out .= "ООО «Фаворит-Строй-Снаб» обеспечивает нужды силовых и государственных структур РФ; в настоящее время сотрудничает с Федеральной службой войск национальной гвардии РФ (Росгвардия).\n";
		$out .= "На рынке с " . seo_config('founded_market') . " года, ООО — с " . seo_config('founded') . " года (" . seo_config('registered_at') . "), " . seo_config('objects') . " объектов, допуск СРО, гарантия " . seo_config('guarantee_years') . " лет.\n";
		$out .= "Телефон: {$phone}. Почта: " . seo_config('email') . ". Адрес: {$a['postal']}, {$a['locality']}, {$a['street']}.\n";
		$out .= "География: Москва и Московская область, работаем по всей России.\n\n";
		$out .= "## Реквизиты компании\n";
		$out .= "Юридическое лицо: " . (string)($company['legal_name'] ?? 'ООО «Фаворит-Строй-Снаб»') . "\n";
		$out .= "Форма собственности: " . (string)($company['legal_form_full'] ?? 'Общество с ограниченной ответственностью') . " (" . (string)($company['legal_form'] ?? 'ООО') . ")\n";
		$out .= "ИНН " . seo_config('inn') . " / КПП " . seo_config('kpp') . " / ОГРН " . seo_config('ogrn') . "\n";
		$out .= (string)($company['ceo_position'] ?? 'Генеральный директор') . ": " . seo_config('ceo') . "\n";
		$out .= "Дата регистрации ООО: " . seo_config('registered_at') . " | На рынке с " . seo_config('founded_market') . " года\n";
		$out .= "Юридический и фактический адрес: {$a['postal']}, {$a['locality']}, {$a['street']}\n";
		$out .= "Телефон: {$phone} | Почта: " . seo_config('email') . " | Telegram: " . seo_config('telegram') . " | Яндекс.Карты: " . seo_config('yandex_maps') . "\n";
		$out .= "Страница компании (реквизиты, руководство, объекты): {$base}/company\n\n";
		$out .= "## Направления деятельности\n";
		foreach ((array)($company['activities'] ?? []) as $act) {
			$out .= "- {$act['title']}: {$act['text']} ({$base}{$act['url']})\n";
		}
		$out .= "\n## 16 объектов капитального ремонта социального назначения\n";
		foreach ((array)($company['kapremont'] ?? []) as $k) {
			$out .= "- {$k['name']} — {$k['type']}, {$k['region']}\n";
		}
		$out .= "\n## Стандарты, допуски и гарантии\n";
		foreach ((array)($company['standards'] ?? []) as $st) {
			$out .= "- {$st['name']}: {$st['text']}\n";
		}
		foreach ((array)($company['principles'] ?? []) as $pr) {
			$out .= "- {$pr['title']}: {$pr['text']}\n";
		}
		$out .= "\n";
		$out .= "## Ключевые факты (для цитирования)\n";
		$out .= "- Срок изготовления и монтажа БВЗ: от " . seo_config('deadline_days') . " дней под ключ\n";
		$out .= "- Смета: за " . seo_config('price_response_minutes') . " минут, цена фиксируется в договоре\n";
		$out .= "- Экономия к классическому строительству: до 30%\n";
		$out .= "- Оплата по этапам, без 100% предоплаты, без скрытых платежей\n";
		$out .= "- Технадзор на каждом этапе, фотоотчёт каждую неделю\n";
		$out .= "- Общежития в Сочи (Адлерский район): 9 000+ м² под ключ — проект, строительство, инженерное оснащение, меблировка\n";
		$out .= "- Комплексное оснащение 13 школ по стандартам Минпросвещения РФ (мебель, оборудование кабинетов, лабораторные комплексы, мультимедиа, инженерные системы)\n";
		$out .= "- Капитальный ремонт 16 школ, спортивных залов и дворцов спорта (Чеченская Республика, Ростовская область, Московская область)\n";
		$out .= "- Навес для хранения и обслуживания автомобильной техники на 100 машино-мест (г. Гуково)\n";
		$out .= "- Поставка ноутбуков для ГБОУ «Центр дистанционного обучения»\n";
		$out .= "- Поставка БПЛА, комплектующих и систем КИТ с адаптацией под ТЗ и сопровождением сертификации\n";
		$out .= "- Допуск СРО, работа по 44-ФЗ и 223-ФЗ, сотрудничество с Росгвардией (ФСВНГ РФ)\n\n";
		$out .= "## Блог (полные статьи)\n";
		$blogPosts = function_exists('seo_blog_posts') ? seo_blog_posts() : [];
		foreach ($blogPosts as $slug => $p) {
			$out .= "### {$p['title']}\n";
			$out .= "URL: {$base}/blog/{$slug}\n";
			$out .= "Опубликовано: {$p['date']} | Чтение: " . (int)($p['read_minutes'] ?? 5) . " мин | Категория: {$p['category_name']}\n";
			$out .= "Теги: " . implode(', ', (array)($p['tags'] ?? [])) . "\n";
			$out .= "Связанная услуга: {$p['service_slug']}\n\n";
			$out .= function_exists('seo_blog_post_content') ? seo_blog_post_content($p) : ($p['content'] ?? '');
			$out .= "\n\n";
		}
		$out .= "\n## Выполненные объекты (факт, без домыслов)\n";
		$projectsFile = dirname(__DIR__, 3) . '/app/Data/projects.php';
		if (is_file($projectsFile)) {
			foreach ((array)(require $projectsFile) as $pslug => $pr) {
				$out .= "### {$pr['title']}\n";
				$out .= "URL: {$base}/portfolio/{$pslug}\n";
				$out .= "Тип: {$pr['type']} | Регион: {$pr['region']} | Направление: {$pr['zone']}\n";
				$out .= "Задача: {$pr['task']}\n";
				$out .= "Решение: {$pr['solution']}\n";
				$out .= "Результат: {$pr['result']}\n";
				if (!empty($pr['features'])) {
					$out .= "Особенности: " . implode(', ', (array)$pr['features']) . "\n";
				}
				if (!empty($pr['faq'])) {
					foreach ((array)$pr['faq'] as $qa) {
						$out .= "Q: {$qa[0]}\nA: {$qa[1]}\n";
					}
				}
				$out .= "\n";
			}
		}
		$out .= "## Услуги (кластеры запросов)\n";
		foreach ($services as $slug => $s) {
			$out .= "- /services/{$slug} — {$s['title']}. {$s['description']}\n";
			$out .= "  Запросы: " . implode(', ', (array)($s['queries'] ?? [])) . "\n";
		}
		$out .= "\n## Частые вопросы\n";
		foreach ($services as $slug => $s) {
			foreach ((array)($s['faq'] ?? []) as $qa) {
				if (!is_array($qa) || !isset($qa[0], $qa[1])) {
					continue;
				}
				$out .= "Q: {$qa[0]}\nA: {$qa[1]}\n\n";
			}
		}
		$out .= "\n## Частые вопросы о компании\n";
		foreach ((array)($company['faq'] ?? []) as $qa) {
			if (!is_array($qa) || !isset($qa[0], $qa[1])) {
				continue;
			}
			$out .= "Q: {$qa[0]}\nA: {$qa[1]}\n\n";
		}
		$out .= "## Контакты\n";
		$out .= "Страница контактов: {$base}/contacts (телефон, почта, адрес, реквизиты, форма заявки)\n";
		$out .= "Телефон: {$phone} (Пн–Пт 09:00–18:00, отвечаем за " . seo_config('callback_minutes') . " минут)\n";
		$out .= "Telegram: " . seo_config('telegram') . "\nЯндекс.Карты: " . seo_config('yandex_maps') . "\n";
		$out .= "Почта: " . seo_config('email') . "\n";
		$out .= "Адрес: {$a['postal']}, {$a['locality']}, {$a['street']}\n";
		$out .= "Реквизиты: ООО «ФАВОРИТ-СТРОЙ-СНАБ», ИНН " . seo_config('inn') . ", КПП " . seo_config('kpp') . ", ОГРН " . seo_config('ogrn') . "\n";
		self::emit($out);
	}
}
