<?php

namespace Tests;

use PHPUnit\Framework\TestCase;
require_once __DIR__ . '/../functions.php';

class FunctionsTest extends TestCase
{
    public function testProcessAlkoData()
    {
        // Load the dummy XLSX file
        $dummyFilePath = __DIR__ . '/dummydata/DummyAlkoFile.xlsx';
        $dummyContent = file_get_contents($dummyFilePath);
        
        // Test data
        $alkoData = [
            'content' => base64_encode($dummyContent),
            'timestamp' => '2023-05-20 12:00:00'
        ];
        $exchangeRate = 1.15;
        $result = processAlkoData($alkoData, $exchangeRate);
        $this->assertIsArray($result);

        foreach ($result as $product) {
            $this->assertArrayHasKey('number', $product);
            $this->assertArrayHasKey('name', $product);
            $this->assertArrayHasKey('bottlesize', $product);
            $this->assertArrayHasKey('price', $product);
            $this->assertArrayHasKey('priceGBP', $product);
            $this->assertArrayHasKey('timestamp', $product);

            $this->assertMatchesRegularExpression('/^\d{6}$/', $product['number']);
            $this->assertIsString($product['name']);
            $this->assertIsString($product['bottlesize']);
            $this->assertIsFloat($product['price']);
            $this->assertIsFloat($product['priceGBP']);
            $this->assertEquals('2023-05-20 12:00:00', $product['timestamp']);

            $this->assertEquals(round($product['price'] * $exchangeRate, 2), $product['priceGBP']);
        }
    }
}