# Must See Travel — документація теми

Жива доменна документація: описує **що є** і **як поводиться**, а не «як переписати».
Усі посилання ведуть у реальний код у форматі `file:line` (рядки можуть зміщуватися — орієнтуйся на назву функції поруч).

## Структура

- [`pages/`](pages/) — огляд кожної сторінки: маршрут → шаблон → блоки зверху вниз.
- [`business-rules/`](business-rules/) — правила за доменами (товари, бронювання, продуктивність).

## Сторінки

- [Головна](pages/home.md) — `front-page.php`
- [Каталог турів](pages/catalog.md) — `page-templates/template-tours.php`
- [Сторінка туру](pages/tour.md) — `woocommerce/content-single-product.php`
- [Бронювання](pages/booking.md) — `page-templates/template-booking.php`

## Бізнес-правила

- [Товари (тури / готелі / сертифікати)](business-rules/products.md)
- [Бронювання → кошик → каса](business-rules/booking.md)
- [Продуктивність ассетів (critical CSS / defer / WebP)](business-rules/assets-performance.md)

## Базові факти

- Стек: WordPress + WooCommerce + Tailwind (зібраний CSS), vanilla JS (`window.MustSee`).
- Локальний домен: `http://mustsee.test`, БД `wordpress_mustsee` (стара `wordpress_shop` не чіпається).
- SEO/sitemap/canonical/OGP/Schema — повністю на **Rank Math**, тема свого SEO не додає (лише `title-tag` + `wp_head()`).
- Контент наповнюється через WooCommerce; новини — звичайні дописи WP; відгуки — CPT `review`.
- Збірка стилів: `npm install` → `npm run build` (collect critical + main). Деталі — [assets-performance](business-rules/assets-performance.md).

## Конвенції

- Префікс усіх функцій/мета — `mustsee_` / `_mustsee_`.
- Єдине джерело статичних лейблів і fallback-меню — `mustsee_config()` (`functions.php:105`).
- Єдина контейнерна обгортка — клас `.container-site`.
- «Грошова» логіка — через сервісні хелпери (напр. `mustsee_commission_rate()`, `functions.php:218`).
