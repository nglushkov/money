# Architecture

## Overview

**Money** is a personal finance tracking web application for recording income, expenses, currency exchanges, and transfers across multiple accounts. It supports multiple users sharing a common pool of accounts, tracks both fiat and cryptocurrency assets, fetches exchange rates from external APIs automatically, and sends Telegram notifications for planned expenses.

The project is self-hosted and intended for personal/family use. There is no public registration — users are created via seeders or directly in the database. Authentication uses Laravel's session-based auth (no API tokens for the web UI).

## Stack

| Component | Technology | Version |
|-----------|-----------|---------|
| Language | PHP | ^8.1 (runtime: 8.3 in Sail) |
| Framework | Laravel | ^10.10 |
| Database | MySQL | 8.0 |
| Cache | Memcached | alpine (via Sail) |
| Frontend templating | Blade + vanilla HTML/CSS | — |
| Frontend build | Vite | ^5.0 |
| Auth | Laravel session auth | — |
| Telegram | irazasyed/telegram-bot-sdk | ^3.13 |
| HTTP client | Guzzle (via Laravel Http facade) | ^7.2 |
| Arbitrary-precision math | PHP bcmath extension | built-in |
| Backups | spatie/laravel-backup | ^8.6 |
| Dev environment | Laravel Sail | ^1.18 |
| Testing | PHPUnit | ^10.1 |

## Repository Structure

```
app/
  Console/Commands/   # Artisan commands: rate fetching, Telegram webhook, notifications
  Http/
    Controllers/      # One controller per domain entity (CRUD + custom actions)
    Requests/         # Form request validation classes (Store* / Update*)
  Models/             # Eloquent models
    Enum/             # PHP enums: OperationType (Expense/Income), MoveType
    Interfaces/       # Copyable interface (operations can be copied)
    Scopes/           # Global scope: IsNotCorrectionScope (hides correction ops by default)
  Service/            # Business logic services per entity
  Helpers/            # MoneyHelper (bcmath wrappers), MoneyFormatter, DateTimeFormatter
  Dto/                # CurrencyAmountDto — currency + amount pair returned by Bill
  Enum/               # App-level enums: CacheKey, CacheTag, StorageFilePath, Time
  Events/             # Domain events: CurrencyProcessed, MoveProcessed, RateProcessed
  Listeners/          # Cache invalidation listeners triggered by events
  Policies/           # Laravel authorization policies per model

database/
  migrations/         # All schema migrations (append-only, dated)
  seeders/            # Dev seed: default user (default@example.com / password), base data
  factories/          # Eloquent model factories for testing

resources/views/      # Blade templates, one subdirectory per entity
routes/
  web.php             # All application routes (auth-guarded + login + Telegram webhook)
  api.php             # Stub (only /user via Sanctum — unused by the app itself)

tests/
  Feature/            # Feature (HTTP-level) tests
  Unit/               # Unit tests

config/               # Standard Laravel config files
storage/app/backup/   # Backup destination (spatie/laravel-backup)
docker-compose.yml    # Sail services: app (PHP 8.3), MySQL 8.0, Memcached
```

## Key Concepts

### Move (base entity)
`Move` is the Eloquent base class for all financial movements. It fires `MoveProcessed` events on create/update/delete, which trigger cache invalidation for affected Bill balances.

There are three concrete Move types:
- **Operation** — an income or expense tied to a Bill, Category, Currency, and optional Place.
- **Transfer** — movement of funds from one Bill to another (same or different currency, with an optional exchange rate).
- **Exchange** — a currency conversion within a single Bill (from one currency to another at a given rate).

### Bill
A Bill is an account or wallet. Key attributes:
- `user_id` — owner (NULL means the Bill is "common"/shared by all users).
- `is_crypto` — marks the Bill as a crypto portfolio (affects which currencies appear in balance calculations).
- `default` — exactly one non-crypto Bill must have `default = true`; used as the default account in forms.

Bill balance has no stored column. It is computed on-demand by summing the initial amounts (`bill_currency_initial` pivot), all operations, transfers, and exchanges for each currency. The result is cached in Memcached forever (key: `bill_amount_{bill_id}_currency_{currency_id}`) and invalidated via events.

### Currency
Currencies can be fiat or crypto (`is_crypto` flag). One fiat currency and one crypto currency carry `is_default = true` — these are the reference currencies for rate conversion and display. Default currency objects are also cached forever in Memcached.

### Rate
A `Rate` row stores the exchange rate between two currencies on a specific date (from the default currency to a target currency). Lookups use "find the most recent rate on or before the operation date" — no exact-date match is required. Rates are cached per `(currency_id, date)` using Memcached tags (`currency_rates` tag).

### ExternalRate
`ExternalRate` stores raw rates fetched from external APIs (cbr-xml-daily.ru for USD/RUB, dolarapi.com for USD/ARS). These are for reference/display; the system also writes a corresponding `Rate` row (for USD/RUB only) to use in actual conversions.

### CryptoBill
A `CryptoBill` row tracks the total amount invested (in the default crypto currency) for a given Bill + Currency pair. Used to calculate profit/revenue on the crypto portfolio page.

### PlannedExpense
Recurring expense reminders with a configurable `reminder_days` lead-time. The `app:notify-planned-expense` command runs daily at 10:00 and sends Telegram messages to the bill owner.

### Money arithmetic
All monetary calculations use PHP's `bcmath` extension via `MoneyHelper`. Scale is 18 for internal calculations, 8 for short display values. This avoids floating-point precision loss for both fiat and crypto amounts.

### Correction Operations
When a Bill's actual balance differs from the recorded balance, `Bill::correctAmount()` creates an Operation with `is_correction = true`. A global Eloquent scope (`IsNotCorrectionScope`) hides these from all normal queries; to see them, call `withoutGlobalScope(IsNotCorrectionScope::class)` explicitly.

### Cache invalidation flow
```
Operation/Transfer/Exchange saved
  → MoveProcessed event
    → ClearBillAmountCache listener  (forgets all bill_amount_* keys for affected bills)

Currency saved
  → CurrencyProcessed event
    → ClearDefaultCurrencyCache listener  (forgets default_currency / default_crypto_currency keys)

Rate saved
  → RateProcessed event
    → ClearRateCache listener  (flushes the entire currency_rates Memcached tag)
```

### Authorization
Each model has a Policy class. Policies enforce that users can only modify their own data (or common resources). The `auth` middleware wraps all routes except login and the Telegram webhook.

### Telegram Bot integration
The bot handles webhook POSTs at `/{TELEGRAM_BOT_TOKEN}/webhook`. The token appears directly in the route URL (via `env()`), which means route enumeration would expose the token. The `app:bot-set-webhook` command registers the webhook URL with Telegram.

## Known Quirks

- **Telegram token in route URL** — `routes/web.php` uses `env('TELEGRAM_BOT_TOKEN')` directly as a URL path segment. This is a deliberate simplicity trade-off (Telegram's recommended approach for webhook security), but the token is visible in server access logs.
- **No stored balance column** — Bill balance is always recomputed from scratch by aggregating all operations + transfers + exchanges. This is correct but O(n) in the number of transactions. Memcached caching makes it fast in practice, but a stale cache (e.g. after `cache:clear`) will cause a full recompute on next access.
- **`is_correction` global scope** — `Operation` has `IsNotCorrectionScope` applied as both a `#[ScopedBy]` attribute and a `booted()` call, which means the scope is registered twice. In practice Laravel deduplicates this, but it is redundant and could confuse a reader.
- **`correctAmount` is on the Bill model** — a `@todo: Move to service` comment exists in `Bill::correctAmount()`. The method creates Operations directly from the model, mixing persistence logic into the model layer.
- **Queue is sync** — `QUEUE_CONNECTION=sync` in `.env`. No queue worker is needed, but all queued jobs block the HTTP request.
- **No CI/CD pipeline** — there is no `.github/`, `.gitlab-ci.yml`, or equivalent. All testing is manual (`sail artisan test`).
- **ARS rates are stored only in ExternalRate** — `GetUsdArsRates` does not create a `Rate` row (unlike `GetRates` for USD/RUB), so ARS rates are available on the external-rates page but cannot be used for conversion in operations.
- **bcmath scale 18 internal / 8 short** — crypto amounts use scale 18 throughout; display calls use scale 8. Mixing scales in output formatting is implicit and depends on which helper is called.
