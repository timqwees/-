export function initViewElements() {
	const conf = {
		distantion: 30,
		view: {
			'0': '0rem',
			'10': '10rem',
			'-10': '-10rem'
		},
	};

	function checkBlocksVisibility() {
		const windowHeight = window.innerHeight;
		const view = document.querySelectorAll('.View, .view');
		const viewLeft = document.querySelectorAll('.viewLeft, .viewleft');
		const viewRight = document.querySelectorAll('.viewRight, .viewright');

		view.forEach(element => {
			const viewHeight = element.getBoundingClientRect().top;
			if (viewHeight < windowHeight - conf.distantion) {
				element.style.opacity = '1';
				element.style.transform = `translateY(${conf.view['0']})`;
			} else {
				element.style.opacity = '0';
				element.style.transform = `translateY(${conf.view['10']})`;
			}
		});
		viewLeft.forEach(element => {
			const viewHeight = element.getBoundingClientRect().top;
			if (viewHeight < windowHeight - conf.distantion) {
				element.style.opacity = '1';
				element.style.transform = `translateX(${conf.view['0']})`;
			} else {
				element.style.opacity = '0';
				element.style.transform = `translateX(${conf.view['-10']})`;
			}
		});
		viewRight.forEach(element => {
			const viewHeight = element.getBoundingClientRect().top;
			if (viewHeight < windowHeight - conf.distantion) {
				element.style.opacity = '1';
				element.style.transform = `translateX(${conf.view['0']})`;
			} else {
				element.style.opacity = '0';
				element.style.transform = `translateX(${conf.view['10']})`;
			}
		});
	}

	checkBlocksVisibility();

	// Throttle scroll via rAF for performance
	let ticking = false;
	window.addEventListener('scroll', function() {
		if (!ticking) {
			window.requestAnimationFrame(function() {
				checkBlocksVisibility();
				ticking = false;
			});
			ticking = true;
		}
	}, { passive: true });
}

window.initViewElements = initViewElements;

// Для первоначальной инициализации, если элементы уже есть на странице
if (document.readyState === 'loading') {
	document.addEventListener('DOMContentLoaded', function () {
		initViewElements();
	});
} else {
	initViewElements();
}