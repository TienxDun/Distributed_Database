<?php

class MongoHelper {
    private static $client = null;
    
    public static function getClient() {
        if (self::$client === null) {
            try {
                $mongoHost = getenv('MONGO_HOST') ?: 'mongodb';
                $mongoPort = getenv('MONGO_PORT') ?: '27017';
                $mongoUser = getenv('MONGO_USER') ?: 'admin';
                $mongoPass = getenv('MONGO_PASSWORD') ?: 'Your@STROng!Mongo#Pass';
                
                // URL encode the password to handle special characters
                $mongoPassEncoded = rawurlencode($mongoPass);
                
                $uri = "mongodb://{$mongoUser}:{$mongoPassEncoded}@{$mongoHost}:{$mongoPort}/?authMechanism=SCRAM-SHA-1&authSource=admin";
                self::$client = new MongoDB\Driver\Manager($uri);
            } catch (Exception $e) {
                error_log("MongoDB connection failed: " . $e->getMessage());
                return null;
            }
        }
        return self::$client;
    }
    
    // Insert audit log
    public static function logAudit($table, $operation, $data, $oldData = null, $site = 'Global') {
        try {
            $manager = self::getClient();
            if (!$manager) return false;
            
            $document = [
                'table' => $table,
                'operation' => $operation,
                'data' => $data,
                'timestamp' => new MongoDB\BSON\UTCDateTime(),
                'site' => $site,
                'ip_address' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
                'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'unknown'
            ];
            
            if ($oldData !== null) {
                $document['old_data'] = $oldData;
            }
            
            $bulk = new MongoDB\Driver\BulkWrite;
            $bulk->insert($document);
            $manager->executeBulkWrite('huflit_logs.audit_logs', $bulk);
            
            return true;
        } catch (Exception $e) {
            error_log("Audit log failed: " . $e->getMessage());
            return false;
        }
    }
    
    // Log query history
    public static function logQuery($endpoint, $method, $params = [], $body = [], $executionTime = 0, $resultCount = 0, $statusCode = 200) {
        try {
            $manager = self::getClient();
            if (!$manager) return false;
            
            $document = [
                'endpoint' => $endpoint,
                'method' => $method,
                'params' => $params,
                'body' => $body,
                'execution_time_ms' => (int)$executionTime,
                'result_count' => (int)$resultCount,
                'status_code' => (int)$statusCode,
                'timestamp' => new MongoDB\BSON\UTCDateTime(),
                'ip_address' => $_SERVER['REMOTE_ADDR'] ?? 'unknown'
            ];
            
            $bulk = new MongoDB\Driver\BulkWrite;
            $bulk->insert($document);
            $manager->executeBulkWrite('huflit_logs.query_history', $bulk);
            
            return true;
        } catch (Exception $e) {
            error_log("Query log failed: " . $e->getMessage());
            return false;
        }
    }
    
    // Query audit logs with filters
    public static function getAuditLogs($filter = [], $limit = 50, $skip = 0) {
        try {
            $manager = self::getClient();
            if (!$manager) return [];
            
            $options = [
                'sort' => ['timestamp' => -1],
                'limit' => $limit,
                'skip' => $skip
            ];
            
            $query = new MongoDB\Driver\Query($filter, $options);
            $cursor = $manager->executeQuery('huflit_logs.audit_logs', $query);
            
            return $cursor->toArray();
        } catch (Exception $e) {
            error_log("Get audit logs failed: " . $e->getMessage());
            return [];
        }
    }
    
    // Query history with filters
    public static function getQueryHistory($filter = [], $limit = 50, $skip = 0) {
        try {
            $manager = self::getClient();
            if (!$manager) return [];
            
            $options = [
                'sort' => ['timestamp' => -1],
                'limit' => $limit,
                'skip' => $skip
            ];
            
            $query = new MongoDB\Driver\Query($filter, $options);
            $cursor = $manager->executeQuery('huflit_logs.query_history', $query);
            
            return $cursor->toArray();
        } catch (Exception $e) {
            error_log("Get query history failed: " . $e->getMessage());
            return [];
        }
    }
    
    // Get statistics using aggregation
    public static function getStatistics($collection, $pipeline) {
        try {
            $manager = self::getClient();
            if (!$manager) return [];
            
            $command = new MongoDB\Driver\Command([
                'aggregate' => $collection,
                'pipeline' => $pipeline,
                'cursor' => new stdClass,
            ]);
            
            $cursor = $manager->executeCommand('huflit_logs', $command);
            return $cursor->toArray();
        } catch (Exception $e) {
            error_log("Get statistics failed: " . $e->getMessage());
            return [];
        }
    }
}
?>
