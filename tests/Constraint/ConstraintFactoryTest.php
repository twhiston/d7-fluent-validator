<?php

use Drupal\twhiston\FluentValidator\Constraint\ConstraintFactory;

use Drupal\twhiston\FluentValidator\Constraint\Constraint;

/**
 * Created by PhpStorm.
 * User: Thomas Whiston
 * Date: 26/01/2016
 * Time: 16:04
 */
class ConstraintFactoryTest extends PHPUnit_Framework_TestCase {

  public function testMakeConstraint(){

    //Check that it works
    /** @var Constraint $constraint */
      $constraint = ConstraintFactory::makeConstraint('Numeric\\GreaterThan',array(5));
      $this->assertInstanceOf('Drupal\twhiston\FluentValidator\Constraint\Constraint',$constraint);
      $this->assertInstanceOf('Drupal\twhiston\FluentValidator\Constraint\Numeric\GreaterThan',$constraint);
      $this->assertTrue($constraint->validate(10)->getStatus());


    try {
      //Check that it doesnt
      $constraint = ConstraintFactory::makeConstraint('Broken\\DoesntExist',array(5));
    } catch (Exception $e){
      $this->assertRegExp('/Could not make constraint/',$e->getMessage());
    }



      //Check that we can make a Constraint with multiple parameters
      $constraint = ConstraintFactory::makeConstraint(
        'Numeric\\Between',
        array(5, 10)
      );
      $this->assertTrue($constraint->validate(7)->getStatus());
      $this->assertFalse($constraint->validate(3)->getStatus());
      $this->assertFalse($constraint->validate(11)->getStatus());


    //Check that we can make a callable constraint
    $data = [
      function($data){
        if(strcmp('I Work',$data)=== 0){
          return TRUE;
        }
        return FALSE;
      }
    ];

    $constraint = ConstraintFactory::makeConstraint('Drupal\twhiston\FluentValidator\Constraint\CallableConstraint',$data);
    $this->assertTrue($constraint->validate('I Work')->getStatus());
    $this->assertFalse($constraint->validate('I suck')->getStatus());

  }

  public function testMakeConstraints(){


    $constraints = [];
    $constraints[] = [
      'class' => 'Numeric\\GreaterThan',
      'args' => array(5),
    ];

    $constraints[] = [
      'class' => 'Numeric\\Between',
      'args' => array(7,11),
    ];

    /** @var Constraint[] $constraint */
    $constraints = ConstraintFactory::makeConstraints($constraints);

    $this->assertCount(2,$constraints);
    $this->assertInstanceOf('Drupal\twhiston\FluentValidator\Constraint\Numeric\GreaterThan',$constraints[0]);
    $this->assertInstanceOf('Drupal\twhiston\FluentValidator\Constraint\Numeric\Between',$constraints[1]);


    $constraints[] = [
      'class' => 'Broken\\Will Not Return',
      'args' => array(7,11),
    ];

    try {
      //Check that it doesnt
      $constraints = ConstraintFactory::makeConstraints($constraints);
    } catch (Exception $e){
      $this->assertRegExp('/Could not make constraint/',$e->getMessage());
    }


  }

}
