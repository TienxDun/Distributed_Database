<?php
/**
 * Common utilities and database functions
 */

/**
 * Get database connection
 */
function getDBConnection()
{
    $dbHost = getenv('DB_HOST');
    $dbPort = getenv('DB_PORT');
    $dbName = getenv('DB_NAME');
    $dbUser = getenv('DB_USER');
    $dbPass = getenv('DB_PASS');

    $dsn = "pgsql:host={$dbHost};port={$dbPort};dbname={$dbName}";

    try {
        $pdo = new PDO($dsn, $dbUser, $dbPass, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);
        return $pdo;
    } catch (Exception $e) {
        error_log("Database connection failed: " . $e->getMessage());
        http_response_code(500);
        echo json_encode([
            'error' => 'Database connection failed',
            'detail' => $e->getMessage(),
        ]);
        exit;
    }
}

/**
 * Send JSON response
 */
function sendResponse($data, $status = 200)
{
    http_response_code($status);
    header('Content-Type: application/json; charset=utf-8');
    try {
        echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    } catch (Exception $e) {
        echo json_encode(['error' => 'JSON encoding failed: ' . $e->getMessage()]);
    }
    exit;
}

/**
 * Get JSON input from request body
 */
function getJsonInput()
{
    $input = file_get_contents('php://input');
    $data = json_decode($input, true);

    if (json_last_error() !== JSON_ERROR_NONE) {
        sendResponse(['error' => 'Invalid JSON input'], 400);
    }

    return $data;
}

/**
 * Validate required fields in data array
 */
function validateRequiredFields($data, $requiredFields)
{
    $missing = [];
    foreach ($requiredFields as $field) {
        if (!isset($data[$field]) || $data[$field] === '') {
            $missing[] = $field;
        }
    }

    if (!empty($missing)) {
        sendResponse(['error' => 'Missing required fields: ' . implode(', ', $missing)], 400);
    }
}

/**
 * Determine which site a record belongs to based on ID
 */
function determineSite($id)
{
    if (empty($id)) {
        return 'Unknown';
    }

    $firstChar = strtoupper(substr($id, 0, 1));

    if ($firstChar < 'M') {
        return 'Site_A';
    } elseif ($firstChar >= 'M' && $firstChar < 'S') {
        return 'Site_B';
    } else {
        return 'Site_C';
    }
}

/**
 * Get site display name
 */
function getSiteDisplayName($site)
{
    $siteMap = [
        'Site_A' => 'Site A',
        'Site_B' => 'Site B',
        'Site_C' => 'Site C',
        'Global' => 'Global'
    ];

    return $siteMap[$site] ?? $site;
}

/**
 * Sanitize string input
 */
function sanitizeString($input)
{
    return trim(htmlspecialchars($input, ENT_QUOTES, 'UTF-8'));
}

/**
 * Validate ID format (alphanumeric, reasonable length)
 */
function validateId($id)
{
    if (!is_string($id) || strlen($id) > 20 || !preg_match('/^[A-Za-z0-9]+$/', $id)) {
        sendResponse(['error' => 'Invalid ID format'], 400);
    }
}