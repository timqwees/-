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

	public static function output(): void
	{
		$base = rtrim(seo_config('base_url'), '/');
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
- [О компании]({$base}/about)\n\n";
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
		$out .= "\n## FAQ для AI Overviews\n";
		foreach ($services as $s) {
			foreach ($s['faq'] as $qa) {
				$out .= "- {$qa[0]} — {$qa[1]}\n";
			}
		}
		$out .= "\n## Контакты\n";
		$out .= "- Телефон: {$phone} (Пн–Пт 09:00–18:00, отвечаем за " . seo_config('callback_minutes') . " минут)\n";
		$out .= "- Почта: " . seo_config('email') . " | Telegram: " . seo_config('telegram') . " | VK: " . seo_config('vk') . "\n";
		$out .= "- Полная версия для AI: {$base}/llms-full.txt\n";
		self::emit($out);
	}

	public static function outputFull(): void
	{
		$base = rtrim(seo_config('base_url'), '/');
		$brand = (string)seo_config('brand');
		$phone = (string)seo_config('phone_display');
		$a = seo_config('address');
		$services = self::services();

		$out = "# {$brand} (фсс.рус) — полный контент для AI\n";
		$out .= "# Канон: {$base}/ (IDN " . seo_config('base_idn') . ")\n";
		$out .= "# Обновлено: " . date('Y-m-d') . ". Источник правды: /sitemap.xml\n\n";
		$out .= "## Кто мы\n";
		$out .= "{$brand} — производство и монтаж быстровозводимых зданий (БВЗ) под ключ, капитальное строение и ремонт, грузоперевозки и аренда спецтехники.\n";
		$out .= "На рынке с " . seo_config('founded_market') . " года, ООО — с " . seo_config('founded') . " года (" . seo_config('registered_at') . "), " . seo_config('objects') . " объектов, допуск СРО, гарантия " . seo_config('guarantee_years') . " лет.\n";
		$out .= "Телефон: {$phone}. Почта: " . seo_config('email') . ". Адрес: {$a['postal']}, {$a['locality']}, {$a['street']}.\n";
		$out .= "География: Москва и Московская область, работаем по всей России.\n\n";
		$out .= "## Ключевые факты (для цитирования)\n";
		$out .= "- Срок изготовления и монтажа БВЗ: от " . seo_config('deadline_days') . " дней под ключ\n";
		$out .= "- Смета: за " . seo_config('price_response_minutes') . " минут, цена фиксируется в договоре\n";
		$out .= "- Экономия к классическому строительству: до 30%\n";
		$out .= "- Оплата по этапам, без 100% предоплаты, без скрытых платежей\n";
		$out .= "- Технадзор на каждом этапе, фотоотчёт каждую неделю\n\n";
		$out .= "## Блог (полные статьи)\n";
		$blogPosts = function_exists('seo_blog_posts') ? seo_blog_posts() : [];
		foreach ($blogPosts as $slug => $p) {
			$out .= "### {$p['title']}\n";
			$out .= "URL: {$base}/blog/{$slug}\n";
			$out .= "Опубликовано: {$p['date']} | Чтение: {$p['read_minutes']} мин | Категория: {$p['category_name']}\n";
			$out .= "Теги: " . implode(', ', $p['tags']) . "\n";
			$out .= "Связанная услуга: {$p['service_slug']}\n\n";
			$out .= function_exists('seo_blog_post_content') ? seo_blog_post_content($p) : ($p['content'] ?? '');
			$out .= "\n\n";
		}
		$out .= "## Услуги (кластеры запросов)\n";
		foreach ($services as $slug => $s) {
			$out .= "- /services/{$slug} — {$s['title']}. {$s['description']}\n";
			$out .= "  Запросы: " . implode(', ', $s['queries']) . "\n";
		}
		$out .= "\n## Частые вопросы\n";
		foreach ($services as $slug => $s) {
			foreach ($s['faq'] as $qa) {
				$out .= "Q: {$qa[0]}\nA: {$qa[1]}\n\n";
			}
		}
		$out .= "## Контакты\n";
		$out .= "Телефон: {$phone} (Пн–Пт 09:00–18:00, отвечаем за " . seo_config('callback_minutes') . " минут)\n";
		$out .= "Telegram: " . seo_config('telegram') . "\nVK: " . seo_config('vk') . "\n";
		$out .= "Почта: " . seo_config('email') . "\n";
		self::emit($out);
	}
}
