<?php

namespace App\Entity;

use Exception;

class EntityException extends Exception {

    public $message;

    public function __construct(string $message) {
        $this->message = $message;
    }

}

?>