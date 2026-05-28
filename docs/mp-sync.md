# Mercado Pago Sync

Автоматическая загрузка транзакций Mercado Pago как Operations.

## Что делает

Раз в сутки (06:00 UTC) тянет транзакции из MP API за последние сутки и создаёт Operations в БД. Дубли исключены через `external_id` (UNIQUE индекс). Поддерживает несколько MP-аккаунтов.

---

## Конфигурация

### .env

```
MP_ACCESS_TOKEN_1=APP_USR-...
MP_USER_ID_1=1
# MP_ACCESS_TOKEN_2=APP_USR-...
# MP_USER_ID_2=2
```

### config/mercadopago.php

Массив `accounts` — по одному элементу на каждый MP-аккаунт:

```php
'accounts' => [
    ['access_token' => env('MP_ACCESS_TOKEN_1'), 'user_id' => env('MP_USER_ID_1')],
],
```

---

## Маппинг описаний → категория + место

Хранится в таблице `mercado_pago_mappings`. Управляется вручную через БД.

| поле | тип | описание |
|------|-----|---------|
| `keyword` | string UNIQUE | ключевое слово для поиска в description (case-insensitive) |
| `category_id` | FK nullable | категория операции |
| `place_name` | string nullable | название места — создаётся автоматически если не существует |
| `is_default` | bool | fallback-строка (keyword = `__default__`) |

Логика: первое совпадение через `str_contains(strtolower($description), $keyword)`. Если ничего — строка с `is_default = true`.

Начальные маппинги засеяны в `MercadoPagoMappingSeeder`: netflix, spotify, apple, microsoft, google → Entretenimiento; uber, didi, subte → Transporte; jumbo → Supermercado; iplan → Internet; default → Otros.

---

## Схема БД

```sql
-- operations
external_id     VARCHAR NULL UNIQUE  -- MP payment.id
external_source VARCHAR NULL         -- 'mercadopago'

-- mercado_pago_mappings (новая таблица)
id, keyword, category_id, place_name, is_default, timestamps
```

---

## Запуск

```bash
# первый запуск — история за 90 дней
sail artisan app:mp-sync --days=90

# обычный cron (по умолчанию --days=1)
sail artisan app:mp-sync
```

Перед первым запуском:
1. Создать счёт "Mercado Pago" в приложении для нужного пользователя
2. Прописать `MP_ACCESS_TOKEN_1` и `MP_USER_ID_1` в `.env`

---

## Архитектура

```
MercadoPagoSync (Command)
  └── MercadoPagoService($token)       GET /v1/payments/search, пагинация
  └── MercadoPagoMappingService        description → category_id + place_id (из БД)
  └── OperationService::createFromExternal()  idempotent INSERT
```

**Резолв счёта и валюты:**
- Bill: `where('name', 'Mercado Pago')->where('user_id', $userId)`
- Currency: `where('name', 'ARS')`

**Тип операции:**
- `operation_type = regular_payment` → Expense
- всё остальное (money_transfer и т.д.) → Income

---

## Фильтр в UI

На странице `/operations` — кнопка **MP** рядом с остальными фильтрами. Показывает только операции с `external_source = 'mercadopago'`.

---

## Notes

- Повторный запуск безопасен — дубли пропускаются на уровне кода и UNIQUE индекса в БД
- Транзакции со статусом != `approved` пропускаются
- `transaction_amount` в MP всегда положительный; тип (Income/Expense) определяется по `operation_type`
