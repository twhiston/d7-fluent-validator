<?php

/**
 * Created by PhpStorm.
 * User: Thomas Whiston
 * Date: 28/01/2016
 * Time: 23:50
 */

use Drupal\twhiston\FluentValidator\FluentValidator;
use Drupal\twhiston\FluentValidator\VRule\VRule;
use Drupal\twhiston\FluentValidator\Constraint\CallableConstraint;

use Drupal\twhiston\FluentValidator\Constraint\Numeric\GreaterThan;
use Drupal\twhiston\FluentValidator\Constraint\Numeric\LessThan;

/**
 * Class FluentValidatorTest
 * @group new
 */
class FluentValidatorTest extends PHPUnit_Framework_TestCase
{

    public function testSimpleFluentValidator(){

        //Here is some data
        $f1in = 'correct';//Imagine we got this from drush_get_option or something
        $lin = 5;//Same here

        $data = [
          'anum' => 25, // If your function call has a single input parameter pass it like this
          'field1' => ['args' => [$f1in, 'correct'] ],//if your function call takes multiple parameters you need to wrap them in ['args'=>[]]
          'lambda' => ['args' => [$lin,12,2]]//This might prove an issue if your callable only needs an array with a key called args,
                                             // but you could always wrap it in something else and unwrap it in your callback
                                             //  as far as i know no php native function needs an 'args' key in an array
        ];

        //Make some rules
        $r = new VRule('field1','default');//rule name matches the field name in our data
        $r->addConstraint(
          new CallableConstraint('is_string')
        ) ->addConstraint(
          new CallableConstraint('strcmp' , [ 0 => TRUE ] ) // Callable constraints expect a return value that can be mapped to a BOOL
                                                            // strcmp outputs 0 if true, so we need to remap this to TRUE.
                                                            // you only need to map TRUE values returned from your callable as not found values will equate to FALSE
                                                            // This means your validation can fail because you forgot to properly map the true state
        );

        $r2 = new VRule('anum',23);
        $r2->addConstraint(
          new GreaterThan(10)
        )->addConstraint(
          new LessThan(29)
        );

        $r3 = new VRule('lambda','you suck');
        $r3->addConstraint(
          new CallableConstraint(
              function($a,$b,$c){
                  //Real exciting!
                  if($a > $c && $a < $b){
                      return TRUE;
                  }
                  return FALSE;
              }
          )
        );

        $vali = new FluentValidator();
        $result = $vali->addVRule($r)->addVRule($r2)->addVRule($r3)->validate($data);
        $this->assertTrue($result);

        $data = [
          'anum' => 30, //fails
          'field1' => ['args' => [$f1in, 'correct'] ],
          'lambda' => ['args' => [$lin,12,2]]
        ];


        $result = $vali->reset()->addVRule($r)->addVRule($r2)->addVRule($r3)->validate($data);//you can pass a new set of options to reset, or pass nothing to keep existing
        $this->assertFalse($result);

        $data = [
          'anum' => 25,
          'field1' => ['args' => [$f1in, 'incorrect'] ],//fails
          'lambda' => ['args' => [$lin,12,2]]
        ];


        $result = $vali->reset()->addVRule($r)->addVRule($r2)->addVRule($r3)->validate($data);
        $this->assertFalse($result);

        $data = [
          'anum' => 25,
          'field1' => ['args' => [$f1in, 'incorrect'] ],
          'lambda' => ['args' => [$lin,3,2]]//fails
        ];


        $result = $vali->reset()->addVRule($r)->addVRule($r2)->addVRule($r3)->validate($data);
        $this->assertFalse($result);


    }

    public function testArrayFluentValidator(){

        //Test arrays

        $data = [
          'wrapped' => [
            'field1' => ['args' => 'input', 'incorrect'],
            'field2' => ['args' => 25, 26]
          ]
        ];

        //to pass nested arrays like this we can use a rule in a constraint
        $r3 = new VRule('wrapped');//we do not pass a default as each rule can deal with its own fields defaults



        //we can also parse this data in another way, by using a different notation in our VRule
        //use a namespace like formatting to apply it to nested data elements in your rule name


    }

}
