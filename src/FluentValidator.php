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

    private $results;

    /** @var  LoggerInterface */
    private $logger;

    private $options;

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

    public function setOptions($options){
        $this->options = [];
        //        if (is_array($options)) {
        //            foreach ($options as $option) {
//                $this->addOption($option);
//            }
//        }
        return $this;
    }

    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;
        return $this;
    }

    public function reset($options = NULL){
        $this->state = null;
        $this->rules = [];
        $this->results = [];
        if($options !== NULL){
            $this->setOptions($options);
        }
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
     * If your functions need extra arguments to validate against you must pass them here in an array keyed to the fields, like the input data
     * If your callable needs a different number of functions to the ones you provide it will not run and will mark the result as an error
     * @param $data
     * @param null|[] $extra
     * @return bool
     */
    public function validate(&$data)
    {

        //How do we do this in a smarter way, we need to recursively validate any arrays
        //We also need to be able to search out depth in arrays from namespace style rule names

        //Firstly find all the arrays in the data and

        /** @var ValidationResult[] $results */
        $state = true;
        $this->results = [];
        foreach ($this->rules as $rule) {
            $name = $rule->getName();
            if (array_key_exists($name, $data)) {
                $constraints = $rule->getConstraints();
                foreach ($constraints as $constraint) {
                    /** @var ValidationResult $result */
                    $result = $constraint->validate(
                      $data[$name]
                    );
                    $this->results[] = $result;
                    if (!$result->getStatus()) {
                        if(array_key_exists('LogLevel',$this->options) && $this->options['LogLevel'] == 'debug'){
                            $this->logger->notice('@rule validation failed',array('@rule' => $name));
                        }
                        $state = false;
                        $def = $rule->getDefault();
                        if ($def !== NoDefault::No) {
                            if(array_key_exists('LogLevel',$this->options) && $this->options['LogLevel'] == 'debug'){
                                $this->logger->notice('@rule set default value @value',array('@rule' => $name, '@value' => $def ));
                            }
                            $data[$name] = $def;
                        }
                    }
                }
            }
        }
        return $state;
    }

}