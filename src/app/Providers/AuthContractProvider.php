<?php

namespace App\Providers;

use App\Contracts\Authorization\AuthContact;
use App\Managers\Authorization\AuthorizationManager;
use Illuminate\Support\ServiceProvider;

/**
 * Authorization
 *
 * Class AuthContractProvider
 * @package App\Providers
 */
class AuthContractProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
        $this->app->bind(AuthContact::class, AuthorizationManager::class);
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
