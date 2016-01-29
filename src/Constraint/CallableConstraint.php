<?php
/**
 * Created by PhpStorm.
 * User: Thomas Whiston
 * Date: 26/01/2016
 * Time: 15:39
 */

namespace Drupal\twhiston\FluentValidator\Constraint;

use Drupal\twhiston\FluentValidator\Result\ValidationResult;


class CallableConstraint implements Constraint
{

    private $callable;

    private $outputMap;

    public function __construct($callable, $outputMap = NULL)
    {
        $this->callable = $callable;
        $this->outputMap = $outputMap;
    }

    public function validate($data)
    {
        $call = $this->callable;
        $fct = new \ReflectionFunction($call);
        $nargs =  $fct->getNumberOfRequiredParameters();

        //If the function only has one we can just pass it the single data variable
        $output = NULL;
        if($nargs === 1 && !is_array($data)) {
            $output = $call($data);
        } elseif($nargs === 1 && is_array($data) && !array_key_exists('args',$data)){
            //one input but its an array, if it doesnt contain an args key we can assume that our function wants an array
            $output = $call($data);
        } elseif(array_key_exists('args', $data)) {

            $args = array_slice($data['args'], 0, $nargs);
            $output = call_user_func_array($call,$args);
        }
//        else if(is_array($data) && array_key_exists('args',$data)){
//            $output = call_user_func_array($call,$data['args']);
//        }

        if ($output instanceof ValidationResult) {
            return $output;
        } else {
            //Do output mapping if necessary
            if($this->outputMap != NULL){
                //If the array key exists set it to this
                if(array_key_exists($output,$this->outputMap)){
                    $output = $this->outputMap[$output];
                } else {
                    //else set it to false
                    $output = FALSE;
                }
            }
            return new ValidationResult((bool)$output);
        }
    }

}