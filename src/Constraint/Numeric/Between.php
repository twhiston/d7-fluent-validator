<?php
/**
 * Created by PhpStorm.
 * User: Thomas Whiston
 * Date: 26/01/2016
 * Time: 17:11
 */

namespace Drupal\twhiston\FluentValidator\Constraint\Numeric;

use Drupal\twhiston\FluentValidator\Constraint\Constraint;
use Drupal\twhiston\FluentValidator\Result\ValidationResult;

/**
 * Created by PhpStorm.
 * User: Thomas Whiston
 * Date: 26/01/2016
 * Time: 15:18
 */
class Between implements Constraint
{

    private $min;

    private $max;

    public function __construct($min, $max)
    {
        $this->min = $min;
        $this->max = $max;
    }

    public function validate($data)
    {

        $state = true;
        if ($data < $this->min) {
            $state = false;
        } else {
            if ($data > $this->max) {
                $state = false;
            }
        }

        return new ValidationResult($state, ($state) ? 'Validation Passed':'Validation Failed');
    }


}