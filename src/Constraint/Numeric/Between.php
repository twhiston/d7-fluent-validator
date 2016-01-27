<?php
/**
 * Created by PhpStorm.
 * User: Thomas Whiston
 * Date: 26/01/2016
 * Time: 17:11
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