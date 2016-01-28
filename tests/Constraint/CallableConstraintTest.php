<?php

/**
 * Created by PhpStorm.
 * User: Thomas Whiston
 * Date: 28/01/2016
 * Time: 01:04
 */

use Drupal\twhiston\DrushOptionValidator\Constraint\CallableConstraint;
class CallableConstraintTest extends PHPUnit_Framework_TestCase
{

    public function testCallableConstraint(){

        $c = new CallableConstraint('is_bool');
        $r = $c->validate(TRUE);
        $this->assertTrue($r->getState());
        $r = $c->validate(FALSE);
        $this->assertTrue($r->getState());
        $r = $c->validate(NULL);
        $this->assertFalse($r->getState());
        $r = $c->validate(0);
        $this->assertFalse($r->getState());
        $r = $c->validate(42);
        $this->assertFalse($r->getState());
        $r = $c->validate('go gadget go');
        $this->assertFalse($r->getState());

        $a = [
            "test" => "data",
        ];

        $c = new CallableConstraint('array_key_exists');
        $r = $c->validate(['args' => array('test', $a )]);
        $this->assertTrue($r->getState());
        $r = $c->validate(['args' => ['beast',$a]]);
        $this->assertFalse($r->getState());


    }

}
