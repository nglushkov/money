<?php

namespace App\Providers;

use App\Events\CurrencyProcessed;
use App\Events\RateProcessed;
use App\Listeners\ClearDefaultCurrencyCache;
use App\Listeners\ClearRateCache;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;
use App\Events\MoveProcessed;
use App\Listeners\ClearBillAmountCache;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        MoveProcessed::class => [
            ClearBillAmountCache::class,
        ],
        CurrencyProcessed::class => [
            ClearDefaultCurrencyCache::class,
        ],
        RateProcessed::class => [
            ClearRateCache::class,
        ],
    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        //
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     */
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}
