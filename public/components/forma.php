<div class="fixed bottom-6 right-6 z-50">
	<button id="forma_toggle" type="button"
		class="text-white w-[56px] h-[56px] rounded-full shadow-lg flex items-center justify-center transition bg-emerald-600 hover:bg-emerald-700 hover:scale-105 active:scale-95">
		<i class="fa-solid fa-comment-dots text-xl"></i>
	</button>
	<!-- Forma Popup — modern -->
	<div id="forma_wrapper" class="hidden absolute bottom-20 right-0 w-[340px] max-w-[92vw] bg-white rounded-[20px] shadow-[0_16px_48px_rgba(15,23,42,.18)] border border-gray-100 p-5">
		<div class="flex justify-between items-start mb-3">
			<div>
				<div class="font-extrabold tracking-tight text-gray-900">Быстрый расчет</div>
				<div class="text-xs text-gray-500">Отвечаем за 7 минут</div>
			</div>
			<button id="forma_close" type="button" class="w-8 h-8 grid place-items-center rounded-full bg-gray-100 text-gray-500 hover:bg-gray-200">
				<i class="fa-solid fa-xmark text-sm"></i>
			</button>
		</div>
		<form class="space-y-3" method="post" data-form="mini" novalidate>
			<input type="hidden" name="mini-forma">
			<?php
				$antibotPath = __DIR__ . '/antibot_fields.php';
				if (file_exists($antibotPath)) include_once $antibotPath;
				else {
					$alt = dirname(__DIR__, 2) . '/app/Mail/Antibot.php';
					if (file_exists($alt)) { require_once $alt; echo Antibot::generateFields(); }
				}
			?>
			<div>
				<label for="mini_name" class="sr-only">Ваше имя</label>
				<input id="mini_name" name="name" type="text" placeholder="Ваше имя *" required autocomplete="name" minlength="2" maxlength="100"
					class="w-full px-3.5 py-3 rounded-2xl border border-gray-200 bg-gray-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500 text-sm">
			</div>
			<div>
				<label for="mini_phone" class="sr-only">Телефон</label>
				<input id="mini_phone" name="phone" type="tel" maxlength="18" placeholder="+7 (___) ___-__-__" required autocomplete="tel" pattern="^\+?[0-9\s\-\(\)]{10,20}$"
					class="w-full px-3.5 py-3 rounded-2xl border border-gray-200 bg-gray-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500 text-sm">
			</div>
			<button type="submit" class="btn-sun w-full !py-3 text-sm">
				Получить расчет <i class="fa-solid fa-arrow-right text-xs"></i></button>
			<p class="text-[11px] leading-tight text-gray-500 text-center">Нажимая, вы соглашаетесь с политикой. Защита от спама.</p>
		</form>
		<div class="mt-4 pt-4 border-t border-gray-100 flex items-center justify-between">
			<div class="text-xs text-gray-500">Или позвоните:</div>
			<?php $fssPhone = preg_replace('/\D/', '', (string)seo_config('phone')); ?>
			<a href="tel:+<?= $fssPhone ?>" class="inline-flex items-center gap-2 bg-emerald-50 text-emerald-700 border border-emerald-200 rounded-full px-3 py-1.5 text-xs font-semibold"> <i class="fa-solid fa-phone"></i> <?= htmlspecialchars((string)seo_config('phone_display')) ?></a>
		</div>
	</div>
</div>

<script defer="false">
	document.addEventListener('DOMContentLoaded', () => {
		const forma_wrapper = document.getElementById('forma_wrapper');
		const forma_close = document.getElementById('forma_close');
		const forma_toggle = document.getElementById('forma_toggle');
		if (forma_close && forma_wrapper) {
			forma_close.addEventListener('click', () => {
				forma_wrapper.classList.toggle('hidden');
			});
		}
		if (forma_toggle && forma_wrapper) {
			forma_toggle.addEventListener('click', () => {
				forma_wrapper.classList.toggle('hidden');
			});
		}
	});
</script>