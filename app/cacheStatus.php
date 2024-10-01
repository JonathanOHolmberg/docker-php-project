<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

require_once __DIR__ . '/functions.php';

$response = makeRequest('http://server/server.php?action=getCacheStatus');
echo json_encode($response);