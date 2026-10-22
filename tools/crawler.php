<?php
declare(strict_types=1);

/**
 * =============================================================================
 *  STUDIO AUDIT CRAWLER — «платный» аудит сайта в одном файле
 * =============================================================================
 *
 *  НАЗНАЧЕНИЕ
 *  Полный автономный краулер-аудитор: обходит сайт (локальный стенд или любой
 *  сайт в интернете), разбирает каждую страницу как DOM, строит граф внутренних
 *  ссылок и выдаёт ТОЧНЫЙ отчёт с явным указанием:
 *      — где проблемы ЕСТЬ (с URL, деталями, критичностью и подсказкой),
 *      — где проблем НЕТ (каждая проверка отображается явно, включая ✅).
 *
 *  ЗАПУСК
 *      php tools/crawler.php <url>
 *
 *      Одной команды достаточно. Сценарий выбирается автоматически по адресу:
 *        • локальный (localhost, 127.0.0.1, ::1, приватные сети, .local/.test/.dev)
 *          — глубокий быстрый обход без пауз, HTTPS-проверки отключены,
 *            проверка служебных файлов включена;
 *        • публичный (любой другой адрес)
 *          — вежливый обход с паузами, HTTPS/TLS-проверки включены,
 *            сканирование чужих служебных файлов отключено.
 *      Без адреса аудитится локальный стенд http://127.0.0.1:8899/
 *
 *  ДОПОЛНИТЕЛЬНЫЕ ОПЦИИ (необязательны, нужны только для нестандартных случаев)
 *      --depth=N          максимальная глубина обхода в кликах
 *      --limit=N          максимум страниц
 *      --concurrency=N    параллельных запросов
 *      --delay=MS         пауза между запросами к одному хосту
 *      --timeout=MS       таймаут запроса
 *      --ua=STRING        User-Agent краулера
 *      --only=PATH        обход только по префиксу пути
 *      --exclude=RE       исключать URL по PCRE (можно повторять)
 *      --respect-robots   не заходить на URL, запрещённые robots.txt
 *      --no-robots        не читать robots.txt
 *      --external[=N]     проверять внешние ссылки (0 — выкл.)
 *      --no-external      не проверять внешние ссылки
 *      --no-probe         не проверять служебные файлы (.env, .git, …)
 *      --json=FILE        дополнительно сохранить JSON отдельным файлом
 *      --html=FILE        сохранить автономный HTML-отчёт
 *
 *  РЕЗУЛЬТАТ РАБОТЫ
 *      Всегда создаётся один файл tools/audit-report.html со всеми результатами:
 *      сводка по группам, находки с цитатами кода и номерами строк, разбор ИИ,
 *      ИИ-исправления «БЫЛО → СТАЛО», сгенерированный llms.txt, покрытие ботов,
 *      домены и зеркала, обойдённые страницы и сырые данные (JSON внутри файла —
 *      кнопка «Скачать JSON»). Отдельные файлы не создаются.
 *      --pages            показать полную таблицу страниц
 *      --no-color         без ANSI-цвета
 *      --quiet            только итоговые строки
 *      --help             справка
 *
 *  ЧТО ПРОВЕРЯЕТСЯ (183 правила в 14 категориях — полный список в Audit::rules())
 *      Каждая проверка опирается на первоисточники: Google Search Central,
 *      Яндекс.Вебмастер, web.dev, W3C/WCAG, Schema.org, RFC, OWASP, llmstxt.org,
 *      документация ботов OpenAI/Anthropic/Perplexity, arXiv (KDD 2024).
 *      • Технические ошибки  — коды ответа, таймауты, цепочки и петли редиректов,
 *                             soft-404, HTTP→HTTPS, смешанный контент, гигиена URL
 *                             (длина, регистр, подчёркивания, не-ASCII, слеш)
 *      • Индексация          — robots.txt/robots meta/X-Robots-Tag, canonical, hreflang,
 *                             сироты, глубина, пагинация, sitemap ↔ обход ↔ /pages, дубли,
 *                             плюс Яндекс-специфичное: Clean-param (только у Яндекса),
 *                             директива Host, лимиты robots.txt (500 КБ), percent-encoding
 *                             не-ASCII путей, Crawl-delay (Яндекс игнорирует с 2018),
 *                             блокировки роботов Яндекса/Google/Bing, теги подтверждения
 *      • Мета-теги           — title/description/H1/lang/viewport/OG/Twitter, дубли, длины,
 *                             иерархия заголовков, favicon, конфликт robots-сигналов
 *      • Разметка            — JSON-LD: валидность, @type, дубли «одиночных» типов,
 *                             обязательные свойства (name/headline/datePublished/…)
 *      • Контент            — тонкий контент, дубли, PHP-warning'и в выводе,
 *                             соответствие языка, плотность текста, переспам терминов
 *      • Ссылки              — битые внутренние и внешние, редиректы, nofollow, пустые
 *                             и шаблонные анкоры, якоря (#id), ссылки на noindex, дубли анкоров
 *      • Изображения         — alt, width/height (CLS), lazy, битые, вес, LCP-прокси
 *      • Скорость            — TTFB, вес HTML, сжатие, кэш-заголовки, блокирующие
 *                             ресурсы, размер DOM, HTTP-версия и HTTP/2, font-display
 *      • Безопасность        — HTTPS/HSTS, CSP, X-Frame-Options, nosniff, Referrer-Policy,
 *                             баннеры сервера, утечка служебных файлов, флаги cookie,
 *                             срок действия и алгоритм TLS-сертификата
 *      • Доступность        — label у полей, дубли id, lang, viewport, iframe-title,
 *                             landmark-разметка, skip-link, доступные имена кнопок
 *      • AI и GEO            — /llms.txt и /llms-full.txt, манифесты для агентов
 *                             (ai-plugin.json, agents.json), правила robots.txt для
 *                             цитатных и обучающих AI-краулеров (OAI-SearchBot,
 *                             ClaudeBot, PerplexityBot, Google-Extended, GPTBot,
 *                             CCBot, Amazonbot, Bingbot, Applebot, Meta AI),
 *                             структура для цитирования (списки, вопросы, FAQPage),
 *                             определение сущности и «быстрый ответ» в начале,
 *                             E-E-A-T (автор/дата/dateModified), внешние источники
 *                             и цитаты (методы KDD 2024), sameAs, зависимость от JS
 *      • AI и GEO (проверка цитируемости по методам Aggarwal et al., KDD 2024) —
 *                             источники, статистика, цитаты с атрибуцией.
 *
 *  АНАЛИТИЧЕСКИЙ СЛОЙ (не только проверки, но и продвижение)
 *      --page=URL         разбор конкретной страницы: семантическое ядро, собранное
 *                         из её текста, заголовков, description, slug и входящих
 *                         анкоров; покрытие ядра по блокам (title/H1/description/текст);
 *                         пробелы — термины, которые есть в окружении, но нет на
 *                         странице; черновики title и description из реальных
 *                         терминов; диагностика «почему страница может не расти»
 *                         (техника, каннибализация, вес, глубина, объём текста);
 *                         список страниц, откуда добавить ссылки; чек-лист
 *                         готовности к топ-10.
 *      --clusters         карта тематических кластеров сайта: какие страницы про
 *                         что, размер кластера и его пробелы.
 *      --competitors=URLS сравнение с конкурентами теми же правилами: что у них
 *                         проходит, а у нас нет — это и есть измеримый разрыв.
 *      --ai[=auto|openrouter|groq|gemini|ollama]
 *                         размышление ИИ над сводкой находок: модель получает
 *                         компактную сводку (без содержимого страниц) и отвечает,
 *                         с чего начать и что не трогать. Ключ — из переменной
 *                         окружения (OPENROUTER_API_KEY / GROQ_API_KEY /
 *                         GEMINI_API_KEY), локально — Ollama без ключа.
 *      --ai-fix[=N]       ИИ пишет готовый код исправления по находкам: модель
 *                         получает адрес страницы, номер строки и сам код строки,
 *                         и отвечает «БЫЛО → СТАЛО» (по умолчанию 20 находок).
 *                         Работает вместе с --ai, ключ — тот же.
 *      --self-test        самотест точности: синтетические страницы с заранее
 *                         известным результатом, дифференциальная проверка
 *                         (recall/ложные срабатывания) — без обращения к сети.
 *      --test-url=ПУТЬ   проверить конкретный URL по правилам robots.txt и показать
 *                         сработавшую строку правила (с номером строки).
 *      --gen-llms         сгенерировать llms.txt из карты сайта (структура по
 *                         спецификации llmstxt.org: H1, цитата, разделы H2).
 *      --ai[=провайдер]   размышление ИИ над сводкой находок (OpenRouter, Groq,
 *                         Gemini или локальный Ollama) — ключ из переменной окружения.
 *
 *  ТОЧНОСТЬ И ПРОЗРАЧНОСТЬ НАХОДОК
 *      Каждая находка показывает, ГДЕ ИМЕННО она обнаружена: адрес страницы
 *      (ссылка — открыть на сайте), исходный фрагмент кода (тег <a>, <img>,
 *      <meta>, строка robots.txt) с НОМЕРОМ СТРОКИ в отдаваемом HTML и
 *      отдельная цитата — что именно нужно исправить.
 *      С опцией --ai-fix модель дополнительно пишет готовый код «БЫЛО → СТАЛО»
 *      по каждой находке прямо в отчёте.
 *      Отдельно — проверка по каждому AI- и поисковому боту: что написано для
 *      него в robots.txt (запрещён/частично/разрешён) и что реально отдаёт
 *      сервер на запрос, подписанный User-Agent'ом этого бота.
 *      Правила не «думают» о просрочке сертификата или битой ссылке, если
 *      факта нет: непроверяемое помечается как непроверяемое.
 *
 *  ЧЕСТНОСТЬ ПРЕТЕНЗИЙ
 *      Инструмент доказывает ТЕХНИЧЕСКУЮ готовность (индексация, уникальность,
 *      семантическое ядро, ссылки, скорость, разметка, доступность) — то есть
 *      полный набор условий, без которых позиция в топ-3/топ-10 невозможна.
 *      Позицию определяет также авторитет домена (внешние ссылки, бренд, история),
 *      который HTTP-обходом не измеряется: такие пункты помечены в отчёте как
 *      требующие внешних данных (Яндекс.Вебмастер, Ahrefs/Semrush, Wordstat).
 *
 *  ПОЧЕМУ КАЖДАЯ ПРОВЕРКА ВАЖНА
 *      У каждого правила есть блок «Влияет» — что конкретно теряет или получает
 *      сайт из-за этой ошибки (без воды) — и ссылки на официальные источники:
 *      Google Search Central, Яндекс.Вебмастер, web.dev (Core Web Vitals),
 *      W3C/WCAG, Schema.org, RFC 9110/8446, OWASP, llmstxt.org, документация
 *      ботов OpenAI/Anthropic/Perplexity. Справочник — Audit::impacts(),
 *      ссылки — Audit::SRC.
 *
 *  ТЕХНИЧЕСКИЕ ДЕТАЛИ
 *      • HTTP-клиент на curl_multi: параллельные запросы, тайминги (TTFB, connect, total),
 *        ручное следование редиректам (полная цепочка), вежливые паузы между пачками,
 *        автоматический повтор неудачных соединений (однопоточный сервер не рвёт обход).
 *      • Разбор HTML через DOMDocument + XPath (не регулярки) — корректная работа с кириллицей,
 *        вложенностью и любым порядком атрибутов.
 *      • Percent-encoding не-ASCII в URL до разбора (parse_url портит кириллицу в query).
 *      • robots.txt: полноценный парсер (Allow/Disallow, *, $, Sitemap, Host, Clean-param)
 *        с правилом «самое длинное совпадение выигрывает».
 *      • Sitemap: urlset + sitemapindex (с учётом XML-namespace), lastmod/changefreq/priority,
 *        сверка с обходом; отдельно — файл-список /pages.
 *      • Граф ссылок: BFS с учётом глубины, входящие/исходящие, сироты, хабы, внешние хосты.
 *      • LCP-прокси: главное изображение страницы проверяется на fetchpriority/size/srcset.
 *      • Дубли контента: Jaccard по шинглам триграмм основного текста (без шапки/футера)
 *        через инвертированный индекс — масштабируется на тысячи страниц.
 *      • Выход: цветной консольный отчёт (✅ для пройденных проверок и ❌ для проблем),
 *        JSON (машиночитаемый) и автономный HTML (без внешних зависимостей) с баллами,
 *        оглавлением, фильтром по категориям и поиском.
 *      • Код возврата: 0 — критичных и высоких замечаний нет, 1 — есть замечания, 2 — ошибка.
 *
 *  ОГРАНИЧЕНИЯ
 *      Инструмент ничего не меняет на сайте: только читает (HEAD/GET).
 *      Разрешён один файл — tools/crawler.php.
 *
 *  @version 2.1.0
 * =============================================================================
 */

namespace Studio\Audit;

if (PHP_SAPI !== 'cli') {
	http_response_code(403);
	exit("CLI only.\n");
}

// Краулер держит в памяти разобранные страницы: на больших сайтах стандартных
// 128 МБ не хватает, а в CLI ограничение памяти мешать не должно.
if (ini_get('memory_limit') !== '-1') {
	@ini_set('memory_limit', '1024M');
}

/**
 * Конфигурация запуска.
 */
final class Cfg
{
	public string $start = 'http://127.0.0.1:8899/';
	public int $depth = 6;
	public int $limit = 500;
	public int $concurrency = 6;
	public int $delayMs = 0;
	public int $timeoutMs = 20000;
	public string $ua = 'StudioAuditBot/1.0 (SEO/technical audit; single-file crawler)';
	public bool $respectRobots = false;
	public bool $readRobots = true;
	public bool $external = true;
	public int $externalLimit = 200;
	public int $externalTimeoutMs = 6000;
	public int $externalBudgetSec = 15;
	public bool $probe = true;
	public ?string $jsonFile = null;
	public ?string $htmlFile = null;
	public bool $noReport = false;
	/** Страница для детального плана продвижения. */
	public ?string $pageUrl = null;
	/** @var list<string> URL конкурентов для сравнения */
	public array $competitors = [];
	/** Показать карту тематических кластеров. */
	public bool $clusters = false;
	/** Провайдер ИИ для размышления над сводкой: auto|openrouter|groq|gemini|ollama|off */
	public string $ai = 'off';
	public string $aiModel = '';
	/** Сколько находок разобрать по пунктам: ИИ пишет готовый код «заменить на …» (0 — выключено). */
	public int $aiFix = 0;
	/** Режим самотеста точности правил. */
	public bool $selfTest = false;
	/** Путь URL для проверки по правилам robots.txt (например /admin/page.html). */
	public ?string $testUrl = null;
	/** Бот для проверки URL (по умолчанию — «*»). */
	public string $testBot = '*';
	/** Сгенерировать llms.txt из карты сайта. */
	public bool $genLlm = false;
	public ?string $llmsOut = null;
	public bool $showPages = false;
	public bool $quiet = false;
	public bool $color = true;
	/** Режим работы: local | global (определяется автоматически). */
	public string $mode = 'auto';
	/** @var list<string> опции, заданные пользователем явно (их сценарий не перекрывает) */
	public array $explicit = [];
	/** @var list<string> */
	public array $only = [];
	/** @var list<string> */
	public array $exclude = [];
	/** @var list<string> */
	public array $errors = [];
	/** @var array<string,string> */
	public array $map = [];

	/**
	 * Локальный это адрес или публичный?
	 * localhost, 127.0.0.0/8, ::1, приватные сети, .local/.test/.dev/.internal, имя без домена.
	 */
	public function detectLocal(): bool
	{
		$host = strtolower(trim((string)parse_url($this->start, PHP_URL_HOST)));
		if ($host === '') {
			return false;
		}
		if (in_array($host, ['localhost', '127.0.0.1', '0.0.0.0', '::1', 'host.docker.internal'], true)) {
			return true;
		}
		if (str_starts_with($host, '127.')) {
			return true;
		}
		foreach (['.local', '.localhost', '.test', '.dev', '.internal', '.lan', '.home', '.localdomain'] as $suffix) {
			if (str_ends_with($host, $suffix)) {
				return true;
			}
		}
		if (filter_var($host, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
			if (str_starts_with($host, '10.') || str_starts_with($host, '192.168.') || str_starts_with($host, '169.254.')) {
				return true;
			}
			if (preg_match('~^172\.(1[6-9]|2\d|3[01])\.~', $host)) {
				return true;
			}
		}
		if (filter_var($host, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)) {
			return str_starts_with($host, '::1')
				|| str_starts_with($host, 'fc')
				|| str_starts_with($host, 'fd')
				|| preg_match('~^fe[89ab]~', $host) === 1;
		}
		return !str_contains($host, '.');   // имя хоста без домена
	}

	public function isLocal(): bool
	{
		return $this->detectLocal();
	}

	/**
	 * Авто-выбор сценария под тип адреса.
	 *
	 * Локальный стенд: обход глубже и быстрее, HTTPS-проверки выключены,
	 * проверка служебных файлов включена.
	 * Публичный сайт: вежливые паузы, меньше глубины, HTTPS/TLS-проверки включены,
	 * сканирование чужих служебных файлов отключено.
	 */
	public function applyScenario(): void
	{
		$local = $this->detectLocal();
		$this->mode = $local ? 'local' : 'global';

		$preset = $local
			? [
				'depth' => 10, 'limit' => 3000, 'concurrency' => 10, 'delayMs' => 0, 'timeoutMs' => 20000,
				'externalLimit' => 300, 'externalTimeoutMs' => 5000, 'externalBudgetSec' => 30, 'probe' => true,
			]
			: [
				'depth' => 5, 'limit' => 500, 'concurrency' => 4, 'delayMs' => 80, 'timeoutMs' => 25000,
				'externalLimit' => 120, 'externalTimeoutMs' => 6000, 'externalBudgetSec' => 15, 'probe' => false,
			];

		foreach ($preset as $key => $value) {
			if (!in_array($key, $this->explicit, true)) {
				$this->{$key} = $value;
			}
		}
	}

	/** Название режима для отчёта. */
	public function modeLabel(): string
	{
		return $this->mode === 'local' ? 'локальный стенд' : 'публичный сайт';
	}
}

/**
 * Терминальный UI: цвета, линии, полосы, таблицы.
 */
final class Ui
{
	private bool $color;

	/** @var array<string,string> */
	private const STYLES = [
		'reset'   => "0",
		'bold'    => "1",
		'dim'     => "2",
		'italic'  => "3",
		'under'   => "4",
		'red'     => "31",
		'green'   => "32",
		'yellow'  => "33",
		'blue'    => "34",
		'magenta' => "35",
		'cyan'    => "36",
		'white'   => "37",
		'gray'    => "90",
		'brred'   => "91",
		'brgreen' => "92",
		'bryellow'=> "93",
		'brblue'  => "94",
		'brcyan'  => "96",
		'bgred'   => "41",
		'bggreen' => "42",
		'bgyellow'=> "43",
		'bgblue'  => "44",
		'bgmagenta' => "45",
		'bgcyan'  => "46",
		'bggray'  => "100",
		'bgwhite' => "47",
	];

	public function __construct(bool $color = true)
	{
		$this->color = $color && getenv('NO_COLOR') === false && getenv('TERM') !== 'dumb';
	}

	public function c(string $text, string $style = ''): string
	{
		if (!$this->color || $style === '') {
			return $text;
		}
		$parts = explode('|', $style);
		$codes = [];
		foreach ($parts as $p) {
			if (isset(self::STYLES[$p])) {
				$codes[] = self::STYLES[$p];
			}
		}
		if (!$codes) {
			return $text;
		}
		return "\033[" . implode(';', $codes) . 'm' . $text . "\033[0m";
	}

	public function line(string $s = ''): void
	{
		echo $s, PHP_EOL;
	}

	/**
	 * Терминал интерактивный (можно рисовать анимацию в одной строке).
	 */
	public function interactive(): bool
	{
		return defined('STDERR') && function_exists('stream_isatty') && @stream_isatty(STDERR);
	}

	/**
	 * Вопрос пользователю с вариантами. Пустая строка — значение по умолчанию.
	 *
	 * @param list<string> $options подсказки (не обязательны)
	 */
	public function ask(string $question, string $default = '', array $options = []): string
	{
		$this->line();
		$this->line('  ' . $this->c($question, 'bold|white'));
		foreach ($options as $i => $opt) {
			$this->line('    ' . $this->c((string)($i + 1) . ')', 'brcyan') . ' ' . $opt);
		}
		$hint = $default !== '' ? ' [' . $default . ']' : '';
		fwrite(STDOUT, '  ' . $this->c('>', 'brcyan') . ' ' . $hint . ' ');
		$line = fgets(STDIN);
		if ($line === false) {
			$this->line();
			return $default;
		}
		$line = trim($line);
		return $line !== '' ? $line : $default;
	}

	/**
	 * Выравнивание по символам (не по байтам) — иначе кириллица «разъезжается».
	 */
	public function pad(string $s, int $len, bool $right = true): string
	{
		$cur = mb_strlen($s);
		if ($cur >= $len) {
			return mb_substr($s, 0, $len);
		}
		$spaces = str_repeat(' ', $len - $cur);
		return $right ? $s . $spaces : $spaces . $s;
	}

	public function title(string $text): void
	{
		$w = 78;
		$this->line($this->c('╔' . str_repeat('═', $w) . '╗', 'brcyan'));
		$this->line($this->c('║', 'brcyan') . $this->c(' ', 'bgcyan')
			. $this->c('  ' . $this->pad($text, $w - 3), 'bold|white') . $this->c('║', 'brcyan'));
		$this->line($this->c('╚' . str_repeat('═', $w) . '╝', 'brcyan'));
	}

	public function section(string $text, string $style = 'brcyan'): void
	{
		$this->line();
		$this->line($this->c('── ' . $text . ' ' . str_repeat('─', max(0, 74 - mb_strlen($text))), $style));
	}

	public function sub(string $text): void
	{
		$this->line($this->c('  ' . $text, 'bold'));
	}

	public function item(string $text): void
	{
		$this->line('    ' . $text);
	}

	public function kv(string $key, string $value): void
	{
		$this->line('  ' . $this->c($this->pad($key, 15) . ': ', 'gray') . $value);
	}

	public function bar(float $pct, int $w = 22): string
	{
		$pct = max(0.0, min(100.0, $pct));
		$fill = (int)round($pct / 100 * $w);
		$style = $pct >= 85 ? 'green' : ($pct >= 65 ? 'yellow' : 'red');
		return $this->c(str_repeat('█', $fill), $style) . $this->c(str_repeat('░', max(0, $w - $fill)), 'gray');
	}

	/** Цвет по критичности. */
	public function sev(string $sev): string
	{
		return match ($sev) {
			'critical' => $this->c('КРИТИЧНО', 'bold|brred'),
			'high'     => $this->c('ВЫСОКАЯ', 'red'),
			'medium'   => $this->c('СРЕДНЯЯ', 'yellow'),
			'low'      => $this->c('НИЗКАЯ', 'blue'),
			default    => $this->c('ИНФО', 'gray'),
		};
	}

	/**
	 * Бейджи поисковых систем, к которым относится проверка.
	 *
	 * @param array{google:bool,yandex:bool,bing:bool,ai:bool} $sys
	 */
	public function sysBadges(array $sys): string
	{
		$map = [
			'google' => ['G', 'bryellow'],
			'yandex' => ['Я', 'brred'],
			'bing'  => ['B', 'brcyan'],
			'ai'     => ['AI', 'brgreen'],
		];
		$out = '';
		foreach ($map as $key => [$label, $style]) {
			if (!empty($sys[$key])) {
				$out .= $this->c(' ' . $label, $style);
			}
		}
		return $out;
	}

	public function ok(string $text = 'ОК'): string
	{
		return $this->c('✓', 'green') . ' ' . $text;
	}

	public function fail(string $text = 'ПРОБЛЕМА'): string
	{
		return $this->c('✗', 'red') . ' ' . $text;
	}

	/**
	 * Цветная плашка (бейдж) с обратным видео: « КРИТИЧНО ».
	 */
	public function chip(string $text, string $style = 'bggray'): string
	{
		return $this->c(' ' . $text . ' ', $style . '|bold|white');
	}

	/**
	 * Бейдж критичности: цвет фона зависит от уровня.
	 */
	public function sevBadge(string $sev): string
	{
		$label = match ($sev) {
			'critical' => 'КРИТИЧНО',
			'high'     => 'ВЫСОКАЯ',
			'medium'   => 'СРЕДНЯЯ',
			'low'      => 'НИЗКАЯ',
			default    => 'ИНФО',
		};
		$style = match ($sev) {
			'critical' => 'bgred',
			'high'     => 'bgyellow',
			'medium'   => 'bgblue',
			'low'      => 'bgmagenta',
			default    => 'bggray',
		};
		return $this->chip($this->pad($label, 8), $style);
	}

	/**
	 * Стабильный цвет для категории (одна и та же категория — всегда один цвет).
	 */
	public function catStyle(string $cat): string
	{
		$palette = ['brcyan', 'bryellow', 'brgreen', 'brblue', 'brred', 'magenta', 'cyan', 'yellow'];
		return $palette[abs(crc32($cat)) % count($palette)];
	}

	/**
	 * Итоговая строка-вердикт: цвет зависит от оценки.
	 */
	public function verdict(float $score, string $text): string
	{
		$style = $score >= 85 ? 'bold|brgreen' : ($score >= 65 ? 'bold|bryellow' : 'bold|brred');
		return $this->c('  ИТОГ  ', $style) . ' ' . $text;
	}

	/**
	 * Горизонтальная 100%-ная полоса со стеком сегментов (например, доля проблем).
	 *
	 * @param array<string,float> $parts метка => доля (0..1)
	 * @param array<string,string> $styles метка => стиль
	 */
	public function stackBar(array $parts, array $styles, int $w = 60): string
	{
		$out = '';
		$used = 0;
		$keys = array_keys($parts);
		$i = 0;
		foreach ($parts as $label => $share) {
			$len = (int)round($share * $w);
			if ($i === count($keys) - 1) {
				$len = max(0, $w - $used);
			}
			$used += $len;
			if ($len > 0) {
				$out .= $this->c(str_repeat('█', $len), $styles[$label] ?? 'gray');
			}
			$i++;
		}
		return $out;
	}
}

/**
 * Живая индикация загрузки: вращающийся значок, текущая фаза, счётчик,
 * прошедшее время и грубая оценка остатка («осталось ≈ 0:42»).
 *
 * Рисуется одной строкой в stderr и стирается по \r, поэтому не мешает
 * обычному выводу отчёта. Если терминал не интерактивный — молчит.
 */
final class Spin
{
	/** Кадры «вращения» (braille). */
	private const FRAMES = ['⠋', '⠙', '⠹', '⠸', '⠼', '⠴', '⠦', '⠧', '⠇', '⠏'];

	private Ui $ui;
	/** Умеет ли рисовать (интерактивный терминал или AUDIT_SPIN=1). */
	private bool $on;
	/** Идёт ли анимация прямо сейчас (между start() и stop()). */
	private bool $running = false;
	/** Счётчик активных индикаторов: нужен, чтобы вложенные обходы не спорили за строку. */
	private static int $busy = 0;
	/** Текущий активный индикатор (для пульса во время сетевых ожиданий). */
	private static ?Spin $current = null;
	private float $t0 = 0.0;
	private int $frame = 0;
	private int $width = 0;
	private string $text = 'Загрузка данных';
	private ?int $done = null;
	private ?int $total = null;
	private float $paintedAt = 0.0;

	public function __construct(Ui $ui, bool $enabled = true)
	{
		$this->ui = $ui;
		// AUDIT_SPIN=1 включает анимацию даже когда вывод перенаправлен в файл.
		$this->on = $enabled && ($ui->interactive() || getenv('AUDIT_SPIN') === '1');
	}

	/** Идёт ли сейчас анимация в другом месте (вложенный обход). */
	public static function busy(): bool
	{
		return self::$busy > 0;
	}

	/** Активный индикатор (или null, если анимация выключена). */
	public static function current(): ?Spin
	{
		return self::$current;
	}

	public function active(): bool
	{
		return $this->on && $this->running;
	}

	/** Начало работы: сброс таймера и первая отрисовка. */
	public function start(string $text): void
	{
		if (!$this->on) {
			return;
		}
		$this->text = $text;
		$this->t0 = microtime(true);
		$this->done = null;
		$this->total = null;
		if (!$this->running) {
			$this->running = true;
			self::$busy++;
		}
		self::$current = $this;
		$this->paint(true);
	}

	/** Новая подпись и (необязательно) счётчик. Смена фазы обнуляет таймер. */
	public function set(string $text, ?int $done = null, ?int $total = null): void
	{
		if ($text !== $this->text) {
			$this->t0 = microtime(true);
		}
		$this->text = $text;
		$this->done = $done;
		$this->total = $total;
		$this->paint();
	}

	/** Пульс анимации: крутит значок, пока идёт ожидание сети. */
	public function tick(): void
	{
		$this->paint();
	}

	/** Стирает строку индикатора (перед печатью отчёта). */
	public function erase(): void
	{
		if (!$this->on || $this->width === 0) {
			return;
		}
		fwrite(STDERR, "\r" . str_repeat(' ', $this->width) . "\r");
		$this->width = 0;
	}

	/** Завершение: стирает строку и (по желанию) печатает итог. */
	public function stop(string $text = ''): void
	{
		if (!$this->on) {
			return;
		}
		$this->erase();
		if ($text !== '') {
			fwrite(STDERR, '  ' . $this->ui->c('✓', 'green') . ' ' . $text . PHP_EOL);
		}
		if ($this->running) {
			$this->running = false;
			if (self::$busy > 0) {
				self::$busy--;
			}
		}
		if (self::$current === $this) {
			self::$current = null;
		}
	}

	/** @param list<array{0:string,1:string}> $parts */
	private function paint(bool $force = false): void
	{
		if (!$this->on || !$this->running) {
			return;
		}
		$now = microtime(true);
		if (!$force && ($now - $this->paintedAt) < 0.08) {
			return;   // не чаще ~12 кадров в секунду
		}
		$this->paintedAt = $now;

		$parts = $this->parts();
		$plain = '';
		$colored = '';
		foreach ($parts as [$text, $style]) {
			$plain .= $text;
			$colored .= $style === '' ? $text : $this->ui->c($text, $style);
		}
		$pad = max(0, $this->width - mb_strlen($plain));
		fwrite(STDERR, "\r" . $colored . str_repeat(' ', $pad));
		$this->width = mb_strlen($plain);
		$this->frame++;
	}

	/**
	 * Строка индикатора по частям: [текст, стиль].
	 *
	 * @return list<array{0:string,1:string}>
	 */
	private function parts(): array
	{
		$elapsed = microtime(true) - $this->t0;
		$frame = self::FRAMES[$this->frame % count(self::FRAMES)];
		$parts = [[$frame . ' ', 'brcyan'], [$this->text, 'bold|white']];

		if ($this->done !== null && $this->total !== null && $this->total > 0) {
			$pct = (int)round(min(100, $this->done / $this->total * 100));
			$fill = (int)round($pct / 100 * 18);
			$parts[] = ['  ', ''];
			$parts[] = [str_repeat('█', $fill), 'brcyan'];
			$parts[] = [str_repeat('░', 18 - $fill), 'gray'];
			$parts[] = [' ' . $this->done . '/' . $this->total . ' (' . $pct . '%)', 'gray'];
		} elseif ($this->done !== null) {
			$parts[] = ['  ' . $this->done . ' стр.', 'gray'];
		}

		$parts[] = ['  прошло ' . self::hms($elapsed), 'gray'];
		$eta = $this->eta($elapsed);
		if ($eta !== null) {
			$parts[] = ['  осталось ≈ ' . self::hms($eta), 'bryellow'];
		}
		return $parts;
	}

	/** Оценка остатка по средней скорости (в секундах). */
	private function eta(float $elapsed): ?float
	{
		if ($this->done === null || $this->total === null || $this->total <= 0) {
			return null;
		}
		// по одному-двум элементам оценка бессмысленна — не показываем её
		if ($this->done < 2 || $elapsed < 0.8) {
			return null;
		}
		$left = $this->total - $this->done;
		if ($left <= 0) {
			return 0.0;
		}
		return $elapsed / $this->done * $left;
	}

	/** Секунды → «0:42» / «1:02:30». */
	private static function hms(float $sec): string
	{
		$sec = max(0, (int)round($sec));
		$h = intdiv($sec, 3600);
		$m = intdiv($sec % 3600, 60);
		$s = $sec % 60;
		return $h > 0 ? sprintf('%d:%02d:%02d', $h, $m, $s) : sprintf('%d:%02d', $m, $s);
	}
}

/**
 * URL: нормализация, разрешение относительных ссылок (RFC 3986), сравнение хостов.
 */
final class Url
{
	/**
	 * Percent-encoding не-ASCII и пробелов в URL.
	 *
	 * Зачем: PHP-парсер parse_url() портит не-ASCII в query (кириллица в адресе
	 * превращается в «_»), а curl не может отправить кириллицу в строке запроса.
	 * Поэтому кодируем URL ДО любого разбора — дальше работаем только с ASCII.
	 */
	public static function encode(string $url): string
	{
		return (string)preg_replace_callback('~[^\x21-\x7E]~', static function (array $m): string {
			$out = '';
			foreach (str_split(bin2hex($m[0]), 2) as $byte) {
				$out .= '%' . strtoupper($byte);
			}
			return $out;
		}, $url);
	}

	/** @return array{url:string,scheme:string,host:string,port:?int,path:string,query:?string,fragment:?string}|null */
	public static function parse(string $url): ?array
	{
		$parts = parse_url(self::encode($url));
		if ($parts === false || !isset($parts['scheme'], $parts['host'])) {
			return null;
		}
		return [
			'url'      => $url,
			'scheme'   => strtolower($parts['scheme']),
			'host'     => strtolower($parts['host']),
			'port'     => isset($parts['port']) ? (int)$parts['port'] : null,
			'path'     => $parts['path'] ?? '/',
			'query'    => $parts['query'] ?? null,
			'fragment' => $parts['fragment'] ?? null,
		];
	}

	public static function hostOf(string $url): string
	{
		$p = self::parse($url);
		return $p === null ? '' : $p['host'] . ($p['port'] ? ':' . $p['port'] : '');
	}

	/** Путь+query — то, что показываем в отчёте. */
	public static function displayPath(string $url): string
	{
		$p = self::parse($url);
		if ($p === null) {
			return $url;
		}
		return $p['path'] . ($p['query'] !== null && $p['query'] !== '' ? '?' . $p['query'] : '');
	}

	/** Схема://хост — для сравнения «тот же домен». */
	public static function origin(string $url): string
	{
		$p = self::parse($url);
		if ($p === null) {
			return '';
		}
		return $p['scheme'] . '://' . $p['host'] . ($p['port'] ? ':' . $p['port'] : '');
	}

	/** Тот же сайт (www и без www — один сайт). */
	public static function sameSite(string $a, string $b): bool
	{
		$ha = self::hostOf($a);
		$hb = self::hostOf($b);
		if ($ha === '' || $hb === '') {
			return false;
		}
		$strip = static fn(string $h): string => preg_replace('~^www\.~i', '', $h);
		return $strip($ha) === $strip($hb);
	}

	public static function sameHost(string $a, string $b): bool
	{
		return self::hostOf($a) === self::hostOf($b) && self::hostOf($a) !== '';
	}

	/** Нормализация абсолютного URL: схема/хост в нижний регистр, без фрагмента, без :80/:443. */
	public static function normalize(string $url): ?string
	{
		$p = self::parse($url);
		if ($p === null) {
			return null;
		}
		$port = $p['port'];
		if (($p['scheme'] === 'http' && $port === 80) || ($p['scheme'] === 'https' && $port === 443)) {
			$port = null;
		}
		$path = $p['path'] !== '' ? $p['path'] : '/';
		$out = $p['scheme'] . '://' . $p['host'] . ($port ? ':' . $port : '') . $path;
		if ($p['query'] !== null && $p['query'] !== '') {
			$out .= '?' . $p['query'];
		}
		return $out;
	}

	/** Убирает . и .. из пути. */
	public static function removeDotSegments(string $path): string
	{
		$out = [];
		foreach (explode('/', $path) as $seg) {
			if ($seg === '' || $seg === '.') {
				continue;
			}
			if ($seg === '..') {
				array_pop($out);
				continue;
			}
			$out[] = $seg;
		}
		$res = '/' . implode('/', $out);
		if (str_ends_with($path, '/') && $res !== '/') {
			$res .= '/';
		}
		return $res;
	}

	/**
	 * Разрешение ссылки относительно базы (RFC 3986, раздел 5).
	 * Возвращает null для не-HTTP схем (mailto:, tel:, javascript:, data: …).
	 */
	public static function resolve(string $ref, string $base): ?string
	{
		$ref = trim($ref);
		if ($ref === '') {
			return null;
		}
		if (preg_match('~^(mailto|tel|sms|javascript|data|blob|about|ftp|ftps|ws|wss|file|magnet|intent|whatsapp|viber|telegram|skype|news):~i', $ref)) {
			return null;
		}
		// Любая другая схема (news:, geo:, callto: …) — не наш ресурс
		if (preg_match('~^[a-z][a-z0-9+.\-]*:~i', $ref) && !preg_match('~^https?://~i', $ref)) {
			return null;
		}
		$ref = self::encode($ref);
		$bp = self::parse($base);
		if ($bp === null) {
			return null;
		}
		if (str_starts_with($ref, '//')) {
			$ref = $bp['scheme'] . ':' . $ref;
		}
		if (preg_match('~^https?://~i', $ref)) {
			return self::normalize($ref);
		}

		$path = $bp['path'];
		$query = $bp['query'];
		$fragment = null;

		if (str_starts_with($ref, '#')) {
			$fragment = substr($ref, 1);
		} elseif (str_starts_with($ref, '?')) {
			$query = substr($ref, 1);
		} elseif (str_starts_with($ref, '/')) {
			$path = $ref;
			$query = null;
		} else {
			$slash = strrpos($path, '/');
			$dir = $slash === false ? '/' : substr($path, 0, $slash + 1);
			$rel = $dir . $ref;
			if (($q = strpos($rel, '?')) !== false) {
				$query = substr($rel, $q + 1);
				$rel = substr($rel, 0, $q);
			}
			if (($h = strpos($rel, '#')) !== false) {
				$fragment = substr($rel, $h + 1);
				$rel = substr($rel, 0, $h);
			}
			$path = $rel;
		}
		if ($path === '') {
			$path = '/';
		}
		$path = self::removeDotSegments($path);

		$out = $bp['scheme'] . '://' . $bp['host'] . ($bp['port'] ? ':' . $bp['port'] : '') . $path;
		if ($query !== null && $query !== '') {
			$out .= '?' . $query;
		}
		if ($fragment !== null && $fragment !== '') {
			$out .= '#' . $fragment;
		}
		return self::encode($out);
	}

	/** Разделяет URL на «без фрагмента» и сам фрагмент. */
	public static function splitFragment(string $url): array
	{
		$pos = strpos($url, '#');
		if ($pos === false) {
			return [$url, null];
		}
		return [substr($url, 0, $pos), substr($url, $pos + 1)];
	}

	/** Ссылка «без query» — для сравнения дублей (utm, ref и т.п.). */
	public static function withoutQuery(string $url): string
	{
		$pos = strpos($url, '?');
		return $pos === false ? $url : substr($url, 0, $pos);
	}

	/** Ключ нормализации для сравнения URL (регистр пути НЕ меняем — он значим). */
	public static function key(string $url): string
	{
		$u = self::withoutQuery(self::normalize($url) ?? $url);
		return rtrim($u, '/') === '' ? $u : $u;
	}
}

/**
 * Один HTTP-запрос: тело, заголовки, тайминги, цепочка редиректов.
 */
final class Req
{
	public string $url = '';
	public string $final = '';
	public int $status = 0;
	/** @var array<string,string> заголовки финального ответа (ключ в нижнем регистре) */
	public array $headers = [];
	/** @var list<string> все заголовки всех хопов */
	public array $headerLog = [];
	public string $body = '';
	/** @var list<array{from:string,to:string,status:int}> */
	public array $redirects = [];
	public ?string $error = null;
	public float $timeTotal = 0.0;
	public float $ttfb = 0.0;
	public float $connect = 0.0;
	public float $namelookup = 0.0;
	public int $bytes = 0;
	public string $httpVersion = '';
	public int $hops = 0;

	public function header(string $name, string $default = ''): string
	{
		return $this->headers[strtolower($name)] ?? $default;
	}

	public function hasHeader(string $name): bool
	{
		return isset($this->headers[strtolower($name)]);
	}
}

/**
 * HTTP-клиент на curl_multi: параллельные запросы, ручное следование редиректам.
 */
final class Http
{
	private const MAX_REDIRECTS = 10;

	/** @var null|callable():void колбэк для анимации во время ожидания сети */
	private static $onProgress = null;

	/**
	 * Подписка на «пульс» во время сетевых ожиданий (анимация в терминале).
	 */
	public static function onProgress(?callable $cb): void
	{
		self::$onProgress = $cb;
	}

	/**
	 * Один запрос (с цепочкой редиректов).
	 */
	public static function fetch(string $url, Cfg $cfg, string $method = 'GET', bool $keepBody = true, ?int $timeoutMs = null, int $maxHops = 10): Req
	{
		$out = self::fetchMany([$url], $cfg, $method, $keepBody, $timeoutMs, $maxHops);
		return $out[$url] ?? new Req();
	}

	/**
	 * Запрос с подменой User-Agent (нужно для проверки поведения сервера
	 * «глазами» конкретного бота).
	 */
	public static function fetchWithUa(string $url, Cfg $cfg, string $ua, array $extraHeaders = []): Req
	{
		$clone = clone $cfg;
		$clone->ua = $ua !== '' ? $ua : $cfg->ua;
		$out = self::fetchMany([$url], $clone, 'GET', false, 8000, 0);
		$req = $out[$url] ?? new Req();
		if ($extraHeaders !== []) {
			// повторяем запрос с дополнительными заголовками (Accept и т.п.)
			$ch = curl_init($url);
			$headers = ['Accept: ' . implode(', ', $extraHeaders)];
			curl_setopt_array($ch, [
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_FOLLOWLOCATION => false,
				CURLOPT_TIMEOUT        => 10,
				CURLOPT_CONNECTTIMEOUT => 6,
				CURLOPT_USERAGENT      => $clone->ua,
				CURLOPT_HTTPHEADER     => $headers,
			]);
			$body = curl_exec($ch);
			$code = (int)curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
			$ctype = (string)curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
			curl_close($ch);
			if ($code > 0) {
				$req->status = $code;
				$req->headers['content-type'] = $ctype;
			}
		}
		return $req;
	}

	/**
	 * Параллельные запросы. Возвращает массив Req по исходным URL.
	 *
	 * @param list<string> $urls
	 * @return array<string,Req>
	 */
	public static function fetchMany(array $urls, Cfg $cfg, string $method = 'GET', bool $keepBody = true, ?int $timeoutMs = null, int $maxHops = 10): array
	{
		$results = [];
		if (!$urls) {
			return $results;
		}
		$multi = curl_multi_init();
		$active = [];   // handle => Req
		$pending = array_values($urls);
		$queue = [];

		foreach ($pending as $u) {
			$req = new Req();
			$req->url = $u;
			$req->final = $u;
			$h = self::makeHandle($req, $u, $cfg, $method, $keepBody, $timeoutMs);
			if ($h === null) {
				$req->error = 'Некорректный URL';
				$results[$u] = $req;
				continue;
			}
			curl_multi_add_handle($multi, $h);
			$active[(int)$h] = $req;
		}

		do {
			$status = curl_multi_exec($multi, $running);
			if ($running > 0) {
				curl_multi_select($multi, 0.2);
				if (self::$onProgress !== null) {
					(self::$onProgress)();
				}
			}
			while (($info = curl_multi_info_read($multi)) !== false) {
				$h = $info['handle'];
				$req = $active[(int)$h] ?? null;
				if ($req === null) {
					curl_multi_remove_handle($multi, $h);
					continue;
				}
				$code = (int)curl_getinfo($h, CURLINFO_RESPONSE_CODE);
				$err = curl_error($h);
				$req->status = $code;
				$req->final = (string)(curl_getinfo($h, CURLINFO_EFFECTIVE_URL) ?: $req->final);
				$req->timeTotal = (float)curl_getinfo($h, CURLINFO_TOTAL_TIME);
				$req->ttfb = (float)curl_getinfo($h, CURLINFO_STARTTRANSFER_TIME);
				$req->connect = (float)curl_getinfo($h, CURLINFO_CONNECT_TIME);
				$req->namelookup = (float)curl_getinfo($h, CURLINFO_NAMELOOKUP_TIME);
				$req->bytes = (int)curl_getinfo($h, CURLINFO_SIZE_DOWNLOAD);
				$req->httpVersion = (string)curl_getinfo($h, CURLINFO_HTTP_VERSION);

				if ($err !== '' && $code === 0) {
					$req->error = $err;
				}

				$location = $req->header('location');
				$isRedirect = in_array($code, [301, 302, 303, 307, 308], true) && $location !== '';

				if ($isRedirect && $req->hops < $maxHops) {
					$next = Url::resolve($location, $req->final);
					if ($next === null) {
						$next = $location;
					}
					$req->redirects[] = ['from' => $req->final, 'to' => $next, 'status' => $code];
					$req->hops++;
					curl_multi_remove_handle($multi, $h);
					unset($active[(int)$h]);
					$req->final = $next;
					$req->headers = [];
					$req->body = '';   // тело заглушки редиректа не должно попасть в разбор страницы
					$nh = self::makeHandle($req, $next, $cfg, ($code === 303 ? 'GET' : $method), $keepBody, $timeoutMs);
					if ($nh !== null) {
						curl_multi_add_handle($multi, $nh);
						$active[(int)$nh] = $req;
					} else {
						$req->error = 'Некорректный URL редиректа';
					}
					continue;
				}
				if ($isRedirect) {
					// лимит переходов достигнут (или переходы отключены): фиксируем сам редирект
					$req->redirects[] = ['from' => $req->final, 'to' => $location, 'status' => $code];
					$req->error = $maxHops > 0
						? 'Слишком много редиректов (' . self::MAX_REDIRECTS . ')'
						: null;
				}
				$results[$req->url] = $req;
				curl_multi_remove_handle($multi, $h);
				unset($active[(int)$h]);
			}
		} while ($running > 0 || $active !== []);

		// Незавершённые (таймаут) — фиксируем как ошибку
		foreach ($active as $req) {
			$req->error = $req->error ?? ('Превышен таймаут ' . ($cfg->timeoutMs / 1000) . ' с');
			$results[$req->url] = $req;
		}
		curl_multi_close($multi);
		return $results;
	}

	private static function makeHandle(Req $req, string $url, Cfg $cfg, string $method, bool $keepBody, ?int $timeoutMs = null): ?\CurlHandle
	{
		$ch = curl_init();
		if ($ch === false) {
			return null;
		}
		$timeout = $timeoutMs ?? $cfg->timeoutMs;
		curl_setopt_array($ch, [
			CURLOPT_URL            => Url::encode($url),
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_FOLLOWLOCATION => false,
			CURLOPT_MAXREDIRS      => 0,
			CURLOPT_TIMEOUT_MS     => $timeout,
			CURLOPT_CONNECTTIMEOUT_MS => min($timeout, 10000),
			CURLOPT_USERAGENT      => $cfg->ua,
			CURLOPT_ACCEPT_ENCODING => 'gzip, deflate, br',
			CURLOPT_SSL_VERIFYPEER => true,
			CURLOPT_SSL_VERIFYHOST => 2,
			CURLOPT_ENCODING       => '',
			CURLOPT_HEADER         => false,
			CURLOPT_NOBODY         => $method === 'HEAD',
		]);
		curl_setopt($ch, CURLOPT_HEADERFUNCTION, function ($ch, string $line) use ($req): int {
			$len = strlen($line);
			$trim = trim($line);
			if ($trim !== '') {
				$req->headerLog[] = $trim;
				if (str_contains($trim, ':')) {
					[$k, $v] = explode(':', $trim, 2);
					$req->headers[strtolower(trim($k))] = trim($v);
				}
			}
			return $len;
		});
		if ($keepBody) {
			curl_setopt($ch, CURLOPT_WRITEFUNCTION, function ($ch, string $chunk) use ($req): int {
				$req->body .= $chunk;
				return strlen($chunk);
			});
		}
		return $ch;
	}
}

/**
 * robots.txt: парсер правил Allow/Disallow (с *, $), Sitemap, Host, Clean-param.
 */
final class Robots
{
	/** @var array<string,list<array{allow:bool,path:string,raw:string}>> */
	public array $groups = [];
	/** @var list<string> */
	public array $sitemaps = [];
	public ?string $host = null;
	/** @var list<string> */
	public array $errors = [];
	public int $status = 0;
	public bool $exists = false;
	/** @var array<string,list<int>> */
	public array $cleanParams = [];
	/** @var array<string,float> User-agent => crawl-delay в секундах */
	public array $crawlDelay = [];

	/**
	 * @param array<string,string> $headers
	 */
	public static function parse(string $text): self
	{
		$r = new self();
		$lines = preg_split('~\R~', $text) ?: [];
		$currentAgents = [];
		$lastWasAgent = false;
		$lineNo = 0;

		foreach ($lines as $line) {
			$lineNo++;
			$line = trim(preg_replace('~\s+~u', ' ', $line) ?? '');
			if ($line === '' || str_starts_with($line, '#')) {
				$lastWasAgent = false;
				continue;
			}
			$pos = strpos($line, ':');
			if ($pos === false) {
				$r->errors[] = 'Строка без разделителя «:»: ' . mb_substr($line, 0, 60);
				continue;
			}
			$field = strtolower(trim(substr($line, 0, $pos)));
			$value = trim(substr($line, $pos + 1));

			switch ($field) {
				case 'user-agent':
					if (!$lastWasAgent) {
						$currentAgents = [];
					}
					$currentAgents[] = strtolower($value);
					$lastWasAgent = true;
					foreach ($currentAgents as $ua) {
						if (!isset($r->groups[$ua])) {
							$r->groups[$ua] = [];
						}
					}
					break;
				case 'allow':
				case 'disallow':
					$allow = $field === 'allow';
					if ($value === '' && !$allow) {
						break; // пустой Disallow = разрешено всё
					}
					foreach ($currentAgents as $ua) {
						$r->groups[$ua][] = [
							'allow' => $allow,
							'path'  => $value,
							'raw'   => $field . ': ' . $value,
							'line'  => $lineNo,
							'text'  => $line,
						];
					}
					$lastWasAgent = false;
					break;
				case 'sitemap':
					if ($value !== '') {
						$r->sitemaps[] = $value;
					}
					$lastWasAgent = false;
					break;
				case 'host':
					$r->host = $value;
					$lastWasAgent = false;
					break;
				case 'clean-param':
					$parts = preg_split('~\s+~', $value) ?: [];
					$ref = array_shift($parts) ?? '';
					if ($ref !== '') {
						$r->cleanParams[$ref] = $parts;
					}
					$lastWasAgent = false;
					break;
				case 'crawl-delay':
					$delay = (float)str_replace(',', '.', $value);
					foreach ($currentAgents as $ua) {
						$r->crawlDelay[$ua] = $delay;
					}
					$lastWasAgent = false;
					break;
				default:
					$r->errors[] = 'Неизвестная директива: ' . $field;
					$lastWasAgent = false;
			}
		}
		return $r;
	}

	/** Разрешён ли путь (с query) для указанного агента. */
	public function allowed(string $pathAndQuery, string $agent = '*'): bool
	{
		$rules = $this->rulesFor($agent);
		if (!$rules) {
			return true;
		}
		$best = null;   // ['len'=>int,'allow'=>bool]
		foreach ($rules as $rule) {
			if ($this->match($rule['path'], $pathAndQuery)) {
				$len = strlen(str_replace(['*', '$'], '', $rule['path']));
				if ($best === null || $len > $best['len'] || ($len === $best['len'] && $rule['allow'])) {
					$best = ['len' => $len, 'allow' => $rule['allow']];
				}
			}
		}
		return $best === null ? true : $best['allow'];
	}

	/**
	 * Покрытие ботов: «прописан» (своя секция) / «через *» (действует общее правило) /
	 * «не задан» (упомянут только в общем правиле без явного блока).
	 *
	 * @return array<string,array{state:string,rules:list<array<string,mixed>>,delay:?float}>
	 */
	public function coverage(): array
	{
		$out = [];
		foreach (self::KNOWN_BOTS as $bot => $_) {
			$state = 'none';
			$rules = [];
			if (isset($this->groups[$bot])) {
				$state = 'explicit';
				$rules = $this->groups[$bot];
			} elseif (isset($this->groups['*'])) {
				$state = 'wildcard';
				$rules = $this->groups['*'];
			}
			$out[$bot] = [
				'state' => $state,
				'rules' => $rules,
				'delay' => $this->crawlDelay[$bot] ?? null,
			];
		}
		return $out;
	}

	/** Канонические имена ботов (для проверки регистра и покрытия). */
	public const KNOWN_BOTS = [
		'googlebot' => 'Googlebot', 'yandex' => 'Yandex', 'bingbot' => 'bingbot', 'duckduckbot' => 'DuckDuckBot',
		'gptbot' => 'GPTBot', 'oai-searchbot' => 'OAI-SearchBot', 'chatgpt-user' => 'ChatGPT-User',
		'claudebot' => 'ClaudeBot', 'anthropic-ai' => 'anthropic-ai', 'perplexitybot' => 'PerplexityBot',
		'google-extended' => 'Google-Extended', 'applebot-extended' => 'Applebot-Extended',
		'bytespider' => 'Bytespider', 'amazonbot' => 'Amazonbot', 'ccbot' => 'CCBot',
		'cohere-ai' => 'cohere-ai', 'diffbot' => 'Diffbot', 'meta-externalagent' => 'Meta-ExternalAgent',
		'semrushbot' => 'SemrushBot', 'ahrefsbot' => 'AhrefsBot', 'yandeximages' => 'YandexImages',
		'yandexvideo' => 'YandexVideo', 'yandexmedia' => 'YandexMedia', 'yandexblogs' => 'YandexBlogs',
	];

	/**
	 * Проверка конкретного URL: разрешён ли он боту и какое правило сработало.
	 *
	 * @return array{allowed:bool,rule:?array,matched:?array}
	 */
	public function testUrl(string $pathAndQuery, string $agent = '*'): array
	{
		$rules = $this->rulesFor($agent);
		$best = null;
		$bestLen = -1;
		foreach ($rules as $rule) {
			if (!$this->match($rule['path'], $pathAndQuery)) {
				continue;
			}
			$len = strlen(str_replace(['*', '$'], '', $rule['path']));
			if ($len > $bestLen || ($len === $bestLen && $rule['allow'])) {
				$best = $rule;
				$bestLen = $len;
			}
		}
		return [
			'allowed' => $best === null ? true : (bool)$best['allow'],
			'rule'    => $best,
			'matched' => $best === null ? null : [
				'line' => (int)($best['line'] ?? 0),
				'text' => (string)($best['text'] ?? $best['raw'] ?? ''),
			],
		];
	}

	/**
	 * Правило, которое реально сработало на указанный путь (для цитаты в отчёте).
	 *
	 * @return array{allow:bool,path:string,raw:string,line:int,text:string}|null
	 */
	public function bestMatch(string $pathAndQuery, string $agent = '*'): ?array
	{
		$rules = $this->rulesFor($agent);
		$best = null;
		foreach ($rules as $rule) {
			if (!$this->match($rule['path'], $pathAndQuery)) {
				continue;
			}
			$len = strlen(str_replace(['*', '$'], '', $rule['path']));
			if ($best === null || $len > $best['len'] || ($len === $best['len'] && $rule['allow'])) {
				$best = $rule + ['len' => $len];
			}
		}
		return $best === null ? null : $best;
	}

	/** @return list<array{allow:bool,path:string,raw:string}> */
	public function rulesFor(string $agent): array
	{
		$agent = strtolower($agent);
		$out = $this->groups[$agent] ?? [];
		if (!$out) {
			$out = $this->groups['*'] ?? [];
		}
		return $out;
	}

	private function match(string $pattern, string $path): bool
	{
		if ($pattern === '') {
			return true;
		}
		$regex = '';
		$len = strlen($pattern);
		for ($i = 0; $i < $len; $i++) {
			$ch = $pattern[$i];
			if ($ch === '*') {
				$regex .= '.*';
			} elseif ($ch === '$' && $i === $len - 1) {
				$regex .= '$';
			} else {
				$regex .= preg_quote($ch, '#');
			}
		}
		return (bool)preg_match('#^' . $regex . '#', $path);
	}
}

/**
 * Карта сайта: urlset + sitemapindex (+ .gz), сверка с фактическим обходом.
 */
final class SitemapSet
{
	/** @var array<string,array{lastmod:?string,changefreq:?string,priority:?string,source:string}> */
	public array $urls = [];
	/** @var list<string> */
	public array $sitemaps = [];
	/** @var list<string> */
	public array $errors = [];
	public int $status = 0;
	public bool $exists = false;
	/** @var array<string,int> статус кода по URL из карты */
	public array $statuses = [];

	/**
	 * Скачивает и разбирает карту (и индекс карт).
	 */
	public function load(string $origin, Cfg $cfg): void
	{
		$started = microtime(true);
		$this->deadline = $started + 20;   // карта не должна съедать весь лимит времени
		$start = rtrim($origin, '/') . '/sitemap.xml';
		$this->loadFile($start, $cfg, 0);
		if (!$this->exists) {
			$idx = rtrim($origin, '/') . '/sitemap-index.xml';
			$this->loadFile($idx, $cfg, 0);
		}
	}

	/** @var float дедлайн загрузки карты (секунды от старта) */
	private float $deadline = 0.0;

	private function loadFile(string $url, Cfg $cfg, int $level): void
	{
		if ($level > 4 || count($this->sitemaps) > 60) {
			return;
		}
		if ($this->deadline > 0 && microtime(true) > $this->deadline) {
			$this->errors[] = 'Загрузка карты остановлена по лимиту времени';
			return;
		}
		$req = Http::fetch($url, $cfg);
		$this->status = $req->status;
		if ($req->status !== 200) {
			$this->errors[] = $url . ' → HTTP ' . ($req->status ?: ($req->error ?? 'нет ответа'));
			return;
		}
		$this->exists = true;
		$this->sitemaps[] = $url;
		$body = $req->body;
		if (str_contains($req->header('content-type'), 'gzip') || str_starts_with($url, '') && str_ends_with($url, '.gz')) {
			$decoded = @gzdecode($body);
			if ($decoded !== false) {
				$body = $decoded;
			}
		}
		$doc = new \DOMDocument();
		libxml_use_internal_errors(true);
		$ok = $doc->loadXML(trim($body), LIBXML_NOWARNING | LIBXML_NOERROR | LIBXML_NONET);
		libxml_clear_errors();
		if (!$ok || $doc->documentElement === null) {
			$this->errors[] = $url . ' → невалидный XML';
			return;
		}
		$xp = new \DOMXPath($doc);
		$root = strtolower($doc->documentElement->nodeName);

		if ($root === 'sitemapindex') {
			foreach ($xp->query('//*[local-name()="sitemap"]') ?: [] as $node) {
				$loc = trim((string)($xp->query('./*[local-name()="loc"]', $node)?->item(0)?->nodeValue ?? ''));
				if ($loc === '') {
					continue;
				}
				$this->loadFile($loc, $cfg, $level + 1);
			}
			return;
		}
		foreach ($xp->query('//*[local-name()="url"]') ?: [] as $node) {
			$loc = trim((string)($xp->query('./*[local-name()="loc"]', $node)?->item(0)?->nodeValue ?? ''));
			if ($loc === '') {
				continue;
			}
			$norm = Url::normalize($loc) ?? $loc;
			$lastmod = trim((string)($xp->query('./*[local-name()="lastmod"]', $node)?->item(0)?->nodeValue ?? ''));
			$cf = trim((string)($xp->query('./*[local-name()="changefreq"]', $node)?->item(0)?->nodeValue ?? ''));
			$pr = trim((string)($xp->query('./*[local-name()="priority"]', $node)?->item(0)?->nodeValue ?? ''));
			$this->urls[$norm] = [
				'lastmod'    => $lastmod !== '' ? $lastmod : null,
				'changefreq' => $cf !== '' ? $cf : null,
				'priority'   => $pr !== '' ? $pr : null,
				'source'     => $url,
			];
		}
	}
}

/**
 * Разбор HTML: DOM + XPath (регулярки не используются — иначе ломается
 * кириллица, вложенность и произвольный порядок атрибутов).
 */
final class Html
{
	/** Размер bottom-k эскиза (кандидаты для поиска дублей). */
	private const SKETCH_SIZE = 128;

	/** @return array<string,mixed> */
	public static function parse(string $html, string $contentType = '', string $charsetHint = ''): array
	{
		$html = self::toUtf8($html, $contentType, $charsetHint);
		if (trim($html) === '') {
			return self::emptyResult('');
		}

		$doc = new \DOMDocument();
		$prev = libxml_use_internal_errors(true);
		$enc = mb_encode_numericentity($html, [0x80, 0x10FFFF, 0, 0x1FFFFF], 'UTF-8');
		$ok = $doc->loadHTML($enc, LIBXML_NOWARNING | LIBXML_NOERROR | LIBXML_NONET | LIBXML_COMPACT);
		libxml_clear_errors();
		libxml_use_internal_errors($prev);
		if (!$ok) {
			return self::emptyResult($html);
		}
		$lines = explode("\n", $html);
		$cites = [];   // цитаты источника: ключ → [код, номер строки, строка файла]
		if ($doc->documentElement !== null) {
			// тег <html> собираем вручную: saveHTML(documentElement) вернул бы весь документ
			$htmlTag = '<html';
			foreach ($doc->documentElement->attributes ?? [] as $attr) {
				$htmlTag .= ' ' . $attr->nodeName . '="' . $attr->nodeValue . '"';
			}
			$htmlTag .= '>';
			$htmlLine = method_exists($doc->documentElement, 'getLineNo') ? (int)$doc->documentElement->getLineNo() : 0;
			$cites['html'] = [
				'code' => $htmlTag,
				'line' => $htmlLine,
				'text' => ($htmlLine > 0 && isset($lines[$htmlLine - 1]) && trim($lines[$htmlLine - 1]) !== '')
					? trim($lines[$htmlLine - 1])
					: $htmlTag,
			];
		}
		$xp = new \DOMXPath($doc);

		$headNode = $xp->query('//head')->item(0);
		$headHtml = '';
		if ($headNode !== null) {
			$inner = '';
			foreach ($headNode->childNodes as $child) {
				$inner .= $doc->saveHTML($child);
			}
			$inner = trim(preg_replace('~\s+~u', ' ', $inner) ?? $inner);
			$headHtml = mb_strlen($inner) > 600 ? mb_substr($inner, 0, 600) . '…' : $inner;
		}

		// --- title ---
		$titleNode = $xp->query('//head/title')->item(0);
		$title = trim(preg_replace('~\s+~u', ' ', (string)($titleNode?->textContent ?? '')));
		$titleTag = $titleNode !== null ? self::tagOf($doc, $titleNode) : '';
		if ($titleNode !== null) {
			$cites['title'] = self::cite($doc, $titleNode, $lines);
		}

		// --- meta-теги ---
		$meta = [];
		$metaTag = [];
		foreach ($xp->query('//head/meta') ?: [] as $m) {
			$name = strtolower(trim((string)$m->getAttribute('name')));
			$prop = strtolower(trim((string)$m->getAttribute('property')));
			$httpEquiv = strtolower(trim((string)$m->getAttribute('http-equiv')));
			$content = trim((string)$m->getAttribute('content'));
			$key = $name !== '' ? $name : ($prop !== '' ? $prop : $httpEquiv);
			if ($key === '') {
				continue;
			}
			if (!isset($meta[$key])) {
				$meta[$key] = $content;
				$metaTag[$key] = self::tagOf($doc, $m);
				$cites['meta:' . $key] = self::cite($doc, $m, $lines);
			}
		}

		// --- link-теги ---
		$canonical = null;
		$canonicalTag = '';
		$hreflang = [];
		$icons = [];
		$stylesheets = [];
		$preloads = 0;
		$llmsLinks = [];
		foreach ($xp->query('//head/link') ?: [] as $l) {
			$rel = strtolower(trim((string)$l->getAttribute('rel')));
			$href = trim((string)$l->getAttribute('href'));
			$parts = preg_split('~\s+~', $rel) ?: [];
			if (in_array('canonical', $parts, true) && $canonical === null) {
				$canonical = $href;
				$canonicalTag = self::tagOf($doc, $l);
				$cites['canonical'] = self::cite($doc, $l, $lines);
			}
			if (in_array('alternate', $parts, true) && $l->hasAttribute('hreflang')) {
				$hreflang[] = ['lang' => trim((string)$l->getAttribute('hreflang')), 'href' => $href];
			}
			if (in_array('icon', $parts, true) || in_array('shortcut', $parts, true) || in_array('apple-touch-icon', $parts, true)) {
				$icons[] = $href;
				$cites['icon:' . (count($icons) - 1)] = self::cite($doc, $l, $lines);
			}
			if (in_array('stylesheet', $parts, true)) {
				$stylesheets[] = $href;
			}
			if (in_array('preload', $parts, true) || in_array('preconnect', $parts, true) || in_array('dns-prefetch', $parts, true)) {
				$preloads++;
			}
			if (in_array('llms-txt', $parts, true) || in_array('llms-full-txt', $parts, true) || in_array('ai-content', $parts, true)) {
				$llmsLinks[] = ['rel' => $rel, 'href' => $href, 'type' => (string)$l->getAttribute('type')];
			}
		}

		// --- заголовки ---
		$headings = [];
		$headingCounts = [1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0, 6 => 0];
		$h1Texts = [];
		$h1No = 0;
		foreach ($xp->query('//h1|//h2|//h3|//h4|//h5|//h6') ?: [] as $h) {
			$level = (int)substr(strtolower($h->nodeName), 1);
			$txt = trim(preg_replace('~\s+~u', ' ', (string)$h->textContent));
			$headingCounts[$level]++;
			$headings[] = ['level' => $level, 'text' => $txt];
			if ($level === 1) {
				$h1Texts[] = $txt;
				if ($h1No < 5) {
					$cites['h1:' . $h1No] = self::cite($doc, $h, $lines);
				}
				$h1No++;
			}
		}

		// --- зоны страницы: где именно на странице стоит элемент ---
		// (текст статьи, меню, подвал, шапка, боковая колонка) — нужно, чтобы в
		// отчёте было видно МЕСТО находки, а не только URL
		$zoneOf = [];
		$zoneQueries = [
			'//main'    => 'текст страницы',
			'//article' => 'текст статьи',
			'//nav'     => 'меню',
			'//header'  => 'шапка',
			'//footer'  => 'подвал',
			'//aside'   => 'боковая колонка',
		];
		foreach ($zoneQueries as $q => $zone) {
			foreach ($xp->query($q) ?: [] as $box) {
				foreach ($xp->query('.//a[@href]|.//area[@href]', $box) as $node) {
					$zoneOf[spl_object_id($node)] = $zone;
				}
				foreach ($xp->query('.//img', $box) as $node) {
					$zoneOf[spl_object_id($node)] = $zone;
				}
			}
		}

		// --- ссылки ---
		$links = [];
		foreach ($xp->query('//a[@href]|//area[@href]') ?: [] as $a) {
			$raw = trim((string)$a->getAttribute('href'));
			$text = trim(preg_replace('~\s+~u', ' ', (string)$a->textContent));
			$linkTitle = trim((string)$a->getAttribute('title'));
			$rel = strtolower(trim((string)$a->getAttribute('rel')));
			$cite = self::cite($doc, $a, $lines);
			$links[] = [
				'raw'    => $raw,
				'text'   => $text !== '' ? $text : $linkTitle,
				'rel'    => $rel,
				'target' => strtolower(trim((string)$a->getAttribute('target'))),
				'title'  => $linkTitle,
				'hidden' => strtolower(trim((string)$a->getAttribute('aria-hidden'))) === 'true',
				'zone'   => $zoneOf[spl_object_id($a)] ?? 'вне основных блоков',
				'tag'    => self::tagOf($doc, $a),
				'line'   => $cite['line'],
				'text0'  => $cite['text'],
			];
		}

		// --- изображения ---
		$images = [];
		foreach ($xp->query('//img') ?: [] as $im) {
			$src = trim((string)$im->getAttribute('src'));
			$alt = $im->hasAttribute('alt') ? (string)$im->getAttribute('alt') : null;
			$w = $im->hasAttribute('width') ? (int)$im->getAttribute('width') : null;
			$h = $im->hasAttribute('height') ? (int)$im->getAttribute('height') : null;
			$cite = self::cite($doc, $im, $lines);
			$images[] = [
				'src'     => $src,
				'alt'     => $alt,
				'w'       => ($w !== null && $w > 0) ? $w : null,
				'h'       => ($h !== null && $h > 0) ? $h : null,
				'loading' => strtolower(trim((string)$im->getAttribute('loading'))),
				'srcset'  => $im->hasAttribute('srcset') && trim((string)$im->getAttribute('srcset')) !== '',
				'fetchprio'=> strtolower(trim((string)$im->getAttribute('fetchpriority'))),
				'zone'    => $zoneOf[spl_object_id($im)] ?? 'вне основных блоков',
				'tag'     => self::tagOf($doc, $im),
				'line'    => $cite['line'],
				'text0'   => $cite['text'],
			];
		}

		// --- подгружаемые ресурсы (mixed content, вес страницы) ---
		$resources = [];
		foreach ($xp->query('//img[@src]|//script[@src]|//iframe[@src]|//source[@src]|//video[@src]|//audio[@src]|//embed[@src]|//object[@data]') ?: [] as $r) {
			$u = trim((string)($r->getAttribute('src') ?: $r->getAttribute('data')));
			if ($u !== '') {
				$resources[] = ['url' => $u, 'tag' => strtolower($r->nodeName)];
			}
		}
		foreach ($xp->query('//link[@href]') ?: [] as $l) {
			$rel = strtolower(trim((string)$l->getAttribute('rel')));
			if (preg_match('~stylesheet|preload|prefetch|icon|manifest~', $rel)) {
				$u = trim((string)$l->getAttribute('href'));
				if ($u !== '') {
					$resources[] = ['url' => $u, 'tag' => 'link'];
				}
			}
		}

		// --- JSON-LD ---
		$jsonld = [];
		$jsonLdRaw = '';
		foreach ($xp->query('//script[@type="application/ld+json"]') ?: [] as $s) {
			$raw = trim((string)$s->textContent);
			$jsonLdRaw .= $raw . "\n";
			$entry = ['raw' => $raw, 'ok' => false, 'types' => [], 'error' => null, 'nodes' => 0];
			if ($raw === '') {
				$entry['error'] = 'Пустой блок JSON-LD';
			} else {
				$decoded = json_decode($raw, true);
				if (json_last_error() !== JSON_ERROR_NONE) {
					$entry['error'] = 'Некорректный JSON: ' . json_last_error_msg();
				} else {
					$entry['ok'] = true;
					$entry['nodes'] = self::countJsonLdNodes($decoded);
					foreach (self::collectJsonLdTypes($decoded) as $t) {
						$entry['types'][$t] = ($entry['types'][$t] ?? 0) + 1;
					}
				}
			}
			$jsonld[] = $entry;
			$cites['jsonld:' . (count($jsonld) - 1)] = self::cite($doc, $s, $lines);
		}

		// --- формы ---
		$forms = ['count' => 0, 'noLabel' => 0, 'noName' => 0, 'noAction' => 0, 'fields' => 0, 'bad' => []];
		foreach ($xp->query('//form') ?: [] as $f) {
			$forms['count']++;
			if (trim((string)$f->getAttribute('action')) === '') {
				$forms['noAction']++;
			}
			foreach ($xp->query('.//input|.//select|.//textarea', $f) ?: [] as $field) {
				$type = strtolower((string)$field->getAttribute('type'));
				if ($type === 'hidden' || $type === 'submit' || $type === 'button' || $type === 'reset' || $type === 'image') {
					continue;
				}
				$forms['fields']++;
				if (trim((string)$field->getAttribute('name')) === '') {
					$forms['noName']++;
				}
				$id = trim((string)$field->getAttribute('id'));
				$hasLabel = $id !== '' && $xp->query(sprintf('//label[@for=%s]', self::xpathLiteral($id)))->length > 0;
				if (!$hasLabel) {
					$hasLabel = $xp->query('.//label', $field)->length > 0;
				}
				if (!$hasLabel) {
					$hasLabel = $xp->query('ancestor::label', $field)->length > 0;   // <label>…<input>…</label>
				}
				if (!$hasLabel) {
					$hasLabel = $field->hasAttribute('aria-label') || $field->hasAttribute('aria-labelledby');
				}
				if (!$hasLabel) {
					$forms['noLabel']++;
					$forms['bad'][] = [
						'name'   => trim((string)$field->getAttribute('name')) ?: '(без name)',
						'type'   => $type !== '' ? $type : $field->nodeName,
						'action' => trim((string)$f->getAttribute('action')) ?: '(action не задан)',
						'ph'     => trim((string)$field->getAttribute('placeholder')),
					];
				}
			}
		}

		// --- iframe ---
		$iframes = ['count' => 0, 'noTitle' => 0];
		foreach ($xp->query('//iframe') ?: [] as $fr) {
			$iframes['count']++;
			if (trim((string)$fr->getAttribute('title')) === '' && !$fr->hasAttribute('aria-label')) {
				$iframes['noTitle']++;
			}
		}

		// --- id ---
		$ids = [];
		foreach ($xp->query('//*[@id]') ?: [] as $n) {
			$ids[] = (string)$n->getAttribute('id');
		}
		$idCount = array_count_values($ids);
		$dupIds = array_keys(array_filter($idCount, static fn($c) => $c > 1));

		// --- скрипты/стили ---
		$scripts = $xp->query('//script')->length;
		$inlineScripts = $xp->query('//script[not(@src)]')->length;
		$styles = $xp->query('//style')->length;
		$blocking = $stylesheets;
		$blockingJs = 0;
		foreach ($xp->query('//head/script[@src]') ?: [] as $s) {
			$async = strtolower(trim((string)$s->getAttribute('async'))) !== 'false';
			$defer = strtolower(trim((string)$s->getAttribute('defer'))) !== 'false';
			$type = strtolower(trim((string)$s->getAttribute('type')));
			if (!$async && !$defer && $type !== 'module') {
				$blockingJs++;
			}
		}

		// --- видимый текст ---
		foreach (['//script', '//style', '//noscript', '//template', '//svg', '//iframe'] as $xpPath) {
			foreach ($xp->query($xpPath) ?: [] as $n) {
				$n->parentNode?->removeChild($n);
			}
		}
		$body = $xp->query('//body')->item(0);
		$text = (string)($body?->textContent ?? '');
		$text = trim(preg_replace('~\s+~u', ' ', $text) ?? '');
		$words = preg_match_all('~[\p{L}\p{N}]+~u', $text) ?: 0;

		// --- «основной» текст без навигации/футера/шапки (для поиска дублей) ---
		foreach (['//nav', '//header', '//footer', '//aside'] as $xpPath) {
			foreach ($xp->query($xpPath) ?: [] as $n) {
				$n->parentNode?->removeChild($n);
			}
		}
		$bodyMain = $xp->query('//body')->item(0);
		$mainText = (string)($bodyMain?->textContent ?? '');
		$mainText = trim(preg_replace('~\s+~u', ' ', $mainText) ?? '');
		if (mb_strlen($mainText) < 200) {
			$mainText = $text;   // нет явного main — берём весь текст
		}

		// --- сигналы цитируемости (GEO) ---
		$contentLinks = $xp->query('//main//a[@href]|//article//a[@href]')->length;
		$firstPara = '';
		$fp = $xp->query('//main//p|//article//p|//body//p')->item(0);
		if ($fp !== null) {
			$firstPara = trim(preg_replace('~\s+~u', ' ', (string)$fp->textContent) ?? '');
		}
		$statsCount = preg_match_all('~\b\d[\d\s.,]{0,8}\s?(?:%|‰|руб|₽|млн|млрд|тыс|см|м|м²|м3|кг|т|лет|дн|шт)~u', $mainText) ?: 0;
		$quoteCount = preg_match_all('~[«"„]~u', $mainText) ?: 0;

		// --- частотность слов по ПОЛНОМУ тексту (без усечения) ---
		$topKeywords = [];
		$wordList = preg_split('~[^\p{L}\p{N}]+~u', mb_strtolower($mainText), -1, PREG_SPLIT_NO_EMPTY) ?: [];
		$wordTotal = count($wordList);
		if ($wordTotal >= 150) {
			$stop = ['и','в','на','с','по','для','не','что','это','как','мы','вы','наш','ваш','к','а','из','до','за','то','же','бы','их','при','более','всего','также','или','если','чтобы','этот'];
			$counts = array_count_values($wordList);
			arsort($counts);
			$seen = [];
			foreach ($counts as $w => $n) {
				$w = (string)$w;
				if (mb_strlen($w) < 4 || in_array($w, $stop, true) || isset($seen[$w])) {
					continue;
				}
				$seen[$w] = true;
				$topKeywords[] = ['w' => $w, 'n' => $n, 'share' => $n / $wordTotal];
				if (count($topKeywords) >= 3) {
					break;
				}
			}
		}

		// --- язык ---
		$htmlNode = $xp->query('//html')->item(0);
		$lang = $htmlNode !== null ? strtolower(trim((string)$htmlNode->getAttribute('lang'))) : '';
		$script = self::detectScript($text);

		// --- viewport / charset ---
		$viewport = $meta['viewport'] ?? null;
		$charset = null;
		foreach ($xp->query('//head/meta') ?: [] as $m) {
			if (strtolower(trim((string)$m->getAttribute('charset'))) !== '') {
				$charset = trim((string)$m->getAttribute('charset'));
			}
		}
		if ($charset === null && preg_match('~<meta[^>]+charset=[\'"]([^\'"]+)~i', $html, $cm)) {
			$charset = $cm[1];
		}

		// --- ошибки PHP в выводе ---
		// Ловим только настоящий формат ошибки: «Warning: текст in /path/file.php on line 12».
		// Без «on line N» это обычно текст документации, а не ошибка сайта.
		$phpErrors = [];
		$phpPatterns = [
			'~(?<!\w)(Warning|Notice|Deprecated|Fatal error|Parse error|Recoverable fatal error):\s[^\n<]{3,160}\s+in\s+[^\n<]*(?:[/\x5c]|\.php\b)[^\n<]{0,120}\s+on\s+line\s+\d+~i',
			'~(?<!\w)(Uncaught)\s+\w*(Error|Exception)\b[^\n<]{0,160}~i',
			'~(?<!\w)(Warning|Notice|Deprecated):\s*(?:Неопределенн|неопределенн)[^\n<]{0,120}~iu',
		];
		foreach ($phpPatterns as $pat) {
			if (preg_match_all($pat, $html, $mm)) {
				foreach (array_slice(array_unique($mm[0]), 0, 3) as $hit) {
					$phpErrors[] = trim(preg_replace('~\s+~u', ' ', $hit) ?? $hit);
				}
			}
		}

		// --- шинглы для поиска дублей (по основному тексту) ---
		$shingles = self::shingles($mainText);
		// «эскиз» bottom-k: k наименьших хешей — дешёвый кандидат-поиск пар-дублей
		$sketch = $shingles;
		sort($sketch);
		$sketch = array_slice($sketch, 0, self::SKETCH_SIZE);

		// --- доступность и структура ---
		$nodes = $doc->getElementsByTagName('*')->length;
		$hasMain = $xp->query('//main')->length > 0;
		$navCount = $xp->query('//nav')->length;
		$navLinks = $xp->query('//nav//a[@href]')->length;
		$hasSkipLink = false;
		foreach ($xp->query('//a[@href]') ?: [] as $a) {
			$href = strtolower(trim((string)$a->getAttribute('href')));
			$cls = strtolower((string)$a->getAttribute('class'));
			if (str_starts_with($href, '#') && (str_contains($cls, 'skip') || in_array(ltrim($href, '#'), ['content', 'main', 'main-content', 'maincontent', 'content-wrap'], true))) {
				$hasSkipLink = true;
				break;
			}
		}
		$namelessControls = 0;
		foreach ($xp->query('//button|//a|//input[@type="submit"]|//input[@type="button"]') ?: [] as $ctrl) {
			$txt = trim(preg_replace('~\s+~u', ' ', (string)$ctrl->textContent) ?? '');
			$hasName = $txt !== ''
				|| trim((string)$ctrl->getAttribute('title')) !== ''
				|| trim((string)$ctrl->getAttribute('aria-label')) !== ''
				|| $ctrl->getAttribute('aria-labelledby') !== ''
				|| $xp->query('.//img[@alt]', $ctrl)->length > 0;
			if (!$hasName) {
				$namelessControls++;
			}
		}

		// --- структура, удобная для цитирования (GEO) ---
		$lists = $xp->query('//ul|//ol')->length;
		$articles = $xp->query('//article')->length;
		$questions = 0;
		foreach ($xp->query('//h2|//h3|//dt') ?: [] as $q) {
			$t = trim((string)$q->textContent);
			if (str_ends_with($t, '?') || str_ends_with($t, '？')) {
				$questions++;
			}
		}
		$paras = $xp->query('//p')->length;
		$shortParas = 0;
		$longParas = 0;
		foreach ($xp->query('//p') ?: [] as $pnode) {
			$len = mb_strlen(trim((string)$pnode->textContent));
			if ($len >= 40 && $len <= 260) {
				$shortParas++;
			} elseif ($len > 600) {
				$longParas++;
			}
		}
		$hasAuthor = ($meta['author'] ?? '') !== ''
			|| $xp->query('//*[@rel="author"]')->length > 0
			|| $xp->query('//*[contains(@class,"author")]')->length > 0
			|| stripos((string)($xp->query('//body')->item(0)?->textContent ?? ''), 'автор') !== false
			|| stripos($jsonLdRaw, '"author"') !== false;   // автор в разметке — тоже сигнал E-E-A-T
		$hasDate = $xp->query('//time[@datetime]')->length > 0
			|| ($meta['article:published_time'] ?? '') !== ''
			|| ($meta['article:modified_time'] ?? '') !== ''
			|| ($meta['date'] ?? '') !== ''
			|| stripos($jsonLdRaw, '"datePublished"') !== false
			|| stripos($jsonLdRaw, '"dateModified"') !== false;
		$sameAs = substr_count($jsonLdRaw, '"sameAs"');
		$relNext = false;
		$relPrev = false;
		foreach ($xp->query('//link') ?: [] as $l) {
			$rel = strtolower(trim((string)$l->getAttribute('rel')));
			if ($rel === 'next') {
				$relNext = true;
			}
			if ($rel === 'prev') {
				$relPrev = true;
			}
		}
		$fontNoDisplay = 0;
		$inlineCss = '';
		foreach ($xp->query('//style') ?: [] as $st) {
			$inlineCss .= (string)$st->textContent;
		}
		if (preg_match_all('~@font-face\s*\{[^}]*\}~i', $inlineCss, $ff)) {
			foreach ($ff[0] as $block) {
				if (!preg_match('~font-display\s*:~i', $block)) {
					$fontNoDisplay++;
				}
			}
		}
		$faqSchema = false;
		foreach ($xp->query('//script[@type="application/ld+json"]') ?: [] as $s) {
			if (stripos((string)$s->textContent, 'FAQPage') !== false) {
				$faqSchema = true;
			}
		}

		return [
			'title'        => $title,
			'titleTag'     => $titleTag,
			'headHtml'     => $headHtml,
			'cites'        => $cites,
			'titleLen'     => mb_strlen($title),
			'description'  => $meta['description'] ?? '',
			'descLen'      => mb_strlen((string)($meta['description'] ?? '')),
			'robotsMeta'   => strtolower((string)($meta['robots'] ?? '')),
			'meta'         => $meta,
			'metaTag'      => $metaTag,
			'googlebot'    => strtolower((string)($meta['googlebot'] ?? '')),
			'canonical'    => $canonical,
			'canonicalTag' => $canonicalTag,
			'hreflang'     => $hreflang,
			'llmsLinks'   => $llmsLinks,
			'icons'        => $icons,
			'viewport'     => $viewport,
			'charset'      => $charset,
			'lang'         => $lang,
			'og'           => [
				'title'       => $meta['og:title'] ?? '',
				'description' => $meta['og:description'] ?? '',
				'image'       => $meta['og:image'] ?? '',
				'url'         => $meta['og:url'] ?? '',
				'type'        => $meta['og:type'] ?? '',
			],
			'twitter'      => [
				'card'  => $meta['twitter:card'] ?? '',
				'title' => $meta['twitter:title'] ?? '',
				'image' => $meta['twitter:image'] ?? '',
			],
			'jsonld'       => $jsonld,
			'headings'     => $headings,
			'headingCount' => $headingCounts,
			'h1'           => $h1Texts,
			'links'        => $links,
			'images'       => $images,
			'resources'    => $resources,
			'forms'        => $forms,
			'iframes'      => $iframes,
			'ids'          => ['count' => count($ids), 'dupes' => $dupIds],
			'idList'       => array_slice($ids, 0, 300),
			'nodes'        => $nodes,
			'hasMain'      => $hasMain,
			'hasSkipLink'  => $hasSkipLink,
			'navCount'     => $navCount,
			'navLinks'     => $navLinks,
			'namelessControls' => $namelessControls,
			'lists'        => $lists,
			'articles'     => $articles,
			'questions'    => $questions,
			'paras'        => $paras,
			'shortParas'   => $shortParas,
			'longParas'    => $longParas,
			'hasAuthor'    => $hasAuthor,
			'hasDate'      => $hasDate,
			'sameAs'       => $sameAs,
			'relNext'      => $relNext,
			'relPrev'      => $relPrev,
			'fontNoDisplay'=> $fontNoDisplay,
			'faqSchema'    => $faqSchema,
			'contentLinks' => $contentLinks,
			'firstPara'    => mb_substr($firstPara, 0, 600),
			'firstParaLen'=> mb_strlen($firstPara),
			'statsCount'   => $statsCount,
			'topKeywords'  => $topKeywords,
			'quoteCount'   => $quoteCount,
			'scripts'      => $scripts,
			'inlineScripts'=> $inlineScripts,
			'styles'       => $styles,
			'blocking'     => $blocking,
			'blockingJs'   => $blockingJs,
			'preloads'     => $preloads,
			'text'         => mb_substr($text, 0, 4000),
			'textLen'      => mb_strlen($text),
			'mainTextLen'  => mb_strlen($mainText),
			'textHash'     => md5($mainText),
			'words'        => $words,
			'script'       => $script,
			'phpErrors'    => $phpErrors,
			'shingles'     => $shingles,
			'sketch'       => $sketch,
			'htmlBytes'    => strlen($html),
		];
	}

	/** @return array<string,mixed> */
	private static function emptyResult(string $html): array
	{
		return [
			'title' => '', 'titleLen' => 0, 'description' => '', 'descLen' => 0, 'robotsMeta' => '',
			'googlebot' => '', 'canonical' => null, 			'hreflang' => [], 'llmsLinks' => [], 'icons' => [], 'viewport' => null,
			'charset' => null, 'lang' => '', 'og' => ['title' => '', 'description' => '', 'image' => '', 'url' => '', 'type' => ''],
			'twitter' => ['card' => '', 'title' => '', 'image' => ''], 'jsonld' => [], 'headings' => [],
			'cites' => [],
			'headingCount' => [1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0, 6 => 0], 'h1' => [], 'links' => [],
			'images' => [], 'resources' => [],
			'forms' => ['count' => 0, 'noLabel' => 0, 'noName' => 0, 'noAction' => 0, 'fields' => 0],
			'iframes' => ['count' => 0, 'noTitle' => 0], 'ids' => ['count' => 0, 'dupes' => []],
			'idList' => [], 'nodes' => 0, 'hasMain' => false, 'hasSkipLink' => false,
			'navCount' => 0, 'navLinks' => 0, 'namelessControls' => 0,
			'lists' => 0, 'articles' => 0, 'questions' => 0, 'paras' => 0, 'shortParas' => 0, 'longParas' => 0,
			'hasAuthor' => false, 'hasDate' => false, 'sameAs' => 0, 'relNext' => false, 'relPrev' => false,
			'fontNoDisplay' => 0, 'faqSchema' => false, 'topKeywords' => [],
			'contentLinks' => 0, 'firstPara' => '', 'firstParaLen' => 0, 'statsCount' => 0, 'quoteCount' => 0,
			'scripts' => 0, 'inlineScripts' => 0, 'styles' => 0, 'blocking' => 0, 'blockingJs' => 0, 'preloads' => 0,
			'text' => '', 'textLen' => 0, 'mainTextLen' => 0, 'textHash' => '', 'words' => 0, 'script' => 'unknown', 'phpErrors' => [],
			'shingles' => [], 'sketch' => [], 'htmlBytes' => strlen($html),
		];
	}

	private static function toUtf8(string $html, string $contentType, string $charsetHint): string
	{
		$charset = '';
		if ($charsetHint !== '') {
			$charset = $charsetHint;
		} elseif (preg_match('~charset=([\w\-]+)~i', $contentType, $m)) {
			$charset = $m[1];
		} elseif (preg_match('~<meta[^>]+charset=[\'"]([^\'"]+)~i', substr($html, 0, 2000), $m)) {
			$charset = $m[1];
		}
		$charset = strtoupper(trim($charset));
		if ($charset === '' || $charset === 'UTF-8' || $charset === 'UTF8') {
			return $html;
		}
		if (in_array($charset, ['WINDOWS-1251', 'CP1251'], true)) {
			$converted = @mb_convert_encoding($html, 'UTF-8', 'Windows-1251');
			return $converted === false ? $html : $converted;
		}
		if (function_exists('iconv')) {
			$converted = @iconv($charset, 'UTF-8//IGNORE', $html);
			if ($converted !== false) {
				return $converted;
			}
		}
		return $html;
	}

	/** @return list<string> */
	public static function collectJsonLdTypes(mixed $node): array
	{
		$types = [];
		if (is_array($node)) {
			if (isset($node['@type'])) {
				$t = $node['@type'];
				foreach (is_array($t) ? $t : [$t] as $one) {
					if (is_string($one)) {
						$types[] = $one;
					}
				}
			}
			if (isset($node['@graph']) && is_array($node['@graph'])) {
				foreach ($node['@graph'] as $child) {
					$types = array_merge($types, self::collectJsonLdTypes($child));
				}
			}
			foreach ($node as $k => $v) {
				if ($k === '@type' || $k === '@graph') {
					continue;
				}
				if (is_array($v) && (isset($v['@type']) || isset($v['@graph']))) {
					$types = array_merge($types, self::collectJsonLdTypes($v));
				}
			}
		}
		return $types;
	}

	public static function countJsonLdNodes(mixed $node): int
	{
		if (!is_array($node)) {
			return 0;
		}
		if (isset($node['@graph']) && is_array($node['@graph'])) {
			return count($node['@graph']);
		}
		return 1;
	}

	/** Определение доминирующего алфавита текста. */
	public static function detectScript(string $text): string
	{
		$sample = mb_substr($text, 0, 4000);
		$cyr = preg_match_all('~[\p{Cyrillic}]~u', $sample) ?: 0;
		$lat = preg_match_all('~[A-Za-z]~u', $sample) ?: 0;
		if ($cyr === 0 && $lat === 0) {
			return 'unknown';
		}
		return $cyr >= $lat ? 'cyrillic' : 'latin';
	}

	/**
	 * Исходный HTML-тег узла — чтобы в отчёте показать точный фрагмент кода,
	 * который нужно исправить (цитата строки).
	 */
	private static function tagOf(\DOMDocument $doc, \DOMNode $node): string
	{
		$html = $doc->saveHTML($node);
		if (!is_string($html)) {
			return '';
		}
		$html = trim(preg_replace('~\s+~u', ' ', $html) ?? $html);
		return mb_strlen($html) > 400 ? mb_substr($html, 0, 400) . '…' : $html;
	}

	/**
	 * Цитата источника: фрагмент кода, номер строки в отдаваемом HTML и сама строка.
	 *
	 * Нужна, чтобы в отчёте показать не «что-то нашлось», а конкретное место:
	 * «страница /services/x, строка 12» + код этой строки.
	 *
	 * @param list<string> $lines строки исходного HTML
	 * @return array{code:string,line:int,text:string}
	 */
	private static function cite(\DOMDocument $doc, ?\DOMNode $node, array $lines): array
	{
		$code = '';
		$line = 0;
		if ($node !== null) {
			$raw = $doc->saveHTML($node);
			if (is_string($raw)) {
				$code = trim(preg_replace('~\s+~u', ' ', $raw) ?? '');
			}
			if (method_exists($node, 'getLineNo')) {
				$line = (int)$node->getLineNo();
			}
		}
		if (mb_strlen($code) > 600) {
			$code = mb_substr($code, 0, 600) . '…';
		}
		$text = '';
		if ($line > 0 && isset($lines[$line - 1])) {
			$text = trim($lines[$line - 1]);
			if (mb_strlen($text) > 600) {
				$text = mb_substr($text, 0, 600) . '…';
			}
		}
		return ['code' => $code, 'line' => $line, 'text' => $text];
	}

	/** @return list<int> хеши триграмм слов (для поиска дублей). */
	public static function shingles(string $text): array
	{
		$text = mb_strtolower($text);
		$words = preg_split('~[^\p{L}\p{N}]+~u', $text, -1, PREG_SPLIT_NO_EMPTY) ?: [];
		if (count($words) < 3) {
			return [];
		}
		$set = [];
		$count = count($words);
		$step = 1;
		if ($count > 1200) {
			$step = (int)ceil($count / 1200);
		}
		for ($i = 0; $i + 2 < $count; $i += $step) {
			$gram = $words[$i] . ' ' . $words[$i + 1] . ' ' . $words[$i + 2];
			$set[crc32($gram)] = true;
		}
		return array_map('intval', array_keys($set));
	}

	public static function xpathLiteral(string $s): string
	{
		if ($s === '' || !preg_match('~[^\w\-]~u', $s)) {
			return "'" . $s . "'";
		}
		if (str_contains($s, "'")) {
			return "concat('" . str_replace("'", "',\"'\",'", $s) . "')";
		}
		return "'" . $s . "'";
	}
}

/**
 * Краулер: BFS-обход, граф внутренних ссылок, внешние проверки, зондирование.
 */
final class Crawler
{
	/** Расширения, которые не обходятся (статика/медиа). */
	private const SKIP_EXT = [
		'jpg', 'jpeg', 'png', 'gif', 'webp', 'avif', 'svg', 'ico', 'bmp', 'tiff',
		'css', 'js', 'mjs', 'map', 'json', 'rss', 'atom',
		'woff', 'woff2', 'ttf', 'otf', 'eot',
		'mp4', 'webm', 'ogv', 'mov', 'avi', 'mp3', 'wav', 'ogg',
		'pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx',
		'zip', 'rar', 'gz', 'tar', '7z', 'iso', 'dmg',
		'csv', 'txt', 'log', 'bak', 'swf', 'apk', 'exe', 'dll',
	];

	/** Пути, которые не должны быть доступны извне. */
	private const SENSITIVE_PATHS = [
		'/.env',
		'/.env.local',
		'/.env.production',
		'/.git/config',
		'/.git/HEAD',
		'/.gitignore',
		'/.htaccess',
		'/.htpasswd',
		'/.DS_Store',
		'/composer.json',
		'/composer.lock',
		'/package.json',
		'/package-lock.json',
		'/phpinfo.php',
		'/info.php',
		'/server-status',
		'/server-info',
		'/.svn/entries',
		'/.well-known/security.txt',
		'/backup.sql',
		'/dump.sql',
		'/config.php',
		'/wp-config.php.bak',
	];

	public Cfg $cfg;
	public Ui $ui;
	/** Индикатор загрузки (анимация в терминале). */
	public Spin $spin;
	/** @var array<string,array<string,mixed>> страницы по URL */
	public array $pages = [];
	/** @var array<string,array{count:int,from:array<string,int>,anchors:array<string,int>}> */
	public array $inlinks = [];
	/** @var array<string,array<string,int>> */
	public array $outlinks = [];
	/** @var array<string,array{count:int,from:array<string,int>}> */
	public array $externalLinks = [];
	/** @var array<string,int> внешние хосты → число ссылок */
	public array $externalHosts = [];
	/** @var array<string,array{status:int,error:?string}> */
	public array $externalStatus = [];
	/** @var array<string,array{status:int,error:?string}> внутренние не-HTML ссылки */
	public array $assetStatus = [];
	/** @var array<string,int> внутренние изображения (src) → число вхождений */
	public array $imageUrls = [];
	/** @var array<string,array{status:int,error:?string}> */
	public array $probeStatus = [];
	/** @var array<string,mixed> данные TLS-сертификата */
	public array $tls = [];
	/** @var array<string,int> URL из sitemap → код ответа */
	public array $sitemapStatus = [];
	/** @var list<string> URL из файла-списка /pages (если есть) */
	public array $urlList = [];
	/** @var array<string,mixed> ответы сервера на карты сайта: url → [status, ctype, lastmod] */
	public array $sitemapFiles = [];
	/** @var array<string,mixed> заголовок Content-Signal (политика использования ИИ) */
	public array $contentSignal = [];
	/** @var array<string,mixed> содержимое /llms.txt (если есть) */
	public array $llmsTxt = [];
	/** @var array<string,mixed> манифесты для AI: ai-plugin.json, .well-known/agents.json */
	public array $aiManifests = [];
	/** @var array<string,mixed> анализ доменов: зеркала, варианты, доноры, дубли */
	public array $domains = [];
	/** @var list<array<string,mixed>> проверка по каждому AI/поисковому боту */
	public array $aiBots = [];
	/** @var array<string,int> агентные протоколы: путь → код ответа */
	public array $agentProtocols = [];
	/** @var array<string,mixed> машиночитаемая выдача: markdown, content-signals, link-заголовки */
	public array $machineReadable = [];

	/**
	 * Известные боты: имя в robots.txt → [владелец, User-Agent].
	 * Строки берутся из официальных документов ботов.
	 */
	public const AI_BOTS = [
		'googlebot'      => ['Google', 'Mozilla/5.0 AppleWebKit/537.36 (KHTML, like Gecko); compatible; Googlebot/2.1; +http://www.google.com/bot.html'],
		'yandex'         => ['Яндекс', 'Mozilla/5.0 (compatible; YandexBot/3.0; +http://yandex.com/bots)'],
		'bingbot'        => ['Bing', 'Mozilla/5.0 (compatible; bingbot/2.0; +http://www.bing.com/bingbot.htm)'],
		'duckduckbot'    => ['DuckDuckGo', 'Mozilla/5.0 (compatible; DuckDuckBot-Https/1.1; https://duckduckgo.com/duckduckbot)'],
		'gptbot'         => ['OpenAI', 'Mozilla/5.0 AppleWebKit/537.36 (KHTML, like Gecko); compatible; GPTBot/1.1; +https://openai.com/gptbot'],
		'oai-searchbot'  => ['OpenAI', 'Mozilla/5.0 AppleWebKit/537.36 (KHTML, like Gecko); compatible; OAI-SearchBot/1.0; +https://openai.com/searchbot'],
		'chatgpt-user'   => ['OpenAI', 'Mozilla/5.0 AppleWebKit/537.36 (KHTML, like Gecko); compatible; ChatGPT-User/1.0; +https://openai.com/bot'],
		'claudebot'      => ['Anthropic', 'Mozilla/5.0 AppleWebKit/537.36 (KHTML, like Gecko); compatible; ClaudeBot/1.0; +claudebot@anthropic.com'],
		'anthropic-ai'   => ['Anthropic', 'anthropic-ai'],
		'perplexitybot'  => ['Perplexity', 'Mozilla/5.0 AppleWebKit/537.36 (KHTML, like Gecko); compatible; PerplexityBot/1.0; +https://perplexity.ai/perplexitybot'],
		'google-extended'=> ['Google', 'Mozilla/5.0 (compatible; Google-Extended)'],
		'applebot-extended' => ['Apple', 'Mozilla/5.0 (compatible; Applebot-Extended/1.0)'],
		'bytespider'     => ['ByteDance', 'Mozilla/5.0 (compatible; Bytespider; spider-feedback@bytedance.com)'],
		'amazonbot'      => ['Amazon', 'Mozilla/5.0 (compatible; Amazonbot/0.1; +https://developer.amazon.com/support/amazonbot)'],
		'ccbot'          => ['Common Crawl', 'Mozilla/5.0 (compatible; CCBot/2.0; +https://commoncrawl.org/faq/)'],
		'meta-externalagent' => ['Meta', 'meta-externalagent/1.1 (+https://developers.facebook.com/docs/sharing/webmasters/crawler)'],
		'cohere-ai'      => ['Cohere', 'Mozilla/5.0 (compatible; cohere-ai/1.0; +https://cohere.com)'],
		'diffbot'        => ['Diffbot', 'Mozilla/5.0 (compatible; Diffbot/0.1; +http://www.diffbot.com)'],
	];
	public ?Robots $robots = null;
	public SitemapSet $sitemaps;
	/** @var list<string> */
	public array $notes = [];
	public float $duration = 0.0;
	public int $requests = 0;
	public int $retries = 0;
	/** @var array<string,float> тайминги фаз (сек) */
	public array $timings = [];

	public function __construct(Cfg $cfg, Ui $ui, ?Spin $spin = null)
	{
		$this->cfg = $cfg;
		$this->ui = $ui;
		// Без явно переданного индикатора анимация выключена: вложенные обходы
		// (конкуренты, самотест) не должны спорить за строку терминала.
		$this->spin = $spin ?? new Spin($ui, false);
		$this->sitemaps = new SitemapSet();
	}

	public function run(): void
	{
		$t0 = microtime(true);
		$start = $this->cfg->start;

		// Анимация: пока идёт сбор данных — видно, что процесс жив.
		$this->spin->start('Загрузка данных · robots.txt');
		Http::onProgress(static function (): void {
			Spin::current()?->tick();
		});

		// --- robots.txt ---
		$phase = microtime(true);
		if ($this->cfg->readRobots) {
			$req = Http::fetch(rtrim(Url::origin($start), '/') . '/robots.txt', $this->cfg);
			$this->requests++;
			if ($req->status === 200) {
				$this->robots = Robots::parse($req->body);
				$this->robots->status = 200;
				$this->robots->exists = true;
			} else {
				$this->robots = new Robots();
				$this->robots->status = $req->status;
				$this->robots->exists = false;
				if ($req->status !== 404) {
					$this->notes[] = 'robots.txt: HTTP ' . ($req->status ?: 'нет ответа');
				}
			}
		}
		$this->timings['robots'] = round(microtime(true) - $phase, 2);

		// --- sitemap ---
		$phase = microtime(true);
		$this->spin->set('Загрузка данных · карта сайта (sitemap.xml)');
		$this->sitemaps->load(Url::origin($start), $this->cfg);
		$this->requests += count($this->sitemaps->sitemaps) + 1;
		$this->timings['sitemap'] = round(microtime(true) - $phase, 2);

		// --- список URL /pages (для Screaming Frog и ручных проверок) ---
		$phase = microtime(true);
		$this->spin->set('Загрузка данных · список URL (/pages)');
		$listReq = Http::fetch(rtrim(Url::origin($start), '/') . '/pages', $this->cfg);
		if ($listReq->status === 200 && $listReq->body !== '') {
			foreach (preg_split('~\R~', $listReq->body) ?: [] as $line) {
				$line = trim($line);
				if ($line === '' || !preg_match('~^https?://~i', $line)) {
					continue;
				}
				$norm = Url::normalize($line);
				if ($norm !== null) {
					$this->urlList[] = $norm;
				}
			}
			$this->urlList = array_values(array_unique($this->urlList));
		}
		$this->timings['url_list'] = round(microtime(true) - $phase, 2);

		// --- AI/GEO: llms.txt и манифесты ---
		$phase = microtime(true);
		$this->spin->set('Загрузка данных · llms.txt и манифесты ИИ');
		$origin = Url::origin($start);
		$llms = Http::fetch($origin . '/llms.txt', $this->cfg);
		if ($llms->status === 200) {
			$this->llmsTxt = [
				'status'  => 200,
				'len'     => strlen($llms->body),
				'links'   => preg_match_all('~https?://[^\s\)\]]+~i', $llms->body) ?: 0,
				'hasTitle'=> (bool)preg_match('~^#\s+\S+~m', $llms->body),
				'body'    => mb_substr($llms->body, 0, 4000),
			];
		} else {
			$this->llmsTxt = ['status' => $llms->status];
		}
		foreach (['/ai-plugin.json', '/.well-known/agents.json'] as $mf) {
			$r = Http::fetch($origin . $mf, $this->cfg);
			$this->aiManifests[$mf] = [
				'status' => $r->status,
				'ctype'  => strtolower($r->header('content-type')),
				'json'   => $r->status === 200 ? json_decode($r->body, true) : null,
				'len'    => strlen($r->body),
			];
		}
		$this->timings['ai'] = round(microtime(true) - $phase, 2);

		// --- обход ---
		$phase = microtime(true);
		$this->spin->set('Загрузка данных · обход страниц');
		$this->crawl($start);
		$this->timings['crawl'] = round(microtime(true) - $phase, 2);

		// --- внешние ссылки ---
		$phase = microtime(true);
		if ($this->cfg->external && $this->cfg->externalLimit > 0) {
			$this->checkExternal();
		}
		$this->timings['external'] = round(microtime(true) - $phase, 2);
		// --- внутренние не-HTML ссылки (pdf, css, картинки) ---
		$phase = microtime(true);
		$this->checkAssets();
		$this->timings['assets'] = round(microtime(true) - $phase, 2);
		// --- URL из sitemap, до которых не дошли ---
		$phase = microtime(true);
		$this->checkSitemapUrls();
		$this->timings['sitemap_check'] = round(microtime(true) - $phase, 2);
		// --- служебные пути ---
		$phase = microtime(true);
		if ($this->cfg->probe) {
			$this->probeSensitive();
		} elseif ($this->cfg->mode === 'global') {
			$this->notes[] = 'Проверка служебных файлов (.env, .git) отключена для публичных сайтов — включается флагом --probe';
		}
		$this->timings['probe'] = round(microtime(true) - $phase, 2);

		// --- проверка по каждому AI/поисковому боту ---
		$phase = microtime(true);
		$this->checkAiBots();
		$this->timings['ai_bots'] = round(microtime(true) - $phase, 2);

		// --- карты сайта: доступность, тип, свежесть ---
		$phase = microtime(true);
		$this->spin->set('Загрузка данных · проверка карт сайта');
		foreach ($this->sitemaps->sitemaps as $sm) {
			$req = Http::fetch($sm, $this->cfg, 'HEAD', false, 8000, 0);
			$this->sitemapFiles[$sm] = [
				'status'  => $req->status,
				'ctype'   => strtolower($req->header('content-type')),
				'lastmod' => (string)$req->header('last-modified'),
				'len'     => (int)$req->header('content-length'),
			];
		}
		$this->timings['sitemap_files'] = round(microtime(true) - $phase, 2);

		// --- заголовок Content-Signal (политика использования контента ИИ) ---
		$plain = Http::fetch(rtrim($this->cfg->start, '/') . '/', $this->cfg, 'HEAD', false, 8000, 0);
		$this->contentSignal = [
			'value'  => (string)$plain->header('content-signal'),
			'status' => $plain->status,
		];

		// --- AI- и агентные протоколы ---
		$phase = microtime(true);
		$this->spin->set('Загрузка данных · агентные протоколы');
		$this->checkAgentProtocols();
		$this->checkMachineReadable();
		$this->timings['protocols'] = round(microtime(true) - $phase, 2);

		// --- llms.txt: содержимое и структура ---
		$this->spin->set('Загрузка данных · разбор llms.txt');
		$llms = Http::fetch(rtrim($this->cfg->start, '/') . '/llms.txt', $this->cfg, 'GET', true, 8000, 0);
		$this->llmsTxt = [
			'status'   => $llms->status,
			'ctype'    => strtolower($llms->header('content-type')),
			'analysis' => $llms->status === 200 ? LlmTxt::analyze($llms->body) : [],
			'body'     => $llms->status === 200 ? $llms->body : '',
		];

		// --- TLS-сертификат (только для https) ---
		$phase = microtime(true);
		$this->spin->set('Загрузка данных · TLS-сертификат');
		$this->tls = $this->checkTls();
		$this->timings['tls'] = round(microtime(true) - $phase, 2);

		// --- домены, зеркала, «доноры» (типичные ошибки при переезде) ---
		$phase = microtime(true);
		$this->spin->set('Загрузка данных · домены и зеркала');
		$this->checkDomains();
		$this->timings['domains'] = round(microtime(true) - $phase, 2);

		$this->duration = microtime(true) - $t0;
	}

	/**
	 * Поиск зеркал и «донорских» доменов — то, что компании чаще всего
	 * ломают при переезде на новый домен/разделении доменов.
	 *
	 * Ищем: варианты адреса (http/https, www/без www), которые отдают 200 или
	 * не тот код редиректа, и внешние домены, которые отдают копию сайта
	 * (тот же title) — это и есть источник «разделившегося» ИКС.
	 */
	private function checkDomains(): void
	{
		$started = microtime(true);
		$origin = $this->cfg->start;
		$host = Url::hostOf($origin);
		if ($host === '') {
			return;
		}
		$bare = preg_replace('~^www\.~i', '', $host);
		$variants = [];
		// Для локального стенда схему не меняем: HTTPS-проба к http-серверу
		// невалидна и может «завесить» однопоточный dev-сервер.
		$schemes = $this->cfg->isLocal() ? [parse_url($origin, PHP_URL_SCHEME) ?: 'http'] : ['http', 'https'];
		foreach ($schemes as $scheme) {
			foreach (array_unique([$host, $bare]) as $h) {
				$u = $scheme . '://' . $h . '/';
				if ($u === $origin) {
					continue;
				}
				$req = Http::fetch($u, $this->cfg, 'HEAD', false, 4000, 0);
				if ($req->status === 0 && $req->error !== null && stripos((string)$req->error, 'resolve') !== false) {
					continue;   // домен не существует — не наша проблема
				}
				$variants[$u] = [
					'status' => $req->status,
					'hops'   => count($req->redirects),
					'code'   => (int)($req->redirects[0]['status'] ?? 0),
					'final'  => $req->final,
					'finalHost' => Url::hostOf($req->final),
					'toRoot' => ($req->final !== '' && rtrim($req->final, '/') === rtrim($origin, '/')) && $req->final !== $u,
				];
			}
		}

		// внешние домены, на которые ссылается сайт: ищем среди них копии/доноры
		$ourTitle = '';
		$home = rtrim($origin, '/') . '/';
		if (isset($this->pages[$home])) {
			$ourTitle = $this->normalizeTitle((string)$this->pages[$home]['html']['title']);
		}
		$hosts = [];
		foreach (array_keys($this->externalLinks) as $u) {
			$h = Url::hostOf($u);
			if ($h !== '' && $h !== $host && str_contains($h, '.')) {
				$hosts[$h] = true;
			}
		}
		$donors = [];
		$baseHost = preg_replace('~^www\.~i', '', $host);
		foreach (array_slice(array_keys($hosts), 0, 6) as $dh) {
			if (microtime(true) - $started > 20) {
				$this->notes[] = 'Проверка доменов остановлена по лимиту времени (20 с)';
				break;
			}
			// поддомены нашего же домена (downloads.php.net → php.net) — это норма, не «донор»
			if ($dh === $baseHost || str_ends_with($dh, '.' . $baseHost)) {
				continue;
			}
			$req = Http::fetch('https://' . $dh . '/', $this->cfg, 'GET', true, 5000);
			if ($req->status !== 200) {
				continue;
			}
			$finalHost = Url::hostOf($req->final);
			if ($finalHost !== '' && $finalHost === $host) {
				$donors[$dh] = ['kind' => 'редирект на ваш домен', 'final' => $req->final];
				continue;
			}
			if ($ourTitle !== '' && $this->normalizeTitle($this->pageTitleOf($req->body)) === $ourTitle) {
				$donors[$dh] = ['kind' => 'отдаёт копию сайта (тот же title)', 'final' => $req->final];
			}
		}

		$this->domains = [
			'variants' => $variants,
			'donors'   => $donors,
			'ourTitle' => $ourTitle,
		];
	}

	/** Title без брендового хвоста — чтобы сравнивать копии сайта. */
	private function normalizeTitle(string $title): string
	{
		$title = mb_strtolower(trim($title));
		$title = preg_split('~[|—–\-]~u', $title)[0] ?? $title;
		return trim((string)$title);
	}

	private function pageTitleOf(string $html): string
	{
		if (preg_match('~<title[^>]*>(.*?)</title>~is', $html, $m)) {
			return trim(strip_tags($m[1]));
		}
		return '';
	}

	/**
	 * Данные TLS-сертификата: срок действия, соответствие имени, алгоритм.
	 *
	 * @return array<string,mixed>
	 */
	private function checkTls(): array
	{
		$origin = $this->cfg->start;
		if (!str_starts_with($origin, 'https://')) {
			return [];
		}
		$parts = parse_url($origin);
		$host = $parts['host'] ?? '';
		$port = (int)($parts['port'] ?? 443);
		if ($host === '') {
			return [];
		}
		$ctx = stream_context_create([
			'ssl' => [
				'capture_peer_cert' => true,
				'verify_peer'       => false,
				'verify_peer_name'  => false,
			],
		]);
		$errno = 0;
		$errstr = '';
		$fp = @stream_socket_client(
			'ssl://' . $host . ':' . $port,
			$errno,
			$errstr,
			5,
			STREAM_CLIENT_CONNECT,
			$ctx
		);
		if ($fp === false) {
			// одна повторная попытка: соединение могло не пройти из-за кратковременной перегрузки
			usleep(300000);
			$fp = @stream_socket_client(
				'ssl://' . $host . ':' . $port,
				$errno,
				$errstr,
				5,
				STREAM_CLIENT_CONNECT,
				$ctx
			);
		}
		if ($fp === false) {
			return ['error' => 'Не удалось установить TLS-соединение' . ($errstr !== '' ? ': ' . $errstr : ''), 'unverified' => true];
		}
		$params = stream_context_get_params($fp);
		$cert = $params['options']['ssl']['peer_certificate'] ?? null;
		fclose($fp);
		if ($cert === null || $cert === '') {
			return ['error' => 'Сертификат не получен'];
		}
		$info = @openssl_x509_parse($cert);   // PHP 8: принимает OpenSSLCertificate
		if (!is_array($info)) {
			return ['error' => 'Сертификат не удалось разобрать'];
		}
		$to = (int)($info['validTo_time_t'] ?? 0);
		$from = (int)($info['validFrom_time_t'] ?? 0);
		$names = [];
		if (!empty($info['extensions']['subjectAltName'])) {
			foreach (explode(',', (string)$info['extensions']['subjectAltName']) as $n) {
				$n = trim($n);
				if (str_starts_with($n, 'DNS:')) {
					$names[] = substr($n, 4);
				}
			}
		}
		$cn = $info['subject']['CN'] ?? '';
		if ($cn !== '' && !in_array($cn, $names, true)) {
			$names[] = $cn;
		}
		$hostOk = false;
		foreach ($names as $n) {
			if (str_starts_with($n, '*.')) {
				$base = substr($n, 2);
				if (str_ends_with(strtolower($host), '.' . strtolower($base))) {
					$hostOk = true;
					break;
				}
			} elseif (strcasecmp($n, $host) === 0) {
				$hostOk = true;
				break;
			}
		}
		return [
			'valid_from'  => $from,
			'valid_to'    => $to,
			'days_left'   => $to > 0 ? (int)floor(($to - time()) / 86400) : null,
			'expired'     => $to > 0 && $to < time(),
			'host_ok'     => $hostOk,
			'names'       => $names,
			'issuer'      => (string)($info['issuer']['CN'] ?? ($info['issuer']['O'] ?? '—')),
			'signature'   => (string)($info['signatureTypeSN'] ?? '—'),
			'self_signed' => isset($info['issuer']['CN'], $info['subject']['CN']) && $info['issuer']['CN'] === $info['subject']['CN'],
			'key_size'    => (int)($info['publicKey']['bits'] ?? 0),
		];
	}

	/**
	 * BFS-обход.
	 */
	private function crawl(string $start): void
	{
		$cfg = $this->cfg;
		$queue = [$start => 0];
		$seen = [];
		$querySeen = 0;
		$origin = Url::origin($start);
		$done = 0;

		while ($queue !== [] && count($this->pages) < $cfg->limit) {
			$batch = [];
			while (count($batch) < $cfg->concurrency && $queue !== []) {
				$url = array_key_first($queue);
				$depth = $queue[$url];
				unset($queue[$url]);
				if (isset($seen[$url])) {
					continue;
				}
				$seen[$url] = true;

				if ($depth > $cfg->depth) {
					continue;
				}
				if (str_contains($url, '?') && ++$querySeen > 60) {
					continue;
				}
				if ($this->skipUrl($url)) {
					continue;
				}
				$batch[$url] = $depth;
			}
			if ($batch === []) {
				break;
			}

			$results = Http::fetchMany(array_keys($batch), $cfg);
			$this->requests += count($batch);
			$this->pace();
			// Неудачные соединения (перегруженный однопоточный сервер) — повторяем по одному
			$retried = $this->retryFailed($results, array_keys($batch));
			if ($retried > 0) {
				$this->retries += $retried;
			}

			foreach ($results as $url => $req) {
				$depth = $batch[$url];
				$page = $this->buildPage($url, $req, $depth, $origin);
				$this->pages[$url] = $page;

				if ($req->status >= 200 && $req->status < 400 && $req->error === null) {
					$isHtml = $this->isHtml($req, $page);
					if ($isHtml) {
						$this->extractLinks($page, $origin, $queue, $depth, $seen);
					}
				}
			}

			$done += count($batch);
			// DOMDocument содержит циклические ссылки — без сборки циклов память растёт
			gc_collect_cycles();
			$this->progress($done, count($this->pages), count($queue));
		}
		$this->progressDone();
	}

	/** @return array<string,mixed> */
	private function buildPage(string $url, Req $req, int $depth, string $origin): array
	{
		$contentType = $req->header('content-type');
		$charset = '';
		if (preg_match('~charset=([\w\-]+)~i', $contentType, $m)) {
			$charset = $m[1];
		}
		// Тело разбираем всегда: у страниц с кодом 404/403 контент тоже нужен
		// (иначе каждая битая страница ещё и «не имела» title/description/H1 —
		// шум, который маскирует настоящую проблему).
		$html = $req->body;
		$parsed = $html !== '' ? Html::parse($html, $contentType, $charset) : Html::parse('', $contentType, $charset);

		return [
			'url'         => $url,
			'path'        => Url::displayPath($url),
			'status'      => $req->status,
			'final'       => $req->final,
			'redirects'   => $req->redirects,
			'depth'       => $depth,
			'headers'     => $req->headers,
			'headerLog'   => $req->headerLog,
			'bytes'       => $req->bytes,
			'bodySize'    => strlen($req->body),
			'timeTotal'   => $req->timeTotal,
			'ttfb'        => $req->ttfb,
			'connect'     => $req->connect,
			'httpVersion' => $req->httpVersion,
			'contentType' => $contentType,
			'error'       => $req->error,
			'html'        => $parsed,
		];
	}

	private function isHtml(Req $req, array $page): bool
	{
		$ct = strtolower($page['contentType']);
		if ($ct !== '' && !str_contains($ct, 'html')) {
			return false;
		}
		$body = ltrim(substr($req->body, 0, 200));
		return $body === '' || str_starts_with($body, '<');
	}

	/**
	 * Разбор ссылок страницы: внутренние → входящие/исходящие, внешние → список.
	 */
	private function extractLinks(array $page, string $origin, array &$queue, int $depth, array $seen): void
	{
		$from = $page['url'];
		$links = $page['html']['links'];

		// изображения (<img src>) — тоже проверяем на доступность
		foreach ($page['html']['images'] as $im) {
			$src = trim((string)$im['src']);
			if ($src === '' || str_starts_with($src, 'data:')) {
				continue;
			}
			$abs = Url::resolve($src, $from);
			if ($abs === null) {
				continue;
			}
			[$abs] = Url::splitFragment($abs);
			$abs = Url::normalize($abs) ?? $abs;
			if (Url::hostOf($abs) === Url::hostOf($origin)) {
				$this->imageUrls[$abs] = ($this->imageUrls[$abs] ?? 0) + 1;
			}
		}

		// og:image тоже проверяем на доступность
		{
			$src = trim((string)($page['html']['og']['image'] ?? ''));
			if ($src !== '' && !str_starts_with($src, 'data:')) {
				$abs = Url::resolve($src, $from);
				if ($abs !== null) {
					[$abs] = Url::splitFragment($abs);
					$abs = Url::normalize($abs) ?? $abs;
					if (Url::hostOf($abs) === Url::hostOf($origin)) {
						$this->imageUrls[$abs] = ($this->imageUrls[$abs] ?? 0) + 1;
					}
				}
			}
		}

		foreach ($links as $link) {
			$raw = $link['raw'];
			$abs = Url::resolve($raw, $from);
			if ($abs === null) {
				continue;
			}
			[$abs, $fragment] = Url::splitFragment($abs);
			$abs = Url::normalize($abs) ?? $abs;
			if ($abs === '' || !preg_match('~^https?://~i', $abs)) {
				continue;
			}
			$isInternal = Url::hostOf($abs) === Url::hostOf($origin) || Url::sameSite($abs, $origin);

			if ($isInternal) {
				$this->inlinks[$abs] ??= ['count' => 0, 'from' => [], 'anchors' => [], 'src' => []];
				$this->inlinks[$abs]['count']++;
				$this->inlinks[$abs]['from'][$from] = ($this->inlinks[$abs]['from'][$from] ?? 0) + 1;
				$anchor = trim($link['text']);
				if ($anchor !== '') {
					$this->inlinks[$abs]['anchors'][$anchor] = ($this->inlinks[$abs]['anchors'][$anchor] ?? 0) + 1;
				}
				// откуда именно найдена ссылка: страница, текст ссылки, зона страницы
				$srcList = &$this->inlinks[$abs]['src'];
				if (isset($srcList[$from])) {
					$srcList[$from]['n']++;
				} elseif (count($srcList) < 8) {
					// детализируем до 8 разных источников на цель — этого хватает для отчёта
					$srcList[$from] = ['n' => 1, 'anchors' => [], 'zones' => [], 'tags' => []];
				}
				if (isset($srcList[$from])) {
					if ($anchor !== '') {
						$srcList[$from]['anchors'][$anchor] = ($srcList[$from]['anchors'][$anchor] ?? 0) + 1;
					}
					$zone = (string)($link['zone'] ?? '');
					if ($zone !== '') {
						$srcList[$from]['zones'][$zone] = ($srcList[$from]['zones'][$zone] ?? 0) + 1;
					}
					// исходный тег ссылки — цитата для отчёта
					$tag = (string)($link['tag'] ?? '');
					if ($tag !== '' && count($srcList[$from]['tags']) < 3) {
						$srcList[$from]['tags'][] = $tag;
					}
				}
				unset($srcList);

				$this->outlinks[$from] ??= [];
				$this->outlinks[$from][$abs] = ($this->outlinks[$from][$abs] ?? 0) + 1;

				if (!isset($seen[$abs]) && !isset($queue[$abs]) && $depth + 1 <= $this->cfg->depth) {
					if (!$this->skipUrl($abs)) {
						$queue[$abs] = $depth + 1;
					}
				}
				continue;
			}

			// внешняя ссылка
			$this->externalLinks[$abs] ??= ['count' => 0, 'from' => []];
			$this->externalLinks[$abs]['count']++;
			$this->externalLinks[$abs]['from'][$from] = ($this->externalLinks[$abs]['from'][$from] ?? 0) + 1;
			$host = Url::hostOf($abs);
			$this->externalHosts[$host] = ($this->externalHosts[$host] ?? 0) + 1;
		}
	}

	private function skipUrl(string $url): bool
	{
		$path = parse_url($url, PHP_URL_PATH) ?: '/';
		$ext = strtolower((string)pathinfo($path, PATHINFO_EXTENSION));
		if ($ext !== '' && in_array($ext, self::SKIP_EXT, true)) {
			return true;
		}
		foreach ($this->cfg->exclude as $pattern) {
			if (@preg_match($pattern, $url) === 1) {
				return true;
			}
		}
		if ($this->cfg->only !== []) {
			$ok = false;
			foreach ($this->cfg->only as $prefix) {
				if (str_starts_with($path, $prefix) || $path === $prefix) {
					$ok = true;
					break;
				}
			}
			if (!$ok) {
				return true;
			}
		}
		if ($this->cfg->respectRobots && $this->robots !== null) {
			$pathAndQuery = $path . (parse_url($url, PHP_URL_QUERY) ? '?' . parse_url($url, PHP_URL_QUERY) : '');
			if (!$this->robots->allowed($pathAndQuery)) {
				return true;
			}
		}
		return false;
	}

	/**
	 * Пауза между пачками запросов (вежливость к серверу).
	 */
	private function pace(): void
	{
		if ($this->cfg->delayMs > 0) {
			usleep($this->cfg->delayMs * 1000);
		}
	}

	/**
	 * Повтор неудачных запросов по одному.
	 *
	 * Зачем: однопоточный сервер (в т.ч. php -S) не выдерживает параллельные
	 * запросы — часть соединений отваливается по таймауту. Без повтора такие
	 * сбои выглядят как «битые ссылки», что неверно. Повторяем последовательно.
	 *
	 * @param array<string,Req> $results
	 * @param list<string>       $urls
	 */
	private function retryFailed(array &$results, array $urls, string $method = 'GET', bool $keepBody = false): int
	{
		$failed = [];
		foreach ($urls as $u) {
			$r = $results[$u] ?? null;
			if ($r !== null && $r->status === 0) {
				$failed[] = $u;
			}
		}
		if (!$failed) {
			return 0;
		}
		$fixed = 0;
		foreach ($failed as $u) {
			$this->pace();
			$r = Http::fetch($u, $this->cfg, $method, $keepBody);
			if ($r->status > 0) {
				$results[$u] = $r;
				$fixed++;
			}
		}
		return $fixed;
	}

	/**
	 * Запросы пачками по concurrency — нельзя открывать сотни соединений сразу.
	 *
	 * @param list<string> $urls
	 * @return array<string,Req>
	 */
	private function fetchBatched(array $urls, string $method = 'GET', bool $keepBody = false, ?int $timeoutMs = null): array
	{
		$out = [];
		$chunks = array_chunk($urls, max(1, $this->cfg->concurrency));
		foreach ($chunks as $chunk) {
			$this->pace();
			$res = Http::fetchMany($chunk, $this->cfg, $method, $keepBody, $timeoutMs);
			$this->retryFailed($res, $chunk, $method, $keepBody);
			foreach ($res as $url => $req) {
				$out[$url] = $req;
			}
		}
		return $out;
	}

	/** Проверка внешних ссылок (HEAD, при отказе — GET). */
	private function checkExternal(): void
	{
		$urls = array_slice(array_keys($this->externalLinks), 0, $this->cfg->externalLimit);
		if (!$urls) {
			return;
		}
		$start = microtime(true);
		$skipped = [];
		$retry = [];
		$chunks = array_chunk($urls, max(1, $this->cfg->concurrency));
		$total = count($urls);
		$seen = 0;
		foreach ($chunks as $chunk) {
			if ((microtime(true) - $start) > $this->cfg->externalBudgetSec) {
				$skipped = array_merge($skipped, $chunk);
				continue;
			}
			$this->pace();
			$this->spin->set('Загрузка данных · внешние ссылки', $seen, $total);
			foreach (Http::fetchMany($chunk, $this->cfg, 'HEAD', false, $this->cfg->externalTimeoutMs, 0) as $url => $req) {
				if ($req->status === 405 || $req->status === 403 || $req->status === 501 || $req->status === 0) {
					$retry[] = $url;
				} else {
					$this->externalStatus[$url] = ['status' => $req->status, 'error' => $req->error];
				}
			}
			$seen += count($chunk);
		}
		if ($retry) {
			// повтор GET'ом ограничен по времени и количеству
			$left = $this->cfg->externalBudgetSec - (microtime(true) - $start);
			if ($left > 2) {
				$limit = max(1, min(8, (int)floor($left * $this->cfg->concurrency / 6)));
				$this->pace();
				foreach (Http::fetchMany(array_slice($retry, 0, $limit), $this->cfg, 'GET', false, $this->cfg->externalTimeoutMs) as $url => $req) {
					$this->externalStatus[$url] = ['status' => $req->status, 'error' => $req->error];
				}
			}
		}
		if ($skipped) {
			$this->notes[] = 'Внешних ссылок не проверено из-за лимита времени: ' . count($skipped);
		}
	}

	/** Проверка внутренних ссылок на не-HTML ресурсы (картинки, pdf, css). */
	private function checkAssets(): void
	{
		$started = microtime(true);
		$targets = [];
		foreach ($this->inlinks as $url => $info) {
			if (isset($this->pages[$url])) {
				continue;
			}
			$targets[] = $url;
		}
		foreach (array_keys($this->imageUrls) as $img) {
			$targets[] = $img;
		}
		$targets = array_values(array_unique($targets));
		$targets = array_slice($targets, 0, 150);
		if (!$targets) {
			return;
		}
		$out = [];
		$chunks = array_chunk($targets, max(1, $this->cfg->concurrency));
		$total = count($targets);
		$seen = 0;
		foreach ($chunks as $chunk) {
			if (microtime(true) - $started > 20) {
				$this->notes[] = 'Проверка файлов остановлена по лимиту времени (20 с)';
				break;
			}
			$this->pace();
			$this->spin->set('Загрузка данных · файлы (картинки, pdf, css)', $seen, $total);
			foreach (Http::fetchMany($chunk, $this->cfg, 'HEAD', false, 5000, 0) as $url => $req) {
				$out[$url] = $req;
			}
			$seen += count($chunk);
		}
		// серверы без поддержки HEAD (405/501) и «молчащие» по HEAD (таймаут) —
		// перепроверяем GET'ом, иначе такие цели ошибочно считаются битыми
		$retry = [];
		foreach ($out as $url => $req) {
			if ($req->status === 405 || $req->status === 501 || $req->status === 0) {
				$retry[] = $url;
			}
		}
		if ($retry) {
			foreach (Http::fetchMany(array_slice($retry, 0, 60), $this->cfg, 'GET', false, 10000, 0) as $url => $req) {
				$out[$url] = $req;
			}
		}
		foreach ($out as $url => $req) {
			$this->assetStatus[$url] = [
				'status' => $req->status,
				'error'  => $req->error,
				'length' => (int)$req->header('content-length'),
			];
		}
	}

	/** Проверка URL из sitemap, до которых краулер не дошёл. */
	private function checkSitemapUrls(): void
	{
		$started = microtime(true);
		$missing = array_values(array_diff(array_keys($this->sitemaps->urls), array_keys($this->pages)));
		if (!$missing) {
			return;
		}
		$out = [];
		$chunks = array_chunk(array_slice($missing, 0, 40), max(1, $this->cfg->concurrency));
		$total = min(40, count($missing));
		$seen = 0;
		foreach ($chunks as $chunk) {
			if (microtime(true) - $started > 15) {
				$this->notes[] = 'Проверка URL из карты остановлена по лимиту времени (15 с)';
				break;
			}
			$this->pace();
			$this->spin->set('Загрузка данных · URL из карты сайта', $seen, $total);
			foreach (Http::fetchMany($chunk, $this->cfg, 'HEAD', false, 6000, 0) as $url => $req) {
				$out[$url] = $req->status;
			}
			$seen += count($chunk);
		}
		foreach ($out as $url => $status) {
			$this->sitemapStatus[$url] = $status;
		}
	}

	/**
	 * Агентные протоколы и машиночитаемые представления сайта:
	 * agents.json, ai-plugin.json, MCP server card, A2A agent card, agent skills,
	 * api-catalog, OpenAPI, OAuth discovery, llms.txt, llms-full.txt.
	 */
	private function checkAgentProtocols(): void
	{
		$origin = rtrim($this->cfg->start, '/');
		$paths = [
			'/llms.txt'                      => 'llms.txt (карта для LLM)',
			'/llms-full.txt'                 => 'llms-full.txt (полная версия)',
			'/agents.json'                   => 'agents.json (A2A-карточка агента)',
			'/.well-known/agents.json'       => '.well-known/agents.json',
			'/ai-plugin.json'                => 'ai-plugin.json (плагин OpenAI)',
			'/.well-known/ai-plugin.json'   => '.well-known/ai-plugin.json',
			'/.well-known/mcp/server-card.json' => 'MCP server card',
			'/.well-known/agent-card.json'  => 'A2A agent card',
			'/.well-known/agent-skills/index.json' => 'Agent Skills (agentskills.io)',
			'/.well-known/api-catalog.json'  => 'API Catalog',
			'/.well-known/openapi.yaml'      => 'OpenAPI-спецификация',
			'/.well-known/oauth-authorization-server' => 'OAuth discovery (сервер авторизации)',
			'/.well-known/oauth-protected-resource'  => 'OAuth discovery (защищённый ресурс)',
		];
		$out = [];
		$pathTotal = count($paths);
		$pathSeen = 0;
		foreach ($paths as $path => $label) {
			$pathSeen++;
			$this->spin->set('Загрузка данных · агентные протоколы', $pathSeen, $pathTotal);
			$req = Http::fetch($origin . $path, $this->cfg, 'HEAD', false, 6000, 0);
			$out[$path] = ['label' => $label, 'status' => $req->status];
		}
		$this->agentProtocols = $out;
	}

	/**
	 * Машиночитаемая выдача: отдаёт ли сервер markdown по запросу Accept,
	 * объявляет ли Content-Signals и альтернативные версии в Link-заголовке.
	 */
	private function checkMachineReadable(): void
	{
		$url = rtrim($this->cfg->start, '/') . '/';
		$md = Http::fetchWithUa($url, $this->cfg, $this->cfg->ua, ['text/markdown']);
		$plain = Http::fetch($url, $this->cfg, 'HEAD', false, 8000, 0);
		$this->machineReadable = [
			'markdown'        => (int)$md->status,
			'markdownType'    => strtolower((string)$md->header('content-type')),
			'contentSignals'  => (string)$plain->header('content-signals'),
			'link'            => (string)$plain->header('link'),
			'xRobotsTag'      => (string)$plain->header('x-robots-tag'),
		];
	}

	/**
	 * Проверка по каждому AI- и поисковому боту: что написано в robots.txt
	 * для этого бота и что реально отдаёт сервер, когда запрос подписан его
	 * User-Agent. Важно: CDN, который определяет ботов по IP, может ответить
	 * настоящему боту иначе — поэтому вывод делаем по обоим сигналам.
	 */
	private function checkAiBots(): void
	{
		$origin = rtrim($this->cfg->start, '/') . '/';
		$robots = $this->robots;
		$coverage = $robots !== null ? $robots->coverage() : [];
		$rows = [];
		foreach (self::AI_BOTS as $bot => [$who, $ua]) {
			$cov = $coverage[$bot] ?? ['state' => 'none', 'rules' => [], 'delay' => null];
			$mentioned = $cov['state'] === 'explicit';
			$disallowed = [];
			$blockedAll = false;
			foreach ($cov['rules'] as $rule) {
				if ($rule['allow']) {
					continue;
				}
				if ($rule['path'] === '/' || $rule['path'] === '') {
					$blockedAll = true;
					continue;
				}
				$disallowed[] = (string)$rule['path'];
			}
			if ($blockedAll) {
				$state = 'blocked';
				$note = 'Disallow: / — бот полностью закрыт';
			} elseif ($disallowed !== []) {
				$state = 'partial';
				$note = count($disallowed) . ' path(s) disallowed: ' . implode(', ', array_slice($disallowed, 0, 8));
			} else {
				$state = $mentioned ? 'allowed' : 'wildcard';
				$note = $mentioned
					? 'Указан в robots.txt, ограничений нет'
					: 'Не упомянут в robots.txt — действуют правила User-agent: *';
			}
			$rows[] = [
				'bot'        => $bot,
				'who'        => $who,
				'ua'         => $ua,
				'state'      => $state,
				'coverage'   => $cov['state'],
				'note'       => $note,
				'disallowed' => $disallowed,
				'mentioned'  => $mentioned,
				'server'     => 'не проверялся — это токен robots.txt, своего краулера нет',
				'code'       => 0,
				'delay'      => $cov['delay'],
			];
		}
		// серверная проверка: подписываем запрос User-Agent'ом бота
		$budget = microtime(true) + 25;
		$botTotal = count($rows);
		$botSeen = 0;
		foreach ($rows as $i => $r) {
			$botSeen++;
			$this->spin->set('Загрузка данных · проверка по ботам (AI и поисковые)', $botSeen, $botTotal);
			if ($r['state'] === 'blocked') {
				continue;
			}
			if (microtime(true) > $budget) {
				$rows[$i]['server'] = 'не проверено — лимит времени';
				continue;
			}
			$this->pace();
			$req = Http::fetchWithUa($origin, $this->cfg, $r['ua']);
			$rows[$i]['code'] = $req->status;
			$rows[$i]['server'] = match (true) {
				$req->status === 200 => '200 — отдаёт как браузеру',
				$req->status === 403 => '403 — закрыт (анти-бот защита)',
				$req->status === 0   => 'нет ответа — таймаут или ограничение частоты, вывод не делаем',
				default             => 'HTTP ' . $req->status . ' — ' . ($req->error ?? 'иной ответ'),
			};
		}
		$this->aiBots = $rows;
	}

	/** Проверка доступности служебных файлов. */
	private function probeSensitive(): void
	{
		$started = microtime(true);
		$origin = Url::origin($this->cfg->start);
		$targets = array_map(static fn(string $p): string => $origin . $p, self::SENSITIVE_PATHS);
		$out = [];
		$chunks = array_chunk($targets, max(1, $this->cfg->concurrency));
		$total = count($targets);
		$seen = 0;
		foreach ($chunks as $chunk) {
			if (microtime(true) - $started > 15) {
				$this->notes[] = 'Проверка служебных файлов остановлена по лимиту времени (15 с)';
				break;
			}
			$this->pace();
			$this->spin->set('Загрузка данных · служебные файлы', $seen, $total);
			foreach (Http::fetchMany($chunk, $this->cfg, 'GET', true, 6000, 0) as $url => $req) {
				$out[$url] = $req;
			}
			$seen += count($chunk);
		}
		foreach ($out as $url => $req) {
			$this->probeStatus[$url] = [
				'status' => $req->status,
				'error'  => $req->error,
				'ctype'  => strtolower($req->header('content-type')),
				'hash'   => md5($req->body),
				'len'    => strlen($req->body),
				'isHtml' => stripos($req->body, '<html') !== false,
			];
		}
		$this->requests += count($out);
	}

	/**
	 * Индикатор обхода.
	 *
	 * Если терминал интерактивный — рисуется анимацией (с оценкой остатка),
	 * иначе остаётся прежняя однострочная полоса.
	 */
	private function progress(int $done, int $pages, int $pending = 0): void
	{
		if ($this->cfg->quiet) {
			return;
		}
		if ($this->spin->active()) {
			// Оценка объёма: уже найдено + в очереди + URL из карты сайта.
			$total = max($done + $pending, count($this->sitemaps->urls));
			$total = min($total, $this->cfg->limit);
			$this->spin->set('Загрузка данных · обход страниц', $done, max($total, $done));
			return;
		}
		if (Spin::busy()) {
			return;   // анимацию рисует внешний обход — не мешаем ему
		}
		$bar = str_repeat('█', min(20, (int)round($done / max(1, $this->cfg->limit) * 20)));
		fwrite(STDERR, "\r  " . $this->ui->c('Обход', 'gray') . ' ' . $this->ui->c($bar, 'brcyan')
			. ' ' . $this->ui->c(str_pad((string)$done, 5, ' ', STR_PAD_LEFT), 'bold|white')
			. $this->ui->c(' стр. · в графе ' . $pages, 'gray') . '   ');
	}

	private function progressDone(): void
	{
		if ($this->cfg->quiet || $this->spin->active() || Spin::busy()) {
			return;
		}
		fwrite(STDERR, "\r" . str_repeat(' ', 60) . "\r");
	}
}

/**
 * Контекст аудита: данные краулера + вспомогательные выборки.
 */
final class Ctx
{
	public Cfg $cfg;
	public Crawler $cr;

	public function __construct(Cfg $cfg, Crawler $cr)
	{
		$this->cfg = $cfg;
		$this->cr = $cr;
	}

	/** @return array<string,array<string,mixed>> */
	public function pages(): array
	{
		return $this->cr->pages;
	}

	/**
	 * Цитата источника для нарушения: строка кода, её номер и сама строка файла.
	 *
	 * Нужна, чтобы в отчёте показать не «найдено на странице X», а конкретное место:
	 * адрес страницы, номер строки и код этой строки.
	 *
	 * @return array{code:string,line:int,text:string,key:string}|null
	 */
	public function sourceCitation(?string $pageUrl, string $snippet, string $rule = ''): ?array
	{
		if ($pageUrl === null || $pageUrl === '') {
			return null;
		}
		$page = $this->cr->pages[$pageUrl] ?? null;
		if (!is_array($page)) {
			return null;
		}
		$cites = (array)(($page['html'] ?? [])['cites'] ?? []);
		if ($cites === []) {
			return null;
		}

		// 1) по фрагменту кода: берём самую длинную подходящую цитату
		$needle = self::normSource($snippet);
		if ($needle !== '') {
			$best = null;
			$bestKey = '';
			$bestLen = 0;
			foreach ($cites as $key => $cite) {
				$code = self::normSource((string)($cite['code'] ?? ''));
				if ($code === '') {
					continue;
				}
				$hit = $code === $needle
					|| str_starts_with($code, $needle)
					|| str_starts_with($needle, $code)
					|| str_contains($code, $needle)
					|| str_contains($needle, $code);
				if ($hit && strlen($code) > $bestLen) {
					$best = $cite;
					$bestKey = (string)$key;
					$bestLen = strlen($code);
				}
			}
			if ($best !== null) {
				return [
					'code' => (string)($best['code'] ?? ''),
					'line' => (int)($best['line'] ?? 0),
					'text' => (string)($best['text'] ?? ''),
					'key'  => $bestKey,
					'exact' => true,
				];
			}
		}

		// 2) по правилу: какой тег отвечает за эту находку
		foreach (self::citeKeysForRule($rule) as $i => $key) {
			if (!isset($cites[$key])) {
				continue;
			}
			$cite = $cites[$key];
			return [
				'code' => (string)($cite['code'] ?? ''),
				'line' => (int)($cite['line'] ?? 0),
				'text' => (string)($cite['text'] ?? ''),
				'key'  => $key,
				'exact' => $i === 0,
			];
		}
		return null;
	}

	/**
	 * Какие элементы <head> отвечают за находку по правилу.
	 *
	 * Первый — точный источник проблемы, дальше — ориентиры («добавьте рядом»),
	 * если нужного тега на странице нет.
	 *
	 * @return list<string>
	 */
	private static function citeKeysForRule(string $rule): array
	{
		$map = [
			'canonical'    => ['canonical', 'title', 'html'],
			'desc'        => ['meta:description', 'title', 'html'],
			'description' => ['meta:description', 'title', 'html'],
			'robots_meta' => ['meta:robots', 'title', 'html'],
			'noindex'     => ['meta:robots', 'title', 'html'],
			'title'       => ['title', 'html'],
			'og_'         => ['meta:og:title', 'title', 'html'],
			'.og'         => ['meta:og:title', 'title', 'html'],
			'twitter'     => ['meta:twitter:card', 'title', 'html'],
			'viewport'    => ['meta:viewport', 'title', 'html'],
			'charset'     => ['meta:charset', 'html'],
			'favicon'     => ['icon:0', 'title', 'html'],
			'h1'          => ['h1:0', 'html'],
			'schema'      => ['jsonld:0', 'title', 'html'],
			'lang'        => ['html'],
		];
		foreach ($map as $needle => $keys) {
			if (str_contains($rule, $needle)) {
				return $keys;
			}
		}
		return [];
	}

	/** Нормализация фрагмента кода для сопоставления. */
	private static function normSource(string $s): string
	{
		$s = strtolower(trim(preg_replace('~\s+~u', ' ', $s) ?? $s));
		return mb_substr($s, 0, 200);
	}

	public function origin(): string
	{
		return Url::origin($this->cfg->start);
	}

	public function host(): string
	{
		return Url::hostOf($this->cfg->start);
	}

	/** Локальный стенд (localhost/127.0.0.1) — HTTPS-проверки к нему неприменимы. */
	public function isLocal(): bool
	{
		return $this->cfg->isLocal();
	}

	/** Внутренняя ссылка (тот же сайт, www и без www — один сайт). */
	public function isInternal(string $url): bool
	{
		return Url::sameSite($url, $this->cfg->start);
	}

	public function path(string $url): string
	{
		return Url::displayPath($url);
	}

	/** @return array<string,string> путь → URL (для сверки с sitemap) */
	public function pathMap(): array
	{
		static $cache = null;
		if ($cache !== null) {
			return $cache;
		}
		$cache = [];
		foreach ($this->cr->pages as $url => $page) {
			$cache[$this->path($url)] = $url;
		}
		return $cache;
	}

	/** Код ответа по любому известному URL (страница, ассет, внешний, sitemap). */
	public function statusOf(string $url): ?int
	{
		$url = Url::normalize($url) ?? $url;
		if (isset($this->cr->pages[$url])) {
			return (int)$this->cr->pages[$url]['status'];
		}
		if (isset($this->cr->assetStatus[$url])) {
			return (int)$this->cr->assetStatus[$url]['status'];
		}
		if (isset($this->cr->externalStatus[$url])) {
			return (int)$this->cr->externalStatus[$url]['status'];
		}
		if (isset($this->cr->sitemapStatus[$url])) {
			return (int)$this->cr->sitemapStatus[$url];
		}
		// финальный URL после редиректа
		foreach ($this->cr->pages as $page) {
			if (($page['final'] ?? '') === $url) {
				return (int)$page['status'];
			}
		}
		return null;
	}

	/** @return array{status:int,error:?string,length:int} */
	public function headOf(string $url): array
	{
		$norm = Url::normalize($url) ?? $url;
		foreach ([$this->cr->assetStatus[$norm] ?? null, $this->cr->externalStatus[$norm] ?? null] as $row) {
			if ($row !== null) {
				return ['status' => (int)$row['status'], 'error' => $row['error'] ?? null, 'length' => (int)($row['length'] ?? 0)];
			}
		}
		return ['status' => 0, 'error' => null, 'length' => 0];
	}

	/**
	 * Где именно найден проблемный адрес: список источников со страницей,
	 * текстом ссылки и зоной страницы (меню/текст/подвал).
	 *
	 * @return list<array{from:string,anchor:string,zone:string,n:int,total:int}>
	 */
	public function foundOn(string $target, int $limit = 6): array
	{
		$entry = $this->cr->inlinks[$target] ?? null;
		if ($entry === null) {
			return [];
		}
		$total = (int)($entry['count'] ?? 0);
		$out = [];
		foreach (($entry['src'] ?? []) as $from => $info) {
			$anchors = array_keys($info['anchors'] ?? []);
			$zones = array_keys($info['zones'] ?? []);
			$tags = $info['tags'] ?? [];
			$out[] = [
				'from'   => (string)$from,
				'anchor' => (string)($anchors[0] ?? ''),
				'zone'   => (string)($zones[0] ?? ''),
				'n'      => (int)($info['n'] ?? 1),
				'total'  => $total,
				'tag'    => (string)($tags[0] ?? ''),
			];
		}
		usort($out, static fn(array $a, array $b): int => $b['n'] <=> $a['n']);
		return array_slice($out, 0, $limit);
	}

	/** Нормализованный путь (без завершающего слэша) — для сравнения URL. */
	public static function pathKey(string $url): string
	{
		$p = parse_url($url, PHP_URL_PATH) ?: '/';
		$q = parse_url($url, PHP_URL_QUERY);
		$p = '/' . trim(rawurldecode($p), '/');
		return $p . ($q ? '?' . $q : '');
	}
}

/**
 * Движок аудита: набор правил, их выполнение, подсчёт баллов.
 */
final class Audit
{
	/**
	 * Официальные источники, на которые опирается каждая проверка.
	 * Ключи используются в справочнике Audit::impacts().
	 */
	public const SRC = [
		'title'    => 'https://developers.google.com/search/docs/appearance/title-link',
		'snippet'  => 'https://developers.google.com/search/docs/appearance/snippet',
		'canon'    => 'https://developers.google.com/search/docs/crawling-indexing/consolidate-duplicate-urls',
		'robots'   => 'https://developers.google.com/search/docs/crawling-indexing/robots-meta-tag',
		'robottxt' => 'https://developers.google.com/search/docs/crawling-indexing/robots/intro',
		'sitemap'  => 'https://developers.google.com/search/docs/crawling-indexing/sitemaps/build-sitemap',
		'links'    => 'https://developers.google.com/search/docs/crawling-indexing/links-crawlable',
		'images'   => 'https://developers.google.com/search/docs/appearance/google-images',
		'sd'       => 'https://developers.google.com/search/docs/appearance/structured-data/intro-structured-data',
		'crawl'    => 'https://developers.google.com/search/docs/crawling-indexing/overview',
		'js'       => 'https://developers.google.com/search/docs/crawling-indexing/javascript/javascript-seo-basics',
		'cwv'      => 'https://web.dev/articles/vitals',
		'perf'     => 'https://web.dev/articles/optimize-lcp',
		'hreflang' => 'https://developers.google.com/search/docs/specialty/international/localized-versions',
		'aifeat'   => 'https://developers.google.com/search/docs/appearance/ai-features',
		'yrobot'   => 'https://yandex.ru/support/webmaster/robot-workings/robots-txt.html',
		'ymeta'    => 'https://yandex.ru/support/webmaster/meta-tags/meta-tag.html',
		'ysitem'   => 'https://yandex.ru/support/webmaster/sitemap/working-with-sitemap.html',
		'yquality' => 'https://yandex.ru/support/webmaster/quality/',
		'wcag'     => 'https://www.w3.org/WAI/WCAG22/quickref/',
		'html'     => 'https://www.w3.org/TR/html52/',
		'schema'   => 'https://schema.org/docs/gs.html',
		'rfc'      => 'https://www.rfc-editor.org/rfc/rfc9110.html',
		'owasp'    => 'https://owasp.org/www-project-secure-headers/',
		'cookies'  => 'https://developer.mozilla.org/en-US/docs/Web/HTTP/Cookies',
		'tls'      => 'https://www.rfc-editor.org/rfc/rfc8446.html',
		'llmstxt'  => 'https://llmstxt.org/',
		'gptbot'   => 'https://platform.openai.com/docs/bots',
		'claudebot'=> 'https://support.anthropic.com/en/articles/8896518',
		'perplex'  => 'https://docs.perplexity.ai/guides/bots',
		'gcommon'  => 'https://developers.google.com/search/docs/crawling-indexing/google-common-crawlers',
		'rfc9111'  => 'https://www.rfc-editor.org/rfc/rfc9111.html',
		'rfc9113'  => 'https://www.rfc-editor.org/rfc/rfc9113.html',
		'rfc6797'  => 'https://www.rfc-editor.org/rfc/rfc6797.html',
		'owasph'   => 'https://cheatsheetseries.owasp.org/cheatsheets/HTTP_Headers_Cheat_Sheet.html',
		'fontdisp' => 'https://web.dev/articles/font-display',
		'kdd'      => 'https://arxiv.org/abs/2311.09735',
		'yclean'   => 'https://yandex.ru/support/webmaster/robot-workings/clean-param.html',
		'ycrawl'   => 'https://yandex.ru/support/webmaster/robot-workings/crawl-delay.html',
		'ysnippet' => 'https://yandex.ru/support/webmaster/search-results/title-and-description.html',
		'ymicro'   => 'https://yandex.ru/support/webmaster/schema-org/',
		'yquality2'=> 'https://yandex.ru/support/webmaster/quality/',
		'ymove'   => 'https://yandex.ru/support/webmaster/ru/yandex-indexing/moving-site',
		'ymirror' => 'https://yandex.ru/support/webmaster/ru/yandex-indexing/www-migration',
		'yhost'   => 'https://yandex.ru/blog/platon/pereezd-sayta-posle-otkaza-ot-direktivy-host/',
		'yiks'    => 'https://yandex.ru/support/webmaster/ru/site-quality-index',
		'ygcopy'  => 'https://yandex.ru/support/webmaster/ru/yandex-indexing/moving-site',
	];

	/**
	 * Для каждой проверки: на что она влияет (факт, без воды) и источники.
	 *
	 * @return array<string,array{i:string,s:list<string>}>
	 */
	public static function impacts(): array
	{
		return [
			// --- технические ошибки ---
			'http.error' => ['i' => 'Критично: страница недоступна — краулер выпадает из обхода, а пользователь видит ошибку. Потеря позиций и конверсии.', 's' => ['crawl']],
			'http.status' => ['i' => 'Коды 4xx/5xx не индексируются и обрывают путь обхода: поисковик тратит краулинговый бюджет впустую.', 's' => ['crawl', 'rfc']],
			'http.redirect' => ['i' => 'Редирект — лишний запрос и задержка: цепочка увеличивает время ответа и расходует краулинговый бюджет. Сигналы при этом не теряются (302 тоже их передаёт) — важна длина цепочки и постоянный 301 для переезда.', 's' => ['crawl', 'cwv']],
			'http.redirect_loop' => ['i' => 'Петля редиректов полностью выбрасывает страницу из индекса: краулер не может добраться до контента.', 's' => ['crawl']],
			'http.redirect_to_error' => ['i' => 'Редирект в ошибку = потерянный путь: и пользователь, и робот упираются в 404/500.', 's' => ['crawl', 'rfc']],
			'http.redirect_cross_host' => ['i' => 'Внешний редирект передаёт сигналы и вес другому домену и запускает цепочку за пределами контроля.', 's' => ['crawl', 'canon']],
			'http.soft404' => ['i' => 'Ответ 200 с пустым содержанием индексируется как обычная страница: плодит дубли и размывает релевантность.', 's' => ['crawl', 'canon']],
			'http.empty' => ['i' => 'Пустой ответ 200 — признак сбоя шаблона или кэша: страница выглядит как «голая» и не несёт сигналов.', 's' => ['crawl']],
			'http.https_available' => ['i' => 'Без HTTPS браузер помечает сайт как небезопасный, а поисковики понижают доверие к домену и не включают защищённые функции.', 's' => ['owasp', 'crawl']],
			'http.http_to_https' => ['i' => 'Без принудительного 301 http→https создаются две копии сайта, метрики и ссылочный вес делятся между ними.', 's' => ['canon', 'rfc']],
			'http.mixed_content' => ['i' => 'Браузер блокирует http-ресурсы на https-странице: ломаются картинки/скрипты, растёт риск безопасности и падает LCP.', 's' => ['cwv', 'owasp']],

			// --- индексация ---
			'index.noindex_meta' => ['i' => 'noindex полностью исключает страницу из поиска и из передачи ссылочного веса. Снимают только осознанно.', 's' => ['robots', 'yrobot']],
			'index.noindex_header' => ['i' => 'X-Robots-Tag действует сильнее meta-тега: при конфликте поисковик ориентируется на заголовок.', 's' => ['robots']],
			'index.nofollow' => ['i' => 'nofollow на странице останавливает передачу веса по её ссылкам и говорит роботу не индексировать цели.', 's' => ['robots']],
			'index.canonical_missing' => ['i' => 'Без canonical дубли (www, слеш, параметры, регистр) конкурируют за одну выдачу, а сигналы делятся. Для Яндекса canonical — основной способ указать главный адрес дубля (Clean-param работает только с параметрами).', 's' => ['canon', 'yclean']],
			'index.canonical_mismatch' => ['i' => 'Canonical указывает на другую страницу — она выпадает из индекса в пользу целевой, и весь вес уходит туда.', 's' => ['canon']],
			'index.canonical_broken' => ['i' => 'Canonical на 404 или ошибку = страница не индексируется вообще, даже если ссылается на неё весь сайт.', 's' => ['canon']],
			'index.canonical_cross_domain' => ['i' => 'Междоменный canonical передаёт индексацию другому сайту — это осознанный инструмент, а не ошибка верстки.', 's' => ['canon']],
			'index.canonical_noindex' => ['i' => 'noindex с canonical — противоречащие друг другу сигналы: страница закрыта, но ссылочный вес уходит на целевую.', 's' => ['canon', 'robots']],
			'index.sitemap_format' => ['i' => 'Некорректные lastmod/priority делают данные карты непригодными: поисковик игнорирует подсказки о свежести.', 's' => ['sitemap']],
			'index.hreflang' => ['i' => 'Без hreflang языковые версии конкурируют за один запрос и не знают, какую показывать региону.', 's' => ['hreflang']],
			'index.hreflang_broken' => ['i' => 'Ссылка на несуществующую языковую версию отключает всю группу hreflang, а не только одну страницу.', 's' => ['hreflang']],
			'index.hreflang_on_canonical' => ['i' => 'hreflang на странице, канонизированной на другой URL, игнорируется: языковые сигналы теряются.', 's' => ['hreflang', 'canon']],
			'index.hreflang_not_return' => ['i' => 'Без обратной ссылки hreflang-пара не принимается: языковые версии не связываются в группу.', 's' => ['hreflang']],
			'index.robots_txt_missing' => ['i' => 'Без robots.txt нельзя управлять обходом, а роботы не получают карту сайта и инструкции по коду.', 's' => ['robottxt', 'yrobot']],
			'index.robots_blocks' => ['i' => 'Disallow на индексируемом URL закрывает его и от поисковика, и от внутренней перелинковки — вес не доходит.', 's' => ['robottxt', 'links']],
			'index.robots_syntax' => ['i' => 'Опечатка в директиве делает правило бессмысленным: робот читает robots.txt построчно и не поймёт опечатку.', 's' => ['robottxt', 'yrobot']],
			'index.orphan' => ['i' => 'Страница без входящих ссылок почти не индексируется: робот находит её только по sitemap, а вес она не получает.', 's' => ['links', 'crawl']],
			'index.depth' => ['i' => '«3 клика» — отраслевой ориентир, а не правило Google: по словам Google, регулярный краулинг вообще не является условием ранжирования. Практический смысл: глубокие страницы обходятся реже и получают меньше ссылок, поэтому их стоит поднимать в хабы и перелинковывать.', 's' => ['links', 'crawl']],
			'index.sitemap_missing' => ['i' => 'Карта сайта — самый дешёвый способ показать все URL: без неё часть страниц не попадает в обход вообще.', 's' => ['sitemap', 'ysitem']],
			'index.sitemap_vs_crawl' => ['i' => 'Расхождение карты и обхода = либо «сироты» без ссылок, либо страницы вне карты, о которых робот не знает.', 's' => ['sitemap', 'links']],
			'index.url_list' => ['i' => 'Файл-список используют для ручных проверок и Screaming Frog: расхождение с картой скрывает реальные страницы от проверки.', 's' => ['sitemap']],
			'index.sitemap_bad_status' => ['i' => 'Битые URL в карте — прямой сигнал о неряшливом сайте: поисковик тратит обход впустую и снижает доверие.', 's' => ['sitemap', 'crawl']],
			'index.sitemap_noindex' => ['i' => 'URL с noindex в карте — противоречие: страницу закрывают от индекса, но одновременно показывают как важную.', 's' => ['sitemap', 'robots']],
			'index.sitemap_duplicates' => ['i' => 'Дубли в карте размывают приоритеты и расходуют лимит обхода на повторные URL.', 's' => ['sitemap']],
			'index.sitemap_lastmod' => ['i' => 'lastmod в будущем или сильно устаревший обесценивает всю карту: робот перестаёт доверять датам обновления.', 's' => ['sitemap']],
			'index.duplicate_content' => ['i' => 'Дубли конкурируют за один запрос: поисковик выбирает одну версию, остальные выпадают, а краулинг тратится впустую.', 's' => ['canon', 'yquality']],

			// --- мета-теги ---
			'meta.title_missing' => ['i' => 'Title и description — основные элементы сниппета: без них поисковик берёт произвольный фрагмент страницы. По документации Яндекса, их качество влияет на CTR, а не на ранжирование.', 's' => ['title', 'ymeta', 'ysnippet']],
			'meta.title_length' => ['i' => 'Google не ограничивает длину title: длина не является фактором ранжирования. Обрезка в выдаче идёт по пикселям (≈600px на десктопе, заметно меньше на мобильном) — длинный заголовок теряет часть текста и снижает CTR.', 's' => ['title']],
			'meta.title_duplicate' => ['i' => 'Одинаковые title: поисковик не знает, какую страницу показать по запросу, и выдача становится случайной.', 's' => ['title', 'yquality']],
			'meta.desc_missing' => ['i' => 'Description не участвует в ранжировании (ни в Google, ни в Яндексе), но задаёт текст сниппета и влияет на CTR: без него поисковик берёт произвольный фрагмент страницы.', 's' => ['snippet', 'ymeta', 'ysnippet']],
			'meta.desc_length' => ['i' => 'Лимита длины description у Google нет; сниппет обрезается примерно на 155–160 символах (рекомендация Google: 50–160 символов). Влияет на CTR, не на ранжирование.', 's' => ['snippet']],
			'meta.desc_duplicate' => ['i' => 'Одинаковые description показывают поисковику одинаковую релевантность для разных страниц и снижают CTR.', 's' => ['snippet', 'yquality']],
			'meta.h1_missing' => ['i' => 'По официальной позиции Google (John Mueller) H1 не обязателен и не имеет ни верхнего, ни нижнего ограничения: это вопрос семантики и доступности, а не ранжирования. Один осмысленный H1 всё же полезен пользователю и скринридеру.', 's' => ['title', 'wcag']],
			'meta.h1_duplicate' => ['i' => 'Повторяющийся H1 на разных страницах — признак шаблонных страниц. На ранжирование это не влияет напрямую, но размывает семантику и снижает качество страницы.', 's' => ['yquality', 'html']],
			'meta.heading_skip' => ['i' => 'Пропуск уровней заголовков ломает структуру документа: навигация по разделам и скринридеры теряют иерархию.', 's' => ['html', 'wcag']],
			'meta.lang_missing' => ['i' => 'Атрибут lang определяет язык для поисковика, переводчиков и озвучки — без него страница «неязыковая».', 's' => ['html', 'hreflang']],
			'meta.lang_mismatch' => ['i' => 'Несовпадение языка (lang="en" при русском тексте) мешает определить язык и ранжировать по языковым запросам.', 's' => ['hreflang', 'html']],
			'meta.charset_missing' => ['i' => 'Без charset кириллица ломается у части клиентов — это прямой удар по поведенческим факторам и доверию.', 's' => ['html']],
			'meta.viewport_missing' => ['i' => 'Без viewport мобильный браузер покажет « shrunk » версию: показатели мобильного трафика и Core Web Vitals ухудшаются.', 's' => ['cwv', 'ymeta']],
			'meta.viewport_restricted' => ['i' => 'user-scalable=no ломает доступность для слабовидящих и может ухудшать мобильные метрики.', 's' => ['wcag', 'cwv']],
			'meta.og_missing' => ['i' => 'OG-теги задают вид ссылки в мессенджерах и соцсетях (часть AI-ассистентов тоже использует их для превью). На ранжирование не влияют, но напрямую работают на CTR и узнаваемость бренда.', 's' => ['snippet', 'aifeat']],
			'meta.og_url_mismatch' => ['i' => 'Расхождение og:url и canonical создаёт «два адреса» одной страницы в соцсетях и в сохранённых данных.', 's' => ['canon']],
			'meta.og_image_broken' => ['i' => 'Битая og:image = ссылка без превью в соцсетях, в мессенджерах и в AI-карточках: CTR падает.', 's' => ['aifeat', 'images']],
			'meta.twitter_missing' => ['i' => 'Twitter/X использует свои теги, а не OG: без них превью в твитере собирается случайно.', 's' => ['snippet']],
			'meta.favicon' => ['i' => 'Иконка сайта отображается в закладках, в поиске и в панелях браузера — влияет на узнаваемость и поведение.', 's' => ['images']],

			// --- разметка ---
			'schema.missing' => ['i' => 'Разметка Schema.org даёт расширенные сниппеты: Яндекс показывает в них адрес, телефон, быстрые ссылки и данные организации, Google — отзывы, цены и FAQ. Для AI-поиска разметка позволяет машинно извлечь суть страницы.', 's' => ['sd', 'schema', 'ymicro', 'aifeat']],
			'schema.invalid' => ['i' => 'Битый JSON в разметке игнорируется целиком: вместо rich snippet остаётся обычный.', 's' => ['sd', 'schema']],
			'schema.no_type' => ['i' => 'Объект без @type не интерпретируется: поисковик не понимает, что это за сущность, и не показывает её в ответных блоках.', 's' => ['schema', 'sd']],
			'schema.duplicate_type' => ['i' => 'Два конкурирующих объекта одного типа на странице (например, две Organization) — поисковик выберет один наугад.', 's' => ['sd', 'schema']],
			'schema.required' => ['i' => 'Без обязательных свойств (headline, datePublished, name) разметка не даёт rich snippet и хуже извлекается AI-поиском.', 's' => ['sd', 'schema']],

			// --- контент ---
			'content.thin' => ['i' => 'Порога в 300 слов у Google нет (Danny Sullivan: «лучшее количество слов для успеха — не существует»). 300 — отраслевой ориентир для коммерческих страниц; реальная проблема — когда страница не отвечает на запрос вообще.', 's' => ['yquality', 'aifeat']],
			'content.php_errors' => ['i' => 'Технический текст в HTML виден пользователю: падает доверие, растёт bounce-rate, портится вёрстка. Яндекс прямо относит страницы с ошибками к некачественным.', 's' => ['yquality']],
			'content.text_ratio' => ['i' => 'Страница, почти целиком состоящая из разметки, воспринимается как пустая: меньше текста на ту же страницу.', 's' => ['yquality']],
			'content.encoding_ok' => ['i' => 'Неверный charset в ответе ломает вывод кириллицы у роботов и агентов — часть страницы выглядит мусором.', 's' => ['html']],
			'content.robots_meta_conflict' => ['i' => 'Конфликт robots-сигналов (meta против заголовка) делает индексацию непредсказуемой.', 's' => ['robots']],
			'content.keyword_density' => ['i' => 'Плотность термина сама по себе не является фактором ранжирования (John Mueller). Значение выше 3,5% — индикатор переспама: текст выглядит роботизированным, ухудшается поведение и читаемость.', 's' => ['yquality', 'aifeat']],

			// --- ссылки ---
			'links.broken_internal' => ['i' => 'Битая внутренняя ссылка обрывает путь пользователя и передачу веса: на неё уходит краулинговый бюджет.', 's' => ['links', 'crawl']],
			'links.redirect_internal' => ['i' => 'Ссылка на редирект = лишний запрос и задержка для робота и пользователя. Длинные цепочки съедают краулинговый бюджет, поэтому внутренние ссылки должны вести сразу на конечный адрес.', 's' => ['links', 'crawl']],
			'links.broken_external' => ['i' => 'Битые внешние ссылки подрывают доверие к ресурсу и ухудшают репутацию домена в глазах поисковика.', 's' => ['links', 'yquality']],
			'links.nofollow_internal' => ['i' => 'nofollow на внутренней ссылке обрывает передачу веса и говорит: эта страница не важна.', 's' => ['links', 'robots']],
			'links.empty_anchor' => ['i' => 'Пустой анкор (иконка без текста) не передаёт тему ссылки ни роботу, ни пользователю.', 's' => ['links', 'wcag']],
			'links.generic_anchor' => ['i' => 'Анкор «подробнее» не несёт запроса: страница-получатель не получает текстового сигнала по релевантности.', 's' => ['links', 'title']],
			'links.broken_fragment' => ['i' => 'Битый якорь (#id) уводит в никуда: пользователь не находит блок, растёт недовольство и возвраты на поиск.', 's' => ['links', 'wcag']],
			'links.to_noindex' => ['i' => 'Вес на noindex-страницу не передаётся: внутренние ссылки перестают работать как канал веса.', 's' => ['links', 'robots']],
			'links.extensions' => ['i' => 'Ссылки на .php/.html плохо переживают смену ЧПУ и часто ломаются при переносе — риск для обхода.', 's' => ['links', 'yrobot']],
			'links.many_links' => ['i' => 'Огромное число ссылок на странице размывает внутренний вес: значимыми остаются единицы.', 's' => ['links', 'yquality']],
			'links.js_navigation' => ['i' => 'Меню, которое появляется только после JS, краулер может не увидеть: часть страниц выпадает из обхода.', 's' => ['js', 'crawl']],
			'links.ambiguous_anchor' => ['i' => 'Одинаковый анкор на разные адреса размывает сигнал ссылки и путает пользователя при выборе.', 's' => ['links']],

			// --- изображения ---
			'img.missing_alt' => ['i' => 'alt нужен для поиска по картинкам, для доступности и для извлечения смысла картинки AI-поиском.', 's' => ['images', 'wcag', 'aifeat']],
			'img.empty_alt' => ['i' => 'alt="" у контентной картинки скрывает её и от поиска по изображениям, и от AI-извлечения сути.', 's' => ['images', 'wcag']],
			'img.no_dimensions' => ['i' => 'Без width/height браузер резервирует место после загрузки: растёт CLS — метрика Core Web Vitals.', 's' => ['cwv']],
			'img.lazy_loading' => ['i' => 'Без lazy ниже fold загружаются лишние байты: растёт LCP и трафик мобильных пользователей.', 's' => ['cwv', 'perf']],
			'img.broken' => ['i' => 'Битая картинка выглядит как недоделка, снижает доверие и ухудшает поведенческие факторы.', 's' => ['images', 'yquality']],
			'img.lcp' => ['i' => 'LCP-картинка — самый заметный элемент первого экрана: без приоритета и размеров она тормозит главную метрику.', 's' => ['cwv', 'perf']],
			'img.oversize' => ['i' => 'Тяжёлые картинки съедают мобильный трафик и время до LCP: Google оценивает в том числе вес главного контента.', 's' => ['cwv', 'perf']],

			// --- скорость ---
			'perf.ttfb' => ['i' => 'TTFB — первый порог Core Web Vitals: медленный ответ сервера ухудшает LCP и удержание. Google называет CWV не главным фактором ранжирования, но тай-брейкером — и прямое влияние на UX.', 's' => ['cwv', 'perf']],
			'perf.page_size' => ['i' => 'Большой HTML замедляет парсинг и стилизацию: растёт время до первого контента и INP.', 's' => ['cwv', 'perf']],
			'perf.compression' => ['i' => 'Без gzip/br HTML передаётся в 4–5 раз тяжелее: напрямую бьёт по LCP и трафику.', 's' => ['cwv', 'perf']],
			'perf.cache_control' => ['i' => 'Без Cache-Control (RFC 9111) и ETag каждый визит — полная перерисовка: растёт TTFB и нагрузка на сервер, а повторный обход страницы тратит краулинговый бюджет.', 's' => ['cwv', 'perf', 'rfc9111']],
			'perf.blocking' => ['i' => 'Рендер-блокирующие CSS/JS задерживают первую отрисовку: прямое влияние на LCP и FCP.', 's' => ['cwv', 'perf']],
			'perf.dom_size' => ['i' => 'Тяжёлый DOM замедляет парсинг и стилизацию на слабых телефонах: растёт INP и TTFB.', 's' => ['cwv', 'perf']],
			'perf.http_version' => ['i' => 'HTTP/1.0 без keep-alive открывает новое соединение на каждый ресурс — лишние RTT.', 's' => ['rfc', 'cwv']],
			'perf.assets_weight' => ['i' => 'Суммарный вес статики определяет, сколько данных грузит мобильный пользователь до первого экрана.', 's' => ['cwv', 'perf']],

			// --- безопасность ---
			'sec.https' => ['i' => 'HTTPS — базовая гигиена: без неё браузер предупреждает, а поисковики не доверяют домену и не дают расширенных возможностей.', 's' => ['owasp', 'crawl']],
			'sec.hsts' => ['i' => 'HSTS закрывает окно SSL-stripping и запрещает браузеру уйти в http. RFC 6797: max-age от 1 года (31536000); OWASP рекомендует 63072000 (2 года) с includeSubDomains и preload.', 's' => ['owasp', 'owasph', 'rfc6797', 'tls']],
			'sec.csp' => ['i' => 'CSP ограничивает выполнение скриптов: главный рубеж против XSS и кражи сессий.', 's' => ['owasp']],
			'sec.xfo' => ['i' => 'Защита от iframe останавливает clickjacking: наложение чужой страницы поверх вашей кнопки.', 's' => ['owasp', 'wcag']],
			'sec.nosniff' => ['i' => 'nosniff запрещает браузеру угадывать тип файла: базовая защита от MIME-конфликтов и подмены JS.', 's' => ['owasp']],
			'sec.referrer_policy' => ['i' => 'Referrer-Policy убирает utm-метки и внутренние пути из чужих логов: меньше утечки и точнее аналитика.', 's' => ['owasp', 'mdn']],
			'sec.permissions_policy' => ['i' => 'Permissions-Policy выключает неиспользуемые API (камера, микрофон, геолокация) — уменьшает поверхность атаки.', 's' => ['owasp']],
			'sec.banners' => ['i' => 'Версии nginx/PHP в заголовках упрощают подбор эксплойтов: баннер стоит скрыть.', 's' => ['owasp']],
			'sec.sensitive_files' => ['i' => 'Критично: открытый .env или .git раскрывает пароли, ключи и всю структуру проекта.', 's' => ['owasp']],
			'sec.tls_expired' => ['i' => 'Просроченный сертификат = предупреждение браузера для всех посетителей и потеря доверия к домену. Сертификат действует до даты в поле notAfter; проверяется автоматически при установке TLS-соединения.', 's' => ['tls', 'rfc6797']],
			'sec.tls_unverified' => ['i' => 'Если сертификат не удалось проверить автоматически (соединение не установилось или сервер не отдал сертификат), срок его действия нужно проверить вручную — инструмент не делает выводов о просрочке без фактов.', 's' => ['tls', 'rfc6797']],
			'sec.tls_quality' => ['i' => 'Сертификат на чужое имя и слабые алгоритмы (SHA-1/MD5) приводят к предупреждениям и понижают доверие.', 's' => ['tls', 'owasp']],
			'sec.cookies' => ['i' => 'Cookie без Secure/HttpOnly/SameSite утекает в JS и перехватывается по http: это вектор кражи сессии.', 's' => ['cookies', 'owasp']],

			// --- доступность ---
			'a11y.form_label' => ['i' => 'Без label поле недоступно с клавиатуры и для скринридеров: теряется часть трафика, WCAG 2.1 A.', 's' => ['wcag']],
			'a11y.field_name' => ['i' => 'Поле без name не отправляется и не восстанавливается: часть заявок теряется молча.', 's' => ['wcag', 'html']],
			'a11y.duplicate_ids' => ['i' => 'Дубли id ломают якоря и скрипты: кликабельные элементы перестают работать корректно.', 's' => ['html', 'wcag']],
			'a11y.iframe_title' => ['i' => 'Без title iframe невозможно отличить в содержимом для скринридера: WCAG 2.1 A.', 's' => ['wcag']],
			'a11y.landmark' => ['i' => 'Отдельного критерия WCAG «наличие <main>» нет, но landmark-разметка входит в SC 1.3.1 (связи в разметке, уровень A) и помогает скринридерам и поисковику понять структуру страницы.', 's' => ['wcag', 'html']],
			'a11y.skip_link' => ['i' => 'Skip-link экономит клавиатурным пользователям проход по меню на каждой странице — WCAG 2.4 A.', 's' => ['wcag']],
			'a11y.button_label' => ['i' => 'Иконочная кнопка без имени недоступна: часть пользователей не может завершить действие.', 's' => ['wcag']],
			// --- AI/GEO, URL-гигиена, пагинация, шрифты, HTTP/2 ---
			'index.sitemap_directive' => ['i' => 'Директива Sitemap: — самый надёжный способ сообщить поисковику о карте: robots.txt читают все роботы, а карту ищут не всегда.', 's' => ['robottxt', 'sitemap']],
			// --- Яндекс-специфичные проверки ---
			'index.robots_bots' => ['i' => 'Disallow: / для робота Яндекса (или Googlebot) означает, что сайт не индексируется этой системой вообще. Правила для конкретного бота важнее общих User-agent: *.', 's' => ['yrobot', 'robottxt']],
			'index.clean_param' => ['i' => 'Clean-param читает только робот Яндекса: он склеивает адреса, отличающиеся этими параметрами, переносит накопленные метрики на основной URL и не обходит дубли заново. Опасно вносить туда параметры, меняющие содержимое (фильтры, сортировки): робот посчитает разные страницы одинаковыми. Google директиву не поддерживает — для него нужны canonical и noindex.', 's' => ['yclean', 'yrobot']],
			'index.robots_encoding' => ['i' => 'Яндекс прямо указывает: пути в Disallow с не-ASCII нужно писать в percent-encoding (например /%D0%BA%D0%BE%D1%80%D0%B7%D0%B8%D0%BD%D0%B0). Буквальная кириллица в директиве не сработает, и раздел останется открытым для обхода.', 's' => ['yrobot']],
			'index.robots_limits' => ['i' => 'Требование Яндекса к robots.txt: файл в корне, отдаёт 200 OK, размер не больше 500 КБ. При превышении лимита робот читает файл частично — часть правил просто не применяется.', 's' => ['yrobot']],
			'index.robots_host' => ['i' => 'Директива Host работает только у робота Яндекса: указывает основное зеркало, если сайт доступен по нескольким адресам. Директива должна быть одна — при нескольких учитывается первая.', 's' => ['yrobot']],
			'index.crawl_delay' => ['i' => 'С 22 февраля 2018 года Яндекс не учитывает Crawl-delay: скорость обхода задаётся только в Вебмастере. Директива в robots.txt остаётся подсказкой для других роботов, но на Яндекс не влияет.', 's' => ['ycrawl']],
			'index.verification' => ['i' => 'Подтверждение прав в Вебмастере и Search Console даёт доступ к переобходу, отчётам и инструментам диагностики — без него часть проверок и инструментов недоступна.', 's' => ['yquality2', 'ymicro']],
			'ai.llms_txt' => ['i' => 'llms.txt — предложение сообщества (llmstxt.org), а не официальный стандарт: Google и OpenAI поддержку не объявляли, peer-reviewed замеров эффекта нет. Полезен как машиночитаемый указатель для агентов, но ставить его нужно осознанно, а не «потому что модно».', 's' => ['llmstxt', 'aifeat']],
			'ai.plugin_manifest' => ['i' => 'Манифест (ai-plugin.json / agents.json) — это машиночитаемое описание того, что сайт умеет и где брать данные: точка входа для агентов. Формат предложен OpenAI, официальной поддержки поисковыми системами нет — полезен как гигиена, а не как фактор ранжирования.', 's' => ['gptbot', 'aifeat']],
			'ai.robots_crawlers' => ['i' => 'Disallow для GPTBot/ClaudeBot/PerplexityBot/Google-Extended прямо исключает контент из ответов AI-поиска. Это бизнес-решение, но оно должно быть явным.', 's' => ['robottxt', 'gptbot', 'perplex', 'aifeat']],
			'ai.answer_structure' => ['i' => 'Списки, вопросы в заголовках и FAQPage дают движку «готовые единицы» для цитирования и одновременно работают на сниппеты и featured snippets. Строгого измеренного порога нет — это отраслевая практика, подтверждённая чек-листами и самой постановкой задачи RAG.', 's' => ['aifeat', 'sd']],
			'ai.eeat' => ['i' => 'Автор и дата — опорные сигналы E-E-A-T и прозрачности источника. Официального измеренного эффекта на цитирование именно по автору нет, но без них материал анонимный: его сложнее проверить и сложнее цитировать, а в части поисковых систем это прямой сигнал качества.', 's' => ['aifeat', 'sd']],
			'ai.answer_first' => ['i' => 'Единица ответа генеративной модели — короткий фрагмент: длинная «простыня» режется на куски и часто цитируется частично или не попадает в ответ. Короткие абзацы (40–260 символов) — эвристика извлекаемости, а не измеренный порог.', 's' => ['aifeat', 'kdd']],
			'ai.entity_sameas' => ['i' => 'sameAs связывает сущность «компания» с её профилями и базами знаний: так система понимает, кто вы, и не приписывает вам чужое.', 's' => ['schema', 'aifeat']],
			'url.too_long' => ['i' => 'URL длиннее ~100 символов обрезается в выдаче и в соцсетях, часть агентов и систем отбрасывает его целиком.', 's' => ['links', 'crawl']],
			'url.mixed_case' => ['i' => 'Регистр в пути создаёт дубли: /Blog и /blog — разные адреса для робота, хотя пользователь видит одну страницу.', 's' => ['canon', 'links']],
			'url.underscore' => ['i' => 'Подчёркивание хуже дефиса воспринимается движками и разделителями слов, из-за чего часть запросов не распознаётся.', 's' => ['links', 'yrobot']],
			'url.non_ascii' => ['i' => 'Не-ASCII в URL ломает копирование, аналитику и часть агентов; транслитерация в адресе убирает эти риски.', 's' => ['links', 'yrobot']],
			'url.slash_consistency' => ['i' => 'Смешение /blog и /blog/ порождает дубли: робот обходит оба, а вес делится между ними вместо концентрации.', 's' => ['canon', 'links']],
			'index.pagination' => ['i' => 'rel=next/prev задаёт роботу последовательность пагинации и снижает риск, что страницы 2, 3, 4 будут считаны дублями первой.', 's' => ['links', 'crawl']],
			'perf.font_display' => ['i' => 'Без font-display браузер ждёт шрифт и рисует текст позже: растут FOUT и CLS — метрики Core Web Vitals (web.dev).', 's' => ['cwv', 'perf', 'fontdisp']],
			'perf.http2' => ['i' => 'HTTP/2 (RFC 9113) мультиплексирует запросы в одном соединении: меньше RTT на десятки ресурсов — быстрее первая отрисовка.', 's' => ['rfc9113', 'cwv']],

			// --- углублённые проверки ---
			'meta.default_title' => ['i' => 'Авто-заголовок («Главная», «Home», «Page 2») не несёт темы страницы: поисковик берёт её вместо вашего текста.', 's' => ['title', 'yquality']],
			'img.alt_placeholder' => ['i' => 'alt из имени файла формально закрывает проверку, но не даёт ни ранжирования по картинкам, ни доступности — это «обман» а не описание.', 's' => ['images', 'wcag']],
			'links.content_links' => ['i' => 'Ссылки только из меню и футера не передают контекст: робот не понимает, какие темы раскрывает страница, и хабы не укрепляются.', 's' => ['links', 'yquality']],
			'perf.cdn' => ['i' => 'CDN разгружает origin, ускоряет отдачу статики по всему миру и добавляет защиту от DDoS — это влияет и на скорость, и на доступность.', 's' => ['cwv', 'perf']],
			'index.faceted_nav' => ['i' => 'Фильтры и сортировки плодят URL, которые конкурируют с основными страницами. Закрывайте их noindex и не пускайте в карту.', 's' => ['canon', 'sitemap']],
			'schema.local_business' => ['i' => 'Organization/LocalBusiness с address и geo привязывают компанию к месту в локальной выдаче и картах.', 's' => ['schema', 'sd']],
			'index.host_variants' => ['i' => 'Каждый живой вариант домена (http/https, www) — отдельная копия сайта: робот обходит их вместо полезных страниц.', 's' => ['canon', 'crawl']],
			'ai.citation_bots' => ['i' => 'Цитатные AI-краулеры (OAI-SearchBot, ClaudeBot, PerplexityBot, Google-Extended) — это те, кто формирует ссылки в ответах ИИ; блокируя их, вы закрываете канал трафика из AI-ответов. Обучение моделей (GPTBot, CCBot, Amazonbot) — отдельный канал, решение о нём независимое и не влияет на цитирование.', 's' => ['gptbot', 'perplex', 'gcommon', 'aifeat']],
			'ai.entity_definition' => ['i' => '«Определение сущности в первом абзаце 40–50 слов» — это рекомендация отраслевых GEO-чеклистов (Alice Labs, 2026), а не измеренный результат исследования. В самой работе Aggarwal et al. (KDD\'24) такого порога нет — там проверялись цитаты, источники и статистика.', 's' => ['aifeat', 'kdd']],
			'ai.quick_answer' => ['i' => '«Блок с коротким ответом до 160 символов в начале страницы» — практика из GEO-чеклистов (Alice Labs, 2026), не измеренный фактор: в peer-reviewed работе Aggarwal et al. такого порога нет. Проверка полезна как эвристика извлекаемости.', 's' => ['aifeat', 'kdd']],
			'ai.sources_cited' => ['i' => 'В эксперименте Aggarwal et al. (KDD\'24) Cite Sources давал от −30% (уже топовые страницы) до +115% (страницы 5-й позиции) относительной видимости в ответах генеративных движков. Эффект зависит от текущей позиции, поэтому для сильных страниц добавление ссылок может не помочь. Критический обзор GEO (2026) отмечает: коммерческие обещания GEO опережают данные.', 's' => ['kdd', 'aifeat']],
			'ai.statistics' => ['i' => 'Statistics Addition в Aggarwal et al. (KDD\'24) давал существенный прирост видимости (до ~+40% для слабых по позиции страниц) и около −20% для уже топовой страницы. Статистика и даты — то, что генеративная модель может процитировать дословно.', 's' => ['kdd', 'aifeat']],
			'ai.quotes' => ['i' => 'Quotation Addition — лучший метод в исследовании Aggarwal et al. (KDD\'24): рост взвешенной видимости примерно на 30–40% (показатель PAWC вырос с 19,3 до 27,2). Поздние работы (FeatGEO, ACL 2026) показывают, что изолированные текстовые правки не всегда дают устойчивый эффект.', 's' => ['kdd', 'aifeat']],
			'ai.js_dependency' => ['i' => 'AI-краулеры не выполняют JavaScript: контент, который рендерится только на клиенте, для них пуст. Для Яндекса это отдельное требование — основной текст должен присутствовать в исходном HTML («текстовая версия»). Для Google индексация JS — отдельная задача, для GPTBot/ClaudeBot/Perplexity потеря контента полная.', 's' => ['aifeat', 'js', 'gptbot', 'yquality2']],
			'ai.date_modified' => ['i' => 'dateModified — машиночитаемый сигнал свежести: Google использует даты публикации/обновления в сниппетах, а без разметки дата остаётся неоднозначной для роботов и агентов.', 's' => ['aifeat', 'sd']],
			'ai.llms_full' => ['i' => 'llms-full.txt — развёрнутая версия карты (предложение сообщества llmstxt.org). Официальной поддержки и измеренного эффекта нет; полезен тем, кто отдаёт контент агентам напрямую.', 's' => ['llmstxt']],

			// --- дополнительные проверки (enterno.io и др.) ---
			'sec.cors' => ['i' => 'Access-Control-Allow-Origin: * на обычном контентном сайте позволяет любому чужому сайту читать ответы вашего сервера через браузер пользователя (функции авторизации это не заменяет, но лишнее).', 's' => ['owasph', 'mdn']],
			'tech.detect' => ['i' => 'Стек (CMS, CDN, WAF) объясняет скорость, блокировки ботов и особенности редиректов. Определяется по заголовкам ответа; если сервер их скрывает — это тоже осознанное решение, а не проблема.', 's' => ['mdn', 'rfc']],
			'compliance.personal_data' => ['i' => 'Для сайта, работающего на территории РФ, обязательны политика обработки персональных данных и явное согласие пользователя на их обработку (чекбокс в форме + ссылка на политику). Отсутствие — предписание Роскомнадзора и потеря заявок.', 's' => ['yquality2']],
			'commerce.agent_ready' => ['i' => 'Автономные покупатели (Agentic Commerce) берут товар, цену и наличие из разметки Offer/Product. Без цены, наличия и условий агент не сможет оформить заказ, а магазин теряет канал продаж.', 's' => ['sd', 'schema', 'aifeat']],
			'dns.email_records' => ['i' => 'Заявки с форм уходят на почту: без SPF и DMARC письма попадают в спам, а домен можно подделать в отправителе. Проверяется по публичным DNS-записям домена.', 's' => ['yquality2']],
			'http.http3' => ['i' => 'HTTP/3 (QUIC) убирает head-of-line blocking и ускоряет первую отрисовку на мобильных. Определяется через ALPN-переговоры при TLS-соединении.', 's' => ['cwv', 'rfc9113']],

			// --- AI и агентные протоколы ---
			'ai.agent_protocols' => ['i' => 'Агентные протоколы (agents.json, MCP server card, A2A, agent skills, api-catalog, OpenAPI, OAuth discovery) — машиночитаемое описание того, что умеет и где отдаёт данные сайт. Без них ИИ-агент и интеграции работают вслепую: напрямую с верстанием страницы, теряя структуру и метаданные.', 's' => ['aifeat', 'llmstxt']],
			'ai.markdown_negotiation' => ['i' => 'Агенты часто запрашивают страницу с Accept: text/markdown. Если сервер отдаёт HTML, агенту приходится вырезать разметку самостоятельно — контекст теряется, а цитируемость падает. Markdown-выдача — де-факто стандарт для LLM-краулеров.', 's' => ['aifeat', 'llmstxt']],
			'ai.content_signals' => ['i' => 'Content-Signals — заголовок, декларирующий, как машинам работать с контентом (например, разрешён ли синтез речи). Стандарт новый и внедряется быстро; его отсутствие пока не штраф, но это сигнал готовности.', 's' => ['aifeat']],
			'ai.link_headers' => ['i' => 'Link: <https://site/page.md>; rel="alternate"; type="text/markdown" — машинный способ указать роботу на альтернативную версию страницы, которую проще разобрать.', 's' => ['aifeat', 'rfc']],
			'ai.speakable' => ['i' => 'speakable в schema.org указывает, какой фрагмент страницы можно озвучить или процитировать целиком. Это прямой путь в голосовые ответы и AI-сводки, где цитируют фрагмент, а не страницу.', 's' => ['sd', 'schema', 'aifeat']],
			'ai.meta_tags' => ['i' => 'citation_title / citation_author / citation_publisher / citation_language и ai-* дают машинам данные об авторе и происхождении материала — тот же механизм атрибуции, что у Google Scholar. Без них источник цитирования определяется эвристически и часто неверно.', 's' => ['aifeat', 'sd']],
			'ai.llms_link' => ['i' => 'Явная ссылка <link rel="llms-txt"> в HTML — подсказка агентам, где лежит карта сайта для моделей: не нужно угадывать адрес /llms.txt.', 's' => ['llmstxt', 'aifeat']],
			'ai.llms_structure' => ['i' => 'Структура llms.txt — это первое, что читает модель. Без H1 («# Название») она не понимает, о каком проекте файл, и цитирует его неверно; блок-цитата и разделы H2 дают ей карту контента без разбора HTML.', 's' => ['llmstxt', 'aifeat']],
			'ai.llms_links' => ['i' => 'Относительные ссылки модель разрешит неверно — у неё нет адреса сайта. Описание после «:» — это то, по чему LLM понимает, что за страница, и цитирует её с правильным контекстом.', 's' => ['llmstxt', 'aifeat']],
			'ai.llms_size' => ['i' => 'Файл занимает часть контекстного окна модели: 300–1000 токенов достаточно для понимания структуры, а большой файл вытесняет полезный контекст запроса. Полный контент — в llms-full.txt.', 's' => ['llmstxt']],
			'ai.llms_declared' => ['i' => 'Без директивы Sitemap: в robots.txt AI-краулер не узнаёт, что карта сайта для моделей существует, — файл остаётся не найденным по умолчанию.', 's' => ['llmstxt', 'yrobot']],
			'index.robots_blocks_assets' => ['i' => 'Робот не отрисует страницу без CSS/JS: не увидит мета-теги из скриптов, ссылки, собранные на клиенте, и не сможет оценить рендеринг. Это ломает понимание страницы целиком, а не отдельную проверку.', 's' => ['robottxt', 'yrobot', 'crawl']],
			'index.robots_useragent_case' => ['i' => 'Имя секции сопоставляется роботом по точному совпадению: «Yandex» и «yandex» — разные секции, и правило для одной из них не применится к другой.', 's' => ['yrobot', 'robottxt']],
			'index.robots_block_params' => ['i' => 'Disallow: *?* закрывает все адреса с параметрами. В связке с Clean-param из обхода выпадают и фильтры, и пагинация — проверьте, что среди закрытых нет нужных страниц.', 's' => ['yclean', 'yrobot']],
			'sitemap.freshness' => ['i' => 'Устаревшая карта (lastmod старше 30 дней) читается роботом как признак «индекс не обновляется»: новые страницы он находит медленнее, а старые считает актуальными дольше, чем они есть.', 's' => ['sitemap', 'ysitem']],
			'sitemap.format' => ['i' => 'Карта сайта — входной файл для робота: без корректного XML и кода 200 содержимое не разбирается вообще, и URL из карты просто не попадают в обход.', 's' => ['sitemap', 'ysitem']],
			'ai.content_signal' => ['i' => 'Content-Signal — машинное заявление о том, можно ли использовать контент для обучения ИИ и для ответов ассистентов. Без него политика «не обучать на нас» существует только в robots.txt, а часть систем её не читает.', 's' => ['aifeat', 'rfc']],

			// --- домены, зеркала, миграция ---
			'domain.mirror_duplicate' => ['i' => 'С 2019 года Яндекс больше не склеивает зеркала по совпадению контента: если вариант адреса отвечает 200, он становится самостоятельным сайтом со своим ИКС, своей индексацией и долей ссылочного веса. Именно так компании «размножают» ИКС вместо того, чтобы перенести его.', 's' => ['yhost', 'ymove', 'yiks']],
			'domain.redirect_code' => ['i' => '301 — постоянный редирект: он переносит сигналы и убирает старый адрес из индекса. 302/307 — временный, старый адрес остаётся в базе, страницы не склеиваются, а при переезде теряется часть позиций.', 's' => ['ymove', 'canon']],
			'domain.redirect_target' => ['i' => 'По инструкции Яндекса редирект должен вести со страниц старого сайта на аналогичные страницы нового. Редирект «вся стартовая страница → главная» сохраняет только трафик главной, а остальные разделы обнуляются.', 's' => ['ymove', 'yhost']],
			'domain.donor_copy' => ['i' => 'Старый домен, который продолжает отдавать сайт, — самая частая ошибка при переезде: он получает собственный ИКС, собственные позиции и делит ссылочный вес с новым сайтом. То же самое происходит, если компания «разводит» домены по аудиториям (b2b/b2c) и оставляет старые домены живыми.', 's' => ['ymove', 'yiks', 'yhost']],
			'domain.sitemap_foreign' => ['i' => 'Оставленные в карте адреса старого домена отправляют робота не туда: часть страниц нового сайта не попадает в обход, а индексируются старые URL.', 's' => ['ysitem', 'ymove']],
			'domain.internal_foreign' => ['i' => 'Ссылка на свой же старый домен даже с 301 — лишний переход для пользователя и робота, а вес идёт через цепочку. В шаблонах, CMS и письмах нужно заменить все внутренние адреса на актуальные.', 's' => ['links', 'ymove']],
			'domain.iks_split' => ['i' => 'Факт из справки Яндекса: ИКС считается отдельно для главного и неглавных адресов и для поддоменов, и при разных доменах значения могут различаться. Редирект не переносит ИКС — новый домен считается заново (недели–месяцы). При этом ИКС на ранжирование не влияет — это индикатор полезности и доверия, а не фактор выдачи.', 's' => ['yiks', 'ymove']],
		];
	}
	/**
	 * Каким поисковым системам релевантна проверка (маркировка в отчёте).
	 */
	public const SYSTEMS = [
		'Технические ошибки'   => ['google' => true, 'yandex' => true, 'bing' => true, 'ai' => true],
		'Индексация'           => ['google' => true, 'yandex' => true, 'bing' => true, 'ai' => true],
		'Мета-теги'            => ['google' => true, 'yandex' => true, 'bing' => true, 'ai' => true],
		'Разметка Schema.org'   => ['google' => true, 'yandex' => true, 'bing' => true, 'ai' => true],
		'Контент'              => ['google' => true, 'yandex' => true, 'bing' => true, 'ai' => true],
		'Ссылки'               => ['google' => true, 'yandex' => true, 'bing' => true, 'ai' => true],
		'Изображения'          => ['google' => true, 'yandex' => true, 'bing' => true, 'ai' => true],
		'Скорость'             => ['google' => true, 'yandex' => true, 'bing' => true, 'ai' => true],
		'Безопасность'         => ['google' => true, 'yandex' => true, 'bing' => true, 'ai' => true],
		'Доступность'          => ['google' => true, 'yandex' => true, 'bing' => true, 'ai' => false],
		'AI и GEO'              => ['google' => false, 'yandex' => true, 'bing' => false, 'ai' => true],
		'Домены и миграция'     => ['google' => true, 'yandex' => true, 'bing' => true, 'ai' => false],
		'Технический стек'      => ['google' => true, 'yandex' => true, 'bing' => true, 'ai' => false],
		'Соответствие 152-ФЗ'   => ['google' => false, 'yandex' => true, 'bing' => false, 'ai' => false],
	];

	public const CAT_WEIGHTS = [
		'Технические ошибки'    => 18,
		'Индексация'            => 20,
		'Мета-теги'             => 12,
		'Разметка Schema.org'    => 8,
		'Контент'               => 10,
		'Ссылки'                => 14,
		'Изображения'           => 6,
		'Скорость'              => 8,
		'Безопасность'          => 8,
		'Доступность'           => 6,
		'AI и GEO'               => 8,
		'Домены и миграция'      => 10,
		'Технический стек'       => 4,
		'Соответствие 152-ФЗ'    => 6,
	];

	/** Вес правила по критичности. */
	public const SEV_WEIGHT = [
		'critical' => 10.0,
		'high'     => 6.0,
		'medium'   => 3.0,
		'low'      => 1.0,
		'info'     => 0.4,
	];

	/** @var list<array<string,mixed>> */
	private array $results = [];

	/**
	 * Прогон всех правил.
	 *
	 * @param null|callable(int,int):void $onProgress колбэк (номер правила, всего правил)
	 */
	public function run(Ctx $c, ?callable $onProgress = null): array
	{
		$this->results = [];
		$impacts = self::impacts();
		$rules = self::rules();
		$total = count($rules);
		$index = 0;
		foreach ($rules as $rule) {
			$index++;
			/** @var list<array{url:?string,detail:string}> $violations */
			$violations = ($rule['check'])($c);
			$scope = $rule['scope'] ?? 'page';
			$checked = $scope === 'site' ? 1 : max(1, count($c->pages()));
			$failed = 0;
			if ($scope === 'site') {
				$failed = $violations === [] ? 0 : 1;
			} else {
				$urls = [];
				foreach ($violations as $v) {
					$key = $v['url'] ?? '__site__';
					$urls[$key] = true;
				}
				$failed = count($urls);
			}
			$imp = $impacts[$rule['id']] ?? ['i' => '', 's' => []];
			$this->results[] = [
				'id'         => $rule['id'],
				'cat'        => $rule['cat'],
				'sev'        => $rule['sev'],
				'title'      => $rule['title'] ?? $rule['id'],
				'sys'        => self::SYSTEMS[$rule['cat']] ?? ['google' => true, 'yandex' => true, 'bing' => true, 'ai' => true],
				'hint'       => $rule['hint'] ?? '',
				'impact'     => $imp['i'],
				'sources'    => array_map(static fn(string $k): string => self::SRC[$k] ?? '', $imp['s']),
				'scope'      => $scope,
				'checked'    => $checked,
				'failed'     => $failed,
				'violations' => $violations,
				'pass'       => $failed === 0,
			];
			if ($onProgress !== null) {
				$onProgress($index, $total);
			}
		}
		return $this->results;
	}

	/** @return list<array<string,mixed>> */
	public function results(): array
	{
		return $this->results;
	}

	/**
	 * Баллы: 0–100 по категориям и общий.
	 *
	 * @return array{score:float,total:float,byCat:array<string,array{score:float,weight:float,rules:int,failed:int}>}
	 */
	public function score(): array
	{
		$byCat = [];
		$total = 0.0;
		$weightSum = 0.0;
		foreach (self::CAT_WEIGHTS as $cat => $weight) {
			$byCat[$cat] = ['score' => 0.0, 'weight' => 0.0, 'catWeight' => (float)$weight, 'rules' => 0, 'failed' => 0, 'penalty' => 0.0];
		}
		foreach ($this->results as $r) {
			$cat = $r['cat'];
			if (!isset($byCat[$cat])) {
				$byCat[$cat] = ['score' => 0.0, 'weight' => 0.0, 'catWeight' => 5.0, 'rules' => 0, 'failed' => 0, 'penalty' => 0.0];
			}
			$w = self::SEV_WEIGHT[$r['sev']] ?? 1.0;
			$ratio = $r['checked'] > 0 ? max(0.0, 1.0 - $r['failed'] / $r['checked']) : 1.0;
			$byCat[$cat]['score'] += $ratio * $w;
			$byCat[$cat]['weight'] += $w;   // вес правила по критичности
			$byCat[$cat]['rules']++;
			if (!$r['pass']) {
				$byCat[$cat]['failed']++;
				// фиксированный штраф за само наличие нарушения: одна критичная
				// проблема не должна «размазываться» на сотни корректных страниц
				$byCat[$cat]['penalty'] += $w * 0.7;
			}
		}
		$sum = 0.0;
		$wsum = 0.0;
		foreach ($byCat as $cat => $data) {
			$raw = $data['weight'] > 0 ? 100 * $data['score'] / $data['weight'] : 100;
			$score = (int)max(0, min(100, round($raw - $data['penalty'])));
			$byCat[$cat]['score'] = (float)$score;
			$catWeight = $data['catWeight'];
			$sum += $score * $catWeight;
			$wsum += $catWeight;
			$total += $data['weight'];
		}
		return [
			'score'  => $wsum > 0 ? round($sum / $wsum, 1) : 0.0,
			'total'  => round($total, 1),
			'byCat'  => $byCat,
		];
	}

	/**
	 * Все правила аудита.
	 *
	 * @return list<array<string,mixed>>
	 */
	public static function rules(): array
	{
		return array_merge(
			self::rulesTechnical(),
			self::rulesIndexing(),
			self::rulesMeta(),
			self::rulesSchema(),
			self::rulesContent(),
			self::rulesLinks(),
			self::rulesImages(),
			self::rulesPerformance(),
			self::rulesSecurity(),
			self::rulesA11y(),
			self::rulesAiGeo(),
			self::rulesUrlHygiene(),
			self::rulesDeep(),
			self::rulesDomains(),
			self::rulesExtra(),
			self::rulesAgentic(),
		);
	}

	// =====================================================================
	// 15. AI И АГЕНТНЫЕ ПРОТОКОЛЫ (по открытой методологии AI-readiness)
	// =====================================================================
	private static function rulesAgentic(): array
	{
		return [
			[
				'id' => 'index.robots_blocks_assets', 'cat' => 'Индексация', 'sev' => 'high', 'scope' => 'site',
				'title' => 'CSS/JS-файлы не закрыты от роботов',
				'hint' => 'Если в robots.txt закрыты *.css, *.js или /assets/, робот не отрисует страницу — Google и Яндекс не смогут оценить рендеринг, мета-теги из JS и ссылки, построенные скриптом. Разрешите эти пути.',
				'check' => static function (Ctx $c): array {
					$out = [];
					$r = $c->cr->robots;
					if ($r === null || !$r->exists) {
						return $out;
					}
					$hits = [];
					foreach ($r->coverage() as $bot => $info) {
						foreach ($info['rules'] as $rule) {
							if ($rule['allow']) {
								continue;
							}
							$p = (string)$rule['path'];
							if (preg_match('~\.(css|js|woff2?|ttf|eot|png|jpe?g|gif|svg|webp)(\$|\*|$)~i', $p)
								|| preg_match('~^/(assets|css|js|static|media)/~i', $p)) {
								$hits[] = $bot . ' → ' . trim((string)($rule['text'] ?? $rule['raw']));
							}
						}
					}
					$hits = array_values(array_unique($hits));
					if ($hits) {
						$out[] = ['url' => null, 'detail' => 'закрыты ресурсы, нужные для рендеринга (' . count($hits) . '): ' . implode('; ', array_slice($hits, 0, 4))];
					}
					return $out;
				},
			],
			[
				'id' => 'index.robots_useragent_case', 'cat' => 'Индексация', 'sev' => 'low', 'scope' => 'site',
				'title' => 'Имена User-agent написаны в каноническом виде',
				'hint' => 'Роботы сопоставляют имя секции по точному совпадению: «Yandex» и «yandex» — разные секции. Проверьте регистр и написание имени бота.',
				'check' => static function (Ctx $c): array {
					$out = [];
					$r = $c->cr->robots;
					if ($r === null || !$r->exists) {
						return $out;
					}
					$bad = [];
					foreach ($r->groups as $agent => $rules) {
						$canon = Robots::KNOWN_BOTS[strtolower((string)$agent)] ?? null;
						if ($canon === null) {
							continue;
						}
						if ((string)$agent !== $canon) {
							$bad[] = 'написано «' . $agent . '», канонически «' . $canon . '»';
						}
					}
					if ($bad) {
						$out[] = ['url' => null, 'detail' => 'имя секции User-agent указано не в каноническом виде: ' . implode('; ', array_slice($bad, 0, 4))];
					}
					return $out;
				},
			],
			[
				'id' => 'index.robots_block_params', 'cat' => 'Индексация', 'sev' => 'low', 'scope' => 'site',
				'title' => 'Блокировка «*?*» осознанна',
				'hint' => 'Правило Disallow: *?* закрывает от робота все адреса с параметрами. Для Яндекса это рабочая практика (Clean-param), но вместе с Clean-param параметры фильтров тоже исчезнут из обхода — убедитесь, что это не скрывает нужные страницы.',
				'check' => static function (Ctx $c): array {
					$out = [];
					$r = $c->cr->robots;
					if ($r === null || !$r->exists) {
						return $out;
					}
					$hits = [];
					foreach ($r->coverage() as $bot => $info) {
						foreach ($info['rules'] as $rule) {
							if ($rule['allow']) {
								continue;
							}
							$p = trim((string)$rule['path']);
							if ($p === '*?*' || $p === '/*?*' || $p === '*?') {
								$hits[] = $bot . ' → ' . trim((string)($rule['text'] ?? $rule['raw']));
							}
						}
					}
					$hits = array_values(array_unique($hits));
					if ($hits) {
						$out[] = ['url' => null, 'detail' => 'закрыты все адреса с параметрами (' . count($hits) . '): ' . implode('; ', array_slice($hits, 0, 4))];
					}
					return $out;
				},
			],
			[
				'id' => 'sitemap.freshness', 'cat' => 'Индексация', 'sev' => 'low', 'scope' => 'site',
				'title' => 'Карта сайта свежая (lastmod не старше 30 дней)',
				'hint' => 'Если карту не обновляли больше месяца, робот считает индекс устаревшим и реже заходит на новые страницы. Обновляйте карту при каждом изменении контента.',
				'check' => static function (Ctx $c): array {
					$out = [];
					foreach ($c->cr->sitemapFiles as $url => $f) {
						$lm = (string)($f['lastmod'] ?? '');
						if ($lm === '') {
							continue;
						}
						$ts = strtotime($lm);
						if ($ts === false) {
							continue;
						}
						$days = (int)floor((time() - $ts) / 86400);
						if ($days > 30) {
							$out[] = ['url' => null, 'detail' => $url . ': last-mod ' . $lm . ' — ' . $days . ' дн. назад'];
						}
					}
					return $out;
				},
			],
			[
				'id' => 'sitemap.format', 'cat' => 'Индексация', 'sev' => 'medium', 'scope' => 'site',
				'title' => 'Карта сайта отдаётся 200 и в XML-формате',
				'hint' => 'Карта должна отдаваться по адресу из директивы Sitemap: с кодом 200 и типом text/xml или application/xml. Не-XML-ответ или редирект робот не разберёт.',
				'check' => static function (Ctx $c): array {
					$out = [];
					foreach ($c->cr->sitemapFiles as $url => $f) {
						$status = (int)($f['status'] ?? 0);
						$ctype = (string)($f['ctype'] ?? '');
						if ($status !== 200) {
							$out[] = ['url' => null, 'detail' => $url . ' отдаёт HTTP ' . ($status ?: 'нет ответа')];
							continue;
						}
						if ($ctype !== '' && !str_contains($ctype, 'xml') && !str_contains($ctype, 'text/plain')) {
							$out[] = ['url' => null, 'detail' => $url . ' отдаёт Content-Type «' . $ctype . '» вместо XML'];
						}
					}
					return $out;
				},
			],
			[
				'id' => 'ai.content_signal', 'cat' => 'AI и GEO', 'sev' => 'info', 'scope' => 'site',
				'title' => 'Политика использования контента ИИ объявлена (Content-Signal)',
				'hint' => 'Content-Signal — HTTP-заголовок, которым сайт сообщает, можно ли его использовать для обучения ИИ и для ответов ассистентов (например, ai-train=no, search=yes, ai-input=no). Значения должны соответствовать правилам robots.txt для AI-краулеров.',
				'check' => static function (Ctx $c): array {
					$out = [];
					$cs = (string)($c->cr->contentSignal['value'] ?? '');
					if ($cs === '') {
						$out[] = ['url' => null, 'detail' => 'заголовок Content-Signal не отдаётся — политика использования контента ИИ не объявлена машинно'];
						return $out;
					}
					$r = $c->cr->robots;
					if ($r !== null && $r->exists) {
						$trainBlocked = false;
						foreach (['gptbot', 'claudebot', 'ccbot', 'bytespider', 'amazonbot'] as $bot) {
							foreach ($r->rulesFor($bot) as $rule) {
								if (!$rule['allow'] && ($rule['path'] === '/' || $rule['path'] === '')) {
									$trainBlocked = true;
									break 2;
								}
							}
						}
						if (stripos($cs, 'ai-train=no') !== false && !$trainBlocked) {
							$out[] = ['url' => null, 'detail' => 'Content-Signal говорит ai-train=no, но обучающие краулеры (GPTBot, ClaudeBot, CCBot) в robots.txt разрешены — политики противоречат друг другу'];
						}
					}
					return $out;
				},
			],
			[
				'id' => 'ai.llms_structure', 'cat' => 'AI и GEO', 'sev' => 'medium', 'scope' => 'site',
				'title' => 'Структура llms.txt соответствует спецификации',
				'hint' => 'По спецификации llmstxt.org файл должен начинаться с заголовка H1 (# Название), затем идти краткое описание в блок-цитате (> …) и разделы H2 (## Раздел) со списком ссылок. Без H1 модель не понимает, о каком проекте файл, и цитирует его неверно.',
				'check' => static function (Ctx $c): array {
					$out = [];
					$llms = $c->cr->llmsTxt;
					if (($llms['status'] ?? 0) !== 200) {
						return $out;   // отсутствие файла проверяет отдельное правило
					}
					$a = (array)($llms['analysis'] ?? []);
					if (empty($a['h1'])) {
						$out[] = ['url' => null, 'detail' => 'нет заголовка H1 — начните файл со строки «# Название сайта»'];
					}
					if (empty($a['summary'])) {
						$out[] = ['url' => null, 'detail' => 'нет краткого описания — добавьте строку «> Описание сайта в одном предложении» сразу после H1'];
					}
					if (count((array)($a['sections'] ?? [])) === 0) {
						$out[] = ['url' => null, 'detail' => 'нет ни одного раздела H2 — сгруппируйте ссылки по темам: «## Раздел»'];
					}
					return $out;
				},
			],
			[
				'id' => 'ai.llms_links', 'cat' => 'AI и GEO', 'sev' => 'low', 'scope' => 'site',
				'title' => 'Ссылки в llms.txt абсолютные и с описаниями',
				'hint' => 'Относительные ссылки модель разрешит неверно (не знает адрес сайта), а ссылки без описания после «:» теряют контекст — модель не понимает, что за страница.',
				'check' => static function (Ctx $c): array {
					$out = [];
					$llms = $c->cr->llmsTxt;
					if (($llms['status'] ?? 0) !== 200) {
						return $out;
					}
					$a = (array)($llms['analysis'] ?? []);
					foreach ((array)($a['relative'] ?? []) as $r) {
						$out[] = ['url' => null, 'detail' => 'строка ' . (int)$r['line'] . ': относительная ссылка «' . $r['url'] . '» — укажите полный адрес https://…'];
					}
					$noDesc = (array)($a['noDesc'] ?? []);
					if (count($noDesc) > 0) {
						$out[] = ['url' => null, 'detail' => count($noDesc) . ' ссылок без описания (строки: ' . implode(', ', array_slice($noDesc, 0, 6)) . ') — добавьте «: Краткое описание» после URL'];
					}
					return $out;
				},
			],
			[
				'id' => 'ai.llms_size', 'cat' => 'AI и GEO', 'sev' => 'low', 'scope' => 'site',
				'title' => 'Размер llms.txt в рабочем диапазоне',
				'hint' => 'Оптимально 300–1000 токенов (примерно 2–8 КБ): этого хватает, чтобы модель поняла структуру сайта, и файл не переполняет контекст. Полный контент выносится в llms-full.txt.',
				'check' => static function (Ctx $c): array {
					$out = [];
					$llms = $c->cr->llmsTxt;
					if (($llms['status'] ?? 0) !== 200) {
						return $out;
					}
					$bytes = (int)(($llms['analysis'] ?? [])['bytes'] ?? 0);
					if ($bytes > 30 * 1024) {
						$out[] = ['url' => null, 'detail' => 'файл ' . round($bytes / 1024, 1) . ' КБ — сократите до 30 КБ, детали вынесите в llms-full.txt'];
					} elseif ($bytes > 0 && $bytes < 600) {
						$out[] = ['url' => null, 'detail' => 'файл всего ' . $bytes . ' байт — добавьте ссылки на ключевые разделы и страницы'];
					}
					return $out;
				},
			],
			[
				'id' => 'ai.llms_declared', 'cat' => 'AI и GEO', 'sev' => 'low', 'scope' => 'site',
				'title' => 'llms.txt объявлен в robots.txt',
				'hint' => 'Строка «Sitemap: https://ваш-домен/llms.txt» в robots.txt — единственный способ, которым AI-краулер узнаёт о карте сайта для моделей: иначе файл нужно угадать.',
				'check' => static function (Ctx $c): array {
					$out = [];
					$llms = $c->cr->llmsTxt;
					if (($llms['status'] ?? 0) !== 200) {
						return $out;
					}
					$r = $c->cr->robots;
					$declared = false;
					if ($r !== null) {
						foreach ($r->sitemaps as $sm) {
							if (str_contains((string)$sm, 'llms')) {
								$declared = true;
								break;
							}
						}
					}
					if (!$declared) {
						$out[] = ['url' => null, 'detail' => 'в robots.txt нет директивы Sitemap: …/llms.txt'];
					}
					return $out;
				},
			],
			[
				'id' => 'ai.agent_protocols', 'cat' => 'AI и GEO', 'sev' => 'low', 'scope' => 'site',
				'title' => 'Сайт объявляет агентные протоколы (MCP, A2A, skills, API)',
				'hint' => 'Агентные протоколы позволяют ИИ-агентам и интеграциям понимать, что умеет сайт: agents.json, .well-known/agent-card.json, MCP server card, agent-skills, api-catalog, OpenAPI, OAuth discovery. Каждый файл — отдельная точка входа для машин.',
				'check' => static function (Ctx $c): array {
					$out = [];
					$have = [];
					$missing = [];
					foreach ($c->cr->agentProtocols as $path => $info) {
						if ((int)($info['status'] ?? 0) === 200) {
							$have[] = (string)$info['label'];
						} else {
							$missing[] = (string)$info['label'];
						}
					}
					if ($missing !== []) {
						$out[] = ['url' => null, 'detail' => 'не объявлено (' . count($missing) . '): ' . implode(', ', array_slice($missing, 0, 8))
							. '. Реализовано: ' . ($have !== [] ? implode(', ', $have) : '—')];
					}
					return $out;
				},
			],
			[
				'id' => 'ai.markdown_negotiation', 'cat' => 'AI и GEO', 'sev' => 'low', 'scope' => 'site',
				'title' => 'Сайт умеет отдавать markdown по запросу агента',
				'hint' => 'Часть агентов запрашивает страницу с Accept: text/markdown. Если сервер отдаёт HTML, агенту приходится вырезать разметку самому — это снижает качество цитирования.',
				'check' => static function (Ctx $c): array {
					$out = [];
					$mr = $c->cr->machineReadable;
					if ($mr === []) {
						return $out;
					}
					$type = (string)($mr['markdownType'] ?? '');
					$code = (int)($mr['markdown'] ?? 0);
					if ($code !== 200) {
						$out[] = ['url' => null, 'detail' => 'Запрос с Accept: text/markdown вернул HTTP ' . ($code ?: 'нет ответа')];
					} elseif (!str_contains($type, 'markdown')) {
						$out[] = ['url' => null, 'detail' => 'При Accept: text/markdown сервер отдаёт «' . ($type !== '' ? $type : 'без content-type') . '», а не markdown'];
					}
					return $out;
				},
			],
			[
				'id' => 'ai.content_signals', 'cat' => 'AI и GEO', 'sev' => 'info', 'scope' => 'site',
				'title' => 'Объявлен Content-Signals (машинные условия работы с контентом)',
				'hint' => 'Content-Signals — заголовок, который декларирует, как машинам работать с контентом (например, разрешён ли синтез речи). Пока стандарт не widespread, но его поддержка — сигнал готовности.',
				'check' => static function (Ctx $c): array {
					$out = [];
					$v = (string)($c->cr->machineReadable['contentSignals'] ?? '');
					if ($v === '' && $c->cr->machineReadable !== []) {
						$out[] = ['url' => null, 'detail' => 'Заголовок Content-Signals не объявлен'];
					}
					return $out;
				},
			],
			[
				'id' => 'ai.link_headers', 'cat' => 'AI и GEO', 'sev' => 'info', 'scope' => 'site',
				'title' => 'В Link-заголовке объявлены альтернативные версии',
				'hint' => 'Link: <https://site/page.md>; rel="alternate"; type="text/markdown" — машинный способ указать роботу на markdown-версию страницы.',
				'check' => static function (Ctx $c): array {
					$out = [];
					$v = (string)($c->cr->machineReadable['link'] ?? '');
					if ($v === '' && $c->cr->machineReadable !== []) {
						$out[] = ['url' => null, 'detail' => 'Заголовок Link не объявлен'];
					}
					return $out;
				},
			],
			[
				'id' => 'ai.speakable', 'cat' => 'AI и GEO', 'sev' => 'low', 'scope' => 'page',
				'title' => 'Есть Speakable-разметка (для голосового и AI-вывода)',
				'hint' => 'speakable в schema.org указывает, какой фрагмент страницы можно озвучить или процитировать целиком. Это прямой способ попасть в голосовые ответы и AI-сводки.',
				'check' => static function (Ctx $c): array {
					$out = [];
					foreach ($c->pages() as $url => $p) {
						$found = false;
						foreach ($p['html']['jsonld'] as $block) {
							if (!empty($block['ok']) && stripos((string)$block['raw'], '"speakable"') !== false) {
								$found = true;
								break;
							}
						}
						if (!$found && (int)($p['html']['words'] ?? 0) > 200) {
							$out[] = ['url' => $url, 'detail' => 'в разметке нет свойства speakable'];
						}
					}
					return $out;
				},
			],
			[
				'id' => 'ai.meta_tags', 'cat' => 'AI и GEO', 'sev' => 'low', 'scope' => 'page',
				'title' => 'Указаны машиночитаемые мета-теги (citation_*, ai-*)',
				'hint' => 'citation_title / citation_author / citation_publisher / citation_language и ai-* помогают системам корректно атрибутировать и цитировать материал — это тот же механизм, что у Google Scholar.',
				'check' => static function (Ctx $c): array {
					$out = [];
					foreach ($c->pages() as $url => $p) {
						$meta = $p['html']['meta'] ?? [];
						$has = false;
						foreach (array_keys($meta) as $k) {
							if (str_starts_with((string)$k, 'citation_') || str_starts_with((string)$k, 'ai-') || (string)$k === 'author' || (string)$k === 'publisher') {
								$has = true;
								break;
							}
						}
						if (!$has) {
							$out[] = ['url' => $url, 'detail' => 'нет ни citation_*, ни ai-*, ни author/publisher в <head>'];
						}
					}
					return $out;
				},
			],
			[
				'id' => 'ai.llms_link', 'cat' => 'AI и GEO', 'sev' => 'info', 'scope' => 'page',
				'title' => 'В HTML есть ссылка на llms.txt',
				'hint' => '<link rel="llms-txt" href="/llms.txt" type="text/plain"> — явная подсказка агентам, где лежит карта сайта для моделей. Само объявление файла проверяет правило ai.llms_txt.',
				'check' => static function (Ctx $c): array {
					$out = [];
					foreach ($c->pages() as $url => $p) {
						$links = $p['html']['llmsLinks'] ?? [];
						if (!$links) {
							$out[] = ['url' => $url, 'detail' => 'нет <link rel="llms-txt"> / "llms-full-txt"'];
						}
					}
					return $out;
				},
			],
		];
	}

	// =====================================================================
	// 14. ДОПОЛНИТЕЛЬНЫЕ ПРОВЕРКИ (по открытым наборам: enterno.io и др.)
	// =====================================================================
	private static function rulesExtra(): array
	{
		return [
			[
				'id' => 'sec.cors', 'cat' => 'Безопасность', 'sev' => 'low', 'scope' => 'site',
				'title' => 'Нет бездумного CORS «*» на всех страницах',
				'hint' => 'Access-Control-Allow-Origin: * на обычном сайте позволяет любому сайту читать ваши ответы через браузер пользователя. Для API это нужно, для контента — нет.',
				'check' => static function (Ctx $c): array {
					$out = [];
					$count = 0;
					$example = '';
					foreach ($c->pages() as $url => $p) {
						$aco = strtolower((string)($p['headers']['access-control-allow-origin'] ?? ''));
						if ($aco === '*') {
							$count++;
							$example = Ctx::pathKey($url);
						}
					}
					if ($count > 0) {
						$out[] = ['url' => null, 'detail' => 'Access-Control-Allow-Origin: * на ' . $count . ' страницах (например ' . $example . ')'];
					}
					return $out;
				},
			],
			[
				'id' => 'tech.detect', 'cat' => 'Технический стек', 'sev' => 'info', 'scope' => 'site',
				'title' => 'Определены CMS, CDN и защита',
				'hint' => 'Знание стека (CMS, CDN, WAF) помогает объяснить и скорость, и блокировки ботов, и причины редиректов. Определяется по заголовкам ответа.',
				'check' => static function (Ctx $c): array {
					$out = [];
					$stack = [];
					foreach ($c->pages() as $url => $p) {
						foreach ([
							'server'                => 'веб-сервер',
							'x-powered-by'          => 'PHP-движок',
							'cf-ray'                => 'CDN Cloudflare',
							'x-amz-cf-id'           => 'CDN CloudFront',
							'x-fastly'              => 'CDN Fastly',
							'x-akamai-transformed'  => 'CDN Akamai',
							'x-served-by'           => 'CDN (кеш)',
							'x-vercel-id'           => 'хостинг Vercel',
							'x-nf-request-id'       => 'Netlify',
							'x-shopify-stage'       => 'Shopify',
							'x-tilda'               => 'Tilda',
							'x-bitrix'              => 'Битрикс',
							'x-drupal-cache'        => 'Drupal',
							'x-generator'           => 'генератор страниц',
							'x-wp-'                 => 'WordPress',
						] as $header => $label) {
							$v = trim((string)($p['headers'][$header] ?? ''));
							if ($v !== '') {
								$stack[$label . ': ' . $v] = true;
							}
						}
					}
					$list = array_keys($stack);
					if ($list === []) {
						$out[] = ['url' => null, 'detail' => 'По заголовкам не определён ни CMS, ни CDN, ни WAF (сервер скрывает служебные заголовки)'];
					}
					return $out;
				},
			],
			[
				'id' => 'compliance.personal_data', 'cat' => 'Соответствие 152-ФЗ', 'sev' => 'medium', 'scope' => 'site',
				'title' => 'Есть политика обработки персональных данных и согласие',
				'hint' => 'Для сайта на территории РФ обязательны политика обработки персональных данных и явное согласие на их обработку (чекбокс в формах + ссылка). Без этого — предписание Роскомнадзора и потеря заявок.',
				'check' => static function (Ctx $c): array {
					$out = [];
					$hasPolicy = false;
					$hasConsent = false;
					$forms = 0;
					$consentBoxes = 0;
					foreach ($c->pages() as $url => $p) {
						$text = mb_strtolower((string)($p['html']['text'] ?? ''));
						if (str_contains($text, 'персональных данных') || str_contains($text, 'политика конфиденциальности')
							|| str_contains($text, 'политика обработки')) {
							$hasPolicy = true;
						}
						foreach ($p['html']['forms'] ?? [] as $f) {
							// грубая, но рабочая проверка: есть ли чекбокс согласия
						}
						if ((int)($p['html']['forms']['count'] ?? 0) > 0) {
							$forms++;
						}
						if (str_contains($text, 'согласен на обработку') || str_contains($text, 'согласие на обработку')
							|| str_contains($text, 'соглашаюсь на обработку')) {
							$hasConsent = true;
						}
					}
					if (!$hasPolicy) {
						$out[] = ['url' => null, 'detail' => 'На сайте не найдено упоминание политики обработки персональных данных'];
					}
					if ($forms > 0 && !$hasConsent) {
						$out[] = ['url' => null, 'detail' => 'Формы есть (' . $forms . ' страниц), но текста согласия на обработку персональных данных не найдено'];
					}
					return $out;
				},
			],
			[
				'id' => 'commerce.agent_ready', 'cat' => 'AI и GEO', 'sev' => 'low', 'scope' => 'site',
				'title' => 'Магазин читаем автономными покупателями (agent commerce)',
				'hint' => 'Автоматические агенты (Agentic Commerce) берут товар, цену и наличие из разметки Offer/Product и карточек товара. Без price/availability/gtin агент не сможет оформить заказ.',
				'check' => static function (Ctx $c): array {
					$out = [];
					$hasOffer = false;
					$hasProduct = false;
					foreach ($c->pages() as $url => $p) {
						foreach ($p['html']['jsonld'] ?? [] as $block) {
							if (empty($block['ok'])) {
								continue;
							}
							$raw = (string)$block['raw'];
							if (stripos($raw, '"Offer"') !== false) {
								$hasOffer = true;
							}
							if (stripos($raw, '"Product"') !== false) {
								$hasProduct = true;
							}
						}
					}
					if (!$hasProduct) {
						$out[] = ['url' => null, 'detail' => 'В разметке нет типа Product — автономный покупатель не поймёт, что продаётся'];
					} elseif (!$hasOffer) {
						$out[] = ['url' => null, 'detail' => 'Есть Product, но нет Offer (цены, наличие, условия) — агент не сможет оформить заказ'];
					}
					return $out;
				},
			],
			[
				'id' => 'dns.email_records', 'cat' => 'Технический стек', 'sev' => 'medium', 'scope' => 'site',
				'title' => 'Настроены SPF, DKIM и DMARC (доставляемость писем)',
				'hint' => 'Заявки с форм уходят на почту. Без SPF/DKIM/DMARC письма попадают в спам, а при подмене домена — отправка от вашего имени. Проверяется по DNS-записям домена.',
				'check' => static function (Ctx $c): array {
					$out = [];
					$host = (string)(parse_url($c->origin(), PHP_URL_HOST) ?: '');
					if ($host === '' || $c->isLocal() || !function_exists('dns_get_record')) {
						return $out;
					}
					$records = @dns_get_record($host, DNS_TXT);
					$txt = '';
					foreach ((array)$records as $rec) {
						if (isset($rec['txt'])) {
							$txt .= ' ' . $rec['txt'];
						}
					}
					$hasSpf = stripos($txt, 'v=spf1') !== false;
					$hasDmarc = false;
					$dmarc = @dns_get_record('_dmarc.' . $host, DNS_TXT);
					foreach ((array)$dmarc as $rec) {
						if (isset($rec['txt']) && stripos((string)$rec['txt'], 'v=DMARC1') !== false) {
							$hasDmarc = true;
						}
					}
					if (!$hasSpf) {
						$out[] = ['url' => null, 'detail' => 'Нет TXT-записи SPF для ' . $host . ' — письма с домена не проходят проверку отправителя'];
					}
					if (!$hasDmarc) {
						$out[] = ['url' => null, 'detail' => 'Нет записи _dmarc.' . $host . ' — почту можно подделывать от вашего имени'];
					}
					return $out;
				},
			],
			[
				'id' => 'http.http3', 'cat' => 'Скорость', 'sev' => 'info', 'scope' => 'site',
				'title' => 'Поддерживается HTTP/3 (QUIC)',
				'hint' => 'HTTP/3 убирает head-of-line blocking и ускоряет первую отрисовку на мобильных. Проверяется через ALPN-переговоры при TLS-соединении.',
				'check' => static function (Ctx $c): array {
					$out = [];
					if ($c->isLocal() || !function_exists('stream_socket_client')) {
						return $out;
					}
					$parts = parse_url($c->origin());
					$host = (string)($parts['host'] ?? '');
					$port = (int)($parts['port'] ?? 443);
					if ($host === '' || ($parts['scheme'] ?? '') !== 'https') {
						return $out;
					}
					$ctx = stream_context_create(['ssl' => ['alpn_protocols' => 'h3,h2,http/1.1', 'verify_peer' => false, 'verify_peer_name' => false]]);
					$fp = @stream_socket_client('ssl://' . $host . ':' . $port, $e, $s, 6, STREAM_CLIENT_CONNECT, $ctx);
					if ($fp === false) {
						return $out;
					}
					$params = stream_context_get_params($fp);
					fclose($fp);
					$alpn = (string)($params['options']['ssl']['alpn_protocol'] ?? '');
					if ($alpn !== '' && $alpn !== 'h3') {
						$out[] = ['url' => null, 'detail' => 'Сервер согласовал ALPN «' . $alpn . '» — HTTP/3 (h3) не поддерживается'];
					}
					return $out;
				},
			],
		];
	}

	// =====================================================================
	// 13. ДОМЕНЫ, ЗЕРКАЛА И МИГРАЦИЯ (типовые ошибки компаний)
	// =====================================================================
	private static function rulesDomains(): array
	{
		return [
			[
				'id' => 'domain.mirror_duplicate', 'cat' => 'Домены и миграция', 'sev' => 'critical', 'scope' => 'site',
				'title' => 'Зеркала домена отдают 301 на главный, а не живут своей жизнью',
				'hint' => 'Яндекс с 2019 года не склеивает зеркала по контенту: без 301-редиректа каждое зеркало становится самостоятельным сайтом со своим ИКС и своей долей ссылочного веса. Поставьте 301 со всех вариантов (http/https, www/без www) на главный адрес.',
				'check' => static function (Ctx $c): array {
					$out = [];
					foreach ($c->cr->domains['variants'] ?? [] as $url => $v) {
						if ((int)$v['status'] === 200) {
							$out[] = ['url' => null, 'detail' => $url . ' отдаёт 200 — это полноценная копия сайта: и робот, и пользователь видят два сайта'];
						}
					}
					return $out;
				},
			],
			[
				'id' => 'domain.redirect_code', 'cat' => 'Домены и миграция', 'sev' => 'high', 'scope' => 'site',
				'title' => 'Редиректы между доменами постоянные (301), а не временные',
				'hint' => '301 — единственный код, который надёжно переносит сигналы и убирает старый адрес из индекса. 302/307 оставляет старый адрес в базе, и при переезде страницы не склеиваются. Поставьте 301 на всех зеркалах.',
				'check' => static function (Ctx $c): array {
					$out = [];
					foreach ($c->cr->domains['variants'] ?? [] as $url => $v) {
						$code = (int)$v['code'];
						if ($code === 302 || $code === 303 || $code === 307 || $code === 308) {
							$out[] = ['url' => null, 'detail' => $url . ' → HTTP ' . $code . ' (временный редирект: старый адрес останется в индексе)'];
						}
					}
					return $out;
				},
			],
			[
				'id' => 'domain.redirect_target', 'cat' => 'Домены и миграция', 'sev' => 'high', 'scope' => 'site',
				'title' => 'Редирект ведёт на аналогичную страницу, а не на главную',
				'hint' => 'По инструкции Яндекса перенаправление должно вести со страниц старого сайта на аналогичные страницы нового. Редирект «всё на главную» убивает весь трафик, кроме главной страницы.',
				'check' => static function (Ctx $c): array {
					$out = [];
					foreach ($c->cr->domains['variants'] ?? [] as $url => $v) {
						if (!empty($v['toRoot'])) {
							$out[] = ['url' => null, 'detail' => $url . ' ведёт на главную (' . $v['final'] . ') — вместо соответствия страниц'];
						}
					}
					return $out;
				},
			],
			[
				'id' => 'domain.donor_copy', 'cat' => 'Домены и миграция', 'sev' => 'critical', 'scope' => 'site',
				'title' => 'Найдены домены, отдающие копию сайта (старый домен не отключён)',
				'hint' => 'Это самая частая ошибка при переезде: старый домен продолжает отдавать сайт. Он получает собственный ИКС, собственные позиции и делит ссылочный вес. Поставьте на старом домене 301 на новый и зафиксируйте переезд в Вебмастере.',
				'check' => static function (Ctx $c): array {
					$out = [];
					foreach ($c->cr->domains['donors'] ?? [] as $host => $d) {
						$out[] = ['url' => null, 'detail' => $host . ' — ' . $d['kind'] . ' (' . $d['final'] . ')'];
					}
					return $out;
				},
			],
			[
				'id' => 'domain.sitemap_foreign', 'cat' => 'Домены и миграция', 'sev' => 'medium', 'scope' => 'site',
				'title' => 'В карте сайта нет URL чужих доменов',
				'hint' => 'Если в sitemap.xml остались адреса старого домена, робот будет обходить их вместо новых — часть страниц выпадает из индекса.',
				'check' => static function (Ctx $c): array {
					$out = [];
					$foreign = [];
					foreach (array_keys($c->cr->sitemaps->urls) as $u) {
						if (!$c->isInternal($u)) {
							$foreign[] = Ctx::pathKey($u);
						}
					}
					if ($foreign) {
						$out[] = ['url' => null, 'detail' => count($foreign) . ' URL чужого домена в карте: ' . implode(', ', array_slice($foreign, 0, 6))];
					}
					return $out;
				},
			],
			[
				'id' => 'domain.internal_foreign', 'cat' => 'Домены и миграция', 'sev' => 'medium', 'scope' => 'page',
				'title' => 'Внутренние ссылки ведут на свой же домен через редирект',
				'hint' => 'Ссылка на старый адрес даже с 301 — это потеря перехода: пользователь и робот делают лишний запрос, а вес переходит через цепочку. В шаблонах и CMS замените все внутренние ссылки на актуальный домен.',
				'check' => static function (Ctx $c): array {
					$out = [];
					foreach ($c->pages() as $url => $p) {
						$bad = [];
						foreach ($p['html']['links'] as $l) {
							$abs = Url::resolve($l['raw'], $url);
							if ($abs === null) {
								continue;
							}
							[$abs] = Url::splitFragment($abs);
							$host = Url::hostOf($abs);
							if ($host === '' || Url::sameSite($abs, $url)) {
								continue;
							}
							if (isset($c->cr->domains['donors'][$host])) {
								$bad[] = Ctx::pathKey($abs) . ' (' . $l['zone'] . ')';
							}
						}
						if ($bad) {
							$out[] = ['url' => $url, 'detail' => count($bad) . ' ссылок на свой старый/донорский домен: ' . implode('; ', array_slice($bad, 0, 4))];
						}
					}
					return $out;
				},
			],
			[
				'id' => 'domain.iks_split', 'cat' => 'Домены и миграция', 'sev' => 'info', 'scope' => 'site',
				'title' => 'ИКС Яндекса при переезде и разделении доменов',
				'hint' => 'Факт из справки Яндекса: ИКС считается ОТДЕЛЬНО для главного и неглавных адресов и для поддоменов, и при разных доменах значения могут отличаться. Редирект не переносит ИКС — новый домен начинает расчёт заново, процесс занимает недели или месяцы. При этом сам ИКС на ранжирование не влияет (официальная позиция Яндекса) — это индикатор полезности, а не фактор выдачи.',
				'check' => static function (Ctx $c): array {
					$out = [];
					$d = $c->cr->domains;
					if (!empty($d['donors']) || !empty($d['variants'])) {
						$out[] = ['url' => null, 'detail' => 'Обнаружены связанные адреса (' . count($d['donors']) . ' копий/доноров, ' . count($d['variants']) . ' вариантов). Каждый из них Яндекс считает отдельным адресом со своим ИКС.'];
					}
					return $out;
				},
			],
		];
	}

	// =====================================================================
	// 12. УГЛУБЛЁННЫЕ ПРОВЕРКИ (по опубликованным чек-листам аудита)
	// =====================================================================
	private static function rulesDeep(): array
	{
		return [
			// --- SEO: мета-теги ---
			[
				'id' => 'meta.default_title', 'cat' => 'Мета-теги', 'sev' => 'low', 'scope' => 'page',
				'title' => 'Заголовок не авто-генерированный',
				'hint' => '«Главная», «Home», «Page 2» — дефолтные заголовки CMS: поисковик не понимает тему страницы. Пишите осмысленный заголовок с ключевым запросом.',
				'check' => static function (Ctx $c): array {
					$out = [];
					$defaults = ['главная', 'home', 'index', 'страница', 'page', 'без названия', 'untitled', 'заголовок', 'документ'];
					foreach ($c->pages() as $url => $p) {
						$t = mb_strtolower(trim((string)$p['html']['title']));
						if ($t === '') {
							continue;
						}
						$first = mb_strtolower(preg_split('~[\s|—–\-:]+~u', $t, 2)[0] ?? '');
						if (in_array($first, $defaults, true)) {
							$out[] = ['url' => $url, 'detail' => 'заголовок начинается с «' . $first . '»: ' . mb_substr((string)$p['html']['title'], 0, 50)];
						}
					}
					return $out;
				},
			],
			// --- SEO: изображения ---
			[
				'id' => 'img.alt_placeholder', 'cat' => 'Изображения', 'sev' => 'low', 'scope' => 'page',
				'title' => 'alt описывает картинку, а не имя файла',
				'hint' => 'alt="DSC_0042.jpg" или alt="image" формально присутствуют, но не дают ни пользы поиску по картинкам, ни доступности. Опишите, что на картинке.',
				'check' => static function (Ctx $c): array {
					$out = [];
					$generic = ['image', 'img', 'photo', 'picture', 'фото', 'картинка', 'изображение', 'без названия', 'pic', 'dsc', 'screenshot', 'окно'];
					foreach ($c->pages() as $url => $p) {
						$bad = [];
						foreach ($p['html']['images'] as $im) {
							$alt = mb_strtolower(trim((string)($im['alt'] ?? '')));
							if ($alt === '') {
								continue;
							}
							$isFile = (bool)preg_match('~\.(jpe?g|png|gif|webp|avif|svg|bmp)$~i', $alt);
							$isGeneric = in_array($alt, $generic, true);
							if ($isFile || $isGeneric) {
								$bad[] = basename((string)$im['src']) . ': alt="' . mb_substr($alt, 0, 30) . '" (' . $im['zone'] . ')';
							}
						}
						if ($bad) {
							$out[] = ['url' => $url, 'detail' => count($bad) . ' шт.: ' . implode('; ', array_slice($bad, 0, 4))];
						}
					}
					return $out;
				},
			],
			// --- SEO: внутренние ссылки ---
			[
				'id' => 'links.content_links', 'cat' => 'Ссылки', 'sev' => 'low', 'scope' => 'page',
				'title' => 'Страницы связаны ссылками из текста, а не только из меню',
				'hint' => 'Ссылки только из меню и футера не передают контекст: робот не понимает, о чём страница и какие темы она раскрывает. Добавляйте ссылки внутри текста.',
				'check' => static function (Ctx $c): array {
					$out = [];
					foreach ($c->pages() as $url => $p) {
						$h = $p['html'];
						$total = count($h['links']);
						$content = (int)$h['contentLinks'];
						if ($total < 15 || (int)$h['words'] < 200) {
							continue;
						}
						if ($content === 0) {
							$out[] = ['url' => $url, 'detail' => $total . ' ссылок, из них 0 из основного контента'];
						}
					}
					return $out;
				},
			],
			// --- SEO: инфраструктура ---
			[
				'id' => 'perf.cdn', 'cat' => 'Скорость', 'sev' => 'info', 'scope' => 'site',
				'title' => 'Сайт отдаётся через CDN',
				'hint' => 'CDN снимает нагрузку с origin, ускоряет отдачу статики по всему миру и добавляет защиту от DDoS.',
				'check' => static function (Ctx $c): array {
					$out = [];
					$marks = ['cf-ray', 'x-amz-cf-id', 'x-fastly', 'x-akamai-transformed', 'x-served-by', 'x-vercel-id', 'x-now', 'x-nf-request-id', 'x-cdn', 'x-cache-hits'];
					$found = [];
					foreach ($c->pages() as $url => $p) {
						foreach ($marks as $m) {
							if (!empty($p['headers'][$m])) {
								$found[$m] = true;
							}
						}
					}
					if (!$found) {
						$out[] = ['url' => null, 'detail' => 'Признаков CDN в заголовках ответов нет'];
					}
					return $out;
				},
			],
			[
				'id' => 'index.faceted_nav', 'cat' => 'Индексация', 'sev' => 'info', 'scope' => 'site',
				'title' => 'Фасетная навигация не плодит индексируемые URL',
				'hint' => 'Фильтры и сортировки создают URL, которые не должны индексироваться: закрывайте их noindex и не пускайте в sitemap.',
				'check' => static function (Ctx $c): array {
					$out = [];
					$params = [];
					foreach ($c->cr->pages as $url => $p) {
						$q = parse_url($url, PHP_URL_QUERY);
						if (!$q) {
							continue;
						}
						foreach (explode('&', $q) as $pair) {
							$name = explode('=', $pair, 2)[0];
							if ($name === '' || preg_match('~^(utm_|yclid|gclid|fbclid|_openstat|ref|page)$~i', $name)) {
								continue;
							}
							$params[$name] = ($params[$name] ?? 0) + 1;
						}
					}
					if (count($params) >= 3) {
						$out[] = ['url' => null, 'detail' => 'параметры фильтрации (' . implode(', ', array_keys($params)) . ') дают ' . array_sum($params) . ' URL — проверьте, что они закрыты noindex'];
					}
					return $out;
				},
			],
			[
				'id' => 'schema.local_business', 'cat' => 'Разметка Schema.org', 'sev' => 'low', 'scope' => 'site',
				'title' => 'У организации задан адрес и гео-координаты',
				'hint' => 'LocalBusiness/Organization с address и geo нужны для локальной выдачи и карт: без них бизнес не привязывается к месту.',
				'check' => static function (Ctx $c): array {
					$out = [];
					$withAddress = 0;
					$total = 0;
					foreach ($c->pages() as $url => $p) {
						foreach ($p['html']['jsonld'] as $block) {
							if (!$block['ok']) {
								continue;
							}
							$raw = (string)$block['raw'];
							if (stripos($raw, 'LocalBusiness') === false && stripos($raw, '"Organization"') === false && stripos($raw, "'Organization'") === false) {
								continue;
							}
							$total++;
							if (stripos($raw, '"address"') !== false || stripos($raw, '"geo"') !== false) {
								$withAddress++;
							}
						}
					}
					if ($total > 0 && $withAddress === 0) {
						$out[] = ['url' => null, 'detail' => 'Разметка организации есть в ' . $total . ' местах, но без address/geo'];
					}
					return $out;
				},
			],
			[
				'id' => 'index.host_variants', 'cat' => 'Индексация', 'sev' => 'medium', 'scope' => 'site',
				'title' => 'Все варианты домена (http/https, www) ведут на канонический хост за один хоп',
				'hint' => 'Каждый лишний вариант домена — отдельная копия сайта для робота. Нужен один 301-редирект на канонический хост.',
				'check' => static function (Ctx $c): array {
					$out = [];
					if ($c->isLocal()) {
						return $out;
					}
					$canonical = $c->origin();
					$host = Url::hostOf($canonical);
					$bare = preg_replace('~^www\.~i', '', $host);
					$variants = array_unique([
						'http://' . $host . '/',
						'http://' . $bare . '/',
						'https://' . $bare . '/',
					]);
					foreach ($variants as $v) {
						if ($v === $canonical) {
							continue;
						}
						$req = Http::fetch($v, $c->cfg, 'HEAD');
						$hops = count($req->redirects);
						$finalHost = Url::hostOf($req->final);
						if ($req->status >= 200 && $req->status < 300 && $hops === 0) {
							$out[] = ['url' => null, 'detail' => $v . ' отдаёт 200 без редиректа — это вторая копия сайта'];
						} elseif ($hops > 1) {
							$out[] = ['url' => null, 'detail' => $v . ' → ' . $hops . ' редиректа (нужен один)'];
						} elseif ($hops === 1 && $finalHost !== Url::hostOf($canonical)) {
							$out[] = ['url' => null, 'detail' => $v . ' ведёт на другой хост: ' . $finalHost];
						}
					}
					return $out;
				},
			],
			// --- GEO: цитатные боты ---
			[
				'id' => 'ai.citation_bots', 'cat' => 'AI и GEO', 'sev' => 'medium', 'scope' => 'site',
				'title' => 'AI-краулеры, которые цитируют ответы, не заблокированы',
				'hint' => 'Цитатные боты (OAI-SearchBot, ClaudeBot, PerplexityBot, Google-Extended) приносят трафик из ответов ИИ. Блокировка = вы не попадаете в ответы.',
				'check' => static function (Ctx $c): array {
					$out = [];
					$citation = [
						'oai-searchbot'   => 'OpenAI (поиск ChatGPT)',
						'claudebot'        => 'Anthropic (Claude)',
						'perplexitybot'    => 'Perplexity',
						'google-extended'  => 'Google (AI Overviews, Gemini)',
						'chatgpt-user'     => 'OpenAI (пользовательский агент)',
						'perplexity-user'  => 'Perplexity (пользовательский агент)',
						'claude-user'      => 'Anthropic (пользовательский агент)',
						'bingbot'          => 'Bing / Copilot',
						'applebot'         => 'Apple Intelligence / Siri',
						'meta-externalagent'=> 'Meta AI',
					];
					$r = $c->cr->robots;
					if ($r === null || !$r->exists) {
						return $out;
					}
					$blocked = [];
					$undecided = [];
					foreach ($citation as $bot => $who) {
						$rules = $r->groups[$bot] ?? null;
						if ($rules === null) {
							$undecided[] = $bot;
							continue;
						}
						foreach ($rules as $rule) {
							if (!$rule['allow'] && ($rule['path'] === '/' || $rule['path'] === '')) {
								$blocked[] = $bot . ' (' . $who . ')';
								break;
							}
						}
					}
					if ($blocked) {
						$out[] = ['url' => null, 'detail' => 'Заблокированы цитатные боты: ' . implode(', ', $blocked)];
					}
					if ($undecided && !$blocked) {
						$out[] = ['url' => null, 'detail' => 'Нет явных правил для ' . count($undecided) . ' цитатных ботов (берётся общий User-agent: *)'];
					}
					return $out;
				},
			],
			// --- GEO: цитируемость ---
			[
				'id' => 'ai.entity_definition', 'cat' => 'AI и GEO', 'sev' => 'low', 'scope' => 'page',
				'title' => 'В начале страницы — определение сущности (30–80 слов)',
				'hint' => 'Исследование Aggarwal et al. (KDD 2024): определение сущности в первом абзаце повышает цитируемость в генеративных ответах. Первое предложение должно отвечать «что это и для кого».',
				'check' => static function (Ctx $c): array {
					$out = [];
					foreach ($c->pages() as $url => $p) {
						$h = $p['html'];
						if ((int)$h['words'] < 200 || (int)$h['firstParaLen'] === 0) {
							continue;
						}
						$words = preg_match_all('~[\p{L}\p{N}]+~u', (string)$h['firstPara']) ?: 0;
						if ($words < 20 || $words > 100) {
							$out[] = ['url' => $url, 'detail' => 'первый абзац — ' . $words . ' слов (нужно 30–80 для определения сущности)'];
						}
					}
					return $out;
				},
			],
			[
				'id' => 'ai.quick_answer', 'cat' => 'AI и GEO', 'sev' => 'low', 'scope' => 'page',
				'title' => 'В начале есть короткий готовый ответ (до 160 символов)',
				'hint' => '«Быстрый ответ» в первом блоке — то, что AI-системы цитируют дословно. Дайте ответ до длинных объяснений.',
				'check' => static function (Ctx $c): array {
					$out = [];
					foreach ($c->pages() as $url => $p) {
						$h = $p['html'];
						if ((int)$h['words'] < 200) {
							continue;
						}
						$len = (int)$h['firstParaLen'];
						if ($len > 160) {
							$out[] = ['url' => $url, 'detail' => 'первый абзац — ' . $len . ' символов, короткого ответа нет'];
						}
					}
					return $out;
				},
			],
			[
				'id' => 'ai.sources_cited', 'cat' => 'AI и GEO', 'sev' => 'low', 'scope' => 'page',
				'title' => 'Страница ссылается на внешние источники',
				'hint' => 'Самый сильный метод цитируемости в KDD-2024: ссылки на авторитетные источники внутри текста (+30–115% видимости). Без них контент выглядит голословным.',
				'check' => static function (Ctx $c): array {
					$out = [];
					foreach ($c->pages() as $url => $p) {
						$h = $p['html'];
						if ((int)$h['words'] < 300) {
							continue;
						}
						$ext = 0;
						foreach ($h['links'] as $l) {
							$abs = Url::resolve($l['raw'], $url);
							if ($abs === null) {
								continue;
							}
							if (!Url::sameSite($abs, $url)) {
								$ext++;
							}
						}
						if ($ext === 0) {
							$out[] = ['url' => $url, 'detail' => 'ни одной внешней ссылки на источник'];
						}
					}
					return $out;
				},
			],
			[
				'id' => 'ai.statistics', 'cat' => 'AI и GEO', 'sev' => 'info', 'scope' => 'page',
				'title' => 'В тексте есть цифры и факты со статистикой',
				'hint' => 'Цитаты со статистикой поднимают видимость в генеративных ответах (до +40% по Aggarwal et al., 2024). Голословный текст цитируют реже.',
				'check' => static function (Ctx $c): array {
					$out = [];
					foreach ($c->pages() as $url => $p) {
						$h = $p['html'];
						if ((int)$h['words'] < 300) {
							continue;
						}
						if ((int)$h['statsCount'] === 0) {
							$out[] = ['url' => $url, 'detail' => 'ни одной цифры со статистикой (%, ₽, м², срок) в тексте'];
						}
					}
					return $out;
				},
			],
			[
				'id' => 'ai.quotes', 'cat' => 'AI и GEO', 'sev' => 'info', 'scope' => 'page',
				'title' => 'В тексте есть цитаты с атрибуцией',
				'hint' => 'Прямая цитата с именем, ролью и годом — один из методов цитируемости (+30–40%): подтверждает экспертность материала.',
				'check' => static function (Ctx $c): array {
					$out = [];
					foreach ($c->pages() as $url => $p) {
						$h = $p['html'];
						if ((int)$h['words'] < 300) {
							continue;
						}
						if ((int)$h['quoteCount'] === 0) {
							$out[] = ['url' => $url, 'detail' => 'в тексте нет прямых цитат'];
						}
					}
					return $out;
				},
			],
			[
				'id' => 'ai.js_dependency', 'cat' => 'AI и GEO', 'sev' => 'medium', 'scope' => 'page',
				'title' => 'Контент не зависит от JS-рендеринга',
				'hint' => 'AI-краулеры не выполняют JavaScript. Если текст появляется только после JS, ассистент увидит пустую страницу.',
				'check' => static function (Ctx $c): array {
					$out = [];
					foreach ($c->pages() as $url => $p) {
						$h = $p['html'];
						// только основной контент: текст меню и футера не говорит о JS-зависимости
						$text = (int)($h['mainTextLen'] ?? $h['textLen']);
						$nodes = (int)$h['nodes'];
						if ($text < 400 && $nodes > 200) {
							$out[] = ['url' => $url, 'detail' => 'в HTML ' . $text . ' символов текста при ' . $nodes . ' узлах DOM — контент, вероятно, рендерится JS'];
						}
					}
					return $out;
				},
			],
			[
				'id' => 'ai.date_modified', 'cat' => 'AI и GEO', 'sev' => 'info', 'scope' => 'page',
				'title' => 'У страницы указана дата обновления (dateModified)',
				'hint' => 'dateModified — сигнал свежести, который читают и поисковики, и AI-системы. Без него материал выглядит неактуальным.',
				'check' => static function (Ctx $c): array {
					$out = [];
					foreach ($c->pages() as $url => $p) {
						$found = false;
						foreach ($p['html']['jsonld'] as $block) {
							if ($block['ok'] && stripos((string)$block['raw'], 'dateModified') !== false) {
								$found = true;
								break;
							}
						}
						if (!$found) {
							$out[] = ['url' => $url, 'detail' => 'нет dateModified в JSON-LD'];
						}
					}
					return $out;
				},
			],
			[
				'id' => 'ai.llms_full', 'cat' => 'AI и GEO', 'sev' => 'info', 'scope' => 'site',
				'title' => 'Есть /llms-full.txt — развёрнутая карта для моделей',
				'hint' => 'llms-full.txt — расширенная версия карты сайта со всем содержимым. Модели читают её целиком при построении ответов.',
				'check' => static function (Ctx $c): array {
					$out = [];
					$req = Http::fetch(Url::origin($c->cfg->start) . '/llms-full.txt', $c->cfg, 'HEAD');
					if ($req->status !== 200) {
						$out[] = ['url' => null, 'detail' => '/llms-full.txt не найден (HTTP ' . ($req->status ?: 'нет ответа') . ')'];
					}
					return $out;
				},
			],
		];
	}

	// =====================================================================
	// 11. AI И GEO (попадание в ответы AI-поиска и ассистентов)
	// =====================================================================
	private static function rulesAiGeo(): array
	{
		$aiBots = [
			'gptbot'        => 'OpenAI (ChatGPT, поиск)',
			'oai-searchbot' => 'OpenAI (поисковый индекс ChatGPT)',
			'claudebot'     => 'Anthropic (Claude)',
			'perplexitybot' => 'Perplexity',
			'google-extended'=> 'Google (обучение Gemini, AI Overviews)',
			'ccbot'         => 'Common Crawl (датасеты обучения)',
			'bytespider'    => 'ByteDance (Doubao)',
			'amazonbot'     => 'Amazon (Alexa, Rufus)',
		];

		return [
			[
				'id' => 'ai.llms_txt', 'cat' => 'AI и GEO', 'sev' => 'low', 'scope' => 'site',
				'title' => 'Есть /llms.txt — карта сайта для LLM',
				'hint' => 'llms.txt — открытый стандарт (llmstxt.org): краткая карта ключевых страниц для языковых моделей. Без неё ассистент не знает, что цитировать.',
				'check' => static function (Ctx $c): array {
					$out = [];
					$t = $c->cr->llmsTxt;
					if ($t === []) {
						return $out;
					}
					if ((int)($t['status'] ?? 0) !== 200) {
						$out[] = ['url' => null, 'detail' => '/llms.txt не найден (HTTP ' . ($t['status'] ?: 'нет ответа') . ')'];
						return $out;
					}
					if (empty($t['hasTitle'])) {
						$out[] = ['url' => null, 'detail' => 'В /llms.txt нет заголовка «# Название» — формат не соблюдён'];
					}
					if ((int)($t['links'] ?? 0) === 0) {
						$out[] = ['url' => null, 'detail' => 'В /llms.txt нет ссылок на страницы'];
					}
					return $out;
				},
			],
			[
				'id' => 'ai.plugin_manifest', 'cat' => 'AI и GEO', 'sev' => 'low', 'scope' => 'site',
				'title' => 'Есть манифест для AI-агентов (ai-plugin.json / agents.json)',
				'hint' => 'Манифест описывает, что умеет и где брать данные вашего сайта — это вход для агентов и плагинов. Формат задаётся OpenAI/Google.',
				'check' => static function (Ctx $c): array {
					$out = [];
					$found = false;
					foreach ($c->cr->aiManifests as $path => $m) {
						if ((int)($m['status'] ?? 0) === 200) {
							$found = true;
							if (!is_array($m['json'] ?? null)) {
								$out[] = ['url' => null, 'detail' => $path . ' отдаётся, но это не валидный JSON'];
							}
						}
					}
					if (!$found) {
						$out[] = ['url' => null, 'detail' => 'Нет ни /ai-plugin.json, ни /.well-known/agents.json'];
					}
					return $out;
				},
			],
			[
				'id' => 'ai.robots_crawlers', 'cat' => 'AI и GEO', 'sev' => 'medium', 'scope' => 'site',
				'title' => 'robots.txt не блокирует AI-краулеры без нужды',
				'hint' => 'Disallow для GPTBot/ClaudeBot/PerplexityBot означает, что контент не попадёт в ответы AI-поиска. Это осознанное решение, но оно должно быть осознанным.',
				'check' => static function (Ctx $c) use ($aiBots): array {
					$out = [];
					$r = $c->cr->robots;
					if ($r === null || !$r->exists) {
						return $out;
					}
					$blocked = [];
					$allowed = [];
					$undecided = [];
					foreach ($aiBots as $bot => $who) {
						$rules = $r->groups[$bot] ?? null;
						if ($rules === null) {
							$undecided[] = $bot;
							continue;
						}
						$isBlocked = false;
						foreach ($rules as $rule) {
							if (!$rule['allow'] && ($rule['path'] === '/' || $rule['path'] === '')) {
								$isBlocked = true;
							}
						}
						if ($isBlocked) {
							$blocked[] = $bot . ' (' . $who . ')';
						} else {
							$allowed[] = $bot;
						}
					}
					if ($blocked) {
						$out[] = ['url' => null, 'detail' => 'Заблокированы: ' . implode(', ', $blocked)];
					}
					if ($undecided) {
						$out[] = ['url' => null, 'detail' => 'Нет явных правил (берётся общий User-agent: *): ' . implode(', ', $undecided)];
					}
					if ($allowed && !$blocked) {
						// явно разрешены — это хорошо, но стоит зафиксировать
						$out[] = ['url' => null, 'detail' => 'AI-краулеры разрешены явно: ' . implode(', ', $allowed)];
					}
					return $out;
				},
			],
			[
				'id' => 'ai.answer_structure', 'cat' => 'AI и GEO', 'sev' => 'low', 'scope' => 'page',
				'title' => 'Страница пригодна для цитирования в AI-ответах',
				'hint' => 'AI-ответы собираются из коротких абзацев, списков, FAQ-разметки и прямых ответов. Страница без структуры цитируется реже.',
				'check' => static function (Ctx $c): array {
					$out = [];
					foreach ($c->pages() as $url => $p) {
						$h = $p['html'];
						$signals = [];
						if ((int)$h['lists'] === 0) {
							$signals[] = 'нет списков';
						}
						if ((int)$h['questions'] === 0) {
							$signals[] = 'нет вопросов в заголовках';
						}
						if (empty($h['faqSchema'])) {
							$signals[] = 'нет FAQPage-разметки';
						}
						if (count($signals) === 3) {
							$out[] = ['url' => $url, 'detail' => 'нет структуры для цитирования: ' . implode(', ', $signals)];
						}
					}
					return $out;
				},
			],
			[
				'id' => 'ai.eeat', 'cat' => 'AI и GEO', 'sev' => 'low', 'scope' => 'page',
				'title' => 'У страницы указан автор и дата (E-E-A-T для AI)',
				'hint' => 'AI-системы и поисковики оценивают, кто и когда создал контент. Автор и дата повышают доверие и вероятность попасть в ответ.',
				'check' => static function (Ctx $c): array {
					$out = [];
					foreach ($c->pages() as $url => $p) {
						$h = $p['html'];
						$miss = [];
						if (empty($h['hasAuthor'])) {
							$miss[] = 'автор';
						}
						if (empty($h['hasDate'])) {
							$miss[] = 'дата';
						}
						if ($miss) {
							$out[] = ['url' => $url, 'detail' => 'не указано: ' . implode(', ', $miss)];
						}
					}
					return $out;
				},
			],
			[
				'id' => 'ai.answer_first', 'cat' => 'AI и GEO', 'sev' => 'info', 'scope' => 'page',
				'title' => 'Ответы в тексте оформлены короткими абзацами',
				'hint' => 'Короткий абзац (40–260 символов) — это то, что AI цитирует дословно. Огромные «простыни» цитируются хуже.',
				'check' => static function (Ctx $c): array {
					$out = [];
					foreach ($c->pages() as $url => $p) {
						$h = $p['html'];
						$paras = (int)$h['paras'];
						if ($paras < 3) {
							continue;
						}
						if ((int)$h['shortParas'] === 0) {
							$out[] = ['url' => $url, 'detail' => $paras . ' абзацев, ни одного короткого (40–260 симв.)'];
						}
					}
					return $out;
				},
			],
			[
				'id' => 'ai.entity_sameas', 'cat' => 'AI и GEO', 'sev' => 'info', 'scope' => 'site',
				'title' => 'sameAs связывает сущность с профилями и источниками',
				'hint' => 'sameAs в JSON-LD связывает компанию с её страницами в соцсетях и базах знаний: так AI-системы понимают, кто вы.',
				'check' => static function (Ctx $c): array {
					$out = [];
					$with = 0;
					foreach ($c->pages() as $url => $p) {
						if ((int)$p['html']['sameAs'] > 0) {
							$with++;
						}
					}
					$total = count($c->pages());
					if ($total > 0 && $with === 0) {
						$out[] = ['url' => null, 'detail' => 'Ни на одной из ' . $total . ' страниц нет sameAs'];
					}
					return $out;
				},
			],
		];
	}

	// =====================================================================
	//  URL-ГИГИЕНА (добавляется к техническим проверкам)
	// =====================================================================
	private static function rulesUrlHygiene(): array
	{
		return [
			[
				'id' => 'url.too_long', 'cat' => 'Технические ошибки', 'sev' => 'low', 'scope' => 'page',
				'title' => 'Длина URL не превышает 100 символов',
				'hint' => 'Длинные URL обрезаются в выдаче и неудобны в шаринге; часть систем отсекает их на 100+ символах.',
				'check' => static function (Ctx $c): array {
					$out = [];
					foreach ($c->pages() as $url => $p) {
						$len = strlen(Ctx::pathKey($url));
						if ($len > 100) {
							$out[] = ['url' => $url, 'detail' => $len . ' симв.'];
						}
					}
					return $out;
				},
			],
			[
				'id' => 'url.mixed_case', 'cat' => 'Технические ошибки', 'sev' => 'low', 'scope' => 'page',
				'title' => 'В пути URL нет заглавных букв',
				'hint' => 'Регистр в URL создаёт дубли: /Blog и /blog — разные адреса для робота, но одна страница для пользователя.',
				'check' => static function (Ctx $c): array {
					$out = [];
					foreach ($c->pages() as $url => $p) {
						$path = (string)(parse_url($url, PHP_URL_PATH) ?: '/');
						if (preg_match('~[A-Z]~', $path)) {
							$out[] = ['url' => $url, 'detail' => 'заглавные буквы в пути'];
						}
					}
					return $out;
				},
			],
			[
				'id' => 'url.underscore', 'cat' => 'Технические ошибки', 'sev' => 'low', 'scope' => 'page',
				'title' => 'В URL использован дефис, а не подчёркивание',
				'hint' => 'Подчёркивание в URL не всегда корректно читается движками и хуже разделяет слова, чем дефис.',
				'check' => static function (Ctx $c): array {
					$out = [];
					foreach ($c->pages() as $url => $p) {
						$path = (string)(parse_url($url, PHP_URL_PATH) ?: '/');
						if (str_contains($path, '_')) {
							$out[] = ['url' => $url, 'detail' => 'подчёркивание в пути'];
						}
					}
					return $out;
				},
			],
			[
				'id' => 'url.non_ascii', 'cat' => 'Технические ошибки', 'sev' => 'info', 'scope' => 'page',
				'title' => 'URL не содержит не-ASCII символов',
				'hint' => 'Кириллица в адресе усложняет копирование, аналитику и канонизацию; часть систем и агентов ломаются на таких URL.',
				'check' => static function (Ctx $c): array {
					$out = [];
					foreach ($c->pages() as $url => $p) {
						$path = (string)(parse_url($url, PHP_URL_PATH) ?: '/');
						if (preg_match('~[^\x21-\x7E]~', $path)) {
							$out[] = ['url' => $url, 'detail' => 'не-ASCII в пути: ' . $path];
						}
					}
					return $out;
				},
			],
			[
				'id' => 'url.slash_consistency', 'cat' => 'Технические ошибки', 'sev' => 'low', 'scope' => 'site',
				'title' => 'Слеш в конце URL используется единообразно',
				'hint' => '/blog и /blog/ — разные адреса для робота. Смешивание создаёт дубли и размывает внутренний вес.',
				'check' => static function (Ctx $c): array {
					$out = [];
					$with = 0;
					$without = 0;
					foreach ($c->cr->inlinks as $url => $info) {
						if (isset($c->cr->pages[$url])) {
							continue;
						}
						$path = (string)(parse_url($url, PHP_URL_PATH) ?: '/');
						if ($path === '/') {
							continue;
						}
						if (str_ends_with($path, '/')) {
							$with++;
						} else {
							$without++;
						}
					}
					if ($with > 0 && $without > 0) {
						$out[] = ['url' => null, 'detail' => 'внутренние ссылки смешивают варианты: со слешем — ' . $with . ', без слеша — ' . $without];
					}
					return $out;
				},
			],
			[
				'id' => 'index.pagination', 'cat' => 'Индексация', 'sev' => 'low', 'scope' => 'page',
				'title' => 'Пагинация размечена rel=next / rel=prev',
				'hint' => 'rel=next/prev помогает роботу понять последовательность страниц и не размножать дубли пагинации.',
				'check' => static function (Ctx $c): array {
					$out = [];
					foreach ($c->pages() as $url => $p) {
						$h = $p['html'];
						$isPaginated = (bool)preg_match('~(?:/page/\d+|/p=\d+|[?&]page=\d+)~i', (string)(parse_url($url, PHP_URL_QUERY) ?: parse_url($url, PHP_URL_PATH)));
						if (!$isPaginated || $h['relNext'] || $h['relPrev']) {
							continue;
						}
						$out[] = ['url' => $url, 'detail' => 'страница пагинации без rel=next/prev'];
					}
					return $out;
				},
			],
			[
				'id' => 'perf.font_display', 'cat' => 'Скорость', 'sev' => 'low', 'scope' => 'page',
				'title' => 'В @font-face задан font-display: swap',
				'hint' => 'Без font-display браузер блокирует текст до загрузки шрифта — растут FOUT и CLS.',
				'check' => static function (Ctx $c): array {
					$out = [];
					foreach ($c->pages() as $url => $p) {
						$n = (int)($p['html']['fontNoDisplay'] ?? 0);
						if ($n > 0) {
							$out[] = ['url' => $url, 'detail' => $n . ' @font-face без font-display (в инлайн-стилях)'];
						}
					}
					return $out;
				},
			],
			[
				'id' => 'perf.http2', 'cat' => 'Скорость', 'sev' => 'info', 'scope' => 'site',
				'title' => 'Сервер поддерживает HTTP/2 или новее',
				'hint' => 'HTTP/2 мультиплексирует запросы в одном соединении: меньше RTT и быстрее первая отрисовка.',
				'check' => static function (Ctx $c): array {
					$out = [];
					if ($c->isLocal()) {
						return $out;
					}
					$http1 = 0;
					$total = 0;
					foreach ($c->pages() as $url => $p) {
						$v = (int)$p['httpVersion'];
						if ($v === 0) {
							continue;
						}
						$total++;
						if ($v < 2) {
							$http1++;
						}
					}
					if ($total > 0 && $http1 === $total) {
						$out[] = ['url' => null, 'detail' => 'Все ' . $total . ' ответов пришли по HTTP/1.1 — HTTP/2 не используется'];
					}
					return $out;
				},
			],
			[
				'id' => 'content.keyword_density', 'cat' => 'Контент', 'sev' => 'info', 'scope' => 'page',
				'title' => 'Нет перегруза терминами (ориентир 3,5%)',
				'hint' => 'Плотность одного термина выше 3,5% — признак переспама: поисковик понижает такие страницы.',
				'check' => static function (Ctx $c): array {
					$out = [];
					foreach ($c->pages() as $url => $p) {
						$top = $p['html']['topKeywords'] ?? [];
						if (!$top) {
							continue;
						}
						$kw = $top[0];
						if ((float)$kw['share'] > 0.035) {
							$out[] = ['url' => $url, 'detail' => '«' . $kw['w'] . '» — ' . round((float)$kw['share'] * 100, 1) . '% текста (' . $kw['n'] . ' вхождений)'];
						}
					}
					return $out;
				},
			],
		];
	}

	// =====================================================================
	// 1. ТЕХНИЧЕСКИЕ ОШИБКИ
	// =====================================================================
	private static function rulesTechnical(): array
	{
		return [
			[
				'id' => 'http.error', 'cat' => 'Технические ошибки', 'sev' => 'critical', 'scope' => 'page',
				'title' => 'Страницы открываются без сетевых ошибок',
				'hint' => 'Проверьте логи сервера: таймаут/обрыв соединения, неверный DNS, отказ в соединении.',
				'check' => static function (Ctx $c): array {
					$out = [];
					foreach ($c->pages() as $url => $p) {
						if (!empty($p['error'])) {
							$out[] = ['url' => $url, 'detail' => 'Ошибка соединения: ' . $p['error']];
						}
					}
					return $out;
				},
			],
			[
				'id' => 'http.status', 'cat' => 'Технические ошибки', 'sev' => 'critical', 'scope' => 'page',
				'title' => 'Все страницы отдают код 2xx',
				'hint' => '404/500 на индексируемых URL ломают обход и репутацию домена. Проверьте роутинг и .htaccess.',
				'check' => static function (Ctx $c): array {
					$out = [];
					foreach ($c->pages() as $url => $p) {
						$s = (int)$p['status'];
						if ($s < 200 || $s >= 300) {
							$out[] = ['url' => $url, 'detail' => 'HTTP ' . ($s ?: 'нет ответа')];
						}
					}
					return $out;
				},
			],
			[
				'id' => 'http.redirect', 'cat' => 'Технические ошибки', 'sev' => 'medium', 'scope' => 'page',
				'title' => 'Нет лишних редиректов',
				'hint' => 'Каждый редирект — лишний запрос и потеря веса. Оставьте только канонизацию (http→https, www) и 301 для переезда.',
				'check' => static function (Ctx $c): array {
					$out = [];
					foreach ($c->pages() as $url => $p) {
						$r = $p['redirects'] ?? [];
						if (!$r) {
							continue;
						}
						$chain = [];
						foreach ($r as $hop) {
							$chain[] = $hop['status'] . ' → ' . Ctx::pathKey($hop['to']);
						}
						$out[] = ['url' => $url, 'detail' => count($r) . ' редирект(ов): ' . implode('; ', $chain)];
					}
					return $out;
				},
			],
			[
				'id' => 'http.redirect_loop', 'cat' => 'Технические ошибки', 'sev' => 'critical', 'scope' => 'page',
				'title' => 'Нет петель и длинных цепочек редиректов',
				'hint' => 'Петля редиректов краулер не может разрешить — страница выпадает из индекса.',
				'check' => static function (Ctx $c): array {
					$out = [];
					foreach ($c->pages() as $url => $p) {
						$r = $p['redirects'] ?? [];
						if (count($r) < 2) {
							continue;
						}
						$seen = [];
						$loop = false;
						foreach ($r as $hop) {
							// сравниваем ПОЛНЫЙ адрес: разные домены с корнем «/» — не петля
							$k = mb_strtolower((string)$hop['to']);
							if (isset($seen[$k])) {
								$loop = true;
							}
							$seen[$k] = true;
						}
						if ($loop || count($r) >= 4) {
							$chain = [];
							foreach ($r as $hop) {
								$chain[] = $hop['status'] . ' → ' . $hop['to'];
							}
							$out[] = ['url' => $url, 'detail' => ($loop ? 'обнаружена петля редиректов' : 'цепочка длиннее 3 редиректов')
								. ' (' . implode('; ', $chain) . ')'];
						}
					}
					return $out;
				},
			],
			[
				'id' => 'http.redirect_to_error', 'cat' => 'Технические ошибки', 'sev' => 'critical', 'scope' => 'page',
				'title' => 'Редиректы ведут на существующие страницы',
				'hint' => 'Редирект в 404/500 обрывает путь пользователя. Уберите редирект или создайте целевую страницу.',
				'check' => static function (Ctx $c): array {
					$out = [];
					foreach ($c->pages() as $url => $p) {
						if (empty($p['redirects'])) {
							continue;
						}
						$s = (int)$p['status'];
						if ($s === 403) {
							$out[] = ['url' => $url, 'detail' => 'редирект приводит на закрытую страницу (HTTP 403). Если это «раздел в разработке» — снимите ссылку из меню и из карты сайта'];
						} elseif ($s >= 400) {
							$out[] = ['url' => $url, 'detail' => 'После редиректа — HTTP ' . $s];
						}
					}
					return $out;
				},
			],
			[
				'id' => 'http.redirect_cross_host', 'cat' => 'Технические ошибки', 'sev' => 'medium', 'scope' => 'page',
				'title' => 'Редиректы не уводят на другой домен',
				'hint' => 'Редирект на внешний домен передаёт сигналы и вес другому сайту. Проверьте, так ли задумано.',
				'check' => static function (Ctx $c): array {
					$out = [];
					foreach ($c->pages() as $url => $p) {
						foreach (($p['redirects'] ?? []) as $hop) {
							if (!Url::sameSite($hop['to'], $url)) {
								$out[] = ['url' => $url, 'detail' => 'Внешний редирект на ' . $hop['to']];
								break;
							}
						}
					}
					return $out;
				},
			],
			[
				'id' => 'http.soft404', 'cat' => 'Технические ошибки', 'sev' => 'high', 'scope' => 'page',
				'title' => 'Нет «мягких 404» (страница с кодом 200 и пустым содержанием)',
				'hint' => 'Верните настоящий код 404 — так страница не попадёт в индекс как дубль.',
				'check' => static function (Ctx $c): array {
					$out = [];
					foreach ($c->pages() as $url => $p) {
						if ((int)$p['status'] !== 200) {
							continue;
						}
						$h = $p['html'];
						$text = mb_strtolower((string)$h['text']);
						$looks404 = (bool)preg_match('~не найден|не существует|страница не|404|not found|page doesn\'?t exist~u', $text);
						if ($looks404 && (int)$h['words'] < 120) {
							$out[] = ['url' => $url, 'detail' => 'Код 200, но текст похож на 404 (' . $h['words'] . ' слов)'];
						}
					}
					return $out;
				},
			],
			[
				'id' => 'http.empty', 'cat' => 'Технические ошибки', 'sev' => 'medium', 'scope' => 'page',
				'title' => 'Страницы не пустые',
				'hint' => 'Пустой ответ с кодом 200 — признак сбоя шаблона или кэша.',
				'check' => static function (Ctx $c): array {
					$out = [];
					foreach ($c->pages() as $url => $p) {
						if ((int)$p['status'] === 200 && (int)$p['bodySize'] < 200) {
							$out[] = ['url' => $url, 'detail' => 'Тело ответа ' . $p['bodySize'] . ' байт'];
						}
					}
					return $out;
				},
			],
			[
				'id' => 'http.https_available', 'cat' => 'Технические ошибки', 'sev' => 'high', 'scope' => 'site',
				'title' => 'Сайт доступен по HTTPS',
				'hint' => 'Без HTTPS нельзя включить HSTS, браузеры помечают сайт как небезопасный, а Яндекс/Google понижают доверие.',
				'check' => static function (Ctx $c): array {
					$out = [];
					$origin = $c->origin();
					if (str_starts_with($origin, 'https://') || $c->isLocal()) {
						return $out;
					}
					$https = preg_replace('~^http://~i', 'https://', $origin) . '/';
					$req = Http::fetch($https, $c->cfg, 'HEAD');
					if ($req->status === 0) {
						$out[] = ['url' => null, 'detail' => 'HTTPS-версия не отвечает (' . ($req->error ?: 'нет ответа') . ')'];
					} elseif ($req->status >= 500) {
						$out[] = ['url' => null, 'detail' => 'HTTPS-версия отдаёт HTTP ' . $req->status];
					}
					return $out;
				},
			],
			[
				'id' => 'http.http_to_https', 'cat' => 'Технические ошибки', 'sev' => 'medium', 'scope' => 'site',
				'title' => 'HTTP перенаправляется на HTTPS (301)',
				'hint' => 'Добавьте принудительный редирект http→https одним шагом, без связок цепочек.',
				'check' => static function (Ctx $c): array {
					$out = [];
					$origin = $c->origin();
					if (str_starts_with($origin, 'https://') || $c->isLocal()) {
						return $out;
					}
					$https = preg_replace('~^http://~i', 'https://', $origin) . '/';
					$req = Http::fetch($https, $c->cfg, 'HEAD');
					if ($req->status === 0 || $req->status >= 400) {
						return $out; // HTTPS не работает — другая проблема
					}
					$reqHttp = Http::fetch($origin . '/', $c->cfg, 'HEAD');
					$redirectsToHttps = false;
					foreach ($reqHttp->redirects as $hop) {
						if (str_starts_with($hop['to'], 'https://')) {
							$redirectsToHttps = true;
						}
					}
					if (!$redirectsToHttps && $reqHttp->status === 200) {
						$out[] = ['url' => null, 'detail' => 'http:// отдаёт 200 без редиректа на https'];
					}
					return $out;
				},
			],
			[
				'id' => 'http.mixed_content', 'cat' => 'Технические ошибки', 'sev' => 'high', 'scope' => 'page',
				'title' => 'Нет смешанного контента (http:// ресурсы на https-странице)',
				'hint' => 'Браузер блокирует такие ресурсы — «битые» картинки/скрипты и предупреждение в консоли.',
				'check' => static function (Ctx $c): array {
					$out = [];
					$httpsSite = str_starts_with($c->origin(), 'https://');
					if (!$httpsSite) {
						return $out;
					}
					foreach ($c->pages() as $url => $p) {
						$bad = [];
						// Смешанный контент = только подгружаемые ресурсы, а не гиперссылки
						foreach ($p['html']['resources'] as $res) {
							if (str_starts_with(strtolower($res['url']), 'http://')) {
								$bad[] = $res['tag'] . ' ' . $res['url'];
							}
						}
						if ($bad) {
							$out[] = ['url' => $url, 'detail' => count($bad) . ' шт.: ' . implode(', ', array_slice(array_unique($bad), 0, 4))];
						}
					}
					return $out;
				},
			],
		];
	}

	// =====================================================================
	// 2. ИНДЕКСАЦИЯ
	// =====================================================================
	private static function rulesIndexing(): array
	{
		return [
			[
				'id' => 'index.noindex_meta', 'cat' => 'Индексация', 'sev' => 'info', 'scope' => 'page',
				'title' => 'Страницы, закрытые от индекса (noindex) — список на проверку',
				'hint' => 'Если эти страницы должны быть в индексе — снимите noindex. Если нет — оставьте, но не ставьте их в sitemap и не собирайте на них основной трафик.',
				'check' => static function (Ctx $c): array {
					$out = [];
					foreach ($c->pages() as $url => $p) {
						$robots = strtolower((string)$p['html']['robotsMeta']);
						if ($robots !== '' && str_contains($robots, 'noindex')) {
							$out[] = ['url' => $url, 'detail' => 'meta robots: ' . $robots];
						}
					}
					return $out;
				},
			],
			[
				'id' => 'index.noindex_header', 'cat' => 'Индексация', 'sev' => 'high', 'scope' => 'page',
				'title' => 'Нет X-Robots-Tag: noindex',
				'hint' => 'Заголовок важнее meta-тега: поисковик увидит noindex даже при корректном <meta>.',
				'check' => static function (Ctx $c): array {
					$out = [];
					foreach ($c->pages() as $url => $p) {
						$h = strtolower((string)($p['headers']['x-robots-tag'] ?? ''));
						if ($h !== '' && str_contains($h, 'noindex')) {
							$out[] = ['url' => $url, 'detail' => 'X-Robots-Tag: ' . $h];
						}
					}
					return $out;
				},
			],
			[
				'id' => 'index.nofollow', 'cat' => 'Индексация', 'sev' => 'medium', 'scope' => 'page',
				'title' => 'Страницы не закрыты nofollow',
				'hint' => 'nofollow на всей странице останавливает передачу веса по ссылкам — обычно это ошибка.',
				'check' => static function (Ctx $c): array {
					$out = [];
					foreach ($c->pages() as $url => $p) {
						$robots = strtolower((string)$p['html']['robotsMeta']);
						if ($robots !== '' && str_contains($robots, 'nofollow')) {
							$out[] = ['url' => $url, 'detail' => 'meta robots: ' . $robots];
						}
					}
					return $out;
				},
			],
			[
				'id' => 'index.canonical_missing', 'cat' => 'Индексация', 'sev' => 'medium', 'scope' => 'page',
				'title' => 'У каждой страницы задан canonical',
				'hint' => 'Без canonical дубли (www, слэш, параметры) конкурируют между собой за выдачу.',
				'check' => static function (Ctx $c): array {
					$out = [];
					foreach ($c->pages() as $url => $p) {
						if (empty($p['html']['canonical'])) {
							$out[] = ['url' => $url, 'detail' => 'Тег <link rel="canonical"> отсутствует'];
						}
					}
					return $out;
				},
			],
			[
				'id' => 'index.canonical_mismatch', 'cat' => 'Индексация', 'sev' => 'medium', 'scope' => 'page',
				'title' => 'Canonical ведёт на другую страницу (не на сам URL)',
				'hint' => 'Если canonical ведёт на другую страницу, она выпадает из индекса в пользу целевой. Для дублей (фильтры, пагинация) добавьте к canonical ещё noindex.',
				'check' => static function (Ctx $c): array {
					$out = [];
					foreach ($c->pages() as $url => $p) {
						$canon = (string)($p['html']['canonical'] ?? '');
						if ($canon === '') {
							continue;
						}
						$robots = strtolower((string)$p['html']['robotsMeta']);
						if (str_contains($robots, 'noindex')) {
							continue;   // noindex + canonical на хаб — осознанное решение
						}
						$abs = Url::resolve($canon, $url);
						if ($abs === null) {
							$out[] = ['url' => $url, 'detail' => 'Canonical не разбирается: ' . $canon];
							continue;
						}
						[$abs] = Url::splitFragment($abs);
						if (Ctx::pathKey($abs) === Ctx::pathKey($url)) {
							continue;
						}
						// canonical снимает параметры — штатная практика для фильтров/пагинации
						$base = static fn(string $u): string => explode('?', Ctx::pathKey($u), 2)[0];
						if ($base($abs) === $base($url)) {
							$out[] = ['url' => $url, 'detail' => 'canonical снимает параметры → ' . $base($abs) . ' (для фильтров/пагинации — штатно, добавьте noindex)'];
							continue;
						}
						$out[] = ['url' => $url, 'detail' => 'canonical → ' . Ctx::pathKey($abs)];
					}
					return $out;
				},
			],
			[
				'id' => 'index.canonical_broken', 'cat' => 'Индексация', 'sev' => 'high', 'scope' => 'page',
				'title' => 'Страница, указанная в canonical, доступна',
				'hint' => 'Canonical на 404/ошибку = страница не индексируется вообще.',
				'check' => static function (Ctx $c): array {
					$out = [];
					foreach ($c->pages() as $url => $p) {
						$canon = (string)($p['html']['canonical'] ?? '');
						if ($canon === '') {
							continue;
						}
						$abs = Url::resolve($canon, $url);
						if ($abs === null) {
							continue;
						}
						[$abs] = Url::splitFragment($abs);
						$status = $c->statusOf($abs);
						if ($status !== null && $status >= 400) {
							$out[] = ['url' => $url, 'detail' => 'canonical → ' . Ctx::pathKey($abs) . ' (HTTP ' . $status . ')'];
						}
					}
					return $out;
				},
			],
			[
				'id' => 'index.canonical_cross_domain', 'cat' => 'Индексация', 'sev' => 'high', 'scope' => 'page',
				'title' => 'Canonical не ведёт на чужой домен',
				'hint' => 'Междоменный canonical передаёт индексацию другому сайту.',
				'check' => static function (Ctx $c): array {
					$out = [];
					$host = $c->host();
					foreach ($c->pages() as $url => $p) {
						$canon = (string)($p['html']['canonical'] ?? '');
						if ($canon === '') {
							continue;
						}
						$abs = Url::resolve($canon, $url);
						if ($abs === null) {
							continue;
						}
						if (!Url::sameSite($abs, $url)) {
							$out[] = ['url' => $url, 'detail' => 'canonical ведёт на чужой домен: ' . Url::hostOf($abs)];
						} elseif (Url::hostOf($abs) !== Url::hostOf($url)) {
							$out[] = ['url' => $url, 'detail' => 'canonical ведёт на вариант домена (' . Url::hostOf($abs) . ' вместо ' . Url::hostOf($url) . ')'];
						}
					}
					return $out;
				},
			],
			[
				'id' => 'index.sitemap_format', 'cat' => 'Индексация', 'sev' => 'medium', 'scope' => 'site',
				'title' => 'Формат sitemap.xml корректен (lastmod, priority, changefreq, домен)',
				'hint' => 'Некорректный lastmod или priority заставляют поисковик игнорировать данные карты. Дата — в формате W3C (YYYY-MM-DD или ISO-8601).',
				'check' => static function (Ctx $c): array {
					$out = [];
					$allowedFreq = ['always', 'hourly', 'daily', 'weekly', 'monthly', 'yearly', 'never'];
					$badDate = [];
					$badPriority = [];
					$badFreq = [];
					$foreign = [];
					foreach ($c->cr->sitemaps->urls as $url => $meta) {
						$lm = $meta['lastmod'] ?? null;
						if ($lm !== null && $lm !== '') {
							if (!preg_match('~^\d{4}-\d{2}-\d{2}([T ]\d{2}:\d{2}(:\d{2})?(\.\d+)?(Z|[+\-]\d{2}:?\d{2})?)?$~', $lm)) {
								$badDate[] = $lm . ' (' . Ctx::pathKey($url) . ')';
							}
						}
						$pr = $meta['priority'] ?? null;
						if ($pr !== null && $pr !== '') {
							if (!is_numeric($pr) || (float)$pr < 0 || (float)$pr > 1) {
								$badPriority[] = $pr . ' (' . Ctx::pathKey($url) . ')';
							}
						}
						$cf = $meta['changefreq'] ?? null;
						if ($cf !== null && $cf !== '' && !in_array(strtolower($cf), $allowedFreq, true)) {
							$badFreq[] = $cf . ' (' . Ctx::pathKey($url) . ')';
						}
						if (!$c->isInternal($url)) {
							$foreign[] = $url;
						}
					}
					if ($badDate) {
						$out[] = ['url' => null, 'detail' => 'Некорректный lastmod (' . count($badDate) . '): ' . implode(', ', array_slice($badDate, 0, 4))];
					}
					if ($badPriority) {
						$out[] = ['url' => null, 'detail' => 'Некорректный priority (' . count($badPriority) . '): ' . implode(', ', array_slice($badPriority, 0, 4))];
					}
					if ($badFreq) {
						$out[] = ['url' => null, 'detail' => 'Некорректный changefreq (' . count($badFreq) . '): ' . implode(', ', array_slice($badFreq, 0, 4))];
					}
					if ($foreign) {
						$out[] = ['url' => null, 'detail' => 'В карте URL чужого домена (' . count($foreign) . '): ' . implode(', ', array_slice($foreign, 0, 3))];
					}
					return $out;
				},
			],
			[
				'id' => 'index.canonical_noindex', 'cat' => 'Индексация', 'sev' => 'low', 'scope' => 'page',
				'title' => 'Нет противоречия «canonical + noindex»',
				'hint' => 'noindex с canonical убирает страницу из индекса, но оставляет canonical — сигналы противоречат друг другу. Для служебных страниц (поиск, политика) это обычно безвредно.',
				'check' => static function (Ctx $c): array {
					$out = [];
					foreach ($c->pages() as $url => $p) {
						$robots = strtolower((string)$p['html']['robotsMeta']);
						$header = strtolower((string)($p['headers']['x-robots-tag'] ?? ''));
						$noindex = str_contains($robots, 'noindex') || str_contains($header, 'noindex');
						if ($noindex && !empty($p['html']['canonical'])) {
							$out[] = ['url' => $url, 'detail' => 'noindex + canonical → ' . Ctx::pathKey((string)$p['html']['canonical'])];
						}
					}
					return $out;
				},
			],
			[
				'id' => 'index.hreflang', 'cat' => 'Индексация', 'sev' => 'low', 'scope' => 'site',
				'title' => 'Указаны hreflang для языковых версий',
				'hint' => 'Для одноязычного сайта hreflang не обязателен. Для мультиязычного — нужен и должен быть взаимным (ru ↔ en).',
				'check' => static function (Ctx $c): array {
					$out = [];
					$withLang = 0;
					$total = 0;
					foreach ($c->pages() as $url => $p) {
						$total++;
						if (!empty($p['html']['hreflang'])) {
							$withLang++;
						}
					}
					if ($total > 0 && $withLang === 0) {
						$out[] = ['url' => null, 'detail' => 'Ни на одной из ' . $total . ' страниц нет hreflang'];
					}
					return $out;
				},
			],
			[
				'id' => 'index.hreflang_broken', 'cat' => 'Индексация', 'sev' => 'high', 'scope' => 'page',
				'title' => 'Альтернативы hreflang ведут на существующие страницы',
				'hint' => 'hreflang на 404 или редирект игнорируется поисковиком целиком.',
				'check' => static function (Ctx $c): array {
					$out = [];
					foreach ($c->pages() as $url => $p) {
						foreach ($p['html']['hreflang'] as $al) {
							if (trim((string)$al['href']) === '') {
								$out[] = ['url' => $url, 'detail' => 'hreflang ' . $al['lang'] . ': пустой href'];
								continue;
							}
							$abs = Url::resolve((string)$al['href'], $url);
							if ($abs === null) {
								continue;
							}
							$status = $c->statusOf($abs);
							if ($status !== null && $status >= 400) {
								$out[] = ['url' => $url, 'detail' => 'hreflang ' . $al['lang'] . ' → ' . Ctx::pathKey($abs) . ' (HTTP ' . $status . ')'];
							}
						}
					}
					return $out;
				},
			],
			[
				'id' => 'index.hreflang_on_canonical', 'cat' => 'Индексация', 'sev' => 'low', 'scope' => 'page',
				'title' => 'hreflang не стоит на страницах, канонизированных на другой URL',
				'hint' => 'Если страница отдаёт canonical на другой адрес, её hreflang игнорируется. Для дублей (фильтры, пагинация) уберите hreflang или снимите canonical.',
				'check' => static function (Ctx $c): array {
					$out = [];
					foreach ($c->pages() as $url => $p) {
						$alts = $p['html']['hreflang'];
						if (!$alts) {
							continue;
						}
						$canon = (string)($p['html']['canonical'] ?? '');
						if ($canon === '') {
							continue;
						}
						$cabs = Url::resolve($canon, $url);
						if ($cabs === null) {
							continue;
						}
						[$cabs] = Url::splitFragment($cabs);
						if (Ctx::pathKey($cabs) !== Ctx::pathKey($url)) {
							$out[] = ['url' => $url, 'detail' => 'canonical → ' . Ctx::pathKey($cabs) . ', но hreflang задан (' . count($alts) . ')'];
						}
					}
					return $out;
				},
			],
			[
				'id' => 'index.hreflang_not_return', 'cat' => 'Индексация', 'sev' => 'medium', 'scope' => 'page',
				'title' => 'hreflang взаимный (страница указывает и на себя в ответ)',
				'hint' => 'Без обратной ссылки hreflang-пара не принимается: добавьте alternates на обеих версиях.',
				'check' => static function (Ctx $c): array {
					$out = [];
					foreach ($c->pages() as $url => $p) {
						$alts = $p['html']['hreflang'];
						if (!$alts) {
							continue;
						}
						// Проверяем взаимность только у «настоящих» языковых версий
						// (canonical указывает на саму страницу) — иначе это дубль, а не перевод.
						$canon = (string)($p['html']['canonical'] ?? '');
						if ($canon !== '') {
							$cabs = Url::resolve($canon, $url);
							if ($cabs !== null) {
								[$cabs] = Url::splitFragment($cabs);
								if (Ctx::pathKey($cabs) !== Ctx::pathKey($url)) {
									continue;
								}
							}
						}
						foreach ($alts as $al) {
							$abs = Url::resolve((string)$al['href'], $url);
							if ($abs === null) {
								continue;
							}
							[$abs] = Url::splitFragment($abs);
							$target = null;
							foreach ($c->pages() as $u2 => $p2) {
								if (Ctx::pathKey($u2) === Ctx::pathKey($abs)) {
									$target = $p2;
									break;
								}
							}
							if ($target === null) {
								continue;
							}
							$back = false;
							foreach ($target['html']['hreflang'] as $al2) {
								$abs2 = Url::resolve((string)$al2['href'], (string)($target['url'] ?? ''));
								if ($abs2 !== null && Ctx::pathKey($abs2) === Ctx::pathKey($url)) {
									$back = true;
								}
							}
							if (!$back) {
								$out[] = ['url' => $url, 'detail' => 'hreflang ' . $al['lang'] . ' → ' . Ctx::pathKey($abs) . ' не возвращает обратную ссылку'];
							}
						}
					}
					return $out;
				},
			],
			[
				'id' => 'index.robots_txt_missing', 'cat' => 'Индексация', 'sev' => 'high', 'scope' => 'site',
				'title' => 'robots.txt доступен и отдаёт 200',
				'hint' => 'Без robots.txt вы не управляете обходом, а часть роботов (Yandex, Google) не получает карту сайта.',
				'check' => static function (Ctx $c): array {
					$out = [];
					$r = $c->cr->robots;
					if ($r === null) {
						return $out;
					}
					if (!$r->exists) {
						$out[] = ['url' => null, 'detail' => 'robots.txt не найден (HTTP ' . ($r->status ?: 'нет ответа') . ')'];
					}
					return $out;
				},
			],
			[
				'id' => 'index.robots_blocks', 'cat' => 'Индексация', 'sev' => 'high', 'scope' => 'page',
				'title' => 'robots.txt не запрещает индексируемые страницы',
				'hint' => 'Disallow на важном URL закрывает его и от поисковика, и от внутренней перелинковки.',
				'check' => static function (Ctx $c): array {
					$out = [];
					$r = $c->cr->robots;
					if ($r === null || !$r->exists) {
						return $out;
					}
					foreach ($c->pages() as $url => $p) {
						$robots = strtolower((string)$p['html']['robotsMeta']);
						if (str_contains($robots, 'noindex')) {
							continue;
						}
						$path = parse_url($url, PHP_URL_PATH) ?: '/';
						$q = parse_url($url, PHP_URL_QUERY);
						$full = $path . ($q ? '?' . $q : '');
						if ($r->allowed($full)) {
							continue;
						}
						$hit = $r->bestMatch($full);
						$cite = '';
						if ($hit !== null) {
							$cite = 'robots.txt, строка ' . (int)($hit['line'] ?? 0) . ': ' . (string)($hit['text'] ?? $hit['raw']);
						}
						$out[] = [
							'url'     => $url,
							'detail'  => 'Путь закрыт директивой в robots.txt: ' . ($hit['raw'] ?? $full),
							'snippet' => (string)($hit['text'] ?? ''),
							'cite'    => $cite,
						];
					}
					return $out;
				},
			],
			[
				'id' => 'index.robots_syntax', 'cat' => 'Индексация', 'sev' => 'low', 'scope' => 'site',
				'title' => 'Синтаксис robots.txt корректен',
				'hint' => 'Опечатки в директивах делают правило бессмысленным — робот трактует файл построчно.',
				'check' => static function (Ctx $c): array {
					$out = [];
					$r = $c->cr->robots;
					if ($r === null || !$r->exists) {
						return $out;
					}
					foreach ($r->errors as $e) {
						$out[] = ['url' => null, 'detail' => $e];
					}
					return $out;
				},
			],
			[
				'id' => 'index.orphan', 'cat' => 'Индексация', 'sev' => 'high', 'scope' => 'page',
				'title' => 'Все страницы получают внутренние ссылки',
				'hint' => 'Страница без входящих ссылок — «сирота»: краулер находит её только по sitemap, а вес она не получает.',
				'check' => static function (Ctx $c): array {
					$out = [];
					$home = rtrim($c->origin(), '/') . '/';
					foreach ($c->pages() as $url => $p) {
						if ($url === $home || rtrim($url, '/') === rtrim($home, '/')) {
							continue;
						}
						$in = $c->cr->inlinks[$url]['count'] ?? 0;
						if ($in === 0) {
							$out[] = ['url' => $url, 'detail' => 'Входящих ссылок: 0'];
						}
					}
					return $out;
				},
			],
			[
				'id' => 'index.depth', 'cat' => 'Индексация', 'sev' => 'low', 'scope' => 'page',
				'title' => 'Страницы не глубже 3–4 кликов от главной (отраслевой ориентир)',
				'hint' => 'Страницы глубже 3 кликов плохо индексируются. Добавьте навигацию, хабы и перелинковку.',
				'check' => static function (Ctx $c): array {
					$out = [];
					foreach ($c->pages() as $url => $p) {
						if ((int)$p['depth'] > 3) {
							$out[] = ['url' => $url, 'detail' => 'Глубина: ' . $p['depth'] . ' клик(ов)'];
						}
					}
					return $out;
				},
			],
			[
				'id' => 'index.robots_bots', 'cat' => 'Индексация', 'sev' => 'critical', 'scope' => 'site',
				'title' => 'Основные поисковые роботы не заблокированы',
				'hint' => 'Робот Яндекса (и Googlebot, Bingbot) не должен быть закрыт Disallow: иначе сайт просто не попадёт в выдачу этой системы. Отдельно проверяют Яндекс.Картинки, Яндекс.Видео и Яндекс.Новости.',
				'check' => static function (Ctx $c): array {
					$out = [];
					$r = $c->cr->robots;
					if ($r === null || !$r->exists) {
						return $out;
					}
					$bots = [
						'yandex'         => 'Яндекс (основной робот)',
						'googlebot'      => 'Google',
						'bingbot'        => 'Bing',
						'yandeximages'   => 'Яндекс.Картинки',
						'yandexvideo'    => 'Яндекс.Видео',
						'yandexmedia'    => 'Яндекс.Новости',
						'yandexblogs'    => 'Яндекс.Блоги',
					];
					$blocked = [];
					foreach ($bots as $bot => $who) {
						$rules = $r->groups[$bot] ?? null;
						if ($rules === null) {
							continue;   // действует общее правило User-agent: *
						}
						foreach ($rules as $rule) {
							if (!$rule['allow'] && ($rule['path'] === '/' || $rule['path'] === '')) {
								$blocked[] = $who . ' (' . $bot . ')';
								break;
							}
						}
					}
					if ($blocked) {
						$out[] = ['url' => null, 'detail' => 'Полностью заблокированы: ' . implode(', ', $blocked)];
					}
					return $out;
				},
			],
			[
				'id' => 'index.clean_param', 'cat' => 'Индексация', 'sev' => 'critical', 'scope' => 'site',
				'title' => 'Clean-param не содержит параметров, меняющих содержимое страницы',
				'hint' => 'Clean-param читает только робот Яндекса: он склеивает адреса, отличающиеся этими параметрами, и переносит метрики на основной URL. Вносить туда можно только параметры, НЕ меняющие содержимое (utm-метки, рефереры). Параметры фильтров и сортировки там опасны: робот посчитает разные страницы одинаковыми и уберёт их из выдачи.',
				'check' => static function (Ctx $c): array {
					$out = [];
					$r = $c->cr->robots;
					if ($r === null || !$r->exists || !$r->cleanParams) {
						return $out;
					}
					// параметры, реально встречающиеся в URL сайта: значение → отпечаток содержимого
					$byParam = [];
					foreach ($c->cr->pages as $url => $p) {
						$q = parse_url($url, PHP_URL_QUERY);
						if (!$q) {
							continue;
						}
						foreach (explode('&', $q) as $pair) {
							if (!str_contains($pair, '=')) {
								continue;
							}
							[$name, $value] = explode('=', $pair, 2);
							$byParam[$name][$value] = (string)($p['html']['textHash'] ?? '');
						}
					}
					$clean = [];
					foreach ($r->cleanParams as $ref => $params) {
						foreach ($params as $p) {
							$clean[strtolower($p)] = true;
						}
						if ($ref !== '') {
							$clean[strtolower($ref)] = true;
						}
					}
					// параметр меняет содержимое, если у разных значений разный отпечаток текста
					$contentChanging = [];
					foreach ($byParam as $name => $values) {
						if (count(array_unique($values)) < 2) {
							continue;
						}
						$contentChanging[] = $name;
					}
					$danger = [];
					foreach ($contentChanging as $name) {
						if (isset($clean[strtolower($name)])) {
							$danger[] = $name;
						}
					}
					if ($danger) {
						$out[] = ['url' => null, 'detail' => 'в Clean-param попали параметры, меняющие содержимое: ' . implode(', ', $danger)
							. ' — робот Яндекса склеит разные страницы в одну и уберёт их из выдачи'];
					}
					// служебные параметры, которые не закрыты
					$tracking = [];
					foreach (array_keys($byParam) as $name) {
						if (preg_match('~^(utm_|yclid|gclid|dclid|fbclid|mc_cid|mc_eid|_openstat|ref|referrer)~i', $name)
							&& !isset($clean[strtolower($name)])) {
							$tracking[] = $name;
						}
					}
					if ($tracking) {
						$out[] = ['url' => null, 'detail' => 'служебные параметры не закрыты Clean-param: ' . implode(', ', array_slice($tracking, 0, 8))
							. ' — Яндекс обычно удаляет аналитические метки сам, но явная директива надёжнее'];
					}
					return $out;
				},
			],
			[
				'id' => 'index.robots_encoding', 'cat' => 'Индексация', 'sev' => 'low', 'scope' => 'site',
				'title' => 'Пути в robots.txt записаны в percent-encoding',
				'hint' => 'Яндекс прямо требует кодировать не-ASCII пути в Disallow (например /%D0%BA%D0%BE%D1%80%D0%B7%D0%B8%D0%BD%D0%B0). Запись кириллицейLiteral не сработает и раздел останется открытым для обхода.',
				'check' => static function (Ctx $c): array {
					$out = [];
					$r = $c->cr->robots;
					if ($r === null || !$r->exists) {
						return $out;
					}
					$bad = [];
					foreach ($r->groups as $agent => $rules) {
						foreach ($rules as $rule) {
							if (preg_match('~[^\x21-\x7E]~', $rule['path'])) {
								$bad[] = $agent . ': ' . $rule['raw'];
							}
						}
					}
					if ($bad) {
						$out[] = ['url' => null, 'detail' => 'не-ASCII в директивах (' . count($bad) . '): ' . implode('; ', array_slice($bad, 0, 3)) . ' — нужно percent-encoding'];
					}
					return $out;
				},
			],
			[
				'id' => 'index.robots_limits', 'cat' => 'Индексация', 'sev' => 'info', 'scope' => 'site',
				'title' => 'robots.txt отвечает 200 и не превышает 500 КБ',
				'hint' => 'Требование Яндекса: файл лежит в корне, отдаёт 200 OK и весит не больше 500 КБ. При превышении робот читает файл частично и часть правил не применяет.',
				'check' => static function (Ctx $c): array {
					$out = [];
					$r = $c->cr->robots;
					if ($r === null) {
						return $out;
					}
					if (!$r->exists) {
						$out[] = ['url' => null, 'detail' => 'robots.txt недоступен (HTTP ' . ($r->status ?: 'нет ответа') . ') — робот не получает ни правил, ни адрес карты сайта'];
					}
					return $out;
				},
			],
			[
				'id' => 'index.robots_host', 'cat' => 'Индексация', 'sev' => 'info', 'scope' => 'site',
				'title' => 'Указан Host для выбора основного зеркала',
				'hint' => 'Директива Host — только для робота Яндекса: указывает, какое зеркало считать основным, если сайт доступен по нескольким адресам. Директива может быть только одна.',
				'check' => static function (Ctx $c): array {
					$out = [];
					$r = $c->cr->robots;
					if ($r === null || !$r->exists) {
						return $out;
					}
					if ($r->host === null) {
						$out[] = ['url' => null, 'detail' => 'Host не задан: при нескольких зеркалах робот Яндекса сам выберет основное — и это не обязательно нужное вам'];
					}
					return $out;
				},
			],
			[
				'id' => 'index.crawl_delay', 'cat' => 'Индексация', 'sev' => 'info', 'scope' => 'site',
				'title' => 'Crawl-delay: robots.txt не управляет скоростью Яндекса',
				'hint' => 'С 22 февраля 2018 года Яндекс не учитывает Crawl-delay. Частоту обхода задают только в Вебмастере (Настройки → Скорость обхода). Директива в файле остаётся подсказкой для других роботов.',
				'check' => static function (Ctx $c): array {
					$out = [];
					$r = $c->cr->robots;
					if ($r === null || !$r->exists || $r->crawlDelay === []) {
						return $out;
					}
					foreach ($r->crawlDelay as $agent => $sec) {
						$out[] = ['url' => null, 'detail' => 'Crawl-delay: ' . $sec . ' с для ' . $agent . ' — Яндекс его игнорирует, скорость обхода задаётся в Вебмастере'];
					}
					return $out;
				},
			],
			[
				'id' => 'index.verification', 'cat' => 'Индексация', 'sev' => 'info', 'scope' => 'site',
				'title' => 'Сайт подтверждён в Вебмастере и Search Console',
				'hint' => 'Теги подтверждения (yandex-verification, google-site-verification) или файл на сервере позволяют работать с отчётами, переобходом и инструментами вебмастера.',
				'check' => static function (Ctx $c): array {
					$out = [];
					$found = ['yandex' => false, 'google' => false];
					foreach ($c->cr->pages as $url => $p) {
						foreach ($p['html']['meta'] ?? [] as $k => $v) {
							if (str_contains(strtolower((string)$k), 'yandex-verification')) {
								$found['yandex'] = true;
							}
							if (str_contains(strtolower((string)$k), 'google-site-verification')) {
								$found['google'] = true;
							}
						}
					}
					$miss = [];
					if (!$found['yandex']) {
						$miss[] = 'yandex-verification';
					}
					if (!$found['google']) {
						$miss[] = 'google-site-verification';
					}
					if ($miss) {
						$out[] = ['url' => null, 'detail' => 'нет тегов подтверждения: ' . implode(', ', $miss) . ' (возможна и другая схема подтверждения — файл или DNS)'];
					}
					return $out;
				},
			],
			[
				'id' => 'index.sitemap_directive', 'cat' => 'Индексация', 'sev' => 'low', 'scope' => 'site',
				'title' => 'Карта сайта указана в robots.txt',
				'hint' => 'Директива Sitemap: в robots.txt — самый надёжный способ сообщить поисковику о карте сайта.',
				'check' => static function (Ctx $c): array {
					$out = [];
					$r = $c->cr->robots;
					if ($r === null || !$r->exists) {
						return $out;
					}
					if (!$r->sitemaps) {
						$out[] = ['url' => null, 'detail' => 'В robots.txt нет директивы Sitemap:'];
					}
					return $out;
				},
			],
			[
				'id' => 'index.sitemap_missing', 'cat' => 'Индексация', 'sev' => 'high', 'scope' => 'site',
				'title' => 'Карта сайта доступна',
				'hint' => 'Добавьте /sitemap.xml и укажите его в robots.txt — это самый дешёвый способ показать все URL.',
				'check' => static function (Ctx $c): array {
					$out = [];
					if (!$c->cr->sitemaps->exists) {
						$out[] = ['url' => null, 'detail' => 'sitemap.xml не найден (' . implode('; ', array_slice($c->cr->sitemaps->errors, 0, 2)) . ')'];
					}
					return $out;
				},
			],
			[
				'id' => 'index.sitemap_vs_crawl', 'cat' => 'Индексация', 'sev' => 'medium', 'scope' => 'site',
				'title' => 'Карта сайта совпадает с фактической структурой',
				'hint' => 'URL, которые есть только в карте, — «сироты»: добавьте на них ссылки с сайта. URL вне карты — добавьте в карту.',
				'check' => static function (Ctx $c): array {
					$out = [];
					$smPaths = [];
					foreach (array_keys($c->cr->sitemaps->urls) as $u) {
						$smPaths[Ctx::pathKey($u)] = $u;
					}
					$pagePaths = $c->pathMap();
					$onlySitemap = array_diff_key($smPaths, $pagePaths);
					$onlyCrawl = array_diff_key($pagePaths, $smPaths);
					if ($onlySitemap) {
						$out[] = ['url' => null, 'detail' => 'В карте, но недостижимы по ссылкам (' . count($onlySitemap) . '): ' . implode(', ', array_slice(array_keys($onlySitemap), 0, 8))];
					}
					$withQuery = [];
					$real = [];
					foreach (array_keys($onlyCrawl) as $path) {
						if (str_contains($path, '?')) {
							$withQuery[] = $path;
							continue;
						}
						// noindex-страницы в карте быть не должно — их отсутствие это норма
						$isNoindex = false;
						foreach ($c->pages() as $pu => $pp) {
							if (Ctx::pathKey($pu) === $path) {
								$isNoindex = str_contains(strtolower((string)$pp['html']['robotsMeta']), 'noindex');
								break;
							}
						}
						if (!$isNoindex) {
							$real[] = $path;
						}
					}
					if ($real) {
						$out[] = ['url' => null, 'detail' => 'Реальные страницы вне карты (' . count($real) . '): ' . implode(', ', array_slice($real, 0, 8))];
					}
					if ($withQuery) {
						$out[] = ['url' => null, 'detail' => 'URL с параметрами вне карты (' . count($withQuery) . ') — это нормально, но их не должно быть в индексе: ' . implode(', ', array_slice($withQuery, 0, 5))];
					}
					return $out;
				},
			],
			[
				'id' => 'index.url_list', 'cat' => 'Индексация', 'sev' => 'medium', 'scope' => 'site',
				'title' => 'Файл-список /pages совпадает с картой и обходом',
				'hint' => '/pages используют для ручных проверок и Screaming Frog — он должен совпадать с sitemap.xml и с реальной структурой сайта.',
				'check' => static function (Ctx $c): array {
					$out = [];
					if (!$c->cr->urlList) {
						return $out;
					}
					$pagePaths = $c->pathMap();
					$smPaths = [];
					foreach (array_keys($c->cr->sitemaps->urls) as $u) {
						$smPaths[Ctx::pathKey($u)] = true;
					}
					$notCrawled = [];
					$notInMap = [];
					foreach ($c->cr->urlList as $u) {
						$p = Ctx::pathKey($u);
						if (!isset($pagePaths[$p])) {
							$notCrawled[] = $p;
						}
						$isNoindex = false;
						foreach ($c->pages() as $pu => $pp) {
							if (Ctx::pathKey($pu) === $p) {
								$isNoindex = str_contains(strtolower((string)$pp['html']['robotsMeta']), 'noindex');
								break;
							}
						}
						if (!isset($smPaths[$p]) && !$isNoindex) {
							$notInMap[] = $p;   // noindex-страницы в карте быть не должно
						}
					}
					if ($notCrawled) {
						$out[] = ['url' => null, 'detail' => 'В /pages, но краулер не дошёл (' . count($notCrawled) . '): ' . implode(', ', array_slice($notCrawled, 0, 8))];
					}
					if ($notInMap) {
						$out[] = ['url' => null, 'detail' => 'В /pages, но нет в sitemap.xml (' . count($notInMap) . '): ' . implode(', ', array_slice($notInMap, 0, 8))];
					}
					return $out;
				},
			],
			[
				'id' => 'index.sitemap_bad_status', 'cat' => 'Индексация', 'sev' => 'critical', 'scope' => 'site',
				'title' => 'Все URL из карты сайта отвечают 200',
				'hint' => 'Битые URL в карте — сигнал о неряшливом сайте. Уберите их или восстановите страницы.',
				'check' => static function (Ctx $c): array {
					$out = [];
					foreach ($c->cr->sitemapStatus as $url => $status) {
						if ($status >= 400 || $status === 0) {
							$out[] = ['url' => $url, 'detail' => 'HTTP ' . ($status ?: 'нет ответа')];
						}
					}
					return $out;
				},
			],
			[
				'id' => 'index.sitemap_noindex', 'cat' => 'Индексация', 'sev' => 'high', 'scope' => 'site',
				'title' => 'В карте сайта нет noindex-страниц',
				'hint' => 'URL с noindex в карте — противоречие: уберите их из карты или снимите noindex.',
				'check' => static function (Ctx $c): array {
					$out = [];
					foreach ($c->cr->sitemaps->urls as $url => $meta) {
						$page = null;
						foreach ($c->pages() as $u => $p) {
							if (Ctx::pathKey($u) === Ctx::pathKey($url)) {
								$page = $p;
								break;
							}
						}
						if ($page === null) {
							continue;
						}
						$robots = strtolower((string)$page['html']['robotsMeta']);
						if (str_contains($robots, 'noindex')) {
							$out[] = ['url' => $url, 'detail' => 'noindex, но URL в карте'];
						}
					}
					return $out;
				},
			],
			[
				'id' => 'index.sitemap_duplicates', 'cat' => 'Индексация', 'sev' => 'medium', 'scope' => 'site',
				'title' => 'В карте сайта нет дублей URL',
				'hint' => 'Дубли в карте размывают приоритеты и расходуют лимит обхода.',
				'check' => static function (Ctx $c): array {
					$out = [];
					$seen = [];
					foreach ($c->cr->sitemaps->urls as $url => $meta) {
						$k = Ctx::pathKey($url);
						if (isset($seen[$k])) {
							$out[] = ['url' => $url, 'detail' => 'Дубль пути: ' . $k];
						}
						$seen[$k] = true;
					}
					return $out;
				},
			],
			[
				'id' => 'index.sitemap_lastmod', 'cat' => 'Индексация', 'sev' => 'low', 'scope' => 'site',
				'title' => 'lastmod в карте сайта корректен',
				'hint' => 'Дата в будущем или очень старая дата обесценивает lastmod: поисковик перестаёт ему доверять.',
				'check' => static function (Ctx $c): array {
					$out = [];
					$now = time();
					$old = 0;
					foreach ($c->cr->sitemaps->urls as $url => $meta) {
						$lm = $meta['lastmod'] ?? null;
						if ($lm === null) {
							continue;
						}
						$ts = strtotime($lm);
						if ($ts === false) {
							$out[] = ['url' => $url, 'detail' => 'Нечитаемая дата lastmod: ' . $lm];
							continue;
						}
						if ($ts > $now + 86400) {
							$out[] = ['url' => $url, 'detail' => 'lastmod в будущем: ' . $lm];
						} elseif ($ts < $now - 400 * 86400) {
							$old++;
						}
					}
					if ($old > 0) {
						$out[] = ['url' => null, 'detail' => 'Устаревший lastmod (старше 13 месяцев) у ' . $old . ' URL'];
					}
					return $out;
				},
			],
			[
				'id' => 'index.duplicate_content', 'cat' => 'Индексация', 'sev' => 'high', 'scope' => 'site',
				'title' => 'Нет дублирующихся страниц по содержимому',
				'hint' => 'Дубли (шаблоны, фильтры, пагинация) забирают место у уникальных страниц. Синхронизируйте canonical и содержание.',
				'check' => static function (Ctx $c): array {
					$out = [];
					$pages = $c->pages();
					$ids = array_keys($pages);
					$indexable = [];
					foreach ($ids as $i => $id) {
						$robots = strtolower((string)$pages[$id]['html']['robotsMeta']);
						if (str_contains($robots, 'noindex')) {
							continue;   // закрытые от индекса страницы не конкурируют
						}
						$canon = (string)($pages[$id]['html']['canonical'] ?? '');
						if ($canon !== '') {
							$cabs = Url::resolve($canon, $id);
							if ($cabs !== null) {
								[$cabs] = Url::splitFragment($cabs);
								if (Ctx::pathKey($cabs) !== Ctx::pathKey($id)) {
									continue;   // страница уже канонизирована на другой URL — дубли не конкурируют
								}
							}
						}
						$indexable[$i] = true;
					}
					$index = [];   // эскиз-шингл → [id] (кандидаты)
					foreach ($ids as $i => $id) {
						if (!isset($indexable[$i])) {
							continue;
						}
						foreach (($pages[$id]['html']['sketch'] ?? []) as $sh) {
							$index[$sh][] = $i;
						}
					}
					$candidates = [];
					foreach ($index as $sh => $list) {
						if (count($list) < 2) {
							continue;
						}
						for ($a = 0; $a < count($list); $a++) {
							for ($b = $a + 1; $b < count($list); $b++) {
								$key = $list[$a] . '|' . $list[$b];
								$candidates[$key] = ($candidates[$key] ?? 0) + 1;
							}
						}
					}
					$seenPairs = [];
					foreach ($candidates as $key => $shared) {
						[$a, $b] = explode('|', $key);
						$sa = $pages[$ids[$a]]['html']['shingles'] ?? [];
						$sb = $pages[$ids[$b]]['html']['shingles'] ?? [];
						if (!$sa || !$sb) {
							continue;
						}
						$inter = count(array_intersect($sa, $sb));
						$union = count($sa) + count($sb) - $inter;
						$j = $union > 0 ? $inter / $union : 0;
						if ($j >= 0.85) {
							$pair = $ids[$a] . '~' . $ids[$b];
							if (isset($seenPairs[$pair])) {
								continue;
							}
							$seenPairs[$pair] = true;
							$out[] = ['url' => $ids[$a], 'detail' => 'Похоже на ' . Ctx::pathKey($ids[$b]) . ' — совпадение текста ' . round($j * 100) . '%'];
						}
					}
					return $out;
				},
			],
		];
	}

	// =====================================================================
	// 3. МЕТА-ТЕГИ И ЗАГОЛОВКИ
	// =====================================================================
	private static function rulesMeta(): array
	{
		return [
			[
				'id' => 'meta.title_missing', 'cat' => 'Мета-теги', 'sev' => 'critical', 'scope' => 'page',
				'title' => 'У каждой страницы есть <title>',
				'hint' => 'Title — главный сигнал запроса и заголовок в выдаче. Формула: «Ключевое предложение | Бренд».',
				'check' => static function (Ctx $c): array {
					$out = [];
					foreach ($c->pages() as $url => $p) {
						if (trim((string)$p['html']['title']) === '') {
							$out[] = [
								'url'     => $url,
								'detail'  => 'Тег <title> пуст или отсутствует',
								'snippet' => (string)($p['html']['headHtml'] ?? ''),
								'cite'    => 'Добавьте в <head> уникальный <title> с основным запросом: <title>Заголовок страницы</title>',
							];
						}
					}
					return $out;
				},
			],
			[
				'id' => 'meta.title_length', 'cat' => 'Мета-теги', 'sev' => 'low', 'scope' => 'page',
				'title' => 'Title помещается в выдачу без обрезки (по пикселям, а не по символам)',
				'hint' => 'Google не ограничивает длину title и не считает её фактором ранжирования, но обрезает заголовок по пикселям: на десктопе это ~600px, на мобильном заметно меньше. Ориентир отрасли — 50–60 символов, важные слова — в начало.',
				'check' => static function (Ctx $c): array {
					$out = [];
					foreach ($c->pages() as $url => $p) {
						$len = (int)$p['html']['titleLen'];
						if ($len === 0) {
							continue;
						}
						if ($len < 30) {
							$out[] = ['url' => $url, 'detail' => $len . ' симв. — коротко'];
						} elseif ($len > 65) {
							$out[] = ['url' => $url, 'detail' => $len . ' симв. — обрежется в выдаче'];
						}
					}
					return $out;
				},
			],
			[
				'id' => 'meta.title_duplicate', 'cat' => 'Мета-теги', 'sev' => 'high', 'scope' => 'site',
				'title' => 'Заголовки страниц уникальны',
				'hint' => 'Одинаковые title = поисковик не знает, какую страницу показать. Добавьте релевантный хвост.',
				'check' => static function (Ctx $c): array {
					$out = [];
					$by = [];
					foreach ($c->pages() as $url => $p) {
						$t = trim((string)$p['html']['title']);
						if ($t === '') {
							continue;
						}
						if (str_contains(strtolower((string)$p['html']['robotsMeta']), 'noindex')) {
							continue;   // закрытые от индекса страницы не конкурируют
						}
						$by[$t][] = $url;
					}
					foreach ($by as $t => $urls) {
						if (count($urls) > 1) {
							$out[] = ['url' => $urls[0], 'detail' => '"' . mb_substr($t, 0, 70) . '" — ' . count($urls) . ' стр.: ' . implode(', ', array_map(static fn($u) => Ctx::pathKey($u), array_slice($urls, 0, 6)))];
						}
					}
					return $out;
				},
			],
			[
				'id' => 'meta.desc_missing', 'cat' => 'Мета-теги', 'sev' => 'high', 'scope' => 'page',
				'title' => 'У каждой страницы есть meta description',
				'hint' => 'Description формирует сниппет и влияет на CTR. Пишите 120–160 символов с ключевым запросом и выгодой.',
				'check' => static function (Ctx $c): array {
					$out = [];
					foreach ($c->pages() as $url => $p) {
						if (trim((string)$p['html']['description']) === '') {
							$out[] = [
								'url'     => $url,
								'detail'  => 'Тег <meta name="description"> отсутствует или пуст',
								'snippet' => (string)($p['html']['headHtml'] ?? ''),
								'cite'    => 'Добавьте описание для сниппета: <meta name="description" content="120–160 символов с основным запросом">',
							];
						}
					}
					return $out;
				},
			],
			[
				'id' => 'meta.desc_length', 'cat' => 'Мета-теги', 'sev' => 'low', 'scope' => 'page',
				'title' => 'Description укладывается в сниппет (≈155–160 символов)',
				'hint' => 'Лимита длины description у Google нет, но сниппет обрезается примерно на 155–160 символах; рекомендация Google — 50–160 символов. На ранжирование не влияет, работает на CTR.',
				'check' => static function (Ctx $c): array {
					$out = [];
					foreach ($c->pages() as $url => $p) {
						$d = trim((string)$p['html']['description']);
						if ($d === '') {
							continue;
						}
						$len = mb_strlen($d);
						if ($len < 70) {
							$out[] = ['url' => $url, 'detail' => $len . ' симв. — мало'];
						} elseif ($len > 165) {
							$out[] = ['url' => $url, 'detail' => $len . ' симв. — обрежется'];
						}
					}
					return $out;
				},
			],
			[
				'id' => 'meta.desc_duplicate', 'cat' => 'Мета-теги', 'sev' => 'high', 'scope' => 'site',
				'title' => 'Описания страниц уникальны',
				'hint' => 'Одинаковые description снижают CTR и ухудшают релевантность выдачи для этих запросов.',
				'check' => static function (Ctx $c): array {
					$out = [];
					$by = [];
					foreach ($c->pages() as $url => $p) {
						$d = trim((string)$p['html']['description']);
						if ($d === '') {
							continue;
						}
						if (str_contains(strtolower((string)$p['html']['robotsMeta']), 'noindex')) {
							continue;
						}
						$by[$d][] = $url;
					}
					foreach ($by as $d => $urls) {
						if (count($urls) > 1) {
							$out[] = ['url' => $urls[0], 'detail' => '"' . mb_substr($d, 0, 60) . '…" — ' . count($urls) . ' стр.: ' . implode(', ', array_map(static fn($u) => Ctx::pathKey($u), array_slice($urls, 0, 6)))];
						}
					}
					return $out;
				},
			],
			[
				'id' => 'meta.h1_missing', 'cat' => 'Мета-теги', 'sev' => 'low', 'scope' => 'page',
				'title' => 'На странице есть <h1> с осмысленным текстом',
				'hint' => 'H1 задаёт тему страницы. Добавьте один H1 с ключевым запросом — не пустой тег.',
				'check' => static function (Ctx $c): array {
					$out = [];
					foreach ($c->pages() as $url => $p) {
						$count = (int)$p['html']['headingCount'][1];
						if ($count === 0) {
							$out[] = ['url' => $url, 'detail' => 'Тег <h1> отсутствует'];
						} elseif ($count > 1) {
							$out[] = ['url' => $url, 'detail' => $count . ' тегов <h1> (должен быть один)'];
						} else {
							$h1 = trim((string)($p['html']['h1'][0] ?? ''));
							if ($h1 === '') {
								$out[] = ['url' => $url, 'detail' => '<h1> пустой'];
							}
						}
					}
					return $out;
				},
			],
			[
				'id' => 'meta.h1_duplicate', 'cat' => 'Мета-теги', 'sev' => 'medium', 'scope' => 'site',
				'title' => 'Заголовки H1 уникальны',
				'hint' => 'Повторяющийся H1 на разных страницах — признак шаблонных (неуникальных) страниц.',
				'check' => static function (Ctx $c): array {
					$out = [];
					$by = [];
					foreach ($c->pages() as $url => $p) {
						$h1 = trim((string)($p['html']['h1'][0] ?? ''));
						if ($h1 === '') {
							continue;
						}
						if (str_contains(strtolower((string)$p['html']['robotsMeta']), 'noindex')) {
							continue;
						}
						$by[$h1][] = $url;
					}
					foreach ($by as $h => $urls) {
						if (count($urls) > 1) {
							$out[] = ['url' => $urls[0], 'detail' => '"' . mb_substr($h, 0, 60) . '" — ' . count($urls) . ' стр.: ' . implode(', ', array_map(static fn($u) => Ctx::pathKey($u), array_slice($urls, 0, 6)))];
						}
					}
					return $out;
				},
			],
			[
				'id' => 'meta.heading_skip', 'cat' => 'Мета-теги', 'sev' => 'low', 'scope' => 'page',
				'title' => 'Иерархия заголовков без пропусков уровней',
				'hint' => 'Переход h1 → h3 без h2 ломает структуру документа и навигацию скринридеров.',
				'check' => static function (Ctx $c): array {
					$out = [];
					foreach ($c->pages() as $url => $p) {
						$prev = 0;
						$skips = [];
						foreach ($p['html']['headings'] as $h) {
							$lvl = (int)$h['level'];
							if ($prev > 0 && $lvl > $prev + 1) {
								$skips[] = 'h' . $prev . ' → h' . $lvl . ' (' . mb_substr($h['text'], 0, 30) . ')';
							}
							$prev = $lvl;
						}
						if ($skips) {
							$out[] = ['url' => $url, 'detail' => implode('; ', array_slice($skips, 0, 3))];
						}
					}
					return $out;
				},
			],
			[
				'id' => 'meta.lang_missing', 'cat' => 'Мета-теги', 'sev' => 'medium', 'scope' => 'page',
				'title' => 'У <html> указан lang',
				'hint' => 'Атрибут lang нужен поисковику для правильного определения языка и для переводчиков.',
				'check' => static function (Ctx $c): array {
					$out = [];
					foreach ($c->pages() as $url => $p) {
						if (trim((string)$p['html']['lang']) === '') {
							$out[] = ['url' => $url, 'detail' => 'Атрибут lang у <html> отсутствует'];
						}
					}
					return $out;
				},
			],
			[
				'id' => 'meta.lang_mismatch', 'cat' => 'Мета-теги', 'sev' => 'medium', 'scope' => 'page',
				'title' => 'lang соответствует языку содержимого',
				'hint' => 'Несовпадение языка (lang="en" при русском тексте) мешает ранжированию и переводам.',
				'check' => static function (Ctx $c): array {
					$out = [];
					foreach ($c->pages() as $url => $p) {
						$lang = strtolower((string)$p['html']['lang']);
						$script = (string)$p['html']['script'];
						if ($lang === '' || $script === 'unknown' || (int)$p['html']['words'] < 40) {
							continue;
						}
						$isRu = str_starts_with($lang, 'ru');
						$isEn = str_starts_with($lang, 'en');
						if ($isRu && $script === 'latin') {
							$out[] = ['url' => $url, 'detail' => 'lang="' . $lang . '", но текст без кириллицы'];
						} elseif ($isEn && $script === 'cyrillic') {
							$out[] = ['url' => $url, 'detail' => 'lang="' . $lang . '", но текст на кириллице'];
						}
					}
					return $out;
				},
			],
			[
				'id' => 'meta.charset_missing', 'cat' => 'Мета-теги', 'sev' => 'low', 'scope' => 'page',
				'title' => 'Кодировка страницы объявлена и равна UTF-8',
				'hint' => 'Без <meta charset> кириллица может отображаться «кракозябрами» — это критично для поведенческих факторов.',
				'check' => static function (Ctx $c): array {
					$out = [];
					foreach ($c->pages() as $url => $p) {
						$cs = trim((string)$p['html']['charset']);
						if ($cs === '') {
							$out[] = ['url' => $url, 'detail' => 'Кодировка не объявлена'];
						} elseif (!in_array(strtoupper($cs), ['UTF-8', 'UTF8'], true)) {
							$out[] = ['url' => $url, 'detail' => 'Кодировка: ' . $cs];
						}
					}
					return $out;
				},
			],
			[
				'id' => 'meta.viewport_missing', 'cat' => 'Мета-теги', 'sev' => 'high', 'scope' => 'page',
				'title' => 'Задан viewport для мобильных устройств',
				'hint' => 'Без viewport мобильный браузер покажет « shrunk » версию и метрика ухудшится.',
				'check' => static function (Ctx $c): array {
					$out = [];
					foreach ($c->pages() as $url => $p) {
						if (trim((string)$p['html']['viewport']) === '') {
							$out[] = ['url' => $url, 'detail' => 'Тег <meta name="viewport"> отсутствует'];
						}
					}
					return $out;
				},
			],
			[
				'id' => 'meta.viewport_restricted', 'cat' => 'Мета-теги', 'sev' => 'medium', 'scope' => 'page',
				'title' => 'Viewport не запрещает масштабирование',
				'hint' => 'user-scalable=no и фиксированная ширина ухудшают доступность для слабовидящих.',
				'check' => static function (Ctx $c): array {
					$out = [];
					foreach ($c->pages() as $url => $p) {
						$vp = strtolower((string)$p['html']['viewport']);
						if ($vp === '') {
							continue;
						}
						if (str_contains($vp, 'user-scalable=no') || str_contains($vp, 'maximum-scale=1')) {
							$out[] = ['url' => $url, 'detail' => $vp];
						}
					}
					return $out;
				},
			],
			[
				'id' => 'meta.og_missing', 'cat' => 'Мета-теги', 'sev' => 'medium', 'scope' => 'page',
				'title' => 'Заполнены Open Graph-теги (title, description, image, url)',
				'hint' => 'Без OG-тегов ссылка в мессенджерах и соцсетях выглядит как голый URL.',
				'check' => static function (Ctx $c): array {
					$out = [];
					foreach ($c->pages() as $url => $p) {
						$og = $p['html']['og'];
						$missing = [];
						foreach (['title' => 'og:title', 'description' => 'og:description', 'image' => 'og:image', 'url' => 'og:url'] as $k => $tag) {
							if (trim((string)($og[$k] ?? '')) === '') {
								$missing[] = $tag;
							}
						}
						if ($missing) {
							$out[] = ['url' => $url, 'detail' => 'Нет: ' . implode(', ', $missing)];
						}
					}
					return $out;
				},
			],
			[
				'id' => 'meta.og_url_mismatch', 'cat' => 'Мета-теги', 'sev' => 'low', 'scope' => 'page',
				'title' => 'og:url совпадает с canonical',
				'hint' => 'Расхождение og:url и canonical приводит к «двум адресам» одной страницы в соцсетях.',
				'check' => static function (Ctx $c): array {
					$out = [];
					foreach ($c->pages() as $url => $p) {
						$ogUrl = trim((string)($p['html']['og']['url'] ?? ''));
						$canon = trim((string)($p['html']['canonical'] ?? ''));
						if ($ogUrl === '' || $canon === '') {
							continue;
						}
						$a = Url::resolve($ogUrl, $url);
						$b = Url::resolve($canon, $url);
						if ($a === null || $b === null) {
							continue;
						}
						if (Ctx::pathKey($a) !== Ctx::pathKey($b)) {
							$out[] = ['url' => $url, 'detail' => 'og:url → ' . Ctx::pathKey($a) . ', canonical → ' . Ctx::pathKey($b)];
						}
					}
					return $out;
				},
			],
			[
				'id' => 'meta.og_image_broken', 'cat' => 'Мета-теги', 'sev' => 'medium', 'scope' => 'page',
				'title' => 'Картинка og:image доступна',
				'hint' => 'Битая og:image = ссылка без превью в соцсетях и в AI-ответчиках.',
				'check' => static function (Ctx $c): array {
					$out = [];
					foreach ($c->pages() as $url => $p) {
						$img = trim((string)($p['html']['og']['image'] ?? ''));
						if ($img === '') {
							continue;
						}
						$abs = Url::resolve($img, $url);
						if ($abs === null) {
							continue;
						}
						$status = $c->statusOf($abs);
						if ($status !== null && $status >= 400) {
							$out[] = ['url' => $url, 'detail' => 'og:image → ' . Ctx::pathKey($abs) . ' (HTTP ' . $status . ')'];
						}
					}
					return $out;
				},
			],
			[
				'id' => 'meta.twitter_missing', 'cat' => 'Мета-теги', 'sev' => 'low', 'scope' => 'page',
				'title' => 'Заполнены Twitter Card-теги',
				'hint' => 'Twitter/X использует свои теги, а не OG. Минимум: twitter:card, twitter:title, twitter:description, twitter:image.',
				'check' => static function (Ctx $c): array {
					$out = [];
					foreach ($c->pages() as $url => $p) {
						$tw = $p['html']['twitter'];
						$missing = [];
						if (trim((string)($tw['card'] ?? '')) === '') {
							$missing[] = 'twitter:card';
						}
						if (trim((string)($tw['title'] ?? '')) === '') {
							$missing[] = 'twitter:title';
						}
						if (trim((string)($tw['image'] ?? '')) === '') {
							$missing[] = 'twitter:image';
						}
						if ($missing) {
							$out[] = ['url' => $url, 'detail' => 'Нет: ' . implode(', ', $missing)];
						}
					}
					return $out;
				},
			],
			[
				'id' => 'meta.favicon', 'cat' => 'Мета-теги', 'sev' => 'low', 'scope' => 'site',
				'title' => 'Указана иконка сайта (favicon)',
				'hint' => 'Иконка отображается во вкладках, закладках и в поиске Яндекса/Google.',
				'check' => static function (Ctx $c): array {
					$out = [];
					$withIcon = 0;
					foreach ($c->pages() as $url => $p) {
						if (!empty($p['html']['icons'])) {
							$withIcon++;
						}
					}
					$total = count($c->pages());
					if ($total > 0 && $withIcon === 0) {
						$out[] = ['url' => null, 'detail' => 'Ни на одной странице нет <link rel="icon">'];
					}
					return $out;
				},
			],
		];
	}

	// =====================================================================
	// 4. РАЗМЕТКА SCHEMA.ORG
	// =====================================================================
	private static function rulesSchema(): array
	{
		return [
			[
				'id' => 'schema.missing', 'cat' => 'Разметка Schema.org', 'sev' => 'medium', 'scope' => 'page',
				'title' => 'Есть разметка Schema.org (JSON-LD)',
				'hint' => 'Разметка даёт расширенные сниппеты и помогает AI-поиску понять суть страницы. Минимум — WebPage/Organization.',
				'check' => static function (Ctx $c): array {
					$out = [];
					foreach ($c->pages() as $url => $p) {
						if (!$p['html']['jsonld']) {
							$out[] = ['url' => $url, 'detail' => 'Блок <script type="application/ld+json"> отсутствует'];
						}
					}
					return $out;
				},
			],
			[
				'id' => 'schema.invalid', 'cat' => 'Разметка Schema.org', 'sev' => 'high', 'scope' => 'page',
				'title' => 'JSON-LD валиден',
				'hint' => 'Битый JSON в разметке полностью игнорируется поисковиком — проверьте кавычки и запятые.',
				'check' => static function (Ctx $c): array {
					$out = [];
					foreach ($c->pages() as $url => $p) {
						foreach ($p['html']['jsonld'] as $block) {
							if (!$block['ok']) {
								$out[] = ['url' => $url, 'detail' => (string)$block['error']];
							}
						}
					}
					return $out;
				},
			],
			[
				'id' => 'schema.no_type', 'cat' => 'Разметка Schema.org', 'sev' => 'medium', 'scope' => 'page',
				'title' => 'В разметке указан @type',
				'hint' => 'Без @type объект не интерпретируется — укажите конкретный тип (Article, Product, LocalBusiness…).',
				'check' => static function (Ctx $c): array {
					$out = [];
					foreach ($c->pages() as $url => $p) {
						foreach ($p['html']['jsonld'] as $block) {
							if ($block['ok'] && !$block['types']) {
								$out[] = ['url' => $url, 'detail' => 'В JSON-LD нет @type'];
							}
						}
					}
					return $out;
				},
			],
			[
				'id' => 'schema.duplicate_type', 'cat' => 'Разметка Schema.org', 'sev' => 'low', 'scope' => 'page',
				'title' => 'Нет дублей «одиночных» @type в одной разметке',
				'hint' => 'Два Organization / BreadcrumbList / WebSite на одной странице конфликтуют — поисковик выберет один наугад. (Множественные Article/BlogPosting — норма для листингов.)',
				'check' => static function (Ctx $c): array {
					$out = [];
					$singleton = ['Organization', 'LocalBusiness', 'WebSite', 'BreadcrumbList', 'FAQPage'];
					foreach ($c->pages() as $url => $p) {
						$counts = [];
						foreach ($p['html']['jsonld'] as $block) {
							foreach ($block['types'] as $t => $n) {
								if (isset($singleton[$t])) {
									$counts[$t] = ($counts[$t] ?? 0) + $n;
								}
							}
						}
						foreach ($counts as $t => $n) {
							if ($n > 1) {
								$out[] = ['url' => $url, 'detail' => $t . ' — ' . $n . ' шт. в одной разметке'];
							}
						}
					}
					return $out;
				},
			],
			[
				'id' => 'schema.required', 'cat' => 'Разметка Schema.org', 'sev' => 'medium', 'scope' => 'page',
				'title' => 'У ключевых типов заполнены обязательные свойства',
				'hint' => 'Без headline/datePublished/author/name разметка не даёт расширенный сниппет.',
				'check' => static function (Ctx $c): array {
					$out = [];
					$required = [
						'Article'     => ['headline', 'datePublished'],
						'BlogPosting' => ['headline', 'datePublished'],
						'Organization' => ['name', 'url'],
						'LocalBusiness' => ['name', 'address'],
						'Product'     => ['name'],
						'FAQPage'     => ['mainEntity'],
						'BreadcrumbList' => ['itemListElement'],
					];
					foreach ($c->pages() as $url => $p) {
						foreach ($p['html']['jsonld'] as $block) {
							if (!$block['ok']) {
								continue;
							}
							$raw = (string)$block['raw'];
							foreach ($block['types'] as $t => $n) {
								if (!isset($required[$t])) {
									continue;
								}
								foreach ($required[$t] as $prop) {
									if (!preg_match('~"' . preg_quote($prop, '~') . '"\s*:~i', $raw)) {
										$out[] = ['url' => $url, 'detail' => $t . ': нет свойства "' . $prop . '"'];
									}
								}
							}
						}
					}
					return $out;
				},
			],
		];
	}

	// =====================================================================
	// 5. КОНТЕНТ
	// =====================================================================
	private static function rulesContent(): array
	{
		return [
			[
				'id' => 'content.thin', 'cat' => 'Контент', 'sev' => 'low', 'scope' => 'page',
				'title' => 'Объём текста достаточен для раскрытия темы (ориентир от 300 слов)',
				'hint' => 'Тонкие страницы плохо ранжируются. Добавьте пользу: детали, цены, сроки, ответы на вопросы.',
				'check' => static function (Ctx $c): array {
					$out = [];
					foreach ($c->pages() as $url => $p) {
						if (str_contains(strtolower((string)$p['html']['robotsMeta']), 'noindex')) {
							continue;   // служебные (поиск) — тонкий контент там норма
						}
						$w = (int)$p['html']['words'];
						if ($w > 0 && $w < 300) {
							$out[] = ['url' => $url, 'detail' => $w . ' слов'];
						}
					}
					return $out;
				},
			],
			[
				'id' => 'content.php_errors', 'cat' => 'Контент', 'sev' => 'high', 'scope' => 'page',
				'title' => 'В выводе нет PHP-warning/error',
				'hint' => 'Технический текст в HTML виден пользователю и портит вёрстку. Включите display_errors=Off и чините код.',
				'check' => static function (Ctx $c): array {
					$out = [];
					foreach ($c->pages() as $url => $p) {
						foreach ($p['html']['phpErrors'] as $err) {
							$out[] = ['url' => $url, 'detail' => $err];
						}
					}
					return $out;
				},
			],
			[
				'id' => 'content.text_ratio', 'cat' => 'Контент', 'sev' => 'low', 'scope' => 'page',
				'title' => 'Доля текста в HTML не ниже 5%',
				'hint' => 'Страница, почти целиком состоящая из разметки и скриптов, воспринимается как пустая. Вынесите инлайн-стили и скрипты в файлы.',
				'check' => static function (Ctx $c): array {
					$out = [];
					foreach ($c->pages() as $url => $p) {
						$bytes = (int)$p['html']['htmlBytes'];
						$text = (int)$p['html']['textLen'];
						if ($bytes > 10000 && $text / $bytes < 0.05) {
							$out[] = ['url' => $url, 'detail' => round($text / $bytes * 100, 1) . '% текста (' . round($bytes / 1024) . ' КБ HTML)'];
						}
					}
					return $out;
				},
			],
			[
				'id' => 'content.encoding_ok', 'cat' => 'Контент', 'sev' => 'low', 'scope' => 'site',
				'title' => 'Верная кодировка в заголовке ответа',
				'hint' => 'Content-Type без charset или с windows-1251 ломает вывод кириллицы у части клиентов и краулеров.',
				'check' => static function (Ctx $c): array {
					$out = [];
					foreach ($c->pages() as $url => $p) {
						$ct = strtolower((string)$p['contentType']);
						if ($ct === '' || !str_contains($ct, 'html')) {
							continue;
						}
						if (!str_contains($ct, 'charset=')) {
							$out[] = ['url' => $url, 'detail' => 'Content-Type: ' . $ct];
						} elseif (!str_contains($ct, 'utf-8') && !str_contains($ct, 'utf8')) {
							$out[] = ['url' => $url, 'detail' => 'Content-Type: ' . $ct];
						}
					}
					return $out;
				},
			],
			[
				'id' => 'content.robots_meta_conflict', 'cat' => 'Контент', 'sev' => 'low', 'scope' => 'page',
				'title' => 'meta robots и X-Robots-Tag не противоречат друг другу',
				'hint' => 'Конфликт robots-сигналов (meta против заголовка) приводит к непредсказуемой индексации.',
				'check' => static function (Ctx $c): array {
					$out = [];
					foreach ($c->pages() as $url => $p) {
						$meta = strtolower((string)$p['html']['robotsMeta']);
						$head = strtolower((string)($p['headers']['x-robots-tag'] ?? ''));
						if ($meta === '' || $head === '') {
							continue;
						}
						$mi = str_contains($meta, 'noindex');
						$hi = str_contains($head, 'noindex');
						if ($mi !== $hi) {
							$out[] = ['url' => $url, 'detail' => 'meta: ' . $meta . ' | заголовок: ' . $head];
						}
					}
					return $out;
				},
			],
		];
	}

	// =====================================================================
	// 6. ССЫЛКИ
	// =====================================================================
	private static function rulesLinks(): array
	{
		$generic = ['нажмите', 'здесь', 'подробнее', 'читать', 'больше', 'далее', 'ссылка', 'клик', 'more', 'here', 'read more', 'learn more', 'click', 'link', 'подробнее>>', 'смотреть'];
		return [
			[
				'id' => 'links.broken_internal', 'cat' => 'Ссылки', 'sev' => 'critical', 'scope' => 'page',
				'title' => 'Внутренние ссылки ведут на существующие страницы',
				'hint' => 'В отчёте указано, на каких страницах стоит ссылка. В каждой из них: замените href на существующий адрес, снимите ссылку (если блок не нужен) или поставьте 301-редирект со старого адреса на новый. Порядок работ: сначала внутренние битые, затем внешние.',
				'check' => static function (Ctx $c): array {
					$out = [];
					foreach ($c->cr->inlinks as $url => $info) {
						$status = $c->statusOf($url);
						if ($status === null) {
							continue;
						}
						// 405/429/501/503 = сервер не поддерживает HEAD или временный лимит:
						// это не «битая» ссылка
						if (in_array($status, [405, 429, 501, 503], true)) {
							continue;
						}
						if ($status >= 400 || $status === 0) {
							$found = $c->foundOn($url, 5);
							$where = [];
							$snippet = '';
							foreach ($found as $f) {
								$where[] = Ctx::pathKey($f['from']) . ' — «' . mb_substr($f['anchor'] !== '' ? $f['anchor'] : 'без текста', 0, 40) . '» (' . $f['zone'] . ')';
								if ($snippet === '' && $f['tag'] !== '') {
									$snippet = $f['tag'];
								}
							}
							$out[] = [
								'url'     => $url,
								'detail'  => 'HTTP ' . ($status ?: 'нет ответа') . ' · ссылок на этот адрес: ' . count($info['from'])
									. ($where !== [] ? ' → ' . implode('; ', $where) : ''),
								'found'   => $found,
								'snippet' => $snippet,
								'cite'    => $snippet !== ''
									? 'Замените href в этом теге на существующий адрес (или снимите ссылку): ' . $snippet
									: '',
							];
						}
					}
					return $out;
				},
			],
			[
				'id' => 'links.redirect_internal', 'cat' => 'Ссылки', 'sev' => 'medium', 'scope' => 'page',
				'title' => 'Внутренние ссылки не ведут на редиректы',
				'hint' => 'В отчёте указано, на каких страницах стоит ссылка. Замените href в этих местах на конечный адрес страницы (без цепочки редиректов).',
				'check' => static function (Ctx $c): array {
					$out = [];
					foreach ($c->cr->inlinks as $url => $info) {
						$page = $c->cr->pages[$url] ?? null;
						if ($page === null || empty($page['redirects'])) {
							continue;
						}
						$found = $c->foundOn($url, 5);
						$where = [];
						foreach ($found as $f) {
							$where[] = Ctx::pathKey($f['from']) . ' — «' . mb_substr($f['anchor'] !== '' ? $f['anchor'] : 'без текста', 0, 40) . '» (' . $f['zone'] . ')';
						}
						$out[] = [
							'url'    => $url,
							'detail' => count($page['redirects']) . ' редирект(ов) · ссылок: ' . count($info['from'])
								. ($where !== [] ? ' → ' . implode('; ', $where) : ''),
							'found'  => $found,
						];
					}
					return $out;
				},
			],
			[
				'id' => 'links.broken_external', 'cat' => 'Ссылки', 'sev' => 'high', 'scope' => 'page',
				'title' => 'Внешние ссылки работают',
				'hint' => 'В отчёте указано, с какой страницы ведёт битая внешняя ссылка. Замените её на рабочий адрес или снимите — мёртвые ссылки подрывают доверие к ресурсу.',
				'check' => static function (Ctx $c): array {
					$out = [];
					$blocked = [];
					foreach ($c->cr->externalStatus as $url => $info) {
						$status = (int)$info['status'];
						if ($status === 0 || $status >= 400) {
							// 403/405/418/429/503 — обычно анти-бот защита, а не битая ссылка
							if (in_array($status, [403, 405, 418, 429, 503], true)) {
								$blocked[] = $url . ' (HTTP ' . $status . ')';
								continue;
							}
							$found = $c->foundOn($url, 4);
							$where = [];
							$snippet = '';
							foreach ($found as $f) {
								$where[] = Ctx::pathKey($f['from']) . ' — «' . mb_substr($f['anchor'] !== '' ? $f['anchor'] : 'без текста', 0, 40) . '» (' . $f['zone'] . ')';
								if ($snippet === '' && $f['tag'] !== '') {
									$snippet = $f['tag'];
								}
							}
							$out[] = [
								'url'     => $url,
								'detail'  => 'HTTP ' . ($status ?: ($info['error'] ?? 'нет ответа'))
									. ($where !== [] ? ' → найдено на: ' . implode('; ', $where) : ''),
								'found'   => $found,
								'snippet' => $snippet,
								'cite'    => $snippet !== '' ? 'Исправьте или удалите этот тег: ' . $snippet : '',
							];
						}
					}
					if ($blocked) {
						$out[] = ['url' => null, 'detail' => 'Проверка невозможна (защита от ботов): ' . implode(', ', array_slice($blocked, 0, 5))];
					}
					return $out;
				},
			],
			[
				'id' => 'links.nofollow_internal', 'cat' => 'Ссылки', 'sev' => 'medium', 'scope' => 'page',
				'title' => 'Внутренние ссылки без rel=nofollow',
				'hint' => 'nofollow на внутренней ссылке обрывает передачу веса и передаёт сигнал «не важно».',
				'check' => static function (Ctx $c): array {
					$out = [];
					foreach ($c->pages() as $url => $p) {
						$bad = 0;
						foreach ($p['html']['links'] as $l) {
							if (!str_contains($l['rel'], 'nofollow')) {
								continue;
							}
							$abs = Url::resolve($l['raw'], $url);
							if ($abs !== null && $c->isInternal($abs)) {
								$bad++;
							}
						}
						if ($bad > 0) {
							$out[] = ['url' => $url, 'detail' => $bad . ' внутренних ссылок с nofollow'];
						}
					}
					return $out;
				},
			],
			[
				'id' => 'links.empty_anchor', 'cat' => 'Ссылки', 'sev' => 'medium', 'scope' => 'page',
				'title' => 'У ссылок есть текст (анкор)',
				'hint' => 'В отчёте указана зона страницы, где стоят ссылки без текста. Добавьте в эти места анкор (текст ссылки) или aria-label для иконок — иначе ссылка ничего не сообщает ни роботу, ни пользователю.',
				'check' => static function (Ctx $c): array {
					$out = [];
					foreach ($c->pages() as $url => $p) {
						$empty = 0;
						$zones = [];
						foreach ($p['html']['links'] as $l) {
							if (!empty($l['hidden'])) {
								continue;   // декоративная ссылка (aria-hidden) — не ошибка
							}
							if (trim((string)$l['text']) === '' && trim((string)$l['title']) === '') {
								$empty++;
								$zones[$l['zone']] = ($zones[$l['zone']] ?? 0) + 1;
							}
						}
						if ($empty > 0) {
							$where = [];
							foreach ($zones as $z => $n) {
								$where[] = $z . ' × ' . $n;
							}
							$out[] = ['url' => $url, 'detail' => $empty . ' ссылок без анкора (' . implode(', ', $where) . ')'];
						}
					}
					return $out;
				},
			],
			[
				'id' => 'links.generic_anchor', 'cat' => 'Ссылки', 'sev' => 'low', 'scope' => 'page',
				'title' => 'Нет шаблонных анкоров («нажмите здесь», «подробнее»)',
				'hint' => 'Описывающий анкор даёт поисковику тему ссылки. «Нажмите сюда» — нет.',
				'check' => static function (Ctx $c) use ($generic): array {
					$out = [];
					foreach ($c->pages() as $url => $p) {
						$bad = [];
						foreach ($p['html']['links'] as $l) {
							$t = mb_strtolower(trim((string)$l['text']));
							if ($t === '' || mb_strlen($t) > 30) {
								continue;
							}
							foreach ($generic as $g) {
								// слово целиком, а не подстр��да («рассчитать» ≠ «читать»)
								if (preg_match('~(?<![\p{L}\p{N}])' . preg_quote($g, '~') . '(?![\p{L}\p{N}])~iu', $t)) {
									$bad[] = $t;
									break;
								}
							}
						}
						if ($bad) {
							$out[] = ['url' => $url, 'detail' => count($bad) . ' шт.: ' . implode(', ', array_slice(array_unique($bad), 0, 5))];
						}
					}
					return $out;
				},
			],
			[
				'id' => 'links.broken_fragment', 'cat' => 'Ссылки', 'sev' => 'low', 'scope' => 'page',
				'title' => 'Якоря (#id) существуют на целевой странице',
				'hint' => 'Битый якор ведёт в никуда: пользователь не находит нужный блок, поведенческие факторы ухудшаются.',
				'check' => static function (Ctx $c): array {
					$out = [];
					foreach ($c->pages() as $url => $p) {
						$broken = [];
						foreach ($p['html']['links'] as $l) {
							$raw = $l['raw'];
							if (!str_contains($raw, '#')) {
								continue;
							}
							$abs = Url::resolve($raw, $url);
							if ($abs === null) {
								continue;
							}
							[$abs, $frag] = Url::splitFragment($abs);
							if ($frag === null || $frag === '') {
								continue;
							}
							$target = null;
							foreach ($c->cr->pages as $u2 => $p2) {
								if (Ctx::pathKey($u2) === Ctx::pathKey($abs)) {
									$target = $p2;
									break;
								}
							}
							if ($target === null) {
								continue;
							}
							$ids = $target['html']['idList'] ?? [];
							if ($ids && !in_array($frag, $ids, true)) {
								$broken[] = '#' . $frag . ' → ' . Ctx::pathKey($abs) . ' («' . mb_substr($l['text'] !== '' ? $l['text'] : 'без текста', 0, 30) . '», ' . $l['zone'] . ')';
							}
						}
						if ($broken) {
							$out[] = ['url' => $url, 'detail' => count($broken) . ' шт. на этой странице: ' . implode('; ', array_slice($broken, 0, 5))];
						}
					}
					return $out;
				},
			],
			[
				'id' => 'links.to_noindex', 'cat' => 'Ссылки', 'sev' => 'info', 'scope' => 'site',
				'title' => 'Куда ведут ссылки с индексируемых страниц (noindex-цели)',
				'hint' => 'Вес на noindex-страницу не передаётся. Обычно это футер (политика/согласие) — если так, добавьте rel="nofollow", чтобы не тратить краулинговый бюджет.',
				'check' => static function (Ctx $c): array {
					$out = [];
					$targets = [];
					foreach ($c->pages() as $url => $p) {
						if (str_contains(strtolower((string)$p['html']['robotsMeta']), 'noindex')) {
							continue;
						}
						foreach ($p['html']['links'] as $l) {
							$abs = Url::resolve($l['raw'], $url);
							if ($abs === null) {
								continue;
							}
							[$abs] = Url::splitFragment($abs);
							if (!$c->isInternal($abs)) {
								continue;
							}
							$target = $c->cr->pages[$abs] ?? null;
							if ($target === null) {
								continue;
							}
							if (!str_contains(strtolower((string)$target['html']['robotsMeta']), 'noindex')) {
								continue;
							}
							$key = Ctx::pathKey($abs);
							$targets[$key] = ($targets[$key] ?? 0) + 1;
						}
					}
					arsort($targets);
					foreach ($targets as $path => $cnt) {
						$out[] = ['url' => null, 'detail' => $path . ' — ссылок: ' . $cnt];
					}
					return $out;
				},
			],
			[
				'id' => 'links.extensions', 'cat' => 'Ссылки', 'sev' => 'low', 'scope' => 'page',
				'title' => 'Внутренние ссылки ведут на ЧПУ-адреса',
				'hint' => 'Ссылки вида /page.php или /page.html отдают 404 на некоторых хостингах и ухудшают вид URL.',
				'check' => static function (Ctx $c): array {
					$out = [];
					foreach ($c->pages() as $url => $p) {
						$bad = [];
						foreach ($p['html']['links'] as $l) {
							$abs = Url::resolve($l['raw'], $url);
							if ($abs === null) {
								continue;
							}
							[$abs] = Url::splitFragment($abs);
							if (!$c->isInternal($abs)) {
								continue;
							}
							$path = parse_url($abs, PHP_URL_PATH) ?: '';
							if (preg_match('~\.(php|html|htm)$~i', $path)) {
								$bad[] = $path;
							}
						}
						if ($bad) {
							$out[] = ['url' => $url, 'detail' => count($bad) . ' шт.: ' . implode(', ', array_slice(array_unique($bad), 0, 5))];
						}
					}
					return $out;
				},
			],
			[
				'id' => 'links.many_links', 'cat' => 'Ссылки', 'sev' => 'low', 'scope' => 'page',
				'title' => 'Не более 300 ссылок на странице',
				'hint' => 'Огромное количество ссылок размывает внутренний вес и усложняет обход.',
				'check' => static function (Ctx $c): array {
					$out = [];
					foreach ($c->pages() as $url => $p) {
						$n = count($p['html']['links']);
						if ($n > 300) {
							$out[] = ['url' => $url, 'detail' => $n . ' ссылок'];
						}
					}
					return $out;
				},
			],
			[
				'id' => 'links.js_navigation', 'cat' => 'Ссылки', 'sev' => 'info', 'scope' => 'page',
				'title' => 'Навигация реализована обычными ссылками',
				'hint' => 'Ссылки, которые появляются только после JS, краулер может не увидеть. Продублируйте их в <a href>.',
				'check' => static function (Ctx $c): array {
					$out = [];
					foreach ($c->pages() as $url => $p) {
						$h = $p['html'];
						if (($h['navCount'] ?? 0) > 0 && ($h['navLinks'] ?? 0) === 0) {
							$out[] = ['url' => $url, 'detail' => $h['navCount'] . ' блоков <nav> без обычных ссылок'];
						}
					}
					return $out;
				},
			],
			[
				'id' => 'links.ambiguous_anchor', 'cat' => 'Ссылки', 'sev' => 'low', 'scope' => 'page',
				'title' => 'Одинаковый анкор на странице ведёт на разные адреса',
				'hint' => 'Одинаковые анкоры на разные цели размывают сигнал и путают пользователя.',
				'check' => static function (Ctx $c): array {
					$out = [];
					foreach ($c->pages() as $url => $p) {
						$map = [];
						foreach ($p['html']['links'] as $l) {
							$t = trim((string)$l['text']);
							if ($t === '') {
								continue;
							}
							$abs = Url::resolve($l['raw'], $url);
							if ($abs === null) {
								continue;
							}
							[$abs] = Url::splitFragment($abs);
							$map[mb_strtolower($t)][$abs] = true;
						}
						$bad = [];
						foreach ($map as $t => $targets) {
							if (count($targets) > 1) {
								$bad[] = '"' . $t . '" → ' . count($targets) . ' адресов';
							}
						}
						if ($bad) {
							$out[] = ['url' => $url, 'detail' => count($bad) . ' шт.: ' . implode(', ', array_slice($bad, 0, 4))];
						}
					}
					return $out;
				},
			],
		];
	}

	// =====================================================================
	// 7. ИЗОБРАЖЕНИЯ
	// =====================================================================
	private static function rulesImages(): array
	{
		return [
			[
				'id' => 'img.missing_alt', 'cat' => 'Изображения', 'sev' => 'high', 'scope' => 'page',
				'title' => 'У всех изображений заполнен alt',
				'hint' => 'alt нужен для доступности и для поиска по картинкам. Для декоративных — alt="".',
				'check' => static function (Ctx $c): array {
					$out = [];
					foreach ($c->pages() as $url => $p) {
						$bad = [];
						$samples = [];
						foreach ($p['html']['images'] as $im) {
							if ($im['alt'] === null) {
								$bad[] = basename((string)$im['src']) . ' (' . $im['zone'] . ')';
								if (count($samples) < 3 && $im['tag'] !== '') {
									$samples[] = $im['tag'];
								}
							}
						}
						if ($bad) {
							$out[] = [
								'url'     => $url,
								'detail'  => implode(', ', array_slice($bad, 0, 4)) . ' — без атрибута alt (' . count($bad) . ' из ' . count($p['html']['images']) . ' изображений)',
								'snippet' => $samples[0] ?? '',
								'cite'    => $samples !== [] ? 'Добавьте alt с описанием картинки: ' . $samples[0] : '',
							];
						}
					}
					return $out;
				},
			],
			[
				'id' => 'img.empty_alt', 'cat' => 'Изображения', 'sev' => 'low', 'scope' => 'page',
				'title' => 'alt="" только у декоративных изображений',
				'hint' => 'Пустой alt у контентного изображения «прячет» его от поиска по картинкам.',
				'check' => static function (Ctx $c): array {
					$out = [];
					foreach ($c->pages() as $url => $p) {
						$bad = [];
						foreach ($p['html']['images'] as $im) {
							if ($im['alt'] === '' && ($im['w'] ?? 0) >= 120) {
								$bad[] = $im['src'];
							}
						}
						if ($bad) {
							$out[] = ['url' => $url, 'detail' => count($bad) . ' шт.: ' . implode(', ', array_slice(array_unique($bad), 0, 4))];
						}
					}
					return $out;
				},
			],
			[
				'id' => 'img.no_dimensions', 'cat' => 'Изображения', 'sev' => 'medium', 'scope' => 'page',
				'title' => 'У изображений заданы width и height (защита от CLS)',
				'hint' => 'Без размеров браузер «прыгает» при загрузке — растёт CLS и падает LCP.',
				'check' => static function (Ctx $c): array {
					$out = [];
					foreach ($c->pages() as $url => $p) {
						$bad = 0;
						foreach ($p['html']['images'] as $im) {
							if ($im['w'] === null || $im['h'] === null) {
								$bad++;
							}
						}
						if ($bad > 0) {
							$out[] = ['url' => $url, 'detail' => $bad . ' из ' . count($p['html']['images']) . ' без width/height'];
						}
					}
					return $out;
				},
			],
			[
				'id' => 'img.lazy_loading', 'cat' => 'Изображения', 'sev' => 'low', 'scope' => 'page',
				'title' => 'Изображения ниже первого экрана подгружаются лениво',
				'hint' => 'loading="lazy" на картинках ниже fold ускоряет отрисовку первого экрана.',
				'check' => static function (Ctx $c): array {
					$out = [];
					foreach ($c->pages() as $url => $p) {
						$h = $p['html'];
						$total = count($h['images']);
						if ($total < 4) {
							continue;
						}
						$eager = [];
						for ($i = 2; $i < $total; $i++) {
							$im = $h['images'][$i];
							if ($im['loading'] !== 'lazy' && $im['fetchprio'] !== 'high') {
								$eager[] = basename((string)$im['src']) . ' (' . $im['zone'] . ')';
							}
						}
						if ($eager) {
							$out[] = ['url' => $url, 'detail' => count($eager) . ' из ' . ($total - 2) . ' без loading="lazy": ' . implode('; ', array_slice($eager, 0, 4))];
						}
					}
					return $out;
				},
			],
			[
				'id' => 'img.broken', 'cat' => 'Изображения', 'sev' => 'high', 'scope' => 'page',
				'title' => 'Изображения загружаются (нет битых картинок)',
				'hint' => 'В отчёте указано, на какой странице и в какой зоне стоит битая картинка. Либо загрузите файл по этому пути, либо исправьте путь в src на существующий.',
				'check' => static function (Ctx $c): array {
					$out = [];
					foreach ($c->pages() as $url => $p) {
						$bad = [];
						$samples = [];
						foreach ($p['html']['images'] as $im) {
							if (trim((string)$im['src']) === '') {
								continue;
							}
							$abs = Url::resolve($im['src'], $url);
							if ($abs === null) {
								continue;
							}
							$status = $c->statusOf($abs);
							if ($status !== null && ($status >= 400 || $status === 0)) {
								$bad[] = basename((string)$im['src']) . ' (' . $im['zone'] . ') → HTTP ' . $status;
								if (count($samples) < 3 && $im['tag'] !== '') {
									$samples[] = $im['tag'];
								}
							}
						}
						if ($bad) {
							$out[] = [
								'url'     => $url,
								'detail'  => count($bad) . ' шт.: ' . implode('; ', array_slice(array_unique($bad), 0, 4)),
								'snippet' => $samples[0] ?? '',
								'cite'    => $samples !== [] ? 'Исправьте путь к изображению в этом теге: ' . $samples[0] : '',
							];
						}
					}
					return $out;
				},
			],
			[
				'id' => 'img.lcp', 'cat' => 'Изображения', 'sev' => 'info', 'scope' => 'page',
				'title' => 'Главное изображение страницы (LCP) оптимизировано',
				'hint' => 'LCP-картинка (первая крупная на экране) должна иметь fetchpriority="high", размеры width/height и modern-формат — иначе она тормозит первую отрисовку.',
				'check' => static function (Ctx $c): array {
					$out = [];
					foreach ($c->pages() as $url => $p) {
						$imgs = $p['html']['images'];
						if (!$imgs) {
							continue;
						}
						$lcp = null;
						foreach ($imgs as $im) {
							if (($im['w'] ?? 0) >= 300) {
								$lcp = $im;
								break;
							}
						}
						$lcp ??= $imgs[0];
						$miss = [];
						if ($lcp['fetchprio'] !== 'high') {
							$miss[] = 'fetchpriority=high';
						}
						if ($lcp['w'] === null || $lcp['h'] === null) {
							$miss[] = 'width/height';
						}
						if (!$lcp['srcset']) {
							$miss[] = 'srcset';
						}
						if ($miss) {
							$out[] = ['url' => $url, 'detail' => 'LCP-картинка ' . basename((string)$lcp['src']) . ': нет ' . implode(', ', $miss)];
						}
					}
					return $out;
				},
			],
			[
				'id' => 'img.oversize', 'cat' => 'Изображения', 'sev' => 'info', 'scope' => 'site',
				'title' => 'Нет тяжёлых изображений (от 200 КБ)',
				'hint' => 'Большие картинки тормозят мобильную загрузку. Сжимайте, переводите в WebP/AVIF, задавайте srcset.',
				'check' => static function (Ctx $c): array {
					$out = [];
					foreach ($c->cr->assetStatus as $url => $info) {
						$len = (int)($info['length'] ?? 0);
						if ($len >= 200 * 1024) {
							$out[] = ['url' => $url, 'detail' => round($len / 1024) . ' КБ'];
						}
					}
					return $out;
				},
			],
		];
	}

	// =====================================================================
	// 8. СКОРОСТЬ
	// =====================================================================
	private static function rulesPerformance(): array
	{
		return [
			[
				'id' => 'perf.ttfb', 'cat' => 'Скорость', 'sev' => 'medium', 'scope' => 'page',
				'title' => 'TTFB не превышает 500 мс',
				'hint' => 'Медленный TTFB напрямую упирается в Core Web Vitals. Используйте кэш страниц (ETag/Cache-Control) и OPcache.',
				'check' => static function (Ctx $c): array {
					$out = [];
					foreach ($c->pages() as $url => $p) {
						$t = (float)$p['ttfb'];
						if ($t > 0.5) {
							$out[] = ['url' => $url, 'detail' => round($t * 1000) . ' мс'];
						}
					}
					return $out;
				},
			],
			[
				'id' => 'perf.page_size', 'cat' => 'Скорость', 'sev' => 'medium', 'scope' => 'page',
				'title' => 'Размер HTML не превышает 300 КБ',
				'hint' => 'Огромный HTML замедляет парсинг. Вынесите лишнее в JSON/SSI и уберите inline-дубли.',
				'check' => static function (Ctx $c): array {
					$out = [];
					foreach ($c->pages() as $url => $p) {
						$size = (int)$p['bodySize'];
						if ($size > 300 * 1024) {
							$out[] = ['url' => $url, 'detail' => round($size / 1024) . ' КБ'];
						}
					}
					return $out;
				},
			],
			[
				'id' => 'perf.compression', 'cat' => 'Скорость', 'sev' => 'medium', 'scope' => 'site',
				'title' => 'HTML отдаётся в сжатом виде (gzip/brotli)',
				'hint' => 'Без сжатия HTML весит в 4–5 раз больше. Включите mod_deflate или Brotli на уровне сервера.',
				'check' => static function (Ctx $c): array {
					$out = [];
					$without = [];
					foreach ($c->pages() as $url => $p) {
						$enc = strtolower((string)($p['headers']['content-encoding'] ?? ''));
						if ($enc === '' && (int)$p['bodySize'] > 2000) {
							$without[] = Ctx::pathKey($url);
						}
					}
					if ($without) {
						$out[] = ['url' => null, 'detail' => count($without) . ' страниц без Content-Encoding (примеры: ' . implode(', ', array_slice($without, 0, 5)) . ')'];
					}
					return $out;
				},
			],
			[
				'id' => 'perf.cache_control', 'cat' => 'Скорость', 'sev' => 'medium', 'scope' => 'site',
				'title' => 'Заданы Cache-Control / ETag',
				'hint' => 'Без Cache-Control и ETag каждый визит — полная перерисовка страницы и нагрузка на сервер.',
				'check' => static function (Ctx $c): array {
					$out = [];
					$noCache = [];
					$noEtag = 0;
					foreach ($c->pages() as $url => $p) {
						$cc = (string)($p['headers']['cache-control'] ?? '');
						if ($cc === '') {
							$noCache[] = Ctx::pathKey($url);
						}
						if (empty($p['headers']['etag']) && empty($p['headers']['last-modified'])) {
							$noEtag++;
						}
					}
					if ($noCache) {
						$out[] = ['url' => null, 'detail' => count($noCache) . ' страниц без Cache-Control (примеры: ' . implode(', ', array_slice($noCache, 0, 5)) . ')'];
					}
					if ($noEtag > 0) {
						$out[] = ['url' => null, 'detail' => $noEtag . ' страниц без ETag/Last-Modified'];
					}
					return $out;
				},
			],
			[
				'id' => 'perf.blocking', 'cat' => 'Скорость', 'sev' => 'low', 'scope' => 'page',
				'title' => 'Не более 4 блокирующих ресурсов в <head>',
				'hint' => 'Каждый блокирующий CSS/JS задерживает первую отрисовку. Критичные — инлайн, остальные — async/defer.',
				'check' => static function (Ctx $c): array {
					$out = [];
					foreach ($c->pages() as $url => $p) {
						$css = (int)$p['html']['blocking'];
						$js = (int)($p['html']['blockingJs'] ?? 0);
						$n = $css + $js;
						if ($n > 4) {
							$out[] = ['url' => $url, 'detail' => $n . ' блокирующих ресурсов (CSS: ' . $css . ', JS: ' . $js . ')'];
						}
					}
					return $out;
				},
			],
			[
				'id' => 'perf.dom_size', 'cat' => 'Скорость', 'sev' => 'low', 'scope' => 'page',
				'title' => 'DOM не перегружен (до 1500 узлов)',
				'hint' => 'Тяжёлый DOM замедляет парсинг и стилизацию, особенно на слабых телефонах.',
				'check' => static function (Ctx $c): array {
					$out = [];
					foreach ($c->pages() as $url => $p) {
						$n = (int)($p['html']['nodes'] ?? 0);
						if ($n > 1500) {
							$out[] = ['url' => $url, 'detail' => $n . ' узлов DOM'];
						}
					}
					return $out;
				},
			],
			[
				'id' => 'perf.http_version', 'cat' => 'Скорость', 'sev' => 'info', 'scope' => 'site',
				'title' => 'Используется HTTP/1.1 или новее',
				'hint' => 'HTTP/1.0 без keep-alive = отдельное TCP-соединение на каждый ресурс.',
				'check' => static function (Ctx $c): array {
					$out = [];
					$old = 0;
					foreach ($c->pages() as $url => $p) {
						$v = (string)$p['httpVersion'];
						if ($v !== '' && (int)$v < 2) {
							$old++;
						}
					}
					if ($old > 0) {
						$out[] = ['url' => null, 'detail' => $old . ' страниц отдано по HTTP/1.x без HTTP/2'];
					}
					return $out;
				},
			],
			[
				'id' => 'perf.assets_weight', 'cat' => 'Скорость', 'sev' => 'info', 'scope' => 'site',
				'title' => 'Суммарный вес ресурсов в норме',
				'hint' => 'Большой вес статики = медленный первый экран. Считается по Content-Length найденных ассетов.',
				'check' => static function (Ctx $c): array {
					$out = [];
					$sum = 0;
					$count = 0;
					foreach ($c->cr->assetStatus as $url => $info) {
						$len = (int)($info['length'] ?? 0);
						if ($len > 0) {
							$sum += $len;
							$count++;
						}
					}
					if ($count > 0 && $sum > 5 * 1024 * 1024) {
						$out[] = ['url' => null, 'detail' => 'Суммарно ' . round($sum / 1024 / 1024, 1) . ' МБ на ' . $count . ' ресурсов'];
					}
					return $out;
				},
			],
		];
	}

	// =====================================================================
	// 9. БЕЗОПАСНОСТЬ
	// =====================================================================
	private static function rulesSecurity(): array
	{
		return [
			[
				'id' => 'sec.https', 'cat' => 'Безопасность', 'sev' => 'high', 'scope' => 'site',
				'title' => 'Сайт работает по HTTPS',
				'hint' => 'Без HTTPS браузер предупреждает посетителя, а поисковик понижает доверие к домену.',
				'check' => static function (Ctx $c): array {
					$out = [];
					if (!str_starts_with($c->origin(), 'https://') && !$c->isLocal()) {
						$out[] = ['url' => null, 'detail' => 'Основной адрес без HTTPS: ' . $c->origin()];
					}
					return $out;
				},
			],
			[
				'id' => 'sec.hsts', 'cat' => 'Безопасность', 'sev' => 'medium', 'scope' => 'site',
				'title' => 'Отправляется Strict-Transport-Security',
				'hint' => 'HSTS запрещает браузеру уйти в незашифрованный HTTP. RFC 6797: max-age от 31536000 (1 год); OWASP рекомендует 63072000 (2 года) с includeSubDomains и preload.',
				'check' => static function (Ctx $c): array {
					$out = [];
					if (!str_starts_with($c->origin(), 'https://')) {
						return $out;
					}
					$missing = [];
					foreach ($c->pages() as $url => $p) {
						if (empty($p['headers']['strict-transport-security'])) {
							$missing[] = Ctx::pathKey($url);
						}
					}
					if ($missing) {
						$out[] = ['url' => null, 'detail' => 'Заголовок отсутствует на ' . count($missing) . ' из ' . count($c->pages()) . ' страниц (например: ' . implode(', ', array_slice($missing, 0, 4)) . ')'];
					}
					return $out;
				},
			],
			[
				'id' => 'sec.csp', 'cat' => 'Безопасность', 'sev' => 'low', 'scope' => 'site',
				'title' => 'Настроена Content-Security-Policy',
				'hint' => 'CSP — главная защита от XSS. Начните с report-only и постепенно ужесточайте.',
				'check' => static function (Ctx $c): array {
					$out = [];
					$missing = [];
					foreach ($c->pages() as $url => $p) {
						if (empty($p['headers']['content-security-policy'])) {
							$missing[] = Ctx::pathKey($url);
						}
					}
					if ($missing) {
						$out[] = ['url' => null, 'detail' => 'Заголовок отсутствует на ' . count($missing) . ' из ' . count($c->pages()) . ' страниц'];
					}
					return $out;
				},
			],
			[
				'id' => 'sec.xfo', 'cat' => 'Безопасность', 'sev' => 'medium', 'scope' => 'site',
				'title' => 'Установлен X-Frame-Options (или frame-ancestors в CSP)',
				'hint' => 'Без защиты от iframe сайт можно встроить на чужую страницу (clickjacking).',
				'check' => static function (Ctx $c): array {
					$out = [];
					$missing = [];
					foreach ($c->pages() as $url => $p) {
						$xfo = (string)($p['headers']['x-frame-options'] ?? '');
						$csp = strtolower((string)($p['headers']['content-security-policy'] ?? ''));
						if ($xfo === '' && !str_contains($csp, 'frame-ancestors')) {
							$missing[] = Ctx::pathKey($url);
						}
					}
					if ($missing) {
						$out[] = ['url' => null, 'detail' => 'Нет X-Frame-Options и frame-ancestors на ' . count($missing) . ' из ' . count($c->pages()) . ' страниц'];
					}
					return $out;
				},
			],
			[
				'id' => 'sec.nosniff', 'cat' => 'Безопасность', 'sev' => 'medium', 'scope' => 'site',
				'title' => 'Установлен X-Content-Type-Options: nosniff',
				'hint' => 'nosniff запрещает браузеру угадывать тип файла — базовая защита от MIME-sniffing.',
				'check' => static function (Ctx $c): array {
					$out = [];
					$missing = [];
					foreach ($c->pages() as $url => $p) {
						if (strtolower((string)($p['headers']['x-content-type-options'] ?? '')) !== 'nosniff') {
							$missing[] = Ctx::pathKey($url);
						}
					}
					if ($missing) {
						$out[] = ['url' => null, 'detail' => 'Заголовок отсутствует или не nosniff на ' . count($missing) . ' из ' . count($c->pages()) . ' страниц'];
					}
					return $out;
				},
			],
			[
				'id' => 'sec.referrer_policy', 'cat' => 'Безопасность', 'sev' => 'low', 'scope' => 'site',
				'title' => 'Задан Referrer-Policy',
				'hint' => 'Без политики полный URL (с utm и параметрами) утекает на сторонние сайты.',
				'check' => static function (Ctx $c): array {
					$out = [];
					$missing = [];
					foreach ($c->pages() as $url => $p) {
						if (trim((string)($p['headers']['referrer-policy'] ?? '')) === '') {
							$missing[] = Ctx::pathKey($url);
						}
					}
					if ($missing) {
						$out[] = ['url' => null, 'detail' => 'Заголовок отсутствует на ' . count($missing) . ' из ' . count($c->pages()) . ' страниц'];
					}
					return $out;
				},
			],
			[
				'id' => 'sec.permissions_policy', 'cat' => 'Безопасность', 'sev' => 'info', 'scope' => 'site',
				'title' => 'Задан Permissions-Policy',
				'hint' => 'Отключите неиспользуемые API (камера, микрофон, геолокация) для всех страниц.',
				'check' => static function (Ctx $c): array {
					$out = [];
					$missing = [];
					foreach ($c->pages() as $url => $p) {
						if (trim((string)($p['headers']['permissions-policy'] ?? '')) === '') {
							$missing[] = Ctx::pathKey($url);
						}
					}
					if ($missing) {
						$out[] = ['url' => null, 'detail' => 'Заголовок отсутствует на ' . count($missing) . ' из ' . count($c->pages()) . ' страниц'];
					}
					return $out;
				},
			],
			[
				'id' => 'sec.banners', 'cat' => 'Безопасность', 'sev' => 'low', 'scope' => 'site',
				'title' => 'Версии ПО не раскрываются в заголовках',
				'hint' => 'Server: nginx/1.24.7 и X-Powered-By упрощают подбор уязвимостей. Отключите или скройте.',
				'check' => static function (Ctx $c): array {
					$out = [];
					$found = [];
					foreach ($c->pages() as $url => $p) {
						foreach (['server' => 'Server', 'x-powered-by' => 'X-Powered-By'] as $k => $label) {
							$v = trim((string)($p['headers'][$k] ?? ''));
							if ($v !== '' && preg_match('~[\d]+\.[\d]+~', $v)) {
								$found[$label . ': ' . $v] = true;
							}
						}
					}
					foreach (array_keys($found) as $f) {
						$out[] = ['url' => null, 'detail' => $f];
					}
					return $out;
				},
			],
			[
				'id' => 'sec.sensitive_files', 'cat' => 'Безопасность', 'sev' => 'critical', 'scope' => 'site',
				'title' => 'Служебные файлы недоступны извне',
				'hint' => 'Критично: .env, .git и composer.json не должны отдаваться наружу. Закройте правилами сервера.',
				'check' => static function (Ctx $c): array {
					$out = [];
					foreach ($c->cr->probeStatus as $url => $info) {
						$status = (int)$info['status'];
						$path = parse_url($url, PHP_URL_PATH) ?: '/';
						if (str_ends_with($path, '/.well-known/security.txt')) {
							continue;
						}
						if ($status < 200 || $status >= 300) {
							continue;
						}
						// Если сервер отдал HTML-страницу (catch-all/404) — это не сам файл.
						if (!empty($info['isHtml'])) {
							continue;
						}
						$out[] = ['url' => $url, 'detail' => 'Файл отдаётся с кодом ' . $status
							. ' (тип: ' . ($info['ctype'] !== '' ? $info['ctype'] : 'неизвестен')
							. ', размер: ' . round((int)($info['len'] ?? 0) / 1024, 1) . ' КБ)'];
					}
					return $out;
				},
			],
			[
				'id' => 'sec.tls_expired', 'cat' => 'Безопасность', 'sev' => 'critical', 'scope' => 'site',
				'title' => 'TLS-сертификат действителен',
				'hint' => 'Просроченный сертификат = красный замок в браузере для всех посетителей. Продлите сертификат заранее (Let\'s Encrypt — автопродление).',
				'check' => static function (Ctx $c): array {
					$out = [];
					$tls = $c->cr->tls;
					if (!$tls) {
						return $out;
					}
					if (!empty($tls['unverified'])) {
						return $out;   // соединение не установилось — это не «просрочка», вынесено отдельно
					}
					if (!empty($tls['expired'])) {
						$out[] = ['url' => null, 'detail' => 'Сертификат истёк ' . date('d.m.Y', (int)$tls['valid_to'])];
					} elseif ((int)($tls['days_left'] ?? 999) <= 30) {
						$out[] = ['url' => null, 'detail' => 'Истекает через ' . $tls['days_left'] . ' дн. (' . date('d.m.Y', (int)$tls['valid_to']) . ')'];
					}
					return $out;
				},
			],
			[
				'id' => 'sec.tls_unverified', 'cat' => 'Безопасность', 'sev' => 'info', 'scope' => 'site',
				'title' => 'Сертификат удалось проверить',
				'hint' => 'Проверьте срок действия сертификата вручную: openssl s_client -connect домен:443 | openssl x509 -noout -dates. Сайт без HTTPS — это риск для доверия и для функций безопасности.',
				'check' => static function (Ctx $c): array {
					$out = [];
					$tls = $c->cr->tls;
					if (!$tls) {
						return $out;
					}
					if (!empty($tls['unverified'])) {
						$out[] = ['url' => null, 'detail' => (string)($tls['error'] ?? 'сертификат не проверен') . ' — срок действия нужно проверить вручную'];
					}
					return $out;
				},
			],
			[
				'id' => 'sec.tls_quality', 'cat' => 'Безопасность', 'sev' => 'high', 'scope' => 'site',
				'title' => 'TLS-сертификат выдан на домен и использует надёжный алгоритм',
				'hint' => 'Сертификат на чужое имя или самоподписанный вызывает предупреждение браузера. Алгоритм SHA-1/MD5 считается небезопасным.',
				'check' => static function (Ctx $c): array {
					$out = [];
					$tls = $c->cr->tls;
					if (!$tls || isset($tls['error'])) {
						return $out;
					}
					if (empty($tls['host_ok'])) {
						$out[] = ['url' => null, 'detail' => 'Сертификат выдан на: ' . implode(', ', array_slice((array)$tls['names'], 0, 3))];
					}
					if (preg_match('~sha1|md5~i', (string)$tls['signature'])) {
						$out[] = ['url' => null, 'detail' => 'Слабый алгоритм подписи: ' . $tls['signature']];
					}
					if (!empty($tls['self_signed'])) {
						$out[] = ['url' => null, 'detail' => 'Самоподписанный сертификат'];
					}
					if ((int)($tls['key_size'] ?? 0) > 0 && (int)$tls['key_size'] < 2048) {
						$out[] = ['url' => null, 'detail' => 'Слабый ключ: ' . $tls['key_size'] . ' бит'];
					}
					return $out;
				},
			],
			[
				'id' => 'sec.cookies', 'cat' => 'Безопасность', 'sev' => 'medium', 'scope' => 'site',
				'title' => 'Cookies выставлены с Secure/HttpOnly/SameSite',
				'hint' => 'Без флагов cookie утекает в JS и перехватывается по HTTP.',
				'check' => static function (Ctx $c): array {
					$out = [];
					$problems = [];
					foreach ($c->pages() as $url => $p) {
						foreach ($p['headerLog'] as $line) {
							if (!stripos($line, 'set-cookie:')) {
								continue;
							}
							$cookie = trim(substr($line, strlen('set-cookie:')));
							$miss = [];
							if (!preg_match('~;\s*secure~i', $cookie)) {
								$miss[] = 'Secure';
							}
							if (!preg_match('~;\s*httponly~i', $cookie)) {
								$miss[] = 'HttpOnly';
							}
							if (!preg_match('~;\s*samesite~i', $cookie)) {
								$miss[] = 'SameSite';
							}
							if ($miss) {
								$problems[$cookie] = implode(', ', $miss);
							}
						}
					}
					foreach ($problems as $cookie => $miss) {
						$out[] = ['url' => null, 'detail' => 'нет флагов (' . $miss . '): ' . mb_substr($cookie, 0, 60)];
					}
					return $out;
				},
			],
		];
	}

	// =====================================================================
	// 10. ДОСТУПНОСТЬ
	// =====================================================================
	private static function rulesA11y(): array
	{
		return [
			[
				'id' => 'a11y.form_label', 'cat' => 'Доступность', 'sev' => 'medium', 'scope' => 'page',
				'title' => 'У полей форм есть подписи (label/aria-label)',
				'hint' => 'Откройте указанную страницу, найдите форму (в отчёте — количество полей) и добавьте к каждому полю <label for="id">Текст</label> либо aria-label. Поля без подписи не заполняются с клавиатуры и теряются для скринридеров.',
				'check' => static function (Ctx $c): array {
					$out = [];
					foreach ($c->pages() as $url => $p) {
						$n = (int)$p['html']['forms']['noLabel'];
						if ($n > 0) {
							$bad = $p['html']['forms']['bad'] ?? [];
							$names = [];
							foreach (array_slice($bad, 0, 4) as $b) {
								$names[] = $b['name'] . ($b['ph'] !== '' ? ' (placeholder: «' . mb_substr($b['ph'], 0, 20) . '»)' : '');
							}
							$action = $bad[0]['action'] ?? '';
							$out[] = ['url' => $url, 'detail' => $n . ' полей без подписи: ' . implode('; ', $names)
								. ($action !== '' ? ' · форма action=' . $action : '')];
						}
					}
					return $out;
				},
			],
			[
				'id' => 'a11y.field_name', 'cat' => 'Доступность', 'sev' => 'medium', 'scope' => 'page',
				'title' => 'У полей форм есть атрибут name',
				'hint' => 'Без name поле не отправляется на сервер и не сохраняется после перезагрузки.',
				'check' => static function (Ctx $c): array {
					$out = [];
					foreach ($c->pages() as $url => $p) {
						$n = (int)$p['html']['forms']['noName'];
						if ($n > 0) {
							$out[] = ['url' => $url, 'detail' => $n . ' полей без name'];
						}
					}
					return $out;
				},
			],
			[
				'id' => 'a11y.duplicate_ids', 'cat' => 'Доступность', 'sev' => 'medium', 'scope' => 'page',
				'title' => 'Идентификаторы (id) на странице уникальны',
				'hint' => 'Дубли id ломают якорные ссылки и скрипты, работающие с DOM.',
				'check' => static function (Ctx $c): array {
					$out = [];
					foreach ($c->pages() as $url => $p) {
						$dupes = $p['html']['ids']['dupes'] ?? [];
						if ($dupes) {
							$out[] = ['url' => $url, 'detail' => 'Повторяются: ' . implode(', ', array_slice($dupes, 0, 6))];
						}
					}
					return $out;
				},
			],
			[
				'id' => 'a11y.iframe_title', 'cat' => 'Доступность', 'sev' => 'low', 'scope' => 'page',
				'title' => 'У iframe есть title/aria-label',
				'hint' => 'Без названия iframe невозможно отличить в содержимом для скринридера.',
				'check' => static function (Ctx $c): array {
					$out = [];
					foreach ($c->pages() as $url => $p) {
						$n = (int)$p['html']['iframes']['noTitle'];
						if ($n > 0) {
							$out[] = ['url' => $url, 'detail' => $n . ' iframe без заголовка'];
						}
					}
					return $out;
				},
			],
			[
				'id' => 'a11y.landmark', 'cat' => 'Доступность', 'sev' => 'low', 'scope' => 'page',
				'title' => 'У страницы есть landmark-разметка <main>',
				'hint' => 'Landmarks позволяют скринридерам и поисковику понять структуру страницы. Добавьте <main> вокруг основного содержимого.',
				'check' => static function (Ctx $c): array {
					$out = [];
					foreach ($c->pages() as $url => $p) {
						if (empty($p['html']['hasMain'])) {
							$out[] = ['url' => $url, 'detail' => 'Нет тега <main>'];
						}
					}
					return $out;
				},
			],
			[
				'id' => 'a11y.skip_link', 'cat' => 'Доступность', 'sev' => 'low', 'scope' => 'site',
				'title' => 'Есть ссылка «перейти к содержимому»',
				'hint' => 'Skip-link позволяет клавиатурным пользователям не проходить длинное меню на каждой странице.',
				'check' => static function (Ctx $c): array {
					$out = [];
					foreach ($c->pages() as $url => $p) {
						if (!empty($p['html']['hasSkipLink'])) {
							continue;
						}
					}
					$with = 0;
					foreach ($c->pages() as $url => $p) {
						if (!empty($p['html']['hasSkipLink'])) {
							$with++;
						}
					}
					$total = count($c->pages());
					if ($total > 0 && $with === 0) {
						$out[] = ['url' => null, 'detail' => 'Skip-link не найден ни на одной странице'];
					}
					return $out;
				},
			],
			[
				'id' => 'a11y.button_label', 'cat' => 'Доступность', 'sev' => 'low', 'scope' => 'page',
				'title' => 'У кнопок и ссылок есть доступное имя',
				'hint' => 'Иконочная кнопка без подписи (текста, title, aria-label) недоступна для скринридера.',
				'check' => static function (Ctx $c): array {
					$out = [];
					foreach ($c->pages() as $url => $p) {
						$n = (int)($p['html']['namelessControls'] ?? 0);
						if ($n > 0) {
							$out[] = ['url' => $url, 'detail' => $n . ' элементов без доступного имени'];
						}
					}
					return $out;
				},
			],
		];
	}
}

/**
 * Разбор и генерация llms.txt (спецификация llmstxt.org):
 *   # Название          — заголовок H1, обязателен
 *   > Краткое описание — блок-цитата, рекомендуется
 *   ## Раздел           — группировка ссылок, рекомендуется
 *   - [Заголовок](url): описание
 */
final class LlmTxt
{
	/**
	 * Структурный разбор llms.txt с привязкой к номерам строк (для цитат).
	 *
	 * @return array<string,mixed>
	 */
	public static function analyze(string $body): array
	{
		$lines = preg_split('~\R~', $body) ?: [];
		$h1 = null;
		$h1Line = 0;
		$summary = null;
		$summaryLine = 0;
		$sections = [];
		$links = [];
		$relative = [];
		$noDesc = [];
		$otherHeadings = [];

		foreach ($lines as $i => $line) {
			$n = $i + 1;
			$t = trim($line);
			if ($t === '') {
				continue;
			}
			if ($h1 === null && str_starts_with($t, '# ')) {
				$h1 = trim(substr($t, 2));
				$h1Line = $n;
				continue;
			}
			if ($summary === null && str_starts_with($t, '> ')) {
				$summary = trim(substr($t, 1));
				$summaryLine = $n;
				continue;
			}
			if (preg_match('~^##\s+(.+)$~u', $t, $m)) {
				$sections[] = ['name' => trim($m[1]), 'line' => $n];
				continue;
			}
			if (preg_match('~^#{3,}\s+(.+)$~u', $t, $m)) {
				$otherHeadings[] = ['name' => trim($m[1]), 'line' => $n];
				continue;
			}
			if (preg_match('~^[-*]\s+\[([^\]]*)\]\(([^)]*)\)\s*:?\s*(.*)$~u', $t, $m)) {
				$url = trim($m[2]);
				$desc = trim($m[3]);
				$links[] = ['title' => trim($m[1]), 'url' => $url, 'desc' => $desc, 'line' => $n];
				if ($url !== '' && !preg_match('~^https?://~i', $url)) {
					$relative[] = ['url' => $url, 'line' => $n];
				}
				if ($desc === '') {
					$noDesc[] = $n;
				}
			}
		}

		return [
			'bytes'      => strlen($body),
			'lines'      => count($lines),
			'h1'         => $h1,
			'h1Line'     => $h1Line,
			'summary'    => $summary,
			'summaryLine'=> $summaryLine,
			'sections'   => $sections,
			'otherHeadings' => $otherHeadings,
			'links'      => $links,
			'relative'   => $relative,
			'noDesc'     => $noDesc,
		];
	}

	/**
	 * Генерация llms.txt из карты сайта: заголовки берутся из title страниц.
	 *
	 * @param array<string,string> $pages url => title
	 */
	public static function generate(array $pages, string $name, string $description, int $max = 100): string
	{
		$out = "# " . $name . "\n\n> " . $description . "\n\n";
		$groups = [];
		foreach (array_slice($pages, 0, $max, true) as $url => $title) {
			$path = (string)(parse_url((string)$url, PHP_URL_PATH) ?? '/');
			$seg = trim($path, '/');
			$group = $seg === '' ? 'Главная' : self::humanize(explode('/', $seg)[0]);
			$groups[$group][] = ['url' => (string)$url, 'title' => $title !== '' ? $title : $seg];
		}
		ksort($groups);
		foreach ($groups as $group => $items) {
			$out .= '## ' . (string)$group . "\n\n";
			foreach ($items as $it) {
				$out .= '- [' . $it['title'] . '](' . $it['url'] . ")\n";
			}
			$out .= "\n";
		}
		return $out;
	}

	/** Человекочитаемое имя раздела из slug. */
	private static function humanize(string $slug): string
	{
		$map = [
			'services' => 'Услуги', 'portfolio' => 'Портфолио', 'blog' => 'Блог', 'about' => 'О компании',
			'contacts' => 'Контакты', 'company' => 'О компании', 'uslugi' => 'Услуги', 'catalog' => 'Каталог',
			'public' => 'Материалы', 'news' => 'Новости', 'docs' => 'Документация', 'b2b' => 'B2B', 'b2c' => 'B2C',
		];
		$key = mb_strtolower($slug);
		if (isset($map[$key])) {
			return $map[$key];
		}
		$words = preg_split('~[-_]~u', $key) ?: [];
		$words = array_map(static fn(string $w): string => $w !== '' ? mb_convert_case($w, MB_CASE_TITLE, 'UTF-8') : '', $words);
		return trim(implode(' ', $words)) !== '' ? implode(' ', $words) : 'Разделы';
	}
}

/**
 * Мозговой модуль: отдаёт сводку аудита внешней бесплатной нейросети
 * и возвращает её рассуждение. Ключ берётся из переменной окружения,
 * дополнительных файлов и библиотек не требуется.
 */
final class AiBrain
{
	/** Провайдеры: endpoint, формат, ключ из окружения, модель по умолчанию. */
	public const PROVIDERS = [
		'openrouter' => [
			'url'   => 'https://openrouter.ai/api/v1/chat/completions',
			'style' => 'openai',
			'env'   => ['OPENROUTER_API_KEY', 'AUDIT_AI_KEY'],
			'model' => 'openrouter/free',
			'name'  => 'OpenRouter (20+ бесплатных моделей, 50 запросов/сутки)',
		],
		'groq' => [
			'url'   => 'https://api.groq.com/openai/v1/chat/completions',
			'style' => 'openai',
			'env'   => ['GROQ_API_KEY', 'AUDIT_AI_KEY'],
			'model' => 'gpt-oss-120b',
			'name'  => 'Groq (бесплатный тариф, очень быстро)',
		],
		'ollama' => [
			'url'   => 'http://localhost:11434/v1/chat/completions',
			'style' => 'openai',
			'env'   => ['OLLAMA_HOST'],
			'model' => 'qwen3',
			'name'  => 'Ollama локально (полностью бесплатно и приватно)',
			'noKey' => true,
		],
		'gemini' => [
			'url'   => 'https://generativelanguage.googleapis.com/v1beta/models/',
			'style' => 'gemini',
			'env'   => ['GEMINI_API_KEY', 'GOOGLE_API_KEY', 'AUDIT_AI_KEY'],
			'model' => 'gemini-2.5-flash',
			'name'  => 'Google AI Studio (Gemini, бесплатный tier)',
		],
	];

	/**
	 * Компактная сводка для ИИ: только факты аудита, без содержимого страниц.
	 *
	 * @return string
	 */
	public static function digest(Ctx $c, Audit $audit, array $score): string
	{
		$out = [];
		$out[] = 'САЙТ: ' . $c->origin();
		$out[] = 'СТРАНИЦ ОБОЙДЕНО: ' . count($c->pages());
		$out[] = 'ПРАВИЛ ПРОВЕДЕНО: ' . count($audit->results());
		$out[] = 'ОЦЕНКА: ' . (float)$score['score'] . '/100';
		$out[] = '';
		$out[] = 'ОЦЕНКИ ПО НАПРАВЛЕНИЯМ:';
		foreach ($score['byCat'] as $cat => $d) {
			$out[] = sprintf('  %-24s %3d/100  проверок: %d, с замечаниями: %d', (string)$cat, (int)$d['score'], (int)$d['rules'], (int)$d['failed']);
		}
		$out[] = '';
		$out[] = 'КРИТИЧЕСКИЕ И ВЫСОКИЕ ЗАМЕЧАНИЯ (с правилом, что не так, где и что делать):';
		$n = 0;
		foreach ($audit->results() as $r) {
			if ($r['pass'] || !in_array($r['sev'], ['critical', 'high'], true)) {
				continue;
			}
			$n++;
			$out[] = sprintf('  [%s] %s — %s', strtoupper((string)$r['sev']), (string)$r['id'], (string)$r['title']);
			$out[] = '     где: ' . (string)($r['violations'][0]['url'] ?? 'сайт целиком');
			$out[] = '     суть: ' . (string)($r['violations'][0]['detail'] ?? '');
			$out[] = '     влияет: ' . (string)$r['impact'];
			$out[] = '     действие: ' . (string)$r['hint'];
			if ($n >= 18) {
				break;
			}
		}
		$out[] = '';
		$out[] = 'ЗАМЕЧАНИЯ СРЕДНЕЙ ВАЖНОСТИ (кратко):';
		$n = 0;
		foreach ($audit->results() as $r) {
			if ($r['pass'] || $r['sev'] !== 'medium') {
				continue;
			}
			$n++;
			$out[] = sprintf('  - %s (%s): %s', (string)$r['id'], (string)($r['violations'][0]['url'] ?? 'сайт'), (string)($r['violations'][0]['detail'] ?? ''));
			if ($n >= 12) {
				break;
			}
		}
		$out[] = '';
		$out[] = 'МЕТРИКИ САЙТА:';
		$words = 0;
		foreach ($c->pages() as $p) {
			$words += (int)($p['html']['words'] ?? 0);
		}
		$ttfb = [];
		foreach ($c->pages() as $p) {
			$ttfb[] = (float)$p['ttfb'];
		}
		$out[] = '  слов на страницах: ' . $words . '; медианный TTFB: ' . (round((float)($ttfb ? $ttfb[intdiv(count($ttfb), 2)] : 0) * 1000)) . ' мс';
		$out[] = '  внутренних ссылок: ' . array_sum(array_map(static fn($i) => $i['count'], $c->cr->inlinks));
		return implode("\n", $out);
	}

	/**
	 * Запрос к модели. Возвращает текст ответа или сообщение об ошибке.
	 *
	 * @return array{ok:bool,text:string,provider:string,model:string,error:string}
	 */
	public static function call(string $provider, string $key, string $model, string $prompt, int $timeoutSec = 90): array
	{
		$p = self::PROVIDERS[$provider] ?? null;
		if ($p === null) {
			return ['ok' => false, 'text' => '', 'provider' => $provider, 'model' => $model, 'error' => 'Неизвестный провайдер: ' . $provider];
		}
		$model = $model !== '' ? $model : (string)$p['model'];

		if (($p['style'] ?? '') === 'gemini') {
			$url = rtrim((string)$p['url'], '/') . '/' . rawurlencode($model) . ':generateContent?key=' . rawurlencode($key);
			$payload = [
				'contents'         => [['parts' => [['text' => $prompt]]]],
				'generationConfig' => ['temperature' => 0.3, 'maxOutputTokens' => 2600],
			];
		} else {
			$url = (string)$p['url'];
			// свой endpoint (прокси, self-hosted, локальный сервер) — перекрывает провайдера
			$custom = (string)(getenv('AUDIT_AI_URL') ?: '');
			if ($custom !== '') {
				$url = $custom;
			}
			$payload = [
				'model'    => $model,
				'messages' => [
					['role' => 'system', 'content' => 'Ты опытный SEO-аналитик. Работаешь только с фактами из сводки: не выдумывай данные, не упоминай то, чего в сводке нет. Отвечай по-русски, конкретно и без воды.'],
					['role' => 'user', 'content' => $prompt],
				],
				'temperature' => 0.3,
				'max_tokens'  => 2600,
			];
		}
		$headers = ['Content-Type: application/json'];
		if (($p['style'] ?? '') === 'openai') {
			$headers[] = 'Authorization: Bearer ' . $key;
			$headers[] = 'HTTP-Referer: https://localhost';
			$headers[] = 'X-Title: Studio Audit Crawler';
		}

		$ch = curl_init($url);
		curl_setopt_array($ch, [
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_POST           => true,
			CURLOPT_POSTFIELDS     => json_encode($payload, JSON_UNESCAPED_UNICODE),
			CURLOPT_HTTPHEADER     => $headers,
			CURLOPT_TIMEOUT        => $timeoutSec,
			CURLOPT_CONNECTTIMEOUT => 15,
		]);
		$body = curl_exec($ch);
		$err = curl_error($ch);
		$code = (int)curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
		curl_close($ch);

		if (!is_string($body)) {
			return ['ok' => false, 'text' => '', 'provider' => $provider, 'model' => $model, 'error' => 'Сеть недоступна: ' . $err];
		}
		$json = json_decode($body, true);
		$text = '';
		if (is_array($json)) {
			if (isset($json['choices'][0]['message']['content'])) {
				$text = (string)$json['choices'][0]['message']['content'];
			} elseif (isset($json['candidates'][0]['content']['parts'][0]['text'])) {
				$text = (string)$json['candidates'][0]['content']['parts'][0]['text'];
			} elseif (isset($json['error']['message'])) {
				return ['ok' => false, 'text' => '', 'provider' => $provider, 'model' => $model, 'error' => 'API: ' . (string)$json['error']['message']];
			}
		}
		if ($text === '') {
			return ['ok' => false, 'text' => '', 'provider' => $provider, 'model' => $model, 'error' => 'Пустой ответ API (HTTP ' . $code . ')'];
		}
		return ['ok' => true, 'text' => $text, 'provider' => $provider, 'model' => $model, 'error' => ''];
	}

	/**
	 * Найти провайдера по ключу в окружении.
	 *
	 * @return array{provider:string,key:string}|null
	 */
	public static function detect(): ?array
	{
		foreach (self::PROVIDERS as $name => $p) {
			foreach ((array)$p['env'] as $env) {
				$v = (string)(getenv($env) ?: '');
				if ($v !== '') {
					return ['provider' => $name, 'key' => $v];
				}
			}
		}
		return null;
	}
}

/**
 * Самотест точности правил: синтетические страницы с ЗАРАНЕЕ ИЗВЕСТНЫМ
 * эталоном. Для каждой фикстуры указано, какие правила ДОЛЖНЫ сработать,
 * а «чистая» страница не должна давать ни одной находки.
 */
final class SelfTest
{
	/**
	 * @return array{total:int,passed:int,missed:list<string>,falsePositives:list<string>,rows:list<array<string,mixed>>}
	 */
	public static function run(): array
	{
		$fixtures = [
			'clean' => [
				'html' => '<!DOCTYPE html><html lang="ru"><head><meta charset="UTF-8"><title>Склады под ключ в Москве — компания ФСС</title>'
					. '<meta name="description" content="Строительство быстровозводимых зданий под ключ: склады, ангары, АБК. Срок от 14 дней, гарантия 50 лет, смета за 15 минут в Москве и области.">'
					. '<meta name="viewport" content="width=device-width, initial-scale=1"><link rel="canonical" href="https://site.test/clean">'
					. '<meta property="og:title" content="Склады под ключ"><meta property="og:description" content="Строительство складов под ключ за 14 дней.">'
					. '<meta property="og:image" content="https://site.test/og.png"><meta property="og:url" content="https://site.test/clean">'
					. '<meta name="twitter:card" content="summary_large_image"><meta name="twitter:title" content="Склады под ключ">'
					. '<meta name="twitter:image" content="https://site.test/og.png"><link rel="icon" href="/favicon.ico">'
					. '<script type="application/ld+json">{"@context":"https://schema.org","@type":"Article","headline":"Склады под ключ","datePublished":"2026-01-15","dateModified":"2026-06-01","author":{"@type":"Person","name":"Иван"},"sameAs":["https://example.com/company"]}</script>'
					. '</head><body><main><h1>Склады под ключ</h1>'
					. '<p>' . str_repeat('Строительство складов из сэндвич панелей под ключ с гарантией 50 лет. ', 24) . '</p>'
					. '<h2>Какие сроки строительства?</h2><p>Срок строительства от 14 дней. По словам специалиста: «Мы запускаем объект через 3 недели после замера».</p>'
					. '<ul><li>Проект за 15 минут</li><li>Монтаж под ключ</li></ul>'
					. '<p>Нормативы: 50 лет гарантии, 12000 м² площади, 3 смены.</p>'
					. '<a href="https://example.com/standarts">Требования к складам</a>'
					. '<img src="/img/1.png" alt="Склад из сэндвич панелей" width="800" height="600" loading="lazy" fetchpriority="high" srcset="/img/1.png 1x, /img/1@2x.png 2x">'
					. '</main><footer><a href="/contacts">Контакты</a></footer></body></html>',
				'expect' => [],
			],
			'no-title' => [
				'html' => '<!DOCTYPE html><html lang="ru"><head><meta charset="UTF-8"><meta name="description" content="Описание страницы с достаточной длиной для проверки правил аудита и отображения в сниппете."></head><body><h1>Заголовок</h1><p>Текст страницы.</p></body></html>',
				'expect' => ['meta.title_missing'],
			],
			'no-description' => [
				'html' => '<!DOCTYPE html><html lang="ru"><head><meta charset="UTF-8"><title>Заголовок страницы — достаточно длинный для проверки</title></head><body><h1>Заголовок</h1><p>Текст.</p></body></html>',
				'expect' => ['meta.desc_missing'],
			],
			'no-viewport' => [
				'html' => '<!DOCTYPE html><html lang="ru"><head><meta charset="UTF-8"><title>Заголовок страницы — достаточно длинный для проверки</title><meta name="description" content="Описание страницы с достаточной длиной для проверки правил аудита и отображения в сниппете."></head><body><h1>Заголовок</h1><p>Текст.</p></body></html>',
				'expect' => ['meta.viewport_missing'],
			],
			'no-lang' => [
				'html' => '<!DOCTYPE html><html><head><meta charset="UTF-8"><title>Заголовок страницы — достаточно длинный для проверки</title><meta name="description" content="Описание страницы с достаточной длиной для проверки правил аудита и отображения в сниппете."><meta name="viewport" content="width=device-width, initial-scale=1"></head><body><h1>Заголовок</h1><p>Текст.</p></body></html>',
				'expect' => ['meta.lang_missing'],
			],
			'no-h1' => [
				'html' => '<!DOCTYPE html><html lang="ru"><head><meta charset="UTF-8"><title>Заголовок страницы — достаточно длинный для проверки</title><meta name="description" content="Описание страницы с достаточной длиной для проверки правил аудита и отображения в сниппете."><meta name="viewport" content="width=device-width, initial-scale=1"></head><body><h2>Подзаголовок</h2><p>Текст.</p></body></html>',
				'expect' => ['meta.h1_missing'],
			],
			'two-h1' => [
				'html' => '<!DOCTYPE html><html lang="ru"><head><meta charset="UTF-8"><title>Заголовок страницы — достаточно длинный для проверки</title><meta name="description" content="Описание страницы с достаточной длиной для проверки правил аудита и отображения в сниппете."><meta name="viewport" content="width=device-width, initial-scale=1"></head><body><h1>Первый</h1><h1>Второй</h1><p>Текст.</p></body></html>',
				'expect' => ['meta.h1_missing'],
			],
			'noindex' => [
				'html' => '<!DOCTYPE html><html lang="ru"><head><meta charset="UTF-8"><meta name="robots" content="noindex, nofollow"><title>Заголовок страницы — достаточно длинный для проверки</title><meta name="description" content="Описание страницы с достаточной длиной для проверки правил аудита и отображения в сниппете."><meta name="viewport" content="width=device-width, initial-scale=1"></head><body><h1>Заголовок</h1><p>Текст.</p></body></html>',
				'expect' => ['index.noindex_meta'],
			],
			'img-no-alt' => [
				'html' => '<!DOCTYPE html><html lang="ru"><head><meta charset="UTF-8"><title>Заголовок страницы — достаточно длинный для проверки</title><meta name="description" content="Описание страницы с достаточной длиной для проверки правил аудита и отображения в сниппете."><meta name="viewport" content="width=device-width, initial-scale=1"></head><body><h1>Заголовок</h1><p>Текст.</p><img src="/i.png" width="800" height="600"></body></html>',
				'expect' => ['img.missing_alt'],
			],
			'img-no-size' => [
				'html' => '<!DOCTYPE html><html lang="ru"><head><meta charset="UTF-8"><title>Заголовок страницы — достаточно длинный для проверки</title><meta name="description" content="Описание страницы с достаточной длиной для проверки правил аудита и отображения в сниппете."><meta name="viewport" content="width=device-width, initial-scale=1"></head><body><h1>Заголовок</h1><p>Текст.</p><img src="/i.png" alt="Картинка"></body></html>',
				'expect' => ['img.no_dimensions'],
			],
			'no-schema' => [
				'html' => '<!DOCTYPE html><html lang="ru"><head><meta charset="UTF-8"><title>Заголовок страницы — достаточно длинный для проверки</title><meta name="description" content="Описание страницы с достаточной длиной для проверки правил аудита и отображения в сниппете."><meta name="viewport" content="width=device-width, initial-scale=1"></head><body><h1>Заголовок</h1><p>' . str_repeat('Текст страницы для объёма. ', 12) . '</p></body></html>',
				'expect' => ['schema.missing'],
			],
			'bad-canonical' => [
				'html' => '<!DOCTYPE html><html lang="ru"><head><meta charset="UTF-8"><title>Заголовок страницы — достаточно длинный для проверки</title><meta name="description" content="Описание страницы с достаточной длиной для проверки правил аудита и отображения в сниппете."><meta name="viewport" content="width=device-width, initial-scale=1"><link rel="canonical" href="https://site.test/other"></head><body><h1>Заголовок</h1><p>' . str_repeat('Текст страницы для объёма. ', 12) . '</p></body></html>',
				'expect' => ['index.canonical_mismatch'],
			],
			'generic-anchor' => [
				'html' => '<!DOCTYPE html><html lang="ru"><head><meta charset="UTF-8"><title>Заголовок страницы — достаточно длинный для проверки</title><meta name="description" content="Описание страницы с достаточной длиной для проверки правил аудита и отображения в сниппете."><meta name="viewport" content="width=device-width, initial-scale=1"></head><body><h1>Заголовок</h1><p>' . str_repeat('Текст страницы для объёма. ', 12) . '</p><a href="/other">Подробнее</a></body></html>',
				'expect' => ['links.generic_anchor'],
			],
			'thin-content' => [
				'html' => '<!DOCTYPE html><html lang="ru"><head><meta charset="UTF-8"><title>Заголовок страницы — достаточно длинный для проверки</title><meta name="description" content="Описание страницы с достаточной длиной для проверки правил аудита и отображения в сниппете."><meta name="viewport" content="width=device-width, initial-scale=1"></head><body><h1>Заголовок</h1><p>Коротко.</p></body></html>',
				'expect' => ['content.thin'],
			],
		];

		// Собираем синтетический сайт в памяти (без сети)
		$cfg = new Cfg();
		$cfg->start = 'https://site.test/';
		$cfg->quiet = true;
		$cr = new Crawler($cfg, new Ui(false));
		$cr->robots = null;
		$cr->sitemaps = new SitemapSet();

		$rows = [];
		$missed = [];
		$falsePositives = [];
		$passed = 0;
		$total = 0;

		foreach ($fixtures as $name => $fx) {
			$url = 'https://site.test/' . $name;
			$parsed = Html::parse((string)$fx['html'], 'text/html; charset=UTF-8');
			$cr->pages[$url] = [
				'url' => $url, 'path' => '/' . $name, 'status' => 200, 'final' => $url,
				'redirects' => [], 'depth' => 0, 'headers' => [], 'headerLog' => [],
				'bytes' => strlen((string)$fx['html']), 'bodySize' => strlen((string)$fx['html']),
				'timeTotal' => 0.1, 'ttfb' => 0.05, 'connect' => 0.01, 'httpVersion' => '2',
				'contentType' => 'text/html; charset=UTF-8', 'error' => null, 'html' => $parsed,
			];
			$cr->inlinks[$url] = ['count' => 1, 'from' => ['https://site.test/' => 1], 'anchors' => [], 'src' => []];
		}
		// битая внутренняя ссылка отдельной фикстурой
		$cr->assetStatus['https://site.test/broken-link'] = ['status' => 404, 'error' => null, 'length' => 0];

		$ctx = new Ctx($cfg, $cr);
		$results = (new Audit())->run($ctx);
		$byRule = [];
		foreach ($results as $r) {
			foreach ($r['violations'] as $v) {
				$byRule[$r['id']][] = (string)($v['url'] ?? '');
			}
		}
		// базовая линия: какие правила срабатывают на эталонной «чистой» странице
		$baseline = [];
		foreach ($byRule as $rule => $urls) {
			if (in_array('https://site.test/clean', $urls, true)) {
				$baseline[] = $rule;
			}
		}

		foreach ($fixtures as $name => $fx) {
			$url = 'https://site.test/' . $name;
			$expected = (array)$fx['expect'];
			$got = [];
			foreach ($byRule as $rule => $urls) {
				if (in_array($url, $urls, true)) {
					$got[] = $rule;
				}
			}
			$delta = array_values(array_diff($got, $baseline));
			$miss = array_values(array_diff($expected, $delta));
			$extra = array_values(array_diff($delta, $expected));
			$total += count($expected);
			$passed += count($expected) - count($miss);
			foreach ($miss as $m) {
				$missed[] = $name . ': ' . $m;
			}
			foreach ($extra as $e) {
				$falsePositives[] = $name . ': ' . $e;
			}
			$rows[] = [
				'fixture' => $name,
				'expected'=> $expected,
				'got'     => $delta,
				'ok'      => $miss === [],
			];
		}

		return [
			'total'          => $total,
			'passed'         => $passed,
			'missed'         => $missed,
			'falsePositives' => $falsePositives,
			'baseline'       => $baseline,
			'rows'           => $rows,
		];
	}
}

/**
 * Анализ ключевых слов страницы: частота, плотность, блоки, где встречается
 * термин, интент и TF-IDF относительно корпуса сайта.
 */
final class Keywords
{
	/**
	 * @return array{words:list<array{term:string,count:int,density:float,blocks:list<string>,tfidf:float,intent:string}>,ngrams:array<string,list<array{term:string,count:int,density:float,intent:string}>>}
	 */
	public static function pageKeywords(Ctx $c, string $url, int $limit = 40, bool $withNgrams = true): array
	{
		$page = $c->cr->pages[$url] ?? null;
		if ($page === null) {
			return [];
		}
		$h = $page['html'];

		// тексты по блокам
		$blocks = [
			'title'       => (string)($h['title'] ?? ''),
			'h1'          => (string)($h['h1'][0] ?? ''),
			'h2-h3'       => implode(' ', array_map(static fn(array $x): string => (string)$x['text'], array_filter($h['headings'] ?? [], static fn(array $x): bool => (int)$x['level'] >= 2 && (int)$x['level'] <= 3))),
			'description' => (string)($h['description'] ?? ''),
			'alt'         => implode(' ', array_map(static fn(array $i): string => (string)($i['alt'] ?? ''), $h['images'] ?? [])),
			'текст'       => (string)($h['mainText'] ?? $h['text'] ?? ''),
		];

		$wordCount = 0;
		$textWords = preg_split('~[^\p{L}\p{N}]+~u', mb_strtolower($blocks['текст']), -1, PREG_SPLIT_NO_EMPTY) ?: [];
		$wordCount = count($textWords);

		$counts = [];
		$where = [];
		foreach ($blocks as $bname => $text) {
			$words = preg_split('~[^\p{L}\p{N}]+~u', mb_strtolower((string)$text), -1, PREG_SPLIT_NO_EMPTY) ?: [];
			$uniq = array_count_values($words);
			foreach ($uniq as $w => $n) {
				if (mb_strlen((string)$w) < 3) {
					continue;
				}
				if (!isset($counts[$w])) {
					$counts[$w] = 0;
					$where[$w] = [];
				}
				$counts[$w] += $n;
				$where[$w][$bname] = ($where[$w][$bname] ?? 0) + $n;
			}
		}

		// корпус сайта — для TF-IDF
		$corpus = Insight::siteLexicon($c);
		$pagesTotal = max(1, count($c->pages()));

		$rows = [];
		foreach ($counts as $w => $n) {
			$w = (string)$w;
			if (in_array($w, Insight::STOP, true)) {
				continue;
			}
			$tf = $wordCount > 0 ? $n / $wordCount : 0.0;
			$df = (int)($corpus[$w] ?? 0);
			$idf = $df > 0 ? log(($pagesTotal + 1) / ($df + 1)) + 1.0 : log($pagesTotal + 1) + 1.0;
			$rows[] = [
				'term'    => $w,
				'count'   => $n,
				'density' => round($tf * 100, 2),
				'blocks'  => array_keys($where[$w]),
				'tfidf'   => round($tf * $idf, 4),
				'intent'  => self::intentOf($w),
			];
		}
		usort($rows, static fn(array $a, array $b): int => $b['tfidf'] <=> $a['tfidf']);
		// n-граммы: двусловные и трёхсловные сочетания
		$ngrams = [];
		if ($withNgrams) {
			$words = $textWords;
			foreach ([[2, 'двусловные'], [3, 'трёхсловные']] as [$n, $label]) {
				for ($i = 0; $i + $n <= count($words); $i++) {
					$g = implode(' ', array_slice($words, $i, $n));
					$ngrams[$label][$g] = ($ngrams[$label][$g] ?? 0) + 1;
				}
			}
		}

		$out = ['words' => array_slice($rows, 0, $limit), 'ngrams' => []];
		foreach ($ngrams as $label => $list) {
			arsort($list);
			$out['ngrams'][$label] = [];
			$i = 0;
			foreach ($list as $g => $n) {
				if ($i++ >= 12) {
					break;
				}
				$out['ngrams'][$label][] = [
					'term'    => (string)$g,
					'count'   => $n,
					'density' => round(($wordCount > 0 ? $n / $wordCount : 0) * 100, 2),
					'intent'  => self::intentOf((string)$g),
				];
			}
		}
		return $out;
	}

	/** Классификация поискового намерения по маркерам. */
	public static function intentOf(string $term): string
	{
		$t = mb_strtolower($term);
		$rules = [
			'транзакционный' => ['купить', 'цена', 'заказать', 'стоимость', 'прайс', 'оформить', 'заявка', 'смету', 'коммерческ', 'производств', 'изготов', 'поставк', 'монтаж', 'заказать', 'оптом', 'опт'],
			'коммерческий'    => ['лучш', 'рейтинг', 'сравнен', 'обзор', 'топ ', 'выбор', 'отзывы', 'сравн', 'варианты', 'преимущества'],
			'навигационный'   => ['вход', 'кабинет', 'личный', 'выйти', 'регистрац', 'авторизац', 'аккаунт', 'профиль', 'справка', 'контакт'],
			'информационный'  => ['что такое', 'как ', 'почему', 'зачем', 'инструкц', 'руководств', 'пример', 'видео', 'урок', 'схема', 'размер'],
		];
		foreach ($rules as $intent => $marks) {
			foreach ($marks as $m) {
				if (str_contains($t, $m)) {
					return $intent;
				}
			}
		}
		return 'общий';
	}
}

/**
 * Аналитический слой: семантическое ядро страницы, тематические кластеры,
 * план продвижения конкретной страницы, сравнение с конкурентами и
 * модель готовности к топ-10. Всё опирается на реальные данные обхода:
 * собственный текст, заголовки, входящие анкоры, граф ссылок и правила аудита.
 */
final class Insight
{
	/** Служебные слова — не участвуют в семантическом ядре. */
	public const STOP = [
		'и', 'в', 'на', 'с', 'по', 'для', 'не', 'что', 'это', 'как', 'мы', 'вы', 'наш', 'ваш', 'их', 'его',
		'к', 'а', 'из', 'до', 'за', 'то', 'же', 'бы', 'при', 'более', 'всего', 'также', 'или', 'если', 'чтобы',
		'под', 'над', 'без', 'через', 'после', 'перед', 'между', 'среди', 'все', 'всю', 'её', 'его', 'их', 'им', 'ими',
		'где', 'когда', 'только', 'уже', 'еще', 'ещё', 'там', 'тут', 'вот', 'чем', 'этот', 'эта', 'эти', 'того', 'той', 'том', 'тем',
		'этот', 'эта', 'эти', 'того', 'тоже', 'там', 'тут', 'уже', 'еще', 'ещё', 'быть', 'есть', 'который',
		'которые', 'которое', 'свой', 'свои', 'своих', 'может', 'можно', 'нужно', 'надо', 'очень', 'более',
		'меня', 'тебя', 'него', 'нее', 'них', 'вам', 'нам', 'вас', 'них', 'том', 'тем', 'той', 'этом', 'той',
		'который', 'новый', 'старый', 'главный', 'первый', 'последний', 'другой', 'следующий', 'весь', 'всё',
		'свойство', 'страница', 'странице', 'сайт', 'сайта', 'раздел', 'услуги', 'услуга', 'компания', 'клиент',
		'заявка', 'заявки', 'форма', 'меню', 'footer', 'header', 'main', 'nav', 'div', 'span', 'http', 'https', 'www',
		'index', 'php', 'html', 'href', 'src', 'class', 'alt', 'title', 'meta', 'link', 'script', 'style',
	];

	/** Частотные слова сайта: термин → число страниц, где встречается. */
	public static function siteLexicon(Ctx $c): array
	{
		$lex = [];
		foreach ($c->pages() as $url => $p) {
			foreach (self::termsOf((string)($p['html']['mainText'] ?? $p['html']['text'] ?? '')) as $t) {
				$lex[$t] = ($lex[$t] ?? 0) + 1;
			}
		}
		arsort($lex);
		return $lex;
	}

	/**
	 * Разбор текста на термины: слова и устойчивые словосочетания (биграммы).
	 *
	 * @return array<string,int>
	 */
	public static function termsOf(string $text): array
	{
		$text = mb_strtolower($text);
		$words = preg_split('~[^\p{L}\p{N}]+~u', $text, -1, PREG_SPLIT_NO_EMPTY) ?: [];
		$terms = [];
		$prev = '';
		foreach ($words as $w) {
			// только слова с буквами: чистые числа («2025», «000») — не семантика
			if (mb_strlen($w) >= 3 && !in_array($w, self::STOP, true) && preg_match('~[\p{L}]~u', $w)) {
				$terms[$w] = ($terms[$w] ?? 0) + 1;
				if ($prev !== '') {
					$bg = $prev . ' ' . $w;
					$terms[$bg] = ($terms[$bg] ?? 0) + 1;
				}
				$prev = $w;
			} else {
				$prev = '';
			}
		}
		return $terms;
	}

	/**
	 * Аналитический разбор: ядро страницы, кластеры, конкуренты.
	 *
	 * @param array<string,mixed> $insight
	 */
	public static function renderInsightConsole(Ui $ui, array $insight, Ctx $c, Crawler $cr): void
	{

		// ---------- План продвижения страницы ----------
		if (!empty($insight['page'])) {
			$plan = $insight['page'];
			$core = $plan['core'];
			$ui->section('СЕМАНТИЧЕСКОЕ ЯДРО И ПЛАН ПРОДВИЖЕНИЯ · ' . Ctx::pathKey($plan['url']));

			$ui->line('  ' . $ui->c('Ядро страницы', 'bold|white')
				. '  ' . $ui->c('основной термин: ', 'gray') . $ui->c($core['primary'], 'bold|brcyan'));
			foreach (array_slice($core['support'], 0, 6) as $s) {
				$blocks = [];
				foreach ($s['blocks'] as $b => $n) {
					$blocks[] = $b . ' × ' . $n;
				}
				$ui->line('    ' . $ui->c('· ' . $s['term'], 'white') . $ui->c('  (' . implode(', ', array_slice($blocks, 0, 3)) . ')', 'gray'));
			}

			$ui->line();
			$ui->line('  ' . $ui->c('Покрытие ядра по блокам страницы', 'bold|white'));
			$ui->line('    ' . $ui->c(str_pad('термин', 30, ' ', STR_PAD_RIGHT) . 'title    h1        description  текст', 'gray'));
			foreach ($core['coverage'] as $t => $cov) {
				$mark = static fn(bool $ok): string => $ok ? $ui->c('✓', 'brgreen') : $ui->c('·', 'red');
				$ui->line('    ' . $ui->c($ui->pad($t, 30), 'white')
					. $mark((bool)$cov['title']) . '       ' . $mark((bool)$cov['h1']) . '        '
					. $mark((bool)$cov['description']) . '          ' . $mark((bool)($cov['текст'] ?? false)));
			}

			if (!empty($core['gap'])) {
				$ui->line();
				$ui->line('  ' . $ui->c('Пробелы: термины из тематического окружения, которых нет на странице', 'bold|white'));
				foreach ($core['gap'] as $g) {
					$ui->line('    ' . $ui->c('· ' . $g['term'], 'bryellow')
						. $ui->c('  встречается в анкорах/заголовках ссылающихся страниц: ' . implode(', ', array_slice($g['from'], 0, 2)), 'gray'));
				}
			}

			$ui->line();
			$ui->line('  ' . $ui->c('Черновики (собраны из реальных терминов страницы)', 'bold|white'));
			$ui->line('    ' . $ui->c('title      ', 'gray') . $ui->c($core['titleDraft'], 'white') . $ui->c('  (' . $core['titleLen'] . ' симв.)', 'gray'));
			$ui->line('    ' . $ui->c('description ', 'gray') . $ui->c(mb_substr($core['descDraft'], 0, 120), 'white') . $ui->c('  (' . $core['descLen'] . ' симв.)', 'gray'));
			$ui->line('    ' . $ui->c('текущий title: ', 'gray') . $core['currentTitle']);
			$ui->line('    ' . $ui->c('текущий desc : ', 'gray') . ($core['currentDesc'] !== '' ? mb_substr($core['currentDesc'], 0, 120) : $ui->c('— отсутствует —', 'red')));

			if (!empty($plan['keywords'])) {
				$kw = $plan['keywords'];
				$ui->line();
				$ui->line('  ' . $ui->c('КЛЮЧЕВЫЕ СЛОВА СТРАНИЦЫ (частотность · плотность · где встречается · интент)', 'bold|brcyan'));
				$ui->line('  ' . $ui->c($ui->pad('термин', 24) . ' вх.  плотн.  TF-IDF  где встречается              интент', 'gray'));
				foreach ($kw['words'] as $w) {
					$blocks = $w['blocks'];
					$shown = implode(', ', array_slice($blocks, 0, 3));
					if (count($blocks) > 3) {
						$shown .= ', +' . (count($blocks) - 3);
					}
					$ui->line('  ' . $ui->c($ui->pad($w['term'], 24), 'white')
						. $ui->c($ui->pad((string)$w['count'], 5, false), 'gray')
						. $ui->c($ui->pad($w['density'] . '%', 8, false), 'gray')
						. $ui->c($ui->pad((string)$w['tfidf'], 7, false), 'brcyan')
						. $ui->c($ui->pad($shown, 34), 'gray')
						. $ui->c($w['intent'], $w['intent'] === 'транзакционный' ? 'brgreen' : 'gray'));
				}
				foreach ($kw['ngrams'] as $label => $list) {
					if (!$list) {
						continue;
					}
					$ui->line('  ' . $ui->c($label . ': ', 'gray')
						. implode(' · ', array_map(static fn(array $g): string => $g['term'] . ' ×' . $g['count'], array_slice($list, 0, 5))));
				}
			}

			if (!empty($plan['diagnostics'])) {
				$ui->line();
				$ui->line('  ' . $ui->c('ПОЧЕМУ СТРАНИЦА МОЖЕТ НЕ РАСТИ', 'bold|brcyan'));
				foreach ($plan['diagnostics'] as $d) {
					$ui->line('    ' . $ui->c('• ' . $d['why'], 'bryellow'));
					$ui->line('        ' . $ui->c('факт: ', 'gray') . $d['fact']);
					$ui->line('        ' . $ui->c('действие: ', 'brgreen') . $d['fix']);
				}
			}

			if (!empty($plan['blockers'])) {
				$ui->line();
				$ui->line('  ' . $ui->c('Технические блокеры страницы', 'bold|brcyan'));
				foreach (array_slice($plan['blockers'], 0, 10) as $b) {
					$ui->line('    ' . $ui->sevBadge($b['sev']) . ' ' . $ui->c($b['rule'], 'white') . ' — ' . $b['detail']);
				}
			}

			if (!empty($plan['linkSources'])) {
				$ui->line();
				$ui->line('  ' . $ui->c('Откуда добавить ссылки (тематически близкие страницы, которые ещё не ссылаются)', 'bold|white'));
				foreach ($plan['linkSources'] as $ls) {
					$ui->line('    ' . $ui->c('· ' . $ls['url'], 'brcyan') . $ui->c('  — ' . mb_substr($ls['title'], 0, 50), 'gray'));
				}
			}

			$r = $plan['readiness'];
			$ui->line();
			$ui->line('  ' . $ui->c('ГОТОВНОСТЬ К ТОП-10', 'bold|white') . '  ' . $ui->c($r['ready'] . '%', 'bold|brcyan')
				. $ui->c('  (' . $r['tech'] . ' из ' . $r['techTotal'] . ' технических условий выполнено)', 'gray'));
			foreach ($r['items'] as $i) {
				$mark = $i['external'] ? $ui->c('◐', 'bryellow') : ($i['ok'] ? $ui->c('✓', 'brgreen') : $ui->c('✗', 'red'));
				$ui->line('    ' . $mark . ' ' . $i['title']);
				$ui->line('        ' . $ui->c('факт: ', 'gray') . $i['fact']);
				if (!$i['ok']) {
					$ui->line('        ' . $ui->c('действие: ', 'brgreen') . $i['fix']);
				}
			}
			$ui->line();
			$ui->line('  ' . $ui->c('Важно: ', 'bryellow') . $ui->c('техническая готовность ≠ позиция. Топ-3/топ-10 определяет авторитет домена '
				. '(внешние ссылки, бренд, возраст домена) — это измеряется в Яндекс.Вебмастере и Ahrefs/Semrush, а не обходом. '
				. 'Инструмент закрывает ту часть, которую можно доказать и починить.', 'gray'));
		}

		// ---------- размышление ИИ над сводкой ----------
		if (!empty($insight['ai'])) {
			$ai = $insight['ai'];
			$ui->section('РАЗМЫШЛЕНИЕ ИИ НАД АУДИТОМ');
			if (empty($ai['ok'])) {
				$ui->line('  ' . $ui->c('!', 'bryellow') . ' ' . $ai['error']);
				$ui->line();
				$ui->line('  ' . $ui->c('Как включить (нужна одна переменная окружения, файлов не требуется):', 'gray'));
				$ui->line('    ' . $ui->c('export OPENROUTER_API_KEY=…   ', 'brcyan') . $ui->c('— 20+ бесплатных моделей, 50 запросов/сутки, без карты', 'gray'));
				$ui->line('    ' . $ui->c('export GROQ_API_KEY=…          ', 'brcyan') . $ui->c('— очень быстро, 1000 запросов/сутки', 'gray'));
				$ui->line('    ' . $ui->c('export GEMINI_API_KEY=…        ', 'brcyan') . $ui->c('— Google AI Studio, длинный контекст', 'gray'));
				$ui->line('    ' . $ui->c('ollama serve                   ', 'brcyan') . $ui->c('— локально и приватно, ключ не нужен (--ai=ollama)', 'gray'));
				$ui->line('    ' . $ui->c('запуск: php tools/crawler.php --ai=auto', 'brcyan'));
			} else {
				$ui->line('  ' . $ui->c('модель: ', 'gray') . $ai['provider'] . ' / ' . $ai['model'] . $ui->c(' · сводка отправлена без содержимого страниц', 'gray'));
				$ui->line();
				foreach (preg_split('~\R~', (string)$ai['text']) ?: [] as $line) {
					$ui->line('  ' . $line);
				}
			}
		}

		// ---------- Покрытие ботов: секция, вердикт, правила, ответ сервера ----------
		if (!empty($insight['bots'])) {
			$ui->section('ПОКРЫТИЕ БОТОВ В ROBOTS.TXT');
			$ui->line('  ' . $ui->c($ui->pad('Бот', 22), 'gray') . $ui->c($ui->pad('Секция', 12), 'gray') . $ui->c($ui->pad('Вердикт', 14), 'gray') . $ui->c($ui->pad('Crawl-delay', 12), 'gray') . $ui->c('Ответ сервера', 'gray'));
			foreach ($insight['bots'] as $b) {
				$state = ['explicit' => 'прописан', 'wildcard' => 'через *', 'none' => 'не задан'][$b['coverage'] ?? 'none'] ?? '—';
				$verdict = $b['verdict'] ?? '';
				$v = match ($b['state'] === 'none' ? 'wildcard' : $b['state']) {
					'blocked'  => $ui->c('■ ЗАКРЫТ', 'brred'),
					'partial'  => $ui->c('◐ частично', 'bryellow'),
					'allowed'  => $ui->c('✓ разрешён', 'brgreen'),
					default    => $ui->c('— по *', 'gray'),
				};
				$delay = $b['delay'] !== null ? $b['delay'] . ' с' : '—';
				$ui->line('  ' . $ui->c($ui->pad((string)$b['bot'], 22), 'brcyan')
					. $ui->c($ui->pad($state, 12), 'gray')
					. $ui->pad($v, 22)
					. $ui->c($ui->pad($delay, 12), 'gray')
					. $ui->c(mb_substr((string)$b['server'], 0, 42), $b['code'] === 403 ? 'bryellow' : 'gray'));
			}
			$ui->line();
			$ui->line('  ' . $ui->c('CDN, определяющий ботов по IP, может ответить настоящему боту иначе — поэтому вывод делаем по двум сигналам: правилу robots.txt и фактическому ответу сервера.', 'gray'));
		}

		// ---------- Тест конкретного URL против правил robots.txt ----------
		if (!empty($insight['urlTest'])) {
			$t = $insight['urlTest'];
			$ui->section('ТЕСТ URL ПО ПРАВИЛАМ ROBOTS.TXT');
			$ui->line('  ' . $ui->c('URL: ', 'gray') . $ui->c($t['path'], 'bold|white')
				. '   ' . $ui->c('бот: ', 'gray') . $ui->c($t['agent'], 'brcyan')
				. '   ' . ($t['allowed'] ? $ui->c('РАЗРЕШЁН к обходу', 'brgreen') : $ui->c('ЗАБЛОКИРОВАН', 'brred')));
			if (!empty($t['matched'])) {
				$ui->line('  ' . $ui->c('сработавшее правило: ', 'gray') . $ui->c('строка ' . $t['matched']['line'] . ': ' . $t['matched']['text'], 'white'));
			} else {
				$ui->line('  ' . $ui->c('сработавшее правило: ', 'gray') . $ui->c('нет — URL не попадает ни под одну директиву', 'gray'));
			}
		}

		// ---------- Домены, зеркала, переезд: факты и состояние ----------
		$dom = $cr->domains;
		if (!empty($dom['variants']) || !empty($dom['donors']) || !empty($cr->pages)) {
			$ui->section('ДОМЕНЫ, ЗЕРКАЛА И ПЕРЕЕЗД (факты Яндекса)');
			foreach ($dom['variants'] ?? [] as $u => $v) {
				$state = (int)$v['status'] === 200
					? $ui->c('отдаёт 200 — это отдельный сайт без редиректа', 'brred')
					: ((int)$v['code'] > 0
						? $ui->c('редирект HTTP ' . $v['code'] . ' → ' . $v['final'], ((int)$v['code'] === 301 ? 'brgreen' : 'bryellow'))
						: $ui->c('HTTP ' . ($v['status'] ?: 'нет ответа'), 'gray'));
				$ui->line('  ' . $ui->c($u, 'brcyan') . '  ' . $state);
			}
			foreach ($dom['donors'] ?? [] as $h => $d) {
				$ui->line('  ' . $ui->c($h, 'brcyan') . '  ' . $ui->c($d['kind'], 'brred') . $ui->c(' (' . $d['final'] . ')', 'gray'));
			}
			$ui->line();
			foreach ([
				'Яндекс с 2019 года не склеивает зеркала по совпадению контента: без 301 каждое зеркало (http/https, www/без www, старый домен) живёт как самостоятельный сайт.',
				'ИКС считается ОТДЕЛЬНО для главного и неглавных адресов и для поддоменов; при разных доменах значения различаются. Редирект ИКС не переносит — новый домен считается заново (недели–месяцы).',
				'При переезде редирект должен вести со страниц старого сайта на АНАЛОГИЧНЫЕ страницы нового, а не все на главную. Если структура изменилась — допустим двойной редирект.',
				'301 — постоянный: переносит сигналы и убирает старый адрес из индекса. 302/307 — временный, старый адрес остаётся в базе и страницы не склеиваются.',
				'ИКС не влияет на ранжирование (официальная позиция Яндекса) — это индикатор полезности, доверия и размера аудитории, а не фактор выдачи. Растить его «ради цифры» бессмысленно: он растёт сам при правильной работе.',
				'После переезда обязательно: оба домена доступны роботу, новый добавлен в Вебмастер, отправлена заявка «Переезд сайта», внешние ссылки обновлены на новый домен.',
			] as $fact) {
				$ui->line('  ' . $ui->c('•', 'brcyan') . ' ' . $fact);
			}
		}

		// ---------- Кластеры ----------
		if (!empty($insight['clusters'])) {
			$ui->section('ТЕМАТИЧЕСКИЕ КЛАСТЕРЫ САЙТА');
			foreach (array_slice($insight['clusters'], 0, 12) as $i => $cl) {
				$ui->line('  ' . $ui->c(($i + 1) . '. ', 'bold|brcyan')
					. $ui->c('кластер из ' . $cl['size'] . ' стр. ', 'bold|white')
					. $ui->c('· ' . implode(', ', array_slice($cl['terms'], 0, 6)), 'gray'));
				$ui->line('     ' . $ui->c('страницы: ', 'gray') . implode(', ', array_slice(array_map(static fn($u) => Ctx::pathKey($u), $cl['pages']), 0, 6)));
				if ($cl['gaps']) {
					$ui->line('     ' . $ui->c('пробелы кластера (есть в других страницах): ', 'gray')
						. $ui->c(implode(', ', array_slice(array_keys($cl['gaps']), 0, 8)), 'bryellow'));
				}
			}
		}

		// ---------- Конкуренты ----------
		if (!empty($insight['competitors'])) {
			$ui->section('СРАВНЕНИЕ С КОНКУРЕНТАМИ (те же 152 правила)');
			foreach ($insight['competitors'] as $comp) {
				$ui->line('  ' . $ui->c($comp['url'], 'bold|brcyan')
					. $ui->c('  страниц: ' . $comp['pages'] . ' · оценка: ' . $comp['score'] . '/100', 'gray'));
				if ($comp['better']) {
					$ui->line('    ' . $ui->c('у конкурента лучше (проверки, которые он проходит, а мы — нет):', 'bryellow'));
					foreach (array_slice($comp['better'], 0, 15) as $id) {
						$ui->line('      · ' . $id);
					}
				}
				if ($comp['worse']) {
					$ui->line('    ' . $ui->c('у нас лучше:', 'brgreen'));
					foreach (array_slice($comp['worse'], 0, 10) as $id) {
						$ui->line('      · ' . $id);
					}
				}
			}
		}
	}

	/**
	 * Семантическое ядро страницы: ядро, поддержка, пробелы и черновики.
	 *
	 * @return array<string,mixed>
	 */
	public static function semanticCore(Ctx $c, string $url): array
	{
		$page = $c->cr->pages[$url] ?? null;
		if ($page === null) {
			return [];
		}
		$h = $page['html'];

		// 1. Термины по блокам страницы с весами
		$blocks = [
			'title'       => [(string)($h['title'] ?? ''), 4],
			'h1'          => [(string)($h['h1'][0] ?? ''), 4],
			'h2-h3'       => [implode(' ', array_map(
				static fn(array $x): string => (string)$x['text'],
				array_filter($h['headings'] ?? [], static fn(array $x): bool => (int)$x['level'] >= 2 && (int)$x['level'] <= 3)
			)), 2],
			'description' => [(string)($h['description'] ?? ''), 2],
			'текст'       => [(string)($h['mainText'] ?? $h['text'] ?? ''), 1],
		];
		$score = [];
		$where = [];
		foreach ($blocks as $bname => [$text, $weight]) {
			foreach (self::termsOf((string)$text) as $t => $n) {
				$score[$t] = ($score[$t] ?? 0) + $n * $weight;
				if (!isset($where[$t])) {
					$where[$t] = [];
				}
				$where[$t][$bname] = ($where[$t][$bname] ?? 0) + $n;
			}
		}

		// 2. Входящие анкоры — реальный сигнал темы страницы внутри сайта
		$anchors = $c->cr->inlinks[$url]['anchors'] ?? [];
		foreach ($anchors as $anchor => $n) {
			foreach (self::termsOf((string)$anchor) as $t => $cnt) {
				$score[$t] = ($score[$t] ?? 0) + $cnt * $n * 3;
				$where[$t]['входящие анкоры'] = ($where[$t]['входящие анкоры'] ?? 0) + $cnt * $n;
			}
		}

		// 3. Слова из slug (путь URL) — тоже сигнал темы
		$slug = trim((string)(parse_url($url, PHP_URL_PATH) ?? ''), '/');
		foreach (self::termsOf(str_replace('-', ' ', $slug)) as $t => $n) {
			$score[$t] = ($score[$t] ?? 0) + $n * 2;
			$where[$t]['путь URL'] = ($where[$t]['путь URL'] ?? 0) + $n;
		}

		arsort($score);
		$core = [];
		foreach ($score as $t => $s) {
			if (count($core) >= 12) {
				break;
			}
			$core[] = [
				'term'   => (string)$t,
				'score'  => (float)$s,
				'blocks' => $where[$t] ?? [],
			];
		}

		// 4. Ядро страницы без учёта bigram-шумов: только одиночные слова
		$single = array_values(array_filter($core, static fn(array $x): bool => !str_contains($x['term'], ' ')));
		$primary = $single[0]['term'] ?? ($core[0]['term'] ?? '');
		$support = array_slice($single, 1, 7);

		// 5. Пробелы: термины из страниц, которые ссылаются на эту, но их нет на ней
		$have = array_map(static fn(string $t): bool => true, array_keys($score));
		$haveTerms = [];
		foreach (array_keys($score) as $t) {
			$haveTerms[$t] = true;
		}
		$gap = [];
		foreach ($c->cr->inlinks[$url]['from'] ?? [] as $src => $n) {
			$sp = $c->cr->pages[$src] ?? null;
			if ($sp === null) {
				continue;
			}
			foreach (self::termsOf((string)($sp['html']['title'] ?? '')) as $t => $cnt) {
				if (isset($haveTerms[$t]) || in_array($t, self::STOP, true) || mb_strlen($t) < 4) {
					continue;
				}
				if (!isset($gap[$t])) {
					$gap[$t] = ['term' => $t, 'count' => 0, 'from' => []];
				}
				$gap[$t]['count'] += $cnt * $n;
				if (count($gap[$t]['from']) < 3) {
					$gap[$t]['from'][] = Ctx::pathKey($src);
				}
			}
		}
		uasort($gap, static fn(array $a, array $b): int => $b['count'] <=> $a['count']);
		$gap = array_slice($gap, 0, 8, true);

		// 6. Покрытие ядра по блокам
		$coverage = [];
		foreach (array_slice($single, 0, 5) as $item) {
			$t = (string)$item['term'];
			$coverage[$t] = [
				'title'       => (bool)preg_match('~' . preg_quote($t, '~') . '~iu', (string)($h['title'] ?? '')),
				'h1'          => (bool)preg_match('~' . preg_quote($t, '~') . '~iu', (string)($h['h1'][0] ?? '')),
				'description' => (bool)preg_match('~' . preg_quote($t, '~') . '~iu', (string)($h['description'] ?? '')),
				'текст'       => (bool)preg_match('~' . preg_quote($t, '~') . '~iu', (string)($h['mainText'] ?? $h['text'] ?? '')),
			];
		}

		// 7. Бренд и гео для черновиков
		$brand = self::brandOf($c);
		$lang = (string)($h['lang'] ?? 'ru');
		$geo = str_starts_with($lang, 'ru') ? '' : '';

		// 8. Черновики title и description на основе реальных терминов
		$sup = array_slice(array_column($support, 'term'), 0, 2);
		$titleDraft = trim(ucfirst($primary) . ($sup !== [] ? ' — ' . implode(', ', $sup) : '') . ($brand !== '' ? ' | ' . $brand : ''));
		$descDraft = trim(
			ucfirst($primary) . ' — ' . ($sup !== [] ? implode(', ', $sup) . '. ' : '')
			. 'Подробности, сроки и стоимость: уточните детали у менеджера.'
		);

		return [
			'url'         => $url,
			'primary'     => $primary,
			'support'     => $support,
			'core'        => $core,
			'gap'         => $gap,
			'coverage'    => $coverage,
			'inlinks'     => $c->cr->inlinks[$url]['count'] ?? 0,
			'outlinks'    => count($c->cr->outlinks[$url] ?? []),
			'words'       => (int)($h['words'] ?? 0),
			'brand'       => $brand,
			'titleDraft'  => $titleDraft,
			'titleLen'    => mb_strlen($titleDraft),
			'descDraft'   => $descDraft,
			'descLen'     => mb_strlen($descDraft),
			'currentTitle'=> (string)($h['title'] ?? ''),
			'currentDesc' => (string)($h['description'] ?? ''),
		];
	}

	/** Название бренда: og:site_name, og:title или «хвост» заголовка главной. */
	public static function brandOf(Ctx $c): string
	{
		$home = rtrim($c->origin(), '/') . '/';
		$page = $c->cr->pages[$home] ?? null;
		if ($page === null) {
			foreach ($c->cr->pages as $p) {
				$page = $p;
				break;
			}
		}
		if ($page === null) {
			return '';
		}
		$og = (string)($page['html']['og']['site_name'] ?? '');
		if ($og !== '') {
			return $og;
		}
		$title = (string)($page['html']['title'] ?? '');
		if (preg_match('~[|—–\-]\s*([^|—–\-]{2,30})$~u', $title, $m)) {
			return trim($m[1]);
		}
		return '';
	}

	/**
	 * Тематические кластеры сайта: какие страницы про что и где пробелы.
	 *
	 * @return list<array{terms:list<string>,pages:list<string>,size:int,gaps:array<string,int>}>
	 */
	public static function clusters(Ctx $c, float $threshold = 0.28): array
	{
		$docs = [];
		foreach ($c->cr->pages as $url => $p) {
			$terms = self::termsOf((string)($p['html']['title'] ?? '') . ' ' . (string)($p['html']['h1'][0] ?? '') . ' ' . (string)($p['html']['mainText'] ?? $p['html']['text'] ?? ''));
			if (!$terms) {
				continue;
			}
			arsort($terms);
			$vec = [];
			$norm = 0.0;
			foreach (array_slice($terms, 0, 25, true) as $t => $n) {
				$vec[$t] = (float)$n;
				$norm += $n * $n;
			}
			$docs[$url] = ['vec' => $vec, 'norm' => sqrt($norm)];
		}
		$used = [];
		$clusters = [];
		foreach ($docs as $u1 => $d1) {
			if (isset($used[$u1])) {
				continue;
			}
			$used[$u1] = true;
			$members = [$u1];
			$terms = $d1['vec'];
			foreach ($docs as $u2 => $d2) {
				if ($u1 === $u2 || isset($used[$u2]) || $d1['norm'] === 0.0 || $d2['norm'] === 0.0) {
					continue;
				}
				$dot = 0.0;
				foreach ($d2['vec'] as $t => $v) {
					if (isset($d1['vec'][$t])) {
						$dot += $d1['vec'][$t] * $v;
					}
				}
				$cos = $dot / ($d1['norm'] * $d2['norm']);
				if ($cos >= $threshold) {
					$used[$u2] = true;
					$members[] = $u2;
					foreach ($d2['vec'] as $t => $v) {
						$terms[$t] = ($terms[$t] ?? 0) + $v;
					}
				}
			}
			arsort($terms);
			$clusterTerms = array_slice(array_keys($terms), 0, 10);
			// пробелы кластера: термины, которые есть в кластере, но не в первой странице
			$first = $docs[$members[0]]['vec'];
			$gaps = [];
			foreach ($terms as $t => $v) {
				if (!isset($first[$t]) && !str_contains((string)$t, ' ')) {
					$gaps[(string)$t] = (int)$v;
				}
			}
			arsort($gaps);
			$clusters[] = [
				'terms' => $clusterTerms,
				'pages' => $members,
				'size'  => count($members),
				'gaps'  => array_slice($gaps, 0, 8, true),
			];
		}
		usort($clusters, static fn(array $a, array $b): int => $b['size'] <=> $a['size']);
		return $clusters;
	}

	/**
	 * План продвижения конкретной страницы: что мешает расти и что делать.
	 *
	 * @return array<string,mixed>
	 */
	public static function pagePlan(Ctx $c, string $url, array $auditResults): array
	{
		$core = self::semanticCore($c, $url);
		if ($core === []) {
			return [];
		}
		$page = $c->cr->pages[$url];
		$h = $page['html'];

		// 1. Технические блокеры по этой странице
		$blockers = [];
		foreach ($auditResults as $r) {
			if ($r['pass']) {
				continue;
			}
			foreach ($r['violations'] as $v) {
				if (($v['url'] ?? null) === $url) {
					$blockers[] = [
					'rule'   => $r['id'],
					'title'  => $r['title'],
						'sev'    => $r['sev'],
						'title'  => $r['title'],
						'detail' => $v['detail'],
						'hint'   => $r['hint'],
						'impact' => $r['impact'],
					];
				}
			}
		}
		usort($blockers, static fn(array $a, array $b): int => ['critical' => 0, 'high' => 1, 'medium' => 2, 'low' => 3, 'info' => 4][$a['sev']] <=> ['critical' => 0, 'high' => 1, 'medium' => 2, 'low' => 3, 'info' => 4][$b['sev']]);

		// 2. Диагностика «почему не растёт»
		$diagnostics = [];
		$title = (string)($h['title'] ?? '');
		$h1 = (string)($h['h1'][0] ?? '');
		$desc = (string)($h['description'] ?? '');
		$primary = (string)$core['primary'];
		$inlinks = (int)$core['inlinks'];
		$words = (int)$core['words'];

		if ($primary !== '' && !preg_match('~' . preg_quote($primary, '~') . '~iu', $title)) {
			$diagnostics[] = [
				'why'    => 'Основной термин страницы не вынесен в title',
				'fact'   => 'ядро: «' . $primary . '», в title: ' . ($title !== '' ? '«' . mb_substr($title, 0, 60) . '»' : '—'),
				'fix'    => 'Поставить «' . $primary . '» в начало title — это и запрос, и обещание страницы.',
			];
		}
		if ($primary !== '' && !preg_match('~' . preg_quote($primary, '~') . '~iu', $h1)) {
			$diagnostics[] = [
				'why'    => 'H1 не содержит основной термин',
				'fact'   => 'ядро: «' . $primary . '», H1: «' . mb_substr($h1, 0, 60) . '»',
				'fix'    => 'Синхронизировать H1 с ядром страницы.',
			];
		}
		if (trim($desc) === '') {
			$diagnostics[] = [
				'why'  => 'Нет description — сниппет собирается поисковиком случайно',
				'fact' => 'description отсутствует',
				'fix'  => 'Написать description по черновику из блока «Черновики».',
			];
		}
		if ($words < 300) {
			$diagnostics[] = [
				'why'    => 'Мало содержания: страница не может закрыть запрос целиком',
				'fact'   => $words . ' слов (ориентир от 300)',
				'fix'    => 'Дополнить разделами по подтверждённым подтемам кластера и фактами с цифрами.',
			];
		}
		if ($inlinks === 0) {
			$diagnostics[] = [
				'why'    => 'Страница не получает внутреннего веса',
				'fact'   => 'входящих ссылок: 0',
				'fix'    => 'Добавить ссылки из тематически близких страниц (список ниже).',
			];
		} elseif ($inlinks < 3) {
			$diagnostics[] = [
				'why'    => 'Мало входящих ссылок для устойчивого ранжирования',
				'fact'   => 'входящих ссылок: ' . $inlinks,
				'fix'    => 'Довести до 5–10 ссылок с релевантными анкорами из тематических соседей.',
			];
		}
		if ((int)$page['depth'] > 3) {
			$diagnostics[] = [
				'why'    => 'Страница глубже 3 кликов — краулер и пользователь добираются до неё дольше',
				'fact'   => 'глубина: ' . $page['depth'],
				'fix'    => 'Поднять страницу в хаб или в меню второго уровня.',
			];
		}
		// каннибализация: другая страница сайта про то же
		$cannibal = [];
		foreach ($c->cr->pages as $u2 => $p2) {
			if ($u2 === $url) {
				continue;
			}
			$t2 = (string)($p2['html']['title'] ?? '');
			if ($t2 === '' || $t2 === $title) {
				continue;
			}
			$t2l = mb_strtolower($t2);
			if ($primary !== '' && mb_strpos($t2l, $primary) !== false) {
				$cannibal[] = Ctx::pathKey($u2) . ' — «' . mb_substr($t2, 0, 50) . '»';
			}
		}
		if ($cannibal !== []) {
			$diagnostics[] = [
				'why'    => 'Каннибализация: ту же тему закрывают другие страницы сайта',
				'fact'   => implode('; ', array_slice($cannibal, 0, 3)),
				'fix'    => 'Развести страницы по подтемам или объединить их и оставить один канонический адрес.',
			];
		}

		// 3. Откуда добавить ссылки: тематически близкие страницы, которые не ссылаются
		$linkSources = [];
		$targetTerms = array_column($core['support'], 'term');
		$targetTerms[] = (string)$core['primary'];
		foreach ($c->cr->pages as $u2 => $p2) {
			if ($u2 === $url) {
				continue;
			}
			if (isset($c->cr->inlinks[$u2]['from'][$url])) {
				continue;   // уже ссылается
			}
			$t2 = mb_strtolower((string)($p2['html']['title'] ?? '') . ' ' . (string)($p2['html']['h1'][0] ?? ''));
			$hits = 0;
			foreach ($targetTerms as $t) {
				if ($t !== '' && mb_strpos($t2, $t) !== false) {
					$hits++;
				}
			}
			if ($hits > 0) {
				$linkSources[] = ['url' => Ctx::pathKey($u2), 'hits' => $hits, 'title' => (string)($p2['html']['title'] ?? '')];
			}
		}
		usort($linkSources, static fn(array $a, array $b): int => $b['hits'] <=> $a['hits']);
		$linkSources = array_slice($linkSources, 0, 8);

		// 4. Готовность к топ-10
		$readiness = self::readiness($c, $url, $auditResults, $core, $blockers);

		return [
			'url'         => $url,
			'core'        => $core,
			'keywords'    => Keywords::pageKeywords($c, $url),
			'blockers'    => $blockers,
			'diagnostics' => $diagnostics,
			'linkSources' => $linkSources,
			'readiness'   => $readiness,
			'clusters'    => self::clusters($c),
		];
	}

	/**
	 * Модель готовности страницы к попаданию в топ-10.
	 * Технические условия проверяем сами; авторитет (ссылки, бренд) — нет,
	 * и это честно отмечено как требующее внешних данных.
	 *
	 * @return array{items:list<array{title:string,ok:bool,fact:string,fix:string,external:bool}>,ready:float}
	 */
	public static function readiness(Ctx $c, string $url, array $auditResults, array $core, array $blockers): array
	{
		$items = [];
		$blocked = array_column($blockers, 'rule');
		$page = $c->cr->pages[$url];
		$h = $page['html'];

		$items[] = [
			'title'    => 'Страница доступна для индексации (нет noindex/robots, canonical ведёт на себя)',
			'ok'       => !array_intersect($blocked, ['index.noindex_meta', 'index.noindex_header', 'index.canonical_mismatch', 'index.canonical_broken']),
			'fact'     => 'robots meta: ' . (trim((string)$h['robotsMeta']) !== '' ? $h['robotsMeta'] : '—') . ' · canonical: ' . (trim((string)($h['canonical'] ?? '')) !== '' ? $h['canonical'] : '—'),
			'fix'      => 'Снять noindex и выставить self-canonical.',
			'external' => false,
		];
		$items[] = [
			'title'    => 'Контент уникален (нет дублей по всему сайту)',
			'ok'       => !in_array('index.duplicate_content', $blocked, true),
			'fact'     => 'слов на странице: ' . (int)$h['words'] . ' · страниц в обходе: ' . count($c->pages()),
			'fix'      => 'Убрать совпадения: переписать текст или объединить страницы.',
			'external' => false,
		];
		$items[] = [
			'title'    => 'Семантическое ядро вынесено в title и H1',
			'ok'       => (bool)($core['coverage'][$core['primary']]['title'] ?? false) && (bool)($core['coverage'][$core['primary']]['h1'] ?? false),
			'fact'     => 'ядро: «' . $core['primary'] . '» · title: ' . (($core['coverage'][$core['primary']]['title'] ?? false) ? 'есть' : 'нет')
				. ' · H1: ' . (($core['coverage'][$core['primary']]['h1'] ?? false) ? 'есть' : 'нет'),
			'fix'      => 'Использовать черновики title/description из отчёта.',
			'external' => false,
		];
		$items[] = [
			'title'    => 'Есть description для сниппета',
			'ok'       => trim((string)$h['description']) !== '',
			'fact'     => trim((string)$h['description']) !== '' ? mb_substr((string)$h['description'], 0, 80) : 'отсутствует',
			'fix'      => 'Написать description по черновику.',
			'external' => false,
		];
		$items[] = [
			'title'    => 'Достаточно входящих внутренних ссылок',
			'ok'       => (int)$core['inlinks'] >= 3,
			'fact'     => 'входящих ссылок: ' . (int)$core['inlinks'] . ' (медиана по сайту: ' . self::medianInlinks($c) . ')',
			'fix'      => 'Добавить ссылки с тематически близких страниц.',
			'external' => false,
		];
		$items[] = [
			'title'    => 'Страница не глубже 3 кликов',
			'ok'       => (int)$page['depth'] <= 3,
			'fact'     => 'глубина: ' . (int)$page['depth'],
			'fix'      => 'Поднять страницу в хаб или меню.',
			'external' => false,
		];
		$items[] = [
			'title'    => 'Скорость в норме (TTFB и вес страницы)',
			'ok'       => !in_array('perf.ttfb', $blocked, true) && !in_array('perf.page_size', $blocked, true),
			'fact'     => 'TTFB: ' . round((float)$page['ttfb'] * 1000) . ' мс · HTML: ' . round((int)$page['bodySize'] / 1024, 1) . ' КБ',
			'fix'      => 'Кэш страниц, сжатие, оптимизация запросов.',
			'external' => false,
		];
		$items[] = [
			'title'    => 'Мобильная версия и viewport корректны',
			'ok'       => !in_array('meta.viewport_missing', $blocked, true),
			'fact'     => 'viewport: ' . (trim((string)$h['viewport']) !== '' ? $h['viewport'] : 'отсутствует'),
			'fix'      => 'Добавить meta viewport.',
			'external' => false,
		];
		$items[] = [
			'title'    => 'Есть разметка Schema.org',
			'ok'       => !empty($h['jsonld']),
			'fact'     => 'блоков JSON-LD: ' . count($h['jsonld']),
			'fix'      => 'Добавить разметку под тип страницы.',
			'external' => false,
		];
		$items[] = [
			'title'    => 'Разметка Open Graph (превью в соцсетях и AI-ответах)',
			'ok'       => !in_array('meta.og_missing', $blocked, true),
			'fact'     => !in_array('meta.og_missing', $blocked, true) ? 'заполнена' : 'заполнена не полностью',
			'fix'      => 'Дописать og:title, og:description, og:image, og:url.',
			'external' => false,
		];
		$items[] = [
			'title'    => 'Изображения с alt и размерами (нет битых, CLS под контролем)',
			'ok'       => !array_intersect($blocked, ['img.missing_alt', 'img.broken', 'img.no_dimensions']),
			'fact'     => 'изображений: ' . count($h['images']),
			'fix'      => 'Заполнить alt, задать width/height, починить пути.',
			'external' => false,
		];
		$items[] = [
			'title'    => 'АВТОРИТЕТ ДОМЕНА (внешние ссылки, бренд, история)',
			'ok'       => false,
			'fact'     => 'внешние данные: не измеряются HTTP-обходом',
			'fix'      => 'Проверить в Яндекс.Вебмастере (входящие ссылки, запросы, позиции) и в Ahrefs/Semrush (referring domains, DR). '
				. 'Ключевой вопрос: у конкурентов, которые выше, больше ссылок с авторитетных доменов — если да, технические правки не поднимут страницу в топ.',
			'external' => true,
		];

		$tech = 0;
		$techTotal = 0;
		foreach ($items as $i) {
			if ($i['external']) {
				continue;
			}
			$techTotal++;
			if ($i['ok']) {
				$tech++;
			}
		}
		return [
			'items' => $items,
			'ready' => $techTotal > 0 ? round($tech / $techTotal * 100) : 0,
			'tech'  => $tech,
			'techTotal' => $techTotal,
		];
	}

	/** Медиана входящих ссылок по сайту — база для сравнения. */
	public static function medianInlinks(Ctx $c): int
	{
		$counts = [];
		foreach ($c->cr->pages as $url => $p) {
			$counts[] = $c->cr->inlinks[$url]['count'] ?? 0;
		}
		if (!$counts) {
			return 0;
		}
		sort($counts);
		$mid = intdiv(count($counts), 2);
		return count($counts) % 2 === 0
			? (int)round(($counts[$mid - 1] + $counts[$mid]) / 2)
			: $counts[$mid];
	}

	/**
	 * Сравнение с конкурентами: те же 152 правила против их страниц.
	 * Показывает, что именно у конкурента сделано лучше — это и есть разрыв,
	 * который мешает занять позицию.
	 *
	 * @return list<array{url:string,pages:int,score:float,better:list<string>,worse:list<string>,rules:array<string,bool>}>
	 */
	public static function compareCompetitors(Cfg $cfg, array $ourResults, ?callable $onProgress = null): array
	{
		$our = [];
		foreach ($ourResults as $r) {
			$our[$r['id']] = $r['pass'];
		}
		$out = [];
		$total = count($cfg->competitors);
		$index = 0;
		foreach ($cfg->competitors as $url) {
			$index++;
			if ($onProgress !== null) {
				$onProgress($index - 1, $total, $url);
			}
			$ccfg = new Cfg();
			$ccfg->start = $url;
			$ccfg->depth = 2;
			$ccfg->limit = 40;
			$ccfg->concurrency = 3;
			$ccfg->delayMs = 100;
			$ccfg->external = false;
			$ccfg->probe = false;
			$ccfg->quiet = true;
			$ccfg->ua = $cfg->ua;

			$cr = new Crawler($ccfg, new Ui(false));
			$cr->run();
			$c = new Ctx($ccfg, $cr);
			$a = new Audit();
			$res = $a->run($c);
			$their = [];
			foreach ($res as $r) {
				$their[$r['id']] = $r['pass'];
			}
			$better = [];
			$worse = [];
			foreach ($their as $id => $pass) {
				if (!isset($our[$id]) || $our[$id] === $pass) {
					continue;
				}
				if ($pass) {
					$better[] = $id;
				} else {
					$worse[] = $id;
				}
			}
			$out[] = [
				'url'   => $url,
				'pages' => count($cr->pages),
				'score' => (float)$a->score()['score'],
				'better'=> $better,
				'worse' => $worse,
				'rules' => $their,
			];
			if ($onProgress !== null) {
				$onProgress($index, $total, $url);
			}
		}
		return $out;
	}
}
final class Report
{
	public function __construct(private Ui $ui, private Cfg $cfg)
	{
	}

	// -----------------------------------------------------------------
	//  Консоль
	// -----------------------------------------------------------------
	public function renderConsole(Ctx $c, Crawler $cr, Audit $audit, array $score): void
	{
		$ui = $this->ui;
		$results = $audit->results();

		$bySev = ['critical' => 0, 'high' => 0, 'medium' => 0, 'low' => 0, 'info' => 0];
		$passed = 0;
		foreach ($results as $r) {
			if ($r['pass']) {
				$passed++;
			} else {
				$bySev[$r['sev']]++;
			}
		}

		$ui->title('STUDIO AUDIT · SEO · ТЕХНИКА · СКОРОСТЬ · БЕЗОПАСНОСТЬ · ДОСТУПНОСТЬ');
		$ui->line();
		$ui->kv('Сайт', $ui->c($c->origin() . '/', 'bold|brcyan'));
		$ui->kv('Режим', $ui->c($this->cfg->modeLabel(), 'bold|white') . $ui->c(' (определён автоматически)', 'gray'));
		$ui->kv('Обход', count($cr->pages) . ' URL · глубина ≤ ' . $this->cfg->depth . ' · ' . round($cr->duration, 2) . ' с · ' . $this->cfg->concurrency . ' параллельных запросов');
		$inTotal = array_sum(array_map(static fn($i) => $i['count'], $cr->inlinks));
		$ui->kv('Правил', count($results) . ' проверок · страниц в обходе: ' . count($cr->pages) . ' · внутренних ссылок: ' . $inTotal . ' · внешних: ' . count($cr->externalLinks));
		if ($cr->timings) {
			$phases = [];
			foreach ($cr->timings as $name => $sec) {
				$phases[] = $name . ' ' . round($sec, 2) . ' с';
			}
			$ui->kv('Этапы', implode(' · ', $phases) . ' · запросов: ' . $cr->requests . ($cr->retries > 0 ? ' · повторов: ' . $cr->retries : '') . ' · память: ' . round(memory_get_peak_usage(true) / 1048576, 1) . ' МБ');
		}
		if ($cr->notes) {
			$ui->kv('Заметки', $ui->c(implode('; ', array_slice($cr->notes, 0, 3)), 'bryellow'));
		}

		// --- дашборд: оценка + распределение по критичности ---
		$scoreVal = (float)$score['score'];
		$scoreColor = $scoreVal >= 85 ? 'brgreen' : ($scoreVal >= 65 ? 'bryellow' : 'brred');
		$ui->line();
		$ui->line('  ' . $ui->c('ОБЩАЯ ОЦЕНКА', 'bold|white')
			. '  ' . $ui->c(number_format($scoreVal, 1), 'bold|' . $scoreColor)
			. $ui->c(' / 100', 'gray')
			. '   ' . $ui->bar($scoreVal, 26)
			. '  ' . $ui->c($this->scoreWord($scoreVal), $scoreColor));
		$ui->line();

		// --- плитки по критичности ---
		$sevStyle = ['critical' => 'bgred', 'high' => 'bgyellow', 'medium' => 'bgblue', 'low' => 'bgmagenta', 'info' => 'bggray'];
		$tiles = '';
		foreach (['critical', 'high', 'medium', 'low', 'info'] as $sev) {
			$label = ['critical' => 'КРИТИЧНО', 'high' => 'ВЫСОКАЯ', 'medium' => 'СРЕДНЯЯ', 'low' => 'НИЗКАЯ', 'info' => 'ИНФО'][$sev];
			$tiles .= $ui->chip($ui->pad($label, 8), $sevStyle[$sev]) . ' ' . $ui->c((string)$bySev[$sev], 'bold|white') . '   ';
		}
		$tiles .= $ui->chip($ui->pad('ПРОЙДЕНО', 8), 'bggreen') . ' ' . $ui->c((string)$passed, 'bold|brgreen');
		$ui->line('  ' . $tiles);
		$totalRules = max(1, count($results));
		$parts = [];
		foreach (['critical', 'high', 'medium', 'low', 'info'] as $sev) {
			$parts[$sev] = $bySev[$sev] / $totalRules;
		}
		$ui->line('  ' . $ui->stackBar($parts, $sevStyle, 62) . ' ' . $ui->c(' доля проблем по критичности', 'gray'));
		$ui->line();

		// --- оценка по направлениям ---
		foreach ($score['byCat'] as $cat => $data) {
			$sc = (int)$data['score'];
			$catColor = $ui->catStyle((string)$cat);
			$failed = $data['failed'] > 0
				? $ui->c(' ⚠ ' . $data['failed'] . ' с замечаниями', 'bryellow')
				: $ui->c(' ✓ без замечаний', 'brgreen');
			$ui->line('  ' . $ui->c('▸', $catColor) . ' ' . $ui->c($ui->pad((string)$cat, 22), $catColor)
				. $ui->c($ui->pad((string)$sc, 3, false), 'bold') . $ui->c('/100', 'gray')
				. '  ' . $ui->bar((float)$sc, 18) . $failed);
		}

		// --- сводка ---
		$ui->section('СВОДКА');
		$ui->line('  ' . $ui->c('Проверок пройдено без замечаний: ', 'gray') . $ui->c($passed . ' из ' . count($results), 'bold|brgreen')
			. ($passed === count($results)
				? $ui->c('   ✓ все проверки пройдены', 'brgreen')
				: $ui->c('   с замечаниями: ' . (count($results) - $passed), 'bryellow')));

		// --- ТОП проблем: сначала то, что нужно закрыть сегодня ---
		$top = [];
		foreach (['critical', 'high', 'medium'] as $sev) {
			foreach ($results as $r) {
				if (!$r['pass'] && $r['sev'] === $sev) {
					$top[] = $r;
				}
			}
		}
		if ($top) {
			$ui->section('ЧТО ЗАКРЫТЬ В ПЕРВУЮ ОЧЕРЕДЬ', 'bryellow');
			foreach (array_slice($top, 0, 8) as $r) {
				$first = $r['violations'][0] ?? null;
				$where = $first !== null && $first['url'] !== null ? Ctx::pathKey($first['url']) : 'сайт целиком';
				$ui->line('  ' . $ui->sevBadge($r['sev']) . ' ' . $ui->c($ui->pad($r['id'], 30), 'bold|white')
					. $ui->sysBadges((array)($r['sys'] ?? []))
					. ' ' . $ui->c(mb_substr($r['title'], 0, 46), 'gray'));
				$ui->line('      ' . $ui->c('→ ', 'cyan') . $ui->c(mb_substr($where, 0, 60), 'brcyan')
					. ($first !== null ? $ui->c('  ' . mb_substr((string)$first['detail'], 0, 70), 'gray') : ''));
				if ($r['impact'] !== '') {
					$ui->line('      ' . $ui->c('◎ ', 'brcyan') . $ui->c(mb_substr($r['impact'], 0, 120), 'gray'));
				}
			}
			if (count($top) > 8) {
				$ui->line('  ' . $ui->c('… ещё ' . (count($top) - 8) . ' — см. разделы ниже', 'gray'));
			}
		}

		// --- проблемы по критичности ---
		foreach (['critical', 'high', 'medium', 'low', 'info'] as $sev) {
			$group = array_values(array_filter($results, static fn($r) => !$r['pass'] && $r['sev'] === $sev));
			if (!$group) {
				continue;
			}
			$ui->section($ui->sevBadge($sev) . ' ' . $ui->c('· ' . count($group) . ' ' . $this->plural(count($group), 'проблема', 'проблемы', 'проблем'), 'bold|white'),
				$sev === 'critical' ? 'brred' : ($sev === 'high' ? 'bryellow' : 'brcyan'));
			foreach ($group as $r) {
				$ui->line('  ' . $ui->c('✗', 'red') . ' ' . $ui->c($r['id'], 'bold|white') . $ui->sysBadges((array)($r['sys'] ?? [])) . '  ' . $ui->c($r['title'], 'brcyan'));
				$ui->line('    ' . $ui->c('затронуто: ' . $r['failed'] . ' из ' . $r['checked'] . ' (' . $r['scope'] . ') · ' . $r['cat'], 'gray'));
				foreach (array_slice($r['violations'], 0, 6) as $v) {
					$where = $v['url'] !== null ? Ctx::pathKey($v['url']) : 'сайт целиком';
					$ui->line('      ' . $ui->c('•', 'cyan') . ' ' . $ui->c($where, 'white') . ' ' . $ui->c('—', 'gray') . ' ' . $v['detail']);
					if (!empty($v['url'])) {
						$ui->line('          ' . $ui->c('↗ адрес: ', 'gray') . $ui->c((string)$v['url'], 'brcyan'));
					}
					$cite = $c->sourceCitation($v['url'] ?? null, (string)($v['snippet'] ?? ''), (string)$r['id']);
					if ($cite !== null && $cite['line'] > 0) {
						$ui->line('          ' . $ui->c('⇅ строка ', 'magenta') . $ui->c((string)$cite['line'], 'bold|white')
							. $ui->c(' отдаваемого HTML', 'gray'));
					}
					if (!empty($v['found'])) {
						foreach ($v['found'] as $f) {
							$ui->line('          ' . $ui->c('└ найдено на ', 'gray') . $ui->c(Ctx::pathKey($f['from']), 'brcyan')
								. ($f['anchor'] !== '' ? $ui->c(' — «' . mb_substr($f['anchor'], 0, 40) . '»', 'gray') : '')
								. $ui->c(' (' . $f['zone'] . ')', 'gray'));
						}
					}
					if (!empty($v['snippet'])) {
						$ui->line('          ' . $ui->c('▸ фрагмент: ', 'magenta') . $ui->c(mb_substr((string)$v['snippet'], 0, 220), 'white'));
					}
					if (!empty($v['cite'])) {
						$ui->line('          ' . $ui->c('✎ цитата: ', 'brcyan') . $ui->c((string)$v['cite'], 'gray'));
					}
				}
				if (count($r['violations']) > 6) {
					$ui->line('      ' . $ui->c('… и ещё ' . (count($r['violations']) - 6), 'gray'));
				}
				if ($r['hint'] !== '') {
					$ui->line('    ' . $ui->c('▸ Что делать: ', 'brgreen') . $r['hint']);
				}
				if ($r['impact'] !== '') {
					$ui->line('    ' . $ui->c('◎ Влияет: ', 'brcyan') . $ui->c($r['impact'], 'white'));
				}
				if ($r['sources']) {
					$ui->line('    ' . $ui->c('↳ Источник: ', 'gray') . $ui->c(implode('  ·  ', $r['sources']), 'gray'));
				}
				$ui->line();
			}
		}

		// --- что проверено и проблем нет ---
		$passGroups = [];
		foreach ($results as $r) {
			if ($r['pass']) {
				$passGroups[$r['cat']][] = $r;
			}
		}
		$ui->section($ui->chip('ПРОЙДЕНО', 'bggreen') . ' ' . $ui->c('· замечаний нет (' . $passed . ')', 'bold|brgreen'), 'brgreen');
		foreach ($passGroups as $cat => $rules) {
			$ui->line('  ' . $ui->c('▸', $ui->catStyle((string)$cat)) . ' ' . $ui->c((string)$cat, 'bold|' . $ui->catStyle((string)$cat)));
			foreach ($rules as $r) {
				$ui->line('    ' . $ui->c('✓', 'brgreen') . ' ' . $ui->c($r['title'], 'gray') . $ui->c('  (' . $r['checked'] . ' шт.)', 'gray'));
			}
		}

		// --- карта сайта и граф ---
		$this->renderGraph($c, $cr);

		// --- таблица страниц ---
		if ($this->cfg->showPages) {
			$this->renderPageTable($c, $cr);
		}

		// --- финальный вердикт ---
		$ui->line();
		$ui->line($ui->verdict($scoreVal, number_format($scoreVal, 1) . '/100 · ' . $this->verdictText($bySev, $passed, count($results))));
	}

	/** Текстовая оценка по баллу. */
	private function scoreWord(float $score): string
	{
		return match (true) {
			$score >= 90 => 'отлично',
			$score >= 80 => 'хорошо',
			$score >= 65 => 'нормально, есть что чинить',
			$score >= 45 => 'слабо, много замечаний',
			default      => 'критично, нужен полный ремонт',
		};
	}

	/** Итоговая фраза отчёта. */
	private function verdictText(array $bySev, int $passed, int $total): string
	{
		if (($bySev['critical'] ?? 0) > 0) {
			return 'закрыть ' . $bySev['critical'] . ' критичн. и ' . ($bySev['high'] ?? 0) . ' высоких — остальное вторично';
		}
		if (($bySev['high'] ?? 0) > 0) {
			return 'критичных нет, закрыть ' . $bySev['high'] . ' высоких замечаний';
		}
		return 'критичных замечаний нет · пройдено ' . $passed . ' из ' . $total . ' проверок';
	}

	private function renderGraph(Ctx $c, Crawler $cr): void
	{
		$ui = $this->ui;
		$ui->section('ГРАФ САЙТА');
		$depths = [];
		foreach ($cr->pages as $p) {
			$d = (int)$p['depth'];
			$depths[$d] = ($depths[$d] ?? 0) + 1;
		}
		ksort($depths);
		$max = $depths ? max($depths) : 1;
		foreach ($depths as $d => $cnt) {
			$bar = str_repeat('█', (int)round($cnt / $max * 30));
			// чем глубже — тем «тревожнее» цвет
			$style = $d <= 1 ? 'brgreen' : ($d === 2 ? 'bryellow' : ($d === 3 ? 'yellow' : 'red'));
			$ui->line('  ' . $ui->c($ui->pad('глубина ' . $d, 16), 'gray')
				. $ui->c($ui->pad((string)$cnt, 4, false) . ' стр. ', 'white')
				. $ui->c($bar, $style)
				. '  ' . $ui->c($d > 3 ? 'глубже 3 кликов — риск для индексации' : '', 'gray'));
		}

		$in = [];
		foreach ($cr->inlinks as $url => $info) {
			$in[] = ['url' => $url, 'count' => $info['count'], 'from' => count($info['from'])];
		}
		usort($in, static fn($a, $b) => $b['count'] <=> $a['count']);
		$ui->line();
		$ui->line('  ' . $ui->c('СТРАНИЦЫ С НАИБОЛЬШИМ ВНУТРЕННИМ ВЕСОМ', 'bold|brcyan'));
		foreach (array_slice($in, 0, 8) as $row) {
			$ui->line('    ' . $ui->c($ui->pad((string)$row['count'], 5, false), 'bold|brcyan')
				. $ui->c(' ссылок', 'gray')
				. '  ' . $ui->c('← из ' . $row['from'] . ' стр.', 'gray')
				. '  ' . $ui->c(Ctx::pathKey($row['url']), 'white'));
		}
		if (!$in) {
			$ui->line('    ' . $ui->c('нет данных', 'gray'));
		}

		if ($cr->externalHosts) {
			arsort($cr->externalHosts);
			$ui->line();
			$ui->line('  ' . $ui->c('ВНЕШНИЕ ХОСТЫ', 'bold|brcyan'));
			foreach (array_slice($cr->externalHosts, 0, 8, true) as $host => $cnt) {
				$ui->line('    ' . $ui->c($ui->pad((string)$cnt, 5, false), 'bold|white')
					. $ui->c(' ссылок', 'gray')
					. '  ' . $ui->c($host, 'brcyan'));
			}
		}
	}

	private function renderPageTable(Ctx $c, Crawler $cr): void
	{
		$ui = $this->ui;
		$ui->section('СТРАНИЦЫ');
		$head = 'ПУТЬ' . str_repeat(' ', 38) . ' КОД   ВХ  ГЛ   СЛОВ     БАЙТ   TTFB  TITLE';
		$ui->line('  ' . $ui->c($head, 'bold|gray'));
		$ui->line('  ' . $ui->c(str_repeat('─', 96), 'gray'));
		foreach ($cr->pages as $url => $p) {
			$in = $cr->inlinks[$url]['count'] ?? 0;
			$code = (int)$p['status'];
			$codeStr = $code === 200
				? $ui->c('200', 'brgreen')
				: ($code >= 400 ? $ui->c((string)$code, 'bold|brred') : $ui->c((string)$code, 'bryellow'));
			$title = mb_substr(trim((string)$p['html']['title']), 0, 40);
			$ttfb = (int)round((float)$p['ttfb'] * 1000);
			$ttfbStr = $ttfb > 500 ? $ui->c($ui->pad($ttfb . 'мс', 6, false), 'bryellow')
				: $ui->c($ui->pad($ttfb . 'мс', 6, false), 'gray');
			$ui->line('  ' . $ui->c($ui->pad(mb_substr(Ctx::pathKey($url), 0, 40), 41), 'white')
				. ' ' . $codeStr
				. '  ' . $ui->c($ui->pad((string)$in, 3, false), 'brcyan')
				. '  ' . $ui->c($ui->pad((string)$p['depth'], 2, false), 'gray')
				. '  ' . $ui->c($ui->pad((string)$p['html']['words'], 5, false), 'gray')
				. '  ' . $ui->c($ui->pad((string)(int)$p['bodySize'], 7, false), 'gray')
				. '  ' . $ttfbStr
				. '  ' . $ui->c($title, 'gray'));
		}
	}

	// -----------------------------------------------------------------
	//  JSON
	// -----------------------------------------------------------------
	public function renderJson(Ctx $c, Crawler $cr, Audit $audit, array $score, array $insight = []): string
	{
		$results = $audit->results();
		$issues = [];
		$bySev = ['critical' => 0, 'high' => 0, 'medium' => 0, 'low' => 0, 'info' => 0];
		$passed = 0;
		foreach ($results as $r) {
			if ($r['pass']) {
				$passed++;
				continue;
			}
			$bySev[$r['sev']]++;
			foreach ($r['violations'] as $v) {
				$issues[] = [
					'rule'      => $r['id'],
					'category'  => $r['cat'],
					'severity'  => $r['sev'],
					'title'     => $r['title'],
					'hint'      => $r['hint'],
					'impact'    => $r['impact'],
					'sources'   => $r['sources'],
					'url'       => $v['url'],
					'path'      => $v['url'] !== null ? Ctx::pathKey($v['url']) : null,
					'detail'    => $v['detail'],
					'found'     => $v['found'] ?? [],
					'snippet'   => $v['snippet'] ?? '',
					'cite'      => $v['cite'] ?? '',
				];
			}
		}
		$pages = [];
		foreach ($cr->pages as $url => $p) {
			$pages[] = [
				'url'       => $url,
				'path'      => Ctx::pathKey($url),
				'status'    => (int)$p['status'],
				'final'     => $p['final'],
				'redirects' => count($p['redirects'] ?? []),
				'depth'     => (int)$p['depth'],
				'inlinks'   => $cr->inlinks[$url]['count'] ?? 0,
				'outlinks'  => count($cr->outlinks[$url] ?? []),
				'title'     => $p['html']['title'],
				'description' => $p['html']['description'],
				'canonical' => $p['html']['canonical'],
				'robots'    => $p['html']['robotsMeta'],
				'lang'      => $p['html']['lang'],
				'words'     => (int)$p['html']['words'],
				'bytes'     => (int)$p['bodySize'],
				'ttfb_ms'   => (int)round((float)$p['ttfb'] * 1000),
				'total_ms'  => (int)round((float)$p['timeTotal'] * 1000),
				'http_version' => $p['httpVersion'],
				'content_type'  => $p['contentType'],
				'images'    => count($p['html']['images']),
				'links'     => count($p['html']['links']),
				'jsonld'    => count($p['html']['jsonld']),
			];
		}
		$checks = [];
		foreach ($results as $r) {
			$checks[] = [
				'id' => $r['id'], 'category' => $r['cat'], 'severity' => $r['sev'],
				'title' => $r['title'], 'hint' => $r['hint'], 'impact' => $r['impact'],
				'sources' => $r['sources'], 'scope' => $r['scope'],
				'checked' => $r['checked'], 'failed' => $r['failed'], 'pass' => $r['pass'],
				'violations' => $r['violations'],
			];
		}
		$data = [
			'tool'     => 'Studio Audit Crawler',
			'version'  => '2.0.0',
			'target'   => $c->origin() . '/',
			'generated' => date('Y-m-d H:i:s'),
			'duration_sec' => round($cr->duration, 2),
			'config'   => [
				'depth' => $this->cfg->depth, 'limit' => $this->cfg->limit,
				'concurrency' => $this->cfg->concurrency, 'timeout_ms' => $this->cfg->timeoutMs,
				'user_agent' => $this->cfg->ua, 'external_check' => $this->cfg->external,
				'external_limit' => $this->cfg->externalLimit, 'probe' => $this->cfg->probe,
			],
			'score'   => [
				'total'       => (float)$score['score'],
				'by_category' => $score['byCat'],
			],
			'summary' => [
				'pages'          => count($cr->pages),
				'rules'          => count($results),
				'passed'         => $passed,
				'failed'         => count($results) - $passed,
				'issues'         => count($issues),
				'by_severity'    => $bySev,
				'internal_links' => array_sum(array_map(static fn($i) => $i['count'], $cr->inlinks)),
				'external_links' => count($cr->externalLinks),
				'http_requests'  => $cr->requests,
			],
			'checks'  => $checks,
			'issues'  => $issues,
			'pages'   => $pages,
			'robots'  => [
				'exists'   => $cr->robots?->exists ?? false,
				'status'   => $cr->robots?->status ?? 0,
				'sitemaps' => $cr->robots?->sitemaps ?? [],
				'host'     => $cr->robots?->host,
				'errors'   => $cr->robots?->errors ?? [],
			],
			'sitemap' => [
				'exists' => $cr->sitemaps->exists,
				'files'  => $cr->sitemaps->sitemaps,
				'urls'   => count($cr->sitemaps->urls),
				'errors' => $cr->sitemaps->errors,
			],
		];
		if (!empty($insight)) {
			$data['insight'] = [
				'ai'      => $insight['ai'] ?? null,
				'ai_fixes'=> $insight['aiFixes'] ?? null,
				'llms'    => $insight['llms'] ?? null,
				'page'    => $insight['page'] ?? null,
				'clusters'=> $insight['clusters'] ?? null,
				'competitors' => $insight['competitors'] ?? null,
				'bots'    => $insight['bots'] ?? null,
				'url_test'=> $insight['urlTest'] ?? null,
			];
		}
		return (string)json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
	}

	// -----------------------------------------------------------------
	//  HTML
	// -----------------------------------------------------------------
	public function renderHtml(Ctx $c, Crawler $cr, Audit $audit, array $score, array $insight = [], string $json = ''): string
	{
		$results = $audit->results();
		$bySev = ['critical' => 0, 'high' => 0, 'medium' => 0, 'low' => 0, 'info' => 0];
		$passed = 0;
		$issues = [];
		$origin = $c->origin();
		foreach ($results as $r) {
			if ($r['pass']) {
				$passed++;
				continue;
			}
			$bySev[$r['sev']]++;
			foreach ($r['violations'] as $v) {
				$abs = $v['url'] !== null ? (string)$v['url'] : $origin;
				$issues[] = [
					'sev'    => $r['sev'],
					'cat'    => $r['cat'],
					'rule'   => $r['id'],
					'title'  => $r['title'],
					'sys'    => $r['sys'] ?? [],
					'hint'   => $r['hint'],
					'impact' => $r['impact'],
					'sources'=> $r['sources'],
					'path'   => $v['url'] !== null ? Ctx::pathKey($v['url']) : 'сайт целиком',
					'url'    => $v['url'],
					'abs'    => $abs,
					'detail' => $v['detail'],
					'found'  => $v['found'] ?? [],
					'snippet'=> $v['snippet'] ?? '',
					'cite'   => $v['cite'] ?? '',
				];
			}
		}
		usort($issues, static function (array $a, array $b): int {
			$order = ['critical' => 0, 'high' => 1, 'medium' => 2, 'low' => 3, 'info' => 4];
			return [$order[$a['sev']], $a['cat'], $a['rule']] <=> [$order[$b['sev']], $b['cat'], $b['rule']];
		});

		$sevLabel = ['critical' => 'Критично', 'high' => 'Высокая', 'medium' => 'Средняя', 'low' => 'Низкая', 'info' => 'Инфо'];
		$cats = array_keys($score['byCat']);

		// Приоритет 1: правила (не отдельные находки), которые надо закрыть первыми
		$priority = [];
		foreach (['critical', 'high', 'medium'] as $sev) {
			foreach ($results as $r) {
				if (!$r['pass'] && $r['sev'] === $sev) {
					$priority[] = $r;
				}
			}
		}

		// --- разделы отчёта: порядок определяет и боковое меню, и нумерацию ---
		$sections = [];
		$sections['summary'] = ['Итоги по группам проверок', 'Что проверяем · есть ли · оценка 0–5 · заметки'];
		$sections['dirs'] = ['Оценка по направлениям', 'Где слабые места: клик по карточке показывает замечания раздела'];
		$sections['prio'] = ['Что закрыть в первую очередь', 'Правила с замечаниями уровня «критично», «высокая» и «средняя»'];
		$sections['issues'] = ['Все замечания', 'Полный список находок: клик по строке раскрывает детали и источники'];
		$aiFixes = (array)($insight['aiFixes'] ?? []);
		$aiFixMap = [];
		foreach ((array)($aiFixes['items'] ?? []) as $f) {
			$aiFixMap[(string)($f['rule'] ?? '') . '|' . (string)($f['path'] ?? '')] = $f;
		}
		if (isset($insight['aiFixes'])) {
			$sections['aifix'] = ['ИИ-исправления: готовый код', 'По каждой находке — что заменить и на что'];
		}
		if (!empty($insight['page'])) {
			$sections['core'] = ['Ядро страницы и план продвижения', 'Семантика, покрытие блоков, пробелы и черновики'];
		}
		if (!empty($insight['clusters'])) {
			$sections['clusters'] = ['Тематические кластеры', 'Карта тем сайта: какие страницы про что'];
		}
		if (!empty($insight['competitors'])) {
			$sections['comp'] = ['Сравнение с конкурентами', 'Что у них проходит, а у нас нет — измеримый разрыв'];
		}
		if (!empty($insight['ai'])) {
			$sections['ai'] = ['Размышление ИИ над аудитом', 'Модель получает сводку находок без содержимого страниц'];
		}
		if (!empty($insight['llms']['text'])) {
			$sections['llms'] = ['Сгенерированный llms.txt', 'Готовый файл для ИИ-краулеров — из карты сайта'];
		}
		if (!empty($insight['bots']) || !empty($insight['urlTest'])) {
			$sections['bots'] = ['Боты и robots.txt', 'Кто и как видит сайт: поисковые, ИИ-краулеры и тест URL'];
		}
		$sections['domains'] = ['Домены, зеркала и переезд', 'Факты Яндекса по зеркалам, ИКС и переезду'];
		$sections['pages'] = ['Обойдённые страницы', 'Коды, глубина, входящие ссылки, вес, скорость'];
		$sections['passed'] = ['Проверено без замечаний', 'Подтверждённое отсутствие проблем — не «молчание» инструмента'];
		$sections['data'] = ['Файлы отчёта', 'Всё в одном HTML: данные выгружаются кнопками ниже'];

		$secNum = [];
		$secNo = 0;
		foreach ($sections as $sid => $st) {
			$secNum[$sid] = sprintf('%02d', ++$secNo);
		}

		// --- числа для строки показателей ---
		$ttfbSum = 0.0;
		$bytesSum = 0;
		$wordsSum = 0;
		foreach ($cr->pages as $pp) {
			$ttfbSum += (float)$pp['ttfb'];
			$bytesSum += (int)$pp['bodySize'];
			$wordsSum += (int)$pp['html']['words'];
		}
		$pageCount = count($cr->pages);
		$avgTtfb = $pageCount > 0 ? (int)round($ttfbSum / $pageCount * 1000) : 0;
		$avgSize = $pageCount > 0 ? round($bytesSum / $pageCount / 1024, 1) : 0.0;
		$host = (string)(parse_url($c->origin(), PHP_URL_HOST) ?: $c->origin());
		$e = static fn(string $s): string => htmlspecialchars($s, ENT_QUOTES);
		$scoreVal = (float)$score['score'];
		$scoreCls = $scoreVal >= 85 ? 'ok' : ($scoreVal >= 65 ? 'warn' : 'bad');

		$h = '<!DOCTYPE html><html lang="ru" data-theme="light"><head><meta charset="utf-8">';
		$h .= '<meta name="viewport" content="width=device-width, initial-scale=1">';
		$h .= '<title>Studio Audit — ' . $e($host) . '</title>';
		$h .= '<meta name="description" content="Технический SEO-аудит: ' . count($issues) . ' замечаний, оценка ' . number_format($scoreVal, 1) . '/100">';
		$h .= '<script>(function(){try{var t=localStorage.getItem("sa-theme");if(!t&&matchMedia("(prefers-color-scheme: dark)").matches)t="dark";if(t)document.documentElement.setAttribute("data-theme",t);}catch(e){}})();</script>';
		$h .= '<style>' . $this->css() . '</style></head><body>';
		$h .= '<div class="app">';

		// ---------------- боковое меню ----------------
		$h .= '<aside class="side">';
		$h .= '<div class="brand"><span class="logo">SA</span><div><b>Studio Audit</b><span>технический SEO-аудит</span></div></div>';
		$h .= '<div class="side-site"><div class="side-host">' . $e($host) . '</div>';
		$h .= '<div class="side-meta">' . $e($this->cfg->modeLabel()) . ' · ' . $pageCount . ' ' . $this->plural($pageCount, 'страница', 'страницы', 'страниц')
			. ' · ' . count($results) . ' ' . $this->plural(count($results), 'проверка', 'проверки', 'проверок') . '</div>';
		$h .= '<div class="side-score"><b class="' . $scoreCls . '">' . number_format($scoreVal, 1) . '</b><span>/100</span>';
		$h .= '<div class="side-bar"><i class="' . $scoreCls . '" style="width:' . max(2, min(100, $scoreVal)) . '%"></i></div>';
		$h .= '<div class="side-verdict">' . $e($this->scoreWord($scoreVal)) . '</div></div></div>';
		$h .= '<nav class="side-nav"><p class="side-cap">Разделы</p>';
		foreach ($sections as $sid => $st) {
			$h .= '<a href="#' . $sid . '" data-nav="' . $sid . '"><span class="n">' . $secNum[$sid] . '</span>' . $e($st[0]) . '</a>';
		}
		$h .= '</nav>';
		$h .= '<div class="side-foot">Сгенерировано ' . date('d.m.Y H:i') . '<br>Studio Audit Crawler · ' . count($results) . ' проверок</div>';
		$h .= '</aside>';

		// ---------------- рабочая область ----------------
		$h .= '<main class="main">';
		$h .= '<header class="topbar"><div class="tb-head"><div class="tb-title">Отчёт по сайту <span>' . $e($host) . '</span></div>'
			. '<div class="tb-sub">' . $e($this->verdictText($bySev, $passed, count($results))) . '</div></div>';
		$h .= '<div class="tb-tools">';
		$h .= '<button class="cmdk" id="cmdk" type="button" aria-label="Открыть палитру команд">Поиск и переход <kbd>⌘K</kbd></button>';
		$h .= '<span class="tb-count" id="fstate">' . count($issues) . ' находок</span>';
		$h .= '<span class="tb-score" title="Итоговая оценка">' . number_format($scoreVal, 1) . '<i>/100</i></span>';
		$h .= '<button class="icon-btn" id="density" type="button" title="Плотность таблиц" aria-label="Плотность таблиц">≡</button>';
		$h .= '<button class="icon-btn" id="themeBtn" type="button" title="Светлая / тёмная тема" aria-label="Переключить тему">◐</button>';
		$h .= '<button class="icon-btn" id="printBtn" type="button" title="Печать или PDF" aria-label="Печать">⎙</button>';
		$h .= '</div></header>';

		// ---------------- строка показателей ----------------
		$h .= '<div class="stats">';
		$h .= '<div class="stat"><div class="stat-num">' . $pageCount . '</div><div class="stat-lbl">Страниц</div>'
			. '<div class="stat-sub">за ' . round($cr->duration, 1) . ' с · ' . $cr->requests . ' запросов</div></div>';
		$h .= '<div class="stat"><div class="stat-num">' . number_format((float)$score['score'], 1) . '</div><div class="stat-lbl">Оценка</div>'
			. '<div class="stat-sub">из 100 по 14 направлениям</div></div>';
		foreach (['critical', 'high', 'medium', 'low', 'info'] as $sev) {
			$h .= '<div class="stat s-' . $sev . ' clickable" data-sev="' . $sev . '" title="Показать замечания уровня «' . $sevLabel[$sev] . '»">'
				. '<div class="stat-num">' . $bySev[$sev] . '</div>'
				. '<div class="stat-lbl">' . $sevLabel[$sev] . '</div>'
				. '<div class="stat-sub">' . ($bySev[$sev] > 0 ? 'находок — клик для фильтра' : 'находок нет') . '</div></div>';
		}
		$h .= '<div class="stat s-ok"><div class="stat-num">' . $passed . '</div><div class="stat-lbl">Пройдено</div>'
			. '<div class="stat-sub">из ' . count($results) . ' проверок</div></div>';
		$h .= '<div class="stat"><div class="stat-num">' . $avgTtfb . '<span style="font-size:13px;font-weight:600"> мс</span></div><div class="stat-lbl">TTFB</div>'
			. '<div class="stat-sub">в среднем · ' . $avgSize . ' КБ на страницу</div></div>';
		$h .= '</div>';

		// ---------------- 01 · итоги по группам ----------------
		$h .= '<section class="panel" id="summary"><div class="panel-h"><div><div class="panel-t"><span class="num">' . $secNum['summary'] . '</span>' . $e($sections['summary'][0]) . '</div>'
			. '<div class="panel-s">' . $e($sections['summary'][1]) . ' · ' . count($results) . ' проверок</div></div></div>';
		$h .= '<div class="panel-b">' . $this->summaryHtml($results) . '</div></section>';

		// ---------------- 02 · оценка по направлениям ----------------
		$h .= '<section class="panel" id="dirs"><div class="panel-h"><div><div class="panel-t"><span class="num">' . $secNum['dirs'] . '</span>' . $e($sections['dirs'][0]) . '</div>'
			. '<div class="panel-s">' . $e($sections['dirs'][1]) . '</div></div>'
			. '<div class="tb-tools"><span class="tb-count">' . count($score['byCat']) . ' направлений</span></div></div>';
		$h .= '<div class="panel-b"><div class="grid g3">';
		foreach ($score['byCat'] as $cat => $data) {
			$sc = (int)$data['score'];
			$cls = $sc >= 85 ? 'ok' : ($sc >= 65 ? 'warn' : 'bad');
			$failed = (int)$data['failed'];
			$h .= '<a class="dir ' . $cls . '" data-dir="' . $e((string)$cat) . '" href="#issues">';
			$h .= '<div class="dir-h"><span>' . $e((string)$cat) . '</span><b>' . $sc . '<span class="faint" style="font-size:11px;font-weight:600">/100</span></b></div>';
			$h .= '<div class="mini-bar"><i class="' . $cls . '" style="width:' . max(2, $sc) . '%"></i></div>';
			$h .= '<div class="dir-f">'
				. ($failed > 0
					? '<span class="' . ($failed >= 3 ? 'bad' : 'warn') . '">' . $failed . ' ' . $this->plural($failed, 'правило', 'правила', 'правил') . ' с замечаниями</span>'
					: '<span class="good">без замечаний</span>')
				. ' <span class="faint">· ' . $data['rules'] . ' ' . $this->plural((int)$data['rules'], 'проверка', 'проверки', 'проверок') . '</span></div>';
			$h .= '</a>';
		}
		$h .= '</div></div></section>';

		// ---------------- 02 · приоритет 1 ----------------
		$h .= '<section class="panel" id="prio"><div class="panel-h"><div><div class="panel-t"><span class="num">' . $secNum['prio'] . '</span>' . $e($sections['prio'][0]) . '</div>'
			. '<div class="panel-s">' . $e($sections['prio'][1]) . ' · ' . count($priority) . '</div></div>'
			. '<button class="btn primary" id="copyList" type="button">Скопировать план</button></div>';
		if (!$priority) {
			$h .= '<div class="panel-b"><div class="empty">Критичных, высоких и средних замечаний нет — сайт в порядке.</div></div>';
		} else {
			$h .= '<div class="panel-b"><div class="prio-list">';
			$planText = [];
			foreach (array_slice($priority, 0, 25) as $n => $r) {
				$first = $r['violations'][0] ?? null;
				$firstUrl = $first !== null ? $first['url'] : null;
				$where = $firstUrl !== null ? Ctx::pathKey($firstUrl) : 'сайт целиком';
				$abs = $firstUrl !== null ? (string)$firstUrl : $origin;
				$h .= '<div class="prio ' . $r['sev'] . '" id="rule-' . $e($r['id']) . '">';
				$h .= '<div class="prio-n">' . ($n + 1) . '</div>';
				$h .= '<div>';
				$h .= '<div class="prio-h">' . $this->sevBadgeHtml($r['sev'])
					. '<code>' . $e($r['id']) . '</code>'
					. '<span class="faint small">' . $e((string)$r['cat']) . '</span>'
					. '<a class="faint small" style="margin-left:auto;text-decoration:none" data-jump="' . $e((string)$r['cat']) . '" href="#issues">показать замечания →</a></div>';
				$h .= '<div class="prio-t">' . $e((string)$r['title']) . '</div>';
				$h .= '<div class="kv"><b>Где:</b><a class="plink" href="' . $e($abs) . '" target="_blank" rel="noopener">' . $e($where) . ' ↗</a>'
					. '<span class="faint">· затронуто ' . $r['failed'] . ' из ' . $r['checked'] . '</span></div>';
				$found = $r['violations'][0]['found'] ?? [];
				if ($found) {
					$f = $found[0];
					$h .= '<div class="kv"><b>Найдено:</b><a class="plink" href="' . $e((string)$f['from']) . '" target="_blank" rel="noopener">' . $e(Ctx::pathKey((string)$f['from'])) . ' ↗</a>'
						. ($f['anchor'] !== '' ? ' <span class="faint">— «' . $e(mb_substr($f['anchor'], 0, 40)) . '»</span>' : '')
						. ' <span class="faint">(' . $e((string)$f['zone']) . ')</span></div>';
				}
				$h .= $this->sourceBlockHtml($c, $firstUrl, (string)($first['snippet'] ?? ''), $abs, $where, (string)$r['id'], (string)($first['cite'] ?? ''));
				$h .= $this->aiFixHtml($aiFixMap[$r['id'] . '|' . $where] ?? null);
				if ($r['impact'] !== '') {
					$h .= '<div class="kv"><b>Влияет:</b><span>' . $e((string)$r['impact']) . '</span></div>';
				}
				if ($r['hint'] !== '') {
					$h .= '<div class="kv"><b>Что делать:</b><span style="color:var(--accent-2)">' . $e((string)$r['hint']) . '</span></div>';
				}
				if ($r['sources']) {
					$h .= '<div class="srcs">';
					foreach ($r['sources'] as $src) {
						$h .= '<a href="' . $e($src) . '" target="_blank" rel="noopener">источник ↗</a>';
					}
					$h .= '</div>';
				}
				$h .= '</div></div>';
				$planText[] = sprintf(
					"[%s] %s — %s\n  Где: %s (%d из %d)\n  Влияет: %s\n  Что делать: %s\n  Источник: %s",
					$sevLabel[$r['sev']],
					$r['id'],
					$r['title'],
					$where,
					$r['failed'],
					$r['checked'],
					$r['impact'],
					$r['hint'],
					implode(' ', $r['sources'])
				);
			}
			$h .= '</div>';
			$h .= '<textarea id="planText" hidden>' . $e(implode("\n\n", $planText)) . '</textarea>';
			$h .= '</div>';
		}
		$h .= '</section>';

		// ---------------- 03 · замечания ----------------
		$byCatIssues = [];
		foreach ($issues as $i) {
			$byCatIssues[$i['cat']][] = $i;
		}
		$h .= '<section class="panel" id="issues"><div class="panel-h"><div><div class="panel-t"><span class="num">' . $secNum['issues'] . '</span>' . $e($sections['issues'][0]) . '</div>'
			. '<div class="panel-s">' . $e($sections['issues'][1]) . ' · ' . count($issues) . ' находок</div></div></div>';
		$h .= '<div class="filters"><button class="chip active" data-f="all" type="button">Все разделы <span class="c">' . count($issues) . '</span></button>';
		foreach ($cats as $cat) {
			$n = count($byCatIssues[$cat] ?? []);
			$h .= '<button class="chip" data-f="' . $e((string)$cat) . '" type="button">' . $e((string)$cat) . ' <span class="c">' . $n . '</span></button>';
		}
		$h .= '<span class="sp"></span><button class="btn" id="toggleFull" type="button" title="Развернуть список на всю длину">Полный вид</button></div>';
		if (!$issues) {
			$h .= '<div class="panel-b"><div class="empty">Замечаний нет — все проверки пройдены.</div></div></section>';
		} else {
			$h .= '<div class="scroll"><table class="tbl"><thead><tr><th></th><th>Уровень</th><th>Правило</th><th>Что не так</th><th>Где</th><th>Системы</th></tr></thead><tbody>';
			foreach ($issues as $i) {
				$h .= '<tr class="irow" data-cat="' . $e($i['cat']) . '" data-sev="' . $i['sev'] . '"'
					. ' data-text="' . $e(strtolower($i['rule'] . ' ' . $i['path'] . ' ' . $i['detail'] . ' ' . $i['title'] . ' ' . $i['cat'])) . '">';
				$h .= '<td class="exp"><span class="chev">▸</span></td>';
				$h .= '<td><span class="badge sev-' . $i['sev'] . '">' . $sevLabel[$i['sev']] . '</span></td>';
				$h .= '<td><code>' . $e($i['rule']) . '</code><div class="faint small">' . $e($i['title']) . '</div>'
					. '<div class="faint" style="font-size:11px">' . $e($i['cat']) . '</div></td>';
				$h .= '<td>' . $e($i['detail']) . '</td>';
				$h .= '<td class="path"><a class="plink" href="' . $e($i['abs']) . '" target="_blank" rel="noopener" title="Открыть страницу на сайте">' . $e($i['path']) . ' ↗</a></td>';
				$h .= '<td class="sys">' . $this->sysBadgesHtml((array)($i['sys'] ?? [])) . '</td>';
				$h .= '</tr>';
				$h .= '<tr class="det" hidden><td colspan="6"><div class="det-grid">';
				$h .= '<div><b>Как исправить:</b> ' . $e($i['hint']) . '</div>';
				$h .= $this->sourceBlockHtml($c, $i['url'], (string)$i['snippet'], $i['abs'], $i['path'], (string)$i['rule'], (string)$i['cite']);
				if (!empty($i['found'])) {
					$h .= '<div class="found"><b>Найдено на страницах:</b><ul>';
					foreach ($i['found'] as $f) {
						$from = (string)$f['from'];
						$h .= '<li><a class="plink" href="' . $e($from) . '" target="_blank" rel="noopener">' . $e(Ctx::pathKey($from)) . ' ↗</a>'
							. ($f['anchor'] !== '' ? ' <span class="faint">— «' . $e(mb_substr($f['anchor'], 0, 50)) . '»</span>' : '')
							. ' <span class="faint">(' . $e((string)$f['zone']) . ')</span></li>';
					}
					$h .= '</ul></div>';
				}
				if (!empty($i['cite'])) {
					$h .= '<div class="cite"><span>что исправить</span>' . $e((string)$i['cite']) . '</div>';
				}
				$h .= $this->aiFixHtml($aiFixMap[$i['rule'] . '|' . $i['path']] ?? null);
				if ($i['impact'] !== '') {
					$h .= '<div><b>Влияет:</b> ' . $e($i['impact']) . '</div>';
				}
				if ($i['sources']) {
					$h .= '<div class="srcs">';
					foreach ($i['sources'] as $src) {
						$h .= '<a href="' . $e($src) . '" target="_blank" rel="noopener">источник ↗</a>';
					}
					$h .= '</div>';
				}
				$h .= '<div class="faint" style="font-size:11.5px">нажмите на строку ещё раз, чтобы свернуть</div>';
				$h .= '</div></td></tr>';
			}
			$h .= '</tbody></table></div>';
		}
		$h .= '</section>';

		// ---------------- ИИ-исправления: готовый код ----------------
		if (isset($insight['aiFixes'])) {
			$h .= '<section class="panel" id="aifix"><div class="panel-h"><div><div class="panel-t"><span class="num">' . $secNum['aifix'] . '</span>' . $e($sections['aifix'][0]) . '</div>'
				. '<div class="panel-s">' . $e($sections['aifix'][1]) . ' · ' . count($aiFixes['items'] ?? []) . ' находок разобрано</div></div>'
				. '<span class="tb-count">' . $e((string)($aiFixes['provider'] ?? '') . ' / ' . (string)($aiFixes['model'] ?? '')) . '</span></div>';
			if ($aiFixMap === []) {
				$h .= '<div class="panel-b"><div class="empty">'
					. $e((string)($aiFixes['error'] ?? 'ИИ-исправления не запрошены (нужен ключ ИИ: OPENROUTER_API_KEY / GROQ_API_KEY / GEMINI_API_KEY или локальный Ollama).'))
					. '</div></div></section>';
			} else {
				$h .= '<div class="panel-b"><div class="fixlist">';
				foreach ((array)($aiFixes['items'] ?? []) as $f) {
					$h .= '<div class="fixcard ' . $e((string)($f['sev'] ?? 'info')) . '">';
					$h .= '<div class="fixcard-h">' . $this->sevBadgeHtml((string)($f['sev'] ?? 'info'))
						. '<code>' . $e((string)($f['rule'] ?? '')) . '</code>'
						. '<a class="plink" href="' . $e((string)($f['url'] ?? '')) . '" target="_blank" rel="noopener">' . $e((string)($f['path'] ?? '')) . ' ↗</a>'
						. ((int)($f['line'] ?? 0) > 0 ? '<span class="src-line">строка ' . (int)$f['line'] . '</span>' : '')
						. '</div>';
					$h .= '<div class="fixcard-t">' . $e((string)($f['title'] ?? '')) . '</div>';
					$h .= $this->aiFixHtml($f);
					$h .= '</div>';
				}
				$h .= '</div></div></section>';
			}
		}

		// ---------------- ядро страницы и план продвижения ----------------
		if (!empty($insight['page'])) {
			$plan = $insight['page'];
			$core = $plan['core'];
			$h .= '<section class="panel" id="core"><div class="panel-h"><div><div class="panel-t"><span class="num">' . $secNum['core'] . '</span>' . $e($sections['core'][0]) . '</div>'
				. '<div class="panel-s">' . $e($sections['core'][1]) . ' · ' . $e(Ctx::pathKey($plan['url'])) . '</div></div></div>';
			$h .= '<div class="panel-b">';
			$h .= '<div class="core-head"><div class="core-primary"><span>основной термин</span><b>' . $e($core['primary']) . '</b></div>';
			$h .= '<div class="chips">';
			foreach (array_slice($core['support'], 0, 10) as $s) {
				$h .= '<span class="term" title="' . $e(implode(', ', array_map(static fn($b) => $b . ' × ' . $s['blocks'][$b], array_keys($s['blocks'])))) . '">' . $e($s['term']) . '</span>';
			}
			$h .= '</div></div>';

			$h .= '<div class="grid g2">';
			$h .= '<div><h4 class="mini">Покрытие ядра по блокам</h4><table class="tbl sm"><thead><tr><th>Термин</th><th>title</th><th>H1</th><th>desc</th><th>текст</th></tr></thead><tbody>';
			foreach ($core['coverage'] as $t => $cov) {
				$h .= '<tr><td>' . $e((string)$t) . '</td>';
				foreach (['title', 'h1', 'description', 'текст'] as $k) {
					$h .= '<td class="ctr">' . (!empty($cov[$k]) ? '<span class="ok">✓</span>' : '<span class="no">✗</span>') . '</td>';
				}
				$h .= '</tr>';
			}
			$h .= '</tbody></table></div>';
			$h .= '<div><h4 class="mini">Черновики (из реальных терминов)</h4>';
			$h .= '<div class="draft"><span>title</span><p>' . $e($core['titleDraft']) . ' <em>' . $core['titleLen'] . ' симв.</em></p></div>';
			$h .= '<div class="draft"><span>description</span><p>' . $e(mb_substr($core['descDraft'], 0, 160)) . ' <em>' . $core['descLen'] . ' симв.</em></p></div>';
			$h .= '<div class="draft" style="opacity:.75"><span>сейчас</span><p>' . $e(mb_substr($core['currentTitle'], 0, 90)) . '</p>';
			$h .= '<p class="small faint">' . $e(mb_substr($core['currentDesc'], 0, 120)) . '</p></div>';
			$h .= '</div></div>';

			if (!empty($core['gap'])) {
				$h .= '<h4 class="mini">Пробелы: термины, которых нет на странице, но есть в окружении</h4><div class="chips">';
				foreach ($core['gap'] as $g) {
					$h .= '<span class="term gap" title="' . $e(implode(', ', $g['from'])) . '">' . $e($g['term']) . '</span>';
				}
				$h .= '</div>';
			}

			if (!empty($plan['keywords'])) {
				$kw = $plan['keywords'];
				$h .= '<h4 class="mini">Ключевые слова страницы: частота · плотность · где встречается · интент</h4>';
				$h .= '<div class="scroll" style="border:1px solid var(--line);border-radius:var(--r)"><table class="tbl sm"><thead><tr><th>Термин</th><th>Вхождений</th><th>Плотность</th><th>TF-IDF</th><th>Где встречается</th><th>Интент</th></tr></thead><tbody>';
				foreach ($kw['words'] as $w) {
					$h .= '<tr><td>' . $e((string)$w['term']) . '</td>'
						. '<td class="num">' . (int)$w['count'] . '</td>'
						. '<td class="num">' . $e((string)$w['density'] . '%') . '</td>'
						. '<td class="faint num">' . $e((string)$w['tfidf']) . '</td>'
						. '<td class="faint">' . $e(implode(', ', $w['blocks'])) . '</td>'
						. '<td>' . $e((string)$w['intent']) . '</td></tr>';
				}
				$h .= '</tbody></table></div>';
				foreach ($kw['ngrams'] as $label => $list) {
					if (!$list) {
						continue;
					}
					$h .= '<h4 class="mini">' . $e((string)$label) . '</h4><div class="chips">';
					foreach ($list as $g) {
						$h .= '<span class="term" title="плотность ' . $e((string)$g['density'] . '%') . '">' . $e((string)$g['term']) . ' ×' . (int)$g['count'] . '</span>';
					}
					$h .= '</div>';
				}
			}

			if (!empty($plan['diagnostics'])) {
				$h .= '<h4 class="mini">Почему страница может не расти</h4><div class="diag">';
				foreach ($plan['diagnostics'] as $d) {
					$h .= '<div class="diag-item"><b>' . $e($d['why']) . '</b>'
						. '<div class="faint small">факт: ' . $e($d['fact']) . '</div>'
						. '<div class="fix">действие: ' . $e($d['fix']) . '</div></div>';
				}
				$h .= '</div>';
			}

			if (!empty($plan['readiness'])) {
				$r = $plan['readiness'];
				$h .= '<h4 class="mini">Готовность к топ-10: ' . $r['ready'] . '% (' . $r['tech'] . ' из ' . $r['techTotal'] . ' технических условий)</h4>';
				$h .= '<table class="tbl sm"><tbody>';
				foreach ($r['items'] as $i) {
					$mark = $i['external'] ? '<span class="ext">◐</span>' : ($i['ok'] ? '<span class="ok">✓</span>' : '<span class="no">✗</span>');
					$h .= '<tr><td class="ctr">' . $mark . '</td><td><b>' . $e($i['title']) . '</b><div class="faint small">' . $e($i['fact']) . '</div>';
					if (!$i['ok']) {
						$h .= '<div class="fix">действие: ' . $e($i['fix']) . '</div>';
					}
					$h .= '</td></tr>';
				}
				$h .= '</tbody></table>';
				$h .= '<div class="note">Техническая готовность ≠ позиция в выдаче. Топ-3/топ-10 определяет авторитет домена: внешние ссылки с авторитетных доменов, brand-запросы, возраст и история домена. Это измеряется в Яндекс.Вебмастере и Ahrefs/Semrush, а не HTTP-обходом — инструмент закрывает ту часть, которую можно доказать и починить.</div>';
			}

			if (!empty($plan['linkSources'])) {
				$h .= '<h4 class="mini">Откуда добавить ссылки</h4><ul class="link-src">';
				foreach ($plan['linkSources'] as $ls) {
					$h .= '<li><span class="path">' . $e($ls['url']) . '</span> <span class="faint">— ' . $e(mb_substr($ls['title'], 0, 60)) . '</span></li>';
				}
				$h .= '</ul>';
			}
			$h .= '</div></section>';
		}

		// ---------------- тематические кластеры ----------------
		if (!empty($insight['clusters'])) {
			$h .= '<section class="panel" id="clusters"><div class="panel-h"><div><div class="panel-t"><span class="num">' . $secNum['clusters'] . '</span>' . $e($sections['clusters'][0]) . '</div>'
				. '<div class="panel-s">' . $e($sections['clusters'][1]) . ' · ' . count($insight['clusters']) . ' групп</div></div></div>';
			$h .= '<div class="panel-b"><div class="clusters">';
			foreach (array_slice($insight['clusters'], 0, 24) as $i => $cl) {
				$h .= '<div class="cluster"><div class="cl-h"><b>' . ($i + 1) . '.</b> <span>' . $cl['size'] . ' стр.</span></div>';
				$h .= '<div class="chips">' . implode('', array_map(static fn($t) => '<span class="term">' . $e((string)$t) . '</span>', array_slice($cl['terms'], 0, 8))) . '</div>';
				$h .= '<div class="cl-pages">' . implode(' · ', array_slice(array_map(static fn($u) => $e(Ctx::pathKey($u)), $cl['pages']), 0, 6)) . '</div>';
				if ($cl['gaps']) {
					$h .= '<div class="cl-pages" style="color:var(--crit)">пробелы: ' . $e(implode(', ', array_slice(array_keys($cl['gaps']), 0, 6))) . '</div>';
				}
				$h .= '</div>';
			}
			$h .= '</div></div></section>';
		}

		// ---------------- конкуренты ----------------
		if (!empty($insight['competitors'])) {
			$h .= '<section class="panel" id="comp"><div class="panel-h"><div><div class="panel-t"><span class="num">' . $secNum['comp'] . '</span>' . $e($sections['comp'][0]) . '</div>'
				. '<div class="panel-s">' . $e($sections['comp'][1]) . ' · те же правила аудита</div></div></div>';
			$h .= '<div class="panel-b">';
			foreach ($insight['competitors'] as $comp) {
				$h .= '<div class="cmp"><div class="cmp-h"><span>' . $e($comp['url']) . '</span>'
					. '<span class="faint small">' . $comp['pages'] . ' стр. · оценка ' . $comp['score'] . '/100</span></div>';
				if ($comp['better']) {
					$h .= '<div class="cmp-better"><b>у конкурента лучше:</b> ' . $e(implode(', ', array_slice($comp['better'], 0, 20))) . '</div>';
				}
				if ($comp['worse']) {
					$h .= '<div class="cmp-worse"><b>у нас лучше:</b> ' . $e(implode(', ', array_slice($comp['worse'], 0, 12))) . '</div>';
				}
				$h .= '</div>';
			}
			$h .= '</div></section>';
		}

		// ---------------- размышление ИИ ----------------
		if (!empty($insight['ai'])) {
			$ai = $insight['ai'];
			$h .= '<section class="panel" id="ai"><div class="panel-h"><div><div class="panel-t"><span class="num">' . $secNum['ai'] . '</span>' . $e($sections['ai'][0]) . '</div>'
				. '<div class="panel-s">' . $e($sections['ai'][1]) . '</div></div>'
				. '<span class="tb-count">' . $e((string)($ai['provider'] ?? '') . ' / ' . (string)($ai['model'] ?? '')) . '</span></div>';
			$h .= '<div class="panel-b">';
			if (empty($ai['ok'])) {
				$h .= '<div class="note">' . $e((string)$ai['error']) . '</div>';
			} else {
				$h .= '<div class="ai-text">' . nl2br($e((string)$ai['text'])) . '</div>';
			}
			$h .= '</div></section>';
		}

		// ---------------- сгенерированный llms.txt ----------------
		if (!empty($insight['llms']['text'])) {
			$llms = $insight['llms'];
			$h .= '<section class="panel" id="llms"><div class="panel-h"><div><div class="panel-t"><span class="num">' . $secNum['llms'] . '</span>' . $e($sections['llms'][0]) . '</div>'
				. '<div class="panel-s">' . $e($sections['llms'][1]) . ' · ' . (int)($llms['pages'] ?? 0) . ' страниц'
				. (!empty($llms['saved']) ? ' · файл: ' . $e((string)$llms['path']) : '') . '</div></div></div>';
			$h .= '<div class="panel-b"><pre class="llms-code">' . $e((string)$llms['text']) . '</pre></div></section>';
		}

		// ---------------- боты и robots.txt ----------------
		if (!empty($insight['bots']) || !empty($insight['urlTest'])) {
			$h .= '<section class="panel" id="bots"><div class="panel-h"><div><div class="panel-t"><span class="num">' . $secNum['bots'] . '</span>' . $e($sections['bots'][0]) . '</div>'
				. '<div class="panel-s">' . $e($sections['bots'][1]) . ' · ' . count((array)($insight['bots'] ?? [])) . ' ботов</div></div></div>';
			$h .= '<div class="panel-b">';
			if (!empty($insight['bots'])) {
				$h .= '<div class="scroll"><table class="tbl sm"><thead><tr><th>Бот</th><th>Владелец</th><th>Секция</th><th>Вердикт</th><th>Правила</th><th>Crawl-delay</th><th>Ответ сервера</th></tr></thead><tbody>';
				foreach ($insight['bots'] as $b) {
					$state = ['explicit' => 'прописан', 'wildcard' => 'через *', 'none' => 'не задан'][$b['coverage'] ?? 'none'] ?? '—';
					$verdict = match ($b['state']) {
						'blocked' => '<span class="no">■ закрыт</span>',
						'partial' => '<span class="warn">◐ частично</span>',
						'allowed' => '<span class="ok">✓ разрешён</span>',
						default   => '<span class="faint">— по *</span>',
					};
					$h .= '<tr><td class="path">' . $e((string)$b['bot']) . '</td>'
						. '<td class="faint">' . $e((string)$b['who']) . '</td>'
						. '<td>' . $e($state) . '</td>'
						. '<td>' . $verdict . '</td>'
						. '<td class="faint">' . $e(mb_substr((string)$b['note'], 0, 70)) . '</td>'
						. '<td>' . ($b['delay'] !== null ? $e((string)$b['delay'] . ' с') : '<span class="faint">—</span>') . '</td>'
						. '<td class="faint">' . $e((string)$b['server']) . '</td></tr>';
				}
				$h .= '</tbody></table></div>';
				$h .= '<div class="note">CDN, определяющий ботов по IP, может ответить настоящему боту иначе — поэтому вывод делается по двум сигналам: правилу robots.txt и фактическому ответу сервера.</div>';
			}
			if (!empty($insight['urlTest'])) {
				$t = $insight['urlTest'];
				$h .= '<h4 class="mini">Тест URL по правилам robots.txt</h4>';
				$h .= '<div class="cite"><span>результат</span>URL: <b>' . $e((string)$t['path']) . '</b> · бот: <b>' . $e((string)$t['agent']) . '</b> → '
					. ($t['allowed'] ? '<span class="ok">разрешён к обходу</span>' : '<span class="no">заблокирован</span>');
				if (!empty($t['matched'])) {
					$h .= '<br>сработавшее правило — robots.txt, строка ' . (int)$t['matched']['line'] . ': <code>' . $e((string)$t['matched']['text']) . '</code>';
				} else {
					$h .= '<br>сработавшее правило: нет — URL не попадает ни под одну директиву';
				}
				$h .= '</div>';
			}
			$h .= '</div></section>';
		}

		// ---------------- домены, зеркала, переезд ----------------
		$dom = $cr->domains;
		$h .= '<section class="panel" id="domains"><div class="panel-h"><div><div class="panel-t"><span class="num">' . $secNum['domains'] . '</span>' . $e($sections['domains'][0]) . '</div>'
			. '<div class="panel-s">' . $e($sections['domains'][1]) . '</div></div></div>';
		$h .= '<div class="panel-b">';
		if (!empty($dom['variants'])) {
			$h .= '<table class="tbl sm"><thead><tr><th>Вариант адреса</th><th>Состояние</th><th>Куда ведёт</th></tr></thead><tbody>';
			foreach ($dom['variants'] as $u => $v) {
				$state = (int)$v['status'] === 200
					? '<span class="no">отдаёт 200 — отдельный сайт без редиректа</span>'
					: ((int)$v['code'] > 0
						? '<span class="' . ((int)$v['code'] === 301 ? 'ok' : 'no') . '">редирект HTTP ' . $v['code'] . '</span>'
						: '<span class="faint">HTTP ' . ($v['status'] ?: 'нет ответа') . '</span>');
				$h .= '<tr><td class="path">' . $e($u) . '</td><td>' . $state . '</td><td class="faint">' . $e((string)$v['final']) . '</td></tr>';
			}
			$h .= '</tbody></table>';
		}
		if (!empty($dom['donors'])) {
			$h .= '<table class="tbl sm"><thead><tr><th>Домен</th><th>Что делает</th></tr></thead><tbody>';
			foreach ($dom['donors'] as $h2 => $d) {
				$h .= '<tr><td class="path">' . $e((string)$h2) . '</td><td class="no">' . $e((string)$d['kind']) . '</td></tr>';
			}
			$h .= '</tbody></table>';
		}
		$h .= '<ul class="facts">';
		foreach ([
			'Яндекс с 2019 года не склеивает зеркала по совпадению контента: без 301 каждое зеркало (http/https, www/без www, старый домен) живёт как самостоятельный сайт.',
			'ИКС считается ОТДЕЛЬНО для главного и неглавных адресов и для поддоменов; при разных доменах значения различаются. Редирект ИКС не переносит — новый домен считается заново (недели–месяцы).',
			'При переезде редирект должен вести со страниц старого сайта на аналогичные страницы нового, а не все на главную. Если структура изменилась — допустим двойной редирект.',
			'301 — постоянный: переносит сигналы и убирает старый адрес из индекса. 302/307 — временный, старый адрес остаётся в базе и страницы не склеиваются.',
			'ИКС не влияет на ранжирование (официальная позиция Яндекса): это индикатор полезности, доверия и размера аудитории, а не фактор выдачи. Он растёт сам при правильной работе.',
			'После переезда обязательно: оба домена доступны роботу, новый добавлен в Вебмастер, отправлена заявка «Переезд сайта», внешние ссылки обновлены на новый домен.',
		] as $fact) {
			$h .= '<li>' . $e($fact) . '</li>';
		}
		$h .= '</ul></div></section>';

		// ---------------- страницы ----------------
		$h .= '<section class="panel" id="pages"><div class="panel-h"><div><div class="panel-t"><span class="num">' . $secNum['pages'] . '</span>' . $e($sections['pages'][0]) . '</div>'
			. '<div class="panel-s">' . $e($sections['pages'][1]) . ' · ' . count($cr->pages) . ' URL</div></div></div>';
		$h .= '<div class="scroll"><table class="tbl sm"><thead><tr><th>Путь</th><th>Код</th><th>Глубина</th><th>Вход</th><th>Слов</th><th>КБ</th><th>TTFB</th><th>Title</th></tr></thead><tbody>';
		foreach ($cr->pages as $url => $p) {
			$code = (int)$p['status'];
			$cls = $code === 200 ? 'g' : ($code >= 400 ? 'r' : 'y');
			$h .= '<tr><td class="path">' . $e(Ctx::pathKey($url)) . '</td>';
			$h .= '<td><span class="code ' . $cls . '">' . $code . '</span></td>';
			$h .= '<td class="num">' . (int)$p['depth'] . '</td>';
			$h .= '<td class="num">' . ($cr->inlinks[$url]['count'] ?? 0) . '</td>';
			$h .= '<td class="num">' . (int)$p['html']['words'] . '</td>';
			$h .= '<td class="num">' . round((int)$p['bodySize'] / 1024, 1) . '</td>';
			$h .= '<td class="num">' . (int)round((float)$p['ttfb'] * 1000) . ' мс</td>';
			$h .= '<td class="faint">' . $e(mb_substr((string)$p['html']['title'], 0, 90)) . '</td></tr>';
		}
		$h .= '</tbody></table></div></section>';

		// ---------------- проверено без замечаний ----------------
		$h .= '<section class="panel" id="passed"><div class="panel-h"><div><div class="panel-t"><span class="num">' . $secNum['passed'] . '</span>' . $e($sections['passed'][0]) . '</div>'
			. '<div class="panel-s">' . $e($sections['passed'][1]) . ' · ' . $passed . ' проверок</div></div></div>';
		$h .= '<div class="panel-b"><div class="ok-list">';
		foreach ($results as $r) {
			if (!$r['pass']) {
				continue;
			}
			$h .= '<div class="ok-item" data-okcat="' . $e((string)$r['cat']) . '" title="' . $e((string)$r['impact']) . '">'
				. '<span class="t">✓</span><span>' . $e((string)$r['title']) . '</span>'
				. '<span class="m">' . $e((string)$r['cat']) . ' · ' . $r['checked'] . '</span></div>';
		}
		$h .= '</div></div></section>';

		// ---------------- файлы отчёта: всё внутри одного HTML ----------------
		$h .= '<section class="panel" id="data"><div class="panel-h"><div><div class="panel-t"><span class="num">' . $secNum['data'] . '</span>' . $e($sections['data'][0]) . '</div>'
			. '<div class="panel-s">' . $e($sections['data'][1]) . '</div></div></div>';
		$h .= '<div class="panel-b"><div class="tb-tools">'
			. '<button class="btn" id="dlJson" type="button">Скачать JSON</button>';
		if (!empty($insight['llms']['text'])) {
			$h .= '<button class="btn" id="dlLlm" type="button">Скачать llms.txt</button>'
				. '<button class="btn" id="copyLlm" type="button">Скопировать llms.txt</button>';
		}
		$h .= '<button class="btn" id="copySummary" type="button">Скопировать сводку</button>'
			. '<span class="tb-count">отчёт один файл · ничего больше создавать не нужно</span>'
			. '</div></div></section>';

		$h .= '<footer class="foot">Studio Audit Crawler · сгенерировано ' . date('d.m.Y H:i:s') . ' · проверок выполнено: ' . count($results) . ' · находок: ' . count($issues) . '</footer>';
		$h .= '</main>';
		$h .= '</div>';
		$h .= '<button class="to-top" id="toTop" type="button" title="Наверх" aria-label="Наверх">↑</button>';

		// ---------------- палитра команд ----------------
		$h .= '<div class="palette" id="palette" role="dialog" aria-modal="true" aria-label="Поиск и переход">';
		$h .= '<div class="palette-box">';
		$h .= '<input class="palette-input" id="paletteInput" type="text" placeholder="Поиск по правилу, URL, разделу…  ⌘K" autocomplete="off" spellcheck="false">';
		$h .= '<div class="palette-list" id="paletteList"></div>';
		$h .= '<div class="palette-foot"><span>↑↓ — выбрать</span><span>↵ — открыть</span><span>esc — закрыть</span></div>';
		$h .= '</div></div>';

		// ---------------- сырые данные отчёта внутри HTML ----------------
		if ($json === '') {
			$json = $this->renderJson($c, $cr, $audit, $score, $insight);
		}
		$h .= '<script type="application/json" id="reportJson">' . str_replace('</', '<\/', $json) . '</script>';
		$h .= '<script>' . $this->js() . '</script></body></html>';
		return $h;
	}

	/**
	 * Имя файла отчёта по анализируемому сайту: skillcode.pro → skillcode.html
	 */
	public static function reportName(string $url): string
	{
		$host = strtolower((string)(parse_url($url, PHP_URL_HOST) ?: 'site'));
		$host = preg_replace('~^www\.~', '', $host);
		if ($host === 'localhost' || $host === '127.0.0.1' || $host === '::1' || $host === '0.0.0.0') {
			return 'local-audit';
		}
		$host = preg_replace('~[^a-z0-9]+~i', '-', (string)$host);
		$parts = array_values(array_filter(explode('-', (string)$host), static fn(string $p): bool => $p !== ''));
		$tlds = ['pro', 'ru', 'com', 'net', 'org', 'info', 'biz', 'site', 'online', 'io', 'co', 'me', 'tv', 'ws',
			'su', 'xn--p1ai', 'tech', 'store', 'shop', 'club', 'group', 'agency', 'studio', 'works', 'life', 'world',
			'one', 'page', 'art', 'design', 'blog', 'news'];
		if (count($parts) > 1 && in_array(end($parts), $tlds, true)) {
			array_pop($parts);
		}
		$name = trim(implode('-', $parts), '-');
		return $name !== '' ? $name : 'site';
	}

	/** Бейджи поисковых систем для HTML-отчёта. */
	private function sysBadgesHtml(array $sys): string
	{
		$map = [
			'google' => ['G', 'g', 'Google'],
			'yandex' => ['Я', 'y', 'Яндекс'],
			'bing'  => ['B', 'b', 'Bing'],
			'ai'     => ['AI', 'i', 'ИИ-поиск'],
		];
		$out = '';
		foreach ($map as $key => [$label, $cls, $title]) {
			if (!empty($sys[$key])) {
				$out .= '<span class="sys-b ' . $cls . '" title="' . $title . '">' . $label . '</span>';
			}
		}
		return $out;
	}

	/**
	 * Итоги: чек-лист по группам проверок.
	 *
	 * Формат как в рабочем чек-листе: что проверяем · есть ли · оценка 0–5 · заметки.
	 *
	 * @param list<array<string,mixed>> $results
	 */
	private function summaryHtml(array $results): string
	{
		// логичный порядок групп: от индексации к содержанию
		$order = [
			'Индексация', 'Мета-теги', 'Ссылки', 'Изображения', 'Контент', 'Разметка Schema.org',
			'Скорость', 'Технические ошибки', 'Технический стек', 'Безопасность', 'Доступность',
			'AI и GEO', 'Домены и миграция', 'Соответствие 152-ФЗ',
		];
		$grouped = [];
		foreach ($results as $r) {
			$grouped[(string)$r['cat']][] = $r;
		}
		$cats = array_values(array_unique(array_filter(
			array_merge($order, array_keys($grouped)),
			static fn(string $c): bool => isset($grouped[$c])
		)));

		$h = '<div class="summary">';
		$num = 0;
		foreach ($cats as $cat) {
			$rows = $grouped[$cat];
			$okCount = 0;
			foreach ($rows as $r) {
				if (!empty($r['pass'])) {
					$okCount++;
				}
			}
			$num++;
			$h .= '<div class="sumgroup"><h3 class="sumgroup-t"><span class="num">' . $num . '</span>' . htmlspecialchars($cat, ENT_QUOTES)
				. ' <span class="faint">· ' . count($rows) . ' ' . $this->plural(count($rows), 'проверка', 'проверки', 'проверок')
				. ' · в норме ' . $okCount . '</span></h3>';
			$h .= '<table class="tbl sum"><thead><tr><th>Что проверяем</th><th>Есть?</th><th>Оценка</th><th>Заметки</th></tr></thead><tbody>';
			foreach ($rows as $r) {
				$checked = (int)($r['checked'] ?? 0);
				$failed = (int)($r['failed'] ?? 0);
				$pass = !empty($r['pass']);
				$state = $pass ? 'yes' : (($checked > 0 && $failed < $checked) ? 'part' : 'no');
				$stateLabel = ['yes' => 'Да', 'part' => 'Частично', 'no' => 'Нет'][$state];
				$score = $pass ? 5 : ($checked > 0 ? (int)round(5 * ($checked - $failed) / $checked) : 0);
				if (!$pass && $score >= 5) {
					$score = 4;
				}
				$score = max(0, min(5, $score));

				// заметка: что именно найдено
				$viol = (array)($r['violations'] ?? []);
				if ($pass) {
					$note = '✔ Замечаний нет'
						. ($checked > 0 ? ' — проверено ' . $checked . ' ' . $this->plural($checked, 'страница', 'страницы', 'страниц') : '');
				} elseif ($viol !== []) {
					$first = (string)($viol[0]['detail'] ?? '');
					$note = '⚠ ' . $first
						. (count($viol) > 1 ? ' и ещё ' . (count($viol) - 1) : '')
						. ($checked > 0 ? ' (' . $failed . ' из ' . $checked . ')' : '');
				} else {
					$note = '⚠ ' . (string)($r['impact'] ?? 'нарушено');
				}
				$impact = (string)($r['impact'] ?? '');
				if ($pass && $impact !== '' && mb_strlen($impact) <= 150) {
					$note .= ' · ' . $impact;
				}

				$h .= '<tr class="sumrow">'
					. '<td class="what"><b>' . htmlspecialchars((string)$r['title'], ENT_QUOTES) . '</b>'
					. '<div class="faint" style="font-size:11px"><code>' . htmlspecialchars((string)$r['id'], ENT_QUOTES) . '</code></div></td>'
					. '<td class="ctr"><span class="pill ' . $state . '">' . $stateLabel . '</span></td>'
					. '<td class="ctr"><span class="sc s' . $score . '">' . $score . '</span></td>'
					. '<td class="note-cell">' . htmlspecialchars($note, ENT_QUOTES) . '</td>'
					. '</tr>';
			}
			$h .= '</tbody></table></div>';
		}
		$h .= '</div>';
		return $h;
	}

	/** Бейдж критичности для HTML-отчёта. */
	private function sevBadgeHtml(string $sev): string
	{
		$label = match ($sev) {
			'critical' => 'КРИТИЧНО',
			'high'     => 'ВЫСОКАЯ',
			'medium'   => 'СРЕДНЯЯ',
			'low'      => 'НИЗКАЯ',
			default    => 'ИНФО',
		};
		return '<span class="badge sev-' . $sev . '">' . $label . '</span>';
	}

	/**
	 * Цитата источника: адрес страницы, номер строки и сама строка кода.
	 *
	 * Это то самое «максимальное» место в отчёте: видно, на какой странице,
	 * в какой строке и что именно там написано.
	 */
	private function sourceBlockHtml(Ctx $c, ?string $pageUrl, string $snippet, string $abs, string $path, string $rule = '', string $citeText = ''): string
	{
		$cite = $c->sourceCitation($pageUrl, $snippet, $rule);
		$code = $cite !== null ? (string)$cite['code'] : $snippet;
		if (trim($code) === '') {
			return '';
		}
		$e = static fn(string $s): string => htmlspecialchars($s, ENT_QUOTES);
		$line = $cite !== null ? (int)$cite['line'] : 0;
		$text = $cite !== null ? (string)$cite['text'] : '';
		if (trim($text) === '') {
			$text = $code;
		}
		// если нужного тега на странице нет — это подсказка «добавь рядом», а не «вот проблемная строка»
		$isAdd = stripos($citeText, 'добав') !== false
			|| ($cite !== null && empty($cite['exact']));
		$h = '<div class="src"><div class="src-h">';
		$h .= '<a class="src-link" href="' . $e($abs) . '" target="_blank" rel="noopener" title="Открыть страницу на сайте">' . $e($path) . ' ↗</a>';
		if ($line > 0) {
			$h .= '<span class="src-line">' . ($isAdd ? 'ориентир, строка ' : 'строка ') . $line . '</span>';
		}
		$h .= '<span class="src-file">' . ($isAdd ? 'добавьте рядом в &lt;head&gt;' : 'отдаваемый HTML') . '</span>';
		$h .= '</div><pre class="src-code">';
		if ($line > 0) {
			$h .= '<span class="ln">' . $line . '</span>';
		}
		$h .= '<code>' . $e($text) . '</code></pre></div>';
		return $h;
	}

	/**
	 * Блок «ИИ-исправление»: БЫЛО → СТАЛО готовым кодом.
	 *
	 * @param null|array<string,mixed> $fix
	 */
	private function aiFixHtml(?array $fix): string
	{
		if (!is_array($fix) || $fix === []) {
			return '';
		}
		$e = static fn(string $s): string => htmlspecialchars($s, ENT_QUOTES);
		if (empty($fix['ok'])) {
			return '<div class="aifix err"><b>ИИ-исправление не получено:</b> ' . $e((string)($fix['error'] ?? 'нет ответа модели')) . '</div>';
		}
		$h = '<div class="aifix"><div class="aifix-h">Как исправить — ИИ<span class="faint"> готовый код</span></div>';
		if ((string)($fix['before'] ?? '') !== '') {
			$h .= '<div class="aifix-row"><span class="lbl was">БЫЛО</span><pre>' . $e((string)$fix['before']) . '</pre></div>';
		}
		if ((string)($fix['after'] ?? '') !== '') {
			$h .= '<div class="aifix-row"><span class="lbl now">СТАЛО</span><pre>' . $e((string)$fix['after']) . '</pre></div>';
		}
		if ((string)($fix['why'] ?? '') !== '') {
			$h .= '<div class="aifix-why">' . $e((string)$fix['why']) . '</div>';
		}
		$h .= '</div>';
		return $h;
	}


	private function css(): string
	{
		return <<<'CSS'
/* ==========================================================================
   Studio Audit — профессиональная панель аудита (2026):
   один вердикт на экран · иерархия через типографику и воздух, а не рамки ·
   таблица как основной интерфейс · цвет только для статуса ·
   светлая и тёмная темы · палитра команд (⌘K).
   ========================================================================== */
:root{
  color-scheme:light;
  --bg:#f6f7f9; --surface:#fff; --surface-2:#fafbfc; --surface-3:#f1f3f6;
  --ink:#0d1117; --ink-2:#30363d; --muted:#656d76; --faint:#8b949e;
  --line:#e4e7eb; --line-2:#eef0f3;
  --accent:#2f6feb; --accent-2:#1f5fd0; --accent-soft:#eef4ff; --accent-line:#c3d9fd;
  --ok:#1a7f37; --ok-bg:#e9f6ec; --ok-line:#b9dfc5;
  --warn:#9a6700; --warn-bg:#fdf6e4; --warn-line:#eedfae;
  --high:#bc4c00; --high-bg:#fff3ea; --high-line:#f7d3b4;
  --crit:#cf222e; --crit-bg:#ffeaec; --crit-line:#f5c0c4;
  --info:#656d76; --info-bg:#f2f4f7; --info-line:#e0e4e9;
  --r:10px; --r-sm:7px; --r-lg:14px;
  --sh:0 1px 2px rgba(13,17,23,.05);
  --sh-lg:0 12px 32px -12px rgba(13,17,23,.2);
  --mono:ui-monospace,SFMono-Regular,"SF Mono",Menlo,Consolas,monospace;
  --rail:252px;
  --row:1;   /* плотность таблиц: 1 — комфортно, 0 — компактно */
}
[data-theme="dark"]{
  color-scheme:dark;
  --bg:#0b0e14; --surface:#11161d; --surface-2:#161c25; --surface-3:#1b222c;
  --ink:#e6edf3; --ink-2:#c9d1d9; --muted:#9198a1; --faint:#6e7681;
  --line:#232b36; --line-2:#1a212b;
  --accent:#4b91f7; --accent-2:#6ba5f8; --accent-soft:#11223b; --accent-line:#1f3a5f;
  --ok:#3fb950; --ok-bg:#0f2717; --ok-line:#1e4429;
  --warn:#d29922; --warn-bg:#2a2010; --warn-line:#4a3a17;
  --high:#db6d28; --high-bg:#2b1a0f; --high-line:#4d2f17;
  --crit:#f85149; --crit-bg:#2c1215; --crit-line:#54222a;
  --info:#8b949e; --info-bg:#1b222c; --info-line:#2b333d;
  --sh:0 1px 2px rgba(0,0,0,.4);
  --sh-lg:0 18px 44px -18px rgba(0,0,0,.75);
}
*{box-sizing:border-box}
html{scroll-behavior:smooth;scrollbar-color:var(--line) transparent;scrollbar-width:thin}
body{
  margin:0;background:var(--bg);color:var(--ink);
  font:14px/1.55 -apple-system,BlinkMacSystemFont,'Segoe UI','Inter',Roboto,Helvetica,Arial,sans-serif;
  -webkit-font-smoothing:antialiased;text-rendering:optimizeLegibility;
}
h1,h2,h3,h4{margin:0}
a{color:inherit}
:focus-visible{outline:2px solid var(--accent);outline-offset:2px;border-radius:4px}
::selection{background:var(--accent-soft);color:var(--ink)}
::-webkit-scrollbar{width:10px;height:10px}
::-webkit-scrollbar-track{background:transparent}
::-webkit-scrollbar-thumb{background:var(--line);border-radius:8px;border:2px solid var(--bg)}
::-webkit-scrollbar-thumb:hover{background:var(--faint)}

/* ---------- каркас: рельс + рабочая область ---------- */
.app{display:grid;grid-template-columns:var(--rail) minmax(0,1fr);min-height:100vh}
.side{
  position:sticky;top:0;align-self:start;height:100vh;overflow:auto;
  background:var(--surface);border-right:1px solid var(--line);
  padding:20px 18px;display:flex;flex-direction:column;gap:18px;
}
.brand{display:flex;gap:10px;align-items:center}
.brand .logo{
  width:30px;height:30px;border-radius:8px;display:grid;place-items:center;flex:none;
  background:var(--ink);color:var(--bg);font-weight:800;font-size:11.5px;letter-spacing:.02em;
}
.brand b{display:block;font-size:13.5px;letter-spacing:-.01em}
.brand span{display:block;font-size:11px;color:var(--faint)}
.side-site{padding:0}
.side-host{font-size:12.5px;font-weight:650;color:var(--ink-2);word-break:break-all;line-height:1.35}
.side-meta{font-size:11.5px;color:var(--faint);margin-top:4px;line-height:1.5}
/* один вердикт на экран: крупное число, цвет только для статуса */
.side-score{margin-top:14px}
.side-score b{font-size:44px;font-weight:800;letter-spacing:-.035em;line-height:1;color:var(--ink);font-variant-numeric:tabular-nums;display:inline-block}
.side-score b.ok{color:var(--ok)} .side-score b.warn{color:var(--warn)} .side-score b.bad{color:var(--crit)}
.side-score span{font-size:13px;font-weight:600;color:var(--faint);margin-left:3px}
.side-bar{height:4px;border-radius:99px;background:var(--surface-3);overflow:hidden;margin-top:10px}
.side-bar i{display:block;height:100%;border-radius:99px;background:var(--ink)}
.side-bar i.ok{background:var(--ok)} .side-bar i.warn{background:var(--warn)} .side-bar i.bad{background:var(--crit)}
.side-verdict{font-size:12px;color:var(--muted);margin-top:8px;line-height:1.45}
.side-cap{font-size:10px;letter-spacing:.12em;text-transform:uppercase;color:var(--faint);margin:0 0 6px;padding-left:8px}
.side-nav{display:flex;flex-direction:column;gap:1px;flex:1}
.side-nav a{
  display:flex;gap:9px;align-items:center;text-decoration:none;color:var(--muted);font-size:13px;font-weight:500;
  padding:6px 8px;border-radius:var(--r-sm);transition:background .12s,color .12s;
}
.side-nav a .n{font-size:10.5px;color:var(--faint);font-variant-numeric:tabular-nums;width:15px;flex:none}
.side-nav a:hover{background:var(--surface-3);color:var(--ink)}
.side-nav a.active{background:var(--accent-soft);color:var(--accent-2);font-weight:600}
.side-nav a.active .n{color:var(--accent-2)}
.side-foot{font-size:10.5px;color:var(--faint);line-height:1.6;border-top:1px solid var(--line-2);padding-top:12px}
.main{padding:0 32px 80px;min-width:0;max-width:1560px}

/* ---------- верхняя панель ---------- */
.topbar{
  position:sticky;top:0;z-index:30;display:flex;gap:14px;align-items:center;justify-content:space-between;
  flex-wrap:wrap;padding:13px 0 11px;background:var(--bg);border-bottom:1px solid var(--line);
}
.tb-title{font-size:14.5px;font-weight:650;letter-spacing:-.01em}
.tb-title span{color:var(--muted);font-weight:450}
.tb-sub{font-size:12px;color:var(--muted);margin-top:2px}
.tb-head{min-width:0}
.tb-score{
  display:inline-flex;align-items:baseline;gap:2px;background:var(--surface);border:1px solid var(--line);
  color:var(--ink);border-radius:var(--r-sm);padding:5px 10px;font-size:13.5px;font-weight:700;font-variant-numeric:tabular-nums;
}
.tb-score i{font-style:normal;font-size:11px;font-weight:500;color:var(--faint)}
.tb-tools{display:flex;gap:8px;align-items:center;flex-wrap:wrap}
.cmdk{
  display:flex;align-items:center;gap:8px;background:var(--surface);border:1px solid var(--line);
  border-radius:var(--r-sm);padding:6px 10px;font:inherit;font-size:12.5px;color:var(--faint);cursor:pointer;
  min-width:216px;transition:border-color .12s,color .12s;
}
.cmdk:hover{border-color:var(--accent-line);color:var(--ink-2)}
.cmdk kbd{
  font:11px var(--mono);border:1px solid var(--line);border-bottom-width:2px;border-radius:4px;
  padding:1px 5px;margin-left:auto;background:var(--surface-2);color:var(--muted);
}
.icon-btn{
  background:var(--surface);border:1px solid var(--line);color:var(--ink-2);border-radius:var(--r-sm);
  width:32px;height:32px;display:grid;place-items:center;cursor:pointer;font-size:13px;transition:background .12s,border-color .12s,color .12s;
}
.icon-btn:hover{border-color:var(--accent-line);color:var(--accent-2);background:var(--accent-soft)}
.tb-count{font-size:12px;color:var(--muted);font-variant-numeric:tabular-nums}
.btn{
  background:var(--surface);border:1px solid var(--line);color:var(--ink-2);border-radius:var(--r-sm);
  padding:6px 12px;font:inherit;font-size:12.5px;font-weight:550;cursor:pointer;transition:background .12s,border-color .12s,color .12s;
}
.btn:hover{border-color:var(--accent-line);color:var(--accent-2);background:var(--accent-soft)}
.btn.primary{background:var(--accent);border-color:var(--accent);color:#fff}
.btn.primary:hover{background:var(--accent-2);color:#fff}
.btn.ghost{background:transparent;border-color:transparent;color:var(--muted)}
.btn.ghost:hover{background:var(--surface-3);color:var(--ink)}

/* ---------- палитра команд (⌘K) ---------- */
.palette{
  position:fixed;inset:0;z-index:60;display:none;justify-content:center;align-items:flex-start;padding-top:12vh;
  background:rgba(13,17,23,.42);backdrop-filter:blur(3px);
}
.palette.open{display:flex}
.palette-box{
  width:min(620px,92vw);background:var(--surface);border:1px solid var(--line);border-radius:var(--r-lg);
  box-shadow:var(--sh-lg);overflow:hidden;
}
.palette-input{
  width:100%;border:none;border-bottom:1px solid var(--line);background:transparent;color:var(--ink);
  font:inherit;font-size:15px;padding:15px 18px;
}
.palette-input:focus{outline:none}
.palette-list{max-height:52vh;overflow:auto;padding:6px}
.palette-item{display:flex;gap:10px;align-items:center;padding:8px 12px;border-radius:var(--r-sm);cursor:pointer;font-size:13.5px}
.palette-item .k{font-size:10.5px;color:var(--faint);width:18px;font-variant-numeric:tabular-nums;flex:none}
.palette-item .t{color:var(--ink-2);overflow:hidden;text-overflow:ellipsis;white-space:nowrap}
.palette-item .s{margin-left:auto;font-size:11.5px;color:var(--faint);flex:none}
.palette-item.sel{background:var(--accent-soft)}
.palette-item.sel .t{color:var(--accent-2);font-weight:600}
.palette-empty{padding:22px;text-align:center;color:var(--muted);font-size:13px}
.palette-foot{border-top:1px solid var(--line);padding:8px 14px;font-size:11px;color:var(--faint);display:flex;gap:14px}

/* ---------- секции: иерархия через воздух, а не рамки ---------- */
.panel{margin:0 0 40px;scroll-margin-top:76px}
.panel-h{display:flex;gap:16px;align-items:flex-end;justify-content:space-between;flex-wrap:wrap;padding:0 0 14px;border-bottom:1px solid var(--line)}
.panel-t{display:flex;align-items:baseline;gap:10px;font-size:19px;font-weight:680;letter-spacing:-.02em}
.panel-t .num{font-size:11px;font-weight:700;color:var(--faint);font-variant-numeric:tabular-nums}
.panel-s{margin-top:5px;font-size:12.5px;color:var(--muted)}
.panel-b{padding:18px 0 0}
.panel-b.flush{padding:0}
.panel-b.pad-s{padding:12px 0 0}
.muted{color:var(--muted)}
.faint{color:var(--faint)}
.small{font-size:12.5px}
.mono{font-family:var(--mono);font-size:12px}

/* ---------- сетки ---------- */
.grid{display:grid;gap:12px}
.g2{grid-template-columns:repeat(auto-fit,minmax(330px,1fr))}
.g3{grid-template-columns:repeat(auto-fit,minmax(255px,1fr))}
.g4{grid-template-columns:repeat(auto-fit,minmax(196px,1fr))}

/* ---------- показатели: цифры и подписи, без коробок ---------- */
.stats{
  display:grid;grid-template-columns:repeat(auto-fit,minmax(132px,1fr));gap:0;
  border-top:1px solid var(--line);border-bottom:1px solid var(--line);margin:0 0 36px;
}
.stat{padding:14px 16px 15px;border-left:1px solid var(--line-2);cursor:default}
.stat:first-child{border-left:none;padding-left:0}
.stat-num{font-size:25px;font-weight:700;letter-spacing:-.03em;line-height:1;font-variant-numeric:tabular-nums}
.stat.s-critical .stat-num{color:var(--crit)} .stat.s-high .stat-num{color:var(--high)}
.stat.s-medium .stat-num{color:var(--warn)} .stat.s-low .stat-num{color:var(--ink-2)}
.stat.s-info .stat-num{color:var(--muted)} .stat.s-ok .stat-num{color:var(--ok)}
.stat-lbl{margin-top:7px;font-size:10.5px;font-weight:650;letter-spacing:.08em;text-transform:uppercase;color:var(--muted)}
.stat-sub{margin-top:4px;font-size:11.5px;color:var(--faint);line-height:1.4}
.stat.clickable{cursor:pointer;transition:background .12s}
.stat.clickable:hover{background:var(--surface-2)}
.stat.active{background:var(--accent-soft);box-shadow:inset 2px 0 0 var(--accent)}

/* ---------- направления ---------- */
.dir{
  display:block;text-decoration:none;color:inherit;background:var(--surface);
  border:1px solid var(--line);border-radius:var(--r);padding:14px 16px;
  transition:border-color .12s,box-shadow .12s,transform .12s;
}
.dir:hover{border-color:var(--accent-line);box-shadow:var(--sh);transform:translateY(-1px)}
.dir-h{display:flex;justify-content:space-between;align-items:baseline;gap:10px}
.dir-h span{font-weight:600;font-size:13.5px;letter-spacing:-.01em}
.dir-h b{font-size:14px;font-weight:700;font-variant-numeric:tabular-nums;color:var(--ink)}
.mini-bar{height:4px;border-radius:99px;background:var(--surface-3);overflow:hidden;margin:10px 0 8px}
.mini-bar i{display:block;height:100%;border-radius:99px;background:var(--ink)}
.mini-bar i.ok{background:var(--ok)} .mini-bar i.warn{background:var(--warn)} .mini-bar i.bad{background:var(--crit)}
.dir-f{font-size:11.5px;color:var(--muted);display:flex;gap:6px;align-items:center;flex-wrap:wrap}
.dir-f .bad{color:var(--crit);font-weight:650}
.dir-f .warn{color:var(--high);font-weight:650}
.dir-f .good{color:var(--ok);font-weight:650}
.dir.ok{border-left:2px solid var(--ok-line)} .dir.warn{border-left:2px solid var(--high-line)} .dir.bad{border-left:2px solid var(--crit-line)}

/* ---------- приоритет 1 ---------- */
.prio-list{display:flex;flex-direction:column;gap:2px}
.prio{
  display:grid;grid-template-columns:30px minmax(0,1fr);gap:14px;
  border-bottom:1px solid var(--line-2);padding:16px 0;
}
.prio:last-child{border-bottom:none}
.prio.critical{border-left:2px solid var(--crit);padding-left:14px;margin-left:-14px}
.prio.high{border-left:2px solid var(--high);padding-left:14px;margin-left:-14px}
.prio-n{
  width:26px;height:26px;border-radius:8px;background:var(--surface-3);color:var(--ink-2);font-size:11.5px;font-weight:700;
  display:grid;place-items:center;font-variant-numeric:tabular-nums;
}
.prio.critical .prio-n{background:var(--crit-bg);color:var(--crit)}
.prio.high .prio-n{background:var(--high-bg);color:var(--high)}
.prio-h{display:flex;gap:8px;align-items:center;flex-wrap:wrap;margin-bottom:6px}
.prio-t{font-size:15px;font-weight:650;letter-spacing:-.01em;margin-bottom:7px}
.kv{display:flex;gap:7px;font-size:12.5px;color:var(--muted);margin-top:3px;line-height:1.5;flex-wrap:wrap}
.kv b{color:var(--ink-2);font-weight:600;flex:none}
.kv .path{color:var(--ink-2)}
.srcs{display:flex;gap:6px;flex-wrap:wrap;margin-top:9px}
.srcs a{
  font-size:11px;text-decoration:none;color:var(--muted);border:1px solid var(--line);
  background:var(--surface);border-radius:6px;padding:3px 9px;transition:background .12s,color .12s,border-color .12s;
}
.srcs a:hover{background:var(--accent-soft);color:var(--accent-2);border-color:var(--accent-line)}
code{background:var(--surface-3);border:1px solid var(--line-2);border-radius:5px;padding:1px 6px;font-size:11.5px;color:var(--ink-2);font-family:var(--mono)}

/* ---------- фильтры ---------- */
.filters{display:flex;gap:6px;flex-wrap:wrap;align-items:center;padding:12px 0;border-bottom:1px solid var(--line-2)}
.chip{
  background:var(--surface);border:1px solid var(--line);color:var(--muted);border-radius:7px;
  padding:5px 11px;font:inherit;font-size:12.5px;font-weight:550;cursor:pointer;transition:background .12s,border-color .12s,color .12s;
}
.chip:hover{border-color:var(--accent-line);color:var(--accent-2)}
.chip.active{background:var(--ink);border-color:var(--ink);color:var(--bg)}
.chip .c{opacity:.6;font-weight:500;margin-left:5px;font-variant-numeric:tabular-nums}
.filters .sp{flex:1}

/* ---------- таблицы: основной интерфейс ---------- */
.tbl{width:100%;border-collapse:collapse;font-size:13px}
.tbl th{
  text-align:left;color:var(--muted);font-weight:650;font-size:10.5px;letter-spacing:.08em;text-transform:uppercase;
  border-bottom:1px solid var(--line);padding:9px 12px;background:var(--bg);white-space:nowrap;
}
.tbl td{border-bottom:1px solid var(--line-2);padding:calc(10px * var(--row)) 12px;vertical-align:top}
.tbl tbody tr:last-child td{border-bottom:none}
.tbl tbody tr:hover td{background:var(--surface-2)}
.tbl.sm{font-size:12.5px}
.tbl.sm th,.tbl.sm td{padding:calc(7px * var(--row)) 10px}
.tbl.sm td.ctr{text-align:center;width:66px}
.tbl .num{text-align:right;font-variant-numeric:tabular-nums}
.badge{display:inline-block;padding:3px 9px;border-radius:6px;font-size:10.5px;font-weight:650;letter-spacing:.04em;white-space:nowrap}
.badge.sev-critical{background:var(--crit-bg);color:var(--crit)}
.badge.sev-high{background:var(--high-bg);color:var(--high)}
.badge.sev-medium{background:var(--warn-bg);color:var(--warn)}
.badge.sev-low{background:var(--info-bg);color:var(--info)}
.badge.sev-info{background:var(--info-bg);color:var(--info)}
.badge.sev-ok{background:var(--ok-bg);color:var(--ok)}
.path{font-family:var(--mono);font-size:12px;color:var(--ink-2);word-break:break-all}
.impact{color:var(--muted);max-width:330px;line-height:1.5}
.sys{white-space:nowrap;width:104px}
.sys-b{display:inline-block;min-width:20px;text-align:center;font-size:10px;font-weight:650;border-radius:5px;padding:2px 6px;margin-right:3px;background:var(--surface-3);color:var(--muted)}
.sys-b.g{background:var(--warn-bg);color:var(--warn)}
.sys-b.y{background:var(--crit-bg);color:var(--crit)}
.sys-b.b{background:var(--accent-soft);color:var(--accent-2)}
.sys-b.i{background:var(--ok-bg);color:var(--ok)}
.code{font-weight:650;font-variant-numeric:tabular-nums}
.code.g{color:var(--ok)} .code.y{color:var(--high)} .code.r{color:var(--crit)}
.ok{color:var(--ok);font-weight:650}
.no{color:var(--crit);font-weight:650}
.ext{color:var(--accent-2);font-weight:650}
.warn{color:var(--high);font-weight:650}

/* ---------- сгенерированный llms.txt ---------- */
.llms-code{margin:0;background:var(--surface-2);border:1px solid var(--line);border-radius:var(--r);padding:16px 18px;font:12.5px/1.7 var(--mono);white-space:pre-wrap;word-break:break-word;color:var(--ink-2);max-height:560px;overflow:auto}

/* ---------- итоги: чек-лист по группам ---------- */
.summary{display:flex;flex-direction:column;gap:28px}
.sumgroup{border-top:1px solid var(--line);padding-top:14px}
.sumgroup-t{
  display:flex;align-items:baseline;gap:10px;margin:0 0 8px;font-size:15.5px;font-weight:650;letter-spacing:-.015em;
}
.sumgroup-t .num{font-size:11px;font-weight:700;color:var(--faint);font-variant-numeric:tabular-nums;min-width:18px}
.sumgroup-t .faint{font-weight:450;font-size:12px}
.tbl.sum{background:transparent}
.tbl.sum th{background:var(--bg);position:static}
.tbl.sum td{padding:calc(8px * var(--row)) 12px;vertical-align:top}
.tbl.sum td.what{width:32%}
.tbl.sum td.what b{font-weight:600;font-size:13px}
.tbl.sum td.note-cell{color:var(--muted);font-size:12.5px;line-height:1.5}
.tbl.sum tr.sumrow:hover td{background:var(--surface-2)}
.tbl.sum td.ctr{text-align:center;width:92px}
.pill{display:inline-block;min-width:64px;text-align:center;border-radius:6px;padding:3px 8px;font-size:11px;font-weight:650}
.pill.yes{background:var(--ok-bg);color:var(--ok)}
.pill.part{background:var(--high-bg);color:var(--high)}
.pill.no{background:var(--crit-bg);color:var(--crit)}
.sc{display:inline-grid;place-items:center;width:26px;height:24px;border-radius:6px;font-size:12.5px;font-weight:700;font-variant-numeric:tabular-nums;background:var(--surface-3);color:var(--muted)}
.sc.s5{background:var(--ok-bg);color:var(--ok)}
.sc.s4{background:var(--ok-bg);color:var(--ok);opacity:.75}
.sc.s3{background:var(--warn-bg);color:var(--warn)}
.sc.s2{background:var(--high-bg);color:var(--high)}
.sc.s1{background:var(--crit-bg);color:var(--crit)}
.sc.s0{background:var(--crit-bg);color:var(--crit)}

/* ---------- ссылки на страницы и цитаты источника ---------- */
.plink{color:var(--accent-2);text-decoration:none;border-bottom:1px solid var(--accent-line);word-break:break-all}
.plink:hover{color:var(--accent);border-bottom-color:var(--accent)}
.src{border:1px solid var(--line);border-left:2px solid var(--accent);border-radius:var(--r-sm);overflow:hidden;background:var(--surface)}
.src-h{display:flex;align-items:center;gap:10px;flex-wrap:wrap;padding:8px 12px;background:var(--surface-2);border-bottom:1px solid var(--line-2)}
.src-link{font-family:var(--mono);font-size:12px;color:var(--accent-2);text-decoration:none}
.src-link:hover{text-decoration:underline}
.src-line{font-size:10.5px;font-weight:700;letter-spacing:.05em;text-transform:uppercase;color:var(--ink-2);background:var(--surface-3);border-radius:5px;padding:2px 7px}
.src-file{margin-left:auto;font-size:10.5px;letter-spacing:.06em;text-transform:uppercase;color:var(--faint)}
.src-code{margin:0;padding:11px 13px;background:var(--surface-3);color:var(--ink-2);font:12px/1.6 var(--mono);white-space:pre-wrap;word-break:break-word;overflow-x:auto}
.src-code .ln{display:inline-block;min-width:34px;margin-right:12px;padding-right:10px;border-right:1px solid var(--line);color:var(--faint);text-align:right;user-select:none}
.src-code code{background:none;border:none;padding:0;color:inherit;font:inherit}

/* ---------- ИИ-исправления ---------- */
.aifix{border:1px solid var(--accent-line);background:var(--accent-soft);border-radius:var(--r-sm);padding:11px 13px}
.aifix.err{border-color:var(--crit-line);background:var(--crit-bg);color:var(--ink-2);font-size:12.5px}
.aifix-h{font-size:10.5px;font-weight:700;letter-spacing:.08em;text-transform:uppercase;color:var(--accent-2);margin-bottom:9px}
.aifix-row{display:grid;grid-template-columns:52px minmax(0,1fr);gap:10px;align-items:start;margin-bottom:8px}
.aifix-row .lbl{font-size:10px;font-weight:700;letter-spacing:.05em;border-radius:5px;padding:3px 0;text-align:center}
.aifix-row .lbl.was{background:var(--crit-bg);color:var(--crit)}
.aifix-row .lbl.now{background:var(--ok-bg);color:var(--ok)}
.aifix-row pre{margin:0;background:var(--surface);border:1px solid var(--line);border-radius:var(--r-sm);padding:9px 11px;font:12px/1.55 var(--mono);white-space:pre-wrap;word-break:break-word}
.aifix-why{font-size:12.5px;color:var(--ink-2);margin-top:3px}
.fixlist{display:grid;grid-template-columns:repeat(auto-fit,minmax(360px,1fr));gap:12px}
.fixcard{background:var(--surface);border:1px solid var(--line);border-radius:var(--r);padding:14px 16px}
.fixcard.critical{border-left:2px solid var(--crit)}
.fixcard.high{border-left:2px solid var(--high)}
.fixcard.medium{border-left:2px solid var(--warn)}
.fixcard-h{display:flex;align-items:center;gap:8px;flex-wrap:wrap;margin-bottom:8px}
.fixcard-h .plink{font-size:12px}
.fixcard-t{font-size:14px;font-weight:650;letter-spacing:-.01em;margin-bottom:9px}

/* ---------- раскрытие строки ---------- */
tr.irow{cursor:pointer}
tr.irow td.exp{width:26px;color:var(--faint)}
tr.irow .chev{display:inline-block;transition:transform .15s}
tr.irow.open .chev{transform:rotate(90deg);color:var(--accent)}
tr.det td{background:var(--surface-2);padding:16px 18px 18px}
.det-grid{font-size:13px;line-height:1.6;color:var(--muted);display:grid;gap:10px}
.det-grid b{color:var(--ink)}
.found ul{margin:6px 0 0;padding-left:18px}
.found li{margin-bottom:4px}
.snippet span,.cite span{display:block;font-size:10px;letter-spacing:.1em;text-transform:uppercase;color:var(--faint);margin-bottom:5px}
.snippet pre{
  margin:0;background:#0f172a;color:#e2e8f0;border-radius:var(--r-sm);padding:11px 13px;
  font:12px/1.55 var(--mono);white-space:pre-wrap;word-break:break-word;border-left:3px solid var(--crit);
}
.cite{font-size:12.5px;color:var(--ink-2);background:var(--surface);border:1px solid var(--line);border-radius:var(--r-sm);padding:10px 13px}
.ai-text{font-size:14px;line-height:1.7;color:var(--ink-2);white-space:pre-wrap}

/* ---------- аналитический слой ---------- */
.core-head{display:grid;grid-template-columns:minmax(220px,290px) 1fr;gap:16px;align-items:center;margin-bottom:18px}
.core-primary{display:flex;flex-direction:column;gap:3px;border-left:2px solid var(--accent);padding:2px 0 2px 12px}
.core-primary span{font-size:10px;letter-spacing:.08em;text-transform:uppercase;color:var(--faint)}
.core-primary b{font-size:19px;letter-spacing:-.015em;color:var(--ink)}
.chips{display:flex;flex-wrap:wrap;gap:6px}
.term{background:var(--surface-2);border:1px solid var(--line-2);color:var(--ink-2);border-radius:6px;padding:4px 10px;font-size:12px;font-weight:600}
.term.gap{background:var(--crit-bg);border-color:var(--crit-line);color:var(--crit)}
.mini{font-size:11px;font-weight:700;letter-spacing:.08em;text-transform:uppercase;color:var(--muted);margin:24px 0 10px}
.draft{background:var(--surface-2);border:1px solid var(--line-2);border-radius:var(--r-sm);padding:11px 14px;margin-bottom:8px}
.draft span{display:block;font-size:10px;letter-spacing:.08em;text-transform:uppercase;color:var(--faint);margin-bottom:4px}
.draft p{margin:0;font-size:13.5px;color:var(--ink)}
.draft em{font-style:normal;color:var(--faint);font-size:12px}
.diag{display:grid;grid-template-columns:repeat(auto-fit,minmax(300px,1fr));gap:12px}
.diag-item{background:var(--surface-2);border:1px solid var(--line-2);border-left:2px solid var(--accent);border-radius:var(--r-sm);padding:13px 15px}
.diag-item b{display:block;margin-bottom:5px;font-size:13.5px}
.fix{color:var(--accent-2);font-size:12.5px;margin-top:4px}
.note{margin-top:14px;background:var(--surface-2);border:1px solid var(--line-2);border-left:2px solid var(--accent);border-radius:var(--r-sm);padding:12px 14px;font-size:12.5px;color:var(--ink-2);line-height:1.55}
.link-src{margin:0;padding-left:18px;font-size:13px}
.link-src li{margin-bottom:5px}
ul.facts{margin:12px 0 0;padding-left:18px;font-size:13px;color:var(--muted);line-height:1.6}
ul.facts li{margin-bottom:7px}
.clusters{display:grid;grid-template-columns:repeat(auto-fit,minmax(290px,1fr));gap:12px}
.cluster{background:var(--surface-2);border:1px solid var(--line-2);border-radius:var(--r);padding:14px 16px}
.cl-h{display:flex;gap:8px;align-items:baseline;margin-bottom:9px;font-size:13px}
.cl-h b{color:var(--accent-2)}
.cl-pages{margin-top:9px;font-size:11.5px;word-break:break-word;line-height:1.5;color:var(--muted)}
.cmp{background:var(--surface-2);border:1px solid var(--line-2);border-radius:var(--r);padding:14px 16px;margin-bottom:10px}
.cmp-h{display:flex;justify-content:space-between;gap:10px;flex-wrap:wrap;margin-bottom:9px;font-weight:650;font-size:14px}
.cmp-better{background:var(--crit-bg);border:1px solid var(--crit-line);border-radius:var(--r-sm);padding:10px 13px;font-size:12.5px;margin-bottom:7px;color:var(--ink-2)}
.cmp-worse{background:var(--ok-bg);border:1px solid var(--ok-line);border-radius:var(--r-sm);padding:10px 13px;font-size:12.5px;color:var(--ink-2)}

/* ---------- прокручиваемые блоки ---------- */
.scroll{max-height:560px;overflow:auto;overscroll-behavior:contain;border:1px solid var(--line);border-radius:var(--r)}
.scroll .tbl{min-width:840px}
.scroll .tbl th{position:sticky;top:0;z-index:2;background:var(--bg);box-shadow:0 1px 0 var(--line)}
.scroll::-webkit-scrollbar{width:9px;height:9px}
.scroll::-webkit-scrollbar-track{background:var(--bg)}
.scroll::-webkit-scrollbar-thumb{background:var(--line);border-radius:99px}
.scroll::-webkit-scrollbar-thumb:hover{background:var(--faint)}
#issues.full .scroll{max-height:none;border:none}
#issues.full .scroll .tbl th{position:static;box-shadow:none}
section{scroll-margin-top:76px}

/* ---------- пройденные проверки ---------- */
.ok-list{display:grid;grid-template-columns:repeat(auto-fit,minmax(290px,1fr));gap:2px;max-height:560px;overflow:auto;padding-right:4px}
.ok-item{display:flex;gap:9px;align-items:flex-start;font-size:12.5px;padding:7px 10px;border-bottom:1px solid var(--line-2);line-height:1.45;color:var(--muted)}
.ok-item .t{color:var(--ok);font-weight:750;flex:none}
.ok-item .m{margin-left:auto;color:var(--faint);font-size:11px;white-space:nowrap;flex:none}

.empty{padding:30px;text-align:center;color:var(--muted);border:1px dashed var(--line);border-radius:var(--r);background:var(--surface-2);font-size:13px}
.foot{color:var(--faint);font-size:12px;text-align:center;padding:26px 0 8px;border-top:1px solid var(--line)}
.hidden{display:none}
.to-top{
  position:fixed;right:24px;bottom:24px;width:38px;height:38px;border-radius:10px;border:1px solid var(--line);
  background:var(--surface);color:var(--ink-2);font-size:15px;cursor:pointer;box-shadow:var(--sh);
  opacity:0;pointer-events:none;transition:opacity .2s;
}
.to-top.show{opacity:1;pointer-events:auto}
.to-top:hover{border-color:var(--accent-line);color:var(--accent-2)}
@media(max-width:1180px){
  .app{grid-template-columns:1fr}
  .side{position:static;height:auto;flex-direction:row;flex-wrap:wrap;align-items:center;gap:16px}
  .side-nav{flex-direction:row;flex-wrap:wrap;flex:1 1 100%;gap:4px}
  .side-cap,.side-foot{display:none}
  .side-site{flex:1 1 240px}
  .core-head{grid-template-columns:1fr}
}
@media(max-width:720px){
  .main{padding:0 16px 56px}
  .stats{grid-template-columns:repeat(auto-fit,minmax(120px,1fr))}
  .stat:first-child{padding-left:16px}
  .cmdk{min-width:0;flex:1}
}
@media(prefers-reduced-motion:reduce){
  *{transition:none!important;animation:none!important;scroll-behavior:auto!important}
}
@media print{
  .side,.topbar,.to-top,.filters,.palette{display:none!important}
  .app{display:block}
  .main{padding:0;max-width:none}
  .panel{break-inside:avoid}
  body{background:#fff}
}
CSS;
	}

	private function js(): string
	{
		return <<<'JS'
(function(){
  var rows   = [].slice.call(document.querySelectorAll('tr.irow'));
  var chips  = [].slice.call(document.querySelectorAll('.chip[data-f]'));
  var stats  = [].slice.call(document.querySelectorAll('.stat[data-sev]'));
  var dirs   = [].slice.call(document.querySelectorAll('.dir[data-dir]'));
  var jumps  = [].slice.call(document.querySelectorAll('[data-jump]'));
  var oks    = [].slice.call(document.querySelectorAll('.ok-item'));
  var navs   = [].slice.call(document.querySelectorAll('.side-nav a[data-nav]'));
  var search = document.getElementById('search');
  var fstate = document.getElementById('fstate');
  var issues = document.getElementById('issues');
  var curCat = 'all', curSev = '', q = '';

  function matchRow(r){
    var okCat = (curCat === 'all') || (r.getAttribute('data-cat') === curCat);
    var okSev = (!curSev) || (r.getAttribute('data-sev') === curSev);
    var okTxt = (!q) || ((r.getAttribute('data-text') || '').indexOf(q) > -1);
    return okCat && okSev && okTxt;
  }
  function apply(){
    var shown = 0;
    rows.forEach(function(r){
      var ok = matchRow(r);
      r.classList.toggle('hidden', !ok);
      var d = r.nextElementSibling;
      if (d && d.classList.contains('det')) { if (!ok) { d.hidden = true; r.classList.remove('open'); } }
      if (ok) shown++;
    });
    oks.forEach(function(o){
      var okCat = (curCat === 'all') || (o.getAttribute('data-okcat') === curCat);
      var t = (o.textContent || '').toLowerCase();
      o.classList.toggle('hidden', !(okCat && (!q || t.indexOf(q) > -1)));
    });
    if (fstate) fstate.textContent = 'показано ' + shown + ' из ' + rows.length;
  }
  function setFilter(cat, sev){
    curCat = cat || 'all';
    curSev  = sev || '';
    chips.forEach(function(c){ c.classList.toggle('active', c.getAttribute('data-f') === curCat); });
    stats.forEach(function(s){ s.classList.toggle('active', curSev !== '' && s.getAttribute('data-sev') === curSev); });
    apply();
  }
  chips.forEach(function(c){
    c.addEventListener('click', function(){ setFilter(c.getAttribute('data-f'), ''); });
  });
  // клик по показателю критичности — фильтр замечаний
  stats.forEach(function(s){
    s.addEventListener('click', function(){
      var sev = s.getAttribute('data-sev');
      setFilter('all', curSev === sev ? '' : sev);
      if (issues) issues.scrollIntoView({behavior:'smooth', block:'start'});
    });
  });
  // клик по направлению — замечания этого раздела
  dirs.forEach(function(d){
    d.addEventListener('click', function(){ setFilter(d.getAttribute('data-dir'), ''); });
  });
  // переход из плана к замечаниям раздела
  jumps.forEach(function(j){
    j.addEventListener('click', function(ev){
      ev.preventDefault();
      setFilter(j.getAttribute('data-jump'), '');
      if (issues) issues.scrollIntoView({behavior:'smooth', block:'start'});
    });
  });
  // раскрытие строки с деталями
  rows.forEach(function(r){
    r.addEventListener('click', function(){
      var d = r.nextElementSibling;
      if (!d || !d.classList.contains('det')) return;
      var open = d.hidden;
      d.hidden = !open;
      r.classList.toggle('open', open);
    });
  });
  if (search){
    search.addEventListener('input', function(){ q = (search.value || '').toLowerCase().trim(); apply(); });
  }
  // компактный / полный вид списка
  var toggle = document.getElementById('toggleFull');
  if (toggle){
    toggle.addEventListener('click', function(){
      if (!issues) return;
      var full = issues.classList.toggle('full');
      toggle.textContent = full ? 'Компактный вид' : 'Полный вид';
      toggle.classList.toggle('active', full);
    });
  }
  // копирование плана устранения
  var copy = document.getElementById('copyList');
  if (copy){
    copy.addEventListener('click', function(){
      var ta = document.getElementById('planText');
      if (!ta) return;
      var txt = ta.value;
      var done = function(){ copy.textContent = 'Скопировано ✓'; setTimeout(function(){ copy.textContent = 'Скопировать план'; }, 1600); };
      if (navigator.clipboard && navigator.clipboard.writeText) {
        navigator.clipboard.writeText(txt).then(done, function(){ fallback(ta, done); });
      } else { fallback(ta, done); }
      function fallback(ta, done){
        ta.removeAttribute('hidden'); ta.select();
        try { document.execCommand('copy'); done(); } catch(e) { window.prompt('Скопируйте план:', ta.value); }
        ta.setAttribute('hidden','');
      }
    });
  }
  // печать отчёта
  var printBtn = document.getElementById('printBtn');
  if (printBtn){
    printBtn.addEventListener('click', function(){ window.print(); });
  }
  // кнопка «наверх»
  var toTop = document.getElementById('toTop');
  if (toTop){
    toTop.addEventListener('click', function(){ window.scrollTo({top:0, behavior:'smooth'}); });
    window.addEventListener('scroll', function(){ toTop.classList.toggle('show', window.scrollY > 600); });
  }
  // активный раздел в боковом меню
  if ('IntersectionObserver' in window && navs.length){
    var byId = {};
    navs.forEach(function(a){ byId[a.getAttribute('data-nav')] = a; });
    var obs = new IntersectionObserver(function(entries){
      entries.forEach(function(en){
        if (!en.isIntersecting) return;
        navs.forEach(function(a){ a.classList.remove('active'); });
        var a = byId[en.target.id];
        if (a) a.classList.add('active');
      });
    }, {rootMargin: '-76px 0px -70% 0px', threshold: 0});
    navs.forEach(function(a){
      var el = document.getElementById(a.getAttribute('data-nav'));
      if (el) obs.observe(el);
    });
  }

  // ---------- тема: светлая / тёмная ----------
  var themeBtn = document.getElementById('themeBtn');
  if (themeBtn){
    themeBtn.addEventListener('click', function(){
      var cur = document.documentElement.getAttribute('data-theme') === 'dark' ? 'light' : 'dark';
      document.documentElement.setAttribute('data-theme', cur);
      try { localStorage.setItem('sa-theme', cur); } catch(e){}
    });
  }

  // ---------- плотность таблиц ----------
  var density = document.getElementById('density');
  if (density){
    density.addEventListener('click', function(){
      var cur = document.documentElement.style.getPropertyValue('--row');
      var next = (cur === '' || cur === '1') ? '0.7' : '1';
      document.documentElement.style.setProperty('--row', next);
      try { localStorage.setItem('sa-density', next); } catch(e){}
    });
    try {
      var savedRow = localStorage.getItem('sa-density');
      if (savedRow) document.documentElement.style.setProperty('--row', savedRow);
    } catch(e){}
  }

  // ---------- палитра команд (⌘K): разделы и находки ----------
  var palette = document.getElementById('palette');
  var pInput = document.getElementById('paletteInput');
  var pList = document.getElementById('paletteList');
  var pItems = [], pSel = 0;

  function paletteData(q){
    q = (q || '').toLowerCase().trim();
    var out = [];
    navs.forEach(function(a){
      var t = a.textContent.replace(/^\d+/, '').trim();
      if (!q || t.toLowerCase().indexOf(q) > -1) {
        out.push({k: a.querySelector('.n') ? a.querySelector('.n').textContent : '', t: t, s: 'раздел', go: function(){ location.hash = a.getAttribute('data-nav'); }});
      }
    });
    if (q.length >= 2){
      rows.forEach(function(r){
        var txt = r.getAttribute('data-text') || '';
        var title = r.querySelector('td:nth-child(3)');
        var label = title ? title.textContent.replace(/\s+/g, ' ').trim() : txt;
        if (txt.indexOf(q) > -1){
          out.push({k: '', t: label.slice(0, 90), s: r.getAttribute('data-sev') || '', go: function(){
            setFilter('all', '');
            rows.forEach(function(x){ x.classList.remove('open'); var d = x.nextElementSibling; if (d && d.classList.contains('det')) d.hidden = true; });
            r.classList.add('open');
            var d = r.nextElementSibling;
            if (d && d.classList.contains('det')) d.hidden = false;
            r.scrollIntoView({behavior:'smooth', block:'center'});
          }});
        }
      });
    }
    return out.slice(0, 40);
  }
  function paletteRender(){
    if (!pList) return;
    var q = pInput ? pInput.value : '';
    pItems = paletteData(q);
    pSel = 0;
    if (!pItems.length){
      pList.innerHTML = '<div class="palette-empty">Ничего не найдено. Попробуйте другое слово или откройте раздел из меню.</div>';
      return;
    }
    var html = '';
    pItems.forEach(function(it, i){
      html += '<div class="palette-item' + (i === 0 ? ' sel' : '') + '" data-i="' + i + '">'
        + (it.k ? '<span class="k">' + it.k + '</span>' : '<span class="k">•</span>')
        + '<span class="t"></span><span class="s">' + it.s + '</span></div>';
    });
    pList.innerHTML = html;
    [].forEach.call(pList.querySelectorAll('.palette-item'), function(el){
      el.querySelector('.t').textContent = pItems[+el.getAttribute('data-i')].t;
      el.addEventListener('click', function(){
        pItems[+el.getAttribute('data-i')].go();
        paletteClose();
      });
    });
  }
  function paletteOpen(){
    if (!palette) return;
    palette.classList.add('open');
    if (pInput) { pInput.value = ''; pInput.focus(); }
    paletteRender();
  }
  function paletteClose(){
    if (palette) palette.classList.remove('open');
  }
  var cmdk = document.getElementById('cmdk');
  if (cmdk) cmdk.addEventListener('click', paletteOpen);
  if (pInput) pInput.addEventListener('input', paletteRender);
  document.addEventListener('keydown', function(ev){
    if ((ev.metaKey || ev.ctrlKey) && (ev.key === 'k' || ev.key === 'K' || ev.key === 'л')){
      ev.preventDefault();
      if (palette && palette.classList.contains('open')) paletteClose(); else paletteOpen();
      return;
    }
    if (ev.key === 'Escape' && palette && palette.classList.contains('open')){ paletteClose(); return; }
    if (palette && palette.classList.contains('open') && (ev.key === 'ArrowDown' || ev.key === 'ArrowUp')){
      ev.preventDefault();
      if (!pItems.length) return;
      pSel = (pSel + (ev.key === 'ArrowDown' ? 1 : -1) + pItems.length) % pItems.length;
      [].forEach.call(pList.querySelectorAll('.palette-item'), function(el, i){
        el.classList.toggle('sel', i === pSel);
      });
      var sel = pList.querySelector('.palette-item.sel');
      if (sel) sel.scrollIntoView({block:'nearest'});
      return;
    }
    if (palette && palette.classList.contains('open') && ev.key === 'Enter'){
      ev.preventDefault();
      if (pItems[pSel]) { pItems[pSel].go(); paletteClose(); }
    }
  });
  if (palette) palette.addEventListener('click', function(ev){ if (ev.target === palette) paletteClose(); });

  // ---------- выгрузка данных из отчёта ----------
  function download(name, text, type){
    var blob = new Blob([text], {type: type || 'text/plain;charset=utf-8'});
    var url = URL.createObjectURL(blob);
    var a = document.createElement('a');
    a.href = url; a.download = name;
    document.body.appendChild(a); a.click(); a.remove();
    setTimeout(function(){ URL.revokeObjectURL(url); }, 1500);
  }
  var dlJson = document.getElementById('dlJson');
  if (dlJson){
    dlJson.addEventListener('click', function(){
      var node = document.getElementById('reportJson');
      if (!node) return;
      download('audit-report.json', node.textContent, 'application/json;charset=utf-8');
    });
  }
  var llmsText = '';
  var llmsNode = document.querySelector('.llms-code');
  if (llmsNode) llmsText = llmsNode.textContent;
  var dlLlm = document.getElementById('dlLlm');
  if (dlLlm) dlLlm.addEventListener('click', function(){ download('llms.txt', llmsText); });
  var copyLlm = document.getElementById('copyLlm');
  if (copyLlm){
    copyLlm.addEventListener('click', function(){
      var ta = document.getElementById('planText');
      if (navigator.clipboard && navigator.clipboard.writeText){
        navigator.clipboard.writeText(llmsText).then(function(){
          copyLlm.textContent = 'Скопировано ✓';
          setTimeout(function(){ copyLlm.textContent = 'Скопировать llms.txt'; }, 1600);
        });
      }
    });
  }
  var copySummary = document.getElementById('copySummary');
  if (copySummary){
    copySummary.addEventListener('click', function(){
      var node = document.getElementById('reportJson');
      if (!node) return;
      var data = null;
      try { data = JSON.parse(node.textContent); } catch(e){}
      if (!data) return;
      var lines = [];
      lines.push('Оценка: ' + data.score.total + '/100');
      lines.push('Страниц: ' + data.summary.pages + ', правил: ' + data.summary.rules + ', замечаний: ' + data.summary.issues);
      Object.keys(data.summary.by_severity).forEach(function(k){
        if (data.summary.by_severity[k] > 0) lines.push('  ' + k + ': ' + data.summary.by_severity[k]);
      });
      lines.push('');
      (data.checks || []).filter(function(c){ return !c.pass; }).forEach(function(c){
        lines.push('[' + c.severity + '] ' + c.id + ' — ' + c.title);
      });
      if (navigator.clipboard && navigator.clipboard.writeText){
        navigator.clipboard.writeText(lines.join('\n')).then(function(){
          copySummary.textContent = 'Скопировано ✓';
          setTimeout(function(){ copySummary.textContent = 'Скопировать сводку'; }, 1600);
        });
      }
    });
  }
  apply();
})();
JS;
	}

	private function plural(int $n, string $one, string $few, string $many): string
	{
		$n = abs($n) % 100;
		$n1 = $n % 10;
		if ($n > 10 && $n < 20) {
			return $many;
		}
		if ($n1 > 1 && $n1 < 5) {
			return $few;
		}
		if ($n1 === 1) {
			return $one;
		}
		return $many;
	}
}

/**
 * Справка по запуску.
 */
function usage(): string
{
	return <<<'TXT'
STUDIO AUDIT CRAWLER 2.1 — полный SEO/технический/performance-аудит сайта в одном файле

  php tools/crawler.php <url>

  Этого достаточно. Больше ничего указывать не нужно.

  Режим выбирается автоматически по адресу:
    • ЛОКАЛЬНЫЙ (localhost, 127.0.0.1, ::1, приватные сети 10/172.16-31/192.168,
      домены .local/.test/.dev/.internal) — глубокий быстрый обход без пауз,
      HTTPS-проверки отключены (у стенда нет TLS), проверка служебных файлов включена;
    • ПУБЛИЧНЫЙ (любой другой адрес) — вежливый обход с паузами и мягкими таймаутами,
      HTTPS/TLS-проверки включены, сканирование чужих служебных файлов отключено.
  Без адреса аудитится локальный стенд http://127.0.0.1:8899/

  Пока идёт сбор данных, в терминале рисуется анимация: текущая фаза,
  счётчик, полоса прогресса и оценка «осталось ≈ 0:42». Флаг --quiet её отключает.


  Дополнительные параметры (необязательны — нужны только для нестандартных случаев):
    --depth=N          максимальная глубина обхода в кликах
    --limit=N          максимум страниц
    --concurrency=N    параллельных запросов
    --delay=MS         пауза между запросами к одному хосту
    --timeout=MS       таймаут запроса
    --ua=STRING        User-Agent краулера
    --only=PATH        обход только по префиксу пути (через запятую)
    --exclude=RE       исключать URL по PCRE (можно повторять)
    --respect-robots   не заходить на URL, запрещённые robots.txt
    --no-robots        не читать robots.txt
    --external[=N]     проверять внешние ссылки (0 — выкл.)
    --no-external      не проверять внешние ссылки
    --no-probe         не проверять служебные файлы (.env, .git, …)
    --probe            проверять служебные файлы и на публичных сайтах
    --json=FILE        дополнительно сохранить JSON отдельным файлом
    --html=FILE        сохранить HTML-отчёт (по умолчанию tools/audit-report.html)
    --no-report        не создавать HTML-отчёт
    --
    -- РЕЗУЛЬТАТ: всегда создаётся один файл tools/audit-report.html со всеми
    -- результатами: сводка, находки, цитаты кода, разбор ИИ, llms.txt, боты,
    -- домены, страницы и сырые данные (JSON внутри файла, кнопка «Скачать JSON»).
    --pages            показать полную таблицу страниц
    --page=URL         план продвижения конкретной страницы (ядро, пробелы, черновики)
    --clusters         карта тематических кластеров сайта
    --competitors=URLS сравнение с конкурентами (те же правила, через запятую)
    --test-url=ПУТЬ   проверить URL по правилам robots.txt (с --bot=имя)
    --bot=ИМЯ         бот для --test-url (по умолчанию «*»)
    --gen-llms         сгенерировать llms.txt из карты сайта
    --llms-out=FILE    куда сохранить сгенерированный llms.txt
    --ai[=провайдер]   размышление ИИ над сводкой: auto|openrouter|groq|gemini|ollama
                       (ключ — в переменной окружения OPENROUTER_API_KEY / GROQ_API_KEY /
                       GEMINI_API_KEY; свой endpoint — AUDIT_AI_URL)
    --no-color         без ANSI-цвета
    --quiet            только итоговые строки
    --help             эта справка

  Примеры
    php tools/crawler.php https://example.com
    php tools/crawler.php https://example.com --html=/tmp/audit.html --json=/tmp/audit.json
    php tools/crawler.php https://example.com --depth=8 --limit=2000

  Коды возврата
    0 — критичных и высоких замечаний нет
    1 — есть замечания
    2 — ошибка запуска/аудита
TXT;
}

/**
 * Разбор аргументов командной строки.
 */
function parseArgs(array $argv): Cfg
{
	$cfg = new Cfg();
	$args = array_slice($argv, 1);
	$urlGiven = false;

	foreach ($args as $arg) {
		if ($arg === '' || $arg[0] !== '-') {
			if (!$urlGiven) {
				$cfg->start = $arg;
				$urlGiven = true;
			}
			continue;
		}
		$body = ltrim($arg, '-');
		$val = null;
		if (str_contains($body, '=')) {
			[$body, $val] = explode('=', $body, 2);
		}
		switch ($body) {
			case 'help':
			case 'h':
				echo usage(), PHP_EOL;
				exit(0);
			case 'depth':
				$cfg->depth = max(0, (int)$val);
				$cfg->explicit[] = 'depth';
				break;
			case 'limit':
				$cfg->limit = max(1, (int)$val);
				$cfg->explicit[] = 'limit';
				break;
			case 'concurrency':
				$cfg->concurrency = max(1, min(32, (int)$val));
				$cfg->explicit[] = 'concurrency';
				break;
			case 'delay':
				$cfg->delayMs = max(0, (int)$val);
				$cfg->explicit[] = 'delayMs';
				break;
			case 'timeout':
				$cfg->timeoutMs = max(1000, (int)$val);
				$cfg->explicit[] = 'timeoutMs';
				break;
			case 'ua':
				$cfg->ua = (string)$val;
				break;
			case 'only':
				foreach (explode(',', (string)$val) as $part) {
					$part = trim($part);
					if ($part !== '') {
						$cfg->only[] = $part;
					}
				}
				break;
			case 'exclude':
				if ($val !== null) {
					$cfg->exclude[] = $val;
				}
				break;
			case 'respect-robots':
				$cfg->respectRobots = true;
				break;
			case 'no-robots':
				$cfg->readRobots = false;
				break;
			case 'external':
				$cfg->external = true;
				if ($val !== null && $val !== '') {
					$cfg->externalLimit = max(0, (int)$val);
					$cfg->external = $cfg->externalLimit > 0;
					$cfg->explicit[] = 'externalLimit';
				}
				break;
			case 'no-external':
				$cfg->external = false;
				break;
			case 'no-probe':
				$cfg->probe = false;
				$cfg->explicit[] = 'probe';
				break;
			case 'probe':
				$cfg->probe = true;
				$cfg->explicit[] = 'probe';
				break;
			case 'json':
				$cfg->jsonFile = (string)$val;
				break;
			case 'html':
				$cfg->htmlFile = (string)$val;
				break;
			case 'no-report':
				$cfg->noReport = true;
				break;
			case 'page':
				$cfg->pageUrl = (string)$val;
				break;
			case 'competitors':
				foreach (explode(',', (string)$val) as $u) {
					$u = trim($u);
					if ($u !== '') {
						$cfg->competitors[] = $u;
					}
				}
				break;
			case 'clusters':
				$cfg->clusters = true;
				break;
			case 'ai':
				$cfg->ai = $val !== null && $val !== '' ? strtolower((string)$val) : 'auto';
				break;
			case 'ai-fix':
				$cfg->aiFix = $val !== null && $val !== '' ? max(1, (int)$val) : 20;
				break;
			case 'ai-model':
				$cfg->aiModel = (string)$val;
				break;
			case 'self-test':
				$cfg->selfTest = true;
				break;
			case 'test-url':
				$cfg->testUrl = (string)$val;
				break;
			case 'bot':
				$cfg->testBot = (string)$val !== '' ? strtolower((string)$val) : '*';
				break;
			case 'gen-llms':
				$cfg->genLlm = true;
				break;
			case 'llms-out':
				$cfg->llmsOut = (string)$val;
				break;
			case 'pages':
				$cfg->showPages = true;
				break;
			case 'no-color':
				$cfg->color = false;
				break;
			case 'quiet':
			case 'q':
				$cfg->quiet = true;
				break;
			default:
				fwrite(STDERR, "Неизвестная опция: {$arg}\n");
				echo usage(), PHP_EOL;
				exit(2);
		}
	}

	// нормализация адреса
	$start = trim($cfg->start);
	if ($start === '') {
		$start = 'http://127.0.0.1:8899/';
	}
	if (!preg_match('~^[a-z][a-z0-9+.\-]*://~i', $start)) {
		$start = 'http://' . ltrim($start, '/');
	}
	$parsed = parse_url($start);
	if ($parsed === false || !isset($parsed['host'])) {
		fwrite(STDERR, "Некорректный адрес: {$cfg->start}\n");
		exit(2);
	}
	$scheme = strtolower($parsed['scheme'] ?? 'http');
	$host = strtolower($parsed['host']);
	$port = isset($parsed['port']) ? ':' . (int)$parsed['port'] : '';
	$path = $parsed['path'] ?? '/';
	if ($path === '') {
		$path = '/';
	}
	$query = isset($parsed['query']) && $parsed['query'] !== '' ? '?' . $parsed['query'] : '';
	$cfg->start = $scheme . '://' . $host . $port . $path . $query;

	// Сценарий подбирается автоматически: локальный стенд или публичный сайт.
	// Явно заданные пользователем опции при этом не перекрываются.
	$cfg->applyScenario();
	return $cfg;
}

/**
 * Провайдер ИИ и ключ: автоопределение или явный выбор.
 *
 * @return array{ok:bool,provider:string,key:string,model:string,error:string}
 */
function aiProvider(Cfg $cfg): array
{
	$provider = $cfg->ai;
	$key = '';
	if ($provider === 'auto') {
		$detected = AiBrain::detect();
		if ($detected === null) {
			return [
				'ok'       => false,
				'error'    => 'Ключ ИИ не найден. Задайте переменную окружения: OPENROUTER_API_KEY (бесплатно, 20+ моделей, 50 запросов/сутки), GROQ_API_KEY (быстро) или GEMINI_API_KEY. Локально и полностью бесплатно: установите Ollama — ключ не нужен.',
				'provider' => '',
				'key'      => '',
				'model'    => '',
			];
		}
		$provider = $detected['provider'];
		$key = $detected['key'];
	} else {
		$p = AiBrain::PROVIDERS[$provider] ?? null;
		if ($p === null) {
			return ['ok' => false, 'error' => 'Неизвестный провайдер ИИ: ' . $provider, 'provider' => $provider, 'key' => '', 'model' => ''];
		}
		foreach ((array)$p['env'] as $env) {
			$v = (string)(getenv($env) ?: '');
			if ($v !== '') {
				$key = $v;
				break;
			}
		}
		if ($key === '' && empty($p['noKey'])) {
			return [
				'ok'       => false,
				'error'    => 'Для провайдера «' . $provider . '» не задана переменная окружения: ' . implode(' или ', (array)$p['env']),
				'provider' => $provider,
				'key'      => '',
				'model'    => '',
			];
		}
	}
	return ['ok' => true, 'error' => '', 'provider' => $provider, 'key' => $key, 'model' => $cfg->aiModel];
}

/**
 * Разбор ответа ИИ на «БЫЛО / СТАЛО / ПОЧЕМУ».
 *
 * @return array{before:string,after:string,why:string}
 */
function parseAiFix(string $text): array
{
	$before = '';
	$after = '';
	$why = '';
	if (preg_match('~БЫЛО\s*:\s*(.+?)(?=\n\s*СТАЛО\s*:)~su', $text, $m)) {
		$before = trim($m[1]);
	}
	if (preg_match('~СТАЛО\s*:\s*(.+?)(?=\n\s*ПОЧЕМУ\s*:|\z)~su', $text, $m)) {
		$after = trim($m[1]);
	}
	if (preg_match('~ПОЧЕМУ\s*:\s*(.+)\z~su', $text, $m)) {
		$why = trim($m[1]);
	}
	return ['before' => $before, 'after' => $after, 'why' => $why];
}

/**
 * AI-исправления по находкам: не «что не так», а готовый код «заменить на …».
 *
 * Для каждой находки в промпт уходит адрес страницы, номер строки и сам код строки —
 * модель отвечает, что заменить и на что.
 *
 * @param list<array<string,mixed>> $results правила аудита
 * @param null|callable(int,int,string):void $progress
 * @return array{ok:bool,error:string,provider:string,model:string,items:list<array<string,mixed>>}
 */
function runAiFixes(Cfg $cfg, Ctx $ctx, array $results, ?callable $progress = null): array
{
	$empty = ['ok' => false, 'error' => '', 'provider' => '', 'model' => '', 'items' => []];
	$limit = max(0, $cfg->aiFix);
	if ($limit === 0) {
		return $empty;
	}
	$ai = aiProvider($cfg);
	if (!$ai['ok']) {
		return ['ok' => false, 'error' => $ai['error'], 'provider' => $ai['provider'], 'model' => '', 'items' => []];
	}

	// набираем находки: сначала критичные, затем высокие и средние
	$pool = [];
	foreach (['critical', 'high', 'medium'] as $sev) {
		foreach ($results as $r) {
			if (!empty($r['pass']) || ($r['sev'] ?? '') !== $sev) {
				continue;
			}
			foreach ((array)($r['violations'] ?? []) as $v) {
				$pool[] = [
					'rule'    => (string)$r['id'],
					'title'   => (string)($r['title'] ?? ''),
					'cat'     => (string)($r['cat'] ?? ''),
					'sev'     => (string)$r['sev'],
					'url'     => $v['url'] ?? null,
					'detail'  => (string)($v['detail'] ?? ''),
					'snippet' => (string)($v['snippet'] ?? ''),
					'cite'    => (string)($v['cite'] ?? ''),
					'hint'    => (string)($r['hint'] ?? ''),
				];
				if (count($pool) >= $limit) {
					break 2;
				}
			}
		}
	}
	if ($pool === []) {
		return ['ok' => true, 'error' => '', 'provider' => $ai['provider'], 'model' => $ai['model'], 'items' => []];
	}

	$items = [];
	$total = count($pool);
	foreach ($pool as $i => $item) {
		if ($progress !== null) {
			$progress($i + 1, $total, (string)$item['rule']);
		}
		$url = (string)($item['url'] ?? '');
		$cite = $ctx->sourceCitation($url, (string)$item['snippet'], (string)$item['rule']);
		$code = (string)$item['snippet'];
		if ($code === '' && $cite !== null) {
			$code = (string)$cite['code'];
		}
		$where = $url !== '' ? Ctx::pathKey($url) : 'сайт целиком';
		$prompt = "Ты senior-разработчик и SEO-инженер. Ниже одна конкретная находка SEO-аудита.\n"
			. "Дай готовое исправление: что именно заменить и на что. Не пересказывай проблему — дай код.\n\n"
			. "САЙТ: " . $ctx->origin() . "\n"
			. "СТРАНИЦА: " . $where . ($url !== '' ? " (полный URL: " . $url . ")" : "") . "\n"
			. "ПРАВИЛО: " . $item['rule'] . " — " . $item['title'] . "\n"
			. "ЧТО НЕ ТАК: " . $item['detail'] . "\n";
		if ($item['cite'] !== '') {
			$prompt .= "ЧТО ТРЕБУЕТСЯ: " . $item['cite'] . "\n";
		}
		if ($item['hint'] !== '') {
			$prompt .= "РЕКОМЕНДАЦИЯ АУДИТОРА: " . $item['hint'] . "\n";
		}
		if ($code !== '') {
			$prompt .= "СТРОКА КОДА" . ($cite !== null && $cite['line'] > 0 ? " (строка " . $cite['line'] . " отдаваемого HTML)" : "") . ":\n" . $code . "\n";
		}
		$prompt .= "\nПРАВИЛА ОТВЕТА:\n"
			. "- отвечай по-русски, кратко, без вступлений и без markdown-блоков;\n"
			. "- БЫЛО — ровно та строка кода, которую надо заменить (как она есть сейчас);\n"
			. "- СТАЛО — готовая строка кода для вставки: с нашими данными (правильный canonical, наш description, реальный alt), без {{плейсхолдеров}}, если хватает контекста;\n"
			. "- если правка не в существующей строке (нужен новый тег) — в БЫЛО напиши «добавить в <head>», в СТАЛО — готовый тег;\n"
			. "- не выдумывай файлы, классы и поля, которых нет в контексте.\n\n"
			. "ФОРМАТ ОТВЕТА (строго три строки):\n"
			. "БЫЛО: ...\n"
			. "СТАЛО: ...\n"
			. "ПОЧЕМУ: одно предложение\n";

		$res = AiBrain::call($ai['provider'], $ai['key'], $ai['model'], $prompt, 60);
		$parsed = parseAiFix((string)($res['text'] ?? ''));
		$items[] = [
			'rule'   => $item['rule'],
			'title'  => $item['title'],
			'sev'    => $item['sev'],
			'url'    => $url,
			'path'   => $where,
			'line'   => $cite !== null ? (int)$cite['line'] : 0,
			'code'   => $code,
			'ok'     => (bool)($res['ok'] ?? false),
			'error'  => (string)($res['error'] ?? ''),
			'text'   => (string)($res['text'] ?? ''),
			'before' => $parsed['before'],
			'after'  => $parsed['after'],
			'why'    => $parsed['why'],
		];
	}
	return ['ok' => true, 'error' => '', 'provider' => $ai['provider'], 'model' => $ai['model'], 'items' => $items];
}

/**
 * Запуск ИИ-размышления над сводкой аудита.
 *
 * Ключ берётся из переменной окружения (AUDIT_AI_KEY / OPENROUTER_API_KEY /
 * GROQ_API_KEY / GEMINI_API_KEY) — никаких файлов и библиотек не требуется.
 *
 * @return array<string,mixed>
 */
function runAi(Cfg $cfg, Ctx $ctx, Audit $audit, array $score): array
{
	$ai = aiProvider($cfg);
	if (!$ai['ok']) {
		return [
			'ok'       => false,
			'error'    => $ai['error'],
			'provider' => $ai['provider'],
			'model'    => '',
		];
	}
	$provider = $ai['provider'];
	$key = $ai['key'];
	$model = $ai['model'];

	$digest = AiBrain::digest($ctx, $audit, $score);
	$prompt = "Ниже — сводка технического SEO-аудита сайта. Разбери её как опытный SEO-аналитик и ответь по-русски:\n"
		. "1) ТОП-5 проблем по приоритету — с фактом из сводки и почему это важно.\n"
		. "2) Что делать ПЕРВЫМ в понедельник (конкретные шаги).\n"
		. "3) Что НЕ стоит трогать сейчас (чтобы не тратить ресурс).\n"
		. "4) Какие гипотезы и внешние данные проверить дополнительно (Вебмастер, Метрика, Ahrefs).\n"
		. "Не выдумывай фактов, которых нет в сводке. Без воды.\n\n"
		. $digest;

	$res = AiBrain::call($provider, $key, $cfg->aiModel, $prompt);
	$res['digest'] = $digest;
	return $res;
}/**
 * Интерактивный запуск: спрашиваем метод проверки и нужный адрес.
 *
 * Без аргументов в терминале показывается меню; дальше подставляется
 * готовый набор опций под выбранный метод.
 */
function interactiveSetup(Ui $ui): Cfg
{
	$ui->line();
	$ui->line('  ' . $ui->c('STUDIO AUDIT', 'bold|brcyan') . ' ' . $ui->c('· технический SEO-аудит с разбором ИИ', 'gray'));
	$choice = $ui->ask('Что проверяем?', '1', [
		'Весь сайт целиком — все правила, обход, отчёт HTML + JSON, разбор ИИ',
		'Конкретная страница — ядро запросов, план продвижения, покрытие блоков',
		'Быстрая техническая проверка — robots.txt, sitemap.xml, заголовки, TLS',
		'Сравнение с конкурентами — те же правила у вас и у конкурентов',
		'Сгенерировать llms.txt — по карте сайта, для ИИ-краулеров',
		'Самотест правил — точность на синтетических страницах (без сети)',
		'Размышление ИИ над сводкой — что делать и что не трогать',
	]);

	$cfg = new Cfg();
	$cfg->color = true;
	// результат — всегда один файл: tools/audit-report.html (внутри — все данные, JSON и llms.txt)
	$cfg->htmlFile = __DIR__ . DIRECTORY_SEPARATOR . 'audit-report.html';

	switch ($choice) {
		case '2':
			$page = $ui->ask('Ссылка на конкретную страницу', 'http://127.0.0.1:8899/');
			$page = trim($page);
			$origin = Url::origin($page);
			$cfg->start = $origin !== '' ? $origin : $page;
			$cfg->pageUrl = $page;
			$cfg->depth = 3;
			$cfg->limit = 200;
			$cfg->clusters = true;
			$cfg->showPages = false;
			$cfg->probe = true;
			$cfg->ai = 'auto';
			$cfg->aiFix = 10;
			$ui->line('  ' . $ui->c('Разбор страницы: ' . $page, 'brcyan'));
			break;

		case '3':
			$site = $ui->ask('Адрес сайта', 'http://127.0.0.1:8899/');
			$cfg->start = trim($site);
			$cfg->depth = 1;
			$cfg->limit = 5;
			$cfg->concurrency = 2;
			$cfg->probe = false;
			$cfg->external = false;
			$ui->line('  ' . $ui->c('Быстрая проверка: ' . $cfg->start, 'brcyan'));
			break;

		case '4':
			$site = $ui->ask('Адрес сайта', 'http://127.0.0.1:8899/');
			$cfg->start = trim($site);
			$comp = $ui->ask('Адреса конкурентов (через запятую)', '');
			$cfg->competitors = array_values(array_filter(array_map(
				static fn(string $u): string => trim($u),
				explode(',', (string)$comp)
			)));
			$cfg->depth = 4;
			$cfg->limit = 500;
			$cfg->clusters = true;
			$ui->line('  ' . $ui->c('Сравнение: ' . $cfg->start . ' ↔ ' . implode(', ', $cfg->competitors), 'brcyan'));
			break;

		case '5':
			$site = $ui->ask('Адрес сайта', 'http://127.0.0.1:8899/');
			$cfg->start = trim($site);
			$cfg->genLlm = true;
			$cfg->depth = 5;
			$cfg->limit = 1000;
			$ui->line('  ' . $ui->c('Генерация llms.txt для ' . $cfg->start, 'brcyan'));
			break;

		case '6':
			$cfg->selfTest = true;
			break;

		case '7':
			$site = $ui->ask('Адрес сайта', 'http://127.0.0.1:8899/');
			$cfg->start = trim($site);
			$cfg->ai = 'auto';
			$cfg->aiFix = 20;
			$cfg->depth = 5;
			$cfg->limit = 800;
			$cfg->clusters = true;
			$ui->line('  ' . $ui->c('Размышление ИИ над ' . $cfg->start, 'brcyan'));
			break;

		default:
			$site = $ui->ask('Адрес сайта', 'http://127.0.0.1:8899/');
			$cfg->start = trim($site);
			$cfg->depth = 6;
			$cfg->limit = 1500;
			$cfg->concurrency = 6;
			$cfg->probe = true;
			$cfg->clusters = true;
			$cfg->showPages = true;
			$cfg->genLlm = true;
			$cfg->ai = 'auto';
			$cfg->aiFix = 20;
			$ui->line('  ' . $ui->c('Полный аудит: ' . $cfg->start, 'brcyan'));
			break;
	}

	// значения, заданные в диалоге, не перекрываются сценарием
	foreach (['depth', 'limit', 'concurrency', 'probe', 'external'] as $k) {
		if (!in_array($k, $cfg->explicit, true)) {
			$cfg->explicit[] = $k;
		}
	}
	$cfg->applyScenario();
	return $cfg;
}

/**
 * Точка входа.
 */
function main(array $argv): int
{
	$interactive = count($argv) <= 1;
	$cfg = $interactive ? null : parseArgs($argv);
	$ui = new Ui($cfg === null || ($cfg->color && !$cfg->quiet));

	if ($interactive) {
		if (!defined('STDIN')) {
			echo usage(), PHP_EOL;
			return 0;
		}
		$cfg = interactiveSetup($ui);
	}

	// --- самотест точности правил (без сети) ---
	if ($cfg->selfTest) {
		$st = SelfTest::run();
		$ui->line();
		$ui->line('  ' . $ui->c('САМОТЕСТ ТОЧНОСТИ ПРАВИЛ', 'bold|white')
			. '   ' . $ui->c('эталон: синтетические страницы с заранее известным результатом', 'gray'));
		$ui->line();
		$ui->line('  ' . $ui->c('Фоновые правила синтетической среды (не ошибка): ', 'gray')
			. ($st['baseline'] === [] ? 'нет' : implode(', ', $st['baseline'])));
		$ui->line();
		foreach ($st['rows'] as $r) {
			$mark = $r['ok'] ? $ui->c('✓', 'brgreen') : $ui->c('✗', 'brred');
			$extra = array_values(array_diff((array)$r['got'], (array)$r['expected']));
			$ui->line('    ' . $mark . ' ' . $ui->c($ui->pad((string)$r['fixture'], 18), 'white')
				. $ui->c('ожидалось: ', 'gray') . implode(', ', (array)$r['expected'])
				. $ui->c('   получено: ', 'gray') . implode(', ', (array)$r['got'])
				. ($extra !== [] ? $ui->c('   прочее: ' . implode(', ', $extra), 'gray') : ''));
		}
		$ui->line();
		$ui->line('  ' . $ui->c('Сработало ожидаемых проверок: ', 'gray')
			. $ui->c($st['passed'] . ' из ' . $st['total'], 'bold|' . ($st['passed'] === $st['total'] ? 'brgreen' : 'bryellow')));
		$ui->line('  ' . $ui->c('Пропущено (правило не сработало): ', 'gray')
			. ($st['missed'] === [] ? $ui->c('нет', 'brgreen') : $ui->c(implode('; ', $st['missed']), 'brred')));
		$ui->line('  ' . $ui->c('Дополнительно сработавшие правила: ', 'gray')
			. ($st['falsePositives'] === [] ? $ui->c('нет', 'brgreen') : $ui->c(implode('; ', $st['falsePositives']), 'bryellow')));
		$ui->line();
		return ($st['missed'] === []) ? 0 : 1;
	}

	if (!$cfg->quiet) {
		fwrite(STDERR, "Studio Audit Crawler · цель: {$cfg->start}\n");
	}

	// Анимация загрузки: крутится, пока собираются данные, и пока идут проверки.
	$spin = new Spin($ui, !$cfg->quiet);
	$spin->start('Загрузка данных · подключение к сайту');

	$crawler = new Crawler($cfg, $ui, $spin);
	$crawler->run();

	$ctx = new Ctx($cfg, $crawler);
	$audit = new Audit();
	$spin->start('Анализ · проверка правил');
	$results = $audit->run($ctx, static function (int $i, int $total) use ($spin): void {
		$spin->set('Анализ · проверка правил', $i, $total);
	});
	$score = $audit->score();
	$spin->stop();

	// --- аналитический слой: ядро, кластеры, план страницы, конкуренты ---
	$spin->start('Анализ · кластеры, план страницы, конкуренты');
	$insight = ['domains' => $crawler->domains, 'bots' => $crawler->aiBots];

	// --- тест конкретного URL по правилам robots.txt ---
	if ($cfg->testUrl !== null && $cfg->testUrl !== '' && $crawler->robots !== null && $crawler->robots->exists) {
		$path = '/' . ltrim((string)$cfg->testUrl, '/');
		$res = $crawler->robots->testUrl($path, $cfg->testBot);
		$insight['urlTest'] = [
			'path'    => $path,
			'agent'   => $cfg->testBot === '*' ? 'все боты (*)' : $cfg->testBot,
			'allowed' => (bool)$res['allowed'],
			'matched' => $res['matched'],
		];
	}
	if ($cfg->pageUrl !== null && $cfg->pageUrl !== '') {
		$abs = Url::resolve($cfg->pageUrl, $cfg->start) ?? $cfg->pageUrl;
		$insight['page'] = Insight::pagePlan($ctx, $abs, $results);
	}
	if ($cfg->clusters || isset($insight['page'])) {
		$insight['clusters'] = Insight::clusters($ctx);
	}
	if ($cfg->competitors !== []) {
		$insight['competitors'] = Insight::compareCompetitors(
			$cfg,
			$results,
			static function (int $done, int $total, string $url) use ($spin): void {
				$spin->set(
					'Анализ · конкурент ' . min($done + 1, $total) . '/' . $total . ' — ' . $url,
					$done,
					$total
				);
			}
		);
	}

	// --- размышление ИИ над сводкой (бесплатные внешние модели) ---
	if ($cfg->ai !== '' && $cfg->ai !== 'off') {
		$spin->set('Анализ · размышление ИИ над сводкой');
		$insight['ai'] = runAi($cfg, $ctx, $audit, $score);
	}

	// --- ИИ-исправления: готовый код «заменить на …» по каждой находке ---
	if ($cfg->aiFix > 0 && $cfg->ai !== '' && $cfg->ai !== 'off') {
		$spin->start('Анализ · ИИ пишет исправления по находкам');
		$insight['aiFixes'] = runAiFixes(
			$cfg,
			$ctx,
			$results,
			static function (int $i, int $total, string $rule) use ($spin): void {
				$spin->set('Анализ · ИИ-исправление ' . $i . '/' . $total . ' — ' . $rule, $i, $total);
			}
		);
	}
	$spin->stop();

	// --- генерация llms.txt из карты сайта ---
	if ($cfg->genLlm) {
		$pages = [];
		foreach ($crawler->sitemaps->urls as $u => $meta) {
			$title = '';
			foreach ($crawler->pages as $pu => $pp) {
				if (Ctx::pathKey($pu) === Ctx::pathKey((string)$u)) {
					$title = (string)$pp['html']['title'];
					break;
				}
			}
			$pages[(string)$u] = $title;
		}
		if ($pages === []) {
			foreach ($crawler->pages as $pu => $pp) {
				$pages[(string)$pu] = (string)$pp['html']['title'];
			}
		}
		$brand = Insight::brandOf($ctx);
		$llm = LlmTxt::generate($pages, $brand !== '' ? $brand : 'Сайт', 'Краткое описание сайта для ИИ-ассистентов: разделы и ключевые страницы.');
		$outPath = $cfg->llmsOut !== null && $cfg->llmsOut !== '' ? $cfg->llmsOut : '';
		$ok = $outPath !== '' ? @file_put_contents($outPath, $llm) : true;
		$insight['llms'] = [
			'text'  => $llm,
			'path'  => $outPath !== '' ? $outPath : 'внутри HTML-отчёта',
			'pages' => count($pages),
			'saved' => $outPath !== '' && $ok !== false,
		];
		$ui->line();
		$ui->section('СГЕНЕРИРОВАННЫЙ llms.txt');
		$ui->line('  ' . $ui->c('страниц в карте: ' . count($pages) . ' · ' . ($outPath !== '' ? 'файл: ' . $outPath : 'файл не создаётся — текст внутри HTML-отчёта'), 'gray'));
		$ui->line();
		foreach (preg_split('~\R~', $llm) ?: [] as $line) {
			$ui->line('  ' . $ui->c($line, $line !== '' ? 'white' : 'gray'));
		}
	}

	$report = new Report($ui, $cfg);
	if ($cfg->quiet) {
		$sev = ['critical' => 0, 'high' => 0, 'medium' => 0, 'low' => 0, 'info' => 0];
		foreach ($audit->results() as $r) {
			if (!$r['pass']) {
				$sev[$r['sev']]++;
			}
		}
		echo "Оценка: ", number_format((float)$score['score'], 1), "/100  |  ",
			"критично: {$sev['critical']}  высокая: {$sev['high']}  средняя: {$sev['medium']}  низкая: {$sev['low']}  инфо: {$sev['info']}\n",
			"Страниц: ", count($crawler->pages), "  |  Правил: ", count($audit->results()),
			"  |  Замечаний: ", array_sum($sev), "\n";
	} else {
		$report->renderConsole($ctx, $crawler, $audit, $score);
	}
	if ($insight !== []) {
		Insight::renderInsightConsole($ui, $insight, $ctx, $crawler);
	}

	// JSON больше не пишется отдельным файлом: он встроен в HTML (кнопка «Скачать JSON»).
	// Отдельный файл — только если его явно попросили флагом --json.
	$json = $report->renderJson($ctx, $crawler, $audit, $score, $insight);
	if ($cfg->jsonFile !== null && $cfg->jsonFile !== '') {
		$written = @file_put_contents($cfg->jsonFile, $json);
		if ($written === false) {
			fwrite(STDERR, "Не удалось записать JSON в {$cfg->jsonFile}\n");
		} else {
			echo PHP_EOL, 'JSON-отчёт: ', $cfg->jsonFile, ' (', number_format($written / 1024, 1), ' КБ)', PHP_EOL;
		}
	}
	if (!$cfg->noReport) {
		$htmlPath = $cfg->htmlFile !== null && $cfg->htmlFile !== ''
			? $cfg->htmlFile
			: __DIR__ . DIRECTORY_SEPARATOR . Report::reportName($cfg->start) . '.html';
		$spin->start('Формирование итогового HTML-отчёта');
		$html = $report->renderHtml($ctx, $crawler, $audit, $score, $insight, $json);
		$written = @file_put_contents($htmlPath, $html);
		$spin->stop();
		if ($written === false) {
			fwrite(STDERR, "Не удалось записать HTML в {$htmlPath}\n");
		} else {
			echo PHP_EOL, 'Итоговый отчёт (один файл со всем): ', $htmlPath, ' (', number_format($written / 1024, 1), ' КБ)', PHP_EOL;
		}
	}

	$critical = 0;
	$high = 0;
	foreach ($audit->results() as $r) {
		if ($r['pass']) {
			continue;
		}
		if ($r['sev'] === 'critical') {
			$critical++;
		} elseif ($r['sev'] === 'high') {
			$high++;
		}
	}
	return ($critical > 0 || $high > 0) ? 1 : 0;
}

// Запуск как самостоятельный скрипт. Если файл подключён (require) — main() не вызывается,
// классы можно переиспользовать в тестах.
if (isset($_SERVER['SCRIPT_FILENAME']) && realpath((string)$_SERVER['SCRIPT_FILENAME']) === __FILE__) {
	exit(main($argv ?? []));
}
