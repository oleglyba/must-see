# Must See Travel

A WordPress storefront for a travel agency — built as a **pure user-facing frontend**.

- **Tours** come from an external **FastAPI** service (with bundled JSON fixtures as a fallback, so the site runs without any backend).
- **WordPress** handles page rendering, static content (news, info pages, menus) and lead capture.
- **No WooCommerce** — the theme has zero e-commerce plugin dependencies.

---

## Tech stack

| Layer | Technology |
|---|---|
| CMS / rendering | WordPress, custom `mustsee` theme, PHP 8 |
| Styling | Tailwind CSS, self-hosted Inter font, inlined critical CSS |
| Frontend JS | Vanilla JS (`assets/js/theme.js`) — menus, tabs, booking flow, AJAX forms |
| Tour data | External FastAPI + local JSON fixtures |
| Plugins | WP Super Cache, Rank Math SEO |

---

## How tour data works

All tour data flows through one client — `inc/api.php`:

```
Template  →  mustsee_tours() / mustsee_tour()  →  mustsee_api_get()
                                                       │
                                     MUSTSEE_API_URL set?
                                     ├── yes → live API (cached 10 min in transients)
                                     └── no / request failed → assets/fixtures/*.json
```

To connect a live API, add one constant:

```php
// wp-config.php
define( 'MUSTSEE_API_URL', 'https://api.example.com' );
```

No constant — no problem: the theme serves the bundled fixtures and every page keeps working.

### API contract

| Endpoint | Returns |
|---|---|
| `GET /tours` | `{ "items": [Tour], "total": int, "pages": int }` |
| `GET /tours/{slug}` | `Tour` |
| `GET /categories` | `[ { "slug": str, "name": str } ]` |

Supported query params for `/tours`: `category`, `country`, `page`, `per_page`, `featured`.

**Tour object:**

```jsonc
{
  "slug": "weekend-budapest-vienna",
  "title": "Вікенд у Будапешт + Відень",
  "price_eur": 99,
  "days": "4 дні",
  "date_range": "19.07 — 22.07",
  "countries": ["Угорщина", "Австрія"],
  "departure_city": "Львів",
  "places": "Будапешт, Відень",
  "stars": "",
  "departures": [{ "date": "19.07.2026", "price": "99 EUR" }],
  "route": ["Львів", "Будапешт", "Відень", "Львів"],
  "program": [{ "day": "День 1", "items": ["Виїзд зі Львова о 17:00"] }],
  "images": [],
  "badges": ["ТОП тижня"],
  "categories": ["weekend", "top"],
  "featured": true,
  "description": "..."
}
```

The fixtures in `assets/fixtures/` are the reference implementation of this contract — build the FastAPI responses to match them.

---

## Theme structure

```
wordpress/wp-content/themes/mustsee/
├── inc/
│   ├── api.php          FastAPI client: cache + fixture fallback
│   ├── tours.php        Tour helpers: queries, cards, images
│   ├── routing.php      /tours/{slug} rewrite → tour-single.php
│   ├── forms.php        Lead / newsletter / booking AJAX handlers
│   ├── auth.php         Partner login and registration
│   ├── cpt.php          "Reviews" post type
│   └── services.php     JSON responses, rate limiting, logging, mail queue
├── tour-single.php      Tour page rendered from API data
├── page-templates/      Catalog, booking, cabinet, info, contacts, news
├── template-parts/      Hero, cards, nav dropdown, breadcrumbs, forms
└── assets/              CSS (Tailwind), JS, fonts, fixtures
```

---

## Local setup

1. **Serve** the `wordpress/` directory from a local host (e.g. `mustsee.test`) and create a database. `wp-config.php` is not tracked — create your own.
2. **Activate** the `mustsee` theme. The `/tours/{slug}` rewrite rules are flushed automatically on the first load after a theme version change.
3. **Create pages** for the templates you need: catalog page at `/tours/`, booking page at `/booking/`, and so on.

### Rebuild CSS

```bash
cd wordpress/wp-content/themes/mustsee
npm install
npx tailwindcss -c tailwind.config.js \
  -i assets/css/tailwind.src.css \
  -o assets/css/tailwind.css --minify
```

---

## Booking flow

The multi-step booking form (departure → seats → tourist details) posts to the `mustsee_booking` AJAX endpoint, which:

1. validates the nonce and rate-limits by IP,
2. stores the request as a lead post in wp-admin,
3. notifies the admin by email.

When the FastAPI booking endpoint is ready, only the submit target changes — the UI stays as is.
