# 🚀 MONGODB INTEGRATION PLAN - Hybrid Storage Architecture

<div align="center">

**Kế hoạch triển khai tích hợp MongoDB vào HUFLIT Distributed Database**

[![MongoDB](https://img.shields.io/badge/MongoDB-7.0-green?logo=mongodb)](https://www.mongodb.com/)
[![Status](https://img.shields.io/badge/Status-Planning-yellow)]()
[![Priority](https://img.shields.io/badge/Priority-High-red)]()

</div>

---

## 📋 Tổng Quan

### 🎯 Mục tiêu

Tích hợp MongoDB vào hệ thống hiện tại để:
- 📊 **Analytics & Reporting**: Phân tích dữ liệu sinh viên, môn học
- 📝 **Audit Logging**: Ghi lại mọi thao tác CRUD
- 🔔 **Notifications**: Hệ thống thông báo real-time
- 📈 **Data Mining**: Dự đoán kết quả học tập, đề xuất môn học

### 🏗️ Kiến trúc mục tiêu

```text
                    ┌─────────────────┐
                    │    Web UI       │
                    └────────┬────────┘
                             │
                    ┌────────▼────────┐
                    │   PHP REST API  │
                    └────┬───────┬────┘
                         │       │
            ┌────────────▼─┐   ┌─▼────────────┐
            │  SQL Server  │   │   MongoDB    │
            │  (OLTP)      │   │  (Analytics) │
            └──────────────┘   └──────────────┘
            • Khoa                • audit_logs
            • SinhVien            • analytics
            • MonHoc              • notifications
            • CTDaoTao            • reports
            • DangKy              • feedbacks
```

---

## 📅 PHASE 1: INFRASTRUCTURE SETUP (Tuần 1-2)

### ✅ Step 1.1: Cài đặt MongoDB Container

**Thời gian**: 2-3 giờ  
**Mục tiêu**: Thêm MongoDB vào Docker Compose

#### 📝 Task List

- [ ] Cập nhật `docker-compose.yml`
- [ ] Tạo thư mục `db/mongodb`
- [ ] Tạo init script cho MongoDB
- [ ] Test kết nối MongoDB

#### 🔧 Implementation

**1. Cập nhật `docker-compose.yml`**

```yaml
services:
  # ... existing services (api_php, app_php, mssql_*) ...

  # ========== MONGODB SERVICE ==========
  mongodb:
    image: mongo:7.0
    container_name: mongodb_analytics
    networks:
      - huflit-network
    ports:
      - "27017:27017"
    environment:
      - MONGO_INITDB_ROOT_USERNAME=admin
      - MONGO_INITDB_ROOT_PASSWORD=${MONGO_PASSWORD:-MongoDB@2025}
      - MONGO_INITDB_DATABASE=huflit_analytics
    volumes:
      - ./db/mongodb:/docker-entrypoint-initdb.d
      - mongodb_data:/data/db
      - mongodb_config:/data/configdb
    command: --auth
    restart: unless-stopped
    healthcheck:
      test: echo 'db.runCommand("ping").ok' | mongosh localhost:27017/test --quiet
      interval: 10s
      timeout: 5s
      retries: 5

  # ========== MONGO EXPRESS (Optional - for development) ==========
  mongo_express:
    image: mongo-express:latest
    container_name: mongo_express
    networks:
      - huflit-network
    ports:
      - "8082:8081"
    environment:
      - ME_CONFIG_MONGODB_ADMINUSERNAME=admin
      - ME_CONFIG_MONGODB_ADMINPASSWORD=${MONGO_PASSWORD:-MongoDB@2025}
      - ME_CONFIG_MONGODB_URL=mongodb://admin:${MONGO_PASSWORD:-MongoDB@2025}@mongodb:27017/
      - ME_CONFIG_BASICAUTH_USERNAME=admin
      - ME_CONFIG_BASICAUTH_PASSWORD=admin
    depends_on:
      - mongodb
    restart: unless-stopped

volumes:
  # ... existing volumes ...
  mongodb_data:
    name: mongodb_data
  mongodb_config:
    name: mongodb_config
```

**2. Tạo `.env` file** (nếu chưa có)

```bash
# SQL Server
MSSQL_SA_PASSWORD=Your@STROng!Pass#Word

# MongoDB
MONGO_PASSWORD=MongoDB@2025
```

**3. Tạo init script `db/mongodb/init.js`**

```javascript
// Switch to huflit_analytics database
db = db.getSiblingDB('huflit_analytics');

// Create collections with validation
db.createCollection('audit_logs', {
  validator: {
    $jsonSchema: {
      bsonType: 'object',
      required: ['timestamp', 'action', 'entity'],
      properties: {
        timestamp: {
          bsonType: 'date',
          description: 'Timestamp of the action'
        },
        action: {
          enum: ['INSERT', 'UPDATE', 'DELETE'],
          description: 'Type of action performed'
        },
        entity: {
          enum: ['Khoa', 'MonHoc', 'SinhVien', 'CTDaoTao', 'DangKy'],
          description: 'Entity affected'
        },
        entityId: {
          bsonType: 'string',
          description: 'ID of the affected entity'
        },
        site: {
          enum: ['Site A', 'Site B', 'Site C'],
          description: 'Site where action occurred'
        },
        user: {
          bsonType: 'string',
          description: 'User who performed the action'
        }
      }
    }
  }
});

db.createCollection('student_analytics', {
  validator: {
    $jsonSchema: {
      bsonType: 'object',
      required: ['MaSV', 'semester'],
      properties: {
        MaSV: {
          bsonType: 'string',
          description: 'Student ID'
        },
        semester: {
          bsonType: 'string',
          description: 'Semester identifier'
        }
      }
    }
  }
});

db.createCollection('notifications');
db.createCollection('course_feedbacks');
db.createCollection('reports');

// Create indexes for performance
db.audit_logs.createIndex({ timestamp: -1 });
db.audit_logs.createIndex({ entity: 1, entityId: 1 });
db.audit_logs.createIndex({ site: 1 });
db.audit_logs.createIndex({ action: 1 });

db.student_analytics.createIndex({ MaSV: 1, semester: 1 }, { unique: true });
db.student_analytics.createIndex({ 'statistics.averageGrade': -1 });

db.notifications.createIndex({ recipient: 1, read: 1 });
db.notifications.createIndex({ createdAt: -1 });

db.course_feedbacks.createIndex({ MaMH: 1, semester: 1 });

// Create TTL index for old logs (delete after 90 days)
db.audit_logs.createIndex(
  { timestamp: 1 },
  { expireAfterSeconds: 7776000 } // 90 days
);

// Insert sample documents
db.audit_logs.insertOne({
  timestamp: new Date(),
  action: 'INSERT',
  entity: 'System',
  entityId: 'INIT',
  site: 'Global',
  user: 'system',
  oldData: null,
  newData: { message: 'MongoDB initialized successfully' },
  ipAddress: '127.0.0.1'
});

print('✅ MongoDB collections created successfully!');
print('📊 Collections:', db.getCollectionNames());
```

**4. Test commands**

```powershell
# Start all services
docker-compose up -d

# Check MongoDB logs
docker logs mongodb_analytics

# Connect to MongoDB shell
docker exec -it mongodb_analytics mongosh -u admin -p MongoDB@2025

# Test query
docker exec -it mongodb_analytics mongosh -u admin -p MongoDB@2025 --eval "db.getSiblingDB('huflit_analytics').audit_logs.countDocuments()"
```

---

### ✅ Step 1.2: Cài đặt MongoDB PHP Driver

**Thời gian**: 1-2 giờ  
**Mục tiêu**: Kết nối PHP với MongoDB

#### 📝 Task List

- [ ] Cập nhật Dockerfile để cài MongoDB extension
- [ ] Tạo connection helper
- [ ] Test connection
- [ ] Tạo wrapper classes

#### 🔧 Implementation

**1. Cập nhật `app/Dockerfile`**

```dockerfile
FROM php:8.2-cli

# Install system dependencies
RUN apt-get update && apt-get install -y \
    unixodbc-dev \
    gnupg2 \
    curl \
    apt-transport-https \
    libssl-dev \
    libcurl4-openssl-dev \
    pkg-config

# Install Microsoft ODBC Driver for SQL Server
RUN curl https://packages.microsoft.com/keys/microsoft.asc | apt-key add - && \
    curl https://packages.microsoft.com/config/debian/11/prod.list > /etc/apt/sources.list.d/mssql-release.list && \
    apt-get update && \
    ACCEPT_EULA=Y apt-get install -y msodbcsql18

# Install PHP extensions for SQL Server
RUN pecl install sqlsrv pdo_sqlsrv && \
    docker-php-ext-enable sqlsrv pdo_sqlsrv

# Install MongoDB extension
RUN pecl install mongodb && \
    docker-php-ext-enable mongodb

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Copy composer files
COPY composer.json composer.lock ./

# Install PHP dependencies
RUN composer install --no-dev --optimize-autoloader

COPY . .

CMD ["php", "-S", "0.0.0.0:8080", "-t", "public"]
```

**2. Tạo `app/composer.json`**

```json
{
  "name": "huflit/distributed-database",
  "description": "HUFLIT Distributed Database System",
  "type": "project",
  "require": {
    "php": ">=8.0",
    "mongodb/mongodb": "^1.17"
  },
  "autoload": {
    "psr-4": {
      "App\\": "src/"
    }
  }
}
```

**3. Tạo `app/mongo_connection.php`**

```php
<?php

use MongoDB\Client;
use MongoDB\Database;

class MongoConnection {
    private static $instance = null;
    private $client;
    private $database;
    
    private function __construct() {
        $host = getenv('MONGO_HOST') ?: 'mongodb';
        $port = getenv('MONGO_PORT') ?: '27017';
        $username = getenv('MONGO_USERNAME') ?: 'admin';
        $password = getenv('MONGO_PASSWORD') ?: 'MongoDB@2025';
        $dbName = getenv('MONGO_DATABASE') ?: 'huflit_analytics';
        
        $uri = sprintf(
            'mongodb://%s:%s@%s:%s/?authSource=admin',
            $username,
            $password,
            $host,
            $port
        );
        
        try {
            $this->client = new Client($uri);
            $this->database = $this->client->selectDatabase($dbName);
            
            // Test connection
            $this->client->listDatabases();
            error_log("✅ MongoDB connected successfully to database: $dbName");
        } catch (Exception $e) {
            error_log("❌ MongoDB connection failed: " . $e->getMessage());
            throw $e;
        }
    }
    
    public static function getInstance(): MongoConnection {
        if (self::$instance === null) {
            self::$instance = new MongoConnection();
        }
        return self::$instance;
    }
    
    public function getDatabase(): Database {
        return $this->database;
    }
    
    public function getCollection(string $collectionName) {
        return $this->database->selectCollection($collectionName);
    }
}

function getMongoConnection(): Database {
    return MongoConnection::getInstance()->getDatabase();
}

function getMongoCollection(string $collectionName) {
    return MongoConnection::getInstance()->getCollection($collectionName);
}
?>
```

**4. Cập nhật `docker-compose.yml` - thêm env cho api_php**

```yaml
services:
  api_php:
    build: ./app
    # ... existing config ...
    environment:
      - DB_HOST=mssql_global
      - DB_PORT=1433
      - DB_NAME=HUFLIT
      - DB_USER=sa
      - DB_PASS=${MSSQL_SA_PASSWORD}
      - MONGO_HOST=mongodb
      - MONGO_PORT=27017
      - MONGO_USERNAME=admin
      - MONGO_PASSWORD=${MONGO_PASSWORD}
      - MONGO_DATABASE=huflit_analytics
    depends_on:
      - mssql_global
      - mongodb
```

**5. Test connection `app/test_mongo.php`**

```php
<?php
require_once 'mongo_connection.php';

try {
    $mongo = getMongoConnection();
    
    // Test insert
    $collection = $mongo->selectCollection('audit_logs');
    $result = $collection->insertOne([
        'timestamp' => new MongoDB\BSON\UTCDateTime(),
        'action' => 'TEST',
        'entity' => 'System',
        'entityId' => 'TEST-001',
        'site' => 'Global',
        'user' => 'test_user',
        'message' => 'PHP connection test'
    ]);
    
    echo "✅ Insert successful! ID: " . $result->getInsertedId() . "\n";
    
    // Test query
    $count = $collection->countDocuments(['action' => 'TEST']);
    echo "📊 Test documents count: $count\n";
    
    // Test find
    $documents = $collection->find(['action' => 'TEST'])->toArray();
    echo "📄 Found documents:\n";
    print_r($documents);
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
?>
```

**6. Run test**

```powershell
# Rebuild containers
docker-compose down
docker-compose build
docker-compose up -d

# Install composer dependencies
docker exec -it api_php composer install

# Run test
docker exec -it api_php php test_mongo.php
```

---

### ✅ Step 1.3: Tạo Helper Classes & Utilities

**Thời gian**: 2-3 giờ  
**Mục tiêu**: Tạo các class tiện ích để làm việc với MongoDB

#### 📝 Task List

- [ ] Tạo AuditLogger class
- [ ] Tạo AnalyticsService class
- [ ] Tạo NotificationService class
- [ ] Unit tests

#### 🔧 Implementation

**1. Tạo `app/src/Services/AuditLogger.php`**

```php
<?php
namespace App\Services;

use MongoDB\Collection;
use MongoDB\BSON\UTCDateTime;

class AuditLogger {
    private Collection $collection;
    
    public function __construct() {
        $this->collection = getMongoCollection('audit_logs');
    }
    
    public function log(
        string $action,
        string $entity,
        string $entityId,
        string $site,
        $oldData = null,
        $newData = null,
        string $user = 'system',
        string $ipAddress = null
    ): void {
        try {
            $document = [
                'timestamp' => new UTCDateTime(),
                'action' => $action,
                'entity' => $entity,
                'entityId' => $entityId,
                'site' => $site,
                'user' => $user,
                'oldData' => $oldData,
                'newData' => $newData,
                'ipAddress' => $ipAddress ?? $_SERVER['REMOTE_ADDR'] ?? 'unknown'
            ];
            
            $this->collection->insertOne($document);
        } catch (\Exception $e) {
            error_log("Audit log failed: " . $e->getMessage());
        }
    }
    
    public function getEntityHistory(string $entity, string $entityId): array {
        return $this->collection->find(
            ['entity' => $entity, 'entityId' => $entityId],
            ['sort' => ['timestamp' => -1]]
        )->toArray();
    }
    
    public function getRecentLogs(int $limit = 100): array {
        return $this->collection->find(
            [],
            [
                'sort' => ['timestamp' => -1],
                'limit' => $limit
            ]
        )->toArray();
    }
    
    public function getLogsBySite(string $site, int $limit = 100): array {
        return $this->collection->find(
            ['site' => $site],
            [
                'sort' => ['timestamp' => -1],
                'limit' => $limit
            ]
        )->toArray();
    }
    
    public function getStatsByAction(): array {
        $pipeline = [
            [
                '$group' => [
                    '_id' => '$action',
                    'count' => ['$sum' => 1]
                ]
            ],
            [
                '$sort' => ['count' => -1]
            ]
        ];
        
        return $this->collection->aggregate($pipeline)->toArray();
    }
}
?>
```

**2. Tạo `app/src/Services/AnalyticsService.php`**

```php
<?php
namespace App\Services;

use MongoDB\Collection;
use MongoDB\BSON\UTCDateTime;

class AnalyticsService {
    private Collection $collection;
    
    public function __construct() {
        $this->collection = getMongoCollection('student_analytics');
    }
    
    public function updateStudentStats(
        string $maSV,
        string $semester,
        array $statistics
    ): void {
        $document = [
            'MaSV' => $maSV,
            'semester' => $semester,
            'statistics' => $statistics,
            'updatedAt' => new UTCDateTime(),
            'calculatedAt' => new UTCDateTime()
        ];
        
        $this->collection->updateOne(
            ['MaSV' => $maSV, 'semester' => $semester],
            ['$set' => $document],
            ['upsert' => true]
        );
    }
    
    public function getStudentStats(string $maSV, string $semester = null): ?array {
        $filter = ['MaSV' => $maSV];
        if ($semester) {
            $filter['semester'] = $semester;
        }
        
        $result = $this->collection->findOne(
            $filter,
            ['sort' => ['semester' => -1]]
        );
        
        return $result ? (array)$result : null;
    }
    
    public function getTopStudents(int $limit = 10): array {
        return $this->collection->find(
            [],
            [
                'sort' => ['statistics.averageGrade' => -1],
                'limit' => $limit
            ]
        )->toArray();
    }
    
    public function getAtRiskStudents(float $threshold = 5.0): array {
        return $this->collection->find([
            'statistics.averageGrade' => ['$lt' => $threshold]
        ])->toArray();
    }
    
    public function generateSemesterReport(string $semester): array {
        $pipeline = [
            ['$match' => ['semester' => $semester]],
            [
                '$group' => [
                    '_id' => null,
                    'totalStudents' => ['$sum' => 1],
                    'avgGrade' => ['$avg' => '$statistics.averageGrade'],
                    'passRate' => [
                        '$avg' => [
                            '$cond' => [
                                ['$gte' => ['$statistics.averageGrade', 5.0]],
                                1,
                                0
                            ]
                        ]
                    ]
                ]
            ]
        ];
        
        $result = $this->collection->aggregate($pipeline)->toArray();
        return $result[0] ?? [];
    }
}
?>
```

**3. Tạo `app/src/Services/NotificationService.php`**

```php
<?php
namespace App\Services;

use MongoDB\Collection;
use MongoDB\BSON\UTCDateTime;

class NotificationService {
    private Collection $collection;
    
    public function __construct() {
        $this->collection = getMongoCollection('notifications');
    }
    
    public function create(
        string $recipient,
        string $title,
        string $message,
        string $type = 'info',
        array $metadata = []
    ): string {
        $document = [
            'recipient' => $recipient, // MaSV
            'title' => $title,
            'message' => $message,
            'type' => $type, // info, warning, error, success
            'read' => false,
            'metadata' => $metadata,
            'createdAt' => new UTCDateTime()
        ];
        
        $result = $this->collection->insertOne($document);
        return (string)$result->getInsertedId();
    }
    
    public function getUnread(string $recipient): array {
        return $this->collection->find(
            ['recipient' => $recipient, 'read' => false],
            ['sort' => ['createdAt' => -1]]
        )->toArray();
    }
    
    public function markAsRead(string $notificationId): void {
        $this->collection->updateOne(
            ['_id' => new \MongoDB\BSON\ObjectId($notificationId)],
            ['$set' => ['read' => true, 'readAt' => new UTCDateTime()]]
        );
    }
    
    public function markAllAsRead(string $recipient): void {
        $this->collection->updateMany(
            ['recipient' => $recipient, 'read' => false],
            ['$set' => ['read' => true, 'readAt' => new UTCDateTime()]]
        );
    }
    
    public function getCount(string $recipient, bool $unreadOnly = true): int {
        $filter = ['recipient' => $recipient];
        if ($unreadOnly) {
            $filter['read'] = false;
        }
        return $this->collection->countDocuments($filter);
    }
}
?>
```

**4. Update `app/common.php` để load autoloader**

```php
<?php
// Existing code...

// Load Composer autoloader
require_once __DIR__ . '/vendor/autoload.php';

// Load MongoDB connection
require_once __DIR__ . '/mongo_connection.php';

// Existing functions...
?>
```

---

## 📅 PHASE 2: AUDIT LOGGING INTEGRATION (Tuần 2-3)

### ✅ Step 2.1: Tích hợp Audit Logging vào CRUD Operations

**Thời gian**: 4-6 giờ  
**Mục tiêu**: Log mọi thao tác INSERT/UPDATE/DELETE

#### 📝 Task List

- [ ] Update route handlers để log
- [ ] Tạo middleware logging
- [ ] Test logging cho mỗi entity
- [ ] Verify logs trong MongoDB

#### 🔧 Implementation

**1. Tạo `app/src/Middleware/AuditMiddleware.php`**

```php
<?php
namespace App\Middleware;

use App\Services\AuditLogger;

class AuditMiddleware {
    private AuditLogger $logger;
    
    public function __construct() {
        $this->logger = new AuditLogger();
    }
    
    public function logOperation(
        string $action,
        string $entity,
        string $entityId,
        $oldData = null,
        $newData = null
    ): void {
        // Determine site based on entity data
        $site = $this->determineSite($entity, $newData ?? $oldData);
        
        $this->logger->log(
            $action,
            $entity,
            $entityId,
            $site,
            $oldData,
            $newData,
            $this->getCurrentUser(),
            $this->getClientIp()
        );
    }
    
    private function determineSite(string $entity, $data): string {
        if ($entity === 'MonHoc') {
            return 'All Sites';
        }
        
        $maKhoa = null;
        if (is_array($data)) {
            $maKhoa = $data['MaKhoa'] ?? null;
        } elseif (is_object($data)) {
            $maKhoa = $data->MaKhoa ?? null;
        }
        
        if (!$maKhoa) {
            return 'Unknown';
        }
        
        if ($maKhoa < 'M') {
            return 'Site A';
        } elseif ($maKhoa >= 'M' && $maKhoa < 'S') {
            return 'Site B';
        } else {
            return 'Site C';
        }
    }
    
    private function getCurrentUser(): string {
        // TODO: Implement authentication
        return $_SERVER['PHP_AUTH_USER'] ?? 'anonymous';
    }
    
    private function getClientIp(): string {
        return $_SERVER['HTTP_X_FORWARDED_FOR'] 
            ?? $_SERVER['REMOTE_ADDR'] 
            ?? 'unknown';
    }
}
?>
```

**2. Update `app/routes/khoa.php` - Add logging**

```php
<?php
require_once '../common.php';

use App\Middleware\AuditMiddleware;

function handleKhoa($method, $query) {
    $audit = new AuditMiddleware();
    
    try {
        $pdo = getDBConnection();
        switch ($method) {
            case 'POST':
                $data = json_decode(file_get_contents('php://input'), true);
                
                // Validate
                if (!isset($data['MaKhoa']) || !isset($data['TenKhoa'])) {
                    sendResponse(['error' => 'Missing required fields'], 400);
                    break;
                }
                
                // Execute SQL
                $stmt = $pdo->prepare("INSERT INTO Khoa_Global (MaKhoa, TenKhoa) VALUES (?, ?)");
                $stmt->execute([$data['MaKhoa'], $data['TenKhoa']]);
                
                // ✅ LOG AUDIT
                $audit->logOperation(
                    'INSERT',
                    'Khoa',
                    $data['MaKhoa'],
                    null,
                    $data
                );
                
                sendResponse(['message' => 'Khoa created successfully'], 201);
                break;
                
            case 'PUT':
                if (!isset($query['makhoa'])) {
                    sendResponse(['error' => 'Missing parameter: makhoa'], 400);
                    break;
                }
                
                // Get old data first
                $stmt = $pdo->prepare("SELECT * FROM Khoa_Global WHERE MaKhoa = ?");
                $stmt->execute([$query['makhoa']]);
                $oldData = $stmt->fetch(PDO::FETCH_ASSOC);
                
                $data = json_decode(file_get_contents('php://input'), true);
                
                // Execute update
                $stmt = $pdo->prepare("UPDATE Khoa_Global SET TenKhoa = ? WHERE MaKhoa = ?");
                $stmt->execute([$data['TenKhoa'], $query['makhoa']]);
                
                // ✅ LOG AUDIT
                $audit->logOperation(
                    'UPDATE',
                    'Khoa',
                    $query['makhoa'],
                    $oldData,
                    array_merge($oldData, $data)
                );
                
                sendResponse(['message' => 'Khoa updated successfully']);
                break;
                
            case 'DELETE':
                if (!isset($query['makhoa'])) {
                    sendResponse(['error' => 'Missing parameter: makhoa'], 400);
                    break;
                }
                
                // Get data before delete
                $stmt = $pdo->prepare("SELECT * FROM Khoa_Global WHERE MaKhoa = ?");
                $stmt->execute([$query['makhoa']]);
                $oldData = $stmt->fetch(PDO::FETCH_ASSOC);
                
                // Execute delete
                $stmt = $pdo->prepare("DELETE FROM Khoa_Global WHERE MaKhoa = ?");
                $stmt->execute([$query['makhoa']]);
                
                // ✅ LOG AUDIT
                $audit->logOperation(
                    'DELETE',
                    'Khoa',
                    $query['makhoa'],
                    $oldData,
                    null
                );
                
                sendResponse(['message' => 'Khoa deleted successfully']);
                break;
                
            // GET case remains unchanged
            case 'GET':
                // ... existing code ...
                break;
        }
    } catch (Exception $e) {
        sendResponse(['error' => $e->getMessage()], 500);
    }
}
?>
```

**3. Apply tương tự cho các routes khác**

- `app/routes/monhoc.php`
- `app/routes/sinhvien.php`
- `app/routes/ctdaotao.php`
- `app/routes/dangky.php`

---

### ✅ Step 2.2: Tạo API Endpoint xem Audit Logs

**Thời gian**: 2-3 giờ

#### 🔧 Implementation

**1. Tạo `app/routes/audit.php`**

```php
<?php
require_once '../common.php';

use App\Services\AuditLogger;

function handleAudit($method, $query) {
    if ($method !== 'GET') {
        sendResponse(['error' => 'Only GET method is allowed'], 405);
        return;
    }
    
    try {
        $logger = new AuditLogger();
        
        if (isset($query['entity']) && isset($query['id'])) {
            // Get history for specific entity
            $logs = $logger->getEntityHistory($query['entity'], $query['id']);
            sendResponse($logs);
        } elseif (isset($query['site'])) {
            // Get logs by site
            $limit = isset($query['limit']) ? (int)$query['limit'] : 100;
            $logs = $logger->getLogsBySite($query['site'], $limit);
            sendResponse($logs);
        } elseif (isset($query['stats'])) {
            // Get statistics
            $stats = $logger->getStatsByAction();
            sendResponse($stats);
        } else {
            // Get recent logs
            $limit = isset($query['limit']) ? (int)$query['limit'] : 100;
            $logs = $logger->getRecentLogs($limit);
            sendResponse($logs);
        }
    } catch (Exception $e) {
        sendResponse(['error' => $e->getMessage()], 500);
    }
}
?>
```

**2. Update `app/public/index.php`**

```php
<?php
// ... existing code ...

} elseif (preg_match('#^/audit#', $path)) {
    require_once '../routes/audit.php';
    handleAudit($method, $query);
    
// ... existing code ...
?>
```

**3. Test audit logging**

```powershell
# Create a Khoa
curl -X POST http://localhost:8080/khoa -H "Content-Type: application/json" -d "{\"MaKhoa\":\"TEST\",\"TenKhoa\":\"Test Khoa\"}"

# View audit logs
curl http://localhost:8080/audit?limit=10

# View logs for specific entity
curl "http://localhost:8080/audit?entity=Khoa&id=TEST"

# View statistics
curl http://localhost:8080/audit?stats=1
```

---

### ✅ Step 2.3: Tạo UI Dashboard cho Audit Logs

**Thời gian**: 3-4 giờ

#### 🔧 Implementation

**1. Tạo `app/public/audit_dashboard.php`**

```php
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>📝 Audit Logs Dashboard</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        .audit-dashboard {
            padding: 20px;
        }
        
        .stats-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .stat-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        
        .stat-card h3 {
            margin: 0 0 10px 0;
            font-size: 14px;
            opacity: 0.9;
        }
        
        .stat-card .value {
            font-size: 32px;
            font-weight: bold;
        }
        
        .log-filters {
            margin-bottom: 20px;
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }
        
        .log-table {
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .log-entry {
            padding: 15px;
            border-bottom: 1px solid #eee;
            display: grid;
            grid-template-columns: 150px 80px 100px 150px 1fr;
            gap: 15px;
            align-items: center;
        }
        
        .log-entry:hover {
            background: #f9f9f9;
        }
        
        .log-action {
            font-weight: bold;
            padding: 4px 8px;
            border-radius: 4px;
            text-align: center;
        }
        
        .action-INSERT { background: #d4edda; color: #155724; }
        .action-UPDATE { background: #fff3cd; color: #856404; }
        .action-DELETE { background: #f8d7da; color: #721c24; }
        
        .site-badge {
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            text-align: center;
        }
        
        .site-a { background: #cfe2ff; color: #084298; }
        .site-b { background: #d1e7dd; color: #0f5132; }
        .site-c { background: #f8d7da; color: #842029; }
    </style>
</head>
<body>
    <div class="audit-dashboard">
        <h1>📝 Audit Logs Dashboard</h1>
        
        <div class="stats-cards" id="statsCards">
            <!-- Will be populated by JS -->
        </div>
        
        <div class="log-filters">
            <select id="entityFilter">
                <option value="">All Entities</option>
                <option value="Khoa">Khoa</option>
                <option value="MonHoc">MonHoc</option>
                <option value="SinhVien">SinhVien</option>
                <option value="CTDaoTao">CTDaoTao</option>
                <option value="DangKy">DangKy</option>
            </select>
            
            <select id="actionFilter">
                <option value="">All Actions</option>
                <option value="INSERT">INSERT</option>
                <option value="UPDATE">UPDATE</option>
                <option value="DELETE">DELETE</option>
            </select>
            
            <select id="siteFilter">
                <option value="">All Sites</option>
                <option value="Site A">Site A</option>
                <option value="Site B">Site B</option>
                <option value="Site C">Site C</option>
            </select>
            
            <button onclick="loadLogs()">🔄 Refresh</button>
            <button onclick="exportLogs()">📥 Export CSV</button>
        </div>
        
        <div class="log-table" id="logTable">
            <!-- Will be populated by JS -->
        </div>
    </div>
    
    <script>
        let allLogs = [];
        
        async function loadStats() {
            const response = await fetch('http://localhost:8080/audit?stats=1');
            const stats = await response.json();
            
            const statsHtml = stats.map(stat => `
                <div class="stat-card">
                    <h3>${stat._id}</h3>
                    <div class="value">${stat.count}</div>
                </div>
            `).join('');
            
            document.getElementById('statsCards').innerHTML = statsHtml;
        }
        
        async function loadLogs() {
            const response = await fetch('http://localhost:8080/audit?limit=1000');
            allLogs = await response.json();
            renderLogs(allLogs);
        }
        
        function renderLogs(logs) {
            const logsHtml = logs.map(log => {
                const timestamp = new Date(log.timestamp.$date).toLocaleString('vi-VN');
                return `
                    <div class="log-entry">
                        <div>${timestamp}</div>
                        <div class="log-action action-${log.action}">${log.action}</div>
                        <div>${log.entity}</div>
                        <div class="site-badge site-${log.site.toLowerCase().replace(' ', '-')}">${log.site}</div>
                        <div>
                            <strong>${log.entityId}</strong>
                            <br>
                            <small>User: ${log.user} | IP: ${log.ipAddress}</small>
                        </div>
                    </div>
                `;
            }).join('');
            
            document.getElementById('logTable').innerHTML = logsHtml || '<p style="padding: 20px;">No logs found</p>';
        }
        
        function filterLogs() {
            const entity = document.getElementById('entityFilter').value;
            const action = document.getElementById('actionFilter').value;
            const site = document.getElementById('siteFilter').value;
            
            let filtered = allLogs.filter(log => {
                return (!entity || log.entity === entity) &&
                       (!action || log.action === action) &&
                       (!site || log.site === site);
            });
            
            renderLogs(filtered);
        }
        
        document.getElementById('entityFilter').addEventListener('change', filterLogs);
        document.getElementById('actionFilter').addEventListener('change', filterLogs);
        document.getElementById('siteFilter').addEventListener('change', filterLogs);
        
        function exportLogs() {
            // TODO: Implement CSV export
            alert('Export feature coming soon!');
        }
        
        // Load data on page load
        loadStats();
        loadLogs();
        
        // Auto refresh every 30 seconds
        setInterval(() => {
            loadStats();
            loadLogs();
        }, 30000);
    </script>
</body>
</html>
```

**2. Access dashboard**

```
http://localhost:8081/audit_dashboard.php
```

---

## 📅 PHASE 3: ANALYTICS & REPORTING (Tuần 3-4)

### ✅ Step 3.1: ETL Job - Extract from SQL to MongoDB

**Thời gian**: 4-6 giờ  
**Mục tiêu**: Tự động đồng bộ dữ liệu analytics

#### 🔧 Implementation

**1. Tạo `app/jobs/sync_analytics.php`**

```php
<?php
require_once __DIR__ . '/../common.php';

use App\Services\AnalyticsService;

class AnalyticsSync {
    private $sqlPdo;
    private $analyticsService;
    
    public function __construct() {
        $this->sqlPdo = getDBConnection();
        $this->analyticsService = new AnalyticsService();
    }
    
    public function syncAllStudents(string $semester): void {
        echo "🔄 Starting analytics sync for semester: $semester\n";
        
        // Get all students
        $stmt = $this->sqlPdo->query("
            SELECT DISTINCT sv.MaSV, sv.HoTen, sv.MaKhoa, sv.KhoaHoc
            FROM SinhVien_Global sv
        ");
        
        $students = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $total = count($students);
        $count = 0;
        
        foreach ($students as $student) {
            $count++;
            echo "Processing {$count}/{$total}: {$student['MaSV']}...\n";
            
            $stats = $this->calculateStudentStats($student['MaSV'], $semester);
            
            if ($stats) {
                $this->analyticsService->updateStudentStats(
                    $student['MaSV'],
                    $semester,
                    $stats
                );
            }
        }
        
        echo "✅ Sync completed! Processed $count students.\n";
    }
    
    private function calculateStudentStats(string $maSV, string $semester): ?array {
        // Get all courses and grades for student
        $stmt = $this->sqlPdo->prepare("
            SELECT 
                dk.MaMon,
                mh.TenMH,
                dk.DiemThi,
                ctdt.MaKhoa,
                sv.KhoaHoc
            FROM DangKy_Global dk
            JOIN MonHoc_Global mh ON dk.MaMon = mh.MaMH
            JOIN SinhVien_Global sv ON dk.MaSV = sv.MaSV
            LEFT JOIN CTDaoTao_Global ctdt ON dk.MaMon = ctdt.MaMH AND sv.MaKhoa = ctdt.MaKhoa
            WHERE dk.MaSV = ?
        ");
        
        $stmt->execute([$maSV]);
        $courses = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        if (empty($courses)) {
            return null;
        }
        
        $totalCredits = count($courses) * 3; // Assume 3 credits per course
        $grades = array_filter(array_column($courses, 'DiemThi'), fn($g) => $g !== null);
        
        $averageGrade = !empty($grades) ? array_sum($grades) / count($grades) : 0;
        $passedCourses = count(array_filter($grades, fn($g) => $g >= 5.0));
        $failedCourses = count(array_filter($grades, fn($g) => $g < 5.0));
        
        return [
            'totalCourses' => count($courses),
            'completedCourses' => count($grades),
            'pendingCourses' => count($courses) - count($grades),
            'totalCredits' => $totalCredits,
            'averageGrade' => round($averageGrade, 2),
            'maxGrade' => !empty($grades) ? max($grades) : 0,
            'minGrade' => !empty($grades) ? min($grades) : 0,
            'passedCourses' => $passedCourses,
            'failedCourses' => $failedCourses,
            'passRate' => count($grades) > 0 ? round($passedCourses / count($grades) * 100, 2) : 0,
            'gpa' => round($averageGrade, 2),
            'atRisk' => $averageGrade < 5.0,
            'excellent' => $averageGrade >= 8.5,
            'courses' => array_map(function($course) {
                return [
                    'MaMon' => $course['MaMon'],
                    'TenMH' => $course['TenMH'],
                    'DiemThi' => $course['DiemThi'],
                    'passed' => $course['DiemThi'] >= 5.0
                ];
            }, $courses)
        ];
    }
}

// Run sync
if (php_sapi_name() === 'cli') {
    $semester = $argv[1] ?? '2025-1';
    $sync = new AnalyticsSync();
    $sync->syncAllStudents($semester);
}
?>
```

**2. Tạo cron job script `app/jobs/cron.sh`**

```bash
#!/bin/bash

# Run analytics sync daily at 2 AM
0 2 * * * cd /var/www/html/jobs && php sync_analytics.php "2025-1" >> /var/log/sync.log 2>&1
```

**3. Run manual sync**

```powershell
docker exec -it api_php php jobs/sync_analytics.php "2025-1"
```

---

### ✅ Step 3.2: Analytics API Endpoints

**Thời gian**: 2-3 giờ

#### 🔧 Implementation

**1. Tạo `app/routes/analytics.php`**

```php
<?php
require_once '../common.php';

use App\Services\AnalyticsService;

function handleAnalytics($method, $query) {
    if ($method !== 'GET') {
        sendResponse(['error' => 'Only GET method is allowed'], 405);
        return;
    }
    
    try {
        $service = new AnalyticsService();
        
        if (isset($query['masv'])) {
            // Get student stats
            $semester = $query['semester'] ?? null;
            $stats = $service->getStudentStats($query['masv'], $semester);
            sendResponse($stats ?? ['error' => 'No data found']);
            
        } elseif (isset($query['top'])) {
            // Get top students
            $limit = isset($query['limit']) ? (int)$query['limit'] : 10;
            $students = $service->getTopStudents($limit);
            sendResponse($students);
            
        } elseif (isset($query['atrisk'])) {
            // Get at-risk students
            $threshold = isset($query['threshold']) ? (float)$query['threshold'] : 5.0;
            $students = $service->getAtRiskStudents($threshold);
            sendResponse($students);
            
        } elseif (isset($query['report'])) {
            // Generate semester report
            $semester = $query['semester'] ?? '2025-1';
            $report = $service->generateSemesterReport($semester);
            sendResponse($report);
            
        } else {
            sendResponse(['error' => 'Invalid query parameters'], 400);
        }
        
    } catch (Exception $e) {
        sendResponse(['error' => $e->getMessage()], 500);
    }
}
?>
```

**2. Update `app/public/index.php`**

```php
} elseif (preg_match('#^/analytics#', $path)) {
    require_once '../routes/analytics.php';
    handleAnalytics($method, $query);
```

**3. Test analytics API**

```powershell
# Sync data first
docker exec -it api_php php jobs/sync_analytics.php "2025-1"

# Get student stats
curl "http://localhost:8080/analytics?masv=25DH123456"

# Get top 10 students
curl "http://localhost:8080/analytics?top=1&limit=10"

# Get at-risk students
curl "http://localhost:8080/analytics?atrisk=1&threshold=5.0"

# Get semester report
curl "http://localhost:8080/analytics?report=1&semester=2025-1"
```

---

## 📅 PHASE 4: ADVANCED FEATURES (Tuần 4-5)

### ✅ Step 4.1: Notifications System

**Chi tiết trong document riêng để giữ plan ngắn gọn**

### ✅ Step 4.2: Course Feedback System

**Chi tiết trong document riêng**

### ✅ Step 4.3: Predictive Analytics với ML

**Chi tiết trong document riêng**

---

## ✅ TESTING & VALIDATION

### Test Checklist

- [ ] MongoDB connection test
- [ ] Audit logging for all CRUD operations
- [ ] Analytics sync job
- [ ] API endpoints response time < 500ms
- [ ] Load test with 1000 concurrent requests
- [ ] Data consistency between SQL and MongoDB
- [ ] Backup & restore procedures

### Performance Benchmarks

| Metric | Target | Actual |
|--------|--------|--------|
| API Response Time | < 500ms | ___ ms |
| MongoDB Write | < 50ms | ___ ms |
| MongoDB Read | < 100ms | ___ ms |
| Analytics Sync | < 5 min | ___ min |
| Audit Log Volume | 10K/day | ___ K/day |

---

## 📚 DOCUMENTATION

### Files to create/update

- [x] `MONGODB_INTEGRATION_PLAN.md` (this file)
- [ ] `MONGODB_SETUP.md` - Detailed setup guide
- [ ] `ANALYTICS_GUIDE.md` - How to use analytics
- [ ] `API_DOCUMENTATION.md` - Update with new endpoints
- [ ] `DEPLOYMENT.md` - Production deployment guide

---

## 🚀 DEPLOYMENT CHECKLIST

### Pre-deployment

- [ ] Code review completed
- [ ] All tests passing
- [ ] Documentation updated
- [ ] Backup current database
- [ ] Performance benchmarks met

### Deployment Steps

```powershell
# 1. Pull latest code
git pull origin main

# 2. Stop containers
docker-compose down

# 3. Update .env with MongoDB credentials
# (edit .env file)

# 4. Build and start
docker-compose build
docker-compose up -d

# 5. Verify services
docker ps
docker logs mongodb_analytics
docker logs api_php

# 6. Run initial sync
docker exec -it api_php php jobs/sync_analytics.php "2025-1"

# 7. Test endpoints
curl http://localhost:8080/audit?limit=10
curl http://localhost:8080/analytics?report=1

# 8. Access dashboards
# - Web UI: http://localhost:8081/ui.php
# - Audit Dashboard: http://localhost:8081/audit_dashboard.php
# - Mongo Express: http://localhost:8082
```

### Post-deployment

- [ ] Verify all endpoints working
- [ ] Check audit logs being created
- [ ] Monitor MongoDB performance
- [ ] Setup monitoring alerts
- [ ] Schedule backup jobs

---

## 🆘 TROUBLESHOOTING

### Common Issues

**Issue 1: MongoDB connection refused**
```powershell
# Check MongoDB is running
docker ps | grep mongodb

# Check logs
docker logs mongodb_analytics

# Verify network
docker network inspect huflit-network
```

**Issue 2: PHP MongoDB extension not found**
```powershell
# Rebuild with MongoDB extension
docker-compose build --no-cache api_php
docker-compose up -d api_php

# Verify extension loaded
docker exec -it api_php php -m | grep mongodb
```

**Issue 3: Audit logs not being created**
```powershell
# Check MongoDB credentials in .env
cat .env

# Test connection manually
docker exec -it api_php php test_mongo.php

# Check API logs
docker logs api_php
```

---

## 📞 SUPPORT & CONTACTS

- **Project Lead**: [Your Name]
- **MongoDB Expert**: [Expert Name]
- **DevOps**: [DevOps Name]

---

<div align="center">

**[⬅️ Back to README](README.md)** | **[🏗️ Architecture](ARCHITECTURE.md)**

---

**Made with ❤️ for HUFLIT**

*Last updated: 2025-11-21*

</div>
