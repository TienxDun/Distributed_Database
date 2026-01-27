<?php
require_once __DIR__ . '/../mongo_helper.php';

function handleLogs($method, $query) {
    if ($method !== 'GET') {
        sendResponse(['error' => 'Method not allowed'], 405);
        return;
    }
    
    try {
        // Build filter from query params
        $filter = [];
        
        if (isset($query['table'])) {
            $filter['table'] = $query['table'];
        }
        
        if (isset($query['operation'])) {
            $filter['operation'] = strtoupper($query['operation']);
        }
        
        if (isset($query['site'])) {
            $filter['site'] = $query['site'];
        }
        
        // Date range filter
        if (isset($query['from']) || isset($query['to'])) {
            $dateFilter = [];
            if (isset($query['from'])) {
                $dateFilter['$gte'] = new MongoDB\BSON\UTCDateTime(strtotime($query['from']) * 1000);
            }
            if (isset($query['to'])) {
                $dateFilter['$lte'] = new MongoDB\BSON\UTCDateTime(strtotime($query['to']) * 1000);
            }
            $filter['timestamp'] = $dateFilter;
        }
        
        $limit = isset($query['limit']) ? (int)$query['limit'] : 50;
        $page = isset($query['page']) ? (int)$query['page'] : 1;
        $skip = ($page - 1) * $limit;
        
        // Get total count for pagination
        $total = MongoHelper::countAuditLogs($filter);
        $totalPages = ceil($total / $limit);
        
        $logs = MongoHelper::getAuditLogs($filter, $limit, $skip);
        
        // Convert to array and format
        $result = [];
        foreach ($logs as $log) {
            $logArray = (array)$log;
            // Convert MongoDB BSON types to readable format
            if (isset($logArray['timestamp'])) {
                $logArray['timestamp'] = $logArray['timestamp']->toDateTime()->format('Y-m-d H:i:s');
            }
            if (isset($logArray['_id'])) {
                $logArray['_id'] = (string)$logArray['_id'];
            }
            $result[] = $logArray;
        }
        
        RequestLogger::end(count($result), 200);
        sendResponse([
            'success' => true,
            'data' => $result,
            'page' => $page,
            'limit' => $limit,
            'total' => $total,
            'totalPages' => $totalPages
        ]);
        
    } catch (Exception $e) {
        RequestLogger::end(0, 500);
        sendResponse(['error' => $e->getMessage()], 500);
    }
}
?>
