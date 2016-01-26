<?php
/**
 * Created by PhpStorm.
 * User: Thomas Whiston
 * Date: 26/01/2016
 * Time: 20:40
 */

namespace Drupal\px\DrushOptionValidator\Constraint\General;

use Drupal\px\DrushOptionValidator\Constraint\Constraint;
use Drupal\px\DrushOptionValidator\ValidationResult;

class IsBool implements Constraint {


  public function validate($data) {

    return new ValidationResult(is_bool($data));
  }


}