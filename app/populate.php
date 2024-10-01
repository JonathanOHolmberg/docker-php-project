<?php
require_once __DIR__ . '/functions.php';

$result = populateDatabase();
echo json_encode($result, JSON_PRETTY_PRINT);