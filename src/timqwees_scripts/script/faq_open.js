export function openFAQ() {
	document.addEventListener('DOMContentLoaded', function () {
		document.querySelectorAll('.faq-toggle').forEach(function (btn) {
			btn.addEventListener('click', function () {
				const content = btn.nextElementSibling;
				const icon = btn.querySelector('i');
				if (content.classList.contains('hidden')) {
					// Скрыть все остальные
					document.querySelectorAll('.faq-content').forEach(function (el) {
						el.classList.add('hidden');
					});
					document.querySelectorAll('.faq-toggle i').forEach(function (ic) {
						ic.classList.remove('fa-rotate-180');
					});
					// Показать выбранный
					content.classList.remove('hidden');
					icon.classList.add('fa-rotate-180');
				} else {
					content.classList.add('hidden');
					icon.classList.remove('fa-rotate-180');
				}
			});
		});
	});
}