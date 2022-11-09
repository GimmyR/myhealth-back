<?php

namespace App\Controller;

use Exception;

class ControllerException extends Exception {

    public $message;

    public function __construct(string $message) {

        $this->message = $message;
        
    }

}