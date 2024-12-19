<?php

use SQLPG\Databases\DBHost;

require 'vendor/autoload.php';

header('Content-Type: application/json');

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

if(!is_array($request->queries)) {
    echo json_encode([
        'success' => false,
        'error' => 'Key "queries" must be array'
    ]);
    exit;
}

foreach($request->queries as $query) {
    if(!is_string($query)) {
        echo json_encode([
            'success' => false,
            'error' => "Query is not a string"
        ]);
        exit;
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

try {
    $connection = $host->connect();

    $results = array_map(fn($query) => [
        'query' => $query,
        'results' => $connection->query($query)
    ], $request->queries);

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
