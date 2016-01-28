<?php

namespace Drupal\twhiston\DrushOptionValidator\Constraint;

use twhiston\twLib\Object\Instantiate;


/**
 * Created by PhpStorm.
 * User: Thomas Whiston
 * Date: 26/01/2016
 * Time: 15:29
 */
class ConstraintFactory
{

    static public function makeConstraints($constraints)
    {

        $cout = [];
        foreach ($constraints as $constraint) {
            if (array_key_exists('class', $constraint) && array_key_exists(
                'args',
                $constraint
              )
            ) {
                $cout[] = ConstraintFactory::makeConstraint(
                  $constraint['class'],
                  $constraint['args']
                );
            }
        }

        return $cout;
    }

    public static function makeConstraint($class, $args)
    {

        $output = Instantiate::make(
          $class,
          $args,
          "Drupal\\twhiston\\DrushOptionValidator\\Constraint\\",
          'Drupal\twhiston\DrushOptionValidator\Constraint\Constraint'
        );
        if ($output != null) {
            return $output;
        } else {
            throw new \Exception('Could not make constraint');
        }

    }

}