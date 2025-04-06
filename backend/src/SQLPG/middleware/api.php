<?php

use SQLPG\Environment\Environment;

require 'vendor/autoload.php';

header('Access-Control-Allow-Origin: '.Environment::fetchFrontendOrigin());
header('Access-Control-Allow-Headers: Content-Type');

if($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit();
}

header('Content-Type: application/json');
