<?php

/**
 * Created by PhpStorm.
 * User: Thomas Whiston
 * Date: 01/02/2016
 * Time: 00:41
 */

use Drupal\twhiston\FluentValidator\VRule\VRule;
use Drupal\twhiston\FluentValidator\TreeFactory;

use Drupal\twhiston\FluentValidator\Constraint\Numeric\Between;
use Drupal\twhiston\FluentValidator\Constraint\CallableConstraint;

use Drupal\twhiston\FluentValidator\Result\ValidationResult;

use Drupal\twhiston\FluentValidator\FluentValidator;


class TreeFactoryTest extends PHPUnit_Framework_TestCase
{

    public function testTreeFactory(){


          $data = [ 'magazine' => [
             'author' => 'Steve Dongus',
             'publication' => 'penthouse',
             'year' => 2015,
           ]
          ];


        /**
         * Building a tree with the factory is loads prettier than stringing it together ourselves
         */
        $tf = new TreeFactory();
        $tf->startRule('magazine')
                ->startRule('author')
                    ->addConstraint( new CallableConstraint(
                        function($data){
                            if(is_string($data)){
                                return new ValidationResult(TRUE);
                            }
                            return new ValidationResult(FALSE);
                        }
                    ) )
                ->endRule()
                ->startRule('publication')
                    ->addConstraint( new CallableConstraint(
                        function($data){
                            if (ctype_alnum($data)){
                                return new ValidationResult(TRUE);
                            }
                            return new ValidationResult(FALSE);
                        }
                    ))
                ->endRule()
                ->startRule('year')
                    ->addConstraint( new Between(2010,2016) )
                    ->addConstraint( new CallableConstraint('is_numeric') )
                ->endRule();
        //NOTE THAT THERE IS A MISTAKE HERE, THIS WILL THROW

        $vali = new FluentValidator();
        try {
            $vali->addVRules($tf->getTree());
        } catch(\Exception $e){
            //getTree threw because we didnt close our structure
            $this->assertRegExp('/^tree not closed/',$e->getMessage());
        }

        //End our rules properly
        $tf->endRule();

        try {
            $vali->reset()->addVRules($tf->getTree());
        } catch(\Exception $e){
            //OH DEAR! THIS SHOULD NOT HAPPEN
            $this->assertFalse(TRUE);
        }

        $s = $vali->validate($data);
        $r = $vali->getResults();
        $this->assertTrue($s);

        //Test some failing data
        $data = [ 'magazine' => [
          'author' => 'Dr. Mantis Toboggan',
          'publication' => '34e3se#@&#n',
          'year' => '(*#HNS:'
        ]];

        $s = $vali->clearResults()->validate($data);
        $r = $vali->getResults();
        $this->assertFalse($s);

    }

}
