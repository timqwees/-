<?php
require_once __DIR__ . '/../../../app/bootstrap.php';
?>
<!DOCTYPE html>
<html lang="ru">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<link rel="preload" as="style" href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&family=Inter:wght@400;500;600&family=Jura:wght@400;500;600;700&display=swap"><link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&family=Inter:wght@400;500;600&family=Jura:wght@400;500;600;700&display=swap" media="print" onload="this.media='all'"><noscript><link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&family=Inter:wght@400;500;600&family=Jura:wght@400;500;600;700&display=swap"></noscript>
	<style> body { font-family: 'Wix Madefor Display', Manrope, Inter, ui-sans-serif, system-ui, -apple-system, Segoe UI, Roboto, Helvetica, Arial; } </style>

	<title>Услуги — Производство, ремонт, доставка | ФАВОРИТ-СТРОЙ-СНАБ</title>
	<meta name="description" content="Все услуги ФАВОРИТ-СТРОЙ-СНАБ: БВЗ под ключ, капитальный ремонт, грузоперевозки, спецтехника, мебель, склады, навесы.">
	<meta name="author" content="ФАВОРИТ-СТРОЙ-СНАБ">
	<meta name="theme-color" content="#F97316">
	<link rel="preconnect" href="https://cdnjs.cloudflare.com" crossorigin>
	<link rel="preconnect" href="https://cdn.jsdelivr.net" crossorigin>
	<meta name="robots" content="index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1">
	<!-- Favicon unified -->
	<link rel="icon" type="image/png" href="/public/assets/image/logo/favicon-96x96.png" sizes="96x96" />
	<link rel="icon" type="image/svg+xml" href="/public/assets/image/logo/favicon.svg" />
	<link rel="shortcut icon" href="/public/assets/image/logo/favicon.ico" />
	<link rel="apple-touch-icon" sizes="180x180" href="/public/assets/image/logo/apple-touch-icon.png" />
	<meta name="apple-mobile-web-app-title" content="ФСС" />
	<link rel="manifest" href="/public/assets/image/logo/site.webmanifest" />

	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" media="print" onload="this.media='all'">
	<noscript><link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"></noscript>
	<link rel="stylesheet" href="/public/assets/distCSS/style.css?v=20261009">
	<script defer src="/public/assets/distJS/main.js"></script>

	<!-- canonical -->
	<?= seo_canonical_tags() ?>
	<?= seo_verify_tags() ?>

	<?= seo_og_tags('Услуги — Производство, ремонт, доставка | ФАВОРИТ-СТРОЙ-СНАБ', 'Все услуги ФАВОРИТ-СТРОЙ-СНАБ: БВЗ под ключ, капитальный ремонт, грузоперевозки, спецтехника, мебель, склады, навесы.', '/public/assets/image/og/og-uslugi-1200x630.jpg') ?>

	<!-- Swiper Slider for Banner -->
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
	<script defer="true" src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>

	<?php include_once __DIR__ . '/../../components/sheoma.php'; ?>

<?= seo_analytics() ?>

<?= seo_utm_script() ?>

<?= seo_goals_script() ?>
	<link rel="stylesheet" href="/public/assets/distCSS/tailwind.css?v=20261009">
<link rel="stylesheet" href="/public/assets/distCSS/premium.css?v=20261014">

<script src="https://cdn.tailwindcss.com"></script>
<style>
  :root { --mts-red: #F97316; --page-bg: #fff; --ink: #17191d; --muted: #6b7078; --soft: #f3f4f6; }
  * { box-sizing: border-box; }
  html { scroll-behavior: smooth; }
  body { margin: 0; background: var(--page-bg); color: var(--ink); font-family: Arial, Helvetica, sans-serif; -webkit-font-smoothing: antialiased; }
  .container { width: min(100% - 112px, 1280px); margin-inline: auto; padding-top: 96px; }
  .topline { min-height: 34px; border-bottom: 1px solid #f0f0f0; font-size: 11px; }
  .mainnav { min-height: 54px; border-bottom: 1px solid #f0f0f0; font-size: 12px; }
  .brand { color: var(--mts-red); font-weight: 900; letter-spacing: -1.6px; font-size: 22px; line-height: .8; }
  .brand small { display: block; font-size: 8px; letter-spacing: 0; text-align: right; margin-top: 4px; }
  .nav-link { white-space: nowrap; transition: color .2s; }
  .nav-link:hover { color: var(--mts-red); }
  .login { background: var(--mts-red); color: white; border-radius: 13px; padding: 11px 23px; font-weight: 700; }
  .catalog { border-radius: 13px; background: #f4f5f7; padding: 11px 17px; font-weight: 700; white-space: nowrap; }
  .page-title { font-size: clamp(36px, 5vw, 62px); line-height: 1.04; font-weight: 600; letter-spacing: -2.5px; margin: 40px 0 34px; }
  .search { width: 300px; background: #f5f6f8; border: 1px solid #e7e7ea; border-radius: 999px; padding: 11px 16px; color: #17191d; font-size: 13px; display: flex; align-items: center; gap: 10px; margin: 0; transition: border-color .2s, box-shadow .2s; }
  .search:focus-within { border-color: #F97316; background: #fff; box-shadow: 0 0 0 3px rgba(249,115,22,.12); }
  .search i { color: #9ca3af; font-size: 14px; }
  .search input { flex: 1; min-width: 0; border: 0; outline: none; background: transparent; font-family: inherit; font-size: 13px; color: var(--ink); }
  .search input::placeholder { color: #8b8e95; }
  .service-card.is-extra { display: none; }
  .services-grid.is-expanded .service-card.is-extra, .services-grid.is-searching .service-card.is-extra, .services-grid.is-filtering .service-card.is-extra { display: flex; }
  .services-grid.is-searching .service-card[data-match="0"], .services-grid.is-filtering .service-card[data-match="0"] { display: none; }
  .services-grid.is-expanded .service-card.is-extra { animation: uslCardIn .5s cubic-bezier(.16,1,.3,1) both; will-change: opacity, transform; }
  .services-grid.is-expanded .service-card.is-extra:nth-child(9) { animation-delay: 0.00s; }
  .services-grid.is-expanded .service-card.is-extra:nth-child(10) { animation-delay: 0.05s; }
  .services-grid.is-expanded .service-card.is-extra:nth-child(11) { animation-delay: 0.10s; }
  .services-grid.is-expanded .service-card.is-extra:nth-child(12) { animation-delay: 0.15s; }
  .services-grid.is-expanded .service-card.is-extra:nth-child(13) { animation-delay: 0.20s; }
  .services-grid.is-expanded .service-card.is-extra:nth-child(14) { animation-delay: 0.25s; }
  .services-grid.is-expanded .service-card.is-extra:nth-child(15) { animation-delay: 0.30s; }
  .services-grid.is-expanded .service-card.is-extra:nth-child(16) { animation-delay: 0.35s; }
  .services-grid.is-expanded .service-card.is-extra:nth-child(17) { animation-delay: 0.40s; }
  .services-grid.is-expanded .service-card.is-extra:nth-child(18) { animation-delay: 0.45s; }
  .services-grid.is-collapsing .service-card.is-extra { animation: uslCardOut .28s cubic-bezier(.4,0,1,1) forwards; }
  .services-grid.is-anim { overflow: hidden; }
  @keyframes uslCardIn { from { opacity: 0; transform: translateY(16px); } to { opacity: 1; transform: translateY(0); } }
  @keyframes uslCardOut { from { opacity: 1; transform: translateY(0); } to { opacity: 0; transform: translateY(10px); } }
  .usl-empty { display: none; text-align: center; color: #6b7078; font-size: 14px; padding: 40px 0 8px; }
  .usl-empty.show { display: block; }
  .category { background: #f4f5f7; border-radius: 15px; min-height: 58px; display: flex; align-items: center; gap: 12px; padding: 12px 14px; font-size: 12px; font-weight: 600; transition: background .2s, transform .2s; }
  button.category { border: 0; cursor: pointer; font-family: inherit; text-align: left; color: inherit; }
  .category.active { background: #17191d; color: #fff; }
  .category.active .category-icon { color: #F97316; }
  .category:hover { background: #eceef2; transform: translateY(-2px); }
  .category-icon { width: 34px; height: 34px; display: grid; place-items: center; flex: 0 0 34px; border-radius: 11px; color: #4274f4; background: linear-gradient(135deg, #e3eaff, #f7e8f4); font-size: 21px; }
  .services-grid { display: grid; grid-template-columns: repeat(4,minmax(0,1fr)); gap: 18px; }
  .service-card { min-height: 188px; padding: 21px 20px 15px; border-radius: 28px; box-shadow: 0 8px 22px rgba(30,43,67,.10); border: 1px solid rgba(240,242,246,.7); display: flex; flex-direction: column; background: #fff; }
  .service-card h2 { margin: 0 0 9px; font-size: 18px; line-height: 1.13; letter-spacing: -.45px; font-weight: 700; }
  .service-card p { margin: 0; color: #41454c; font-size: 12px; line-height: 1.42; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
  .price { margin-top: auto; padding-top: 15px; font-size: 16px; font-weight: 700; }
  .price small { font-size: 12px; font-weight: 400; }
  .old-price { color: #777; text-decoration: line-through; font-size: 12px; margin-left: 6px; }
  .card-actions { display: grid; gap: 7px; margin-top: 13px; }
  .action { min-height: 38px; border-radius: 13px; background: #f1f2f5; text-align: center; font-size: 11px; font-weight: 700; display: grid; place-items: center; padding: 8px; transition: background .2s; }
  .action:hover { background: #e5e7eb; }
  .action.primary { background: var(--mts-red); color: #fff; }
  .action.primary:hover { background: #EA580C; }
  .tag { display: inline-block; align-self: flex-start; background: var(--mts-red); color: #fff; font-size: 9px; padding: 4px 7px; border-radius: 5px; margin-bottom: 8px; }
  .show-all { display: block; width: min(250px,100%); margin: 38px auto 0; border-radius: 14px; background: #f3f4f6; padding: 13px 18px; font-size: 11px; font-weight: 700; text-align: center; }
  .archive { display: block; margin: 16px auto 0; text-align: center; font-size: 10px; font-weight: 700; letter-spacing: .2px; }
  footer { margin-top: 55px; background: #f2f3f6; padding: 42px 0 60px; color: #34373d; }
  .footer-top { display: flex; flex-wrap: wrap; gap: 25px; padding-bottom: 45px; border-bottom: 1px solid #e5e6ea; font-size: 12px; font-weight: 700; }
  .footer-grid { display: grid; grid-template-columns: 1.1fr 1.1fr 1.1fr 1fr; gap: 38px; padding-top: 34px; }
  .footer-col h3 { font-size: 16px; margin: 0 0 15px; font-weight: 700; }
  .footer-col a, .footer-col p { display: block; font-size: 12px; line-height: 1.55; color: #777b83; margin: 0 0 10px; }
  .store-row { display: flex; flex-wrap: wrap; gap: 8px; margin: 15px 0; }
  .store { color: #fff; background: #17191d; border-radius: 9px; font-size: 10px; padding: 9px 11px; }
  .qr { width: 120px; height: 120px; border-radius: 22px; background: white; padding: 12px; margin-top: 20px; }
  .qr svg { width: 100%; height: 100%; }
  @media (max-width: 1000px) {
    .container { width: min(100% - 48px, 900px); }
    .services-grid { grid-template-columns: repeat(3,minmax(0,1fr)); }
    .category-row { grid-template-columns: repeat(4,minmax(0,1fr)); }
    .topnav-secondary { display: none; }
  }
  @media (max-width: 700px) {
    .container { width: calc(100% - 32px); padding-top: 80px; }
    .topline { display: none; }
    .mainnav { min-height: 64px; }
    .nav-primary { gap: 12px; }
    .nav-primary .nav-link { display: none; }
    .page-title { font-size: 39px; letter-spacing: -1.5px; margin: 32px 0 25px; }
    .title-row { align-items: flex-start; flex-direction: column; gap: 15px; }
    .search { width: 100%; }
    .category-row { grid-template-columns: repeat(2,minmax(0,1fr)); gap: 9px; }
    .services-grid { grid-template-columns: repeat(2,minmax(0,1fr)); gap: 12px; }
    .service-card { min-height: 205px; padding: 17px 15px 13px; border-radius: 22px; }
    .service-card h2 { font-size: 16px; }
    .service-card p { font-size: 11px; }
    .price { font-size: 15px; }
    .action { font-size: 10px; min-height: 36px; }
    .footer-grid { grid-template-columns: repeat(2,minmax(0,1fr)); gap: 28px 18px; }
    .footer-top { gap: 15px 20px; }
  }
  @media (max-width: 420px) {
    .services-grid { grid-template-columns: 1fr; }
    .service-card { min-height: 180px; }
    .service-card h2 { font-size: 18px; }
    .service-card p { font-size: 12px; }
    .card-actions { grid-template-columns: 1fr 1fr; }
    .footer-grid { grid-template-columns: 1fr 1fr; }
  }
</style>


</head>

	<?php include_once 'public/components/header.php'; ?>
	<?= seo_breadcrumbs_html() ?>

	<!-- Hero (реф. ЮНО, sunset) -->
	


	<!-- Main Content -->
	<main class="container">
    <div class="title-row flex items-center justify-between gap-6">
      <h1 class="page-title">Все услуги компании</h1>
      <div class="search" role="search">
        <i class="fa-solid fa-magnifying-glass"></i>
        <input id="uslSearch" type="text" placeholder="Найти услугу" aria-label="Поиск услуги">
      </div>
    </div>

    <section aria-label="Категории услуг" class="category-row grid grid-cols-8 gap-3 mb-7">
      <button class="category" type="button" data-cat="modul" aria-pressed="false"><span class="category-icon"><i class="fa-solid fa-cubes"></i></span><span>Модульные<br>здания</span></button>
      <button class="category" type="button" data-cat="sklad" aria-pressed="false"><span class="category-icon"><i class="fa-solid fa-warehouse"></i></span><span>Склады<br>и ангары</span></button>
      <button class="category" type="button" data-cat="abk" aria-pressed="false"><span class="category-icon"><i class="fa-solid fa-building"></i></span><span>АБК<br>и офисы</span></button>
      <button class="category" type="button" data-cat="obch" aria-pressed="false"><span class="category-icon"><i class="fa-solid fa-building-user"></i></span><span>Общежития</span></button>
      <button class="category" type="button" data-cat="remont" aria-pressed="false"><span class="category-icon"><i class="fa-solid fa-screwdriver-wrench"></i></span><span>Капитальный<br>ремонт</span></button>
      <button class="category" type="button" data-cat="proekt" aria-pressed="false"><span class="category-icon"><i class="fa-solid fa-compass-drafting"></i></span><span>Проектирование</span></button>
      <button class="category" type="button" data-cat="dostavka" aria-pressed="false"><span class="category-icon"><i class="fa-solid fa-truck-fast"></i></span><span>Доставка<br>и техника</span></button>
      <button class="category" type="button" data-cat="popular" aria-pressed="false"><span class="category-icon">✦</span><span>Популярное</span></button>
    </section>

    <section id="services" class="services-grid" aria-label="Список услуг">
      <?php
      $featured = ['proizvodim_modul', 'angar', 'sclad', 'obchegitia', 'kapital', 'gruzoperevozki', 'spectehnika', 'proektirovanie', 'montach'];
      $meta = [
        'proizvodim_modul' => 'от|14 дней', 'angar' => 'от|14 дней', 'sclad' => 'по|смете', 'kapstroenie' => 'под|ключ',
        'obchegitia' => 'под|ключ', 'administrativnoe' => 'под|ключ', 'kapital' => 'по|смете', 'proektirovanie' => 'по|договору',
        'montach' => 'по|договору', 'generatornae' => 'по|смете', 'compressor' => 'по|смете', 'compressor_air' => 'по|смете',
        'gruzoperevozki' => 'по|заявке', 'spectehnika' => 'по|смене', 'musorbak' => 'по|договору', 'naves' => 'от|14 дней',
        'postavka' => 'по|договору', 'modultebel' => 'на|заказ',
      ];
      $catmap = ['proizvodim_modul' => 'modul', 'modultebel' => 'modul', 'musorbak' => 'modul', 'sclad' => 'sklad', 'angar' => 'sklad', 'naves' => 'sklad', 'administrativnoe' => 'abk', 'obchegitia' => 'obch', 'kapital' => 'remont', 'kapstroenie' => 'remont', 'montach' => 'remont', 'proektirovanie' => 'proekt', 'gruzoperevozki' => 'dostavka', 'spectehnika' => 'dostavka', 'postavka' => 'dostavka', 'generatornae' => 'dostavka', 'compressor' => 'dostavka', 'compressor_air' => 'dostavka'];
      $__i = 0;
      foreach (seo_services() as $slug => $svc):
        $feat = in_array($slug, $featured, true);
        [$pre, $val] = array_pad(explode('|', $meta[$slug] ?? 'по|договору', 2), 2, '');
      ?>
      <article class="service-card<?= ($__i >= 8) ? ' is-extra' : '' ?>" data-cat="<?= htmlspecialchars($catmap[$slug] ?? 'modul') ?>"<?= $feat ? ' data-feat="1"' : '' ?>>
        <h2><?= htmlspecialchars($svc['title']) ?></h2>
        <p><?= htmlspecialchars($svc['description']) ?></p>
        <div class="price"><small><?= htmlspecialchars($pre) ?></small> <?= htmlspecialchars($val) ?><?= ($slug === 'angar') ? ' <span class="old-price">30 дней</span>' : '' ?></div>
        <div class="card-actions">
          <?php if ($feat): ?><a class="action primary" href="/#contact" data-goal="uslugi_card_cta">Рассчитать</a><?php endif; ?>
          <a class="action" href="/services/<?= htmlspecialchars($slug) ?>">Подробнее</a>
        </div>
      </article>
      <?php $__i++; endforeach; ?>
    </section>

    <div class="usl-empty" id="uslEmpty">Ничего не найдено — измените запрос или <a href="/#contact" style="color:var(--mts-red)">свяжитесь с нами</a>.</div>
    <button class="show-all" id="showAll">ПОКАЗАТЬ ВСЕ</button>
    <a class="archive" href="/portfolio">Смотреть портфолио объектов</a>
  </main>

  <script>
    (function () {
      var grid = document.getElementById('services');
      var input = document.getElementById('uslSearch');
      var btn = document.getElementById('showAll');
      var empty = document.getElementById('uslEmpty');
      if (!grid) return;
      var cards = Array.prototype.slice.call(grid.querySelectorAll('.service-card'));
      var pills = Array.prototype.slice.call(document.querySelectorAll('.category-row .category'));
      var expanded = false;
      var activeCat = 'all';
      cards.forEach(function (c) {
        var h = c.querySelector('h2'), p = c.querySelector('p');
        c._t = ((h ? h.textContent : '') + ' ' + (p ? p.textContent : '')).toLowerCase();
        c.setAttribute('data-match', '1');
      });
      function render() {
        var q = (input && input.value ? input.value : '').trim().toLowerCase();
        var filtering = !!q || activeCat !== 'all';
        grid.classList.toggle('is-searching', !!q);
        grid.classList.toggle('is-filtering', filtering);
        var visible = 0;
        cards.forEach(function (c) {
          var matchQ = !q || c._t.indexOf(q) > -1;
          var matchC = activeCat === 'all' || (activeCat === 'popular' ? c.hasAttribute('data-feat') : c.getAttribute('data-cat') === activeCat);
          var match = matchQ && matchC;
          c.setAttribute('data-match', match ? '1' : '0');
          if (match) visible++;
        });
        if (empty) empty.classList.toggle('show', visible === 0);
        if (btn) btn.style.display = filtering ? 'none' : '';
      }
      if (input) input.addEventListener('input', render);
      pills.forEach(function (pill) {
        pill.addEventListener('click', function () {
          var c = pill.getAttribute('data-cat');
          activeCat = (activeCat === c) ? 'all' : c;
          pills.forEach(function (x) {
            var on = x.getAttribute('data-cat') === activeCat;
            x.classList.toggle('active', on);
            x.setAttribute('aria-pressed', on ? 'true' : 'false');
          });
          render();
          if (activeCat !== 'all') grid.scrollIntoView({ behavior: reduceMotion ? 'auto' : 'smooth', block: 'start' });
        });
      });
      var reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
      function animateHeight(from, to, dur, ease, done) {
        grid.style.height = from + 'px';
        grid.classList.add('is-anim');
        void grid.offsetHeight;
        grid.style.transition = 'height ' + dur + 'ms ' + ease;
        grid.style.height = to + 'px';
        setTimeout(function () {
          grid.style.height = '';
          grid.style.transition = '';
          grid.classList.remove('is-anim');
          if (done) done();
        }, dur + 60);
      }
      if (btn) btn.addEventListener('click', function () {
        if (!expanded) {
          grid.classList.remove('is-collapsing');
          if (reduceMotion) {
            grid.classList.add('is-expanded');
          } else {
            var h0 = grid.offsetHeight;
            grid.classList.add('is-expanded');
            animateHeight(h0, grid.offsetHeight, 550, 'cubic-bezier(.16,1,.3,1)');
          }
          this.textContent = 'СВЕРНУТЬ';
          expanded = true;
        } else {
          this.textContent = 'ПОКАЗАТЬ ВСЕ';
          expanded = false;
          if (reduceMotion) {
            grid.classList.remove('is-expanded', 'is-collapsing');
            return;
          }
          var h1 = grid.offsetHeight;
          grid.classList.remove('is-expanded');
          var h0c = grid.offsetHeight;
          grid.classList.add('is-expanded');
          grid.classList.add('is-collapsing');
          animateHeight(h1, h0c, 450, 'cubic-bezier(.4,0,.2,1)', function () {
            grid.classList.remove('is-expanded', 'is-collapsing');
          });
          if (btn.getBoundingClientRect().top < 0) btn.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
      });
    })();
  </script>
  
	<!-- footer content -->
	<?php include_once 'public/components/footer.php'; ?>
	<!-- форма обратной связи -->

	<?php include_once 'public/components/forma.php'; ?>

	</body>

</html>