<?php
/**
 * Created by PhpStorm.
 * User: Thomas Whiston
 * Date: 26/01/2016
 * Time: 15:39
 */

namespace Drupal\twhiston\FluentValidator\Constraint;

use Drupal\twhiston\FluentValidator\Result\ValidationResult;


/**
 * Class CallableConstraint
 * Callable constraint class is the most powerful constraint because you can create any validate function with it,
 * you can call a php function by passing its name in the constructor or you can pass a clojure
 * if your callback needs more than the $data argument pass them as the second item in the constructor or use setArgs()
 * @package Drupal\twhiston\FluentValidator\Constraint
 */
class CallableConstraint implements Constraint
{

    /**
     * The name or the callback function
     * @var
     */
    protected $callable;

    /**
     * if your function does not return something that maps to a bool use this to remap it.
     * You only need to map states that result in a TRUE validation state, all unmapped states will be taken as FALSE
     * For example for strcmp [ 0 => TRUE ] would be appropriate
     * @var null
     */
    protected $outputMap;

    /***
     * extra arguments for your callable can be passed in here
     * @var array|null
     */
    protected $args;

    /**
     * CallableConstraint constructor.
     * @param $callable string|callable name of your callable or a callable/clojure/lambda
     * @param null $args mixed[] any extra arguments your validation needs
     * @param null $outputMap mixed[] what output statuses to map to true, if function output cannot be cast to boolean appropriately
     */
    public function __construct($callable, $args = NULL, $outputMap = NULL)
    {
        $this->callable = $callable;

        $this->args = is_array($args)?$args:array($args);
        $this->outputMap = $outputMap;
    }

    /**
     * Set additional arguments that your callable needs
     * @param $args
     */
    public function setArgs($args){
        $this->args = is_array($args)?$args:array($args);
    }


    /**
     * Do the validation, this takes the data, merges it with the args where appropriate and creates a validation result
     * @param $data mixed data to be validated
     * @return bool|\Drupal\twhiston\FluentValidator\Result\ValidationResult
     */
    public function validate($data)
    {
        $call = $this->callable;
        $fct = new \ReflectionFunction($call);
        $nargs =  $fct->getNumberOfRequiredParameters();

        //If the function only has one argument or it is an array without an args key
        //we can just pass it the single data variable
        $output = FALSE;
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
                if(is_bool($output)){
                    $output = $output ? 'true' : 'false';
                }
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