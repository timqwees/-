<?php

declare(strict_types=1);

namespace Setting\Route\Functions;

/**
 * YML-фид для Яндекс.Вебмастер → Товары/Услуги. Роуты: /yml.xml, /feed.yml.
 * Только офферы с ценой из app/Data/services.php, URL канонические.
 */
final class YmlFeed
{
	public static function output(): void
	{
		$base = rtrim((string)seo_config('base_url'), '/');
		$root = dirname(__DIR__, 3);
		$services = file_exists($root . '/app/Data/services.php')
			? (require $root . '/app/Data/services.php')
			: [];

		$cats = ['БВЗ' => 1, 'Ремонт и монтаж' => 2, 'Перевозки и техника' => 3, 'Оборудование и мебель' => 4, 'БПЛА и системы КИТ' => 5];
		$catOf = [
			'proizvodim_modul' => 1, 'angar' => 1, 'sclad' => 1, 'administrativnoe' => 1, 'obchegitia' => 1,
			'kapstroenie' => 2, 'kapital' => 2, 'montach' => 2, 'naves' => 2, 'proektirovanie' => 2,
			'gruzoperevozki' => 3, 'spectehnika' => 3, 'postavka' => 3,
			'musorbak' => 4, 'modultebel' => 4, 'generatornae' => 4, 'compressor' => 4, 'compressor_air' => 4,
			'osnashchenie' => 4, 'bpla' => 5,
		];

		$xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
		$xml .= '<yml_catalog date="' . date('Y-m-d H:i') . '">' . "\n<shop>\n";
		$xml .= '<name>' . htmlspecialchars((string)seo_config('brand')) . '</name>' . "\n";
		$xml .= '<company>ООО "' . htmlspecialchars((string)seo_config('brand')) . '"</company>' . "\n";
		$xml .= '<url>' . $base . '/</url>' . "\n";
		$xml .= '<currencies><currency id="RUB" rate="1"/></currencies>' . "\n<categories>\n";
		foreach ($cats as $name => $id) {
			$xml .= '<category id="' . $id . '">' . htmlspecialchars($name) . '</category>' . "\n";
		}
		$xml .= "</categories>\n<offers>\n";
		foreach ((array)$services as $slug => $s) {
			if (!is_file($root . '/public/pages/services/' . $slug . '/index.php')) {
				continue;
			}
			if (empty($s['price_from'])) {
				continue;
			}
			$price = (int)preg_replace('/\D/', '', (string)$s['price_from']);
			if ($price <= 0) {
				continue;
			}
			$xml .= '<offer id="' . htmlspecialchars((string)$slug) . '" available="true">' . "\n";
			$xml .= '<url>' . htmlspecialchars($base . '/services/' . $slug) . '</url>' . "\n";
			$xml .= '<price>' . $price . '</price>' . "\n";
			$xml .= '<currencyId>RUB</currencyId>' . "\n";
			$xml .= '<categoryId>' . ($catOf[$slug] ?? 1) . '</categoryId>' . "\n";
			$xml .= '<name>' . htmlspecialchars((string)$s['title']) . '</name>' . "\n";
			// Без CDATA: строка с "]]>" ломала бы XML-фид. Экранируем спецсимволы.
			$xml .= '<description>' . htmlspecialchars((string)($s['description'] ?? ''), ENT_XML1 | ENT_QUOTES, 'UTF-8') . '</description>' . "\n";
			$xml .= "</offer>\n";
		}
		$xml .= "</offers>\n</shop>\n</yml_catalog>";

		SecurityHeaders::emitCached($xml, 'application/xml; charset=utf-8');
	}
}
