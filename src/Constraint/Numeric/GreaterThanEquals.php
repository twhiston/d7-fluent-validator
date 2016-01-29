<?php

namespace Drupal\twhiston\FluentValidator\Constraint\Numeric;

use Drupal\twhiston\FluentValidator\Constraint\Constraint;
use Drupal\twhiston\FluentValidator\Result\ValidationResult;

/**
 * Created by PhpStorm.
 * User: Thomas Whiston
 * Date: 26/01/2016
 * Time: 15:18
 */
class GreaterThanEquals implements Constraint
{

    private $value;

    public function __construct($value)
    {
        $this->value = $value;
    }

    public function validate($data)
    {
        $state = ($data >= $this->value);
        return new ValidationResult($state, ($state) ? 'Validation Passed':'Validation Failed');

    }

}