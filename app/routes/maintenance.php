<?php
require_once __DIR__ . '/../common.php';

function handleMaintenance($method, $query)
{
    try {
        $pdo = getDBConnection();
        $action = $query['action'] ?? '';

        switch ($method) {
            case 'POST':
                if ($action === 'reset') {
                    // Reset all tables in all schemas
                    $schemas = ['site_a', 'site_b', 'site_c'];
                    $tables = ['DangKy', 'SinhVien', 'CTDaoTao', 'Khoa', 'MonHoc'];

                    foreach ($schemas as $schema) {
                        foreach ($tables as $table) {
                            $pdo->exec("TRUNCATE TABLE {$schema}.{$table} CASCADE");
                        }
                    }

                    sendResponse(['message' => 'Toàn bộ dữ liệu đã được xóa sạch!']);
                } elseif ($action === 'seed') {
                    // Reset dữ liệu trước khi seed
                    $schemas = ['site_a', 'site_b', 'site_c'];
                    $tables = ['DangKy', 'SinhVien', 'CTDaoTao', 'Khoa', 'MonHoc'];

                    foreach ($schemas as $schema) {
                        foreach ($tables as $table) {
                            $pdo->exec("TRUNCATE TABLE {$schema}.{$table} CASCADE");
                        }
                    }

                    // Execute the Postgres seed script
                    $seedFile = __DIR__ . '/../db/seed_postgres.sql';
                    if (!file_exists($seedFile)) {
                        throw new Exception('Không tìm thấy file seed_postgres.sql tại: ' . $seedFile);
                    }

                    $sql = file_get_contents($seedFile);
                    $pdo->exec($sql);

                    sendResponse(['message' => 'Dữ liệu mẫu đã được nạp thành công!']);
                } elseif ($action === 'init') {
                    // Drop existing schemas
                    $schemas = ['site_a', 'site_b', 'site_c'];
                    foreach ($schemas as $schema) {
                        $pdo->exec("DROP SCHEMA IF EXISTS {$schema} CASCADE");
                    }

                    // Execute the Postgres init script
                    $initFile = __DIR__ . '/../db/init_postgres.sql';
                    if (!file_exists($initFile)) {
                        throw new Exception('Không tìm thấy file init_postgres.sql tại: ' . $initFile);
                    }

                    $sql = file_get_contents($initFile);
                    $pdo->exec($sql);

                    // Execute the Postgres triggers script
                    $triggersFile = __DIR__ . '/../db/triggers_postgres.sql';
                    if (!file_exists($triggersFile)) {
                        throw new Exception('Không tìm thấy file triggers_postgres.sql tại: ' . $triggersFile);
                    }

                    $sql = file_get_contents($triggersFile);
                    $pdo->exec($sql);

                    sendResponse(['message' => 'Database schema đã được khởi tạo thành công!']);
                } else {
                    sendResponse(['error' => 'Action không hợp lệ'], 400);
                }
                break;

            case 'GET':
                if ($action === 'explore') {
                    $table = $query['table'] ?? 'Khoa';
                    // White list tables to prevent SQL injection
                    $allowedTables = ['Khoa', 'MonHoc', 'SinhVien', 'CTDaoTao', 'DangKy'];
                    if (!in_array($table, $allowedTables)) {
                        throw new Exception('Bảng không hợp lệ');
                    }

                    $results = [
                        'site_a' => $pdo->query("SELECT * FROM site_a.{$table}")->fetchAll(PDO::FETCH_ASSOC),
                        'site_b' => $pdo->query("SELECT * FROM site_b.{$table}")->fetchAll(PDO::FETCH_ASSOC),
                        'site_c' => $pdo->query("SELECT * FROM site_c.{$table}")->fetchAll(PDO::FETCH_ASSOC),
                    ];

                    sendResponse($results);
                } else {
                    sendResponse(['error' => 'Action không hợp lệ'], 400);
                }
                break;

            default:
                sendResponse(['error' => 'Method not allowed'], 405);
        }
    } catch (Exception $e) {
        sendResponse(['error' => $e->getMessage()], 500);
    }
}
