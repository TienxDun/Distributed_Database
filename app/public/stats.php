<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Statistics Dashboard - HUFLIT Distributed Database</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="css/base.css">
    <link rel="stylesheet" href="css/layout.css">
    <link rel="stylesheet" href="css/components.css">
    <link rel="stylesheet" href="css/responsive.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@2.9.4"></script>
</head>

<body>

    <div class="app-container">
        <!-- Sidebar -->
        <?php include 'sidebar.php';
        renderSidebar('stats'); ?>

        <!-- Main Wrapper -->
        <main class="main-wrapper">
            <!-- Top Header -->
            <header class="top-header">
                <div class="header-left">
                    <button class="toggle-sidebar-btn" id="toggleSidebarBtn">☰</button>
                    <h1 class="page-title">Statistics & Analytics</h1>
                </div>
            </header>

            <!-- Content Body -->
            <div class="content-body">
                <div class="container">

                    <!-- Stats Overview -->
                    <div id="statsOverview" style="margin-bottom: 2rem;">
                        <div class="card">
                            <div class="card-body" style="text-align: center; color: var(--slate-500);">
                                <div class="spinner"
                                    style="border-width: 3px; width: 30px; height: 30px; display: inline-block;"></div>
                                <p style="margin-top: 0.5rem;">Đang tải tổng quan hệ thống...</p>
                            </div>
                        </div>
                    </div>

                    <!-- Charts Grid -->
                    <div
                        style="display: grid; grid-template-columns: repeat(auto-fit, minmax(400px, 1fr)); gap: 1.5rem; margin-bottom: 1.5rem;">
                        <!-- Operations Chart -->
                        <div class="card">
                            <div class="card-header">
                                <h2 class="card-title">📊 Thao tác theo loại</h2>
                            </div>
                            <div class="card-body">
                                <div style="position: relative; height: 300px;">
                                    <canvas id="operationsChart"></canvas>
                                </div>
                            </div>
                        </div>

                        <!-- Tables Chart -->
                        <div class="card">
                            <div class="card-header">
                                <h2 class="card-title">📋 Thao tác theo bảng</h2>
                            </div>
                            <div class="card-body">
                                <div style="position: relative; height: 300px;">
                                    <canvas id="tablesChart"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div
                        style="display: grid; grid-template-columns: repeat(auto-fit, minmax(400px, 1fr)); gap: 1.5rem; margin-bottom: 1.5rem;">
                        <!-- Sites Chart -->
                        <div class="card">
                            <div class="card-header">
                                <h2 class="card-title">🗺️ Hoạt động theo Site</h2>
                            </div>
                            <div class="card-body">
                                <div style="position: relative; height: 300px;">
                                    <canvas id="sitesChart"></canvas>
                                </div>
                            </div>
                        </div>

                        <!-- Endpoints Chart -->
                        <div class="card">
                            <div class="card-header">
                                <h2 class="card-title">🔗 Truy vấn Endpoint</h2>
                            </div>
                            <div class="card-body">
                                <div style="position: relative; height: 300px;">
                                    <canvas id="endpointsChart"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Response Time Chart -->
                    <div class="card">
                        <div class="card-header">
                            <h2 class="card-title">⚡ Thời gian phản hồi (ms)</h2>
                        </div>
                        <div class="card-body">
                            <div style="position: relative; height: 300px;">
                                <canvas id="responseTimeChart"></canvas>
                                <div id="responseTimeMessage"
                                    style="text-align: center; padding: 20px; color: #666; display: none;">
                                    Chưa có dữ liệu phản hồi.
                                </div>
                            </div>
                        </div>
                    </div>

                    <div id="lastUpdated"
                        style="text-align: right; font-size: 0.75rem; color: var(--slate-400); margin-top: 1rem;"></div>

                </div>
            </div>
        </main>
    </div>

    <!-- LOADING OVERLAY -->
    <div id="loadingOverlay"
        style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(255,255,255,0.8); z-index:2000; align-items:center; justify-content:center;">
        <div style="text-align:center;">
            <div
                style="width:40px; height:40px; border:4px solid var(--slate-200); border-top-color:var(--primary-600); border-radius:50%; animation:spin 1s linear infinite;">
            </div>
            <div class="loading-text" style="margin-top:1rem; font-weight:500; color:var(--slate-600);">Đang xử lý...
            </div>
        </div>
    </div>
    <style>
        @keyframes spin {
            100% {
                transform: rotate(360deg);
            }
        }
    </style>

    <script src="js/sidebar.js"></script>
    <script type="module" src="js/stats.js"></script>
</body>

</html>