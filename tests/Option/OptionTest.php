<?php

/**
 * Created by PhpStorm.
 * User: Thomas Whiston
 * Date: 26/01/2016
 * Time: 18:22
 */

use Drupal\twhiston\DrushOptionValidator\Option\Option;
use Drupal\twhiston\DrushOptionValidator\Constraint\ConstraintFactory;

class OptionTest extends PHPUnit_Framework_TestCase {

  public function testOption(){

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


    $option = new Option('tester',ConstraintFactory::makeConstraints($constraints),666);

    $this->assertCount(3,$option->getValidationConstraints());
    $this->assertRegExp('/tester/',$option->getOptionName());
    $this->assertEquals(666,$option->getDefaultValue());

  }

}
