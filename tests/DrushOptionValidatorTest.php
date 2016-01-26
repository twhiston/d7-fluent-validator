<?php

/**
 * Created by PhpStorm.
 * User: Thomas Whiston
 * Date: 26/01/2016
 * Time: 18:34
 */

use Drupal\px\DrushOptionValidator\DrushOptionValidator;
use Drupal\px\DrushOptionValidator\Option\Option;
use Drupal\px\DrushOptionValidator\Constraint\ConstraintFactory;

class DrushOptionValidatorTest extends PHPUnit_Framework_TestCase {

  public function testDrushOptionValidator(){

    $options = [];

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

    $options[] = new Option('field_1',ConstraintFactory::makeConstraints($constraints));

    $constraints = [];
    $constraints[] = [
      'class' => 'CallableConstraint',
      'args' => array(
        function($data){
          return($data < 10000000)?TRUE:FALSE;
        }
      ),
    ];

    $options[] = new Option('field_2',ConstraintFactory::makeConstraints($constraints));
    $options[] = new Option('field_3',ConstraintFactory::makeConstraints($constraints));


    $vali = new DrushOptionValidator($options);

    $data = [];
    $data['field_1'] = 9;
    $data['field_2'] = 1000000;

    $this->assertTrue($vali->validate($data));

    $data['field_3'] = 100000000;

    $this->assertFalse($vali->validate($data));

  }

}