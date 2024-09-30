<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

$products = [
    [
        'number' => 1,
        'name' => 'Sample Wine 1',
        'bottlesize' => '0.75L',
        'price' => 10.99,
        'priceGBP' => 9.50,
        'timestamp' => date('Y-m-d H:i:s'),
        'orderamount' => 0
    ],
    [
        'number' => 2,
        'name' => 'Sample Beer 1',
        'bottlesize' => '0.33L',
        'price' => 3.99,
        'priceGBP' => 3.45,
        'timestamp' => date('Y-m-d H:i:s'),
        'orderamount' => 0
    ],
    [
        'number' => 3,
        'name' => 'Sample Spirits 1',
        'bottlesize' => '0.5L',
        'price' => 25.99,
        'priceGBP' => 22.50,
        'timestamp' => date('Y-m-d H:i:s'),
        'orderamount' => 0
    ]
];

$action = $_GET['action'] ?? '';

switch ($action) {
    case 'list':
        echo json_encode($products);
        break;
    case 'update':
        foreach ($products as &$product) {
            $product['timestamp'] = date('Y-m-d H:i:s');
        }
        echo json_encode(['status' => 'success', 'message' => 'Products updated']);
        break;
    default:
        echo json_encode(['error' => 'Invalid action']);
}