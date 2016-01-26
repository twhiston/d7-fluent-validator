<?php
/**
 * Created by PhpStorm.
 * User: Thomas Whiston
 * Date: 26/01/2016
 * Time: 14:49
 */

namespace Drupal\px\DrushOptionValidator;


class ValidationResult {

  private $state;

  private $message;

  public function __construct($state, $message) {
    $this->state = $state;
    $this->message = $message;
  }

  /**
   * @return mixed
   */
  public function getState() {
    return $this->state;
  }

  /**
   * @return mixed
   */
  public function getMessage() {
    return $this->message;
  }




}