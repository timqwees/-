<div class="fixed bottom-6 right-6 z-50">
	<button id="forma_toggle" type="button"
		class="text-white w-[56px] h-[56px] rounded-[14px] shadow-[0_8px_24px_rgba(249,115,22,.40)] flex items-center justify-center transition-all duration-200 bg-gradient-to-br from-[#FB923C] via-[#EA580C] to-[#C2410C] hover:from-[#F97316] hover:to-[#A03A0E] hover:scale-105 hover:shadow-[0_12px_36px_rgba(249,115,22,.55)] active:scale-95 focus:outline-none focus:ring-2 focus:ring-[#F97316] focus:ring-offset-2 relative overflow-hidden">
		<i class="fa-solid fa-comment-dots text-xl relative z-10"></i>
		<span class="ripple-effect absolute inset-0 rounded-[14px] pointer-events-none"></span>
	</button>
	<!-- Forma Popup — modern -->
	<div id="forma_wrapper" class="hidden absolute bottom-20 right-0 w-[340px] max-w-[92vw] bg-white rounded-[24px] shadow-[0_24px_64px_rgba(15,23,42,.22)] border border-gray-100 p-5">
		<div class="flex justify-between items-start mb-3">
			<div>
				<div class="font-extrabold tracking-tight text-gray-900">Быстрый расчёт</div>
				<div class="text-xs text-gray-500">Отвечаем за 7 минут</div>
			</div>
			<button id="forma_close" type="button" class="w-8 h-8 grid place-items-center rounded-[12px] bg-gray-100 text-gray-500 hover:bg-gray-200">
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
					if (file_exists($alt)) { require_once $alt; echo \App\Mail\Antibot::generateFields(); }
				}
			?>
			<div>
				<label for="mini_name" class="sr-only">Ваше имя</label>
				<input id="mini_name" name="name" type="text" placeholder="Ваше имя *" required autocomplete="name" minlength="2" maxlength="100"
					class="w-full px-3.5 py-3 rounded-[12px] border border-gray-200 bg-gray-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#F97316] text-sm">
			</div>
			<div>
				<label for="mini_phone" class="sr-only">Телефон</label>
				<input id="mini_phone" name="phone" type="tel" maxlength="18" placeholder="+7 (___) ___-__-__" required autocomplete="tel" pattern="^\+?[0-9\s\-\(\)]{10,20}$"
					class="w-full px-3.5 py-3 rounded-[12px] border border-gray-200 bg-gray-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#F97316] text-sm">
			</div>
			<button type="submit" class="btn-prof w-full !py-3 text-sm">
				Отправить <i class="fa-solid fa-arrow-right text-xs"></i></button>
			<p class="text-[11px] leading-tight text-gray-500 text-center">Нажимая, вы соглашаетесь с политикой. Защита от спама.</p>
		</form>
		<div class="mt-4 pt-4 border-t border-gray-100 flex items-center justify-between">
			<div class="text-xs text-gray-500">Или позвоните:</div>
			<?php $fssPhone = preg_replace('/\D/', '', (string)seo_config('phone')); ?>
			<div class="fss-widget-phone">
				<a href="tel:+<?= $fssPhone ?>" title="Позвонить: <?= htmlspecialchars((string)seo_config('phone_display')) ?>" class="fss-widget-phone-link">
					<span class="fss-widget-phone-icon"><i class="fa-solid fa-phone"></i></span>
					<span class="fss-widget-phone-num"><?= htmlspecialchars((string)seo_config('phone_display')) ?></span>
				</a>
			</div>
		</div>
	</div>
</div>

<script>
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

	// Ripple wave effect on widget toggle button (PrimeReact/Pulsar style)
	if (forma_toggle) {
		forma_toggle.addEventListener('click', (e) => {
			const btn = e.currentTarget;
			const ripple = document.createElement('span');
			const rect = btn.getBoundingClientRect();
			const size = Math.max(rect.width, rect.height) * 2;
			const x = e.clientX - rect.left - size / 2;
			const y = e.clientY - rect.top - size / 2;
			ripple.style.cssText = 'position:absolute;width:' + size + 'px;height:' + size + 'px;left:' + x + 'px;top:' + y + 'px;border-radius:50%;background:rgba(255,255,255,0.45);pointer-events:none;animation:widgetRipple 0.65s ease-out';
			btn.appendChild(ripple);
			setTimeout(() => ripple.remove(), 650);
		});
	}
});
</script>

<style>
@keyframes widgetRipple { 0% { transform: scale(0.3); opacity: 0.6; } 100% { transform: scale(1.1); opacity: 0; } }
/* Фон виджета быстрого расчёта: тёплый фирменный градиент + точечный паттерн */
#forma_wrapper { overflow: hidden; background:
	radial-gradient(120% 90% at 100% 0%, rgba(249,115,22,.16), transparent 60%),
	radial-gradient(90% 70% at 0% 100%, rgba(234,88,12,.12), transparent 55%),
	linear-gradient(180deg, #FFFFFF 0%, #FFF7ED 100%);
}
#forma_wrapper::before { content: ""; position: absolute; inset: 0; pointer-events: none; z-index: 0;
	background-image: radial-gradient(rgba(23,19,16,.05) 1px, transparent 1px);
	background-size: 14px 14px; opacity: .55;
}
#forma_wrapper > * { position: relative; z-index: 1; }
/* Фон кнопки-лаунчера виджета: лёгкий блик + точечный паттерн поверх градиента */
#forma_toggle { border-radius: 14px; background: linear-gradient(135deg, #F97316 0%, #ff8038 55%, #A03A0E 100%); }
#forma_toggle:hover { background: linear-gradient(135deg, #EA580C 0%, #F97316 55%, #8A2E0A 100%); }
#forma_toggle::before { content: ""; position: absolute; inset: 0; pointer-events: none; z-index: 0; border-radius: inherit;
	background:
		radial-gradient(circle at 30% 22%, rgba(255,255,255,.45), transparent 55%),
		radial-gradient(rgba(255,255,255,.28) 1px, transparent 1.4px);
	background-size: 100% 100%, 9px 9px;
}
#forma_toggle i { z-index: 2; }
.fss-widget-phone { background: #F5F5F4; border: 1px solid #E7E5E4; border-radius: 12px; padding: 10px 14px; }
.fss-widget-phone-link { display: inline-flex; align-items: center; gap: 8px; color: #171310; text-decoration: none; font-weight: 600; font-size: 13px; transition: color .2s; }
.fss-widget-phone-link:hover { color: #C2410C; }
.fss-widget-phone-icon { display: inline-flex; align-items: center; justify-content: center; width: 32px; height: 32px; background: #EA580C; color: #fff; border-radius: 10px; font-size: 13px; }
.fss-widget-phone-num { font-weight: 700; }
.btn-prof { display: inline-flex; align-items: center; justify-content: center; gap: 8px; background: #EA580C; color: #fff; border-radius: 12px; padding: 12px 22px; font-weight: 600; font-size: 15px; box-shadow: 0 8px 20px rgba(234,88,12,.30); border: 0; transition: background .2s, transform .15s, box-shadow .2s; cursor: pointer; }
.btn-prof:hover { background: #C2410C; box-shadow: 0 12px 28px rgba(234,88,12,.45); transform: translateY(-2px); }
.btn-prof:active { transform: translateY(0); }
</style>