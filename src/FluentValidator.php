<?php
/**
 * Created by PhpStorm.
 * User: Thomas Whiston
 * Date: 26/01/2016
 * Time: 14:19
 */

namespace Drupal\twhiston\FluentValidator;

use Drupal\twhiston\FluentValidator\VRule\NoDefault;
use Drupal\twhiston\FluentValidator\VRule\VRule;
use Drupal\twhiston\FluentValidator\Result\ValidationResult;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;
use Drupal\twhiston\FluentValidator\Constraint\Constraint;

/**
 * Class DrushOptionSanitizer
 * @package Drupal\twhiston\DrushOptionValidator
 */
class FluentValidator implements LoggerAwareInterface
{


    /**
     * @var VRule[] $rules
     */
    private $rules;

    /** @var ValidationResult[]  */
    private $results;

    /** @var  LoggerInterface */
    private $logger;

    /**
     * @var
     */
    private $options;

    /**
     * @var null
     */
    private $state;

    /**
     * constructor.
     * @param null $options
     */
    public function __construct($options = NULL)
    {
        $this->state = null;
        $this->rules = [];
        $this->results = [];
        $this->setOptions($options);

    }

    /**
     * @param $options
     * @return $this
     */
    public function setOptions($options){
        $this->options = [];
        //        if (is_array($options)) {
        //            foreach ($options as $option) {
//                $this->addOption($option);
//            }
//        }
        return $this;
    }

    /**
     * @param \Psr\Log\LoggerInterface $logger
     * @return $this
     */
    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;
        return $this;
    }

    /**
     * @param null $options
     * @return $this
     */
    public function reset($options = NULL){
        $this->clearResults();

        $this->rules = [];

        if($options !== NULL){
            $this->setOptions($options);
        }
        return $this;
    }

    public function clearResults(){
        $this->state = null;
        $this->results = [];
        return $this;
    }


    /**
     * @param VRule $option
     */
    public function addVRule(VRule $option)
    {
        $this->rules[$option->getName()] = $option;
        return $this;
    }

    /**
     * @param $option VRule[]
     */
    public function addVRules($rules){
        $this->rules = array_merge($rules,$this->rules);
    }

    public function validate(&$data){
        //start the validation chain
        $this->results = [];
        $state = TRUE;//Innocent until proven guilty TODO NO!!!
        return $this->doValidate($data,$this->rules,$state,$this->results);
    }

    /**
     * Re-entrant function
     * If your functions need extra arguments to validate against you must pass them here in an array keyed to the fields, like the input data
     * If your callable needs a different number of functions to the ones you provide it will not run and will mark the result as an error
     * @param $data
     * @param null|[] $extra
     * @return bool
     */
    private function doValidate(&$data, $rules, &$state, &$results)
    {

        /** @var VRule $rule */
        if(!is_array($rules)){
            $rules = array($rules);
        }
        foreach ($rules as $rule) {
            $name = $rule->getName();
            $results[$name] = [];
            if (array_key_exists($name, $data)) {
                $branches = $rule->getTree();
                /** @var Constraint|VRule $branch */
                foreach ($branches as $branch) {
                    //are we a rule or a constraint
                    if(in_array('Drupal\\twhiston\\FluentValidator\\Constraint\\Constraint', class_implements($branch,true))){
                        if($this->validateConstraint($branch,$data,$name,$results[$name]) == FALSE){
                            $state = FALSE;
                        }

                    } else if(  is_subclass_of($branch,'Drupal\\twhiston\\FluentValidator\\VRule\\VRule') ||
                                $branch instanceof VRule === TRUE ){
                        //If its a rule we recursively call this function with the right data
                        if($this->doValidate($data[$name],$branch,$state,$results[$name]) == FALSE){
                            $state = FALSE;
                        }
                    }
                }
            }
        }
        return $state;
    }

    private function validateConstraint(Constraint $constraint, &$data, $name, &$results){

        $state = FALSE;
        /** @var ValidationResult $result */
        $result = $constraint->validate(
          $data[$name]
        );
        $results[] = $result;//set the result
        if (!$result->getStatus()) {
            //If the validation failed
            if(array_key_exists('LogLevel',$this->options) && $this->options['LogLevel'] == 'debug'){
                //Do some logging
                $this->logger->notice('@rule validation failed',array('@rule' => $name));
            }
        } else {
            $state = TRUE;//validation has passed
        }
        return $state;
    }

    /**
     * convenience method to return an array of messages generated during the validation
     * @return array
     */
    public function getMessages(){
        $out = [];
        return $this->doGetMessages($this->results,$out);
    }

    /**
     * Re-entrant function
     * Not beautiful but it gets the job done
     * @return array
     */
    private function doGetMessages($results,&$out){

        foreach($results as $rule => $constraints){
            $isAr = FALSE;
            /** @var ValidationResult $result */
            if(is_array($constraints)){
                //If its an array we need to make an entry for it
                $out[$rule] = [];
                $isAr = TRUE;//We made an array, we need to know this later
            } else {
                //Wrap non arrays to make the next bit play nice
                $constraints = array($constraints);
            }
            foreach ($constraints as $name => $result) {
                if(is_array($result)){
                    //If the result is an array we need to drill down into it again
                    $out[$rule][$name] = [];
                    $this->doGetMessages($result,$out[$rule][$name]);
                } else {
                    $mes = $result->getMessage();
                    if($mes !== NULL){
                        //This is kind of horrible, but to make it work and everything end up named properly it has to do this
                        if($isAr){
                            $out[$rule][] = $result->getMessage();
                        } else {
                            $out[] = $result->getMessage();
                        }
                    }
                }
            }
        }
        return $out;
    }


    /**
     * If we get all the results we can see which elements actually failed validation
     * @return array|\Drupal\twhiston\FluentValidator\Result\ValidationResult[]
     */
    public function getResults(){
        return $this->results;
    }

}