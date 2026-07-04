# Сторінка: Каталог турів

- **Маршрут:** `/tours/` (WP-сторінка з шаблоном)
- **Шаблон:** `page-templates/template-tours.php` (`Template Name: Каталог турів`)
- **Запит:** `mustsee_products()` (`template-parts/template-tours.php:17` → `inc/products.php:34`)

## Параметри URL

| Параметр | Дія | Приклад |
|----------|-----|---------|
| `?cat=<slug>` | фільтр за дочірньою категорією `tours` | `/tours/?cat=sea` |
| `?country=<назва>` | `meta LIKE _mustsee_countries` | `/tours/?country=Італія` |
| `?paged=<n>` | пагінація (6 на сторінку) | `/tours/?paged=2` |

## Блоки

1. Hero + пошук (ті самі партіали, що на головній).
2. Сайдбар фільтрів:
   - «Тип туру» — `mustsee_tour_filter_terms()` (дочірні категорії `tours`) `inc/products.php:78`;
   - «Країна» — список з `mustsee_config('countries')`.
   - На мобільному ховається під кнопкою (toggle `data-toggle="#catalog-filters"`).
3. Список карток-турів (горизонтальні) → ведуть на permalink товару.
4. Пагінація (`paginate_links`, кастомний рендер під стилі).
5. Lead-форма.

## Пастки

- Без `?cat` каталог показує **всю гілку `tours`** (включно з дочірніми `sea/locations/events/...`) і виключає `hotels` + `certificates` (`exclude_cats`).
- З `?cat=<slug>` `exclude_cats` **не застосовується** — показуються рівно товари цієї категорії.
- Фільтр «Країна» — це `meta LIKE`, тож «Італія» співпаде і з «Італія, Австрія». Це навмисно.
- Порожній результат → `mustsee_empty_notice()` (`inc/products.php`), а не порожня сітка.

## Звʼязки

- Модель товару → [business-rules/products.md](../business-rules/products.md)
- Сторінка туру → [tour.md](tour.md)
