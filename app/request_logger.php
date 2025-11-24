<?php
require_once 'mongo_helper.php';

class RequestLogger {
    private static $startTime;
    private static $endpoint;
    private static $method;
    private static $params;
    private static $body;
    
    public static function start() {
        self::$startTime = microtime(true);
        self::$endpoint = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        self::$method = $_SERVER['REQUEST_METHOD'];
        self::$params = $_GET;
        self::$body = json_decode(file_get_contents('php://input'), true) ?? [];
    }
    
    public static function end($resultCount = 0, $statusCode = 200) {
        if (self::$startTime === null) return;
        
        $executionTime = (microtime(true) - self::$startTime) * 1000; // Convert to ms
        
        MongoHelper::logQuery(
            self::$endpoint,
            self::$method,
            self::$params,
            self::$body,
            $executionTime,
            $resultCount,
            $statusCode
        );
    }
}
?>
