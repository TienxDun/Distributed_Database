<?php
require_once '../common.php';

function handleDefault($method, $query) {
    sendResponse(['error' => 'Endpoint not found'], 404);
}
?>