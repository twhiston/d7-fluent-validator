<?php
/**
 * Created by PhpStorm.
 * User: Thomas Whiston
 * Date: 26/01/2016
 * Time: 14:21
 */

namespace Drupal\px\DrushOptionValidator\Option;


/**
 * Interface Option
 * @package Drupal\px\DrushOptionSanitizer
 */
interface Option {

  /**
   * @return string
   */
  public function getOptionName();

  /**
   * @return mixed
   */
  public function getDefaultValue();

  /**
   * Should return an array of callables/closures, which return TRUE or FALSE
   * @return Constraint[]
   */
  public function getValidationConstraints();

}