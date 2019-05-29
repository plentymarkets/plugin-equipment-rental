<?php

namespace EquipmentRental\Validators;

use Plenty\Validation\Validator;

/**
 *  Validator Class
 */
class RentalMailValidator extends Validator
{
    protected function defineAttributes()
    {
        $this->addInt('deviceId', true);
        $this->addInt('userId', true);
    }
}
