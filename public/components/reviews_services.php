<?php
/**
 * Отзывы на страницах услуг (E-E-A-T). Источник — app/Data/reviews.php.
 * Подключается из service_template.php. $svcType может быть задан вызывающим шаблоном.
 *
 * Отзывы выводятся только если они реальные (app/Data/reviews.php не пуст).
 * Иначе показывается блок с проверяемыми фактами: заказчики, объёмы работ,
 * сроки и выполненные кейсы по этому направлению.
 *
 * Оформление — по дизайн-системе главной (ProMaintenance): трекированные
 * подписи, крупные заголовки, карточки с волосяной рамкой.
 */
$__hr = function ($s) { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); };
// Счётчик разделов: задаётся вызывающим шаблоном (service_template.php),
// здесь — запасной вариант, если блок подключат отдельно.
if (!isset($fssSec) || !is_callable($fssSec)) {
	$fssSec = function (string $label): string {
		static $n = 0;
		$n++;
		return str_pad((string)$n, 2, '0', STR_PAD_LEFT) . ' / ' . $label;
	};
}
$__reviews = function_exists('seo_reviews') ? seo_reviews() : [];
$__type = isset($svcType) ? (string)$svcType : '';

if (!$__reviews) {
	// --- Факты вместо отзывов ---
	$__facts = [
		['Работаем с силовыми и государственными структурами', 'В настоящее время сотрудничаем с Федеральной службой войск национальной гвардии Российской Федерации (Росгвардия).'],
		['Кол-во объектов в портфолио', seo_config('objects') . ' объектов: ' . seo_config('kapremont_objects') . ' социальных объектов капитального ремонта, оснащение ' . seo_config('schools_equipped') . ' школ, общежития ' . seo_config('sochi_area') . ' в Сочи.'],
		['Смета и фиксация цены', 'Смету считаем за ' . seo_config('price_response_minutes') . ' минут, цена фиксируется в договоре и не растёт в процессе работ.'],
		['Гарантия и документы', 'Гарантия ' . seo_config('guarantee_years') . ' лет на несущие конструкции, работа по 44-ФЗ и 223-ФЗ, допуск СРО, закрывающие документы по каждому объекту.'],
	];
	?>
	<section class="svc-section section-shell" id="reviews" aria-labelledby="reviews-title">
		<div class="section-topline">
			<p class="eyebrow"><?= $__hr($fssSec('Доверие')) ?></p>
			<span class="eyebrow">Проверяемые факты</span>
		</div>
		<div class="work-heading">
			<h2 id="reviews-title">Почему нам<br>доверяют</h2>
			<p>Факты о выполненных работах<?= $__type !== '' ? ' по направлению «' . $__hr($__type) . '»' : '' ?><br>без вымышленных имён и оценок.</p>
		</div>
		<ul class="trust-rail" aria-label="Заказчики компании">
			<li class="trust-rail-label">Работаем с</li>
			<li>Росгвардия России</li>
			<li>Минпросвещение РФ</li>
			<li>Муниципальные бюджетные учреждения</li>
			<li>Государственные школы и учреждения</li>
			<li>Силовые структуры</li>
		</ul>
		<div class="svc-grid">
			<?php foreach ($__facts as $__f): ?>
				<article class="svc-panel">
					<h3><?= $__hr($__f[0]) ?></h3>
					<p><?= $__hr($__f[1]) ?></p>
				</article>
			<?php endforeach; ?>
		</div>
		<p class="svc-note">Детальные кейсы — в <a href="/portfolio" title="Портфолио объектов" style="text-decoration:underline;text-underline-offset:3px">портфолио</a>.</p>
	</section>
	<?php
	return;
}

$__avg = round(array_sum(array_column($__reviews, 'star')) / max(1, count($__reviews)), 1);
?>
<section class="svc-section section-shell" id="reviews" aria-labelledby="reviews-title">
	<div class="section-topline">
		<p class="eyebrow"><?= $__hr($fssSec('Отзывы')) ?></p>
		<span class="eyebrow">Средняя оценка <?= $__avg ?>/5</span>
	</div>
	<div class="work-heading">
		<h2 id="reviews-title">Отзывы<br>заказчиков</h2>
		<p>Средняя оценка <?= $__avg ?>/5 по <?= count($__reviews) ?> отзывам<?= $__type !== '' ? ' — ' . $__hr($__type) : '' ?>.</p>
	</div>
	<div class="svc-grid">
		<?php foreach (array_slice($__reviews, 0, 3) as $r): ?>
			<figure class="svc-panel" style="margin:0">
				<div style="color:var(--brand);letter-spacing:2px" aria-label="Рейтинг <?= (int)$r['star'] ?> из 5"><?php for ($i = 0; $i < 5; $i++) { echo $i < $r['star'] ? '<i class="fa-solid fa-star"></i>' : '<i class="fa-regular fa-star"></i>'; } ?></div>
				<blockquote style="margin:14px 0 0;font-size:14.5px;line-height:1.7;color:var(--ink-soft)"><?= $__hr($r['text']) ?></blockquote>
				<figcaption style="margin-top:16px;padding-top:14px;border-top:1px solid var(--line);font-size:12px;color:var(--muted)"><strong style="color:var(--ink)"><?= $__hr($r['name']) ?></strong> — <?= $__hr($r['company']) ?> · <time datetime="<?= $__hr($r['date']) ?>"><?= $__hr($r['date']) ?></time></figcaption>
			</figure>
		<?php endforeach; ?>
	</div>
</section>
