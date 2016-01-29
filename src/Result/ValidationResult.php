<?php
/**
 * Created by PhpStorm.
 * User: Thomas Whiston
 * Date: 26/01/2016
 * Time: 14:49
 */

namespace Drupal\twhiston\FluentValidator\Result;


class ValidationResult
{
    /** @var Status */
    private $status;

    /** @var  string */
    private $message;


    public function __construct($status, $message = null)
    {
        $this->status = $status;
        $this->message = $message;
    }

    /**
     * @return mixed
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @return mixed
     */
    public function getMessage()
    {
        return $this->message;
    }


}