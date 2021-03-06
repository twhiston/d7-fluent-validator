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


//Test some actual drupal input for validation
include_once('D7Form.php');

/**
 * Class FluentValidatorTest
 */
class FluentValidatorTest extends PHPUnit_Framework_TestCase
{

    public function tearDown()
    {
        Mockery::close();
    }

    public function testSimpleFluentValidator(){

        //Here is some data
        $f1in = 'correct';//Imagine we got this from drush_get_option or something
        $lin = 5;//Same here

        //The data to validate
        $data = [
            'anum' => 25,
            'field1' => $f1in,
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

        $options['loglevel'] = 'debug';//PSR-3 log level please
        $vali = new FluentValidator();
        $result = $vali->setOptions($options)->addVRule($r)->addVRule($r2)->validate($data);
        $this->assertTrue($result);

        $mes = $vali->getMessages();
        $this->assertRegExp('/Validation Passed/',$mes['field1'][0]);
        $this->assertRegExp('/Validation Passed/',$mes['field1'][1]);
        $this->assertRegExp('/Validation Passed/',$mes['anum'][0]);
        $this->assertRegExp('/Validation Passed/',$mes['anum'][1]);

        $res = $vali->getResults();
        $this->assertInstanceOf('Drupal\\twhiston\\FluentValidator\\Result\\ValidationResult',$res['field1'][0]);
        $this->assertTrue($res['field1'][0]->getStatus());
        $this->assertInstanceOf('Drupal\\twhiston\\FluentValidator\\Result\\ValidationResult',$res['field1'][1]);
        $this->assertTrue($res['field1'][1]->getStatus());
        $this->assertInstanceOf('Drupal\\twhiston\\FluentValidator\\Result\\ValidationResult',$res['anum'][0]);
        $this->assertTrue($res['anum'][0]->getStatus());
        $this->assertInstanceOf('Drupal\\twhiston\\FluentValidator\\Result\\ValidationResult',$res['anum'][1]);
        $this->assertTrue($res['anum'][1]->getStatus());

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

        $data = [
          'anum' => 30, //fails
          'field1' => $f1in ,
          'lambda' => $lin
        ];

        $result = $vali->reset($options)->addVRule($r)->addVRule($r2)->addVRule($r3)->validate($data);//you can pass a new set of options to reset, or pass nothing to keep existing
        $this->assertFalse($result);

        $mes = $vali->getMessages();
        $this->assertRegExp('/Validation Passed/',$mes['field1'][0]);
        $this->assertRegExp('/Validation Passed/',$mes['field1'][1]);
        $this->assertRegExp('/Validation Passed/',$mes['anum'][0]);
        $this->assertRegExp('/Validation Failed/',$mes['anum'][1]);
        $this->assertRegExp('/Validation Passed/',$mes['lambda'][0]);

        $res = $vali->getResults();
        $this->assertInstanceOf('Drupal\\twhiston\\FluentValidator\\Result\\ValidationResult',$res['field1'][0]);
        $this->assertTrue($res['field1'][0]->getStatus());
        $this->assertInstanceOf('Drupal\\twhiston\\FluentValidator\\Result\\ValidationResult',$res['field1'][1]);
        $this->assertTrue($res['field1'][1]->getStatus());
        $this->assertInstanceOf('Drupal\\twhiston\\FluentValidator\\Result\\ValidationResult',$res['anum'][0]);
        $this->assertTrue($res['anum'][0]->getStatus());
        $this->assertInstanceOf('Drupal\\twhiston\\FluentValidator\\Result\\ValidationResult',$res['anum'][1]);
        $this->assertFalse($res['anum'][1]->getStatus());
        $this->assertInstanceOf('Drupal\\twhiston\\FluentValidator\\Result\\ValidationResult',$res['lambda'][0]);
        $this->assertTrue($res['lambda'][0]->getStatus());


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

        $mes = $vali->getMessages();
        $this->assertRegExp('/Validation Passed/',$mes['field1'][0]);
        $this->assertRegExp('/Validation Failed/',$mes['field1'][1]);
        $this->assertRegExp('/Validation Passed/',$mes['anum'][0]);
        $this->assertRegExp('/Validation Passed/',$mes['anum'][1]);
        $this->assertRegExp('/Validation Passed/',$mes['lambda'][0]);

        $res = $vali->getResults();
        $this->assertInstanceOf('Drupal\\twhiston\\FluentValidator\\Result\\ValidationResult',$res['field1'][0]);
        $this->assertTrue($res['field1'][0]->getStatus());
        $this->assertInstanceOf('Drupal\\twhiston\\FluentValidator\\Result\\ValidationResult',$res['field1'][1]);
        $this->assertFalse($res['field1'][1]->getStatus());
        $this->assertInstanceOf('Drupal\\twhiston\\FluentValidator\\Result\\ValidationResult',$res['anum'][0]);
        $this->assertTrue($res['anum'][0]->getStatus());
        $this->assertInstanceOf('Drupal\\twhiston\\FluentValidator\\Result\\ValidationResult',$res['anum'][1]);
        $this->assertTrue($res['anum'][1]->getStatus());
        $this->assertInstanceOf('Drupal\\twhiston\\FluentValidator\\Result\\ValidationResult',$res['lambda'][0]);
        $this->assertTrue($res['lambda'][0]->getStatus());

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

        $rules = [
          $r,$r2,$r5
        ];


        $result = $vali->reset()->setOptions($options)->addVRules($rules)->validate($data);
        $this->assertFalse($result);

        $mes = $vali->getMessages();
        $this->assertRegExp('/Validation Passed/',$mes['field1'][0]);
        $this->assertRegExp('/Validation Passed/',$mes['field1'][1]);
        $this->assertRegExp('/Validation Passed/',$mes['anum'][0]);
        $this->assertRegExp('/Validation Passed/',$mes['anum'][1]);
        $this->assertRegExp('/Validation Failed/',$mes['lambda'][0]);

        $res = $vali->getResults();
        $this->assertInstanceOf('Drupal\\twhiston\\FluentValidator\\Result\\ValidationResult',$res['field1'][0]);
        $this->assertTrue($res['field1'][0]->getStatus());
        $this->assertInstanceOf('Drupal\\twhiston\\FluentValidator\\Result\\ValidationResult',$res['field1'][1]);
        $this->assertTrue($res['field1'][1]->getStatus());
        $this->assertInstanceOf('Drupal\\twhiston\\FluentValidator\\Result\\ValidationResult',$res['anum'][0]);
        $this->assertTrue($res['anum'][0]->getStatus());
        $this->assertInstanceOf('Drupal\\twhiston\\FluentValidator\\Result\\ValidationResult',$res['anum'][1]);
        $this->assertTrue($res['anum'][1]->getStatus());
        $this->assertInstanceOf('Drupal\\twhiston\\FluentValidator\\Result\\ValidationResult',$res['lambda'][0]);
        $this->assertFalse($res['lambda'][0]->getStatus());

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

        $a = [
          new CallableConstraint('is_string'),
          new CallableConstraint('strcmp' , ['input'], [ 0 => TRUE ] )
        ];

        $r1->setTree($a);

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

        $mes = $vali->getMessages();
        $res = $vali->getResults();

        $this->assertRegExp('/Validation Passed/',$mes['wrapped']['field1'][0]);
        $this->assertRegExp('/Validation Passed/',$mes['wrapped']['field1'][1]);
        $this->assertRegExp('/Validation Passed/',$mes['wrapped']['field2'][0]);
        $this->assertRegExp('/Validation Passed/',$mes['wrapped']['field2'][1]);
        $this->assertRegExp('/worked/',$mes['lambda'][0]);


        $data = [
          'wrapped' => [
            'field1' => 'nonput',
            'field2' => 15
          ]
        ];

        $result = $vali->reset()->addVRule($r)->validate($data);
        $this->assertFalse($result);

        $mes = $vali->getMessages();
        $this->assertRegExp('/Validation Passed/',$mes['wrapped']['field1'][0]);
        $this->assertRegExp('/Validation Failed/',$mes['wrapped']['field1'][1]);
        $this->assertRegExp('/Validation Passed/',$mes['wrapped']['field2'][0]);
        $this->assertRegExp('/Validation Passed/',$mes['wrapped']['field2'][1]);

        $res = $vali->getResults();
        $this->assertInstanceOf('Drupal\\twhiston\\FluentValidator\\Result\\ValidationResult',$res['wrapped']['field1'][0]);
        $this->assertTrue($res['wrapped']['field1'][0]->getStatus());
        $this->assertInstanceOf('Drupal\\twhiston\\FluentValidator\\Result\\ValidationResult',$res['wrapped']['field1'][1]);
        $this->assertFalse($res['wrapped']['field1'][1]->getStatus());
        $this->assertInstanceOf('Drupal\\twhiston\\FluentValidator\\Result\\ValidationResult',$res['wrapped']['field2'][0]);
        $this->assertTrue($res['wrapped']['field2'][0]->getStatus());
        $this->assertInstanceOf('Drupal\\twhiston\\FluentValidator\\Result\\ValidationResult',$res['wrapped']['field2'][1]);
        $this->assertTrue($res['wrapped']['field2'][0]->getStatus());


        //Now lets validate something stupid complicated and nested.


    }

    public function testDrupalInput(){

        $this->runDrupalDataValidation();
    }

    private function runDrupalDataValidation(){

        //Get some potted form values
        $form_values = getDrupalData();

        //create the top level of the data array
        $sub = new VRule('submitted');

        /**
         * Our submitted data is
         * 'number' => '123',
         * 'street' => 'fake street',
         * 'postcode' => 'PA1 7GT',
         * 'town' => 'plymouth',
         * 'country' => 'uk',
         */
        $isn = new VRule('number');
        $isn->addConstraint(new CallableConstraint('is_numeric'));

        $str = new VRule('street');
        $str->addConstraint( new CallableConstraint('is_numeric',NULL,['true' => FALSE, 'false' => TRUE]));//make this !is_numeric by using an output map

        $post = new VRule('postcode');
        $post->addConstraint( new CallableConstraint(
        //preg_match must be in a lambda because it takes the data as the second argument,
        //any simple string function calls will always send data as the first parameter
          function($data){
              //At least 2 numbers && last 3 characters are a number and a letter
              if(preg_match('/^(?=.*\d.*\d)/',$data) && preg_match('/.*[0-9]+[a-zA-Z]+[a-zA-Z]$/',$data)){
                  return new ValidationResult(TRUE);
              }
              return new ValidationResult(FALSE);
          }
        ));

        //What can we validate for town?
        //$town = new VRule('town');

        $country = new VRule('country');
        $uk = new CallableConstraint(
          function($data){
              if (preg_match(
                '/^uk/',
                strtolower($data))) {
                  return new ValidationResult(TRUE);
              }
              return new ValidationResult(FALSE);
          }
        );
        $country->addConstraint( $uk );


        //Our submitted array key has all the other keys under it, so add them all to the 'submitted' rule tree
        $sub->addRule($isn)->addRule($str)->addRule($post)->addRule($country);
        //Validate the tree
        $vali = new FluentValidator();
        $state = $vali->addVRule($sub)->validate($form_values);
        $this->assertTrue($state);

        return $vali;
    }

    public function testGetFailedRules(){

        /** @var FluentValidator $vali */
        $vali = $this->runDrupalDataValidation();
        $f = $vali->getFailedRules();
        $this->assertCount(0,$f);

        //Now test with some stuff that actually fails
        //Get some potted form values
        $form_values = getDrupalData();

        //create the top level of the data array
        $sub = new VRule('submitted');

        /**
         * Our submitted data is
         * 'number' => '123',
         * 'street' => 'fake street',
         * 'postcode' => 'PA1 7GT',
         * 'town' => 'plymouth',
         * 'country' => 'uk',
         */
       // $isn = new VRule('number');
       // $isn->addConstraint(new CallableConstraint('is_numeric'));

        $str = new VRule('street');
        $str->addConstraint( new CallableConstraint('is_numeric'));

      //  $post = new VRule('postcode');
      //  $post->addConstraint( new CallableConstraint(
        //preg_match must be in a lambda because it takes the data as the second argument,
        //any simple string function calls will always send data as the first parameter
//          function($data){
//              //At least 2 numbers && last 3 characters are a number and a letter
//              if(preg_match('/^(?=.*\d.*\d)/',$data) && preg_match('/.*[0-9]+[a-zA-Z]+[a-zA-Z]$/',$data)){
//                  return new ValidationResult(TRUE);
//              }
//              return new ValidationResult(FALSE);
//          }
//        ));

        //What can we validate for town?
        //$town = new VRule('town');

        $country = new VRule('country');
        $uk = new CallableConstraint(
          function($data){
              if (preg_match(
                '/^usa/',
                strtolower($data))) {
                  return new ValidationResult(TRUE);
              }
              return new ValidationResult(FALSE);
          }
        );
        $country->addConstraint( $uk );


        //Our submitted array key has all the other keys under it, so add them all to the 'submitted' rule tree
        $sub->addRule($str)->addRule($country);
        //Validate the tree
        $vali = new FluentValidator();
        $state = $vali->addVRule($sub)->validate($form_values);

        $this->assertFalse($state);
        $f = $vali->getFailedRules();
        $this->assertCount(1,$f);
        $this->assertArrayHasKey('submitted',$f);
        $this->assertCount(2,$f['submitted']);

        $frn = $vali->getFailedRuleNames();
        $this->assertCount(2,$frn);
        $this->assertRegExp('/^street/',$frn[0]);
        $this->assertRegExp('/^country/',$frn[1]);

    }

    public function testNumericallyKeyedArrays(){

        $data = [ 'magazines' => [
          [ 'author' => 'Steve Dongus', 'publication' => 'penthouse', 'year' => 2015],
          [ 'author' => 'Dr. Mantis Toboggan', 'publication' => '34e3se#@&#n', 'year' => '(*#HNS:']
         ]
        ];

        $c = function($data){
            if (ctype_alpha(str_replace(' ', '', $data)))
            {
                return new ValidationResult(TRUE);
            }
            return new ValidationResult(FALSE);
        };

        $author = new VRule('author');
        $author->addConstraint(
          new CallableConstraint(
              $c
          )
        );
        $publication = new VRule('publication');
        $publication->addConstraint(
          new CallableConstraint(
            $c
          )
        );
        $year = new VRule('year');
        $year->addConstraint( new CallableConstraint('is_numeric'));

        $magazines = new VRule('magazines');
        foreach ($data['magazines'] as $id => $mag) {

            $v = new VRule((string)$id);//The id MUST be a string, even if our data array is numerically keyed
            $v->addRule($author)->addRule($publication)->addRule($year);
            $magazines->addRule($v);
        }

        $vali = new FluentValidator();
        $s = $vali->addVRule($magazines)->validate($data);
        $this->assertFalse($s);

        $r = $vali->getResults();

        $data = [ 'magazines' => [
                [ 'author' => 'Steve Dongus', 'publication' => 'penthouse', 'year' => 2015],
                [ 'author' => 'Dr Mantis Toboggan', 'publication' => 'the philadelphia chronicle', 'year' => 2016]
            ]
        ];

        $s = $vali->clearResults()->validate($data);
        $this->assertTrue($s);

        //Root array is numeric
        $data = [
          [ 'author' => 'Steve Dongus', 'publication' => 'penthouse', 'year' => 2015],
          [ 'author' => 'Dr Mantis Toboggan', 'publication' => 'the philadelphia chronicle', 'year' => 2016]
        ];

        $vali->reset();
        foreach ($data as $id => $mag) {
            $v = new VRule((string)$id);//The id MUST be a string, even if our data array is numerically keyed
            $v->addRule($author)->addRule($publication)->addRule($year);
            $vali->addVRule($v);
        }
        $s = $vali->validate($data);
        $r = $vali->getResults();
        $this->assertTrue($s);

    }

    public function testDeeperArrays(){

        /**
         * This data is a bit more complicated as it has 2 sets of fields that have the same sort of data, of course we can validate this, but we also have multiple arrays
         * and a deeper nesting level than we have for other stuff
         */
        $data = [
          'field1' => [
              'summary' => 'It is a tale. Told by an idiot, full of sound and fury, Signifying nothing'
              ,
              'author' => [
                  'name' => 'Tom Whiston'
              ],
              'reviews' => [
                  'magazines' => [
                    [ 'author' => 'Steve Dongus', 'publication' => 'penthouse', 'year' => 2015],
                    [ 'author' => 'Dr Mantis Toboggan', 'publication' => 'the philadelphia chronicle', 'year' => 2016],
                  ],
                'tv' => [
                  [ 'author' => 'Steve Dongus', 'publication' => 'penthouse', 'year' => 2015],
                  [ 'author' => 'Dr Mantis Toboggan', 'publication' => 'the philadelphia chronicle', 'year' => 2016],
                ],

              ]
          ],
            //This one is corrupt
          'field2' => [
            'summary' => 'hspjyshp828sosoyesao289haosyhoas',
            'author' => [
              'name' => '12423321#*U$#@(!'
            ],
            'reviews' => [
              'magazines' => [
                [ 'author' => 'RRR**@*F(HP(*S(*#@*#', 'publication' => '#E@#I', 'year' => '$#NEES@!123o'],
              ],
              'tv' => [
                [ 'author' => 'hs.elshe989988998&&', 'publication' => '#@#2sd', 'year' => ')('],
                [ 'author' => 's&WN3SUxSA)({', 'publication' => '}{)+(', 'year' => ''],
              ]
            ]
          ],
        ];

        //Building a validation tree for something like this its best to work outwards
        $summary = new VRule('summary');
        $summary->addConstraint( new CallableConstraint('is_string'));

        $author = new VRule('author');
        $name = new VRule('name');

        $nospecial = new CallableConstraint(
          function($data) {
              if (ctype_alpha(str_replace(' ', '', $data))) {
                  return new ValidationResult(true);
              }
              return new ValidationResult(false);
          }
        );

        $name->addConstraint(
            $nospecial
        );
        $author->addRule($name);

        $rauthor = new VRule('author');
        $rauthor->addConstraint(
            $nospecial
        );
        $publication = new VRule('publication');
        $publication->addConstraint(
            $nospecial
        );

        $year = new VRule('year');
        $year->addConstraint( new CallableConstraint('is_numeric'));

        //Some more top level keys just involve adding things together
        $reviews = new VRule('reviews');
        foreach ($data['field1']['reviews'] as $type => $dt) {
            $r = new VRule($type);
            foreach ($dt as $nm => $item) {
                $sub = new VRule((string)$nm);
                $sub->addRule($rauthor)->addRule($publication)->addRule($year);
                $r->addRule($sub);
            }
            $reviews->addRule($r);
        }

        $field1 = new VRule('field1');
        $field1->addRule($summary)->addRule($author)->addRule($reviews);

        $field2 = new VRule('field2');
        $reviews2 = new VRule('reviews');
        foreach ($data['field2']['reviews'] as $type => $dt) {
            $r = new VRule($type);
            foreach ($dt as $nm => $item) {
                $sub = new VRule((string)$nm);
                $sub->addRule($rauthor)->addRule($publication)->addRule($year);
                $r->addRule($sub);
            }
            $reviews2->addRule($r);
        }
        $field2->addRule($summary)->addRule($author)->addRule($reviews2);

        $vali = new FluentValidator();

        $vali->addVRule($field1);
        $s = $vali->validate($data);
        $r = $vali->getResults();
        $this->assertTrue($s);

        $vali->clearResults()->addVRule($field2);
        $s = $vali->validate($data);
        $this->assertFalse($s);

        $r = $vali->getResults();

    }

}
