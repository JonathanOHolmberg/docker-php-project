<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

$action = $_GET['action'] ?? '';

function makeRequest($url, $method = 'GET', $data = null) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    if ($method === 'POST') {
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    }
    $output = curl_exec($ch);
    curl_close($ch);
    return $output;
}

function populateDatabase() {
    $apiResponse = makeRequest('http://api/index.php?action=list');
    $products = json_decode($apiResponse, true);
    
    if (!is_array($products)) {
        return ['error' => 'Invalid response from API'];
    }
    
    $serverResponse = makeRequest('http://server/server.php?action=setProducts', 'POST', $products);
    return json_decode($serverResponse, true);
}

switch ($action) {
    case 'list':
        $serverResponse = makeRequest('http://server/server.php?action=getProducts');
        $products = json_decode($serverResponse, true);
        if (is_array($products)) {
            echo json_encode($products);
        } else {
            echo json_encode(['error' => 'Invalid response from server']);
        }
        break;
    case 'add':
        $number = $_GET['number'] ?? '';
        $serverResponse = makeRequest("http://server/server.php?action=add&number=$number");
        echo $serverResponse;
        break;
    case 'clear':
        $number = $_GET['number'] ?? '';
        $serverResponse = makeRequest("http://server/server.php?action=clear&number=$number");
        echo $serverResponse;
        break;
    case 'populate':
        $result = populateDatabase();
        echo json_encode($result);
        break;
    default:
        echo json_encode(['error' => 'Invalid action']);
}
