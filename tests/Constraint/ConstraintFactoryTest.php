<?php

use Drupal\twhiston\DrushOptionValidator\Constraint\ConstraintFactory;

use Drupal\twhiston\DrushOptionValidator\Constraint\Constraint;

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
    $this->assertInstanceOf('Drupal\twhiston\DrushOptionValidator\Constraint\Constraint',$constraint);
    $this->assertInstanceOf('Drupal\twhiston\DrushOptionValidator\Constraint\Numeric\GreaterThan',$constraint);


    $this->assertTrue($constraint->validate(10)->getState());


    //Check that it doesnt
    $constraint = ConstraintFactory::makeConstraint('Broken\\DoesntExist',array(5));
    $this->assertNull($constraint);


    //Check that we can make a Constraint with multiple parameters
    $constraint = ConstraintFactory::makeConstraint('Numeric\\Between',array(5,10));
    $this->assertTrue($constraint->validate(7)->getState());
    $this->assertFalse($constraint->validate(3)->getState());
    $this->assertFalse($constraint->validate(11)->getState());


    //Check that we can make a callable constraint
    $data = [
      function($data){
        if(strcmp('I Work',$data)=== 0){
          return TRUE;
        }
        return FALSE;
      }
    ];

    $constraint = ConstraintFactory::makeConstraint('Drupal\twhiston\DrushOptionValidator\Constraint\CallableConstraint',$data);
    $this->assertTrue($constraint->validate('I Work')->getState());
    $this->assertFalse($constraint->validate('I suck')->getState());

  }

  public function testMakeConstraints(){


    $constraints = [];
    $constraints[] = [
      'class' => 'Numeric\\GreaterThan',
      'args' => array(5),
    ];

    $constraints[] = [
      'class' => 'Numeric\\IsNumeric',
      'args' => array(),
    ];

    $constraints[] = [
      'class' => 'Broken\\Will Not Return',
      'args' => array(7,11),
    ];

    $constraints[] = [
      'class' => 'Numeric\\Between',
      'args' => array(7,11),
    ];


    /** @var Constraint[] $constraint */
    $constraints = ConstraintFactory::makeConstraints($constraints);

    $this->assertCount(3,$constraints);
    $this->assertInstanceOf('Drupal\twhiston\DrushOptionValidator\Constraint\Numeric\GreaterThan',$constraints[0]);
    $this->assertInstanceOf('Drupal\twhiston\DrushOptionValidator\Constraint\Numeric\IsNumeric',$constraints[1]);
    $this->assertInstanceOf('Drupal\twhiston\DrushOptionValidator\Constraint\Numeric\Between',$constraints[2]);

  }

}
