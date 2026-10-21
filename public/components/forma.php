<?php
/**
 * Плавающий виджет быстрого расчёта.
 *
 * Дизайн — по дизайн-системе основных страниц (ProMaintenance):
 * тёмное «стекло» (как .enquiry-panel), тонкие линии, радиусы 8/6,
 * бумажный текст, акцент #c7ab94, стрелки-иконки со stroke 1.5,
 * трекированные uppercase-подписи. Виджет самодостаточен:
 * свои переменные, чтобы одинаково выглядеть на любой странице.
 *
 * Поведение:
 *   • кнопка-лаунчер открывает/закрывает панель;
 *   • Esc и клик вне панели закрывают;
 *   • после успешной отправки сервер ставит cookie fss_form_mini_forma_success —
 *     панель открывается с экраном «Заявка принята».
 */
$__fssHomePage = function_exists('seo_url') && seo_url() === '/';
$__phone = preg_replace('/\D/', '', (string)seo_config('phone'));
$__phoneDisplay = htmlspecialchars((string)seo_config('phone_display'));
?>
<div class="fss-float-widget fixed bottom-6 right-6 z-50<?= $__fssHomePage ? ' fss-float-widget--home' : '' ?>" data-fss-widget>
	<!-- Панель быстрого расчёта -->
	<div class="fss-widget-panel" id="forma_wrapper" role="dialog" aria-labelledby="fss-widget-title" hidden>
		<div class="fss-widget-head">
			<div class="fss-widget-head-text">
				<span class="fss-widget-eyebrow">Быстрый расчёт</span>
				<h3 class="fss-widget-title" id="fss-widget-title">Ответим за 15 минут</h3>
			</div>
			<button id="forma_close" type="button" class="fss-widget-close" aria-label="Закрыть форму быстрого расчёта">
				<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">
					<path d="M6 6l12 12"></path>
					<path d="M18 6 6 18"></path>
				</svg>
			</button>
		</div>

		<!-- Форма -->
		<div class="fss-widget-body" data-widget-body>
			<form class="fss-widget-form" method="post" action="/send/email" data-form="mini" novalidate>
				<input type="hidden" name="mini-forma">
				<?php
					$antibotPath = __DIR__ . '/antibot_fields.php';
					if (file_exists($antibotPath)) include $antibotPath;
				?>
				<div class="fss-widget-field">
					<label for="mini_name">Ваше имя</label>
					<input id="mini_name" name="name" type="text" placeholder="Как к вам обращаться" required autocomplete="name" minlength="2" maxlength="100">
				</div>
				<div class="fss-widget-field">
					<label for="mini_phone">Телефон</label>
					<input id="mini_phone" name="phone" type="tel" maxlength="18" placeholder="+7 (___) ___-__-__" required autocomplete="tel" pattern="^\+?[0-9\s\-\(\)]{10,20}$">
				</div>
				<button type="submit" class="fss-widget-submit">
					<span>Отправить заявку</span>
					<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">
						<path d="M7 7h10v10"></path>
						<path d="M7 17 17 7"></path>
					</svg>
				</button>
				<p class="fss-widget-note">Нажимая кнопку, вы соглашаетесь с <a href="/politika" title="Политика конфиденциальности">политикой конфиденциальности</a>.</p>
			</form>
		</div>

		<!-- Экран «Заявка принята» -->
		<div class="fss-widget-success" data-widget-success hidden>
			<span class="fss-widget-check" aria-hidden="true">
				<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
					<path d="M20 6 9 17l-5-5"></path>
				</svg>
			</span>
			<span class="fss-widget-eyebrow">Заявка принята</span>
			<h3 class="fss-widget-success-title">Перезвоним в течение 15 минут</h3>
			<p class="fss-widget-success-text">Оставили запрос на расчёт — сейчас свяжемся и уточним детали проекта.</p>
			<button type="button" class="fss-widget-submit" data-widget-reset>
				<span>Отправить ещё одну</span>
				<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">
					<path d="M5 12h14"></path>
					<path d="m12 5 7 7-7 7"></path>
				</svg>
			</button>
		</div>

		<!-- Телефон -->
		<div class="fss-widget-foot">
			<span class="fss-widget-eyebrow">Или позвоните</span>
			<a class="fss-widget-phone" href="tel:+<?= $__phone ?>" title="Позвонить: <?= $__phoneDisplay ?>">
				<span><?= $__phoneDisplay ?></span>
				<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">
					<path d="M7 17 17 7"></path>
					<path d="M7 7h10v10"></path>
				</svg>
			</a>
		</div>
	</div>

	<!-- Кнопка-лаунчер -->
	<button id="forma_toggle" type="button" class="fss-widget-launcher" aria-expanded="false" aria-controls="forma_wrapper"
		title="Быстрый расчёт" aria-label="Открыть форму быстрого расчёта">
		<svg class="fss-widget-icon-chat" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">
			<path d="M21 12a8 8 0 0 1-8 8H4l2.3-2.9A8 8 0 1 1 21 12Z"></path>
		</svg>
		<svg class="fss-widget-icon-close" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">
			<path d="M6 6l12 12"></path>
			<path d="M18 6 6 18"></path>
		</svg>
	</button>
</div>

<style>
	/* ============================================================
	   ПЛАВАЮЩИЙ ВИДЖЕТ БЫСТРОГО РАСЧЁТА (дизайн-система ProMaintenance)
	   Тёмное «стекло», тонкие линии, акцент #c7ab94, радиусы 8/6.
	   ============================================================ */
	.fss-float-widget {
		--fw-paper: #f0efed;
		--fw-ink: #171310;
		--fw-accent: #c7ab94;
		--fw-muted: #a6a09a;
		--fw-line: rgba(255, 255, 255, .16);
		--fw-radius-surface: 8px;
		--fw-radius-control: 6px;
		--fw-font: 'DM Sans Variable', 'Wix Madefor Display', Manrope, system-ui, -apple-system, sans-serif;
		font-family: var(--fw-font);
	}

	.fss-float-widget *,
	.fss-float-widget input,
	.fss-float-widget button {
		font-family: var(--fw-font);
	}

	/* ── Кнопка-лаунчер ─────────────────────────────────────────── */
	.fss-widget-launcher {
		position: relative;
		display: grid;
		place-items: center;
		width: 56px;
		height: 56px;
		padding: 0;
		border: 1px solid rgba(255, 255, 255, .26);
		border-radius: 50%;
		background: rgba(23, 19, 16, .78);
		backdrop-filter: blur(14px);
		-webkit-backdrop-filter: blur(14px);
		color: var(--fw-paper);
		box-shadow: 0 12px 30px rgba(23, 19, 16, .28);
		cursor: pointer;
		transition: background-color .3s var(--ease, cubic-bezier(.2, .8, .2, 1)),
			border-color .3s, transform .3s var(--ease, cubic-bezier(.2, .8, .2, 1)),
			box-shadow .3s;
	}

	.fss-widget-launcher:hover {
		background: rgba(23, 19, 16, .92);
		border-color: var(--fw-accent);
		transform: translateY(-2px);
		box-shadow: 0 16px 36px rgba(23, 19, 16, .34);
	}

	.fss-widget-launcher:focus-visible {
		outline: 2px solid var(--fw-accent);
		outline-offset: 5px;
	}

	.fss-widget-launcher svg {
		transition: transform .3s var(--ease, cubic-bezier(.2, .8, .2, 1)), opacity .2s;
	}

	.fss-widget-icon-close {
		position: absolute;
		opacity: 0;
		transform: rotate(-45deg) scale(.7);
	}

	.fss-float-widget.is-open .fss-widget-icon-chat {
		opacity: 0;
		transform: rotate(45deg) scale(.7);
	}

	.fss-float-widget.is-open .fss-widget-icon-close {
		opacity: 1;
		transform: rotate(0) scale(1);
	}

	/* ── Панель ─────────────────────────────────────────────────── */
	.fss-widget-panel {
		position: absolute;
		right: 0;
		bottom: calc(100% + 16px);
		z-index: 2;
		width: 344px;
		max-width: calc(100vw - 32px);
		max-height: calc(100vh - 120px);
		overflow-y: auto;
		overscroll-behavior: contain;
		padding: 26px 26px 22px;
		border: 1px solid var(--fw-line);
		border-radius: var(--fw-radius-surface);
		background: rgba(15, 15, 17, .92);
		backdrop-filter: blur(28px);
		-webkit-backdrop-filter: blur(28px);
		box-shadow: 0 18px 48px rgba(0, 0, 0, .42);
		color: var(--fw-paper);
		animation: fss-widget-in .28s cubic-bezier(.2, .8, .2, 1);
	}

	.fss-widget-panel[hidden] {
		display: none;
	}

	@keyframes fss-widget-in {
		from {
			opacity: 0;
			transform: translateY(10px);
		}

		to {
			opacity: 1;
			transform: translateY(0);
		}
	}

	@media (prefers-reduced-motion: reduce) {
		.fss-widget-panel {
			animation: none;
		}
	}

	.fss-widget-head {
		align-items: flex-start;
		justify-content: space-between;
		gap: 18px;
		display: flex;
	}

	.fss-widget-eyebrow {
		color: var(--fw-muted);
		letter-spacing: .12em;
		text-transform: uppercase;
		font-size: 10px;
		font-weight: 500;
		line-height: 1.5;
		display: block;
	}

	.fss-widget-title {
		margin: 10px 0 0;
		letter-spacing: -.03em;
		font-size: 22px;
		font-weight: 500;
		line-height: 1.15;
	}

	.fss-widget-close {
		flex-shrink: 0;
		place-items: center;
		width: 32px;
		height: 32px;
		padding: 0;
		border: 1px solid var(--fw-line);
		border-radius: 50%;
		background: transparent;
		color: var(--fw-muted);
		cursor: pointer;
		display: grid;
		transition: background-color .2s, color .2s, border-color .2s;
	}

	.fss-widget-close:hover {
		background: rgba(255, 255, 255, .08);
		border-color: rgba(255, 255, 255, .34);
		color: var(--fw-paper);
	}

	.fss-widget-close:focus-visible {
		outline: 2px solid var(--fw-accent);
		outline-offset: 3px;
	}

	/* ── Поля ───────────────────────────────────────────────────── */
	.fss-widget-form {
		margin-top: 22px;
		display: grid;
		gap: 14px;
	}

	.fss-widget-field label {
		margin-bottom: 8px;
		color: var(--fw-muted);
		letter-spacing: .12em;
		text-transform: uppercase;
		font-size: 10px;
		font-weight: 500;
		display: block;
	}

	.fss-widget-field input {
		color-scheme: dark;
		color: var(--fw-paper);
		outline: none;
		background: rgba(255, 255, 255, .03);
		border: 1px solid var(--fw-line);
		border-radius: var(--fw-radius-control);
		width: 100%;
		min-height: 46px;
		padding: 12px 14px;
		font-size: 13px;
		transition: border-color .2s, background-color .2s;
	}

	.fss-widget-field input::placeholder {
		color: rgba(240, 239, 237, .38);
	}

	.fss-widget-field input:hover {
		border-color: rgba(255, 255, 255, .3);
	}

	.fss-widget-field input:focus {
		outline: none;
		border-color: var(--fw-accent);
		background: rgba(255, 255, 255, .05);
	}

	.fss-widget-field input[aria-invalid=true] {
		border-color: #e08a8a;
	}

	/* ── Кнопка отправки ────────────────────────────────────────── */
	.fss-widget-submit {
		align-items: center;
		justify-content: space-between;
		gap: 24px;
		width: 100%;
		min-height: 50px;
		padding: 14px 18px;
		border: 1px solid transparent;
		border-radius: var(--fw-radius-control);
		background: var(--fw-paper);
		color: #1b1c1d;
		font-size: 13px;
		font-weight: 500;
		line-height: 1.2;
		cursor: pointer;
		display: flex;
		transition: background-color .3s, color .3s;
	}

	.fss-widget-submit:hover {
		background: #fff;
	}

	.fss-widget-submit:focus-visible {
		outline: 2px solid var(--fw-accent);
		outline-offset: 3px;
	}

	.fss-widget-submit svg {
		flex-shrink: 0;
		transition: transform .4s cubic-bezier(.2, .8, .2, 1);
	}

	.fss-widget-submit:hover svg {
		transform: translate(3px, -3px);
	}

	.fss-widget-note {
		color: rgba(240, 239, 237, .45);
		font-size: 10px;
		line-height: 1.5;
		text-align: center;
	}

	.fss-widget-note a {
		color: var(--fw-accent);
		text-decoration: underline;
		text-underline-offset: 3px;
		white-space: nowrap;
	}

	/* ── Экран «Заявка принята» ──────────────────────────────────── */
	.fss-widget-success {
		margin-top: 22px;
		display: grid;
		justify-items: start;
		gap: 0;
	}

	.fss-widget-success[hidden] {
		display: none;
	}

	.fss-float-widget.is-success .fss-widget-head-text {
		display: none;
	}

	.fss-float-widget.is-success .fss-widget-head {
		justify-content: flex-end;
	}

	.fss-widget-check {
		place-items: center;
		width: 46px;
		height: 46px;
		margin-bottom: 18px;
		border-radius: var(--fw-radius-control);
		background: var(--fw-accent);
		color: var(--fw-ink);
		display: grid;
	}

	.fss-widget-success-title {
		margin: 12px 0 0;
		letter-spacing: -.03em;
		font-size: 20px;
		font-weight: 500;
		line-height: 1.2;
	}

	.fss-widget-success-text {
		margin: 12px 0 20px;
		color: var(--fw-muted);
		font-size: 12px;
		line-height: 1.55;
	}

	/* ── Телефон ─────────────────────────────────────────────────── */
	.fss-widget-foot {
		margin-top: 22px;
		padding-top: 18px;
		border-top: 1px solid rgba(255, 255, 255, .12);
		align-items: center;
		justify-content: space-between;
		gap: 16px;
		display: flex;
	}

	.fss-widget-phone {
		align-items: center;
		gap: 10px;
		border-bottom: 1px solid rgba(240, 239, 237, .4);
		padding-bottom: 4px;
		color: var(--fw-paper);
		font-size: 13px;
		font-weight: 500;
		text-decoration: none;
		display: inline-flex;
		transition: border-color .25s, color .25s;
	}

	.fss-widget-phone svg {
		transition: transform .4s cubic-bezier(.2, .8, .2, 1);
	}

	.fss-widget-phone:hover {
		border-color: var(--fw-accent);
		color: var(--fw-accent);
	}

	.fss-widget-phone:hover svg {
		transform: translate(3px, -3px);
	}

	.fss-widget-phone:focus-visible {
		outline: 2px solid var(--fw-accent);
		outline-offset: 3px;
	}

	/* ── Адаптив ─────────────────────────────────────────────────── */
	@media (max-width: 480px) {
		.fss-float-widget {
			right: 16px;
			bottom: 16px;
		}

		.fss-widget-panel {
			position: fixed;
			right: 16px;
			bottom: 88px;
			left: 16px;
			width: auto;
		}
	}

	/* Скрыть виджет на главной в мобильной версии */
	@media (max-width: 900px) {
		.fss-float-widget--home {
			display: none !important;
		}
	}
</style>

<script>
(function () {
	'use strict';

	var root = document.querySelector('[data-fss-widget]');
	if (!root) return;

	var panel = document.getElementById('forma_wrapper');
	var toggle = document.getElementById('forma_toggle');
	var closeBtn = document.getElementById('forma_close');
	var body = root.querySelector('[data-widget-body]');
	var success = root.querySelector('[data-widget-success]');
	var reset = root.querySelector('[data-widget-reset]');

	if (!panel || !toggle) return;

	function isOpen() {
		return root.classList.contains('is-open');
	}

	function open(state) {
		root.classList.toggle('is-open', state);
		panel.hidden = !state;
		toggle.setAttribute('aria-expanded', state ? 'true' : 'false');
		toggle.setAttribute('aria-label', state ? 'Закрыть форму быстрого расчёта' : 'Открыть форму быстрого расчёта');

		if (state) {
			// фокус — на реальном поле ( honeypot пропускаем )
			var field = panel.querySelector('input[name="name"], input[name="phone"]');
			if (field) {
				setTimeout(function () { field.focus(); }, 60);
			}
		}
	}

	toggle.addEventListener('click', function () {
		open(!isOpen());
	});

	if (closeBtn) {
		closeBtn.addEventListener('click', function () {
			open(false);
			toggle.focus();
		});
	}

	document.addEventListener('keydown', function (e) {
		if (e.key === 'Escape' && isOpen()) {
			open(false);
			toggle.focus();
		}
	});

	document.addEventListener('click', function (e) {
		if (isOpen() && !root.contains(e.target)) {
			open(false);
		}
	});

	if (reset) {
		reset.addEventListener('click', function () {
			if (body) body.hidden = false;
			if (success) success.hidden = true;
			root.classList.remove('is-success');
			var field = panel.querySelector('input[name="name"], input[name="phone"]');
			if (field) field.focus();
		});
	}

	// Заявка отправлена — показываем экран «Заявка принята» прямо в панели.
	// Имя cookie: 'fss_form_<тип>_success', где тип нормализован в [a-z0-9_]
	// (bootstrap.php). Для мини-формы это 'fss_form_mini_forma_success'.
	var cookie = document.cookie.replace(/\s/g, '').split(';');
	var done = cookie.some(function (part) {
		return part.indexOf('fss_form_mini_forma_success=1') === 0
			|| part.indexOf('fss_form_mini-forma_success=1') === 0
			|| part.indexOf('fss_form_mini_success=1') === 0;
	});
	if (done) {
		if (body) body.hidden = true;
		if (success) success.hidden = false;
		root.classList.add('is-success');
		open(true);
	}
})();
</script>
