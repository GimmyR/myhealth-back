<?php

namespace App\Repository;

use Exception;

class RepositoryException extends Exception {

    protected $message;

    public function __construct(string $message) {

        $this->message = $message;
        
    }

}

?>