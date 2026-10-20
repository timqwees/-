<?php
/**
 * Общая форма заявки — структура ProMaintenance (enquiry-panel → enquiry-experience → form).
 * Используется на главной и на странице /contact.
 * Поля: 01 — проект (услуга, локация, описание), 02 — контакты (имя, компания, email, телефон).
 */
$__phone = htmlspecialchars(preg_replace('/\D/', '', (string)seo_config('phone')));
$__phoneDisplay = htmlspecialchars((string)seo_config('phone_display'));
$__email = htmlspecialchars((string)seo_config('email'));
?>
<div class="enquiry-panel">
	<div class="enquiry-experience">
		<div class="form-heading">
			<span class="eyebrow" style="color: #b1aaa4; font-size: 10px;">Заявка на проект</span>
			<span class="form-step-count" data-form-counter style="color: #b1aaa4; font-size: 10px;">01 / 02</span>
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
					<div class="form-field">
						<label for="field-location">Где объект</label>
						<input id="field-location" name="location" placeholder="Город или район" maxlength="120" aria-describedby="error-location">
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

				<div class="space-y-3 pt-2 text-sm leading-relaxed">
					<label class="flex cursor-pointer items-start gap-3">
						<input type="checkbox" name="checkbox" value="on" required class="mt-0.5 h-5 w-5 shrink-0">
						<span>Я согласен <a href="/politika" title="Политика конфиденциальности" class="underline underline-offset-4">с условиями политики конфиденциальности</a></span>
					</label>
					<label class="flex cursor-pointer items-start gap-3">
						<input type="checkbox" name="personal_data" value="on" required class="mt-0.5 h-5 w-5 shrink-0">
						<span>Я согласен <a href="/soglasie" title="Согласие на обработку данных" class="underline underline-offset-4">на обработку моих персональных данных</a></span>
					</label>
					<label class="flex cursor-pointer items-start gap-3">
						<input type="checkbox" name="marketing" value="on" class="mt-0.5 h-5 w-5 shrink-0">
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
	</div>
</div>
