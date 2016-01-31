<?php
/**
 * Created by PhpStorm.
 * User: Thomas Whiston
 * Date: 26/01/2016
 * Time: 14:49
 */

namespace Drupal\twhiston\FluentValidator\Result;


/**
 * Class ValidationResult
 * Simple wrapper for a result that allows us to store a message when we get a result
 * @package Drupal\twhiston\FluentValidator\Result
 */
class ValidationResult
{
    /** @var boolean */
    private $status;

    /** @var  string */
    private $message;


    /**
     * ValidationResult constructor.
     * @param $status boolean
     * @param null $message
     */
    public function __construct($status, $message = null)
    {
        $this->status = $status;
        $this->message = $message;
    }

    /**
     * @return boolean
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @return string|null
     */
    public function getMessage()
    {
        return $this->message;
    }


}