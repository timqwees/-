<?php
/**
 * Перелинковка «PageRank Loop» + блок связанных услуг.
 * Подключается в конце страницы услуги:  include_once dirname(__DIR__,3).'/components/related_services.php';
 * Линкуем только на существующие страницы (битых ссылок не будет — источник services.php).
 *
 * Оформление — по дизайн-системе главной (ProMaintenance).
 */

// Счётчик разделов: задаётся вызывающим шаблоном (service_template.php),
// здесь — запасной вариант, если блок подключат отдельно.
if (!isset($fssSec) || !is_callable($fssSec)) {
	$fssSec = function (string $label): string {
		static $n = 0;
		$n++;
		return str_pad((string)$n, 2, '0', STR_PAD_LEFT) . ' / ' . $label;
	};
}

// Услуга страницы: обычная /services/{slug} и гео-посадочная /services/{slug}/{geo}
if (preg_match('#^/services/([^/]+)(?:/[^/]+)?$#', seo_url(), $m)) {
	$__slug = $m[1];
} else {
	$__slug = '';
}
$__all = seo_services();
$__base = rtrim((string)seo_config('base_url'), '/');
$__current = seo_service($__slug);

// Карта «соседних» услуг: хаб ↔ услуга ↔ услуга (замыкаем петлю веса)
$__relatedMap = [
	'angar' => ['proizvodim_modul', 'sclad', 'kapstroenie', 'spectehnika'],
	'proizvodim_modul' => ['angar', 'sclad', 'administrativnoe', 'obchegitia', 'proektirovanie', 'kapstroenie'],
	'kapstroenie' => ['angar', 'kapital', 'sclad', 'proizvodim_modul', 'montach', 'proektirovanie'],
	'kapital' => ['kapstroenie', 'montach', 'spectehnika', 'gruzoperevozki'],
	'sclad' => ['angar', 'proizvodim_modul', 'administrativnoe', 'gruzoperevozki', 'montach'],
	'administrativnoe' => ['proizvodim_modul', 'obchegitia', 'modultebel', 'montach'],
	'obchegitia' => ['administrativnoe', 'modultebel', 'proizvodim_modul', 'kapital'],
	'gruzoperevozki' => ['spectehnika', 'postavka', 'sclad', 'kapstroenie'],
	'spectehnika' => ['gruzoperevozki', 'kapstroenie', 'naves'],
	'musorbak' => ['naves', 'proizvodim_modul', 'sclad'],
	'naves' => ['musorbak', 'sclad', 'kapstroenie'],
	'generatornae' => ['montach', 'compressor', 'compressor_air', 'kapstroenie'],
	'compressor' => ['compressor_air', 'generatornae', 'montach'],
	'compressor_air' => ['compressor', 'generatornae', 'montach'],
	'montach' => ['kapital', 'generatornae', 'kapstroenie'],
	'modultebel' => ['obchegitia', 'administrativnoe', 'postavka'],
	'postavka' => ['proizvodim_modul', 'gruzoperevozki', 'kapstroenie'],
	'proektirovanie' => ['kapstroenie', 'proizvodim_modul', 'sclad', 'administrativnoe'],
	'osnashchenie' => ['proizvodim_modul', 'modultebel', 'postavka'],
	'bpla' => ['spectehnika', 'gruzoperevozki', 'postavka'],
];
$__related = [];
foreach ($__relatedMap[$__slug] ?? [] as $r) {
	if (isset($__all[$r])) {
		$__related[] = ['slug' => $r, 'title' => $__all[$r]['title'], 'desc' => mb_substr($__all[$r]['description'], 0, 110) . '…'];
	}
}
if ($__related): ?>
	<section class="svc-section section-shell" id="related" aria-labelledby="related-title">
		<div class="section-topline">
			<p class="eyebrow"><?= htmlspecialchars($fssSec('Связанные услуги'), ENT_QUOTES, 'UTF-8') ?></p>
			<span class="eyebrow">Чаще заказывают вместе</span>
		</div>
		<div class="work-heading">
			<h2 id="related-title">Связанные<br>услуги</h2>
			<p>Чаще всего заказывают вместе — можно одним договором<br>и одним выездом инженера.</p>
		</div>
		<div class="svc-grid">
			<?php foreach ($__related as $r): ?>
				<a class="svc-panel" href="/services/<?= htmlspecialchars($r['slug']) ?>" title="<?= htmlspecialchars($r['title']) ?>" data-goal="related_click" style="text-decoration:none">
					<h3><?= htmlspecialchars($r['title']) ?></h3>
					<p><?= htmlspecialchars($r['desc']) ?></p>
					<span class="text-link" style="margin-top:16px;color:inherit;text-decoration:none">Подробнее
						<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">
							<path d="M7 7h10v10"></path>
							<path d="M7 17 17 7"></path>
						</svg>
					</span>
				</a>
			<?php endforeach; ?>
		</div>
		<p class="svc-note">Смотрите также: <a href="/uslugi" title="Все услуги" style="text-decoration:underline;text-underline-offset:3px">все услуги</a> · <a href="/portfolio" title="Портфолио объектов" style="text-decoration:underline;text-underline-offset:3px">портфолио объектов</a>.</p>
	</section>
<?php endif; ?>
