<?php

class MongoHelper
{
    private static $client = null;

    public static function getClient()
    {
        if (self::$client === null) {
            try {
                // Check if MongoDB extension is loaded
                if (!extension_loaded('mongodb')) {
                    error_log("MongoDB extension not loaded. Please rebuild Docker containers.");
                    return null;
                }

                $mongoUri = getenv('MONGO_URI');

                if ($mongoUri) {
                    $uri = $mongoUri;
                } else {
                    $mongoHost = getenv('MONGO_HOST') ?: 'mongodb';
                    $mongoPort = getenv('MONGO_PORT') ?: '27017';
                    $mongoUser = getenv('MONGO_USER') ?: 'admin';
                    $mongoPass = getenv('MONGO_PASSWORD') ?: 'Your@STROng!Mongo#Pass';

                    // URL encode the password to handle special characters
                    $mongoPassEncoded = rawurlencode($mongoPass);

                    $uri = "mongodb://{$mongoUser}:{$mongoPassEncoded}@{$mongoHost}:{$mongoPort}/?authMechanism=SCRAM-SHA-1&authSource=admin";
                }

                self::$client = new MongoDB\Driver\Manager($uri);
            } catch (Exception $e) {
                error_log("MongoDB connection failed: " . $e->getMessage());
                return null;
            }
        }
        return self::$client;
    }

    // Insert audit log
    public static function logAudit($table, $operation, $data, $oldData = null, $site = 'Global')
    {
        try {
            $manager = self::getClient();
            if (!$manager)
                return false;

            $document = [
                'table' => $table,
                'operation' => $operation,
                'timestamp' => new MongoDB\BSON\UTCDateTime(),
                'site' => $site,
                'ip_address' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
                'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'unknown'
            ];

            if ($data !== null) {
                $document['data'] = $data;
            }

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
    public static function logQuery($endpoint, $method, $params = [], $body = [], $executionTime = 0, $resultCount = 0, $statusCode = 200)
    {
        try {
            $manager = self::getClient();
            if (!$manager)
                return false;

            $document = [
                'endpoint' => $endpoint,
                'method' => $method,
                'params' => $params,
                'body' => $body,
                'execution_time_ms' => (int) $executionTime,
                'result_count' => (int) $resultCount,
                'status_code' => (int) $statusCode,
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
    public static function getAuditLogs($filter = [], $limit = 50, $skip = 0)
    {
        try {
            $manager = self::getClient();
            if (!$manager)
                return [];

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

    // Count audit logs with filters
    public static function countAuditLogs($filter = [])
    {
        try {
            $manager = self::getClient();
            if (!$manager)
                return 0;

            $query = empty($filter) ? new stdClass() : json_decode(json_encode($filter), false);

            $commandArray = [
                'count' => 'audit_logs'
            ];
            if (!empty($filter)) {
                $commandArray['query'] = $query;
            }

            $command = new MongoDB\Driver\Command($commandArray);

            $cursor = $manager->executeCommand('huflit_logs', $command);
            $result = $cursor->toArray();

            return $result[0]->n;
        } catch (Exception $e) {
            error_log("Count audit logs failed: " . $e->getMessage());
            return 0;
        }
    }

    // Query history with filters
    public static function getQueryHistory($filter = [], $limit = 50, $skip = 0)
    {
        try {
            $manager = self::getClient();
            if (!$manager)
                return [];

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
    public static function getStatistics($collection, $pipeline)
    {
        try {
            $manager = self::getClient();
            if (!$manager)
                return [];

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