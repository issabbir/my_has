<?php
/**
 * Created by PhpStorm.
 * User: ashraf
 * Date: 1/28/20
 * Time: 4:11 PM
 */

namespace App\Providers;

use App\Contracts\AdvertisementContract;
use App\Contracts\EmailTransportContract;
use App\Contracts\HaApplicationContract;
use App\Contracts\HinterChangeApplicationContract;
use App\Contracts\HouseContract;
use App\Contracts\HreplacementApplicationContract;
use App\Contracts\InterchangeTakeoverContract;
use App\Contracts\LookupContract;
use App\Contracts\Pmis\Employee\EmployeeContract;
use App\Managers\AdvertisementManager;
use App\Managers\EmailTransportManager;
use App\Managers\HaApplicationManager;
use App\Managers\HinterChangeApplicationManager;
use App\Managers\HouseManager;
use App\Managers\HreplacementApplicationManager;
use App\Managers\InterchangeTakeoverManager;
use App\Managers\LookupManager;
use App\Managers\Pmis\Employee\EmployeeManager;
use Illuminate\Support\ServiceProvider;

class HasContractServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(EmailTransportContract::class, EmailTransportManager::class);
        $this->app->bind(LookupContract::class, LookupManager::class);
        $this->app->bind(EmployeeContract::class, EmployeeManager::class);
        $this->app->bind(AdvertisementContract::class, AdvertisementManager::class);
        $this->app->bind(HaApplicationContract::class, HaApplicationManager::class);
        $this->app->bind(HinterChangeApplicationContract::class, HinterChangeApplicationManager::class);
        $this->app->bind(HreplacementApplicationContract::class, HreplacementApplicationManager::class);
        $this->app->bind(HouseContract::class, HouseManager::class);
        $this->app->bind(InterchangeTakeoverContract::class, InterchangeTakeoverManager::class);
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