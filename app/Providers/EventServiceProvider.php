<?php

namespace App\Providers;

use Laravel\Lumen\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        \App\Events\LoanOfferAcceptedEvent::class => [
            \App\Listeners\LoanOfferAcceptedListener::class,
        ],
        \App\Events\WalletBalanceAdjustedEvent::class => [
            \App\Listeners\WalletBalanceAdjustedListener::class
        ],
        \App\Events\UserCreatedEvent::class => [
            \App\Listeners\UserCreatedListener::class
        ]
    ];
}
