# 📋 MongoDB Integration Plan

## 🎯 Mục tiêu
Tích hợp MongoDB vào hệ thống CSDL phân tán HUFLIT để thêm 2 chức năng:
1. **Audit Logs / Change History** - Ghi lại mọi thao tác CRUD
2. **Query History & Statistics** - Theo dõi usage và performance

---

## 🏗️ PHASE 1: Infrastructure Setup

### 1.1. Thêm MongoDB vào Docker Compose
**File**: `docker-compose.yml`

**Thêm service MongoDB**:
```yaml
mongodb:
  image: mongo:latest
  container_name: mongodb
  networks:
    - huflit-network
  environment:
    - MONGO_INITDB_ROOT_USERNAME=admin
    - MONGO_INITDB_ROOT_PASSWORD=${MONGO_PASSWORD}
    - MONGO_INITDB_DATABASE=huflit_logs
  ports:
    - "27017:27017"
  volumes:
    - ./db/mongodb/init:/docker-entrypoint-initdb.d
    - mongodb_data:/data/db
```

**Thêm volume**:
```yaml
volumes:
  mongodb_data:
```

**Environment variables** (`.env` file):
```
MONGO_PASSWORD=Your@STROng!Mongo#Pass
```

### 1.2. Tạo MongoDB Initialization Script
**File**: `db/mongodb/init/init.js`

```javascript
// Switch to database
db = db.getSiblingDB('huflit_logs');

// Create collections with validation
db.createCollection('audit_logs', {
  validator: {
    $jsonSchema: {
      bsonType: "object",
      required: ["table", "operation", "timestamp", "site"],
      properties: {
        table: { bsonType: "string", description: "Table name (Khoa, SinhVien, etc.)" },
        operation: { enum: ["INSERT", "UPDATE", "DELETE"], description: "CRUD operation" },
        data: { bsonType: "object", description: "Data involved in operation" },
        old_data: { bsonType: "object", description: "Old data for UPDATE/DELETE" },
        timestamp: { bsonType: "date", description: "Operation timestamp" },
        site: { enum: ["Site_A", "Site_B", "Site_C", "Global"], description: "Database site" },
        ip_address: { bsonType: "string", description: "Client IP" },
        user_agent: { bsonType: "string", description: "Client user agent" }
      }
    }
  }
});

db.createCollection('query_history', {
  validator: {
    $jsonSchema: {
      bsonType: "object",
      required: ["endpoint", "method", "timestamp"],
      properties: {
        endpoint: { bsonType: "string", description: "API endpoint" },
        method: { enum: ["GET", "POST", "PUT", "DELETE"], description: "HTTP method" },
        params: { bsonType: "object", description: "Query parameters" },
        body: { bsonType: "object", description: "Request body" },
        execution_time_ms: { bsonType: "number", description: "Execution time in milliseconds" },
        result_count: { bsonType: "int", description: "Number of results returned" },
        status_code: { bsonType: "int", description: "HTTP status code" },
        timestamp: { bsonType: "date", description: "Query timestamp" },
        ip_address: { bsonType: "string" }
      }
    }
  }
});

// Create indexes for performance
db.audit_logs.createIndex({ timestamp: -1 });
db.audit_logs.createIndex({ table: 1, timestamp: -1 });
db.audit_logs.createIndex({ operation: 1, timestamp: -1 });
db.audit_logs.createIndex({ site: 1, timestamp: -1 });

db.query_history.createIndex({ timestamp: -1 });
db.query_history.createIndex({ endpoint: 1, timestamp: -1 });
db.query_history.createIndex({ method: 1, timestamp: -1 });

print('✅ MongoDB initialization completed!');
```

---

## 🔧 PHASE 2: PHP MongoDB Integration

### 2.1. Cập nhật Dockerfile
**File**: `app/Dockerfile`

**Thêm MongoDB extension**:
```dockerfile
# Install MongoDB PHP driver
RUN pecl install mongodb && \
    docker-php-ext-enable mongodb && \
    echo "extension=mongodb.so" > /usr/local/etc/php/conf.d/mongodb.ini
```

### 2.2. Tạo MongoDB Connection Helper
**File**: `app/mongo_helper.php`

```php
<?php
require_once 'vendor/autoload.php'; // Nếu dùng Composer

class MongoHelper {
    private static $client = null;
    private static $db = null;
    
    public static function getClient() {
        if (self::$client === null) {
            try {
                $mongoHost = getenv('MONGO_HOST') ?: 'mongodb';
                $mongoPort = getenv('MONGO_PORT') ?: '27017';
                $mongoUser = getenv('MONGO_USER') ?: 'admin';
                $mongoPass = getenv('MONGO_PASSWORD') ?: 'Your@STROng!Mongo#Pass';
                
                $uri = "mongodb://{$mongoUser}:{$mongoPass}@{$mongoHost}:{$mongoPort}";
                self::$client = new MongoDB\Driver\Manager($uri);
            } catch (Exception $e) {
                error_log("MongoDB connection failed: " . $e->getMessage());
                return null;
            }
        }
        return self::$client;
    }
    
    public static function getDatabase($dbName = 'huflit_logs') {
        $client = self::getClient();
        if (!$client) return null;
        return $dbName;
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
    
    // Get statistics
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
```

### 2.3. Tạo Middleware Logger
**File**: `app/request_logger.php`

```php
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
```

---

## 🔌 PHASE 3: API Routes Integration

### 3.1. Cập nhật index.php
**File**: `app/public/index.php`

```php
<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Cache-Control, Pragma, Expires');
header('Access-Control-Max-Age: 3600');

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(204);
    exit(0);
}

require_once '../common.php';
require_once '../request_logger.php';

// Start logging
RequestLogger::start();

$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$query = $_GET;

$routes = [
    '/khoa' => 'khoa',
    '/monhoc' => 'monhoc',
    '/sinhvien' => 'sinhvien',
    '/dangky' => 'dangky',
    '/ctdaotao' => 'ctdaotao',
    '/global' => 'global',
    '/logs' => 'logs',          // NEW: Audit logs endpoint
    '/stats' => 'stats',        // NEW: Statistics endpoint
];

$method = $_SERVER['REQUEST_METHOD'];

if (array_key_exists($path, $routes)) {
    $routeFile = '../routes/' . $routes[$path] . '.php';
    if (file_exists($routeFile)) {
        require_once $routeFile;
        $functionName = 'handle' . ucfirst($routes[$path]);
        if (function_exists($functionName)) {
            $functionName($method, $query);
        } else {
            sendResponse(['error' => 'Handler not found'], 500);
        }
    } else {
        sendResponse(['error' => 'Route file not found'], 500);
    }
} else {
    require_once '../routes/default.php';
    handleDefault($method, $query);
}
?>
```

### 3.2. Tạo Logs Route
**File**: `app/routes/logs.php`

```php
<?php
require_once '../mongo_helper.php';

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
        
        $logs = MongoHelper::getAuditLogs($filter, $limit, $skip);
        
        // Convert to array and format
        $result = [];
        foreach ($logs as $log) {
            $logArray = (array)$log;
            // Convert MongoDB BSON types to readable format
            if (isset($logArray['timestamp'])) {
                $logArray['timestamp'] = $logArray['timestamp']->toDateTime()->format('Y-m-d H:i:s');
            }
            $result[] = $logArray;
        }
        
        RequestLogger::end(count($result), 200);
        sendResponse([
            'success' => true,
            'data' => $result,
            'page' => $page,
            'limit' => $limit
        ]);
        
    } catch (Exception $e) {
        RequestLogger::end(0, 500);
        sendResponse(['error' => $e->getMessage()], 500);
    }
}
?>
```

### 3.3. Tạo Stats Route
**File**: `app/routes/stats.php`

```php
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
    return $result[0]->total ?? 0;
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
    return MongoHelper::getAuditLogs([], $limit, 0);
}

function getQueryCount() {
    $pipeline = [
        ['$count' => 'total']
    ];
    $result = MongoHelper::getStatistics('query_history', $pipeline);
    return $result[0]->total ?? 0;
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
    return $result[0]->avg_time ?? 0;
}

function getSlowestQueries($limit) {
    return MongoHelper::getQueryHistory([], $limit, 0);
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
```

### 3.4. Cập nhật các Route hiện có
**Ví dụ**: `app/routes/sinhvien.php`

Thêm audit logging vào mỗi operation:

```php
<?php
require_once '../common.php';
require_once '../mongo_helper.php';

function handleSinhVien($method, $query) {
    try {
        $pdo = getDBConnection();
        switch ($method) {
            case 'POST':
                $data = getJsonInput();
                // ... existing validation ...
                
                // Execute SQL
                $stmt = $pdo->prepare("INSERT INTO SinhVien_Global (MaSV, HoTen, MaKhoa, KhoaHoc) VALUES (?, ?, ?, ?)");
                $stmt->execute([$data['MaSV'], $data['HoTen'], $data['MaKhoa'], $data['KhoaHoc']]);
                
                // Log to MongoDB
                $site = determineSite($data['MaKhoa']); // Helper function
                MongoHelper::logAudit('SinhVien', 'INSERT', $data, null, $site);
                
                sendResponse(['message' => 'SinhVien created successfully', 'MaSV' => $data['MaSV']], 201);
                break;
                
            case 'PUT':
                // Get old data first
                $stmt = $pdo->prepare("SELECT * FROM SinhVien_Global WHERE MaSV = ?");
                $stmt->execute([$query['id']]);
                $oldData = $stmt->fetch(PDO::FETCH_ASSOC);
                
                $data = getJsonInput();
                // ... existing validation and update ...
                
                // Log to MongoDB
                $site = determineSite($data['MaKhoa']);
                MongoHelper::logAudit('SinhVien', 'UPDATE', $data, $oldData, $site);
                
                sendResponse(['message' => 'SinhVien updated successfully']);
                break;
                
            case 'DELETE':
                // Get data before delete
                $stmt = $pdo->prepare("SELECT * FROM SinhVien_Global WHERE MaSV = ?");
                $stmt->execute([$query['id']]);
                $oldData = $stmt->fetch(PDO::FETCH_ASSOC);
                
                // ... existing delete logic ...
                
                // Log to MongoDB
                $site = determineSite($oldData['MaKhoa']);
                MongoHelper::logAudit('SinhVien', 'DELETE', null, $oldData, $site);
                
                sendResponse(['message' => 'SinhVien deleted successfully']);
                break;
        }
    } catch (Exception $e) {
        sendResponse(['error' => $e->getMessage()], 500);
    }
}

function determineSite($maKhoa) {
    if ($maKhoa < 'M') return 'Site_A';
    if ($maKhoa >= 'M' && $maKhoa < 'S') return 'Site_B';
    return 'Site_C';
}
?>
```

**Áp dụng tương tự cho**: `khoa.php`, `monhoc.php`, `dangky.php`, `ctdaotao.php`

---

## 🎨 PHASE 4: UI Dashboard

### 4.1. Tạo Logs Viewer Page
**File**: `app/public/logs.html`

```html
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Audit Logs - HUFLIT</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        .filter-bar {
            background: #f5f5f5;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
        }
        .filter-bar select, .filter-bar input {
            margin-right: 10px;
            padding: 8px;
        }
        .log-entry {
            background: white;
            border-left: 4px solid #007bff;
            padding: 15px;
            margin-bottom: 10px;
            border-radius: 3px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .log-entry.insert { border-left-color: #28a745; }
        .log-entry.update { border-left-color: #ffc107; }
        .log-entry.delete { border-left-color: #dc3545; }
        .log-header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
        }
        .log-badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 3px;
            font-size: 12px;
            font-weight: bold;
        }
        .log-data {
            background: #f8f9fa;
            padding: 10px;
            border-radius: 3px;
            font-family: monospace;
            font-size: 13px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>📋 Audit Logs & Change History</h1>
        
        <div class="filter-bar">
            <select id="tableFilter">
                <option value="">All Tables</option>
                <option value="Khoa">Khoa</option>
                <option value="SinhVien">SinhVien</option>
                <option value="MonHoc">MonHoc</option>
                <option value="CTDaoTao">CTDaoTao</option>
                <option value="DangKy">DangKy</option>
            </select>
            
            <select id="operationFilter">
                <option value="">All Operations</option>
                <option value="INSERT">INSERT</option>
                <option value="UPDATE">UPDATE</option>
                <option value="DELETE">DELETE</option>
            </select>
            
            <select id="siteFilter">
                <option value="">All Sites</option>
                <option value="Site_A">Site A</option>
                <option value="Site_B">Site B</option>
                <option value="Site_C">Site C</option>
                <option value="Global">Global</option>
            </select>
            
            <input type="date" id="fromDate" placeholder="From Date">
            <input type="date" id="toDate" placeholder="To Date">
            
            <button onclick="loadLogs()">Filter</button>
            <button onclick="clearFilters()">Clear</button>
        </div>
        
        <div id="logsContainer"></div>
        
        <div style="text-align: center; margin-top: 20px;">
            <button onclick="loadMore()">Load More</button>
        </div>
    </div>
    
    <script>
        let currentPage = 1;
        
        async function loadLogs(append = false) {
            if (!append) currentPage = 1;
            
            const params = new URLSearchParams({
                page: currentPage,
                limit: 20
            });
            
            const table = document.getElementById('tableFilter').value;
            const operation = document.getElementById('operationFilter').value;
            const site = document.getElementById('siteFilter').value;
            const from = document.getElementById('fromDate').value;
            const to = document.getElementById('toDate').value;
            
            if (table) params.append('table', table);
            if (operation) params.append('operation', operation);
            if (site) params.append('site', site);
            if (from) params.append('from', from);
            if (to) params.append('to', to);
            
            try {
                const response = await fetch(`http://localhost:8080/logs?${params}`);
                const result = await response.json();
                
                const container = document.getElementById('logsContainer');
                if (!append) container.innerHTML = '';
                
                result.data.forEach(log => {
                    container.innerHTML += formatLogEntry(log);
                });
            } catch (error) {
                alert('Error loading logs: ' + error.message);
            }
        }
        
        function formatLogEntry(log) {
            const opClass = log.operation.toLowerCase();
            return `
                <div class="log-entry ${opClass}">
                    <div class="log-header">
                        <div>
                            <span class="log-badge" style="background: #007bff; color: white;">${log.table}</span>
                            <span class="log-badge" style="background: #6c757d; color: white;">${log.operation}</span>
                            <span class="log-badge" style="background: #17a2b8; color: white;">${log.site}</span>
                        </div>
                        <div style="color: #666; font-size: 14px;">
                            ${log.timestamp} | ${log.ip_address}
                        </div>
                    </div>
                    ${log.old_data ? `<div><strong>Old Data:</strong><div class="log-data">${JSON.stringify(log.old_data, null, 2)}</div></div>` : ''}
                    ${log.data ? `<div><strong>${log.operation === 'DELETE' ? 'Deleted Data' : 'New Data'}:</strong><div class="log-data">${JSON.stringify(log.data, null, 2)}</div></div>` : ''}
                </div>
            `;
        }
        
        function clearFilters() {
            document.getElementById('tableFilter').value = '';
            document.getElementById('operationFilter').value = '';
            document.getElementById('siteFilter').value = '';
            document.getElementById('fromDate').value = '';
            document.getElementById('toDate').value = '';
            loadLogs();
        }
        
        function loadMore() {
            currentPage++;
            loadLogs(true);
        }
        
        // Load logs on page load
        loadLogs();
    </script>
</body>
</html>
```

### 4.2. Tạo Statistics Dashboard
**File**: `app/public/stats.html`

```html
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Statistics Dashboard - HUFLIT</title>
    <link rel="stylesheet" href="styles.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        .stat-card {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            text-align: center;
        }
        .stat-value {
            font-size: 32px;
            font-weight: bold;
            color: #007bff;
            margin: 10px 0;
        }
        .stat-label {
            color: #666;
            font-size: 14px;
        }
        .chart-container {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>📊 Statistics Dashboard</h1>
        
        <div class="stats-grid" id="statsOverview"></div>
        
        <div class="chart-container">
            <h3>Operations by Type</h3>
            <canvas id="operationsChart"></canvas>
        </div>
        
        <div class="chart-container">
            <h3>Operations by Table</h3>
            <canvas id="tablesChart"></canvas>
        </div>
        
        <div class="chart-container">
            <h3>Queries by Endpoint</h3>
            <canvas id="endpointsChart"></canvas>
        </div>
        
        <div class="chart-container">
            <h3>Average Response Time by Endpoint</h3>
            <canvas id="responseTimeChart"></canvas>
        </div>
    </div>
    
    <script>
        async function loadStatistics() {
            try {
                // Load overview stats
                const overviewResponse = await fetch('http://localhost:8080/stats?type=overview');
                const overview = await overviewResponse.json();
                displayOverview(overview.data);
                
                // Load query stats
                const queryResponse = await fetch('http://localhost:8080/stats?type=query_stats');
                const queryStats = await queryResponse.json();
                displayQueryStats(queryStats.data);
                
                // Load performance stats
                const perfResponse = await fetch('http://localhost:8080/stats?type=performance');
                const perfStats = await perfResponse.json();
                displayPerformanceStats(perfStats.data);
                
            } catch (error) {
                alert('Error loading statistics: ' + error.message);
            }
        }
        
        function displayOverview(data) {
            const container = document.getElementById('statsOverview');
            container.innerHTML = `
                <div class="stat-card">
                    <div class="stat-label">Total Operations</div>
                    <div class="stat-value">${data.total_operations}</div>
                </div>
                <div class="stat-card">
                    <div class="stat-label">Total Queries</div>
                    <div class="stat-value">${data.total_queries || 0}</div>
                </div>
            `;
            
            // Operations by type chart
            const opLabels = data.operations_by_type.map(item => item._id);
            const opData = data.operations_by_type.map(item => item.count);
            createPieChart('operationsChart', opLabels, opData);
            
            // Operations by table chart
            const tableLabels = data.operations_by_table.map(item => item._id);
            const tableData = data.operations_by_table.map(item => item.count);
            createBarChart('tablesChart', tableLabels, tableData, 'Operations');
        }
        
        function displayQueryStats(data) {
            const endpointLabels = data.queries_by_endpoint.map(item => item._id);
            const endpointData = data.queries_by_endpoint.map(item => item.count);
            createBarChart('endpointsChart', endpointLabels, endpointData, 'Queries');
        }
        
        function displayPerformanceStats(data) {
            const labels = data.avg_response_time_by_endpoint.map(item => item._id);
            const times = data.avg_response_time_by_endpoint.map(item => item.avg_time);
            createBarChart('responseTimeChart', labels, times, 'Avg Time (ms)', '#ff6384');
        }
        
        function createPieChart(canvasId, labels, data) {
            new Chart(document.getElementById(canvasId), {
                type: 'pie',
                data: {
                    labels: labels,
                    datasets: [{
                        data: data,
                        backgroundColor: ['#36a2eb', '#ff6384', '#ffce56']
                    }]
                }
            });
        }
        
        function createBarChart(canvasId, labels, data, label, color = '#36a2eb') {
            new Chart(document.getElementById(canvasId), {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: label,
                        data: data,
                        backgroundColor: color
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: { beginAtZero: true }
                    }
                }
            });
        }
        
        // Load stats on page load
        loadStatistics();
        
        // Refresh every 30 seconds
        setInterval(loadStatistics, 30000);
    </script>
</body>
</html>
```

---

## ✅ PHASE 5: Testing & Deployment

### 5.1. Testing Checklist

**Infrastructure**:
- [ ] MongoDB container starts successfully
- [ ] Collections and indexes created
- [ ] PHP MongoDB extension installed

**Audit Logs**:
- [ ] INSERT operations logged correctly
- [ ] UPDATE operations include old_data
- [ ] DELETE operations logged
- [ ] Site routing correct (A/B/C)
- [ ] Timestamp accuracy

**Query History**:
- [ ] All API calls logged
- [ ] Execution time measured
- [ ] Result count accurate
- [ ] Status codes logged

**API Endpoints**:
- [ ] `/logs` returns filtered results
- [ ] `/logs` pagination works
- [ ] `/stats?type=overview` returns correct data
- [ ] `/stats?type=query_stats` works
- [ ] `/stats?type=performance` works

**UI**:
- [ ] Logs page displays entries
- [ ] Filters work correctly
- [ ] Stats dashboard shows charts
- [ ] Real-time updates work

### 5.2. Deployment Steps

```powershell
# 1. Stop existing containers
docker-compose down

# 2. Update environment variables
# Create .env file with MongoDB password

# 3. Build and start containers
docker-compose up -d --build

# 4. Wait for MongoDB to be ready
Start-Sleep -Seconds 10

# 5. Verify MongoDB connection
docker exec -it mongodb mongosh -u admin -p 'Your@STROng!Mongo#Pass' --eval "db.adminCommand('ping')"

# 6. Check collections created
docker exec -it mongodb mongosh -u admin -p 'Your@STROng!Mongo#Pass' huflit_logs --eval "show collections"

# 7. Test API endpoints
curl http://localhost:8080/logs
curl http://localhost:8080/stats?type=overview

# 8. Test audit logging by creating a record
curl -X POST http://localhost:8080/khoa -H "Content-Type: application/json" -d '{"MaKhoa":"TEST","TenKhoa":"Test Khoa"}'

# 9. Verify log was created
curl http://localhost:8080/logs?table=Khoa

# 10. Access UI dashboards
start http://localhost:8081/logs.html
start http://localhost:8081/stats.html
```

---

## 📊 Expected Results

### Audit Logs Collection
```javascript
{
  "_id": ObjectId("..."),
  "table": "SinhVien",
  "operation": "INSERT",
  "data": {
    "MaSV": "SV001",
    "HoTen": "Nguyen Van A",
    "MaKhoa": "CNTT",
    "KhoaHoc": 2024
  },
  "timestamp": ISODate("2025-11-24T10:30:00Z"),
  "site": "Site_A",
  "ip_address": "172.18.0.1",
  "user_agent": "Mozilla/5.0..."
}
```

### Query History Collection
```javascript
{
  "_id": ObjectId("..."),
  "endpoint": "/sinhvien",
  "method": "GET",
  "params": {},
  "body": {},
  "execution_time_ms": 45,
  "result_count": 120,
  "status_code": 200,
  "timestamp": ISODate("2025-11-24T10:31:00Z"),
  "ip_address": "172.18.0.1"
}
```

---

## 🚀 Future Enhancements

1. **Real-time Notifications**: WebSocket cho live updates
2. **Export Functionality**: Export logs to CSV/Excel
3. **Advanced Filtering**: Complex query builder
4. **Alerting System**: Email/SMS alerts cho critical operations
5. **Data Retention**: Auto-cleanup old logs
6. **Performance Optimization**: Caching frequently accessed stats
7. **Security**: API authentication/authorization
8. **Backup Strategy**: Automated MongoDB backups

---

## 📝 Notes

- MongoDB sử dụng cổng **27017**
- Logs API endpoint: **http://localhost:8080/logs**
- Stats API endpoint: **http://localhost:8080/stats**
- Logs UI: **http://localhost:8081/logs.html**
- Stats Dashboard: **http://localhost:8081/stats.html**
- Default MongoDB credentials: `admin / Your@STROng!Mongo#Pass`
- Audit logs retention: Unlimited (có thể thêm TTL index)
- Query history retention: Unlimited (có thể thêm TTL index)

---

**Estimated Timeline**: 2-3 days
- Day 1: Infrastructure + MongoDB Integration (Phase 1-2)
- Day 2: API Routes + Logging Integration (Phase 3)
- Day 3: UI Dashboard + Testing (Phase 4-5)
