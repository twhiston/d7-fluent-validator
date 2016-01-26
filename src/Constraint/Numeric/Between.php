<?php
/**
 * Created by PhpStorm.
 * User: Thomas Whiston
 * Date: 26/01/2016
 * Time: 17:11
 */

namespace Drupal\px\DrushOptionValidator\Constraint\Numeric;

use Drupal\px\DrushOptionValidator\Constraint\Constraint;
use Drupal\px\DrushOptionValidator\ValidationResult;

/**
 * Created by PhpStorm.
 * User: Thomas Whiston
 * Date: 26/01/2016
 * Time: 15:18
 */
class Between implements Constraint {

  private $min;

  private $max;

  public function __construct($min,$max) {
    $this->min = $min;
    $this->max = $max;
  }

  public function validate($data) {

    $state = TRUE;
    if($data < $this->min){
      $state = FALSE;
    } else if($data > $this->max){
      $state = FALSE;
    }
    return new ValidationResult($state);
  }


}