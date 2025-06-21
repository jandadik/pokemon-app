<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use App\Models\UserCollection;
use App\Policies\UserCollectionPolicy;
use App\Models\UserCollectionItem;
use App\Policies\CollectionItemPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string|bool>
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
        UserCollection::class => UserCollectionPolicy::class,
        UserCollectionItem::class => CollectionItemPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        // Super-admin má všechna oprávnění
        Gate::before(function ($user, $ability) {
            return $user->hasRole('super-admin') ? true : null;
        });
    }
} 