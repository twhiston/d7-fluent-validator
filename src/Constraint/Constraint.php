<?php
/**
 * Created by PhpStorm.
 * User: Thomas Whiston
 * Date: 26/01/2016
 * Time: 14:48
 */
namespace Drupal\twhiston\FluentValidator\Constraint;

use Drupal\twhiston\FluentValidator\Result\ValidationResult;

/**
 * Interface Constraint
 * Any function that validates data should extend this class
 * @package Drupal\twhiston\DrushOptionSanitizer
 */
interface Constraint
{

    /**
     * Validate the data
     * @param $data
     * @return ValidationResult
     */
    public function validate($data);

}