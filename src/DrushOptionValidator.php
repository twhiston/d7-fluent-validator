<?php
/**
 * Created by PhpStorm.
 * User: Thomas Whiston
 * Date: 26/01/2016
 * Time: 14:19
 */

namespace Drupal\px\DrushOptionValidator;

use Drupal\px\DrushOptionValidator\Option\Option;

/**
 * Class DrushOptionSanitizer
 * @package Drupal\px\DrushOptionValidator
 */
class DrushOptionValidator {


  /**
   * @var Option[] $options
   */
  private $options;

  private $results;

  /**
   * DrushOptionValidator constructor.
   * @param null $rules
   */
  public function __construct($options = NULL) {
    if(is_array($options)) {
      foreach($options as $option) {
        $this->addOption($option);
      }
    }
  }

  /**
   * @param Option $option
   */
  public function addOption(Option $option){
      $this->options[$option->getOptionName()] = $option;
  }

  /**
   * @param $data
   */
  public function validate($data){

    /** @var ValidationResult[] $results */
    $this->results = [];
    foreach($this->options as $option){
      if(array_key_exists($option->getOptionName(),$data)){
        $constraints = $option->getValidationConstraints();
        foreach($constraints as $constraint){
          /** @var ValidationResult $result */
          $result = $constraint->validate($data[$option->getOptionName()]);
          $this->results[] = $result;
          if(!$result->getState()){
            return FALSE;
          }
        }
      }
    }
    //TODO - logging and stuff
    return TRUE;
  }

}