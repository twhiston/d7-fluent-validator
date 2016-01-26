<?php
/**
 * Created by PhpStorm.
 * User: Thomas Whiston
 * Date: 26/01/2016
 * Time: 15:39
 */

namespace Drupal\px\DrushOptionValidator\Constraint;


class CallableConstraint implements Constraint {

  private $callable;

  public function validate($data) {
    $call = $this->callable;
    return $call($data);
  }

  public function __construct($callable) {
    $this->callable = $callable;
  }

}