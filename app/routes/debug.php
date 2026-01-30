<?php
require_once __DIR__ . '/../Core/common.php';

function handleDebug($method, $query) {
    header('Content-Type: application/json; charset=utf-8');
    
    $result = [
        'status' => 'checking',
        'timestamp' => date('Y-m-d H:i:s'),
        'checks' => []
    ];
    
    try {
        // Test 1: Database Connection
        $pdo = getDBConnection();
        $result['checks']['database_connection'] = [
            'status' => 'success',
            'message' => '✅ Kết nối database thành công'
        ];
        
        // Test 2: Check if schemas exist
        $stmt = $pdo->query("
            SELECT schema_name 
            FROM information_schema.schemata 
            WHERE schema_name IN ('site_a', 'site_b', 'site_c')
            ORDER BY schema_name
        ");
        $schemas = $stmt->fetchAll(PDO::FETCH_COLUMN);
        
        if (count($schemas) === 3) {
            $result['checks']['schemas'] = [
                'status' => 'success',
                'message' => '✅ Tất cả 3 schemas đã tồn tại',
                'schemas' => $schemas
            ];
        } else {
            $result['checks']['schemas'] = [
                'status' => 'error',
                'message' => '❌ Thiếu schemas',
                'found' => $schemas,
                'expected' => ['site_a', 'site_b', 'site_c']
            ];
        }
        
        // Test 3: Check if Khoa_Global view exists
        try {
            $stmt = $pdo->query("SELECT COUNT(*) as count FROM Khoa_Global");
            $count = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
            
            $result['checks']['khoa_global_view'] = [
                'status' => 'success',
                'message' => '✅ View Khoa_Global tồn tại',
                'record_count' => (int)$count
            ];
        } catch (Exception $e) {
            $result['checks']['khoa_global_view'] = [
                'status' => 'error',
                'message' => '❌ View Khoa_Global không tồn tại hoặc có lỗi',
                'error' => $e->getMessage()
            ];
        }
        
        // Test 4: Check if tables exist in site_a
        try {
            $stmt = $pdo->query("
                SELECT table_name 
                FROM information_schema.tables 
                WHERE table_schema = 'site_a'
                ORDER BY table_name
            ");
            $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
            
            $expectedTables = ['khoa', 'monhoc', 'sinhvien', 'ctdaotao', 'dangky'];
            $missingTables = array_diff($expectedTables, array_map('strtolower', $tables));
            
            if (empty($missingTables)) {
                $result['checks']['site_a_tables'] = [
                    'status' => 'success',
                    'message' => '✅ Tất cả bảng trong Site A đã tồn tại',
                    'tables' => $tables
                ];
            } else {
                $result['checks']['site_a_tables'] = [
                    'status' => 'error',
                    'message' => '❌ Thiếu bảng trong Site A',
                    'found' => $tables,
                    'missing' => array_values($missingTables)
                ];
            }
        } catch (Exception $e) {
            $result['checks']['site_a_tables'] = [
                'status' => 'error',
                'message' => '❌ Không thể kiểm tra bảng Site A',
                'error' => $e->getMessage()
            ];
        }
        
        // Test 5: Check environment variables
        $envVars = [
            'DB_HOST' => getenv('DB_HOST'),
            'DB_PORT' => getenv('DB_PORT'),
            'DB_NAME' => getenv('DB_NAME'),
            'DB_USER' => getenv('DB_USER') ? '***' : null,
            'DB_PASS' => getenv('DB_PASS') ? '***' : null,
            'MONGO_URI' => getenv('MONGO_URI') ? '***' : null,
        ];
        
        $missingEnvVars = array_keys(array_filter($envVars, fn($v) => $v === false || $v === null));
        
        if (empty($missingEnvVars)) {
            $result['checks']['environment_variables'] = [
                'status' => 'success',
                'message' => '✅ Tất cả biến môi trường đã được cấu hình',
                'variables' => $envVars
            ];
        } else {
            $result['checks']['environment_variables'] = [
                'status' => 'warning',
                'message' => '⚠️ Thiếu biến môi trường',
                'missing' => $missingEnvVars,
                'configured' => $envVars
            ];
        }
        
        // Overall status
        $hasErrors = false;
        foreach ($result['checks'] as $check) {
            if ($check['status'] === 'error') {
                $hasErrors = true;
                break;
            }
        }
        
        $result['status'] = $hasErrors ? 'error' : 'success';
        $result['summary'] = $hasErrors 
            ? '❌ Hệ thống có lỗi cần khắc phục'
            : '✅ Hệ thống hoạt động bình thường';
            
    } catch (Exception $e) {
        $result['status'] = 'error';
        $result['summary'] = '❌ Lỗi nghiêm trọng';
        $result['checks']['fatal_error'] = [
            'status' => 'error',
            'message' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ];
    }
    
    http_response_code($result['status'] === 'success' ? 200 : 500);
    echo json_encode($result, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
}
