<?php

namespace SQLPG\Databases;

class PDOWrapper {
    private $pdo;
    private $allowsMultiRowset;

    function __construct(private string $uri, string $username, string $password, bool $allowsMultiRowset) {
        $this->pdo = new \PDO($uri, $username, $password);
        $this->pdo->setAttribute(\PDO::ATTR_EMULATE_PREPARES, 0);
        $this->pdo->setAttribute(\PDO::ATTR_AUTOCOMMIT, false);
        $this->allowsMultiRowset = $allowsMultiRowset;
    }

    function query(string $sql, ?array $bindParams = null, bool $throwOnError = true) {
        try {
            $stmt = null;

            if($bindParams) {
                $stmt = $this->pdo->prepare($sql);
                $success = $stmt->execute($bindParams);

                if(!$success) {
                    throw new \Error("PDO query failed with error");
                }
            } else {
                $stmt = $this->pdo->query($sql);
            }

            if(!$stmt) {
                throw new \Error("PDO query failed with error");
            }

            $error = $stmt->errorInfo();

            if($error[1] !== null) {
                return [[
                    'type' => 'error',
                    'error' => [
                        'SQLSTATE' => $error[0],
                        'driverError' => $error[1],
                        'driverErrorMsg' => $error[2]
                    ]
                ]];
            }

            $resultsets = [];

            do {
                if($stmt->columnCount() > 0) {
                    $columnNames = [];

                    for($i = 0; $i < $stmt->columnCount(); $i++) {
                        array_push($columnNames, $stmt->getColumnMeta($i)['name']);
                    }
    
                    array_push($resultsets, [
                        'type' => 'resultset',
                        'columns' => $columnNames,
                        'rows' => $stmt->fetchAll(\PDO::FETCH_NUM)
                    ]); 
                }
            } while($this->allowsMultiRowset && $stmt->nextRowset());

            if(count($resultsets) > 0) {
                return $resultsets;
            } else {
                return [[
                    'type' => 'message',
                    'message' => 'Query succeeded with no output.'
                ]];
            }
        } catch(\PDOException $e) {
            $error = $this->pdo->errorInfo();
            
            if($throwOnError) {
                throw new \Error('error:'.$error[0].$error[1].$error[2]);
            }

            return [[
                'type' => 'error',
                'error' => [
                    'SQLSTATE' => $error[0],
                    'driverError' => $error[1],
                    'driverErrorMsg' => $error[2]
                ]
            ]];
        }
    }
}

