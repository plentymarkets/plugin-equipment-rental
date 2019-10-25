<?php

namespace Verleihliste\Providers;


use Plenty\Plugin\ServiceProvider;
use Verleihliste\Contracts\RentalItemRepositoryContract;
use Verleihliste\Repositories\RentalItemRepository;

class EquipmentRentalServiceProvider extends ServiceProvider
{

    /**
     * Register the service provider.
     */

    public function register()
    {
        $this->getApplication()->register(EquipmentRentalRouteServiceProvider::class);
        $this->getApplication()->bind(RentalItemRepositoryContract::class, RentalItemRepository::class);
    }
}
