<?php
// Динамический breadcrumb для SEO
$requestUri = $_SERVER['REQUEST_URI'] ?? '/';
$requestUri = strtok($requestUri, '?');
$requestUri = rtrim($requestUri, '/');
if ($requestUri === '') $requestUri = '/';
$baseUrl = 'https://www.xn--h1aafj.xn--p1ai';
$breadcrumbItems = [];
$breadcrumbItems[] = ['name' => 'Главная', 'url' => $baseUrl . '/'];
// Маппинг путей -> названия
$map = [
  '/pages/uslugi' => 'Услуги',
  '/pages/uslugi/uslugi.php' => 'Услуги',
  '/pages/portfolio.php' => 'Портфолио',
  '/pages/portfolio' => 'Портфолио',
  '/pages/projects.php' => 'Проекты',
  '/pages/projects' => 'Проекты',
  '/pages/politic' => 'Политика конфиденциальности',
  '/pages/agree' => 'Правила обработки данных',
  '/pages/services/kapstroenie' => 'Капитальное строение',
  '/pages/services/kapital' => 'Капитальный ремонт',
  '/pages/services/proizvodim_modul' => 'Производство модульных зданий',
  '/pages/services/modultebel' => 'Модульная мебель',
  '/pages/services/gruzoperevozki' => 'Грузоперевозки',
  '/pages/services/spectehnika' => 'Спецтехника',
  '/pages/services/sclad' => 'Склады',
  '/pages/services/musorbak' => 'Мусорные баки',
  '/pages/services/naves' => 'Навесы',
  '/pages/services/administrativnoe' => 'Административные здания',
  '/pages/services/obchegitia' => 'Общежития',
  '/pages/services/generatornae' => 'Генераторные',
  '/pages/services/compressor' => 'Газовые компрессоры',
  '/pages/services/compressor_air' => 'Воздушные компрессоры',
  '/pages/services/montach' => 'Монтаж инженерных систем',
  '/pages/services/postavka' => 'Поставка материалов',
  '/pages/services/proektirovanie' => 'Проектирование',
];
$matched = null;
foreach ($map as $path => $name) {
  if (strpos($requestUri, $path) !== false || $requestUri === $path) {
    if ($path === '/pages/uslugi' && $requestUri !== '/pages/uslugi/uslugi.php' && $requestUri !== '/pages/uslugi') continue;
    $matched = $path;
    break;
  }
}
// Для вложенных услуг добавляем промежуточный уровень
if (strpos($requestUri, '/pages/services/') !== false) {
  $breadcrumbItems[] = ['name' => 'Услуги', 'url' => $baseUrl . '/pages/uslugi/uslugi.php'];
  if ($matched && isset($map[$matched])) {
    $breadcrumbItems[] = ['name' => $map[$matched], 'url' => $baseUrl . $matched . '/'];
  }
} elseif ($matched) {
  $breadcrumbItems[] = ['name' => $map[$matched], 'url' => $baseUrl . $matched . (str_ends_with($matched, '.php') ? '' : '/')];
}
$hasBreadcrumb = count($breadcrumbItems) > 1;
?>
<!-- Schema.org JSON-LD — Professional SEO -->
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@graph": [
    {
      "@type": "Organization",
      "@id": "https://www.xn--h1aafj.xn--p1ai/#organization",
      "name": "ФАВОРИТ-СТРОЙ-СНАБ",
      "alternateName": "FAVORIT-STROY-SNAB",
      "url": "https://www.xn--h1aafj.xn--p1ai/",
      "logo": {
        "@type": "ImageObject",
        "url": "https://www.xn--h1aafj.xn--p1ai/assets/image/logo/logo.svg",
        "width": 512,
        "height": 512
      },
      "contactPoint": [
        {
          "@type": "ContactPoint",
          "telephone": "+7-938-018-00-06",
          "contactType": "customer service",
          "areaServed": "RU",
          "availableLanguage": ["Russian"],
          "email": "fss@xn--h1aafj.xn--p1ai"
        }
      ],
      "address": {
        "@type": "PostalAddress",
        "postalCode": "101000",
        "addressLocality": "Москва",
        "streetAddress": "ул. Большая Спасоглинищевская, д. 9/1 стр. 10, помещ. 1/2",
        "addressCountry": "RU"
      },
      "sameAs": [
        "https://t.me/fss_rus",
        "https://vk.com/fss_rus"
      ]
    },
    {
      "@type": "WebSite",
      "@id": "https://www.xn--h1aafj.xn--p1ai/#website",
      "url": "https://www.xn--h1aafj.xn--p1ai/",
      "name": "ФАВОРИТ-СТРОЙ-СНАБ — Быстровозводимые здания, капитальный ремонт, грузоперевозки",
      "description": "Быстровозводимые модульные здания под ключ, капитальный ремонт, поставка мебели, грузоперевозки и спецтехника в Москве. От 2 недель, гарантия 50+ лет.",
      "publisher": { "@id": "https://www.xn--h1aafj.xn--p1ai/#organization" },
      "inLanguage": "ru-RU",
      "potentialAction": {
        "@type": "SearchAction",
        "target": {
          "@type": "EntryPoint",
          "urlTemplate": "https://www.xn--h1aafj.xn--p1ai/?s={search_term_string}"
        },
        "query-input": "required name=search_term_string"
      }
    },
    {
      "@type": "LocalBusiness",
      "@id": "https://www.xn--h1aafj.xn--p1ai/#local",
      "name": "ФАВОРИТ-СТРОЙ-СНАБ",
      "image": "https://www.xn--h1aafj.xn--p1ai/assets/image/logo/logo.svg",
      "telephone": "+7-938-018-00-06",
      "email": "fss@xn--h1aafj.xn--p1ai",
      "address": {
        "@type": "PostalAddress",
        "postalCode": "101000",
        "addressLocality": "Москва",
        "streetAddress": "ул. Большая Спасоглинищевская, д. 9/1 стр. 10, помещ. 1/2",
        "addressCountry": "RU"
      },
      "url": "https://www.xn--h1aafj.xn--p1ai/",
      "priceRange": "$$",
      "openingHoursSpecification": {
        "@type": "OpeningHoursSpecification",
        "dayOfWeek": ["Monday","Tuesday","Wednesday","Thursday","Friday"],
        "opens": "09:00",
        "closes": "18:00"
      },
      "geo": {
        "@type": "GeoCoordinates",
        "latitude": "55.754047",
        "longitude": "37.629799"
      }
    },
    {
      "@type": "Service",
      "serviceType": "Быстровозводимые здания под ключ",
      "provider": { "@id": "https://www.xn--h1aafj.xn--p1ai/#organization" },
      "areaServed": { "@type": "Country", "name": "Россия" },
      "url": "https://www.xn--h1aafj.xn--p1ai/pages/services/proizvodim_modul/",
      "description": "Производство и монтаж быстровозводимых модульных зданий (БВЗ): склады, ангары, АБК, общежития от 2 недель."
    },
    {
      "@type": "Service",
      "serviceType": "Капитальный ремонт зданий",
      "provider": { "@id": "https://www.xn--h1aafj.xn--p1ai/#organization" },
      "areaServed": "Москва и Московская область",
      "url": "https://www.xn--h1aafj.xn--p1ai/pages/services/kapital/",
      "description": "Капитальный ремонт школ, общежитий, административных зданий: фасады, кровля, инженерные системы."
    },
    {
      "@type": "Service",
      "serviceType": "Капитальное строительство",
      "provider": { "@id": "https://www.xn--h1aafj.xn--p1ai/#organization" },
      "areaServed": "Москва и Московская область",
      "url": "https://www.xn--h1aafj.xn--p1ai/pages/services/kapstroenie/"
    },
    {
      "@type": "Service",
      "serviceType": "Грузоперевозки и логистика",
      "provider": { "@id": "https://www.xn--h1aafj.xn--p1ai/#organization" },
      "areaServed": "Россия",
      "url": "https://www.xn--h1aafj.xn--p1ai/pages/services/gruzoperevozki/"
    },
    {
      "@type": "Service",
      "serviceType": "Поставка и сборка мебели",
      "provider": { "@id": "https://www.xn--h1aafj.xn--p1ai/#organization" },
      "areaServed": "Москва и Московская область",
      "url": "https://www.xn--h1aafj.xn--p1ai/pages/services/modultebel/"
    },
    {
      "@type": "Service",
      "serviceType": "Аренда спецтехники",
      "provider": { "@id": "https://www.xn--h1aafj.xn--p1ai/#organization" },
      "areaServed": "Москва и Московская область",
      "url": "https://www.xn--h1aafj.xn--p1ai/pages/services/spectehnika/"
    },
    {
      "@type": "Service",
      "serviceType": "Производство мусорных контейнеров",
      "provider": { "@id": "https://www.xn--h1aafj.xn--p1ai/#organization" },
      "areaServed": "Россия",
      "url": "https://www.xn--h1aafj.xn--p1ai/pages/services/musorbak/"
    },
    {
      "@type": "Service",
      "serviceType": "Монтаж инженерных систем",
      "provider": { "@id": "https://www.xn--h1aafj.xn--p1ai/#organization" },
      "areaServed": "Москва и Московская область",
      "url": "https://www.xn--h1aafj.xn--p1ai/pages/services/montach/"
    },
    {
      "@type": "Service",
      "serviceType": "Проектирование зданий",
      "provider": { "@id": "https://www.xn--h1aafj.xn--p1ai/#organization" },
      "areaServed": "Москва и Московская область",
      "url": "https://www.xn--h1aafj.xn--p1ai/pages/services/proektirovanie/"
    }<?php if ($hasBreadcrumb): ?>,
    {
      "@type": "BreadcrumbList",
      "itemListElement": [
<?php foreach ($breadcrumbItems as $i => $bc): ?>
        {
          "@type": "ListItem",
          "position": <?= $i+1 ?>,
          "name": <?= json_encode($bc['name'], JSON_UNESCAPED_UNICODE) ?>,
          "item": <?= json_encode($bc['url'], JSON_UNESCAPED_UNICODE) ?>
        }<?= $i < count($breadcrumbItems)-1 ? ',' : '' ?>

<?php endforeach; ?>
      ]
    }
<?php endif; ?>
  ]
}
</script>
<?php if ($hasBreadcrumb): ?>
<nav aria-label="breadcrumb" style="display:none;">
  <ol>
    <?php foreach ($breadcrumbItems as $bc): ?><li><a href="<?= htmlspecialchars($bc['url']) ?>"><?= htmlspecialchars($bc['name']) ?></a></li><?php endforeach; ?>
  </ol>
</nav>
<?php endif; ?>
