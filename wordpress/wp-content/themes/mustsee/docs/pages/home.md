# Сторінка: Головна

- **Маршрут:** `/` (статична головна, `page_on_front`)
- **Шаблон:** `front-page.php`
- **Дані:** WooCommerce-товари + дописи WP + CPT `review` + меню `cities`.

## Блоки зверху вниз

| # | Блок | Джерело даних | Код |
|---|------|---------------|-----|
| 1 | Hero | перший **featured** тур (fallback — останній у `tours`) | `template-parts/hero.php` |
| 2 | Пошук туру | форма → `/tours/` | `template-parts/search-bar.php` |
| 3 | Подарунковий сертифікат | посилання на категорію `certificates` | `front-page.php` (банер) |
| 4 | ТОП/Рекомендації/Гарячі | статичні плитки | `front-page.php` |
| 5 | Найближчі виїзди | `mustsee_products(featured)` → fallback `tours` | `front-page.php`, картка — `mustsee_tour_card()` `inc/products.php:92` |
| 6 | Популярні міста | меню `cities` → fallback список | `front-page.php`, `mustsee_menu_items()` `functions.php:226` |
| 7 | Тури на море | категорія `sea` | `front-page.php` |
| 8 | Події та свята | категорія `events` | `front-page.php` |
| 9 | Популярні локації | категорія `locations` | `front-page.php` |
| 10 | Тури по категоріям | `mustsee_tour_filter_terms()` `inc/products.php:78` | `front-page.php` |
| 11 | Новини | останні 3 дописи `post` | `front-page.php`, `template-parts/article-card.php` |
| 12 | Lead-форма | статична | `template-parts/lead-form.php` |
| 13 | Відгуки | `mustsee_get_reviews()` (CPT `review`) | `front-page.php` |
| 14 | SEO-текст | статичний | `front-page.php` |
| 15 | Розсилка | статична | `template-parts/newsletter.php` |

## Пастки

- Секції 5/7/8/9/11/13 **ховаються повністю**, якщо немає відповідних товарів/дописів/відгуків (немає порожніх заголовків).
- Hero-зображення завантажується `eager` + `fetchpriority=high` (LCP) — на відміну від решти `lazy` (`template-parts/hero.php`).
- «Найближчі виїзди» = саме **featured**-товари; якщо жоден тур не позначено featured — показуються останні з категорії `tours`.

## Звʼязки

- Дані товарів → [business-rules/products.md](../business-rules/products.md)
- Перф/зображення → [business-rules/assets-performance.md](../business-rules/assets-performance.md)
