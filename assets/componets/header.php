<style>
	.visible-1100 {
		display: none;
	}

	@media screen and (max-width:1286px) {
		.hidden-1286 {
			display: none;
		}
	}

	@media screen and (max-width:1100px) {
		.hidden-1100 {
			display: none;
		}

		.visible-1100 {
			display: flex;
		}

		.underblock_line {
			font-size: 20px;
		}
	}

	@media screen and (max-width:490px) {
		.hidden-490 {
			display: none;
		}
	}
</style>
<header class="bg-white backdrop-blur-md shadow-sm fixed top-0 z-50 w-full">
	<div class=" mx-auto px-4 py-2 flex justify-between items-center">
		<a class="flex items-center ml-2" href="/index.php">

			<div id="themeLogo"></div>

			<span class="ml-3 text-xl font-bold text-gray-800 w-[220px] hidden-490">Фаворит-Строй-Снаб</span>
		</a>
		<nav class="flex space-x-8 items-center">
			<a href="/pages/uslugi/uslugi.php"
				class="relative text-gray-600 hover:text-green-600 transition group hidden-1100">
				<span class="relative z-10">Услуги</span>
				<span
					class="absolute left-0 bottom-0 w-0 h-0.5 bg-green-500 transition-all duration-300 group-hover:w-full"></span>
			</a>
			<a href="/index.php#process"
				class="relative text-gray-600 hover:text-green-600 transition group hidden-1100">
				<span class="relative z-10">Этапы строительства</span>
				<span
					class="absolute left-0 bottom-0 w-0 h-0.5 bg-green-500 transition-all duration-300 group-hover:w-full"></span>
			</a>
			<a href="/pages/projects.php"
				class="relative text-gray-600 hover:text-green-600 transition group hidden-1100">
				<span class="relative z-10">Каталог обьектов</span>
				<span
					class="absolute left-0 bottom-0 w-0 h-0.5 bg-green-500 transition-all duration-300 group-hover:w-full"></span>
			</a>
			<a href="/index.php#advantages"
				class="relative text-gray-600 hover:text-green-600 transition group hidden-1100">
				<span class="relative z-10">Мы выполняем</span>
				<span
					class="absolute left-0 bottom-0 w-0 h-0.5 bg-green-500 transition-all duration-300 group-hover:w-full"></span>
			</a>
			<!-- <a href="/pages/projects.php"
				class="relative text-gray-600 hover:text-green-600 transition group hidden-1100">
				<span class="relative z-10">Проектирование</span>
				<span
					class="absolute left-0 bottom-0 w-0 h-0.5 bg-green-500 transition-all duration-300 group-hover:w-full"></span>
			</a> -->
			<a href="/pages/portfolio.php"
				class="relative text-black hover:opacity-75 transition group bg-green-300 rounded-lg px-5 py-1 hidden-1286">
				<span class="relative z-10">Примеры обьектов</span>
				<span
					class="absolute left-0 bottom-0 w-0 h-0.5 bg-green-500 transition-all duration-300 group-hover:w-full"></span>
			</a>
			<button class="relative cursor-pointer mr-[15px]" id="themeElement"
				style="margin-left: 15px !important"></button>
			<button
				class="relative cursor-pointer visible-1100 flex flex-col justify-center items-center max-w-[40px] gap-[5px]"
				id="toggleMenu" aria-label="Открыть меню">
				<span class="hanburger_line"></span>
				<span class="hanburger_line"></span>
				<span class="hanburger_line"></span>
			</button>
		</nav>
	</div>


	<!-- mobile version menu -->
	<div class="bg-white backdrop-blur-md shadow-sm top-0 w-full mx-auto px-4 py-4 flex flex-col absolute left-0 bg-white visible-1100 h-[100dvh] z-50"
		id="content_menu">

		<button class="z-20 cursor-pointer flex flex-col justify-center items-center absolute color-red px-2 py-2"
			style="top: 20px;
				right: 26px;" id="btn_close_menu" aria-label="Закрыть меню">
			<li class="close_line_menu_one"></li>
			<li class="close_line_menu_two"></li>
		</button>

		<nav class="flex flex-col items-start space-y-8 items-left">

			<div class="flex justify-between underblock_line w-full relative">
				<a href="/pages/uslugi/uslugi.php"
					class="flex justify-between text-gray-600 hover:text-green-600 transition group w-full"
					style="margin-left: 2rem; margin-top: 3.5rem;">
					<span class="relative z-10">Услуги</span>
					<i class="fa fa-arrow-right translate-y-[10px]"></i>
				</a>
			</div>

			<div class="flex justify-between underblock_line w-full relative">
				<a href="/index.php#process"
					class="flex justify-between text-gray-600 hover:text-green-600 transition group w-full"
					style="margin-left: 2rem;">
					<span class="relative z-10">Этапы строительства</span>
					<i class="fa fa-arrow-right translate-y-[10px]"></i>
				</a>
			</div>

			<div class="flex justify-between underblock_line w-full relative">
				<a href="/pages/projects.php"
					class="flex justify-between text-gray-600 hover:text-green-600 transition group w-full"
					style="margin-left: 2rem;">
					<span class="relative z-10">Каталог обьектов</span>
					<i class="fa fa-arrow-right translate-y-[10px]"></i>
				</a>
			</div>

			<div class="flex justify-between underblock_line w-full relative">
				<a href="/index.php#advantages"
					class="flex justify-between text-gray-600 hover:text-green-600 transition group w-full"
					style="margin-left: 2rem;">
					<span class="relative z-10">Мы выполняем</span>
					<i class="fa fa-arrow-right translate-y-[10px]"></i>
				</a>
			</div>

			<!-- <div class="flex justify-between underblock_line w-full relative">
				<a href="/pages/projects.php"
					class="flex justify-between text-gray-600 hover:text-green-600 transition group w-full"
					style="margin-left: 2rem;">
					<span class="relative z-10">Проектирование</span>
					<i class="fa fa-arrow-right translate-y-[10px]"></i>
				</a>
			</div> -->

			<div class="flex justify-between underblock_line w-full relative">
				<a href="/pages/portfolio.php"
					class="flex justify-between text-gray-600 hover:text-green-600 transition group w-full"
					style="margin-left: 2rem;">
					<span class="relative z-10">Примеры объектов</span>
					<i class="fa fa-arrow-right translate-y-[10px]"></i>
				</a>
			</div>

		</nav>
	</div>

	<script>
		document.addEventListener('DOMContentLoaded', () => {
			const togglemenu = document.getElementById('toggleMenu');
			const content_menu = document.getElementById('content_menu');
			const btn_close = document.getElementById('btn_close_menu');

			togglemenu.addEventListener('click', () => {
				content_menu.classList.toggle('active');
			});

			document.addEventListener('keydown', (e) => {
				if (e.key === 'Escape') {
					if (content_menu.classList.contains('active')) {
						content_menu.classList.remove('active');
					}
				}
			});

			document.addEventListener('cancel', () => {
				if (content_menu.classList.contains('active')) {
					content_menu.classList.remove('active');
				}
			});

			btn_close.addEventListener('click', () => {
				if (content_menu.classList.contains('active')) {
					content_menu.classList.remove('active');
				}
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
				? '<img src="/assets/image/logo/logo.svg" alt="FSS Logo" class="h-12">'
				: '<img src="/assets/image/logo/logo_light.svg" alt="FSS Logo" class="h-12">';

			themeToggle.onclick = function () {
				theme = (theme === 'light') ? 'dark' : 'light';
				html.setAttribute('data-theme', theme);
				localStorage.setItem('theme', theme);
				themeToggle.innerHTML = theme === 'light'
					? '<i class="fas fa-sun text-yellow-500"></i>'
					: '<i class="fas fa-moon text-blue-500"></i>';
				themeLogo.innerHTML = theme === 'light'
					? '<img src="/assets/image/logo/logo.svg" alt="FSS Logo" class="h-12">'
					: '<img src="/assets/image/logo/logo_light.svg" alt="FSS Logo" class="h-12">';
			};
		});
	</script>

</header>