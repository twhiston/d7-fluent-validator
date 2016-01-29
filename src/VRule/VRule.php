<?php
/**
 * Created by PhpStorm.
 * User: Thomas Whiston
 * Date: 26/01/2016
 * Time: 14:21
 */

namespace Drupal\twhiston\FluentValidator\VRule;

use Drupal\twhiston\FluentValidator\Constraint\Constraint;
use twhiston\twLib\Enum\Enum;


/**
 * Fluent Rule.
 * Add constraints to this
 * @package Drupal\twhiston\DrushOptionValidator
 */
class VRule
{

    private $name;

    /** @var  Constraint|VRule[] */
    private $tree;



    public function __construct($name) {
        $this->name = $name;
        $this->messages = [];
    }



    public function addConstraint(Constraint $constraint){
        $this->tree[] = $constraint;
        return $this;
    }

    public function addRule(VRule $rule){
        $this->tree[] = $rule;
        return $this;
    }

    public function addTree($tree){
        $tree = (!is_array($tree)) ? array($tree) : $tree;
        $this->tree = (is_array($this->tree))? array_merge($tree, $this->tree) : $tree;
        return $this;
    }

    public function setTree($tree){
        $tree = (!is_array($tree)) ? array($tree) : $tree;
        $this->tree = $tree;
        return $this;
    }

    /**
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

}