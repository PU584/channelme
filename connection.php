<?php

class Connection {
    // Database properties
    private $host;
    private $username;
    private $password;
    private $database;
    private $connection;

    
    public function __construct($host, $username, $password, $database) {
        $this->host = $host;
        $this->username = $username;
        $this->password = $password;
        $this->database = $database;

        $this->connect();   
    }

    private function connect() {
        $this->connection = new mysqli($this->host, $this->username, $this->password, $this->database,3325);

        if ($this->connection->connect_error) {
            die("Connection failed: " . $this->connection->connect_error);
        } else {
            
        }
    }


    public function getConnection(){

        return $this->connection;


    }

    //execute sql query

    public function runquery($query){

        $stmt = $this->connection->prepare($query);
        if (!$stmt) {
            die("SQL Error: " . $this->connection->error);
        }
        return $stmt;
        

    }

}
?>
