<?php

namespace Verleihliste\Validators;

use Plenty\Validation\Validator;

/**
 *  Validator Class
 */
class RentalItemValidator extends Validator
{
    protected function defineAttributes()
    {
        $this->addInt('deviceId', true);
        $this->addInt('userId', true);
        $this->addInt('rent_until', true);
    }
}
