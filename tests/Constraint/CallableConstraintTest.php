<?php

/**
 * Created by PhpStorm.
 * User: Thomas Whiston
 * Date: 28/01/2016
 * Time: 01:04
 */

use Drupal\twhiston\FluentValidator\Constraint\CallableConstraint;
use Drupal\twhiston\FluentValidator\Result\ValidationResult;

class CallableConstraintTest extends PHPUnit_Framework_TestCase
{

    public function testCallableConstraint(){

        $c = new CallableConstraint('is_bool');
        /** @var ValidationResult $r */
        $r = $c->validate(TRUE);
        $this->assertTrue($r->getStatus());
        $r = $c->validate(FALSE);
        $this->assertTrue($r->getStatus());
        $r = $c->validate(NULL);
        $this->assertFalse($r->getStatus());
        $r = $c->validate(0);
        $this->assertFalse($r->getStatus());
        $r = $c->validate(42);
        $this->assertFalse($r->getStatus());
        $r = $c->validate('go gadget go');
        $this->assertFalse($r->getStatus());

        $a = [
            ["test" => "data"],//array_key_exists needs an array as its second parameter
        ];

        $c = new CallableConstraint('array_key_exists',$a);
        $r = $c->validate('test');
        $this->assertTrue($r->getStatus());

        $a = [
          ["beast" => "data"],//array_key_exists needs an array as its second parameter
        ];

        $r = $c->validate('beast');
        $this->assertFalse($r->getStatus());

        //TODO add more tests


    }

}
