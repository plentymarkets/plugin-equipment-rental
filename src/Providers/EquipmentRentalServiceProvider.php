<?php

namespace EquipmentRental\Providers;


use Plenty\Plugin\ServiceProvider;
use EquipmentRental\Contracts\RentalItemRepositoryContract;
use EquipmentRental\Repositories\RentalItemRepository;

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
