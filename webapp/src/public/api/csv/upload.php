<?php

use SQLPG\Utils\Random;

require 'vendor/autoload.php';

if($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode([
        'success' => false,
        'error' => 'Request method must be POST'
    ]);
    exit;
}

if($_SERVER['HTTP_CONTENT_TYPE'] !== 'multipart/form-data') {
    echo json_encode([
        'success' => false,
        'error' => 'Content-Type header must be multipart/form-data'
    ]);
    exit;
}

$file = $_FILES[0];

if($file) {
    echo json_encode([
        'success' => false,
        'error' => 'No file provided'
    ]);
    exit;
}

if($file['error'] !== UPLOAD_ERR_OK) {
    echo json_encode([
        'success' => false,
        'error' => 'A server-side error occurred while uploading the file'
    ]);
    exit;
}

$fileid = Random::secureRandomBytesHex(32);

move_uploaded_file($file['tmp_name'], "/csv/$fileid");

echo json_encode([
    'success' => true
]);
