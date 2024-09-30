<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

$host = $_ENV['DB_HOST'];
$dbname = $_ENV['DB_NAME'];
$user = $_ENV['DB_USER'];
$pass = $_ENV['DB_PASSWORD'];

try {
    $db = new PDO("mysql:host=$host;dbname=$dbname", $user, $pass);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    echo json_encode(['error' => 'Database connection failed: ' . $e->getMessage()]);
    exit;
}

$action = $_GET['action'] ?? '';
$number = $_GET['number'] ?? '';

function getProducts($db) {
    try {
        $stmt = $db->query("SELECT * FROM products");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch(PDOException $e) {
        return ['error' => 'Failed to get products: ' . $e->getMessage()];
    }
}

function setProducts($db, $products) {
    try {
        $db->beginTransaction();
        $stmt = $db->prepare("INSERT INTO products (number, name, bottlesize, price, priceGBP, timestamp, orderamount) 
                              VALUES (:number, :name, :bottlesize, :price, :priceGBP, :timestamp, :orderamount)
                              ON DUPLICATE KEY UPDATE 
                              name = VALUES(name), bottlesize = VALUES(bottlesize), 
                              price = VALUES(price), priceGBP = VALUES(priceGBP), 
                              timestamp = VALUES(timestamp)");
        
        foreach ($products as $product) {
            $stmt->execute($product);
        }
        $db->commit();
        return ['status' => 'success', 'message' => 'Products updated successfully'];
    } catch(PDOException $e) {
        $db->rollBack();
        return ['error' => 'Failed to set products: ' . $e->getMessage()];
    }
}

switch ($action) {
    case 'getProducts':
        echo json_encode(getProducts($db));
        break;
    case 'setProducts':
        $products = json_decode(file_get_contents('php://input'), true);
        echo json_encode(setProducts($db, $products));
        break;
    case 'add':
        try {
            $stmt = $db->prepare("UPDATE products SET orderamount = orderamount + 1 WHERE number = ?");
            $stmt->execute([$number]);
            echo json_encode(['status' => 'success', 'message' => 'Order amount increased']);
        } catch(PDOException $e) {
            echo json_encode(['error' => 'Update failed: ' . $e->getMessage()]);
        }
        break;
    case 'clear':
        try {
            $stmt = $db->prepare("UPDATE products SET orderamount = 0 WHERE number = ?");
            $stmt->execute([$number]);
            echo json_encode(['status' => 'success', 'message' => 'Order amount cleared']);
        } catch(PDOException $e) {
            echo json_encode(['error' => 'Clear failed: ' . $e->getMessage()]);
        }
        break;
    default:
        echo json_encode(['error' => 'Invalid action']);
}
