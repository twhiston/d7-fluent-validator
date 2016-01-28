<?php
/**
 * Created by PhpStorm.
 * User: Thomas Whiston
 * Date: 26/01/2016
 * Time: 15:39
 */

namespace Drupal\twhiston\DrushOptionValidator\Constraint;


use Drupal\twhiston\DrushOptionValidator\ValidationResult;

class CallableConstraint implements Constraint
{

    private $callable;

    public function __construct($callable)
    {
        $this->callable = $callable;
    }

    public function validate($data)
    {
        $call = $this->callable;
        if(is_array($data) && array_key_exists('args',$data)){
            $output = call_user_func_array($call,$data['args']);
        } else {
            $output = $call($data);
        }
        if ($output instanceof ValidationResult) {
            return $output;
        } elseif (is_bool($output)) {
            return new ValidationResult($output);
        }

        return null;
    }

}