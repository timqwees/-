<?php
/**
 * FAQ главной страницы — сборка из семантического ядра (app/Data/services.php).
 * Закрывает информационные интенты («сколько стоит», «какой срок», «как выбрать»)
 * и даёт готовый FAQPage + готовые ответы для AI Overviews / RageRank.
 */

$__homeFaq = [];
foreach (seo_services() as $slug => $s) {
	foreach ($s['faq'] as $qa) {
		$__homeFaq[] = ['q' => $qa[0], 'a' => $qa[1], 'slug' => $slug];
	}
}
$__homeFaq = array_slice($__homeFaq, 0, 12);
$__base = rtrim((string)seo_config('base_url'), '/');
// Счётчик разделов: задаётся вызывающей страницей (home.php).
if (!isset($fssSec) || !is_callable($fssSec)) {
	$fssSec = function (string $label): string {
		static $n = 0;
		$n++;
		return str_pad((string)$n, 2, '0', STR_PAD_LEFT) . ' / ' . $label;
	};
}
?>
<section id="faq" class="faq-section section-shell view">
	<div class="section-topline">
		<p class="eyebrow"><?= htmlspecialchars($fssSec('Вопросы'), ENT_QUOTES, 'UTF-8') ?></p>
		<span class="eyebrow">Сроки, цены и гарантии</span>
	</div>
	<div class="head-row">
		<h2>Частые<br><span>вопросы.</span></h2>
		<p>Коротко и по делу — без мелкого шрифта.<br>Подробнее — на странице услуги.</p>
	</div>
	<div class="faq-grid">
		<?php foreach ($__homeFaq as $i => $item): ?>
			<details class="faq-ex" <?= $i < 2 ? 'open' : '' ?>>
				<summary><span><?= htmlspecialchars($item['q']) ?></span><span class="faq-mark" aria-hidden="true"></span></summary>
				<div class="faq-content">
					<p class="faq-a"><?= htmlspecialchars($item['a']) ?></p>
					<a href="<?= $__base ?>/services/<?= htmlspecialchars($item['slug']) ?>" title="Подробнее об услуге" class="faq-more">Подробнее об услуге</a>
				</div>
			</details>
		<?php endforeach ?>
	</div>
</section>