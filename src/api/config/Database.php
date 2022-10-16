<?php

namespace PabloMessenger\api\config;

use PDO;

class Database
{
    private $host = "localhost";
    private $db_name = "messenger_db";
    private $username = "root";
    private $password = "";
    public $conn;

    public function getConnection()
    {

        $this->conn = null;

        try {
            $this->conn = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->db_name, $this->username, $this->password);
        } catch (PDOException $exception) {
            echo "Connection error: " . $exception->getMessage();
        }

        return $this->conn;
    }
}
