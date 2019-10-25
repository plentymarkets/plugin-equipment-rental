<?php

namespace Verleihliste\Validators;

use Plenty\Validation\Validator;

/**
 *  Validator Class
 */
class RentalUserValidator extends Validator
{
    protected function defineAttributes()
    {
        $this->addString('firstname', true);
        $this->addString('lastname', true);
        $this->addString('email', true);
    }
}
