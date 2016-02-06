<?php

namespace Drupal\twhiston\FluentValidator;

use Drupal\twhiston\FluentValidator\Constraint\CallableConstraint;
use Drupal\twhiston\FluentValidator\VRule\VRule;
use twhiston\twLib\Reference\Stack;
use twhiston\twLib\Reference\Reference;

/**
 * Created by PhpStorm.
 * User: Thomas Whiston
 * Date: 01/02/2016
 * Time: 00:40
 */
class TreeFactory
{

    private $tree;

    /** @var Pointer */
    private $treePoint;

    //History is a reference stack, even if we wish it was made of pointers
    /** @var Stack */
    private $history;

    public function __construct()
    {
        $this->tree = [];
        $this->treePoint = new Reference($this->tree);
        $this->history = new Stack();
    }


    public function startRule($name){

        if($this->treePoint->getRef() instanceof VRule) {
            $this->history->takeReference($this->treePoint->getRef());//this should NOT be a reference, apparently
            $this->treePoint->getRef()->addRule(new VRule($name));
            /** @var Reference $t */
            $t = &$this->history->top();
            $a = &$t->getRef()->refTree();
            $k = count($a)-1;
            //$this->treePoint = &$a[$k];//WOOF!
            $this->treePoint->reset($a[$k]);

            return $this;

        }
        //Don't you wish PHP had c++ style pointers.......
        //This works but its no fun at all
        $this->history->takeReference($this->treePoint->getRef());//add a new entry to the history reference stack
        $this->history->top()->getRef()[$name] = new VRule($name);//make the new rule
        $this->treePoint->reset($this->history->top()->getRef()[$name]);
        //$this->tree = NULL;//uncomment this to test that all the references work, if it passes this and everything turns to null then bingo bango
        return $this;

    }




    public function endRule(){

        $p = $this->history->pop();
        $this->treePoint->reset($p->getRef());
        return $this;

    }

    function find_parent(&$array, $needle, $parent = null) {
        foreach ($array as $key => $value) {
            if($value instanceof VRule){

                $found = FALSE;
                if(strcmp($value->getName(),$needle) === 0){
                    $found = TRUE;
                    return $parent;
                }
                $pass = $parent;
                if (is_string($key)) {
                    $pass = $key;
                }
                $found = $this->find_parent($value->refTree(), $needle, $pass);
                if ($found !== false) {
                    return $array[$found];
                }
            }
        }
        return false;
    }

    public function addConstraint($const){
        if($this->treePoint->getRef() instanceof VRule){
            $this->treePoint->getRef()->addConstraint($const);
        } else {
            $this->treePoint[] = $const;
        }

        return $this;
    }

    public function getTree(){

        if($this->treePoint->getRef() != $this->tree){
            throw new \Exception('tree not closed');
        }
        return $this->tree;
    }

}