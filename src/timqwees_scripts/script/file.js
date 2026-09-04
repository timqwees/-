export function faq_toggel() {
	document.querySelectorAll('.faq-toggle').forEach(button => {
		button.addEventListener('click', () => {
			const content = button.nextElementSibling;
			const icon = button.querySelector('i');

			content.classList.toggle('hidden');
			icon.classList.toggle('rotate-180');
		});
	});
}