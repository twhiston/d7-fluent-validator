<?php

/**
 * Created by PhpStorm.
 * User: Thomas Whiston
 * Date: 26/01/2016
 * Time: 18:22
 */

use Drupal\twhiston\FluentValidator\VRule\VRule;
use Drupal\twhiston\FluentValidator\Constraint\ConstraintFactory;

class OptionTest extends PHPUnit_Framework_TestCase {

  public function testOption(){

    $constraints = [];
    $constraints[] = [
      'class' => 'Numeric\\GreaterThan',
      'args' => array(5),
    ];

    $constraints[] = [
      'class' => 'Numeric\\Between',
      'args' => array(7,11),
    ];

    $option = new VRule('tester');
    $option->addTree(ConstraintFactory::makeConstraints($constraints));

    $this->assertEquals(2,count($option->getTree()));
    $this->assertRegExp('/tester/',$option->getName());

    $constraints[] = [
      'class' => 'Broken\\Will Not Return',
      'args' => array(7,11),
    ];

    try {
      $option = new VRule('tester',ConstraintFactory::makeConstraints($constraints),666);
    } catch (Exception $e){
      $this->assertRegExp('/Could not make constraint/',$e->getMessage());
    }
  }

}
