<?php

/**
 * Created by PhpStorm.
 * User: Thomas Whiston
 * Date: 28/01/2016
 * Time: 00:18
 */

use Drupal\twhiston\FluentValidator\Constraint\Numeric\Between;
use Drupal\twhiston\FluentValidator\Constraint\Numeric\GreaterThan;
use Drupal\twhiston\FluentValidator\Constraint\Numeric\GreaterThanEquals;
use Drupal\twhiston\FluentValidator\Constraint\Numeric\IsNumeric;
use Drupal\twhiston\FluentValidator\Constraint\Numeric\LessThan;
use Drupal\twhiston\FluentValidator\Constraint\Numeric\LessThanEquals;


class NumericTest extends PHPUnit_Framework_TestCase {

  public function testBetween(){

    /** @var Between $t */
    $t = new Between(12, 1456);

    $r = $t->validate(11);
    $this->assertFalse($r->getStatus());

    $r = $t->validate(12);
    $this->assertTrue($r->getStatus());

    $r = $t->validate(13);
    $this->assertTrue($r->getStatus());

    $r = $t->validate(1455);
    $this->assertTrue($r->getStatus());

    $r = $t->validate(1456);
    $this->assertTrue($r->getStatus());

    $r = $t->validate(1457);
    $this->assertFalse($r->getStatus());

    $r = $t->validate(24521);
    $this->assertFalse($r->getStatus());

  }

  public function testGreaterThan(){

    $t = new GreaterThan(23);

    $r = $t->validate(11);
    $this->assertFalse($r->getStatus());

    $r = $t->validate(23);
    $this->assertFalse($r->getStatus());

    $r = $t->validate(24);
    $this->assertTrue($r->getStatus());

    $r = $t->validate(1500);
    $this->assertTrue($r->getStatus());

  }

  public function testGreaterThanEquals(){

    $t = new GreaterThanEquals(23);

    $r = $t->validate(11);
    $this->assertFalse($r->getStatus());

    $r = $t->validate(23);
    $this->assertTrue($r->getStatus());

    $r = $t->validate(24);
    $this->assertTrue($r->getStatus());

    $r = $t->validate(1500);
    $this->assertTrue($r->getStatus());

  }

  public function testLessThan(){

    $t = new LessThan(23);

    $r = $t->validate(1);
    $this->assertTrue($r->getStatus());

    $r = $t->validate(11);
    $this->assertTrue($r->getStatus());

    $r = $t->validate(23);
    $this->assertFalse($r->getStatus());

    $r = $t->validate(24);
    $this->assertFalse($r->getStatus());

    $r = $t->validate(1500);
    $this->assertFalse($r->getStatus());

    $r = $t->validate(-11);
    $this->assertTrue($r->getStatus());

  }

  public function testLessThanEquals(){

    $t = new LessThanEquals(23);

    $r = $t->validate(1);
    $this->assertTrue($r->getStatus());

    $r = $t->validate(11);
    $this->assertTrue($r->getStatus());

    $r = $t->validate(23);
    $this->assertTrue($r->getStatus());

    $r = $t->validate(24);
    $this->assertFalse($r->getStatus());

    $r = $t->validate(1500);
    $this->assertFalse($r->getStatus());

    $r = $t->validate(-11);
    $this->assertTrue($r->getStatus());

  }

}
