<?php

namespace SQLPG\Databases;

use SQLPG\Utils\Random;

class MySQLConnection extends DBConnection {
    protected $user;

    public function getPDOURI(string $database): string {
        return "mysql:host={$this->dbhost->name};port={$this->dbhost->port};dbname=$database";
    }

    public function createUserAndConnect(): PDOWrapper {
        $dbconn = new PDOWrapper($this->getPDOURI('mysql'), 'root', file_get_contents("/run/secrets/{$this->dbhost->name}pwd"), false);

        $this->user = 'user'.Random::secureRandomBytesHex(12);
        $password = Random::secureRandomBytesHex(40);
        $dbconn->query("CREATE USER {$this->user}@'%' IDENTIFIED WITH caching_sha2_password BY '$password'");
        $dbconn->query("CREATE DATABASE {$this->user}");
        $dbconn->query("GRANT ALL PRIVILEGES ON {$this->user}.* TO {$this->user}@'%'");
        $dbconn = null;

        return new PDOWrapper($this->getPDOURI($this->user), $this->user, $password, false);
    }

    public function loadFileIntoTable(string $filePath, string $tableName) {
        "LOAD DATA LOCAL INFILE {$this->quote($filePath)} INTO TABLE";
    }    

    public function cleanup() {
        $this->closeConnection();

        $dbconn = new PDOWrapper($this->getPDOURI('mysql'), 'root', file_get_contents("/run/secrets/{$this->dbhost->name}pwd"), false);

        $dbconn->query("DROP DATABASE {$this->user}");
        $dbconn->query("DROP USER {$this->user}@'%'");
    }
}
