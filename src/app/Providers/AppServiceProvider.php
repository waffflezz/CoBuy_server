<?php

namespace App\Providers;

use App\Models\Product;
use App\Models\ShoppingList;
use App\Policies\ProductPolicy;
use App\Policies\ShoppingListPolicy;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use SocialiteProviders\Manager\SocialiteWasCalled;
use SocialiteProviders\Yandex\Provider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Event::listen(function (SocialiteWasCalled $event) {
            $event->extendSocialite('yandex', Provider::class);
        });

        Gate::policy(ShoppingList::class, ShoppingListPolicy::class);
        Gate::policy(Product::class, ProductPolicy::class);
    }
}
