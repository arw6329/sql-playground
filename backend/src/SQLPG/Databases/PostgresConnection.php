<?php

namespace SQLPG\Databases;

use SQLPG\Utils\Random;

class PostgresConnection extends DBConnection {
    private $user;

    public function getPDOURI(string $database): string {
        return "pgsql:host={$this->dbhost->name};port={$this->dbhost->port};dbname=$database";
    }

    public function createUserAndConnect(): PDOWrapper {
        $dbconn = new PDOWrapper($this->getPDOURI('postgres'), 'postgres', file_get_contents("/run/secrets/{$this->dbhost->name}pwd"), false);

        $this->user = 'user'.Random::secureRandomBytesHex(12);
        $password = Random::secureRandomBytesHex(40);
        $dbconn->query("REVOKE CREATE ON SCHEMA public FROM PUBLIC");
        $dbconn->query("CREATE USER {$this->user} WITH PASSWORD '$password'");
        $dbconn->query("CREATE DATABASE {$this->user}");
        $dbconn->query("ALTER DATABASE {$this->user} OWNER TO {$this->user}");
        $dbconn = null;

        $dbconn = new PDOWrapper($this->getPDOURI($this->user), $this->user, $password, false);
        return $dbconn;
    }

    public function loadFileIntoTable(string $filePath, string $tableName) {
        throw new \Exception('Not implemented');
    }

    public function cleanup() {
        $this->closeConnection();

        $dbconn = new PDOWrapper($this->getPDOURI('postgres'), 'postgres', file_get_contents("/run/secrets/{$this->dbhost->name}pwd"), false);

        $dbconn->query("DROP OWNED BY {$this->user} CASCADE");
        $dbconn->query("DROP DATABASE {$this->user}");
        $dbconn->query("DROP USER {$this->user}");
    }
}
