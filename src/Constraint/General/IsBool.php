<?php
/**
 * Created by PhpStorm.
 * User: Thomas Whiston
 * Date: 26/01/2016
 * Time: 20:40
 */

namespace Drupal\twhiston\DrushOptionValidator\Constraint\General;

use Drupal\twhiston\DrushOptionValidator\Constraint\Constraint;
use Drupal\twhiston\DrushOptionValidator\ValidationResult;

class IsBool implements Constraint {


  public function validate($data) {

    return new ValidationResult(is_bool($data));
  }


}