# Client example for a payment

Routes processing through App\Http\ShopController.php. Request processing through App\Services\Cardinity.php. Payment details temporary saved in Redis cache hash'es.

## Setup

- Run `composer install`.
- Place api credentials (CONSUMER_KEY, CONSUMER_SECRET) to `.env`, then `php artisan confing:cache` to prepare cache values before running the application.
Redis server is required.
