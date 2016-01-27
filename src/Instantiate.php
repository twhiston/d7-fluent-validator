<?php

namespace Drupal\twhiston\DrushOptionValidator;

/**
 * Created by PhpStorm.
 * User: Thomas Whiston
 * Date: 27/01/2016
 * Time: 14:14
 */

//TODO - this should probably be in an external library of general useful things
class Instantiate {

  /**
   * @param $class
   * @param $args
   * @param $namespace string psr-4 style 'whatever\\thing\\another\\'
   *
   * @return null
   */
  public static function make($class,$args,$namespace,$interface = NULL){

    $count = substr_count($class,'\\');
    if($count = 0 || !(0 === strpos($class, 'Drupal')) ){
      //if there is no '/' we assume this is not a fully qualified namespace and make it our root Constraint namespace
      //If there is no drupal at the start then we assume its our class further down the tree
      $class = $namespace.$class;
    }

    try {
      $imp = class_implements($class,true);
      if(!is_array($imp)){return NULL;}

      if($interface !== NULL){
        if(!in_array($interface,$imp)){
          return NULL;
        }
      }
        //Make sure args are an array
      //Make sure args are an array
      $args = (!is_array($args) && $args != NULL) ? array($args) : $args;
        $instance = Instantiate::instantiate($class,$args);

    } catch (\Exception $e){
      return NULL;
    }

    return $instance;
  }

  private static function instantiate($class,$args){

    if(version_compare(phpversion(), '5.6.0', '>=')){
      $instance = new $class(eval('...') . $args);
    } else {
      $reflect  = new \ReflectionClass($class);
      $instance = ($args === NULL) ? $reflect->newInstanceArgs() : $reflect->newInstanceArgs($args) ;
    }
    return $instance;
  }

}