<?php
/**
 * Общая форма заявки — структура ProMaintenance (enquiry-panel → enquiry-experience → form).
 * Используется на главной и на странице /contacts.
 * Поля: 01 — проект (услуга, локация, описание), 02 — контакты (имя, компания, email, телефон).
 *
 * После успешной отправки сервер кладёт данные заявки в сессию (fss_enquiry_result) —
 * вместо формы показывается экран «Заявка принята» (enquiry-success), как в оригинале.
 */
$__phone = htmlspecialchars(preg_replace('/\D/', '', (string)seo_config('phone')));
$__phoneDisplay = htmlspecialchars((string)seo_config('phone_display'));
$__email = htmlspecialchars((string)seo_config('email'));

// Экран успеха: данные заявки из сессии (заполняется обработчиком формы)
$__result = null;
if (session_status() === PHP_SESSION_NONE && !empty($_COOKIE[session_name()])) {
	@session_start();
}
if (!empty($_SESSION['fss_enquiry_result']) && ($_SESSION['fss_enquiry_result']['type'] ?? '') === 'modal') {
	$__result = $_SESSION['fss_enquiry_result'];
	unset($_SESSION['fss_enquiry_result']);
}
?>
<style>
	/* Небольшой отступ между подписью поля и самим полем (select/input/textarea) */
	.form-field label {
		margin-bottom: 8px;
	}
</style>
<div class="enquiry-panel">
	<div class="enquiry-experience">
		<?php if (is_array($__result)): ?>
			<!-- ЭКРАН УСПЕХА (ProMaintenance enquiry-success) -->
			<div id="enquiry-success" tabindex="-1" role="status" aria-live="polite">
				<span class="success-icon">
					<svg width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
						<path d="M20 6 9 17l-5-5"></path>
					</svg>
				</span>
				<p class="eyebrow">Заявка принята</p>
				<h3>Всё готово,<br><span data-success-name><?= htmlspecialchars((string)($__result['name'] ?? 'мы')) ?></span>.</h3>
				<p>Мы получили заявку и перезвоним в течение 15 минут в рабочее время. Дубли отправили на вашу почту.</p>
				<dl>
					<div>
						<dt>Номер заявки</dt>
						<dd data-success-reference><?= htmlspecialchars((string)($__result['reference'] ?? '')) ?></dd>
					</div>
					<div>
						<dt>Услуга</dt>
						<dd data-success-service><?= htmlspecialchars((string)($__result['service'] ?? '')) ?></dd>
					</div>
					<div>
						<dt>Контакты</dt>
						<dd data-success-email><?= htmlspecialchars((string)($__result['email'] ?? '')) ?></dd>
					</div>
				</dl>
				<button class="button" type="button" data-enquiry-reset>Отправить ещё одну заявку
					<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
						<path d="M5 12h14"></path>
						<path d="m12 5 7 7-7 7"></path>
					</svg>
				</button>
			</div>
		<?php else: ?>
			<div class="form-heading">
				<span class="eyebrow">Заявка на проект</span>
				<span class="form-step-count" data-form-counter>01 / 02</span>
			</div>

			<form id="enquiry-form" method="POST" action="/send/email" novalidate data-multistep>
				<input type="hidden" name="mailer">
				<?php include __DIR__ . '/antibot_fields.php'; ?>

				<div class="form-progress" data-form-progress>
					<span class="is-current" data-step-indicator="0">01 <span>Ваш проект</span></span>
					<i></i>
					<span data-step-indicator="1">02 <span>Ваши контакты</span></span>
				</div>

				<div data-error-summary hidden role="alert" class="form-errors">
					<p>Проверьте, пожалуйста:</p>
					<ul data-error-summary-list></ul>
				</div>

				<!-- ЭТАП 1: проект -->
				<div class="form-stage" data-form-stage="0">
					<h3>Чем мы можем помочь?</h3>
					<div class="form-fields">
						<div class="form-field">
							<label for="field-service">Какая услуга нужна <span>*</span></label>
							<select id="field-service" name="product" required aria-describedby="error-service">
								<option value="">Выберите услугу</option>
								<option value="Быстровозводимые здания">Быстровозводимые здания</option>
								<option value="Капитальный ремонт">Капитальный ремонт</option>
								<option value="Модульные здания">Модульные здания</option>
								<option value="Общежития">Общежития</option>
								<option value="Спецтехника">Спецтехника</option>
								<option value="Грузоперевозки">Грузоперевозки</option>
								<option value="Другое">Другое</option>
							</select>
							<p id="error-service" data-error-for="product" hidden></p>
						</div>
						<div class="form-field form-field--address form-field--location" data-location-suggest>
							<label for="field-location">Где объект</label>
							<input type="text" id="field-location" name="location" placeholder="Город, район или адрес" maxlength="120" autocomplete="off" role="combobox" aria-expanded="false" aria-autocomplete="list" aria-controls="location-suggestions" aria-describedby="error-location">
							<div class="location-suggestions" id="location-suggestions" data-location-list role="listbox" aria-label="Подсказки по адресу" hidden></div>
							<p id="error-location" data-error-for="location" hidden></p>
						</div>
						<div class="form-field form-field--full">
							<label for="field-message">Немного о вашем проекте <span>*</span></label>
							<textarea id="field-message" name="message" rows="3" placeholder="Что за объект, какие задачи и что хотите получить." required minlength="10" maxlength="2000" aria-describedby="error-message"></textarea>
							<p id="error-message" data-error-for="message" hidden></p>
						</div>
					</div>
					<button type="button" class="button form-next" data-form-next>Ваши контакты
						<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
							<path d="M5 12h14"></path>
							<path d="m12 5 7 7-7 7"></path>
						</svg>
					</button>
				</div>

				<!-- ЭТАП 2: контакты -->
				<div class="form-stage" data-form-stage="1" hidden>
					<h3>Давайте познакомимся.</h3>
					<div class="form-fields">
						<div class="form-field">
							<label for="field-name">Ваше имя <span>*</span></label>
							<input id="field-name" name="name" placeholder="Полное имя" required autocomplete="name" maxlength="100" aria-describedby="error-name">
							<p id="error-name" data-error-for="name" hidden></p>
						</div>
						<div class="form-field">
							<label for="field-company">Компания <span>*</span></label>
							<input id="field-company" name="company" placeholder="Название компании" required autocomplete="organization" maxlength="120" aria-describedby="error-company">
							<p id="error-company" data-error-for="company" hidden></p>
						</div>
						<div class="form-field">
							<label for="field-email">Email <span>*</span></label>
							<input id="field-email" name="email" placeholder="you@company.ru" required type="email" autocomplete="email" maxlength="254" aria-describedby="error-email">
							<p id="error-email" data-error-for="email" hidden></p>
						</div>
						<div class="form-field">
							<label for="field-phone">Телефон <span>*</span></label>
							<input id="field-phone" name="phone" type="tel" autocomplete="tel" required maxlength="18" aria-describedby="error-phone">
							<p id="error-phone" data-error-for="phone" hidden></p>
						</div>
					</div>

					<div class="form-consent">
						<label class="consent-item">
							<input type="checkbox" name="checkbox" value="on" required>
							<span>Я согласен <a href="/politika" title="Политика конфиденциальности" class="underline underline-offset-4">с условиями политики конфиденциальности</a></span>
						</label>
						<label class="consent-item">
							<input type="checkbox" name="personal_data" value="on" required>
							<span>Я согласен <a href="/soglasie" title="Согласие на обработку данных" class="underline underline-offset-4">на обработку моих персональных данных</a></span>
						</label>
						<label class="consent-item">
							<input type="checkbox" name="marketing" value="on">
							<span>Я согласен на получение информационных и рекламных сообщений</span>
						</label>
					</div>

					<div class="form-actions">
						<button type="button" class="text-link" data-form-back>&#8592; Назад</button>
						<button type="submit" class="button">Отправить заявку
							<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
								<path d="M7 7h10v10"></path>
								<path d="M7 17 17 7"></path>
							</svg>
						</button>
					</div>
				</div>
			</form>
		<?php endif; ?>
	</div>
</div>
