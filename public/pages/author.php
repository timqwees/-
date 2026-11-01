<?php
require_once __DIR__ . '/../../../app/bootstrap.php';

$slug = fss_param('slug');
$authors = [
	'engineering' => [
		'name' => 'Инженерный отдел ФСС',
		'role' => 'Проектирование, изготовление и монтаж быстровозводимых зданий',
		'photo' => '/public/assets/image/logo/favicon/favicon.svg',
		'experience' => 'с 2011 года в быстровозводимом строительстве',
		'bio' => 'Инженерный отдел ООО «Фаворит-Строй-Снаб» занимается полным циклом создания быстровозводимых зданий: от расчёта конструкций и разработки рабочей документации до технадзора на площадке. За 14 лет на рынке отдел прошел путь от монтажа первых ангаров до комплексной поставки модульных общежитий площадью 9 000+ м² в Сочи и капремонта 16 социальных объектов в Чеченской Республике и Ростовской области.',
		'specialization' => ['БВЗ', 'Модульные здания', 'Сэндвич-панели', 'Фундаменты', 'Инженерные сети'],
		'projects' => ['Общежития в Сочи (9 000+ м²)', 'Склад 1 200 м² в Химках', 'Капремонт школы в Гудермесе'],
	],
	'estimate' => [
		'name' => 'Отдел смет и договоров ФСС',
		'role' => 'Расчёт стоимости, сметы и договоры по 44-ФЗ и 223-ФЗ',
		'photo' => '/public/assets/image/logo/favicon/favicon.svg',
		'experience' => 'сметы по выполненным объектам ФСС',
		'bio' => 'Специалисты отдела смет и договоров формируют прозрачные коммерческие предложения: смета за 15 минут, цена фиксируется в договоре и не растёт. Работают по 44-ФЗ и 223-ФЗ, сопровождают госзакупки от подачи заявки до подписания акта приема-передачи. Опыт — сметы на 30+ сданных объектах: склады, ангары, школы, административные здания.',
		'specialization' => ['Сметы', 'Договоры 44-ФЗ/223-ФЗ', 'Коммерческие предложения', 'Госзакупки'],
		'projects' => ['Капремонт 16 школ', 'Оснащение 13 школ по Минпросвещению', 'Административное здание полного цикла'],
	],
	'design' => [
		'name' => 'Проектный отдел ФСС',
		'role' => 'Конструктив, планировки и рабочая документация',
		'photo' => '/public/assets/image/logo/favicon/favicon.svg',
		'experience' => 'проектирование БВЗ и объектов капремонта',
		'bio' => 'Проектный отдел разрабатывает архитектурно-строительные решения под ключ: от концепции до стадии П/Р. Специализация — модульные здания из сэндвич-панелей, капитальный ремонт социальных объектов, комплексы инженерных систем. Проекты проходят государственную экспертизу и согласуются с заказчиками (Минпросвещение РФ, Росгвардия, администрации городов).',
		'specialization' => ['АР/КР', 'Планировки', 'Инженерные системы', 'Госекспертиза', 'ТИМ/BIM'],
		'projects' => ['Модульные общежития', 'Спортивные комплексы', 'Административные здания'],
	],
	'legal' => [
		'name' => 'Юридический отдел ФСС',
		'role' => 'Договоры, разрешения и лицензии, сопровождение 44-ФЗ и 223-ФЗ',
		'photo' => '/public/assets/image/logo/favicon/favicon.svg',
		'experience' => 'сопровождение контрактов по 44-ФЗ и 223-ФЗ',
		'bio' => 'Юридическое сопровождение проектов на всех этапах: от проверки земельного участка и получения разрешительной документации до закрытия объектов. Опыт работы с госзаказчиками (Росгвардия, Минпросвещение РФ, администрации городов), знание 44-ФЗ, 223-ФЗ, Градостроительного кодекса. Обеспечивают правовую чистоту сделок и защиту интересов заказчика.',
		'specialization' => ['44-ФЗ / 223-ФЗ', 'Разрешительная документация', 'Госзакупки', 'Страхование'],
		'projects' => ['Договоры с Росгвардией', 'Контракты Минпросвещения', 'Сопровождение 44-ФЗ'],
	],
	'tech' => [
		'name' => 'Технологический отдел ФСС',
		'role' => 'Технология производства, монтаж и технадзор на площадке',
		'photo' => '/public/assets/image/logo/favicon/favicon.svg',
		'experience' => 'производство и монтаж на собственном предприятии',
		'bio' => 'Технологический отдел управляет производством на собственном заводе БВЗ и монтажом на площадках по всей России. Контроль качества — на каждой стадии: входной контроль материалов, производственный контроль сварных швов и крепежа, приемо-сдаточные испытания. ВнедряютLEAN-технологии, сокращают цикл производства модуля до 14 дней.',
		'specialization' => ['Производство БВЗ', 'Монтаж', 'Технадзор', 'LEAN', 'Контроль качества'],
		'projects' => ['Завод БВЗ ФСС', 'Монтаж в Сочи, Гудермес, Аргун', 'Навес 100 машино-мест'],
	],
	'logistics' => [
		'name' => 'Логистика ФСС',
		'role' => 'Доставка конструкций, оборудования и техники по России',
		'photo' => '/public/assets/image/logo/favicon/favicon.svg',
		'experience' => 'логистика доставки на объекты в разных регионах',
		'bio' => 'Логистический отдел организует доставку модульных конструкций, спецтехники и оборудования с завода в любую точку РФ. Собственный автопарк (фуры, тягачи, манипуляторы) позволяет вести доставку «дверь в дверь» без перегрузок. Маршрутизация с учётом габаритов, весовых ограничений и дорожного режима регионов. Сроки доставки — в смету и договор.',
		'specialization' => ['Грузоперевозки', 'Негабарит', 'Маршрутизация', 'Свой автопарк'],
		'projects' => ['Доставка в Сочи (Краснодарский край)', 'Доставка в Гудермес (Чечня)', 'Доставка на Урал и в Сибирь'],
	],
];

$author = $authors[$slug] ?? null;
if (!$author) {
	\App\Models\Router\Routes::error_404('/author/' . $slug);
	return;
}

$h = fn($s) => htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8');
$base = rtrim((string)seo_config('base_url'), '/');
$authorUrl = $base . '/author/' . $slug;

$title = $author['name'] . ' — Автор блога ФСС';
$description = $author['role'] . '. ' . mb_substr($author['bio'], 0, 150) . '...';
?>
<!DOCTYPE html>
<html lang="ru">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title><?= $h($title) ?></title>
	<meta name="description" content="<?= $h($description) ?>">
	<meta name="robots" content="index, follow">
	<link rel="canonical" href="<?= $h($authorUrl) ?>">
	<meta property="og:title" content="<?= $h($title) ?>">
	<meta property="og:description" content="<?= $h($description) ?>">
	<meta property="og:type" content="profile">
	<meta property="og:url" content="<?= $h($authorUrl) ?>">
	<meta property="og:image" content="<?= $h($base . $author['photo']) ?>">
	<meta name="twitter:card" content="summary_large_image">
	<meta name="twitter:title" content="<?= $h($title) ?>">
	<meta name="twitter:description" content="<?= $h($description) ?>">
	<meta name="twitter:image" content="<?= $h($base . $author['photo']) ?>">
	<link rel="preload" href="/public/assets/distCSS/style.css?v=<?= seo_ver() ?>" as="style">
	<link rel="stylesheet" href="/public/assets/distCSS/style.css?v=<?= seo_ver() ?>">
	<link rel="stylesheet" href="/public/assets/vendor/fontawesome/all.min.css?v=20261024">
	<?= seo_analytics() ?>
	<?= seo_utm_script() ?>
	<?= seo_goals_script() ?>
	<style>
		.author-page { max-width: 800px; margin: 0 auto; padding: calc(var(--fss-header-h, 90px) + 40px) 24px 80px; }
		.author-header { display: flex; gap: 24px; align-items: flex-start; margin-bottom: 40px; }
		.author-avatar { width: 120px; height: 120px; border-radius: 50%; object-fit: cover; background: #f0e6da; flex-shrink: 0; }
		.author-info h1 { font-size: clamp(28px, 4vw, 40px); font-weight: 700; margin: 0 0 8px; color: #171310; }
		.author-role { color: #876b55; font-weight: 600; font-size: 16px; margin-bottom: 8px; }
		.author-experience { color: #78716c; font-size: 14px; }
		.author-bio { font-size: 16px; line-height: 1.75; color: #44403C; margin-bottom: 32px; }
		.author-specialization { display: flex; flex-wrap: wrap; gap: 8px; margin-bottom: 40px; }
		.author-tag { background: #f0e6da; color: #6d5744; padding: 6px 14px; border-radius: 999px; font-size: 13px; font-weight: 500; }
		.author-projects h2 { font-size: 22px; font-weight: 700; margin: 0 0 16px; color: #171310; }
		.author-projects ul { list-style: none; padding: 0; margin: 0; display: grid; gap: 10px; }
		.author-projects li { padding: 12px 16px; background: #fff; border: 1px solid #efe7da; border-radius: 12px; }
		@media (max-width: 600px) { .author-header { flex-direction: column; align-items: center; text-align: center; } .author-avatar { width: 100px; height: 100px; } }
	</style>
</head>
<body class="font-sans text-gray-800" data-page="inner">
	<?php include_once __DIR__ . '/../components/header.php'; ?>
	<main>
		<article class="author-page">
			<header class="author-header">
				<img src="<?= $h($author['photo']) ?>" alt="<?= $h($author['name']) ?>" class="author-avatar" loading="lazy" width="120" height="120">
				<div class="author-info">
					<h1><?= $h($author['name']) ?></h1>
					<div class="author-role"><?= $h($author['role']) ?></div>
					<div class="author-experience"><i class="fa-solid fa-medal" style="color:#876b55;margin-right:6px"></i>Опыт: <?= $h($author['experience']) ?></div>
				</div>
			</header>
			<div class="author-bio"><?= $h($author['bio']) ?></div>
			<div class="author-specialization" aria-label="Специализация">
				<?php foreach ($author['specialization'] as $spec): ?>
					<span class="author-tag"><?= $h($spec) ?></span>
				<?php endforeach; ?>
			</div>
			<section class="author-projects" aria-labelledby="projects-title">
				<h2 id="projects-title">Ключевые проекты</h2>
				<ul>
					<?php foreach ($author['projects'] as $proj): ?>
						<li><?= $h($proj) ?></li>
					<?php endforeach; ?>
				</ul>
			</section>
			<p style="margin-top:40px;color:#78716c;font-size:14px">Статьи автора: <a href="/blog" style="color:#876b55;text-decoration:underline">Блог ФСС</a></p>
		</article>
	</main>
	<?php include_once __DIR__ . '/../components/footer.php'; ?>
</body>
</html>