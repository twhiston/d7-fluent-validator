<?php
/**
 * Created by PhpStorm.
 * User: Thomas Whiston
 * Date: 26/01/2016
 * Time: 15:18
 */

namespace Drupal\twhiston\FluentValidator\Constraint\Numeric;

use Drupal\twhiston\FluentValidator\Constraint\Constraint;
use Drupal\twhiston\FluentValidator\Result\ValidationResult;

/**
 * Class LessThanEquals
 * @package Drupal\twhiston\FluentValidator\Constraint\Numeric
 */
class LessThanEquals implements Constraint
{

    /**
     * @var integer
     */
    private $value;

    /**
     * LessThanEquals constructor.
     * @param $value
     */
    public function __construct($value)
    {
        $this->value = $value;
    }

    /**
     * @param $data
     * @return \Drupal\twhiston\FluentValidator\Result\ValidationResult
     */
    public function validate($data)
    {
        $state = ($data <= $this->value);
        return new ValidationResult($state, ($state) ? 'Validation Passed':'Validation Failed');
    }

}