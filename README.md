### Requirements
* PHP 8.3
### Prerequisites (temporary)
* One Bill must be default=True
* One Currency must be active=True

### Development environment
Run the following commands:
1. Clone the repository
2. Install dependencies `composer install`
3. Copy `.env.example` to `.env` and set the following environment variables:
```
DB_CONNECTION=pgsql
DB_HOST=pgsql
DB_PORT=5432
DB_DATABASE=your_database_name
DB_USERNAME=your_database_username
DB_PASSWORD=your_database_password
```
4. Run `sail up -d`
5. Generate application key `sail artisan key:generate`
6. Run migrations `sail artisan migrate`
7. Run seeders `sail artisan db:seed`

Add to `.env` file:
```
SAIL_XDEBUG_MODE=debug
DEBUGBAR_OPEN_STORAGE=true
```

### Telegram Bot
To test local Telegram Bot, you can use https://localxpose.io/ to expose your local server to the internet.

1. Add to `.env` file:
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
