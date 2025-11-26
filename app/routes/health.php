<?php
require_once '../common.php';

function handleHealth($method, $query) {
    try {
        $pdo = getDBConnection();

        $sites = [
            'Global' => ['name' => 'Global DB', 'status' => 'unknown', 'response_time' => 0, 'details' => ''],
            'Site_A' => ['name' => 'Site A (A-L)', 'status' => 'unknown', 'response_time' => 0, 'details' => ''],
            'Site_B' => ['name' => 'Site B (M-R)', 'status' => 'unknown', 'response_time' => 0, 'details' => ''],
            'Site_C' => ['name' => 'Site C (S-Z)', 'status' => 'unknown', 'response_time' => 0, 'details' => '']
        ];

        // Check Global DB
        $start = microtime(true);
        try {
            $stmt = $pdo->query("SELECT 1 as test");
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            $response_time = round((microtime(true) - $start) * 1000, 2); // ms
            $sites['Global']['status'] = 'healthy';
            $sites['Global']['response_time'] = $response_time;
            $sites['Global']['details'] = 'Connected successfully';
        } catch (Exception $e) {
            $sites['Global']['status'] = 'unhealthy';
            $sites['Global']['response_time'] = round((microtime(true) - $start) * 1000, 2);
            $sites['Global']['details'] = $e->getMessage();
        }

        // Check each site via linked server
        $siteDatabases = [
            'Site_A' => 'SiteA',
            'Site_B' => 'SiteB', 
            'Site_C' => 'SiteC'
        ];
        
        foreach ($siteDatabases as $site => $dbName) {
            $start = microtime(true);
            try {
                // Try a simple query to test connectivity
                $stmt = $pdo->query("SELECT TOP 1 MaKhoa FROM [{$site}].{$dbName}.dbo.Khoa");
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
                $response_time = round((microtime(true) - $start) * 1000, 2);
                $sites[$site]['status'] = 'healthy';
                $sites[$site]['response_time'] = $response_time;
                $sites[$site]['details'] = 'Connected via linked server';
            } catch (Exception $e) {
                $sites[$site]['status'] = 'unhealthy';
                $sites[$site]['response_time'] = round((microtime(true) - $start) * 1000, 2);
                $sites[$site]['details'] = 'Connection failed: ' . $e->getMessage();
            }
        }

        // Calculate overall system health
        $healthy_sites = count(array_filter($sites, function($site) {
            return $site['status'] === 'healthy';
        }));

        if ($healthy_sites === 4) {
            $overall_status = 'healthy';
        } elseif ($healthy_sites >= 2) {
            $overall_status = 'degraded';
        } else {
            $overall_status = 'critical';
        }

        $response = [
            'overall_status' => $overall_status,
            'healthy_sites' => $healthy_sites,
            'total_sites' => 4,
            'sites' => $sites,
            'timestamp' => date('Y-m-d H:i:s')
        ];

        sendResponse($response);

    } catch (Exception $e) {
        sendResponse(['error' => $e->getMessage()], 500);
    }
}