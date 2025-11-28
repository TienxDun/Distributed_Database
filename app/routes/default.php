<?php
require_once __DIR__ . '/../common.php';

function handleDefault($method, $query) {
    sendResponse(['error' => 'Endpoint not found'], 404);
}
?>