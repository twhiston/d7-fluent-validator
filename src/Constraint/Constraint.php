<?php
/**
 * Created by PhpStorm.
 * User: Thomas Whiston
 * Date: 26/01/2016
 * Time: 14:48
 */

namespace Drupal\twhiston\DrushOptionValidator\Constraint;


/**
 * Interface Constraint
 * @package Drupal\twhiston\DrushOptionSanitizer
 */
interface Constraint {

  /**
   * @param $data
   * @return ValidationResult
   */
  public function validate($data);

}