<?php

require_once __DIR__ . '/connection.php';

class Anunciante {

    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }
}
