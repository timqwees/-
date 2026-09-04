// Новый вариант инициализации Яндекс.Карты для #map_kavstal
// (адаптировано по примеру из file_context_0, с сохранением кастомных иконок)

export function initYandexMapKavstal() {
	// Проверяем, что элемент для карты существует
	var mapDiv = document.getElementById('map_kavstal');
	if (!mapDiv) return;

	function initYandexMap() {
		if (typeof ymaps === 'undefined') {
			setTimeout(initYandexMap, 500);
			return;
		}
		ymaps.ready(function () {
			// Удаляем предыдущий экземпляр карты, если он был
			if (mapDiv._yandexMapInstance) {
				mapDiv._yandexMapInstance.destroy();
				mapDiv._yandexMapInstance = null;
			}

			// Создаем карту
			var mapKav = new ymaps.Map('map_kavstal', {
				center: [55.783514, 37.720232],
				zoom: 17,
				controls: []
			}, {
				searchControlProvider: 'yandex#search'
			});

			// Добавляем кастомные объекты (два слоя иконок)
			var features = [
				{
					type: 'Feature',
					geometry: {
						type: 'Point',
						coordinates: [55.783514, 37.720232],
					},
					properties: {
						hintContent: 'Семёновская площадь, 7'
					},
					options: {
						iconLayout: 'default#imageWithContent',
						iconImageHref: '/local/templates/rs2023/img/map/yandex-maps-logo.png',
						iconImageOpacity: 0.2,
						iconImageSize: [50, 50],
						iconImageOffset: [-30, -60]
					}
				},
				{
					type: 'Feature',
					geometry: {
						type: 'Point',
						coordinates: [55.783514, 37.720232],
					},
					properties: {
						hintContent: 'Семёновская площадь, 7'
					},
					options: {
						iconLayout: 'default#imageWithContent',
						iconImageHref: '/local/templates/rs2023/img/favicon.svg',
						iconImageSize: [23, 23],
						iconImageOffset: [-16.5, -49]
					}
				}
			];

			// Добавляем объекты на карту
			if (ymaps.geoQuery) {
				ymaps.geoQuery({
					type: "FeatureCollection",
					features: features
				}).addToMap(mapKav);
			} else {
				// fallback: добавить как обычные Placemark
				features.forEach(function (f) {
					var placemark = new ymaps.Placemark(
						f.geometry.coordinates,
						f.properties,
						f.options
					);
					mapKav.geoObjects.add(placemark);
				});
			}

			mapKav.behaviors.disable('scrollZoom');

			// Сохраняем ссылку на карту в DOM-элементе (для возможного повторного использования)
			mapDiv._yandexMapInstance = mapKav;
		});
	}

	initYandexMap();
}

// Для автоматической инициализации при DOMContentLoaded (если нужно)
if (typeof window !== 'undefined') {
	document.addEventListener('DOMContentLoaded', initYandexMapKavstal);
}
