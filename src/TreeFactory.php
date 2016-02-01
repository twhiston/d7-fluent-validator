<?php

namespace Drupal\twhiston\FluentValidator;

use Drupal\twhiston\FluentValidator\VRule\VRule;

/**
 * Created by PhpStorm.
 * User: Thomas Whiston
 * Date: 01/02/2016
 * Time: 00:40
 */
class TreeFactory
{

    private $tree;

    private $treePoint;

    private $lastTreePoint;

    public function __construct()
    {
        $this->tree = [];
        $this->treePoint = &$this->tree;
        $this->lastTreePoint = &$this->treePoint;

    }


    public function startRule($name){

        $this->lastTreePoint = $this->treePoint;
        if($this->treePoint instanceof VRule){
            $this->treePoint->addRule( new VRule($name) );
            $this->treePoint = end($this->treePoint->refTree());//WOOF!
            return $this;
        }

        $this->treePoint[$name] = new VRule($name);
        $this->treePoint = &$this->treePoint[$name];
        return $this;

    }

    public function endRule(){

        $this->treePoint = $this->lastTreePoint;
        return $this;

    }

    public function addConstraint($const){
        if($this->treePoint instanceof VRule){
            $this->treePoint->addConstraint($const);
        } else {
            $this->treePoint[] = $const;
        }

        return $this;
    }

    public function getTree(){

        if($this->treePoint != $this->tree){
            throw new \Exception('tree not closed');
        }
        return $this->tree;
    }

}