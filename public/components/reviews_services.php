<?php
/**
 * Отзывы на страницах услуг (E-E-A-T). Источник — app/Data/reviews.php.
 * Подключается из service_template.php. $svcType может быть задан вызывающим шаблоном.
 */
$__hr = function ($s) { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); };
$__reviews = function_exists('seo_reviews') ? seo_reviews() : [];
if (!$__reviews) {
	return;
}
$__avg = round(array_sum(array_column($__reviews, 'star')) / max(1, count($__reviews)), 1);
$__type = isset($svcType) ? (string)$svcType : '';
?>
<section class="page-section view" id="reviews">
	<div class="section-heading"><h2>Отзывы заказчиков</h2><span class="heading-divider" aria-hidden="true"></span><p>Средняя оценка <?= $__avg ?>/5 по <?= count($__reviews) ?> отзывам<?= $__type !== '' ? ' — ' . $__hr($__type) : '' ?></p></div>
	<div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(260px,1fr));gap:16px;margin-top:22px">
		<?php foreach (array_slice($__reviews, 0, 3) as $r): ?>
		<figure class="card-prem" style="margin:0;padding:22px;border-radius:18px">
			<div style="color:#F97316;letter-spacing:2px" aria-label="Рейтинг <?= (int)$r['star'] ?> из 5"><?php for ($i = 0; $i < 5; $i++) { echo $i < $r['star'] ? '<i class="fa-solid fa-star"></i>' : '<i class="fa-regular fa-star"></i>'; } ?></div>
			<blockquote style="margin:10px 0;font-size:15px;line-height:1.6"><?= $__hr($r['text']) ?></blockquote>
			<figcaption style="font-size:13px;opacity:.8"><strong><?= $__hr($r['name']) ?></strong> — <?= $__hr($r['company']) ?> · <time datetime="<?= $__hr($r['date']) ?>"><?= $__hr($r['date']) ?></time></figcaption>
		</figure>
		<?php endforeach; ?>
	</div>
</section>
