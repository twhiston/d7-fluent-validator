<?php
/**
 * Created by PhpStorm.
 * User: Thomas Whiston
 * Date: 26/01/2016
 * Time: 15:29
 */

namespace Drupal\twhiston\FluentValidator\Constraint;

use twhiston\twLib\Object\Instantiate;


/**
 * Class ConstraintFactory
 * Make a constraint or an array of constraints
 * @package Drupal\twhiston\FluentValidator\Constraint
 */
class ConstraintFactory
{

    /**
     * Pass an array of names and get an array of constraints
     * @param $constraints
     * @return array
     * @throws \Exception
     */
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

    /**
     * @param $class string class name for FluentValidator types or a fully qualified namespace for others
     * @param $args mixed[] arguments to pass to the Constraint constructor
     * @return null
     * @throws \Exception If the constraint cant be created this will throw an exception
     */
    public static function makeConstraint($class, $args)
    {

        $output = Instantiate::make(
          $class,
          $args,
          "Drupal\\twhiston\\FluentValidator\\Constraint\\",
          'Drupal\twhiston\FluentValidator\Constraint\Constraint'
        );
        if ($output != null) {
            return $output;
        } else {
            throw new \Exception('Could not make constraint');
        }

    }

}