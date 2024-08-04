<?php

namespace SQLPG\Databases;

use SQLPG\Databases\PDOWrapper;

abstract class DBConnection {
    private $dbconn;

    function __construct(public readonly DBHost $dbhost) {
        $this->dbconn = $this->createUserAndConnect();
    }

    public abstract function createUserAndConnect(): PDOWrapper;

    public abstract function cleanup();

    public function query(string $sql, ?array $bindParams = null) {
        return $this->dbconn->query($sql, $bindParams);
    }

    public function closeConnection() {
        $this->dbconn = null;
    }
}