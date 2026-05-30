# Design System

## Principles

- **Clean & minimal** — светлый фон, много пространства, без лишнего шума
- **Functional** — каждый элемент несёт смысл, нет декора ради декора
- **Mobile-first** — нижняя навигация на мобиле, форма перед сайдбарами
- **Consistent color coding** — операции всегда кодируются цветом

## Color Palette

| Токен | Hex | Назначение |
|-------|-----|-----------|
| `--c-bg` | `#f0f4f8` | Фон страницы |
| `--c-surface` | `#ffffff` | Карточки, формы, тулбары |
| `--c-border` | `#e2e8f0` | Границы всех элементов |
| `--c-text` | `#1a202c` | Основной текст |
| `--c-muted` | `#718096` | Вторичный текст, подписи, лейблы форм |
| `--c-accent` | `#16a34a` | Акцент (зелёный, primary-цвет приложения) |
| `--c-accent-dark` | `#15803d` | Hover акцент |
| `--c-income` | `#16a34a` | Доход |
| `--c-expense` | `#dc2626` | Расход |
| `--c-transfer` | `#2563eb` | Перевод |
| `--c-exchange` | `#7c3aed` | Обмен валюты |

## Typography

- **Font**: Inter (Google Fonts) → fallback: system-ui
- **Base size**: 0.9375rem (15px)
- **Form labels**: 0.8rem, uppercase, letter-spacing 0.04em, `--c-muted`
- **Section headers**: 0.72rem, uppercase, letter-spacing 0.06em, `--c-muted`

## Shadows & Radius

| Токен | Значение |
|-------|----------|
| `--radius` | `0.75rem` — карточки, модалки |
| `--radius-sm` | `0.5rem` — кнопки, инпуты, dropdown |
| `--shadow-sm` | `0 1px 2px rgb(0 0 0 / .05)` — subtle |
| `--shadow` | `0 1px 3px rgb(0 0 0 / .10)` — карточки |
| `--shadow-md` | `0 4px 6px rgb(0 0 0 / .10)` — dropdown, модалки |

## Иконки

Bootstrap Icons (`bi-*`) через CDN. Никаких эмоджи в UI.

| Сущность | Иконка |
|---------|--------|
| Expense | `bi-arrow-down` (красный круг) |
| Income | `bi-arrow-up` (зелёный круг) |
| Transfer | `bi-arrow-left-right` (синий круг) |
| Exchange | `bi-currency-exchange` (фиолетовый круг) |
| Home | `bi-house-fill` |
| Bills | `bi-credit-card-fill` |
| New | `bi-plus-lg` |
| Sync | `bi-arrow-repeat` |

## Компоненты

### Navbar (`.navbar-money`)
Тёмно-зелёный (`#166534`). На десктопе горизонтальное меню. Бренд с иконкой `bi-wallet2`.

### Mobile Bottom Nav (`.mobile-bottom-nav`)
Отображается только на `< 768px`. Фиксированный внизу. Структура:
- Home, Bills, **+ FAB** (по центру, акцентный зелёный круг), Reports, Exchanger
- Тело страницы получает `padding-bottom: 5rem` на мобиле

### Page Toolbar (`.page-toolbar`)
Белая карточка с `--shadow-sm`. Flex-row с `.toolbar-left` и `.toolbar-right`.
На мобиле — `.filter-pills-scroll` для горизонтального скролла пилюль.

### Filter Pills (`.filter-pill`)
Округлые кнопки-фильтры. Состояния:
- Default: прозрачный фон, серая граница
- `.pill-active` — зелёный (All/Ops/Transfers/Exchanges)
- `.pill-active-mp` — синий
- `.pill-active-draft` — янтарный

### Move Row (`.move-row`)
Flex-строка с иконкой, телом (title + subtitle) и правой частью (сумма, бейдж).
Иконка — круг с цветом по типу операции.
Клик по всей строке → переход на детали. `onclick="event.stopPropagation()"` на кнопках действий.

### Form Card (`.form-card`)
Белый блок с `--radius` и `--shadow-sm`. Форма создания/редактирования.

### Type Toggle
Expense/Income — radio-кнопки, стилизованные как toggle. Не `<select>`.

### Amount Input (`.input-amount`)
Font-size 1.625rem, bold, text-align right. Всегда первое поле в форме операции.

### Quick-pick Sidebar (`.quickpick-card`)
Боковые карточки с top-категориями/bills/местами. На мобиле — под формой (order: 2 и 3 против order: 1 у формы).

## CSS файл

`public/assets/css/app.css` — все кастомные стили. Подключается после Bootstrap и Tom Select.

## Правила при разработке новых страниц

1. Всегда использовать CSS-переменные из этого документа, не хардкодить цвета
2. Новые страницы — контент через `@yield('content')` в `page-content`
3. Формы — в `.form-card`, лейблы с классом `.form-label`
4. Иконки — только Bootstrap Icons, не эмоджи
5. Мобиль: проверять что элементы не перекрываются bottom nav (padding-bottom уже есть на body)
6. Новые типы фильтров — использовать `.filter-pill` с соответствующим `pill-active-*` классом
7. Таблицы с транзакциями — всегда `.moves-card` + `.move-row` структура (не `<table>`)
