<?php
/**
 * Created by PhpStorm.
 * User: Thomas Whiston
 * Date: 26/01/2016
 * Time: 14:19
 */

namespace Drupal\twhiston\DrushOptionValidator;

use Drupal\twhiston\DrushOptionValidator\Rule\Rule;

/**
 * Class DrushOptionSanitizer
 * @package Drupal\twhiston\DrushOptionValidator
 */
class DrushOptionValidator
{


    /**
     * @var Rule[] $options
     */
    private $options;

    private $results;

    private $defaultOnFail;

    /**
     * DrushOptionValidator constructor.
     * @param null $rules
     */
    public function __construct($options = null, $defaultOnFail = true)
    {
        if (is_array($options)) {
            foreach ($options as $option) {
                $this->addOption($option);
            }
        }
        $this->defaultOnFail = $defaultOnFail;
    }

    /**
     * @param Rule $option
     */
    public function addOption(Rule $option)
    {
        $this->options[$option->getRuleName()] = $option;
    }

    /**
     * @param $data
     */
    public function validate(&$data)
    {

        /** @var ValidationResult[] $results */
        $state = true;
        $this->results = [];
        foreach ($this->options as $option) {
            if (array_key_exists($option->getRuleName(), $data)) {
                $constraints = $option->getValidationConstraints();
                foreach ($constraints as $constraint) {
                    /** @var ValidationResult $result */
                    $result = $constraint->validate(
                      $data[$option->getRuleName()]
                    );
                    $this->results[] = $result;
                    if (!$result->getState()) {
                        $state = false;
                        if ($this->defaultOnFail) {
                            $data[$option->getRuleName(
                            )] = $option->getDefaultValue();
                        }
                    }
                }
            }
        }

        //TODO - logging and stuff
        return $state;
    }

}