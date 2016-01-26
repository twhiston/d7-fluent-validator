<?php
/**
 * Created by PhpStorm.
 * User: Thomas Whiston
 * Date: 26/01/2016
 * Time: 15:39
 */

namespace Drupal\px\DrushOptionValidator\Constraint;


use Drupal\px\DrushOptionValidator\ValidationResult;

class CallableConstraint implements Constraint {

  private $callable;

  public function validate($data) {
    $call = $this->callable;
    $output = $call($data);
    if($output instanceof ValidationResult) {
      return $output;
    } elseif(is_bool($output)){
      return new ValidationResult($output);
    }
    return NULL;
  }

  public function __construct($callable) {
    $this->callable = $callable;
  }

}