<?php
require_once '../mongo_helper.php';

function handleStats($method, $query) {
    if ($method !== 'GET') {
        sendResponse(['error' => 'Method not allowed'], 405);
        return;
    }
    
    try {
        $type = $query['type'] ?? 'overview';
        
        switch ($type) {
            case 'overview':
                // Get overview statistics
                $result = [
                    'total_operations' => getOperationCount(),
                    'operations_by_type' => getOperationsByType(),
                    'operations_by_table' => getOperationsByTable(),
                    'operations_by_site' => getOperationsBySite(),
                    'recent_activity' => getRecentActivity(10)
                ];
                break;
                
            case 'query_stats':
                // Get query statistics
                $result = [
                    'total_queries' => getQueryCount(),
                    'queries_by_endpoint' => getQueriesByEndpoint(),
                    'queries_by_method' => getQueriesByMethod(),
                    'avg_execution_time' => getAvgExecutionTime(),
                    'slowest_queries' => getSlowestQueries(10)
                ];
                break;
                
            case 'performance':
                // Get performance metrics
                $result = [
                    'avg_response_time_by_endpoint' => getAvgResponseTimeByEndpoint(),
                    'error_rate' => getErrorRate(),
                    'peak_hours' => getPeakHours()
                ];
                break;
                
            default:
                sendResponse(['error' => 'Invalid stats type'], 400);
                return;
        }
        
        RequestLogger::end(1, 200);
        sendResponse(['success' => true, 'data' => $result]);
        
    } catch (Exception $e) {
        RequestLogger::end(0, 500);
        sendResponse(['error' => $e->getMessage()], 500);
    }
}

// Helper functions for statistics
function getOperationCount() {
    $pipeline = [
        ['$count' => 'total']
    ];
    $result = MongoHelper::getStatistics('audit_logs', $pipeline);
    return isset($result[0]) ? $result[0]->total : 0;
}

function getOperationsByType() {
    $pipeline = [
        ['$group' => ['_id' => '$operation', 'count' => ['$sum' => 1]]],
        ['$sort' => ['count' => -1]]
    ];
    return MongoHelper::getStatistics('audit_logs', $pipeline);
}

function getOperationsByTable() {
    $pipeline = [
        ['$group' => ['_id' => '$table', 'count' => ['$sum' => 1]]],
        ['$sort' => ['count' => -1]]
    ];
    return MongoHelper::getStatistics('audit_logs', $pipeline);
}

function getOperationsBySite() {
    $pipeline = [
        ['$group' => ['_id' => '$site', 'count' => ['$sum' => 1]]],
        ['$sort' => ['count' => -1]]
    ];
    return MongoHelper::getStatistics('audit_logs', $pipeline);
}

function getRecentActivity($limit) {
    $logs = MongoHelper::getAuditLogs([], $limit, 0);
    $result = [];
    foreach ($logs as $log) {
        $logArray = (array)$log;
        if (isset($logArray['timestamp'])) {
            $logArray['timestamp'] = $logArray['timestamp']->toDateTime()->format('Y-m-d H:i:s');
        }
        if (isset($logArray['_id'])) {
            $logArray['_id'] = (string)$logArray['_id'];
        }
        $result[] = $logArray;
    }
    return $result;
}

function getQueryCount() {
    $pipeline = [
        ['$count' => 'total']
    ];
    $result = MongoHelper::getStatistics('query_history', $pipeline);
    return isset($result[0]) ? $result[0]->total : 0;
}

function getQueriesByEndpoint() {
    $pipeline = [
        ['$group' => ['_id' => '$endpoint', 'count' => ['$sum' => 1]]],
        ['$sort' => ['count' => -1]]
    ];
    return MongoHelper::getStatistics('query_history', $pipeline);
}

function getQueriesByMethod() {
    $pipeline = [
        ['$group' => ['_id' => '$method', 'count' => ['$sum' => 1]]],
        ['$sort' => ['count' => -1]]
    ];
    return MongoHelper::getStatistics('query_history', $pipeline);
}

function getAvgExecutionTime() {
    $pipeline = [
        ['$group' => ['_id' => null, 'avg_time' => ['$avg' => '$execution_time_ms']]]
    ];
    $result = MongoHelper::getStatistics('query_history', $pipeline);
    return isset($result[0]) ? $result[0]->avg_time : 0;
}

function getSlowestQueries($limit) {
    $queries = MongoHelper::getQueryHistory([], $limit, 0);
    $result = [];
    foreach ($queries as $query) {
        $queryArray = (array)$query;
        if (isset($queryArray['timestamp'])) {
            $queryArray['timestamp'] = $queryArray['timestamp']->toDateTime()->format('Y-m-d H:i:s');
        }
        if (isset($queryArray['_id'])) {
            $queryArray['_id'] = (string)$queryArray['_id'];
        }
        $result[] = $queryArray;
    }
    return $result;
}

function getAvgResponseTimeByEndpoint() {
    $pipeline = [
        ['$group' => [
            '_id' => '$endpoint',
            'avg_time' => ['$avg' => '$execution_time_ms'],
            'count' => ['$sum' => 1]
        ]],
        ['$sort' => ['avg_time' => -1]]
    ];
    return MongoHelper::getStatistics('query_history', $pipeline);
}

function getErrorRate() {
    $pipeline = [
        ['$group' => [
            '_id' => ['$cond' => [['$gte' => ['$status_code', 400]], 'error', 'success']],
            'count' => ['$sum' => 1]
        ]]
    ];
    return MongoHelper::getStatistics('query_history', $pipeline);
}

function getPeakHours() {
    $pipeline = [
        ['$group' => [
            '_id' => ['$hour' => '$timestamp'],
            'count' => ['$sum' => 1]
        ]],
        ['$sort' => ['count' => -1]],
        ['$limit' => 5]
    ];
    return MongoHelper::getStatistics('query_history', $pipeline);
}
?>
