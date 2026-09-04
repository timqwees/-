/*
 * ATTENTION: The "eval" devtool has been used (maybe by default in mode: "development").
 * This devtool is neither made for production nor for readable output files.
 * It uses "eval()" calls to create a separate source file in the browser devtools.
 * If you are trying to read the output file, select a different devtool (https://webpack.js.org/configuration/devtool/)
 * or disable the default devtool with "devtool: false".
 * If you are looking for production-ready output files, see mode: "production" (https://webpack.js.org/configuration/mode/).
 */
/******/ var __webpack_modules__ = ({

/***/ "./src/timqwees_scripts/script/faq_open.js":
/*!*************************************************!*\
  !*** ./src/timqwees_scripts/script/faq_open.js ***!
  \*************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

eval("{__webpack_require__.r(__webpack_exports__);\n/* harmony export */ __webpack_require__.d(__webpack_exports__, {\n/* harmony export */   openFAQ: () => (/* binding */ openFAQ)\n/* harmony export */ });\nfunction openFAQ() {\n\tdocument.addEventListener('DOMContentLoaded', function () {\n\t\tdocument.querySelectorAll('.faq-toggle').forEach(function (btn) {\n\t\t\tbtn.addEventListener('click', function () {\n\t\t\t\tconst content = btn.nextElementSibling;\n\t\t\t\tconst icon = btn.querySelector('i');\n\t\t\t\tif (content.classList.contains('hidden')) {\n\t\t\t\t\t// Скрыть все остальные\n\t\t\t\t\tdocument.querySelectorAll('.faq-content').forEach(function (el) {\n\t\t\t\t\t\tel.classList.add('hidden');\n\t\t\t\t\t});\n\t\t\t\t\tdocument.querySelectorAll('.faq-toggle i').forEach(function (ic) {\n\t\t\t\t\t\tic.classList.remove('fa-rotate-180');\n\t\t\t\t\t});\n\t\t\t\t\t// Показать выбранный\n\t\t\t\t\tcontent.classList.remove('hidden');\n\t\t\t\t\ticon.classList.add('fa-rotate-180');\n\t\t\t\t} else {\n\t\t\t\t\tcontent.classList.add('hidden');\n\t\t\t\t\ticon.classList.remove('fa-rotate-180');\n\t\t\t\t}\n\t\t\t});\n\t\t});\n\t});\n}\n\n//# sourceURL=webpack://karim_(stroy_firma)/./src/timqwees_scripts/script/faq_open.js?\n}");

/***/ }),

/***/ "./src/timqwees_scripts/script/file.js":
/*!*********************************************!*\
  !*** ./src/timqwees_scripts/script/file.js ***!
  \*********************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

eval("{__webpack_require__.r(__webpack_exports__);\n/* harmony export */ __webpack_require__.d(__webpack_exports__, {\n/* harmony export */   faq_toggel: () => (/* binding */ faq_toggel)\n/* harmony export */ });\nfunction faq_toggel() {\n\tdocument.querySelectorAll('.faq-toggle').forEach(button => {\n\t\tbutton.addEventListener('click', () => {\n\t\t\tconst content = button.nextElementSibling;\n\t\t\tconst icon = button.querySelector('i');\n\n\t\t\tcontent.classList.toggle('hidden');\n\t\t\ticon.classList.toggle('rotate-180');\n\t\t});\n\t});\n}\n\n//# sourceURL=webpack://karim_(stroy_firma)/./src/timqwees_scripts/script/file.js?\n}");

/***/ }),

/***/ "./src/timqwees_scripts/script/header_footer.js":
/*!******************************************************!*\
  !*** ./src/timqwees_scripts/script/header_footer.js ***!
  \******************************************************/
/***/ (() => {

eval("{\nfetch(\"/assets/componets/header.php\")\n\t.then(response => response.text())\n\t.then(data => {\n\t\tdocument.getElementById('header').innerHTML = data;\n\t});\n\nfetch(\"/assets/componets/footer.php\")\n\t.then(response => response.text())\n\t.then(data => {\n\t\tdocument.getElementById('footer').innerHTML = data;\n\t});\n\n//# sourceURL=webpack://karim_(stroy_firma)/./src/timqwees_scripts/script/header_footer.js?\n}");

/***/ }),

/***/ "./src/timqwees_scripts/script/index.js":
/*!**********************************************!*\
  !*** ./src/timqwees_scripts/script/index.js ***!
  \**********************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

eval("{__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _file_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./file.js */ \"./src/timqwees_scripts/script/file.js\");\n/* harmony import */ var _faq_open_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./faq_open.js */ \"./src/timqwees_scripts/script/faq_open.js\");\n/* harmony import */ var _header_footer_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./header_footer.js */ \"./src/timqwees_scripts/script/header_footer.js\");\n/* harmony import */ var _header_footer_js__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_header_footer_js__WEBPACK_IMPORTED_MODULE_2__);\n/* harmony import */ var _modal_js__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./modal.js */ \"./src/timqwees_scripts/script/modal.js\");\n/* harmony import */ var _modal_js__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_modal_js__WEBPACK_IMPORTED_MODULE_3__);\n/* harmony import */ var _view_js__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ./view.js */ \"./src/timqwees_scripts/script/view.js\");\n/* harmony import */ var _map_js__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ./map.js */ \"./src/timqwees_scripts/script/map.js\");\n\n\n\n\n\n\n\n(0,_modal_js__WEBPACK_IMPORTED_MODULE_3__.ModalWindow)();\n(0,_file_js__WEBPACK_IMPORTED_MODULE_0__.faq_toggel)();\n(0,_faq_open_js__WEBPACK_IMPORTED_MODULE_1__.openFAQ)();\ninitViewElements();\n(0,_map_js__WEBPACK_IMPORTED_MODULE_5__.initYandexMapKavstal)();\n(0,_modal_js__WEBPACK_IMPORTED_MODULE_3__.initModalHandlers)();\n// contentHTML();\n\n//# sourceURL=webpack://karim_(stroy_firma)/./src/timqwees_scripts/script/index.js?\n}");

/***/ }),

/***/ "./src/timqwees_scripts/script/map.js":
/*!********************************************!*\
  !*** ./src/timqwees_scripts/script/map.js ***!
  \********************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

eval("{__webpack_require__.r(__webpack_exports__);\n/* harmony export */ __webpack_require__.d(__webpack_exports__, {\n/* harmony export */   initYandexMapKavstal: () => (/* binding */ initYandexMapKavstal)\n/* harmony export */ });\n// Новый вариант инициализации Яндекс.Карты для #map_kavstal\n// (адаптировано по примеру из file_context_0, с сохранением кастомных иконок)\n\nfunction initYandexMapKavstal() {\n\t// Проверяем, что элемент для карты существует\n\tvar mapDiv = document.getElementById('map_kavstal');\n\tif (!mapDiv) return;\n\n\tfunction initYandexMap() {\n\t\tif (typeof ymaps === 'undefined') {\n\t\t\tsetTimeout(initYandexMap, 500);\n\t\t\treturn;\n\t\t}\n\t\tymaps.ready(function () {\n\t\t\t// Удаляем предыдущий экземпляр карты, если он был\n\t\t\tif (mapDiv._yandexMapInstance) {\n\t\t\t\tmapDiv._yandexMapInstance.destroy();\n\t\t\t\tmapDiv._yandexMapInstance = null;\n\t\t\t}\n\n\t\t\t// Создаем карту\n\t\t\tvar mapKav = new ymaps.Map('map_kavstal', {\n\t\t\t\tcenter: [55.783514, 37.720232],\n\t\t\t\tzoom: 17,\n\t\t\t\tcontrols: []\n\t\t\t}, {\n\t\t\t\tsearchControlProvider: 'yandex#search'\n\t\t\t});\n\n\t\t\t// Добавляем кастомные объекты (два слоя иконок)\n\t\t\tvar features = [\n\t\t\t\t{\n\t\t\t\t\ttype: 'Feature',\n\t\t\t\t\tgeometry: {\n\t\t\t\t\t\ttype: 'Point',\n\t\t\t\t\t\tcoordinates: [55.783514, 37.720232],\n\t\t\t\t\t},\n\t\t\t\t\tproperties: {\n\t\t\t\t\t\thintContent: 'Семёновская площадь, 7'\n\t\t\t\t\t},\n\t\t\t\t\toptions: {\n\t\t\t\t\t\ticonLayout: 'default#imageWithContent',\n\t\t\t\t\t\ticonImageHref: '/local/templates/rs2023/img/map/yandex-maps-logo.png',\n\t\t\t\t\t\ticonImageOpacity: 0.2,\n\t\t\t\t\t\ticonImageSize: [50, 50],\n\t\t\t\t\t\ticonImageOffset: [-30, -60]\n\t\t\t\t\t}\n\t\t\t\t},\n\t\t\t\t{\n\t\t\t\t\ttype: 'Feature',\n\t\t\t\t\tgeometry: {\n\t\t\t\t\t\ttype: 'Point',\n\t\t\t\t\t\tcoordinates: [55.783514, 37.720232],\n\t\t\t\t\t},\n\t\t\t\t\tproperties: {\n\t\t\t\t\t\thintContent: 'Семёновская площадь, 7'\n\t\t\t\t\t},\n\t\t\t\t\toptions: {\n\t\t\t\t\t\ticonLayout: 'default#imageWithContent',\n\t\t\t\t\t\ticonImageHref: '/local/templates/rs2023/img/favicon.svg',\n\t\t\t\t\t\ticonImageSize: [23, 23],\n\t\t\t\t\t\ticonImageOffset: [-16.5, -49]\n\t\t\t\t\t}\n\t\t\t\t}\n\t\t\t];\n\n\t\t\t// Добавляем объекты на карту\n\t\t\tif (ymaps.geoQuery) {\n\t\t\t\tymaps.geoQuery({\n\t\t\t\t\ttype: \"FeatureCollection\",\n\t\t\t\t\tfeatures: features\n\t\t\t\t}).addToMap(mapKav);\n\t\t\t} else {\n\t\t\t\t// fallback: добавить как обычные Placemark\n\t\t\t\tfeatures.forEach(function (f) {\n\t\t\t\t\tvar placemark = new ymaps.Placemark(\n\t\t\t\t\t\tf.geometry.coordinates,\n\t\t\t\t\t\tf.properties,\n\t\t\t\t\t\tf.options\n\t\t\t\t\t);\n\t\t\t\t\tmapKav.geoObjects.add(placemark);\n\t\t\t\t});\n\t\t\t}\n\n\t\t\tmapKav.behaviors.disable('scrollZoom');\n\n\t\t\t// Сохраняем ссылку на карту в DOM-элементе (для возможного повторного использования)\n\t\t\tmapDiv._yandexMapInstance = mapKav;\n\t\t});\n\t}\n\n\tinitYandexMap();\n}\n\n// Для автоматической инициализации при DOMContentLoaded (если нужно)\nif (typeof window !== 'undefined') {\n\tdocument.addEventListener('DOMContentLoaded', initYandexMapKavstal);\n}\n\n\n//# sourceURL=webpack://karim_(stroy_firma)/./src/timqwees_scripts/script/map.js?\n}");

/***/ }),

/***/ "./src/timqwees_scripts/script/modal.js":
/*!**********************************************!*\
  !*** ./src/timqwees_scripts/script/modal.js ***!
  \**********************************************/
/***/ (() => {

eval("{function initModalHandlers() {\n\tconst modal = document.getElementById('catalog-modal');\n\tconst modalBackdrop = document.getElementById('catalog-modal-backdrop');\n\tconst modalClose = document.getElementById('catalog-modal-close');\n\tconst modalTitle = document.getElementById('catalog-modal-title');\n\tconst modalDesc = document.getElementById('catalog-modal-desc');\n\n\t// Polyfill for <dialog> if needed\n\tif (modal && typeof modal.showModal !== 'function') {\n\t\tif (window.dialogPolyfill) {\n\t\t\twindow.dialogPolyfill.registerDialog(modal);\n\t\t}\n\t}\n\n\tdocument.querySelectorAll('.openWindow').forEach(card => {\n\t\tconst btn = card.querySelector('.openWindow_button');\n\t\tif (!btn) return;\n\t\tbtn.style.cursor = 'pointer';\n\t\tbtn.addEventListener('click', function () {\n\t\t\tconst titleElem = card.querySelector('.openWindow_title');\n\t\t\tconst descElem = card.querySelector('.openWindow_description');\n\t\t\tconst title = titleElem ? (titleElem.innerText || titleElem.textContent || '') : '';\n\t\t\tlet desc = '';\n\t\t\tif (descElem) {\n\t\t\t\tif (descElem.tagName === 'INPUT') {\n\t\t\t\t\tdesc = descElem.value;\n\t\t\t\t} else if (descElem.tagName === 'TEXTAREA') {\n\t\t\t\t\tdesc = descElem.value;\n\t\t\t\t} else {\n\t\t\t\t\tdesc = descElem.innerHTML || '';\n\t\t\t\t}\n\t\t\t}\n\t\t\tif (modalTitle) modalTitle.innerHTML = title;\n\t\t\tif (modalDesc) modalDesc.innerHTML = desc;\n\t\t\tif (modal) {\n\t\t\t\tif (typeof modal.showModal === 'function') {\n\t\t\t\t\tmodal.showModal();\n\t\t\t\t} else {\n\t\t\t\t\tmodal.setAttribute('open', '');\n\t\t\t\t}\n\t\t\t}\n\t\t});\n\t});\n\n\t// Close modal by close button\n\tif (modalClose && modal) {\n\t\tmodalClose.addEventListener('click', function () {\n\t\t\tmodal.close();\n\t\t});\n\t}\n\t// Close modal by clicking backdrop\n\tif (modalBackdrop && modal) {\n\t\tmodalBackdrop.addEventListener('click', function (e) {\n\t\t\tif (e.target === modalBackdrop) {\n\t\t\t\tmodal.close();\n\t\t\t}\n\t\t});\n\t}\n\t// Close modal by Esc\n\tif (modal) {\n\t\tmodal.addEventListener('cancel', function (e) {\n\t\t\te.preventDefault();\n\t\t\tmodal.close();\n\t\t});\n\t}\n}\n\nwindow.initModalHandlers = initModalHandlers;\n\n// Для первоначальной инициализации, если элементы уже есть на странице\nif (document.readyState === 'loading') {\n\tdocument.addEventListener('DOMContentLoaded', function () {\n\t\tinitModalHandlers();\n\t});\n} else {\n\tinitModalHandlers();\n}\n\n//# sourceURL=webpack://karim_(stroy_firma)/./src/timqwees_scripts/script/modal.js?\n}");

/***/ }),

/***/ "./src/timqwees_scripts/script/view.js":
/*!*********************************************!*\
  !*** ./src/timqwees_scripts/script/view.js ***!
  \*********************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

eval("{__webpack_require__.r(__webpack_exports__);\n/* harmony export */ __webpack_require__.d(__webpack_exports__, {\n/* harmony export */   initViewElements: () => (/* binding */ initViewElements)\n/* harmony export */ });\nfunction initViewElements() {\n\tconst conf = {\n\t\tdistantion: 30,\n\t\tview: {\n\t\t\t'0': '0rem',\n\t\t\t'10': '10rem',\n\t\t\t'-10': '-10rem'\n\t\t},\n\t};\n\n\tfunction checkBlocksVisibility() {\n\t\tconst windowHeight = window.innerHeight;\n\t\tconst view = document.querySelectorAll('.View, .view');\n\t\tconst viewLeft = document.querySelectorAll('.viewLeft, .viewleft');\n\t\tconst viewRight = document.querySelectorAll('.viewRight, .viewright');\n\n\t\tview.forEach(element => {\n\t\t\tconst viewHeight = element.getBoundingClientRect().top;\n\t\t\tif (viewHeight < windowHeight - conf.distantion) {\n\t\t\t\telement.style.opacity = '1';\n\t\t\t\telement.style.transform = `translateY(${conf.view['0']})`;\n\t\t\t} else {\n\t\t\t\telement.style.opacity = '0';\n\t\t\t\telement.style.transform = `translateY(${conf.view['10']})`;\n\t\t\t}\n\t\t});\n\t\tviewLeft.forEach(element => {\n\t\t\tconst viewHeight = element.getBoundingClientRect().top;\n\t\t\tif (viewHeight < windowHeight - conf.distantion) {\n\t\t\t\telement.style.opacity = '1';\n\t\t\t\telement.style.transform = `translateX(${conf.view['0']})`;\n\t\t\t} else {\n\t\t\t\telement.style.opacity = '0';\n\t\t\t\telement.style.transform = `translateX(${conf.view['-10']})`;\n\t\t\t}\n\t\t});\n\t\tviewRight.forEach(element => {\n\t\t\tconst viewHeight = element.getBoundingClientRect().top;\n\t\t\tif (viewHeight < windowHeight - conf.distantion) {\n\t\t\t\telement.style.opacity = '1';\n\t\t\t\telement.style.transform = `translateX(${conf.view['0']})`;\n\t\t\t} else {\n\t\t\t\telement.style.opacity = '0';\n\t\t\t\telement.style.transform = `translateX(${conf.view['10']})`;\n\t\t\t}\n\t\t});\n\t}\n\n\tcheckBlocksVisibility();\n\n\twindow.addEventListener('scroll', checkBlocksVisibility);\n}\n\nwindow.initViewElements = initViewElements;\n\n// Для первоначальной инициализации, если элементы уже есть на странице\nif (document.readyState === 'loading') {\n\tdocument.addEventListener('DOMContentLoaded', function () {\n\t\tinitViewElements();\n\t});\n} else {\n\tinitViewElements();\n}\n\n//# sourceURL=webpack://karim_(stroy_firma)/./src/timqwees_scripts/script/view.js?\n}");

/***/ })

/******/ });
/************************************************************************/
/******/ // The module cache
/******/ var __webpack_module_cache__ = {};
/******/ 
/******/ // The require function
/******/ function __webpack_require__(moduleId) {
/******/ 	// Check if module is in cache
/******/ 	var cachedModule = __webpack_module_cache__[moduleId];
/******/ 	if (cachedModule !== undefined) {
/******/ 		return cachedModule.exports;
/******/ 	}
/******/ 	// Create a new module (and put it into the cache)
/******/ 	var module = __webpack_module_cache__[moduleId] = {
/******/ 		// no module.id needed
/******/ 		// no module.loaded needed
/******/ 		exports: {}
/******/ 	};
/******/ 
/******/ 	// Execute the module function
/******/ 	__webpack_modules__[moduleId](module, module.exports, __webpack_require__);
/******/ 
/******/ 	// Return the exports of the module
/******/ 	return module.exports;
/******/ }
/******/ 
/************************************************************************/
/******/ /* webpack/runtime/compat get default export */
/******/ (() => {
/******/ 	// getDefaultExport function for compatibility with non-harmony modules
/******/ 	__webpack_require__.n = (module) => {
/******/ 		var getter = module && module.__esModule ?
/******/ 			() => (module['default']) :
/******/ 			() => (module);
/******/ 		__webpack_require__.d(getter, { a: getter });
/******/ 		return getter;
/******/ 	};
/******/ })();
/******/ 
/******/ /* webpack/runtime/define property getters */
/******/ (() => {
/******/ 	// define getter functions for harmony exports
/******/ 	__webpack_require__.d = (exports, definition) => {
/******/ 		for(var key in definition) {
/******/ 			if(__webpack_require__.o(definition, key) && !__webpack_require__.o(exports, key)) {
/******/ 				Object.defineProperty(exports, key, { enumerable: true, get: definition[key] });
/******/ 			}
/******/ 		}
/******/ 	};
/******/ })();
/******/ 
/******/ /* webpack/runtime/hasOwnProperty shorthand */
/******/ (() => {
/******/ 	__webpack_require__.o = (obj, prop) => (Object.prototype.hasOwnProperty.call(obj, prop))
/******/ })();
/******/ 
/******/ /* webpack/runtime/make namespace object */
/******/ (() => {
/******/ 	// define __esModule on exports
/******/ 	__webpack_require__.r = (exports) => {
/******/ 		if(typeof Symbol !== 'undefined' && Symbol.toStringTag) {
/******/ 			Object.defineProperty(exports, Symbol.toStringTag, { value: 'Module' });
/******/ 		}
/******/ 		Object.defineProperty(exports, '__esModule', { value: true });
/******/ 	};
/******/ })();
/******/ 
/************************************************************************/
/******/ 
/******/ // startup
/******/ // Load entry module and return exports
/******/ // This entry module can't be inlined because the eval devtool is used.
/******/ var __webpack_exports__ = __webpack_require__("./src/timqwees_scripts/script/index.js");
/******/ 
