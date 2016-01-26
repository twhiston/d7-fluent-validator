<?php

namespace Drupal\px\DrushOptionValidator\Constraint;

use ReflectionClass;

use Drupal\px\DrushOptionValidator\Constraint\Numeric\GreaterThan;
/**
 * Created by PhpStorm.
 * User: Thomas Whiston
 * Date: 26/01/2016
 * Time: 15:29
 */
class ConstraintFactory {

  public static function makeConstraint($class, $args){

    $count = substr_count($class,'\\');

    $instance = NULL;
    if($count = 0 || !(0 === strpos($class, 'Drupal')) ){
      //if there is no '/' we assume this is not a fully qualified namespace and make it our root Constraint namespace
      //If there is no drupal at the start then we assume its our class further down the tree
      $class = 'Drupal\\px\\DrushOptionValidator\\Constraint\\'.$class;
    }

    try {
      if(in_array('Drupal\px\DrushOptionValidator\Constraint\Constraint',class_implements($class,true))){
        $instance = ConstraintFactory::instantiate($class,$args);
      }
      return $instance;
    } catch (\Exception $e){
      //TODO - Logging
      return NULL;
    }
  }

  static private function instantiate($class,&$args){

    if(version_compare(phpversion(), '5.6.0', '>=')){
      $instance = new $class(eval('...') . $args);
    } else {
      $reflect  = new ReflectionClass($class);
      $instance = $reflect->newInstanceArgs($args);
    }
    return $instance;
  }

}