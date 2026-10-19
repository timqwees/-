<?php

declare(strict_types=1);

namespace Setting\Route\Functions;

/**
 * RSS 2.0 (/rss.xml) + Яндекс.Турбо (/turbo.xml).
 * Айтемы — из app/Data/services.php + блог, даты — filemtime шаблонов / даты постов.
 */
final class RssFeed
{
	/** @return list<array{title:string,link:string,desc:string,date:string,category?:string,author?:array{name:string,uri?:string},image?:string,content?:string}> */
	private static function items(): array
	{
		$base = rtrim(seo_config('base_url'), '/');
		$root = dirname(__DIR__, 3);
		$items = [];

		// Home
		$items[] = [
			'title' => seo_config('brand') . ' — быстровозводимые здания под ключ',
			'link' => $base . '/',
			'desc' => 'Склады, ангары, АБК, общежития, капремонт и грузоперевозки. Срок от 14 дней, гарантия 50+ лет, допуск СРО.',
			'date' => date('r', (int)@filemtime($root . '/public/pages/home.php')),
			'category' => 'Главная',
			'image' => $base . '/public/assets/image/og/og-home-1200x630.jpg',
		];

		// Services index
		$items[] = [
			'title' => 'Каталог услуг — производство, ремонт, доставка',
			'link' => $base . '/uslugi',
			'desc' => 'Все услуги компании ' . seo_config('brand') . ': БВЗ под ключ, капремонт, спецтехника, компрессоры, ДГУ, мебель.',
			'date' => date('r', (int)@filemtime($root . '/public/pages/uslugi/uslugi.php')),
			'category' => 'Услуги',
			'image' => $base . '/public/assets/image/og/og-uslugi-1200x630.jpg',
		];

		// Individual services
		$services = file_exists($root . '/app/Data/services.php')
			? (require $root . '/app/Data/services.php')
			: [];
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
				'category' => 'Услуги',
				'image' => $base . '/public/assets/image/og/og-services-' . $slug . '-1200x630.jpg',
			];
		}

		// Blog posts
		$blogPosts = function_exists('seo_blog_posts') ? seo_blog_posts() : [];
		$blogListFile = $root . '/public/pages/blog/list.php';
		if (is_file($blogListFile)) {
			foreach ($blogPosts as $slug => $p) {
				$author = $p['author'] ?? null;
				$items[] = [
					'title' => $p['title'],
					'link' => $base . '/blog/' . $slug,
					'desc' => $p['description'],
					'content' => $p['content'] ?? null,
					'date' => date('r', strtotime($p['date'])),
					'category' => $p['category_name'] ?? 'Блог',
					'author' => $author ? [
						'name' => $author['name'],
						'role' => $author['role'] ?? '',
						'uri' => $base . '/about',
					] : null,
					'image' => $base . $p['og_image'],
				];
			}
		}

		// Blog index
		$items[] = [
			'title' => 'Блог экспертов ' . seo_config('brand'),
			'link' => $base . '/blog',
			'desc' => 'Экспертные статьи о строительстве БВЗ, материалах, ценах и рекомендациях.',
			'date' => date('r', (int)@filemtime($blogListFile)),
			'category' => 'Блог',
			'image' => $base . '/public/assets/image/og/og-blog-1200x630.jpg',
		];

		// Portfolio
		$items[] = [
			'title' => 'Портфолио реализованных объектов',
			'link' => $base . '/portfolio',
			'desc' => 'Реализованные объекты: склады, ангары, АБК, общежития, школы. Фото и описания.',
			'date' => date('r', (int)@filemtime($root . '/public/pages/portfolio.php')),
			'category' => 'Портфолио',
			'image' => $base . '/public/assets/image/og/og-portfolio-1200x630.jpg',
		];

		return $items;
	}

	private static function emit(string $body, string $contentType = 'application/rss+xml; charset=utf-8'): void
	{
		SecurityHeaders::emitCached($body, $contentType);
	}

	public static function output(): void
	{
		$base = rtrim(seo_config('base_url'), '/');
		$brand = seo_config('brand');
		$phoneTel = (string)preg_replace('/\D/', '', (string)seo_config('phone'));
		$phone = (string)seo_config('phone_display');

		$out = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
		$out .= '<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom" xmlns:media="http://search.yahoo.com/mrss/" xmlns:dc="http://purl.org/dc/elements/1.1/" xmlns:content="http://purl.org/rss/1.0/modules/content/">' . "\n";
		$out .= "\t<channel>\n";
		$out .= "\t\t<title>" . htmlspecialchars($brand . ' — новости, услуги и экспертиза') . "</title>\n";
		$out .= "\t\t<link>{$base}</link>\n";
		$out .= "\t\t<description>" . htmlspecialchars('Быстровозводимые модульные здания под ключ, капитальный ремонт, поставка мебели, грузоперевозки и спецтехника в Москве.') . "</description>\n";
		$out .= "\t\t<language>ru</language>\n";
		$out .= "\t\t<category>Строительство</category>\n";
		$out .= "\t\t<generator>TimQwees Technology</generator>\n";
		$out .= "\t\t<atom:link href=\"{$base}/rss.xml\" rel=\"self\" type=\"application/rss+xml\" />\n";
		$out .= "\t\t<lastBuildDate>" . date('r') . "</lastBuildDate>\n";
		$out .= "\t\t<ttl>60</ttl>\n";
		$out .= "\t\t<managingEditor>" . htmlspecialchars('support@' . parse_url($base, PHP_URL_HOST)) . " (" . htmlspecialchars($brand) . ")</managingEditor>\n";
		$out .= "\t\t<webMaster>" . htmlspecialchars('webmaster@' . parse_url($base, PHP_URL_HOST)) . "</webMaster>\n";
		$out .= "\t\t<image>\n";
		$out .= "\t\t\t<url>{$base}/public/assets/image/logo/favicon/favicon.svg</url>\n";
		$out .= "\t\t\t<title>" . htmlspecialchars($brand) . "</title>\n";
		$out .= "\t\t\t<link>{$base}</link>\n";
		$out .= "\t\t\t<width>144</width>\n";
		$out .= "\t\t\t<height>144</height>\n";
		$out .= "\t\t</image>\n";

		foreach (self::items() as $it) {
			$out .= "\t\t<item>\n";
			$out .= "\t\t\t<title>" . htmlspecialchars($it['title']) . "</title>\n";
			$out .= "\t\t\t<link>" . htmlspecialchars($it['link']) . "</link>\n";
			$out .= "\t\t\t<guid isPermaLink=\"true\">" . htmlspecialchars($it['link']) . "</guid>\n";
			$out .= "\t\t\t<description><![CDATA[" . $it['desc'] . "]]></description>\n";
			if (!empty($it['content'])) {
				$out .= "\t\t\t<content:encoded><![CDATA[" . $it['content'] . "]]></content:encoded>\n";
			}
			$out .= "\t\t\t<pubDate>" . $it['date'] . "</pubDate>\n";
			if (!empty($it['category'])) {
				$out .= "\t\t\t<category>" . htmlspecialchars($it['category']) . "</category>\n";
			}
			if (!empty($it['author'])) {
				$authorName = htmlspecialchars($it['author']['name']);
				$authorRole = !empty($it['author']['role']) ? ' — ' . htmlspecialchars($it['author']['role']) : '';
				$out .= "\t\t\t<dc:creator>" . $authorName . $authorRole . "</dc:creator>\n";
				if (!empty($it['author']['uri'])) {
					$out .= "\t\t\t<author>" . $authorName . $authorRole . " (" . htmlspecialchars($it['author']['uri']) . ")</author>\n";
				}
			}
			if (!empty($it['image'])) {
				$out .= "\t\t\t<media:content url=\"" . htmlspecialchars($it['image']) . "\" medium=\"image\" type=\"image/jpeg\" />\n";
				$out .= "\t\t\t<enclosure url=\"" . htmlspecialchars($it['image']) . "\" type=\"image/jpeg\" />\n";
			}
			$out .= "\t\t</item>\n";
		}
		$out .= "\t</channel>\n";
		$out .= '</rss>';
		self::emit($out);
	}

	public static function outputTurbo(): void
	{
		$base = rtrim(seo_config('base_url'), '/');
		$brand = seo_config('brand');
		$phoneTel = (string)preg_replace('/\D/', '', (string)seo_config('phone'));
		$phone = (string)seo_config('phone_display');
		$metrikaId = seo_config('ym_id') ?? '';

		$out = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
		$out .= '<rss version="2.0" xmlns:turbo="http://turbo.yandex.ru" xmlns:media="http://search.yahoo.com/mrss/" xmlns:dc="http://purl.org/dc/elements/1.1/">' . "\n";
		$out .= "\t<channel>\n";
		$out .= "\t\t<title>" . htmlspecialchars($brand . ' — услуги и экспертиза') . "</title>\n";
		$out .= "\t\t<link>{$base}</link>\n";
		$out .= "\t\t<description>" . htmlspecialchars('Быстровозводимые здания под ключ, капремонт, спецтехника. Москва и МО.') . "</description>\n";
		$out .= "\t\t<language>ru</language>\n";

		// Turbo analytics
		if ($metrikaId) {
			$out .= "\t\t<turbo:analytics type=\"Yandex\" id=\"" . htmlspecialchars($metrikaId) . "\" />\n";
		}

		// Turbo ad network (optional - can be configured)
		// $out .= "\t\t<turbo:adNetwork type=\"Yandex\" id=\"...\" />\n";

		foreach (self::items() as $it) {
			$out .= "\t\t<item turbo=\"true\">\n";
			$out .= "\t\t\t<title>" . htmlspecialchars($it['title']) . "</title>\n";
			$out .= "\t\t\t<link>" . htmlspecialchars($it['link']) . "</link>\n";
			$out .= "\t\t\t<pubDate>" . $it['date'] . "</pubDate>\n";
			if (!empty($it['category'])) {
				$out .= "\t\t\t<category>" . htmlspecialchars($it['category']) . "</category>\n";
			}
			if (!empty($it['author'])) {
				$authorName = htmlspecialchars($it['author']['name']);
				$authorRole = !empty($it['author']['role']) ? ' — ' . htmlspecialchars($it['author']['role']) : '';
				$out .= "\t\t\t<author>" . $authorName . $authorRole . "</author>\n";
			}
			if (!empty($it['image'])) {
				$out .= "\t\t\t<media:content url=\"" . htmlspecialchars($it['image']) . "\" medium=\"image\" />\n";
			}

			// Build turbo content
			$content = '<header><h1>' . htmlspecialchars($it['title']) . '</h1></header>';
			if (!empty($it['author'])) {
				$content .= '<p style="font-size:0.9em;color:#666;"><strong>Автор:</strong> ' . htmlspecialchars($it['author']['name']) . (!empty($it['author']['role']) ? ' — ' . htmlspecialchars($it['author']['role']) : '') . '</p>';
			}
			if (!empty($it['category'])) {
				$content .= '<p style="font-size:0.9em;color:#666;"><strong>Категория:</strong> ' . htmlspecialchars($it['category']) . '</p>';
			}
			if (!empty($it['image'])) {
				$content .= '<figure><img src="' . htmlspecialchars($it['image']) . '" alt="' . htmlspecialchars($it['title']) . '"></figure>';
			}
			$content .= '<p>' . htmlspecialchars($it['desc']) . '</p>';
			if (!empty($it['content'])) {
				$content .= $it['content'];
			}
			$content .= '<p><a href="' . htmlspecialchars($it['link']) . '">Подробнее на сайте</a> · <a href="tel:' . $phoneTel . '">' . htmlspecialchars($phone) . '</a></p>';

			$out .= "\t\t\t<turbo:content><![CDATA[" . $content . "]]></turbo:content>\n";
			$out .= "\t\t</item>\n";
		}
		$out .= "\t</channel>\n";
		$out .= '</rss>';
		self::emit($out, 'application/rss+xml; charset=utf-8');
	}
}