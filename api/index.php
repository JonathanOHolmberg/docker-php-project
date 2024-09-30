<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

require_once __DIR__ . '/vendor/autoload.php';

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

$client = new Client();

function fetchAlkoProducts($client) {
    $alkoUrl = $_ENV['ALKO_URL'] ?? null;
    if (!$alkoUrl) {
        throw new Exception("ALKO_URL environment variable is not set");
    }
    try {
        $response = $client->request('GET', $alkoUrl);
        if ($response->getStatusCode() !== 200) {
            throw new Exception("Failed to fetch Alko products: HTTP status code " . $response->getStatusCode());
        }
        $content = $response->getBody()->getContents();
        return [
            'content' => base64_encode($content),
            'timestamp' => date('Y-m-d H:i:s'),
            'size' => strlen($content)
        ];
    } catch (GuzzleException $e) {
        throw new Exception("Failed to fetch Alko products: " . $e->getMessage());
    }
}

function fetchExchangeRate($client) {
    $apiKey = $_ENV['CURRENCY_API_KEY'];
    $apiUrl = "http://apilayer.net/api/live?access_key={$apiKey}&currencies=GBP&source=EUR&format=1";
    try {
        $response = $client->request('GET', $apiUrl);
        if ($response->getStatusCode() !== 200) {
            throw new Exception("Failed to fetch exchange rate: HTTP status code " . $response->getStatusCode());
        }
        $data = json_decode($response->getBody(), true);
        if (!isset($data['success']) || $data['success'] !== true) {
            throw new Exception("API error: " . ($data['error']['info'] ?? 'Unknown error'));
        }
        $rate = $data['quotes']['EURGBP'] ?? null;
        if ($rate === null) {
            throw new Exception("Exchange rate not found in API response");
        }
        return [
            'rate' => $rate,
            'timestamp' => date('Y-m-d H:i:s')
        ];
    } catch (GuzzleException $e) {
        throw new Exception("Failed to fetch exchange rate: " . $e->getMessage());
    }
}

$action = $_GET['action'] ?? '';

try {
    switch ($action) {
        case 'getProductData':
            $alkoData = fetchAlkoProducts($client);
            $exchangeRate = fetchExchangeRate($client);
            echo json_encode([
                'alko_data' => $alkoData,
                'exchange_rate' => $exchangeRate
            ]);
            break;
        default:
            throw new Exception("Invalid action");
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}