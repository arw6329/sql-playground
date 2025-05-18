<?php

namespace SQLPG\Databases;

class DBHost {
    static function fetchHost(string $host): ?DBHost {
        return match($host) {
            'postgres16',
            'postgres15',
            'postgres14',
            'postgres13',
            'postgres12' => new DBHost(
                $host,
                8000,
                PostgresConnection::class
            ),
            'mysql8.4',
            'mysql8.0' => new DBHost(
                $host,
                3306,
                MySQLConnection::class
            ),
            'maria11.8.1' => new DBHost(
                $host,
                3306,
                MariaDBConnection::class
            ),
            'oracle23ailite',
            'oracle23ai',
            'oracle21c' => new DBHost(
                $host,
                1521,
                OracleConnection::class
            ),
            default => null
        };
    }
    
    function connect(): DBConnection {
        return new $this->connectionClass($this);
    }

    private function __construct(
        public readonly string $name,
        public readonly int $port,
        public readonly string $connectionClass,
    ) {}
}

