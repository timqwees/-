function initModalHandlers() {
	const modal = document.getElementById('catalog-modal');
	const modalBackdrop = document.getElementById('catalog-modal-backdrop');
	const modalClose = document.getElementById('catalog-modal-close');
	const modalTitle = document.getElementById('catalog-modal-title');
	const modalDesc = document.getElementById('catalog-modal-desc');

	// Polyfill for <dialog> if needed
	if (modal && typeof modal.showModal !== 'function') {
		if (window.dialogPolyfill) {
			window.dialogPolyfill.registerDialog(modal);
		}
	}

	document.querySelectorAll('.openWindow').forEach(card => {
		const btn = card.querySelector('.openWindow_button');
		if (!btn) return;
		btn.style.cursor = 'pointer';
		btn.addEventListener('click', function () {
			const titleElem = card.querySelector('.openWindow_title');
			const descElem = card.querySelector('.openWindow_description');
			const title = titleElem ? (titleElem.innerText || titleElem.textContent || '') : '';
			let desc = '';
			if (descElem) {
				if (descElem.tagName === 'INPUT') {
					desc = descElem.value;
				} else if (descElem.tagName === 'TEXTAREA') {
					desc = descElem.value;
				} else {
					desc = descElem.innerHTML || '';
				}
			}
			if (modalTitle) modalTitle.innerHTML = title;
			if (modalDesc) modalDesc.innerHTML = desc;
			if (modal) {
				if (typeof modal.showModal === 'function') {
					modal.showModal();
				} else {
					modal.setAttribute('open', '');
				}
			}
		});
	});

	// Close modal by close button
	if (modalClose && modal) {
		modalClose.addEventListener('click', function () {
			modal.close();
		});
	}
	// Close modal by clicking backdrop
	if (modalBackdrop && modal) {
		modalBackdrop.addEventListener('click', function (e) {
			if (e.target === modalBackdrop) {
				modal.close();
			}
		});
	}
	// Close modal by Esc
	if (modal) {
		modal.addEventListener('cancel', function (e) {
			e.preventDefault();
			modal.close();
		});
	}
}

window.initModalHandlers = initModalHandlers;

// Для первоначальной инициализации, если элементы уже есть на странице
if (document.readyState === 'loading') {
	document.addEventListener('DOMContentLoaded', function () {
		initModalHandlers();
	});
} else {
	initModalHandlers();
}