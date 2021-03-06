<?php
/**
 * Created by PhpStorm.
 * User: Thomas Whiston
 * Date: 26/01/2016
 * Time: 14:19
 */

namespace Drupal\twhiston\FluentValidator;

use Drupal\twhiston\FluentValidator\Constraint\Constraint;
use Drupal\twhiston\FluentValidator\Result\ValidationResult;
use Drupal\twhiston\FluentValidator\VRule\VRule;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;



/**
 * Class FluentValidator
 * The validator that parses our rules
 * $vali = new FluentValidator();
 * $result = $vali->setOptions($options)->addVRule($r)->addVRule($r2)->validate($data);
 * @package Drupal\twhiston\FluentValidator
 */
class FluentValidator implements LoggerAwareInterface
{

    /**
     * All our rules
     * @var VRule[] $rules
     */
    private $rules;

    /**
     * The results of the validation in a tree structured the same as the input data
     * @var ValidationResult[]
     */
    private $results;

    /**
     * PSR-3 Compatible class for logging. If using drupal see https://bitbucket.org/twhiston/drupallogger
     * @var  LoggerInterface
     */
    private $logger;

    /**
     * Array of options for the validator, as a keyed array.
     * Options:
     *      loglevel - PSR-3 logging level, set to debug to get some extra messages about validation
     * @var mixed[]
     */
    private $options;

    /**
     * The state of validation,
     *  null - no validation has occured
     *  true - validation success
     *  false - validation failure
     * @var null|boolean
     */
    private $state;

    /**
     * constructor.
     * @param null $options
     */
    public function __construct($options = NULL)
    {
        $this->state = NULL;
        $this->logger = NULL;
        $this->rules = [];
        $this->results = [];
        $this->setOptions($options);
    }

    /**
     * @param $options
     * @return $this
     */
    public function setOptions($options)
    {
        $this->options = [];
        if (is_array($options)) {
            foreach ($options as $name => $option) {
                $this->options[$name] = $option;
            }
        }

        return $this;
    }

    /**
     * @param \Psr\Log\LoggerInterface $logger
     * @return $this
     * @codeCoverageIgnore
     */
    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;

        return $this;
    }

    /**
     * delete the  results tree, reset the validation state and delete all the rules
     * @param null $options
     * @return $this
     */
    public function reset($options = null)
    {
        $this->clearResults();

        $this->rules = [];

        if ($options !== null) {
            $this->setOptions($options);
        }

        return $this;
    }

    /**
     * Delete the results tree and reset the state
     * @return $this
     */
    public function clearResults()
    {
        $this->state = null;
        $this->results = [];

        return $this;
    }


    /**
     * Add a validation rule
     * @param \Drupal\twhiston\FluentValidator\VRule\VRule $option
     * @return $this
     */
    public function addVRule(VRule $option)
    {
        $this->rules[$option->getName()] = $option;

        return $this;
    }


    /**
     * Add an array of validation rules to the existing rules (array_merge)
     * @param $rules
     * @return $this
     */
    public function addVRules($rules)
    {
        $rules = (is_array($rules))?$rules:array($rules);
        $this->rules = array_merge($rules, $this->rules);

        return $this;
    }

    /**
     * Add an array of validation rules to the existing rules (array_merge)
     * @param $rules
     * @return $this
     */
    public function setVRules($rules)
    {
        (is_array($rules))?:array($rules);
        $this->rules =$rules;

        return $this;
    }

    /**
     * Run the validation over the rules tree and return the validation status
     * @param $data
     * @return bool
     */
    public function validate(&$data)
    {
        //start the validation chain
        $this->results = [];
        $state = true;//Innocent until proven guilty TODO NO!!!
        return $this->doValidate($data, $this->rules, $state, $this->results);
    }

    /**
     * get a numerically indexed flat array of the names of the failed rules.
     * Useful if you just want to get all the results from a simple form
     * Not so useful if you have lots of fields that share names, in that case get a structured tree of failures with getFailedRules()
     * @return mixed
     */
    public function getFailedRuleNames(){
        $out = [];
        return $this->doGetFailedRuleNames($this->results,$out);

    }

    /**
     * Get a tree of only failed results from the validator
     * @return array
     */
    public function getFailedRules(){
        $out = [];
        return $this->doGetFailedRules($this->results, $out);
    }

    /**
     * convenience method to return an array of messages generated during the validation
     * @return array
     */
    public function getMessages()
    {
        $out = [];
        return $this->doGetMessages($this->results, $out);
    }

    /**
     * If we get all the results we can see which elements actually failed validation and get their messages
     * @return array|\Drupal\twhiston\FluentValidator\Result\ValidationResult[]
     */
    public function getResults()
    {
        return $this->results;
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
        if (!is_array($rules)) {
            $rules = array($rules);
        }
        foreach ($rules as $rule) {
            $name = $rule->getName();
            $results[$name] = [];
            if (array_key_exists($name, $data)) {
                $branches = $rule->getTree();
                /** @var Constraint|VRule $branch */
                if($branches == null) { continue;}
                foreach ($branches as $branch) {
                    //are we a rule or a constraint
                    if (in_array(
                      'Drupal\\twhiston\\FluentValidator\\Constraint\\Constraint',
                      class_implements($branch, true)
                    )) {
                        if ($this->validateConstraint(
                            $branch,
                            $data,
                            $name,
                            $results[$name]
                          ) == false
                        ) {
                            $state = false;
                        }
                    } else {
                        if (is_subclass_of(
                            $branch,
                            'Drupal\\twhiston\\FluentValidator\\VRule\\VRule'
                          ) ||
                          $branch instanceof VRule === true
                        ) {
                            //If its a rule we recursively call this function with the right data
                            if ($this->doValidate(
                                $data[$name],
                                $branch,
                                $state,
                                $results[$name]
                              ) == false
                            ) {
                                $state = false;
                            }
                        }
                    }
                }
            }
        }

        return $state;
    }

    /**
     * The actual validation, calls the constraint validate function and returns the status
     * @param \Drupal\twhiston\FluentValidator\Constraint\Constraint $constraint
     * @param $data
     * @param $name
     * @param $results
     * @return bool
     */
    private function validateConstraint(
      Constraint $constraint,
      &$data,
      $name,
      &$results
    ) {

        $state = false;
        /** @var ValidationResult $result */
        $result = $constraint->validate(
          $data[$name]
        );
        $results[] = $result;//set the result
        if (!$result->getStatus()) {
            //If the validation failed do some logging
            if (array_key_exists(
                'loglevel',
                $this->options
              ) && $this->options['loglevel'] == 'debug'
            ) {
                //@codeCoverageIgnoreStart
                if($this->logger !== NULL) {

                    $this->logger->notice(
                      '@rule validation failed',
                      array('@rule' => $name)
                    );
                }
                //@codeCoverageIgnoreEnd
            }
        } else {
            $state = true;//validation has passed
        }

        return $state;
    }


    /**
     * @param $results
     * @param $out
     * @param null $rulename
     * @return mixed
     */
    private function doGetFailedRuleNames($results, &$out, $rulename = NULL){
        foreach ($results as $rule => $constraints) {
            /** @var ValidationResult $result */
            if (!is_array($constraints)) {
                //Wrap non arrays to make the next bit play nice
                $constraints = array($constraints);
            }
            foreach ($constraints as $name => $result) {
                if (is_array($result)) {
                    //If the result is an array we need to drill down into it again
                    $this->doGetFailedRuleNames($result, $out,$name);
                } else {
                    $state = $result->getStatus();
                    if ($state === FALSE) {
                        $out[] = $rulename;
                    }
                }
            }
        }

        return $out;
    }

    /**
     * Re-entrant function
     * Builds a tree of only failed results in the same structure as the input and results data
     * @return array
     */
    private function doGetFailedRules($results, &$out, $rulename = NULL)
    {

        foreach ($results as $rule => $constraints) {
            $isAr = false;
            /** @var ValidationResult $result */
            if (is_array($constraints)) {
                //If its an array we need to make an entry for it
                $out[$rule] = [];
                $isAr = true;//We made an array, we need to know this later
            } else {
                //Wrap non arrays to make the next bit play nice
                $constraints = array($constraints);
            }
            foreach ($constraints as $name => $result) {
                if (is_array($result)) {
                    //If the result is an array we need to drill down into it again
                    $out[$rule][$name] = [];
                    $this->doGetFailedRules($result, $out[$rule][$name]);
                    if(count($out[$rule][$name]) === 0){
                        unset($out[$rule][$name]);
                    }
                } else {
                    $mes = $result;
                    if ($mes !== null && $result->getStatus() === FALSE) {
                        //This is kind of horrible, but to make it work and everything end up named properly it has to do this
                        if ($isAr) {
                            $out[$rule][] = $result;
                        } else {
                            $out[] = $result;
                        }
                    }
                }
            }
            if(array_key_exists($rule,$out) && count($out[$rule]) === 0){
                unset($out[$rule]);
            }
        }
        return $out;
    }

    /**
     * Re-entrant function
     * Builds a tree of data in the same structure as the input and results data
     * @return array
     */
    private function doGetMessages($results, &$out)
    {

        foreach ($results as $rule => $constraints) {
            $isAr = false;
            /** @var ValidationResult $result */
            if (is_array($constraints)) {
                //If its an array we need to make an entry for it
                $out[$rule] = [];
                $isAr = true;//We made an array, we need to know this later
            } else {
                //Wrap non arrays to make the next bit play nice
                $constraints = array($constraints);
            }
            foreach ($constraints as $name => $result) {
                if (is_array($result)) {
                    //If the result is an array we need to drill down into it again
                    $out[$rule][$name] = [];
                    $this->doGetMessages($result, $out[$rule][$name]);
                } else {
                    $mes = $result->getMessage();
                    if ($mes !== null) {
                        //This is kind of horrible, but to make it work and everything end up named properly it has to do this
                        if ($isAr) {
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

}