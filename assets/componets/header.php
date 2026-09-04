<style>
	.visible-1100 { display: none; }
	@media screen and (max-width:1286px) { .hidden-1286 { display: none; } }
	@media screen and (max-width:1100px) {
		.hidden-1100 { display: none; }
		.visible-1100 { display: flex; }
		.underblock_line { font-size: 18px; }
	}
	@media screen and (max-width:490px) { .hidden-490 { display: none; } }
	/* modern header — зелёная гамма как было, но чище */
	.header-glass { background: rgba(255,255,255,.96); backdrop-filter: blur(16px) saturate(1.15); -webkit-backdrop-filter: blur(16px) saturate(1.15); border-bottom: 1px solid #bbf7d0; box-shadow: 0 4px 24px rgba(16,185,129,.07); }
	#content_menu { transition: transform .35s cubic-bezier(.2,.8,.2,1), opacity .25s; transform: translateX(100%); opacity: 0; pointer-events: none; }
	#content_menu.active { transform: translateX(0); opacity: 1; pointer-events: auto; }
	#content_menu_backdrop { opacity: 0; pointer-events: none; transition: opacity .25s; }
	#content_menu.active + #content_menu_backdrop, #content_menu_backdrop.active { opacity: 1; pointer-events: auto; }
	.hanburger_line { background: white !important; }
	[data-theme="dark"] .header-glass { background: rgba(17,24,39,.88); border-bottom-color: rgba(16,185,129,.18); }
	.cta-primary { background: linear-gradient(135deg, #16a34a 0%, #15803d 100%); color: white; border-radius: 9999px; padding: 11px 22px; font-weight: 700; font-size: 14px; letter-spacing: .01em; box-shadow: 0 8px 20px rgba(22,163,74,.28); transition: all .2s; border: 1px solid rgba(255,255,255,.2); }
	.cta-primary:hover { background: linear-gradient(135deg, #15803d 0%, #166534 100%); transform: translateY(-1px); box-shadow: 0 12px 28px rgba(22,163,74,.34); }
	.cta-secondary { border: 1px solid #bbf7d0; border-radius: 9999px; padding: 10px 18px; font-weight: 600; font-size: 14px; background: #f0fdf4; color: #14532d; }
	.cta-secondary:hover { background: #dcfce7; border-color: #86efac; }
	.phone-pill { display:flex; align-items:center; gap:10px; font-weight:800; font-size:14px; color:#14532d; background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 9999px; padding: 6px 12px 6px 6px; }
	.phone-pill i { width:30px; height:30px; display:grid; place-items:center; background:#16a34a; color:white; border-radius:9999px; font-size:12px; box-shadow: 0 2px 8px rgba(22,163,74,.25); }
	.nav-link { font-size:14.5px; font-weight:600; letter-spacing:.01em; color:#334155; position:relative; padding:8px 4px; }
	.nav-link:hover { color:#15803d; }
	.nav-link::after { content:''; position:absolute; left:4px; right:4px; bottom:0; height:2.5px; background:#22c55e; border-radius:9999px; transform: scaleX(0); transition: transform .25s cubic-bezier(.4,0,.2,1); transform-origin: center; }
	.nav-link:hover::after { transform: scaleX(1); }
</style>
<!-- top bar — full width, ultra minimal, зелёный -->
<div class="hidden md:flex w-full bg-[#deffbc] text-[#14532d] text-[12px] leading-none border-b border-[#bbf7d0]">
	<div class="w-full px-6 md:px-10 py-2 flex items-center justify-between">
		<div class="flex items-center gap-6 font-medium tracking-wide">
			<span class="flex items-center gap-1.5"><i class="fa-solid fa-location-dot text-emerald-700 text-[11px]"></i> Москва и МО • По всей РФ</span>
			<span class="hidden lg:flex items-center gap-1.5"><i class="fa-regular fa-clock text-emerald-700 text-[11px]"></i> Пн–Пт 09:00–18:00</span>
			<span class="hidden xl:flex items-center gap-1.5"><i class="fa-solid fa-shield-halved text-emerald-700 text-[11px]"></i> Гарантия 50+ лет</span>
		</div>
		<div class="flex items-center gap-3 font-medium">
			<a href="mailto:fss@фсс.рус" class="hover:text-emerald-800 transition">fss@фсс.рус</a>
			<span class="opacity-20">|</span>
			<span class="hidden sm:inline-flex items-center gap-1.5"><span class="w-1.5 h-1.5 bg-emerald-500 rounded-full animate-pulse"></span> Отвечаем за 7 минут</span>
		</div>
	</div>
</div>
<header class="header-glass sticky top-0 z-50 w-full">
	<div class="w-full px-6 md:px-10 py-4 flex justify-between items-center gap-8">
		<a class="flex items-center gap-3.5 shrink-0 group" href="/index.php">
			<div id="themeLogo" class="shrink-0 transition-transform group-hover:scale-[1.02]"></div>
			<div class="hidden-490 leading-tight">
				<div class="text-[17px] font-extrabold tracking-tight text-gray-900 dark:text-white leading-none">ФАВОРИТ-СТРОЙ-СНАБ</div>
				<div class="text-[11px] font-semibold tracking-[0.14em] uppercase text-emerald-700 -mt-0.5">Быстровозводимые здания • с 2011</div>
			</div>
		</a>
		<nav class="hidden lg:flex items-center gap-8">
			<a href="/pages/uslugi/uslugi.php" class="nav-link">Услуги</a>
			<a href="/index.php#process" class="nav-link">Этапы</a>
			<a href="/pages/projects.php" class="nav-link">Каталог</a>
			<a href="/index.php#advantages" class="nav-link">Направления</a>
			<a href="/pages/portfolio.php" class="nav-link">Портфолио</a>
		</nav>
		<div class="flex items-center gap-2 sm:gap-2.5 shrink-0">
			<a href="tel:+79380180006" class="hidden xl:flex phone-pill">
				<i class="fa-solid fa-phone"></i>
				<span>+7 938 018-00-06</span>
			</a>
			<a href="tel:+79380180006" class="xl:hidden hidden md:grid w-9 h-9 place-items-center rounded-full bg-emerald-600 text-white shadow-sm" aria-label="Позвонить"><i class="fa-solid fa-phone text-xs"></i></a>
			<a href="/#contact" class="cta-primary hidden sm:inline-flex items-center gap-2">
				<span>Рассчитать стоимость</span>
				<i class="fa-solid fa-arrow-right text-xs opacity-90"></i>
			</a>
			<a href="/pages/portfolio.php" class="cta-secondary hidden-1286">Примеры</a>
			<button class="relative cursor-pointer hidden lg:grid place-items-center" id="themeElement" aria-label="Переключить тему" style="width:36px;height:36px;display:grid;place-items:center;border:1px solid #dcfce7;border-radius:9999px;background:#f0fdf4;color:#15803d;"></button>
			<button class="relative cursor-pointer lg:hidden flex flex-col justify-center items-center w-10 h-10 rounded-full bg-emerald-600 text-white shadow-md" id="toggleMenu" aria-label="Открыть меню" aria-expanded="false" aria-controls="content_menu" style="gap:4px;">
				<span class="hanburger_line bg-white"></span>
				<span class="hanburger_line bg-white"></span>
				<span class="hanburger_line bg-white"></span>
			</button>
		</div>
	</div>


	<!-- mobile menu — зелёная гамма, красиво, с оверлеем -->
	<div id="content_menu_backdrop" class="fixed inset-0 bg-black/30 backdrop-blur-sm z-40 hidden lg:hidden"></div>
	<div class="fixed top-0 right-0 w-[88%] max-w-[380px] h-[100dvh] bg-white flex flex-col visible-1100 z-50 overflow-y-auto shadow-[0_20px_60px_rgba(16,185,129,.18)]"
		id="content_menu" aria-hidden="true" style="border-left:3px solid #22c55e; border-top:none;">
		<button class="z-20 cursor-pointer flex items-center justify-center absolute w-10 h-10 rounded-full bg-emerald-600 text-white shadow-md" style="top:18px; right:18px;" id="btn_close_menu" aria-label="Закрыть меню">
			<i class="fa-solid fa-xmark"></i>
		</button>
		<div class="mt-2 flex items-center gap-3">
			<img src="/assets/image/logo/logo.svg" alt="ФАВОРИТ-СТРОЙ-СНАБ" class="h-9 w-9 object-contain" onerror="this.style.display='none'">
			<div class="leading-tight">
				<div class="font-extrabold tracking-tight text-gray-900 leading-none">ФАВОРИТ-СТРОЙ-СНАБ</div>
				<div class="text-[10px] tracking-widest uppercase text-emerald-700 font-semibold">Быстровозводимые здания • с 2011</div>
			</div>
		</div>
		<div class="mt-6 mb-6">
			<div class="inline-flex items-center gap-2 text-[11px] tracking-[0.18em] uppercase font-bold text-emerald-700 bg-emerald-50 border border-emerald-200 rounded-full px-3 py-1">Навигация</div>
			<div class="h-px bg-emerald-100 mt-4"></div>
		</div>
		<nav class="flex flex-col gap-1.5">
			<a href="/pages/uslugi/uslugi.php" class="flex items-center justify-between py-3.5 px-3.5 rounded-2xl bg-emerald-600 text-white shadow-sm transition group">
				<span class="font-semibold">Услуги</span><span class="w-8 h-8 grid place-items-center rounded-full bg-white text-emerald-600 text-xs"><i class="fa-solid fa-arrow-right"></i></span>
			</a>
			<a href="/index.php#process" class="flex items-center justify-between py-3.5 px-3.5 rounded-2xl hover:bg-emerald-50 border border-transparent hover:border-emerald-100 transition group">
				<span class="font-medium text-gray-900">Этапы строительства</span><span class="w-8 h-8 grid place-items-center rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200 group-hover:bg-emerald-600 group-hover:text-white transition"><i class="fa-solid fa-arrow-right"></i></span>
			</a>
			<a href="/pages/projects.php" class="flex items-center justify-between py-3.5 px-3.5 rounded-2xl hover:bg-emerald-50 border border-transparent hover:border-emerald-100 transition group">
				<span class="font-medium text-gray-900">Каталог объектов</span><span class="w-8 h-8 grid place-items-center rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200 group-hover:bg-emerald-600 group-hover:text-white transition"><i class="fa-solid fa-arrow-right"></i></span>
			</a>
			<a href="/index.php#advantages" class="flex items-center justify-between py-3.5 px-3.5 rounded-2xl hover:bg-emerald-50 border border-transparent hover:border-emerald-100 transition group">
				<span class="font-medium text-gray-900">Направления</span><span class="w-8 h-8 grid place-items-center rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200 group-hover:bg-emerald-600 group-hover:text-white transition"><i class="fa-solid fa-arrow-right"></i></span>
			</a>
			<a href="/pages/portfolio.php" class="flex items-center justify-between py-3.5 px-3.5 rounded-2xl hover:bg-emerald-50 border border-transparent hover:border-emerald-100 transition group">
				<span class="font-medium text-gray-900">Портфолио</span><span class="w-8 h-8 grid place-items-center rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200 group-hover:bg-emerald-600 group-hover:text-white transition"><i class="fa-solid fa-arrow-right"></i></span>
			</a>
		</nav>
		<div class="mt-auto pt-6 space-y-3">
			<a href="tel:+79380180006" class="flex items-center justify-center gap-2 w-full bg-emerald-600 text-white rounded-full py-3.5 font-bold shadow-md"><i class="fa-solid fa-phone"></i> +7 938 018-00-06</a>
			<a href="/#contact" class="flex items-center justify-center gap-2 w-full bg-gray-900 text-white rounded-full py-3.5 font-semibold hover:bg-black transition">Рассчитать стоимость <i class="fa-solid fa-arrow-right text-xs"></i></a>
			<div class="text-center text-xs text-emerald-700 bg-emerald-50 border border-emerald-100 rounded-full py-1.5">Отвечаем за 7 минут • Выезд бесплатно</div>
		</div>
	</div>

	<script>
		document.addEventListener('DOMContentLoaded', () => {
			const togglemenu = document.getElementById('toggleMenu');
			const content_menu = document.getElementById('content_menu');
			const btn_close = document.getElementById('btn_close_menu');
			const backdrop = document.getElementById('content_menu_backdrop');

			function setMenuExpanded(expanded) {
				togglemenu.setAttribute('aria-expanded', expanded ? 'true' : 'false');
				content_menu.setAttribute('aria-hidden', expanded ? 'false' : 'true');
				if (expanded) {
					content_menu.classList.add('active');
					if (backdrop) backdrop.classList.add('active');
					document.body.style.overflow = 'hidden';
				} else {
					content_menu.classList.remove('active');
					if (backdrop) backdrop.classList.remove('active');
					document.body.style.overflow = '';
				}
			}
			togglemenu.addEventListener('click', () => {
				const isActive = content_menu.classList.contains('active');
				setMenuExpanded(!isActive);
			});
			if (backdrop) backdrop.addEventListener('click', () => setMenuExpanded(false));

			document.addEventListener('keydown', (e) => {
				if (e.key === 'Escape') {
					if (content_menu.classList.contains('active')) {
						setMenuExpanded(false);
					}
				}
			});

			document.addEventListener('cancel', () => {
				if (content_menu.classList.contains('active')) {
					setMenuExpanded(false);
				}
			});

			btn_close.addEventListener('click', () => {
				setMenuExpanded(false);
			});

			const themeToggle = document.getElementById('themeElement');
			const themeLogo = document.getElementById('themeLogo');
			const html = document.documentElement;
			let theme = localStorage.getItem('theme') || 'light';
			html.setAttribute('data-theme', theme);
			themeToggle.innerHTML = theme === 'light'
				? '<i class=" fas fa-sun text-yellow-500"></i>'
				: '<i class="fas fa-moon text-blue-500"></i>';
			themeLogo.innerHTML = theme === 'light'
				? '<img src="/assets/image/logo/logo.svg" alt="FSS Logo" class="h-12" onerror="this.onerror=null;this.src='/assets/image/logo/logo.svg'" >'
				: '<img src="/assets/image/logo/logo_light.svg" alt="FSS Logo" class="h-12" onerror="this.onerror=null;this.src='/assets/image/logo/logo.svg'" >';

			themeToggle.onclick = function () {
				theme = (theme === 'light') ? 'dark' : 'light';
				html.setAttribute('data-theme', theme);
				localStorage.setItem('theme', theme);
				themeToggle.innerHTML = theme === 'light'
					? '<i class="fas fa-sun text-yellow-500"></i>'
					: '<i class="fas fa-moon text-blue-500"></i>';
				themeLogo.innerHTML = theme === 'light'
					? '<img src="/assets/image/logo/logo.svg" alt="FSS Logo" class="h-12" onerror="this.onerror=null;this.src='/assets/image/logo/logo.svg'" >'
					: '<img src="/assets/image/logo/logo_light.svg" alt="FSS Logo" class="h-12" onerror="this.onerror=null;this.src='/assets/image/logo/logo.svg'" >';
			};

			// Маска +7 (___) ___-__-__ на ВСЕ поля type="tel" как запрошено
			(function(){
				function attachTel(input){
					if(input.dataset.maskAttached) return;
					input.dataset.maskAttached='1';
					input.setAttribute('inputmode','tel');
					input.setAttribute('autocomplete','tel');
					input.addEventListener('focus', function() {
						if (!this.value) this.value = '+7 ';
					});
					input.addEventListener('input', function() {
						var x = this.value.replace(/\D/g, '').substring(0, 11);
						if (x.length === 0) { this.value = ''; return; }
						var formatted = '+7 ';
						if (x.length > 1) formatted += '(' + x.substring(1, 4);
						if (x.length >= 4) formatted += ') ' + x.substring(4, 7);
						if (x.length >= 7) formatted += '-' + x.substring(7, 9);
						if (x.length >= 9) formatted += '-' + x.substring(9, 11);
						this.value = formatted;
					});
				}
				function initAllTel(){
					document.querySelectorAll('input[type="tel"]').forEach(attachTel);
				}
				if(document.readyState==='loading') document.addEventListener('DOMContentLoaded', initAllTel);
				else initAllTel();
				new MutationObserver(mutations=>{
					mutations.forEach(m=>m.addedNodes.forEach(n=>{
						if(n.nodeType===1){
							if(n.matches && n.matches('input[type="tel"]')) attachTel(n);
							n.querySelectorAll && n.querySelectorAll('input[type="tel"]').forEach(attachTel);
						}
					}));
				}).observe(document.body,{childList:true,subtree:true});
				// поддержка старого селектора #phone
				var phoneInput = document.getElementById('phone');
				if (phoneInput) attachTel(phoneInput);
				document.addEventListener('DOMContentLoaded', function(){
					var p=document.getElementById('phone');
					if(p) attachTel(p);
				});
			})();
		});
	</script>

</header>