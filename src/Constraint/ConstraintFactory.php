<?php

namespace Drupal\twhiston\DrushOptionValidator\Constraint;

use ReflectionClass;


use Drupal\twhiston\DrushOptionValidator\Instantiate;
/**
 * Created by PhpStorm.
 * User: Thomas Whiston
 * Date: 26/01/2016
 * Time: 15:29
 */
class ConstraintFactory {

  public static function makeConstraint($class, $args){
    return Instantiate::make($class,$args,"Drupal\\twhiston\\DrushOptionValidator\\Constraint\\", 'Drupal\twhiston\DrushOptionValidator\Constraint\Constraint');
  }

  static public function makeConstraints($constraints){

    $cout = [];
    foreach($constraints as $constraint){
      if(array_key_exists('class',$constraint) && array_key_exists('args',$constraint) ){
        $output = ConstraintFactory::makeConstraint($constraint['class'], $constraint['args']);
        if($output != NULL){
          $cout[] = $output;
        } else {
          throw new \Exception('Could not make constraint');
        }
      } else {
        //TODO logging
      }

    }
    return $cout;
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