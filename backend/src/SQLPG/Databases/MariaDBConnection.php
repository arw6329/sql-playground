<?php

namespace SQLPG\Databases;

use SQLPG\Utils\Random;

class MariaDBConnection extends MySQLConnection {
    public function createUserAndConnect(): PDOWrapper {
        $dbconn = new PDOWrapper($this->getPDOURI('mysql'), 'root', file_get_contents("/run/secrets/{$this->dbhost->name}pwd"), false);

        $this->user = 'user'.Random::secureRandomBytesHex(12);
        $password = Random::secureRandomBytesHex(40);
        $dbconn->query("CREATE USER {$this->user}@'%' IDENTIFIED BY '$password'");
        $dbconn->query("CREATE DATABASE {$this->user}");
        $dbconn->query("GRANT ALL PRIVILEGES ON {$this->user}.* TO {$this->user}@'%'");
        $dbconn = null;

        return new PDOWrapper($this->getPDOURI($this->user), $this->user, $password, false);
    }
}
