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
use Drupal\twhiston\FluentValidator\Result\ValidationResult;

use Drupal\twhiston\FluentValidator\Constraint\Numeric\GreaterThan;
use Drupal\twhiston\FluentValidator\Constraint\Numeric\LessThan;

/**
 * Class FluentValidatorTest
 */
class FluentValidatorTest extends PHPUnit_Framework_TestCase
{

    public function testSimpleFluentValidator(){

        //Here is some data
        $f1in = 'correct';//Imagine we got this from drush_get_option or something
        $lin = 5;//Same here

        //The data to validate
        $data = [
            'anum' => 25,
            'field1' => $f1in,
            'field2' => $lin
        ];

        //Some things we call may need some additional info from us.
        //strcmp needs a str to cmp,
        //greaterthan needs something to be greater than.
        //Make some rules
        $r = new VRule('field1');//rule name matches the field name in our data
        $r->addConstraint(
          new CallableConstraint('is_string')
        ) ->addConstraint(
          new CallableConstraint('strcmp' , //standard php function call
            ['correct'] ,  //pass an array of additional values to pass to your function, data to be validated is always arg 1, so these will be arg2,arg3...

            [ 0 => TRUE ] ) // Callable constraints expect a return value that can be mapped to a BOOL
                            // strcmp outputs 0 if true, so we need to remap this to TRUE.
                            // you only need to map TRUE values returned from your callable as not found values will equate to FALSE
                            // This means your validation can fail because you forgot to properly map the true state
        );

        $r2 = new VRule('anum');
        $r2->addConstraint(
          new GreaterThan(10)
        )->addConstraint(
          new LessThan(29)
        );

        $r3 = new VRule('lambda');
        $r3->addConstraint(
          new CallableConstraint(
              function($a,$b,$c){
                  //Real exciting!
                  if($a > $c && $a < $b){
                      return TRUE;
                  }
                  return FALSE;
              },
                [12,2]//$b and $c
          )
        );

        $vali = new FluentValidator();
        $result = $vali->addVRule($r)->addVRule($r2)->addVRule($r3)->validate($data);
        $this->assertTrue($result);

        $data = [
          'anum' => 30, //fails
          'field1' => $f1in ,
          'lambda' => $lin
        ];

        $result = $vali->reset()->addVRule($r)->addVRule($r2)->addVRule($r3)->validate($data);//you can pass a new set of options to reset, or pass nothing to keep existing
        $this->assertFalse($result);

        $data = [
          'anum' => 25,
          'field1' => $f1in,//fails
          'lambda' => $lin
        ];

        $r4 = new VRule('field1');//rule name matches the field name in our data
        $r4->addConstraint(
          new CallableConstraint('is_string')
        ) ->addConstraint(
          new CallableConstraint('strcmp' , //standard php function call
            ['incorrect'] ,  //pass an array of additional values to pass to your function, data to be validated is always arg 1, so these will be arg2,arg3...

            [ 0 => TRUE ] ) // Callable constraints expect a return value that can be mapped to a BOOL
                            // strcmp outputs 0 if true, so we need to remap this to TRUE.
                            // you only need to map TRUE values returned from your callable as not found values will equate to FALSE
                            // This means your validation can fail because you forgot to properly map the true state
        );

        $result = $vali->reset()->addVRule($r4)->addVRule($r2)->addVRule($r3)->validate($data);
        $this->assertFalse($result);



        $data = [
          'anum' => 25,
          'field1' => $f1in,
          'lambda' => $lin
        ];

        $r5 = new VRule('lambda');
        $r5->addConstraint(
          new CallableConstraint(
            function($a,$b,$c){
                //Real exciting!
                if($a > $c && $a < $b){
                    return TRUE;
                }
                return FALSE;
            },
            [3,2]//$b and $c
          )
        );

        $result = $vali->reset()->addVRule($r)->addVRule($r2)->addVRule($r5)->validate($data);
        $this->assertFalse($result);
        $rs = $vali->getMessages();
        $this->assertRegExp('/Validation Failed/',$rs['lambda'][0]);

    }

    public function testArrayFluentValidator(){

        //Test arrays

        $data = [
          'wrapped' => [
            'field1' => 'input',
            'field2' => 15
          ],
          'lambda' => 5
        ];

        //to pass nested arrays like this we can use a rule in a constraint
        $r = new VRule('wrapped');//we do not pass a default as each rule can deal with its own fields defaults

        //These are our sub rules
        $r1 = new VRule('field1');//rule name matches the field name in our data
        $r1->addConstraint(
          new CallableConstraint('is_string')
        ) ->addConstraint(
          new CallableConstraint('strcmp' , ['input'], [ 0 => TRUE ] )
        );

        $r2 = new VRule('field2');
        $r2->addConstraint(
          new GreaterThan(10)
        )->addConstraint(
          new LessThan(29)
        );

        //Add our sub rules to our main rule
        $r->addRule($r1)->addRule($r2);

        //Create a standard rule at the same level as 'wrapped' field
        $r3 = new VRule('lambda');
        $r3->addConstraint(
          new CallableConstraint(
            function($a,$b,$c){
                //Real exciting!
                if($a > $c && $a < $b){
                    return new ValidationResult(TRUE,'worked');
                }
                return new ValidationResult(FALSE,'failed');
            },
            [12,2]
          )
        );

        $vali = new FluentValidator();
        $result = $vali->addVRule($r)->addVRule($r3)->validate($data);//Add our wrapped rules and our normal rule
        $this->assertTrue($result);


        $data = [
          'wrapped' => [
            'field1' => 'nonput',
            'field2' => 15
          ]
        ];

        $result = $vali->reset()->addVRule($r)->validate($data);
        $this->assertFalse($result);

        $mes = $vali->getMessages();
        $res = $vali->getResults();


        //Now lets validate something stupid complicated and nested.


    }

}
