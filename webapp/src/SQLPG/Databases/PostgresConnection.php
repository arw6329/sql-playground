<?php

namespace SQLPG\Databases;

use SQLPG\Utils\Random;

class PostgresConnection extends DBConnection {
    private $user;

    public function createUserAndConnect(): PDOWrapper {
        $dbconn = new PDOWrapper($this->dbhost, 'postgres', 'postgres', file_get_contents("/run/secrets/{$this->dbhost->name}pwd"));

        $this->user = 'user'.Random::secureRandomBytesHex(12);
        $password = Random::secureRandomBytesHex(40);
        $dbconn->query("REVOKE CREATE ON SCHEMA public FROM PUBLIC");
        $dbconn->query("CREATE USER {$this->user} WITH PASSWORD '$password'");
        $dbconn->query("CREATE DATABASE {$this->user}");
        $dbconn->query("ALTER DATABASE {$this->user} OWNER TO {$this->user}");
        $dbconn = null;

        $dbconn = new PDOWrapper($this->dbhost, $this->user, $this->user, $password);
        return $dbconn;
    }

    public function cleanup() {
        $this->closeConnection();

        $dbconn = new PDOWrapper($this->dbhost, 'postgres', 'postgres', file_get_contents("/run/secrets/{$this->dbhost->name}pwd"));

        $dbconn->query("DROP OWNED BY {$this->user} CASCADE");
        $dbconn->query("DROP DATABASE {$this->user}");
        $dbconn->query("DROP USER {$this->user}");
    }
}
