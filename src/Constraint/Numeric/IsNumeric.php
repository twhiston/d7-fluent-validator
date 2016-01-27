<?php
/**
 * Created by PhpStorm.
 * User: Thomas Whiston
 * Date: 26/01/2016
 * Time: 15:27
 */
namespace Drupal\twhiston\DrushOptionValidator\Constraint\Numeric;

use Drupal\twhiston\DrushOptionValidator\Constraint\Constraint;
use Drupal\twhiston\DrushOptionValidator\ValidationResult;

/**
 * Created by PhpStorm.
 * User: Thomas Whiston
 * Date: 26/01/2016
 * Time: 15:18
 */
class IsNumeric implements Constraint {


  public function validate($data) {
    return new ValidationResult((is_numeric($data)));
  }

}