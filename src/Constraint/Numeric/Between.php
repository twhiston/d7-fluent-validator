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
use twhiston\twLib\tests\inTest;

/**
 * Class Between
 * Simple constraint for verifying a number is between x and y
 * @package Drupal\twhiston\FluentValidator\Constraint\Numeric
 */
class Between implements Constraint
{

    /**
     * @var integer minimum possible value
     */
    private $min;

    /**
     * @var integer max possible value
     */
    private $max;

    /**
     * Between constructor.
     * @param $min
     * @param $max
     */
    public function __construct($min, $max)
    {
        $this->min = $min;
        $this->max = $max;
    }

    /**
     * Validate that the $data is between min and max and return a ValidationResult
     * @param $data
     * @return \Drupal\twhiston\FluentValidator\Result\ValidationResult
     */
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