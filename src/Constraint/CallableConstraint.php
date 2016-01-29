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

    private $args;

    public function __construct($callable, $args = NULL, $outputMap = NULL)
    {
        $this->callable = $callable;

        $this->args = is_array($args)?$args:array($args);
        $this->outputMap = $outputMap;
    }

    public function setArgs($args){
        $this->args = is_array($args)?$args:array($args);
    }

    public function validate($data)
    {
        $call = $this->callable;
        $fct = new \ReflectionFunction($call);
        $nargs =  $fct->getNumberOfRequiredParameters();

        //If the function only has one argument or it is an array without an args key
        //we can just pass it the single data variable
        $output = NULL;
        if( ( $nargs === 1 && !is_array($data) )) {
            $output = $call($data);
        } else {
            //if not then we need to make sure the data has the right number of arguments
            $args = $this->args;
            $c = array_unshift($args,$data);
            $args = array_slice($args, 0, $nargs);//If somehow we have too many arguments for the function slice off the end ones
            $output = call_user_func_array($call,$args);//call it
        }

        if ($output instanceof ValidationResult) {
            //If our callable returned a result directly we can return it
            return $output;
        } else {
            //If it didnt return a result we need to process the result
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
            return new ValidationResult((bool)$output, ((bool)$output)? 'Validation Passed':'Validation Failed');
        }
    }

}