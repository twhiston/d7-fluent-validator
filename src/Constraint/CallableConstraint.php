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

        //If the function only has one argument or it is an array without an args key
        //we can just pass it the single data variable
        $output = NULL;
        if( ( $nargs === 1 && !is_array($data) ) ||
            ( $nargs === 1 && is_array($data) && !array_key_exists('args',$data) )
        ) {
            $output = $call($data);
        } elseif(array_key_exists('args', $data)) {
            //if not then we need to make sure the data has the right number of arguments
            $args = array_slice($data['args'], 0, $nargs);
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
            return new ValidationResult((bool)$output);
        }
    }

}