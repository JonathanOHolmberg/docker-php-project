<?php

require_once __DIR__ . '/vendor/autoload.php';

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Shuchkin\SimpleXLSX;

function makeRequest($url, $method = 'GET', $data = null) {
    $client = new Client();
    try {
        $options = [
            'http_errors' => false,
            'timeout' => 30,
        ];
        if ($method === 'POST' && $data !== null) {
            $options['json'] = $data;
        }
        $response = $client->request($method, $url, $options);
        $statusCode = $response->getStatusCode();
        $body = $response->getBody()->getContents();
        
        return [
            'status_code' => $statusCode,
            'body' => $body,
        ];
    } catch (GuzzleException $e) {
        return [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString(),
        ];
    }
}

function populateDatabase() {
    $apiResponse = makeRequest('http://api/index.php?action=getProductData');
    
    if (isset($apiResponse['error'])) {
        return [
            'error' => 'API request failed',
            'details' => $apiResponse,
        ];
    }
    
    $data = json_decode($apiResponse['body'], true);
    
    if (json_last_error() !== JSON_ERROR_NONE) {
        return [
            'error' => 'Failed to decode API response',
            'details' => [
                'json_error' => json_last_error_msg(),
                'response_body' => $apiResponse['body'],
            ],
        ];
    }
    
    if (!isset($data['alko_data']) || !isset($data['exchange_rate'])) {
        return [
            'error' => 'Invalid response from API',
            'details' => $data,
        ];
    }
    
    $alkoData = $data['alko_data'];
    $exchangeRate = $data['exchange_rate']['rate'];
    
    try {
        $processedData = processAlkoData($alkoData, $exchangeRate);
        $serverResponse = makeRequest('http://server/server.php?action=setProducts', 'POST', $processedData);
        
        if (isset($serverResponse['error'])) {
            return [
                'error' => 'Server request failed',
                'details' => $serverResponse,
            ];
        }
        
        makeRequest('http://server/server.php?action=updateCacheStatus');
        
        return json_decode($serverResponse['body'], true);
    } catch (Exception $e) {
        return [
            'error' => 'Failed to process Alko data',
            'details' => $e->getMessage(),
        ];
    }
}

function processAlkoData($alkoData, $exchangeRate) {
    $content = base64_decode($alkoData['content']);
    $filePath = sys_get_temp_dir() . '/alko_products_' . uniqid() . '.xlsx';
    file_put_contents($filePath, $content);

    if (!file_exists($filePath)) {
        throw new Exception("Failed to create XLSX file");
    }

    $xlsx = SimpleXLSX::parse($filePath);
    if (!$xlsx) {
        throw new Exception(SimpleXLSX::parseError());
    }

    $rows = $xlsx->rows();
    $headers = $rows[3];
    $products = [];

    for ($i = 4; $i < count($rows); $i++) { // Start from the 5th row (index 4)
        $row = $rows[$i];
        $numero = $row[array_search('Numero', $headers)] ?? '';
        if (!preg_match('/^\d{6}$/', $numero)) {
            continue;
        }

        $product = [
            'number' => $numero,
            'name' => $row[array_search('Nimi', $headers)] ?? '',
            'bottlesize' => $row[array_search('Pullokoko', $headers)] ?? '',
            'price' => floatval($row[array_search('Hinta', $headers)] ?? 0),
            'priceGBP' => round(floatval($row[array_search('Hinta', $headers)] ?? 0) * $exchangeRate, 2),
            'timestamp' => $alkoData['timestamp']
        ];
        $products[] = $product;
    }

    unlink($filePath);

    return $products;
}