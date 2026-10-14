<?php
/**
 * Перелинковка «PageRank Loop» + FAQ-блок.
 * Подключается в конце страницы услуги:  include_once dirname(__DIR__,3).'/components/related_services.php';
 * Линкуем только на существующие страницы (битых ссылок не будет — источник services.php).
 */

if (preg_match('#^/services/([^/]+)$#', seo_url(), $m)) {
	$__slug = $m[1];
} else {
	$__slug = '';
}
$__all = seo_services();
$__base = rtrim(seo_config('base_url'), '/');
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
];
$__related = [];
foreach ($__relatedMap[$__slug] ?? [] as $r) {
	if (isset($__all[$r])) {
		$__related[] = ['slug' => $r, 'title' => $__all[$r]['title'], 'desc' => mb_substr($__all[$r]['description'], 0, 110) . '…'];
	}
}
if ($__related): ?>
	<section class="py-12 bg-[#f0fdf4]/60 border-t border-emerald-100">
		<div class="mx-auto max-w-[1280px] px-4">
			<h2 class="text-2xl md:text-3xl font-black tracking-tight text-gray-900">Связанные услуги</h2>
			<p class="mt-2 text-sm text-gray-600">Чаще всего заказывают вместе — можно одним договором и одним выездом инженера.</p>
			<div class="mt-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
			<?php foreach ($__related as $r): ?>
				<a href="/services/<?= htmlspecialchars($r['slug']) ?>" data-goal="related_click"
						class="group bg-white rounded-2xl border border-gray-100 p-5 shadow-sm hover:shadow-lg hover:-translate-y-0.5 transition">
						<div class="text-[15px] font-bold text-gray-900 group-hover:text-emerald-700"><?= htmlspecialchars($r['title']) ?></div>
						<p class="mt-2 text-xs leading-relaxed text-gray-600"><?= htmlspecialchars($r['desc']) ?></p>
						<span class="mt-3 inline-flex items-center gap-1.5 text-xs font-semibold text-emerald-700">Подробнее <i class="fa-solid fa-arrow-right text-[10px]"></i></span>
					</a>
				<?php endforeach; ?>
			</div>
			<div class="mt-6 text-sm text-gray-600">
			Смотрите также: <a href="/uslugi" class="text-emerald-700 font-semibold underline">все услуги</a> ·
			<a href="/portfolio" class="text-emerald-700 font-semibold underline">портфолио объектов</a> ·
			<a href="/portfolio" class="text-emerald-700 font-semibold underline">портфолио объектов</a>
			</div>
		</div>
	</section>
<?php endif; ?>

<?php if (!empty($__current['faq'])): ?>
	<section id="faq" class="py-12 bg-white border-t border-gray-100">
		<div class="mx-auto max-w-[900px] px-4">
			<h2 class="text-2xl md:text-3xl font-black tracking-tight text-gray-900">Частые вопросы</h2>
			<div class="mt-6 divide-y divide-gray-100">
				<?php foreach ($__current['faq'] as $i => $qa): ?>
					<details class="faq-item group py-4" <?= $i === 0 ? 'open' : '' ?>>
						<summary class="flex cursor-pointer items-center justify-between gap-4 text-[15px] font-bold text-gray-900 marker:content-none">
							<?= htmlspecialchars($qa[0]) ?>
							<span class="faq-arrow" aria-hidden="true"></span>
						</summary>
						<p class="mt-3 text-sm leading-relaxed text-gray-600"><?= htmlspecialchars($qa[1]) ?></p>
					</details>
				<?php endforeach; ?>
			</div>
			<p class="mt-6 text-sm text-gray-600">Не нашли ответ? Позвоните
				<a href="tel:<?= htmlspecialchars(str_replace([' ', '(', ')', '-'], '', seo_config('phone'))) ?>" class="font-bold text-emerald-700" data-goal="phone_click_faq">
					<?= htmlspecialchars(seo_config('phone_display')) ?></a> — ответим за <?= htmlspecialchars(seo_config('callback_minutes')) ?> минут.
			</p>
		</div>
	</section>
<?php endif; ?>
