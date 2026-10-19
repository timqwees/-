<?php
/**
 * Поисковый виджет блога — самодостаточный компонент.
 *
 * Внутри: данные блога (посты, категории, теги), разметка и скрипт.
 * Компонент ничего не ждёт снаружи и безопасен для подключения на любой странице.
 *
 * Необязательные параметры (задать до include):
 *   $bs_mode        'filter' — живой фильтр грида на текущей странице (по умолчанию)
 *                   'search' — форма уходит на страницу поиска (/search/blog)
 *   $bs_grid        CSS-селектор грида для фильтрации      (по умолчанию '#fss-articles-grid')
 *   $bs_empty       CSS-селектор блока «ничего не найдено» (по умолчанию '#fss-no-results')
 *   $bs_action      URL формы/ссылок в режиме 'search'     (по умолчанию '/search/blog')
 *   $bs_query       текущий поисковый запрос  (режим 'search')
 *   $bs_cat         активная категория (slug) (режим 'search')
 *   $bs_tag         активный тег              (режим 'search')
 *   $bs_placeholder подпись поля поиска
 *   $bs_show_count  показывать счётчик публикаций (по умолчанию только в 'filter')
 *   $bs_show_tags   показывать чипсы тегов        (режим 'search', по умолчанию true)
 *   $bs_cats_limit  ограничить число категорий (0 = все)
 *   $bs_tags_limit  ограничить число тегов     (по умолчанию 20)
 */

if (!function_exists('seo_blog_posts')) {
	require_once dirname(__DIR__, 2) . '/app/bootstrap.php';
}

$bs_mode        = $bs_mode        ?? 'filter';
$bs_grid        = $bs_grid        ?? '#fss-articles-grid';
$bs_empty       = $bs_empty       ?? '#fss-no-results';
$bs_action      = $bs_action      ?? '/search/blog';
$bs_query       = $bs_query       ?? '';
$bs_cat         = $bs_cat         ?? '';
$bs_tag         = $bs_tag         ?? '';
$bs_placeholder = $bs_placeholder ?? 'Поиск по статьям...';
$bs_show_count  = $bs_show_count  ?? ($bs_mode === 'filter');
$bs_show_tags   = $bs_show_tags   ?? true;
$bs_cats_limit  = (int)($bs_cats_limit ?? 0);
$bs_tags_limit  = (int)($bs_tags_limit ?? 20);

$bs_posts = seo_blog_posts();
$bs_total = count($bs_posts);

$bs_categories = [];
$bs_tag_counts = [];
foreach ($bs_posts as $bs_p) {
	$bs_categories[$bs_p['category_slug']] = $bs_p['category_name'];
	foreach ($bs_p['tags'] as $bs_t) {
		$bs_tag_counts[$bs_t] = ($bs_tag_counts[$bs_t] ?? 0) + 1;
	}
}
if ($bs_cats_limit > 0) {
	$bs_categories = array_slice($bs_categories, 0, $bs_cats_limit, true);
}
arsort($bs_tag_counts);
$bs_tags = $bs_tags_limit > 0
	? array_slice(array_keys($bs_tag_counts), 0, $bs_tags_limit)
	: array_keys($bs_tag_counts);

$bs_e = static fn($s): string => htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8');

$GLOBALS['__bs_uid'] = ($GLOBALS['__bs_uid'] ?? 0) + 1;
$bs_uid = $GLOBALS['__bs_uid'];
$bs_input_id = 'bs-q-' . $bs_uid;

$bs_link = static function (array $over = []) use ($bs_action, $bs_query, $bs_cat, $bs_tag): string {
	$p = array_merge(['q' => $bs_query, 'cat' => $bs_cat, 'tag' => $bs_tag], $over);
	$p = array_filter($p, static fn($v) => $v !== '' && $v !== null);
	return $bs_action . ($p ? '?' . http_build_query($p) : '');
};
?>
<?php if ($bs_mode === 'search'): ?>
<div class="fss-blog-search" data-bs-widget data-bs-mode="search">
	<form method="get" action="<?= $bs_e($bs_action) ?>" role="search" class="bsearch-form">
		<input type="search" name="q" value="<?= $bs_e($bs_query) ?>" placeholder="<?= $bs_e($bs_placeholder) ?>"
			class="bsearch-input" aria-label="Поиск по статьям" autocomplete="off">
		<button type="submit" class="btn-sun bsearch-btn" aria-label="Найти">
			<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><circle cx="11" cy="11" r="8"></circle><path d="m21 21-4.35-4.35"></path></svg>
		</button>
	</form>

	<?php if (!empty($bs_categories)): ?>
	<div class="bsearch-filter-group">
		<span class="bsearch-filter-label">Категория:</span>
		<?php foreach ($bs_categories as $bs_slug => $bs_name): ?>
		<a href="<?= $bs_e($bs_link(['cat' => $bs_slug])) ?>" title="Категория: <?= $bs_e($bs_name) ?>"
			class="bsearch-chip<?= $bs_cat === $bs_slug ? ' active' : '' ?>"><?= $bs_e($bs_name) ?></a>
		<?php endforeach; ?>
	</div>
	<?php endif; ?>

	<?php if ($bs_show_tags && !empty($bs_tags)): ?>
	<div class="bsearch-filter-group">
		<span class="bsearch-filter-label">Теги:</span>
		<?php foreach ($bs_tags as $bs_t): ?>
		<a href="<?= $bs_e($bs_link(['tag' => $bs_t])) ?>" title="Тег: #<?= $bs_e($bs_t) ?>"
			class="bsearch-chip<?= $bs_tag === $bs_t ? ' active' : '' ?>">#<?= $bs_e($bs_t) ?></a>
		<?php endforeach; ?>
	</div>
	<?php endif; ?>
</div>
<?php else: ?>
<div class="fss-blog-toolbar fss-blog-search" data-bs-widget data-bs-mode="filter"
	data-bs-grid="<?= $bs_e($bs_grid) ?>" data-bs-empty="<?= $bs_e($bs_empty) ?>" data-bs-total="<?= $bs_total ?>">
	<?php if ($bs_show_count): ?>
	<div class="fss-toolbar-count"><strong data-bs-count><?= $bs_total ?></strong> публикаций</div>
	<?php endif; ?>
	<div class="fss-toolbar-search">
		<svg class="fss-toolbar-search__icon" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
			<circle cx="11" cy="11" r="8"></circle>
			<path d="m21 21-4.35-4.35"></path>
		</svg>
		<input type="search" id="<?= $bs_e($bs_input_id) ?>" data-bs-input
			placeholder="<?= $bs_e($bs_placeholder) ?>" aria-label="Поиск по статьям" autocomplete="off">
	</div>
	<div class="fss-toolbar-filters" data-bs-cats>
		<button type="button" class="fss-cat-pill fss-cat-pill--active" data-bs-filter="all">Все статьи</button>
		<?php foreach ($bs_categories as $bs_slug => $bs_name): ?>
		<button type="button" class="fss-cat-pill" data-bs-filter="<?= $bs_e($bs_name) ?>"><?= $bs_e($bs_name) ?></button>
		<?php endforeach; ?>
	</div>
</div>
<?php endif; ?>

<?php if ($bs_mode === 'filter' && empty($GLOBALS['__bs_filter_script'])): $GLOBALS['__bs_filter_script'] = true; ?>
<script>
(function () {
	'use strict';
	function init() {
		document.querySelectorAll('[data-bs-widget][data-bs-mode="filter"]').forEach(function (widget) {
			var gridSel = widget.getAttribute('data-bs-grid') || '#fss-articles-grid';
			var emptySel = widget.getAttribute('data-bs-empty') || '#fss-no-results';
			var total = parseInt(widget.getAttribute('data-bs-total'), 10) || 0;
			var input = widget.querySelector('[data-bs-input]');
			var pills = widget.querySelectorAll('[data-bs-filter]');
			var filter = 'all';

			function apply() {
				var q = input ? input.value.trim().toLowerCase() : '';
				var visible = 0;
				document.querySelectorAll(gridSel + ' .fss-article-card').forEach(function (card) {
					var cat = (card.getAttribute('data-category') || '');
					var title = (card.getAttribute('data-title') || '').toLowerCase();
					var excerpt = (card.getAttribute('data-excerpt') || '').toLowerCase();
					var tags = (card.getAttribute('data-tags') || '').toLowerCase();
					var catMatch = filter === 'all' || cat === filter;
					var searchMatch = !q || title.indexOf(q) !== -1 || excerpt.indexOf(q) !== -1 ||
						cat.toLowerCase().indexOf(q) !== -1 || tags.indexOf(q) !== -1;
					var show = catMatch && searchMatch;
					if (show) visible++;
					card.style.display = show ? '' : 'none';
				});
				var empty = document.querySelector(emptySel);
				if (empty) empty.style.display = visible === 0 ? 'flex' : 'none';
				var count = widget.querySelector('[data-bs-count]');
				if (count) count.textContent = (!q && filter === 'all' && total) ? total : visible;
			}

			if (input) input.addEventListener('input', apply);
			pills.forEach(function (btn) {
				btn.addEventListener('click', function () {
					pills.forEach(function (b) { b.classList.remove('fss-cat-pill--active'); });
					btn.classList.add('fss-cat-pill--active');
					filter = btn.getAttribute('data-bs-filter');
					apply();
				});
			});
			apply();
		});
	}
	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', init);
	} else {
		init();
	}
})();
</script>
<?php endif; ?>
