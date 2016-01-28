<?php

namespace Drupal\twhiston\DrushOptionValidator\Constraint\Numeric;

use Drupal\twhiston\DrushOptionValidator\Constraint\Constraint;
use Drupal\twhiston\DrushOptionValidator\ValidationResult;

/**
 * Created by PhpStorm.
 * User: Thomas Whiston
 * Date: 26/01/2016
 * Time: 15:18
 */
class LessThan implements Constraint
{

    private $value;


    public function __construct($value)
    {
        $this->value = $value;
    }

    public function validate($data)
    {
        return new ValidationResult(($data < $this->value));
    }


}