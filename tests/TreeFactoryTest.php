<?php

include_once('D7Form.php');
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

/**
 * Class TreeFactoryTest
 * @group new
 */
class TreeFactoryTest extends PHPUnit_Framework_TestCase
{


    public function testDrupalData(){

        $data = getDrupalData();

        $iss = new CallableConstraint('is_string');

        $tf = new TreeFactory();
        $tf->startRule('submitted')
            ->startRule('number')->addConstraint( new CallableConstraint('is_numeric'))->endRule()
            ->startRule('street')->addConstraint( $iss )->addConstraint( new CallableConstraint('is_null' , NULL, ['false' => TRUE]))->endRule()
            ->startRule('postcode')
                ->addConstraint( new CallableConstraint(
                    function($data){
                        //At least 2 numbers && last 3 characters are a number and a letter
                        if(preg_match('/^(?=.*\d.*\d)/',$data) && preg_match('/.*[0-9]+[a-zA-Z]+[a-zA-Z]$/',$data)){
                         return new ValidationResult(TRUE);
                        }
                        return new ValidationResult(FALSE);
                    }
                ))
            ->endRule()
            ->startRule('town')->addConstraint( $iss )->endRule()
            ->startRule('country')->addConstraint( new CallableConstraint('strcmp', 'uk', [0 => TRUE ] ))->endRule()
          ->endRule()
          ->startRule('details')
            ->startRule('nid')->addConstraint( new CallableConstraint('is_numeric'))->endRule()
            ->startRule('sid')->endRule()
            ->startRule('uid')->addConstraint( new CallableConstraint('is_numeric'))->endRule()
            ->startRule('page_num')->addConstraint( new CallableConstraint('is_numeric'))->endRule()
            ->startRule('page_count')->addConstraint( new CallableConstraint('is_numeric'))->endRule()
            ->startRule('finished')->addConstraint( new CallableConstraint('is_numeric'))->endRule()
          ->endRule()
          ->startRule('op')->addConstraint(new CallableConstraint('strcmp','Submit',[0 => TRUE ]))->endRule()
          ->startRule('form_token')->addConstraint(new CallableConstraint('is_null',NULL,['false' => TRUE]))->endRule();

        $vali = new FluentValidator();
        try {
            $vali->setVRules($tf->getTree());
        } catch (\Exception $e){
            $this->assertTrue(FALSE);
        }

        $s = $vali->validate($data);
        $this->assertTrue($s);
        $r = $vali->getResults();

        //Make sure we have all the results that we should
        $this->assertArrayHasKey('submitted',$r);
        $this->assertArrayHasKey('number',$r['submitted']);
        $this->assertArrayHasKey('street',$r['submitted']);
        $this->assertArrayHasKey('postcode',$r['submitted']);
        $this->assertArrayHasKey('town',$r['submitted']);
        $this->assertArrayHasKey('country',$r['submitted']);

        $this->assertArrayHasKey('details',$r);
        $this->assertArrayHasKey('nid',$r['details']);
        $this->assertArrayHasKey('sid',$r['details']);
        $this->assertArrayHasKey('uid',$r['details']);
        $this->assertArrayHasKey('page_num',$r['details']);
        $this->assertArrayHasKey('page_count',$r['details']);
        $this->assertArrayHasKey('finished',$r['details']);

        $this->assertArrayHasKey('op',$r);
        $this->assertArrayHasKey('form_token',$r);

    }

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
                        //you dont need to do this this way, you could just use 'is_string' as the arg for the Constraint
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
        $this->assertArrayHasKey('magazine',$r);
        $this->assertArrayHasKey('author',$r['magazine']);
        $this->assertArrayHasKey('publication',$r['magazine']);
        $this->assertArrayHasKey('year',$r['magazine']);

        $this->assertCount(2,$r['magazine']['year']);

        $this->assertTrue($r['magazine']['author'][0]->getStatus());
        $this->assertFalse($r['magazine']['publication'][0]->getStatus());
        $this->assertFalse($r['magazine']['year'][0]->getStatus());
        $this->assertFalse($r['magazine']['year'][1]->getStatus());

    }


}
