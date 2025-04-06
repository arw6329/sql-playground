<?php

use SQLPG\Databases\DBHost;
use SQLPG\Environment\Environment;

require 'vendor/autoload.php';

require 'SQLPG/middleware/api.php';

if($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode([
        'success' => false,
        'error' => 'Request method must be POST'
    ]);
    exit;
}

if($_SERVER['HTTP_CONTENT_TYPE'] !== 'application/json') {
    echo json_encode([
        'success' => false,
        'error' => 'Content-Type header must be application/json'
    ]);
    exit;
}

$request = json_decode(file_get_contents('php://input'), false);

if(!is_object($request)) {
    echo json_encode([
        'success' => false,
        'error' => 'Request body is invalid JSON'
    ]);
    exit;
}

if(!is_string($request->database)) {
    echo json_encode([
        'success' => false,
        'error' => 'Key "database" must be string'
    ]);
    exit;
}

if(!is_array($request->operations)) {
    echo json_encode([
        'success' => false,
        'error' => 'Key "operations" must be array'
    ]);
    exit;
}

foreach($request->operations as $operation) {
    switch($operation->type) {
        case 'query': {
            if(!is_string($operation->query)) {
                echo json_encode([
                    'success' => false,
                    'error' => "Query is not a string"
                ]);
                exit;
            }
            break;
        }
        default: {
            echo json_encode([
                'success' => false,
                'error' => "Operation type {$operation->type} is not recognized"
            ]);
            exit;
        }
    }
}

$host = DBHost::fetchHost($request->database);

if(!$host) {
    echo json_encode([
        'success' => false,
        'error' => "Database '{$request->database}' does not exist"
    ]);
    exit;
}

if(!in_array($request->database, Environment::fetchEnabledDBs())) {
    echo json_encode([
        'success' => false,
        'error' => "Database '{$request->database}' is not enabled"
    ]);
    exit;
}

try {
    $connection = $host->connect();

    $results = array_map(fn($operation) => [
        'results' => $operation->query
            ? $connection->query($operation->query)
            : []
    ], $request->operations);

    $connection->cleanup();

    echo json_encode([
        'success' => true,
        'results' => $results
    ]);
} catch(Throwable $e) {
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
