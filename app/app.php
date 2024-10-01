<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

require_once __DIR__ . '/functions.php';

$action = $_GET['action'] ?? '';

switch ($action) {
    case 'list':
        $response = makeRequest('http://server/server.php?action=getProducts');
        echo json_encode($response);
        break;
    case 'add':
    case 'clear':
        $number = $_GET['number'] ?? '';
        $response = makeRequest("http://server/server.php?action=$action&number=$number");
        echo json_encode($response);
        break;
    default:
        echo json_encode(['error' => 'Invalid action']);
}