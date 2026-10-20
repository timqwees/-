<?php
/**
 * Контакты и заявка — по образцу ProMaintenance (enquiry-section--standalone).
 */

$site = Setting\Route\Functions\TheFunction::site();

$seo = Setting\Route\Functions\TheFunction::seo([
	'title' => 'Контакты и заявка на расчёт',
	'description' => 'Оставьте заявку — инженер перезвонит за 15 минут, уточнит задачу и пришлёт смету с фиксированной ценой. Звоните: ' . (string)seo_config('phone_display') . '.',
	'keywords' => 'заявка на расчёт, строительство под ключ, контакты ФСС',
	'image' => $site['shareImageUrl'],
	'url' => $site['baseUrl'] . '/contact',
	'type' => 'website',
	'pageType' => 'ContactPage',
	'breadcrumbs' => [
		['name' => 'Главная', 'url' => $site['baseUrl'] . '/'],
		['name' => 'Контакты', 'url' => $site['baseUrl'] . '/contact'],
	],
]);
?>
<!DOCTYPE html>
<html lang="ru">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">

	<title><?= htmlspecialchars($seo['title']); ?> | ФСС.РУС</title>
	<meta name="description" content="<?= htmlspecialchars($seo['description']); ?>">
	<meta name="robots" content="index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1">
	<meta name="author" content="ФАВОРИТ-СТРОЙ-СНАБ">
	<meta name="theme-color" content="#F97316">
	<meta name="format-detection" content="telephone=yes">
	<meta name="yandex-verification" content="27e0ba9980498150" />
	<link rel="canonical" href="<?= htmlspecialchars($seo['canonical']); ?>">

	<?= seo_og_tags($seo['title'], $seo['description'], $seo['og']['image']) ?>

	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<link rel="preload" as="style" href="https://fonts.googleapis.com/css2?family=DM+Sans+Variable:opsz,wght@9..40,100..1000&family=Manrope:wght@400;500;600;700;800&family=Inter:wght@400;500;600&family=Jura:wght@400;500;600;700&display=swap"><link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=DM+Sans+Variable:opsz,wght@9..40,100..1000&family=Manrope:wght@400;500;600;700;800&family=Inter:wght@400;500;600&family=Jura:wght@400;500;600;700&display=swap" media="print" onload="this.media='all'"><noscript><link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=DM+Sans+Variable:opsz,wght@9..40,100..1000&family=Manrope:wght@400;500;600;700;800&family=Inter:wght@400;500;600&family=Jura:wght@400;500;600;700&display=swap"></noscript>
	<style> body { font-family: 'DM Sans Variable', 'Manrope', system-ui, -apple-system, sans-serif; } </style>

	<!-- Preload critical -->
	<link rel="preload" href="/public/assets/distCSS/style.css?v=20261018" as="style">
	<!-- Styles -->
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" media="print" onload="this.media='all'">
	<noscript><link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"></noscript>
	<link rel="stylesheet" href="/public/assets/distCSS/style.css?v=20261018">
	<link rel="stylesheet" href="/public/assets/distCSS/tailwind.css?v=20261018">
	<link rel="stylesheet" href="/public/assets/distCSS/premium.css?v=20261018">
	<link rel="stylesheet" href="/public/assets/distCSS/protenance.css?v=20261018">

	<!-- Favicon unified -->
	<link rel="icon" type="image/png" href="/public/assets/image/logo/favicon/favicon-96x96.png" sizes="96x96" />
	<link rel="icon" type="image/svg+xml" href="/public/assets/image/logo/favicon/favicon.svg" />
	<link rel="shortcut icon" href="/public/assets/image/logo/favicon/favicon.ico" />
	<link rel="apple-touch-icon" sizes="180x180" href="/public/assets/image/logo/favicon/apple-touch-icon.png" />
	<link rel="manifest" href="/public/assets/image/logo/favicon/site.webmanifest" />

<?php include_once __DIR__ . '/../components/sheoma.php'; ?>
<?= seo_analytics() ?>
<?= seo_utm_script() ?>
<?= seo_goals_script() ?>

</head>

<body class="font-sans text-gray-800" data-page="inner">

	<div class="site-progress" aria-hidden="true"></div>

	<?php include_once __DIR__ . '/../components/header.php'; ?>

	<main>
		<!-- ENQUIRY — standalone (ProMaintenance enquiry-section--standalone) -->
		<section id="enquiry" class="enquiry-section enquiry-section--standalone">
			<div class="enquiry-lead">
				<p class="eyebrow" style="color: rgba(240,239,237,.62); display: flex; align-items: center;">
					<span class="live-dot" style="background: currentColor; border-radius: 50%; width: 6px; height: 6px; margin-right: 12px; display: inline-block;" aria-hidden="true"></span>
					Поговорим о вашем проекте
				</p>
				<h1 id="enquiry-title" style="margin-top: 35px; font-size: clamp(48px, 6.2vw, 102px); font-weight: 450; line-height: 1.03; letter-spacing: -.055em;">
					Рассчитаем стоимость<br>за 15 минут
				</h1>
				<p style="color: #b8b3ae; margin-top: 27px; font-size: 15px; line-height: 1.6;">
					Одна задача. Один разговор.<br>Расскажите, что у вас за проект.
				</p>
				<a class="enquiry-email" href="mailto:<?= htmlspecialchars((string)seo_config('email')) ?>"><?= htmlspecialchars((string)seo_config('email')) ?>
					<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
						<path d="M7 7h10v10"></path>
						<path d="M7 17 17 7"></path>
					</svg>
				</a>
				<a class="enquiry-phone" href="tel:<?= htmlspecialchars(preg_replace('/\D/', '', (string)seo_config('phone'))) ?>" style="color: #b8b3ae; margin-top: 12px; font-size: 13px; display: block;"><?= htmlspecialchars((string)seo_config('phone_display')) ?></a>
				<span class="enquiry-location" style="color: #aaa49e; margin-top: 45px; font-size: 10px; display: block;">
					<?= htmlspecialchars((string)seo_config('address')) ?><br>Работаем по Москве, Московской области и России
				</span>
			</div>

			<?php include_once __DIR__ . '/../components/enquiry_form.php'; ?>
		</section>
	</main>

	<?php include_once __DIR__ . '/../components/footer.php'; ?>

</body>

</html>
