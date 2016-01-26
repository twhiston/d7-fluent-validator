<?php
/**
 * Created by PhpStorm.
 * User: Thomas Whiston
 * Date: 26/01/2016
 * Time: 14:19
 */

namespace Drupal\px\DrushOptionValidator;


/**
 * Class DrushOptionSanitizer
 * @package Drupal\px\DrushOptionSanitizer
 */
class DrushOptionValidator {


  /**
   * @var Option[] $options
   */
  private $options;

  /**
   * DrushOptionSanitizer constructor.
   * @param null $rules
   */
  public function __construct($rules = NULL) {
    if(is_array($rules)) {
      foreach($rules as $rule) {
        $this->addOption($rule);
      }
    }
  }

  /**
   * @param \Drupal\px\DrushOptionSanitizer\Option $option
   */
  public function addOption(Option $option){

      $options[$option->getOptionName()] = $option;
  }

  /**
   * @param $data
   */
  public function validate($data){

    /** @var ValidationResult $results */
    $results = [];
    foreach($this->options as $option){
      if(array_key_exists($option->getOptionName(),$data)){
        $constraints = $option->getValidationConstraints();
        foreach($constraints as $name => $constraint){
          $results[] = call_user_func($constraint,$data[$option->getOptionName()]);
        }
      }
    }

    //TODO - logging and stuff


  }

}