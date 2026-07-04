# Сторінка: Бронювання

- **Маршрут:** `/booking/?tour=<id>`
- **Шаблон:** `page-templates/template-booking.php` (`Template Name: Бронювання туру`)
- **JS:** `MustSee.booking` (`assets/js/theme.js:74`)

## Вхід

- `?tour=<id>` → `wc_get_product($id)`; заголовок і ціна беруться з товару (`template-booking.php` верх).
- Без `tour` — fallback-назва/ціна (демо), і «Забронювати» веде просто в кошик.
- На контейнер `[data-booking]` пишуться `data-product` та `data-cart-url` (`template-booking.php:37`).

## Кроки (3)

1. **Місто виїзду** — к-сть дорослих/дітей + таблиці: рейси, тип розміщення, додаткові послуги (демо-дані в шаблоні).
2. **Вибір місць** — сітка автобуса 4×13; недоступні місця `disabled`; вибір тогглить класи; лічильник `[data-seat-count]`.
3. **Дані туристів** — форма на кожне вибране місце, генерується з `<template data-tourist-template>` (`__SEAT__` → номер).

Навігація кроків і генерація форм — `MustSee.booking.render()` / `buildTouristForms()`.

## Завершення

«Забронювати» (`[data-step-submit]`): додає товар у кошик із **кількістю = к-сть вибраних місць** і веде в кошик:
`{$cartUrl}?add-to-cart={pid}&quantity={seats}` (`assets/js/theme.js:161`). Захищено прапорцем від подвійного кліку.

## Пастки

- Комісія в підсумку — `mustsee_commission_rate()` (`functions.php:218`, дефолт 12%, фільтрується), а не хардкод.
- Дані рейсів/розміщення/послуг — **демо в шаблоні**, ще не привʼязані до товару (наступний крок розвитку).
- Якщо місць не вибрано — у кошик піде `quantity=1`.

## Звʼязки

- Повний флоу грошей → [business-rules/booking.md](../business-rules/booking.md)
