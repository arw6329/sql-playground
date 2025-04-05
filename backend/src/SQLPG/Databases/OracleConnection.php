<?php

namespace SQLPG\Databases;

use Exception;
use SQLPG\Utils\Random;

class OracleConnection extends DBConnection {
    private $user;

    public function getPDOURI(string $database): string {
        return "oci:dbname=//{$this->dbhost->name}:{$this->dbhost->port}/$database";
    }

    public function createUserAndConnect(): PDOWrapper {
        $this->user = 'user'.Random::secureRandomBytesHex(12);
        $password = Random::secureRandomBytesHex(40);

        $this->execPrivileged([
            "CREATE PLUGGABLE DATABASE {$this->user}
                ADMIN USER {$this->user} IDENTIFIED BY \"$password\"
                ROLES = (CONNECT, DBA)
                PARALLEL
                DEFAULT TABLESPACE {$this->user}
                NOLOGGING
                STORAGE (MAXSIZE 10G)",
            "ALTER PLUGGABLE DATABASE {$this->user} OPEN",
            "ALTER SESSION SET CONTAINER = {$this->user}",
            "ALTER USER {$this->user} QUOTA 10G ON {$this->user}"
        ]);

        return new PDOWrapper($this->getPDOURI($this->user), $this->user, $password, false);
    }

    public function cleanup() {
        $this->closeConnection();

        $this->execPrivileged([
            "ALTER PLUGGABLE DATABASE {$this->user} CLOSE",
            "DROP PLUGGABLE DATABASE {$this->user} INCLUDING DATAFILES"
        ]);
    }

    private function execPrivileged(array $queries) {
        $dbconn = \oci_connect(
            'sys',
            file_get_contents("/run/secrets/{$this->dbhost->name}pwd"),
            "//{$this->dbhost->name}:{$this->dbhost->port}/free",
            null,
            OCI_SYSDBA
        );

        if(!$dbconn) {
            throw new Exception('Failure to connect to Oracle database');
        }

        foreach($queries as $query) {
            $success = true;

            $stmt = \oci_parse($dbconn, $query);
            $success &= !!$stmt;
            $success &= \oci_execute($stmt);

            if(!$success) {
                throw new Exception("Error during privileged database operation ($query)");
            }
        }

        \oci_close($dbconn);

        if(!$success) {
            throw new Exception('Error occurred during database setup');
        }
    }
}
