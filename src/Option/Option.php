<?php
/**
 * Created by PhpStorm.
 * User: Thomas Whiston
 * Date: 26/01/2016
 * Time: 14:21
 */

namespace Drupal\twhiston\DrushOptionValidator\Option;

use Drupal\twhiston\DrushOptionValidator\Constraint\Constraint;

/**
 * Interface Option
 * @package Drupal\twhiston\DrushOptionValidator
 */
class Option {

  private $name;

  /** @var  Constraint */
  private $constraints;

  private $default;


  public function __construct($name, $constraints = NULL, $default = NULL) {

    $this->name = $name;
    $this->constraints = (!is_array($constraints))?array():$constraints;
    $this->default = $default;

  }

  /**
   * @return string
   */
  public function getOptionName(){
    return $this->name;
  }

  /**
   * @return mixed
   */
  public function getDefaultValue(){
    return $this->default;
  }

  /**
   * Should return an array of callables/closures, which return TRUE or FALSE
   * Try using the Constraints factory to make this
   * @return Constraint[]
   */
  public function getValidationConstraints(){
    return $this->constraints;
  }

}