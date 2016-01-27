<?php
/**
 * Created by PhpStorm.
 * User: Thomas Whiston
 * Date: 27/01/2016
 * Time: 01:13
 */

namespace Drupal\twhiston\DrushOptionValidator\Constraint\General;


use Drupal\twhiston\DrushOptionValidator\Constraint\Constraint;
use Drupal\twhiston\DrushOptionValidator\ValidationResult;

class NotNull implements Constraint {


  public function validate($data) {
    return new ValidationResult($data !== NULL);
  }

}