<div class="fixed bottom-6 right-6 z-50">
	<button id="forma_toggle"
		class=" text-white w-14 h-14 rounded-full shadow-lg flex items-center justify-center transition bg-green-500">
		<i class=" fas fa-comment-dots text-2xl"></i>
	</button>
	<!-- Forma Popup -->
	<div id="forma_wrapper" class="hidden absolute bottom-20 right-0 w-72 bg-white rounded-lg shadow-xl p-4">
		<div class=" flex justify-between items-center mb-4">
			<h3 class=" font-bold">Форма обратной связи</h3>
			<button id="forma_close" class=" text-gray-500 hover:text-gray-700">
				<i class=" fas fa-times"></i>
			</button>
		</div>
		<form class="space-y-3" method="post" novalidate>
			<input type="hidden" name="mini-forma">
			<?php
				$antibotPath = __DIR__ . '/antibot_fields.php';
				if (file_exists($antibotPath)) include_once $antibotPath;
				else {
					$alt = __DIR__ . '/../PHP/Antibot.php';
					if (file_exists($alt)) { require_once $alt; echo Antibot::generateFields(); }
				}
			?>
			<div>
				<label for="mini_name" class="sr-only">Ваше имя</label>
				<input id="mini_name" name="name" type="text" placeholder="Ваше имя *" required autocomplete="name" minlength="2" maxlength="100"
					class=" w-full px-3 py-2 border rounded-lg text-sm hover:border-green-500 focus:border-green-500 text-gray-800">
			</div>
			<div>
				<label for="mini_phone" class="sr-only">Телефон</label>
				<input id="mini_phone" name="phone" type="tel" placeholder="Телефон * +7..." required autocomplete="tel" pattern="^\+?[0-9\s\-\(\)]{10,20}$"
					class=" w-full px-3 py-2 border rounded-lg text-sm hover:border-green-500 focus:border-green-500 text-gray-800">
			</div>
			<button type="submit"
				class="w-full text-white hover:text-black py-2 rounded-lg text-sm bg-green-500 hover:bg-green-300 transition duration-300">
				Отправить </button>
			<p class="text-[11px] text-gray-400 text-center mt-1">Защита от спама</p>
		</form>
		<div class=" mt-4 pt-4 border-t text-center">
			<p class=" text-sm mb-2">Или свяжитесь с нами:</p>
			<div class=" flex justify-center space-x-4">
				<a href="tel:+79380180006" class="text-green-500 hover:text-green-300 transition"> <i
						class=" fas fa-phone-alt text-xl"></i>
				</a>
			</div>
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