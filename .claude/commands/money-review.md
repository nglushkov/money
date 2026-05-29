# Money App Review

Подключись к продакшн БД и помоги провести ревью операций или других сущностей.

## Подключение к БД

```bash
ssh nglushkov@212.227.241.35
mysql -u$(grep "^DB_USERNAME" /var/www/money/.env | cut -d= -f2) \
      -p$(grep "^DB_PASSWORD" /var/www/money/.env | cut -d= -f2) \
      $(grep "^DB_DATABASE" /var/www/money/.env | cut -d= -f2)
```

## ID основных сущностей (категории)

| ID | Название |
|----|----------|
| 4  | Продукты |
| 5  | Хоз. товары |
| 6  | Алкоголь |
| 7  | Табак |
| 8  | Ребёнок |
| 9  | Такси |
| 10 | Доставка еды |
| 11 | Здоровье, лечение |
| 12 | Онлайн подписки |
| 13 | Видеоигры |
| 20 | Услуги |
| 21 | Рестораны |
| 23 | Одежда, обувь |
| 40 | Monotributo |
| 43 | Общественный транспорт |
| 45 | Спорт |
| 46 | Интернет |

Полный список: `SELECT id, name FROM categories ORDER BY name;`

## ID основных мест (places)

| ID | Название |
|----|----------|
| 2  | Kiosco |
| 7  | DiDi |
| 8  | Uber |
| 19 | Spotify |
| 46 | Прочее |
| 55 | Netflix |
| 61 | Jumbo |
| 104| AFIP |
| 117| Mercado Pago |
| 151| IPlan |
| 171| Apple |
| 193| Adidas |
| 207| Soy Gamer |
| 218| Reebok |
| 219| Subte |

Полный список: `SELECT id, name FROM places ORDER BY name;`

## Типовые запросы

### MP-операции требующие ревью
```sql
SELECT o.id, DATE(o.date) as date, o.amount, o.type, c.name as category, p.name as place, o.notes
FROM operations o
LEFT JOIN categories c ON c.id = o.category_id
LEFT JOIN places p ON p.id = o.place_id
WHERE o.external_source = 'mercadopago'
ORDER BY o.date;
```

### MP-операции с Income типом (артефакты старого синка)
```sql
SELECT id, DATE(date) as date, amount, notes
FROM operations
WHERE external_source = 'mercadopago' AND type = 'Income'
ORDER BY date;
```

### Операции без категории или с дефолтной категорией (Услуги)
```sql
SELECT o.id, DATE(o.date) as date, o.amount, o.notes
FROM operations o
WHERE o.external_source = 'mercadopago' AND o.category_id = 20
ORDER BY o.date;
```

### Посмотреть все маппинги MP
```sql
SELECT m.keyword, c.name as category, p.name as place, m.is_default
FROM mercado_pago_mappings m
LEFT JOIN categories c ON c.id = m.category_id
LEFT JOIN places p ON p.id = m.place_id
ORDER BY m.is_default DESC, m.keyword;
```

### Детали одной операции через MP API
```bash
MP_TOKEN=$(grep "^MP_ACCESS_TOKEN_1" /var/www/money/.env | cut -d= -f2)
EXT_ID=$(mysql ... -se "SELECT external_id FROM operations WHERE id=XXXX;")
curl -s -H "Authorization: Bearer $MP_TOKEN" \
  "https://api.mercadopago.com/v1/payments/$EXT_ID" | python3 -m json.tool
```

## Типовые правки

### Исправить операцию
```sql
UPDATE operations SET type='Expense', category_id=4, place_id=2 WHERE id=XXXX;
```

### Исправить дату (timezone-сдвиг)
```sql
UPDATE operations SET date='2026-05-11 00:30:00' WHERE id=XXXX;
```

### Удалить операцию (только после явного согласования)
```sql
DELETE FROM operations WHERE id=XXXX;
```

### Добавить маппинг
```sql
INSERT INTO mercado_pago_mappings (keyword, category_id, place_id, is_default, created_at, updated_at)
VALUES ('keyword', CAT_ID, PLACE_ID, 0, NOW(), NOW())
ON DUPLICATE KEY UPDATE category_id=VALUES(category_id), place_id=VALUES(place_id), updated_at=NOW();
```

## Важно

- **Удалять на проде только после явного согласования пользователя**
- При ревью показывать операции по одной, ждать решения пользователя
- Для опознания операции — дёргать MP API по external_id
- Timezone: MP возвращает даты с offset -04:00, хранить нужно в America/Argentina/Buenos_Aires

$ARGUMENTS
