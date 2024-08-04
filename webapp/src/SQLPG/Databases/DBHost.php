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
                'pgsql',
                PostgresConnection::class,
                false
            ),
            'mysql8.4',
            'mysql8.0' => new DBHost(
                $host,
                3306,
                'mysql',
                MySQLConnection::class,
                false
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
        public readonly string $PDOProto,
        public readonly string $connectionClass,
        public readonly bool $allowsMultiRowset
    ) {}
}

