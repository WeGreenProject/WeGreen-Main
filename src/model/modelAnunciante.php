<?php

require_once 'connection.php';

class Anunciante {

    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }
}