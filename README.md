Веб приложение на Laravel 10 для учета расходов и доходов.

Код писался в основном на скорую руку и в свободное время, лишь бы завелось и хоть как-то работало.
Поэтому в нем много мест, которые можно улучшить.
Например, валидация форм, обработка ошибок, архитектура, тесты и т.д.

### Requirements
1. PHP 8.2 for Composer
2. Tool sail – [Laravel doc](https://laravel.com/docs/10.x/sail)
3. Docker

### Development environment, quick start
Run the following commands:
1. Clone the repository
2. Install dependencies, run: `composer install`
3. Run `sail up -d`
4. Run `cp .env.example .env`
5. Generate application key `sail artisan key:generate`
6. Run migrations `sail artisan migrate`
7. Run seeders `sail artisan db:seed`
8. Run `sail test`
9. Go to url `localhost`
10. Login as `default@example.com`, password: `password`

For debug, you can add to `.env` file:
```
SAIL_XDEBUG_MODE=debug
DEBUGBAR_OPEN_STORAGE=true
```

### Prerequisites (temporary)
* One Bill must be default=True
* One Currency must be active=True

### Database
For migrations run the following command:
Reset DB and data:
```
sail artisan migrate:refresh --seed
```
```
sail artisan migrate
```
For seeders run:
```
sail artisan db:seed
```
Seeders will create some data and user with email/password:
```
default@example.com
password
```

### Refresh Database from backup
1. Put backup file to `storage/app/backup` directory
2. Run the following command:
```
sail artisan backup:restore --reset &&
sail artisan cache:clear
```

### Telegram Bot
To test local Telegram Bot, you can use https://localxpose.io/ to expose your local server to the internet.

1. Add to `.env` file:
You can get Telegram user id from `@userinfobot`
Add mapping of user id in DB and user id in Telegram, for example:
```
TELEGRAM_USER_IDS='{"1": 863548221, "2": 625332123}'
```
```
TELEGRAM_BOT_TOKEN=your_telegram_bot_token
TELEGRAM_BOT_DOMAIN=https://your_localxpose_url`
```
2. Set webhook `sail artisan app:bot-set-webhook`

### Testing
In this project used PHPUnit for testing. To run tests, use the following command:
```
sail artisan test
```
Tests located in `tests/Feature` and `tests/Unit` directories.
Config located in `phpunit.xml` file.
