<?php

namespace Drupal\px\DrushOptionValidator\Constraint\Numeric;

use Drupal\px\DrushOptionValidator\Constraint\Constraint;
/**
 * Created by PhpStorm.
 * User: Thomas Whiston
 * Date: 26/01/2016
 * Time: 15:18
 */
class GreaterThan implements Constraint {

  private $value;

  public function __construct($value) {
    $this->value = $value;
  }

  public function validate($data) {
    return ($data > $this->value);
  }


}