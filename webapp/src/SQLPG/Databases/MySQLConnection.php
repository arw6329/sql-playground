<?php

namespace SQLPG\Databases;

use SQLPG\Utils\Random;

class MySQLConnection extends DBConnection {
    private $user;

    public function createUserAndConnect(): PDOWrapper {
        $dbconn = new PDOWrapper($this->dbhost, 'mysql', 'root', file_get_contents("/run/secrets/{$this->dbhost->name}pwd"));

        $this->user = 'user'.Random::secureRandomBytesHex(12);
        $password = Random::secureRandomBytesHex(40);
        $dbconn->query("CREATE USER {$this->user}@'%' IDENTIFIED WITH caching_sha2_password BY '$password'");
        $dbconn->query("CREATE DATABASE {$this->user}");
        $dbconn->query("GRANT ALL PRIVILEGES ON {$this->user}.* TO {$this->user}@'%'");
        $dbconn = null;

        return new PDOWrapper($this->dbhost, $this->user, $this->user, $password);
    }

    public function cleanup() {
        $this->closeConnection();

        $dbconn = new PDOWrapper($this->dbhost, 'mysql', 'root', file_get_contents("/run/secrets/{$this->dbhost->name}pwd"));

        $dbconn->query("DROP DATABASE {$this->user}");
        $dbconn->query("DROP USER {$this->user}@'%'");
    }
}
