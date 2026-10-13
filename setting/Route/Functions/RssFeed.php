<?php

declare(strict_types=1);

namespace Setting\Route\Functions;

/**
 * RSS 2.0 (/rss.xml) + Яндекс.Турбо (/turbo.xml).
 * Айтемы — из app/Data/services.php + хабы, даты — filemtime шаблонов.
 */
final class RssFeed
{
	/** @return list<array{title:string,link:string,desc:string,date:string}> */
	private static function items(): array
	{
		$base = rtrim(seo_config('base_url'), '/');
		$root = dirname(__DIR__, 3);
		$services = file_exists($root . '/app/Data/services.php')
			? (require $root . '/app/Data/services.php')
			: [];
		$items = [];
		$items[] = [
			'title' => seo_config('brand') . ' — быстровозводимые здания под ключ',
			'link' => $base . '/',
			'desc' => 'Склады, ангары, АБК, общежития, капремонт и грузоперевозки. Срок от 14 дней, гарантия 50+ лет, допуск СРО.',
			'date' => date('r', (int)@filemtime($root . '/public/pages/home.php')),
		];
		$items[] = [
			'title' => 'Каталог услуг — производство, ремонт, доставка',
			'link' => $base . '/uslugi',
			'desc' => 'Все услуги компании ' . seo_config('brand') . ': БВЗ под ключ, капремонт, спецтехника, компрессоры, ДГУ, мебель.',
			'date' => date('r', (int)@filemtime($root . '/public/pages/uslugi/uslugi.php')),
		];
		foreach ((array)$services as $slug => $s) {
			$file = $root . '/public/pages/services/' . $slug . '/index.php';
			if (!is_file($file)) {
				continue;
			}
			$items[] = [
				'title' => $s['title'],
				'link' => $base . '/services/' . $slug,
				'desc' => $s['description'],
				'date' => date('r', (int)filemtime($file)),
			];
		}
		$items[] = [
			'title' => 'Портфолио реализованных объектов',
			'link' => $base . '/portfolio',
			'desc' => 'Реализованные объекты: склады, ангары, АБК, общежития, школы. Фото и описания.',
			'date' => date('r', (int)@filemtime($root . '/public/pages/portfolio.php')),
		];
		return $items;
	}

	private static function emit(string $body): void
	{
		SecurityHeaders::emitCached($body, 'application/rss+xml; charset=utf-8');
	}

	public static function output(): void
	{
		$base = rtrim(seo_config('base_url'), '/');
		$out = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
		$out .= '<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom">' . "\n";
		$out .= "\t<channel>\n";
		$out .= "\t\t<title>" . htmlspecialchars(seo_config('brand') . ' — новости и услуги') . "</title>\n";
		$out .= "\t\t<link>{$base}</link>\n";
		$out .= "\t\t<description>" . htmlspecialchars('Быстровозводимые модульные здания под ключ, капитальный ремонт, поставка мебели, грузоперевозки и спецтехника в Москве.') . "</description>\n";
		$out .= "\t\t<language>ru</language>\n";
		$out .= "\t\t<category>Строительство</category>\n";
		$out .= "\t\t<generator>TimQwees Technology</generator>\n";
		$out .= "\t\t<atom:link href=\"{$base}/rss.xml\" rel=\"self\" type=\"application/rss+xml\" />\n";
		$out .= "\t\t<lastBuildDate>" . date('r') . "</lastBuildDate>\n";
		$out .= "\t\t<ttl>60</ttl>\n";
		foreach (self::items() as $it) {
			$out .= "\t\t<item>\n";
			$out .= "\t\t\t<title>" . htmlspecialchars($it['title']) . "</title>\n";
			$out .= "\t\t\t<link>" . htmlspecialchars($it['link']) . "</link>\n";
			$out .= "\t\t\t<guid isPermaLink=\"true\">" . htmlspecialchars($it['link']) . "</guid>\n";
			$out .= "\t\t\t<description><![CDATA[" . $it['desc'] . "]]></description>\n";
			$out .= "\t\t\t<pubDate>" . $it['date'] . "</pubDate>\n";
			$out .= "\t\t</item>\n";
		}
		$out .= "\t</channel>\n";
		$out .= '</rss>';
		self::emit($out);
	}

	public static function outputTurbo(): void
	{
		$base = rtrim(seo_config('base_url'), '/');
		$phoneTel = (string)preg_replace('/\D/', '', (string)seo_config('phone'));
		$phone = (string)seo_config('phone_display');
		$out = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
		$out .= '<rss version="2.0" xmlns:turbo="http://turbo.yandex.ru">' . "\n";
		$out .= "\t<channel>\n";
		$out .= "\t\t<title>" . htmlspecialchars(seo_config('brand') . ' — услуги') . "</title>\n";
		$out .= "\t\t<link>{$base}</link>\n";
		$out .= "\t\t<description>" . htmlspecialchars('Быстровозводимые здания под ключ, капремонт, спецтехника. Москва и МО.') . "</description>\n";
		$out .= "\t\t<language>ru</language>\n";
		foreach (self::items() as $it) {
			$out .= "\t\t<item turbo=\"true\">\n";
			$out .= "\t\t\t<title>" . htmlspecialchars($it['title']) . "</title>\n";
			$out .= "\t\t\t<link>" . htmlspecialchars($it['link']) . "</link>\n";
			$out .= "\t\t\t<turbo:content><![CDATA[<header><h1>" . htmlspecialchars($it['title']) . "</h1></header><p>" . htmlspecialchars($it['desc']) . '</p><p><a href="' . htmlspecialchars($it['link']) . '">Подробнее</a> · <a href="tel:' . $phoneTel . '">' . htmlspecialchars($phone) . "</a></p>]]></turbo:content>\n";
			$out .= "\t\t\t<pubDate>" . $it['date'] . "</pubDate>\n";
			$out .= "\t\t</item>\n";
		}
		$out .= "\t</channel>\n";
		$out .= '</rss>';
		self::emit($out);
	}
}
