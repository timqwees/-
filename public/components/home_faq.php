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
$__base = rtrim(seo_config('base_url'), '/');
?>
<section id="faq" class="view relative isolate w-full bg-[#f3f4ef] text-[#3d5951]">
	<div class="relative mx-auto max-w-[1000px] px-4 pb-[80px] pt-[72px]">
		<div class="section-heading">
			<h2>Частые вопросы</h2>
			<span class="heading-divider" aria-hidden="true"></span>
			<p>Сроки, цены и гарантии — коротко и по делу</p>
		</div>
		<div class="mt-10 grid gap-3 md:grid-cols-2">
			<?php foreach ($__homeFaq as $i => $item): ?>
				<details class="faq-ex group" <?= $i < 2 ? 'open' : '' ?>>
					<summary><span class="faq-mark" aria-hidden="true"></span><span><?= htmlspecialchars($item['q']) ?></span></summary>
					<div class="faq-content">
						<p class="faq-a"><?= htmlspecialchars($item['a']) ?></p>
						<a href="<?= $__base ?>/services/<?= htmlspecialchars($item['slug']) ?>" title="Подробнее об услуге" class="mt-3 inline-block text-xs font-semibold text-emerald-700 underline" style="margin-left:24px">Подробнее об услуге</a>
					</div>
				</details>
			<?php endforeach; ?>
		</div>
	</div>
		<!-- Organic bottom edge -->
		<div class="pointer-events-none absolute inset-x-0 -bottom-[86px] z-20 h-[86px]">
			<div class="absolute -top-px left-0 h-[87px] w-[36%] rounded-br-[82px] bg-[#f3f4ef]"></div>
		</div>
	</section>