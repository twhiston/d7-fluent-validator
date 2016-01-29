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

abstract class NoDefault extends Enum
{
    const No = 0;
}

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

    private $default;


    public function __construct($name, $default = NoDefault::No) {
        $this->name = $name;
        $this->default = $default;
        $this->messages = [];
    }

    public function setDefaultValue($default = NoDefault::No) {
        $this->default = $default;
        return $this;
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
     * @return mixed
     */
    public function getDefault()
    {
        return $this->default;
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