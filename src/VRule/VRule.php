<?php
/**
 * Created by PhpStorm.
 * User: Thomas Whiston
 * Date: 26/01/2016
 * Time: 14:21
 */

namespace Drupal\twhiston\FluentValidator\VRule;

use Drupal\twhiston\FluentValidator\Constraint\Constraint;
use Drupal\twhiston\FluentValidator\Result\ValidationResult;
use twhiston\twLib\Enum\Enum;


/**
 * Class VRule
 * Rules are the essence of the validation, create a rule with the same name as your data array key and add constraints and rules to its tree to be validated
 * Rules have a fluent interface so you can chain all your constraint creation together
 * $r = new VRule('field1');//rule name matches the field name in our data
 * $r->addConstraint(
 *      new CallableConstraint('is_string')
 *  ) ->addConstraint(
 *  new CallableConstraint(
 *      'strcmp' ,      //standard php function call
 *      ['correct'] ,   //pass an array of additional values to pass to your function, data to be validated is always arg 1, so these will be arg2,arg3...
 *      [ 0 => TRUE ] ) //Callable constraints expect a return value that can be mapped to a BOOL
 *                      //strcmp outputs 0 if true, so we need to remap this to TRUE.
 *                      // you only need to map TRUE values returned from your callable as not found values will equate to FALSE
 *                      // This means your validation can fail because you forgot to properly map the true state
 *  );
 * See the tests for lots of examples of how to do this
 * @package Drupal\twhiston\DrushOptionValidator
 */
class VRule
{

    /**
     * @var string The name of this rule, should match the field name of the data you with to validate
     */
    private $name;

    /** @var  Constraint|VRule[] An array of rules and constraints that we validate against */
    private $tree;

    /**
     * VRule constructor.
     * @param $name string rule name
     */
    public function __construct($name) {
        $this->name = $name;
        $this->messages = [];
        return $this;
    }

    /**
     * Add a constraint to the validation tree
     * @param \Drupal\twhiston\FluentValidator\Constraint\Constraint $constraint
     * @return $this
     */
    public function addConstraint(Constraint $constraint){
        $this->tree[] = $constraint;
        return $this;
    }

    /**
     * Add a rule to the validation tree
     * @param \Drupal\twhiston\FluentValidator\VRule\VRule $rule
     * @return $this
     */
    public function addRule(VRule $rule){
        $this->tree[] = $rule;
        return $this;
    }

    /**
     * Add a tree of validation rules to the existing rules
     * @param $tree ValidationResult|VRule[]
     * @return $this
     */
    public function addTree($tree){
        $tree = (!is_array($tree)) ? array($tree) : $tree;
        $this->tree = (is_array($this->tree))? array_merge($tree, $this->tree) : $tree;
        return $this;
    }

    /**
     * Set the tree
     * @param $tree ValidationResult|VRule[]
     * @return $this
     */
    public function setTree($tree){
        $tree = (!is_array($tree)) ? array($tree) : $tree;
        $this->tree = $tree;
        return $this;
    }

    /**
     * Get the name of this rule
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Should return an array of callables/closures, which return TRUE or FALSE
     * Try using the Constraints factory to make this
     * @return Constraint|VRule[]
     */
    public function getTree()
    {
        return $this->tree;
    }

    public function &refTree()
    {
        return $this->tree;
    }

}