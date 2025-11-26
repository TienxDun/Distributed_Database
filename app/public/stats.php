<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Statistics Dashboard - HUFLIT MongoDB</title>
    <link rel="stylesheet" href="styles.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@2.9.4"></script>
</head>
<body>
    <!-- Loading Overlay -->
    <div id="loadingOverlay" class="loading-overlay">
        <div class="loading-spinner">
            <div class="spinner"></div>
            <div class="loading-text">Đang xử lý...</div>
        </div>
    </div>

    <!-- Sidebar Navigation -->
    <nav class="sidebar">
        <div class="sidebar-header">
            <h2>📊 Stats</h2>
            <p>Analytics Dashboard</p>
        </div>

        <div class="sidebar-section">
            <h3 class="sidebar-section-title">📊 Navigation</h3>
            <ul class="sidebar-nav">
                <li><a href="ui.php" class="sidebar-link">
                    <span class="sidebar-icon">🏠</span>
                    <span class="sidebar-text">Home</span>
                </a></li>
                <li><a href="logs.php" class="sidebar-link">
                    <span class="sidebar-icon">📋</span>
                    <span class="sidebar-text">Audit Logs</span>
                </a></li>
            </ul>
        </div>

        <div class="sidebar-section">
            <h3 class="sidebar-section-title">⚙️ Tools</h3>
            <ul class="sidebar-nav">
                <li><button class="sidebar-btn" onclick="toggleSiteStatusPanel()">
                    <span class="sidebar-icon">🔍</span>
                    <span class="sidebar-text">Site Status</span>
                </button></li>
                <li><button class="sidebar-btn" onclick="openSettingsModal()">
                    <span class="sidebar-icon">⚙️</span>
                    <span class="sidebar-text">Settings</span>
                </button></li>
            </ul>
        </div>

        <div class="sidebar-section">
            <h3 class="sidebar-section-title">🗺️ Data Sites</h3>
            <div class="site-toggle-container">
                <label class="site-toggle-label" for="toggleSiteColumn">
                    <input type="checkbox" id="toggleSiteColumn" checked onchange="toggleSiteColumnVisibility()" class="site-toggle-checkbox">
                    <div class="site-toggle-slider">
                        <span class="site-toggle-icon">🗺️</span>
                    </div>
                    <span class="site-toggle-text">
                        <strong>Show Site Column</strong>
                        <small>Distributed data</small>
                    </span>
                </label>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="main-content">
        <div class="topbar">
            <button class="sidebar-toggle" onclick="toggleSidebar()">
                <span class="hamburger-icon">☰</span>
            </button>
            <div class="topbar-title">
                <h1>📊 Statistics Dashboard</h1>
                <p>Thống kê và phân tích - HUFLIT Distributed Database</p>
            </div>
            <div class="topbar-actions">
                <div class="quick-actions">
                    <button class="quick-action-btn" onclick="loadStats()" title="Refresh">
                        <span>🔄</span>
                    </button>
                    <button class="quick-action-btn" onclick="resetCharts()" title="Reset Charts">
                        <span>🗑️</span>
                    </button>
                </div>
            </div>
        </div>

        <div class="content-wrapper">
            <div class="stats-overview" id="statsOverview">
                <div class="loading-container">
                    <div class="spinner"></div>
                    <div class="loading-text">Đang tải thống kê...</div>
                </div>
            </div>

            <div class="charts-grid">
                <div class="chart-container">
                    <h3>📊 Thao tác theo loại</h3>
                    <div class="chart-wrapper">
                        <canvas id="operationsChart"></canvas>
                    </div>
                </div>

                <div class="chart-container">
                    <h3>📋 Thao tác theo bảng</h3>
                    <div class="chart-wrapper">
                        <canvas id="tablesChart"></canvas>
                    </div>
                </div>
            </div>

            <div class="charts-grid">
                <div class="chart-container">
                    <h3>🗺️ Thao tác theo Site</h3>
                    <div class="chart-wrapper">
                        <canvas id="sitesChart"></canvas>
                    </div>
                </div>

                <div class="chart-container">
                    <h3>🔗 Truy vấn theo Endpoint</h3>
                    <div class="chart-wrapper">
                        <canvas id="endpointsChart"></canvas>
                    </div>
                </div>
            </div>

            <div class="chart-container">
                <h3>⚡ Thời gian phản hồi trung bình (ms)</h3>
                <div class="chart-wrapper">
                    <canvas id="responseTimeChart" style="width: 100%; height: 100%;"></canvas>
                    <div id="responseTimeMessage" style="text-align: center; padding: 20px; color: #666; display: none;">
                        Đang tải dữ liệu...
                    </div>
                </div>
            </div>

            <div class="last-updated" id="lastUpdated"></div>
        </div>
    </div>

    <script type="module">
        // Import required modules
        import { toggleSiteStatusPanel } from './js/modules/modal.js';
        import { loadSettings, openSettingsModal, closeSettingsModal, updateBackgroundColor, updateBackgroundColorFromText, applyPresetColor, resetToDefault } from './js/modules/settings.js';

        // Module variables
        let charts = {};

        // Expose functions to global scope
        window.toggleSidebar = function() {
            const sidebar = document.querySelector('.sidebar');
            const mainContent = document.querySelector('.main-content');

            sidebar.classList.toggle('collapsed');
            mainContent.classList.toggle('sidebar-collapsed');

            // Save preference to localStorage
            const isCollapsed = sidebar.classList.contains('collapsed');
            localStorage.setItem('sidebarCollapsed', isCollapsed);
        };

        window.toggleSiteStatusPanel = toggleSiteStatusPanel;
        window.openSettingsModal = openSettingsModal;
        window.closeSettingsModal = closeSettingsModal;
        window.updateBackgroundColor = updateBackgroundColor;
        window.updateBackgroundColorFromText = updateBackgroundColorFromText;
        window.applyPresetColor = applyPresetColor;
        window.resetToDefault = resetToDefault;

        // Expose stats functions to global scope
        window.loadStats = loadStatistics;
        window.resetCharts = function() {
            // Reset all charts by destroying them and clearing containers
            Object.keys(charts).forEach(chartId => {
                if (charts[chartId]) {
                    charts[chartId].destroy();
                    delete charts[chartId];
                }
            });
            // Reload statistics
            loadStatistics();
        };

        // Initialize on page load
        function initializePage() {
            loadSettings();

            // Load sidebar state
            const sidebarCollapsed = localStorage.getItem('sidebarCollapsed') === 'true';
            if (sidebarCollapsed) {
                document.querySelector('.sidebar').classList.add('collapsed');
                document.querySelector('.main-content').classList.add('sidebar-collapsed');
            }

            // Load stats
            loadStats();
        }

        // Initialize when DOM is ready
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initializePage);
        } else {
            initializePage();
        }

        async function loadStatistics() {
            try {
                // Load overview stats
                const overviewResponse = await fetch('http://localhost:8080/stats?type=overview');
                const overview = await overviewResponse.json();

                // Load query stats
                const queryResponse = await fetch('http://localhost:8080/stats?type=query_stats');
                const queryStats = await queryResponse.json();

                // Load performance stats
                const perfResponse = await fetch('http://localhost:8080/stats?type=performance');
                const perfStats = await perfResponse.json();

                displayOverview(overview.data, queryStats.data);
                displayCharts(overview.data, queryStats.data, perfStats.data);

                // Update last updated time
                const now = new Date();
                document.getElementById('lastUpdated').textContent = 
                    `⏰ Cập nhật lần cuối: ${now.toLocaleString('vi-VN')}`;

            } catch (error) {
                console.error('Error loading statistics:', error);
                document.getElementById('statsOverview').innerHTML = `
                    <div class="error-message">
                        <h3>❌ Lỗi tải thống kê</h3>
                        <p>${error.message}</p>
                    </div>
                `;
            }
        }

        function displayOverview(overviewData, queryData) {
            const container = document.getElementById('statsOverview');

            const totalOps = overviewData.total_operations || 0;
            const totalQueries = queryData.total_queries || 0;
            const avgTime = (queryData.avg_execution_time || 0).toFixed(2);

            // Calculate operation breakdown
            let insertCount = 0, updateCount = 0, deleteCount = 0;
            if (overviewData.operations_by_type) {
                overviewData.operations_by_type.forEach(item => {
                    if (item._id === 'INSERT') insertCount = item.count;
                    if (item._id === 'UPDATE') updateCount = item.count;
                    if (item._id === 'DELETE') deleteCount = item.count;
                });
            }

            container.innerHTML = `
                <div class="stat-card">
                    <div class="stat-card-header">
                        <div class="stat-icon blue">📝</div>
                        <div class="stat-content">
                            <div class="stat-label">Tổng thao tác</div>
                            <div class="stat-value blue">${totalOps.toLocaleString()}</div>
                        </div>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-card-header">
                        <div class="stat-icon green">✅</div>
                        <div class="stat-content">
                            <div class="stat-label">Insert</div>
                            <div class="stat-value green">${insertCount.toLocaleString()}</div>
                        </div>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-card-header">
                        <div class="stat-icon orange">🔄</div>
                        <div class="stat-content">
                            <div class="stat-label">Update</div>
                            <div class="stat-value orange">${updateCount.toLocaleString()}</div>
                        </div>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-card-header">
                        <div class="stat-icon red">❌</div>
                        <div class="stat-content">
                            <div class="stat-label">Delete</div>
                            <div class="stat-value red">${deleteCount.toLocaleString()}</div>
                        </div>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-card-header">
                        <div class="stat-icon purple">🔍</div>
                        <div class="stat-content">
                            <div class="stat-label">Truy vấn</div>
                            <div class="stat-value purple">${totalQueries.toLocaleString()}</div>
                        </div>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-card-header">
                        <div class="stat-icon cyan">⚡</div>
                        <div class="stat-content">
                            <div class="stat-label">Thời gian TB</div>
                            <div class="stat-value cyan">${avgTime}ms</div>
                        </div>
                    </div>
                </div>
            `;
        }

        function displayCharts(overviewData, queryData, perfData) {
            // Operations by type chart
            if (overviewData.operations_by_type && overviewData.operations_by_type.length > 0) {
                const opLabels = overviewData.operations_by_type.map(item => item._id);
                const opData = overviewData.operations_by_type.map(item => item.count);
                createPieChart('operationsChart', opLabels, opData, [
                    'rgba(16, 185, 129, 0.8)',
                    'rgba(245, 158, 11, 0.8)',
                    'rgba(239, 68, 68, 0.8)'
                ]);
            }

            // Operations by table chart
            if (overviewData.operations_by_table && overviewData.operations_by_table.length > 0) {
                const tableLabels = overviewData.operations_by_table.map(item => item._id);
                const tableData = overviewData.operations_by_table.map(item => item.count);
                createBarChart('tablesChart', tableLabels, tableData, 'Số thao tác', 'rgba(37, 99, 235, 0.8)');
            }

            // Operations by site chart
            if (overviewData.operations_by_site && overviewData.operations_by_site.length > 0) {
                const siteLabels = overviewData.operations_by_site.map(item => item._id);
                const siteData = overviewData.operations_by_site.map(item => item.count);
                createPieChart('sitesChart', siteLabels, siteData, [
                    'rgba(139, 92, 246, 0.8)',
                    'rgba(245, 158, 11, 0.8)',
                    'rgba(6, 182, 212, 0.8)',
                    'rgba(99, 102, 241, 0.8)'
                ]);
            }

            // Queries by endpoint chart
            if (queryData.queries_by_endpoint && queryData.queries_by_endpoint.length > 0) {
                const endpointLabels = queryData.queries_by_endpoint.map(item => item._id);
                const endpointData = queryData.queries_by_endpoint.map(item => item.count);
                createBarChart('endpointsChart', endpointLabels, endpointData, 'Số truy vấn', 'rgba(16, 185, 129, 0.8)');
            }

            // Response time chart
            if (perfData.avg_response_time_by_endpoint && perfData.avg_response_time_by_endpoint.length > 0) {
                console.log('Performance data:', perfData.avg_response_time_by_endpoint);
                // Filter out null avg_time values
                const validData = perfData.avg_response_time_by_endpoint.filter(item => item.avg_time !== null);
                console.log('Valid performance data:', validData);
                
                if (validData.length > 0) {
                    const labels = validData.map(item => item._id);
                    const times = validData.map(item => parseFloat(item.avg_time.toFixed(2)));
                    console.log('Labels:', labels, 'Times:', times);
                    createBarChart('responseTimeChart', labels, times, 'Thời gian TB (ms)', 'rgba(239, 68, 68, 0.8)');
                    document.getElementById('responseTimeMessage').style.display = 'none';
                } else {
                    console.log('No valid performance data after filtering');
                    document.getElementById('responseTimeMessage').textContent = 'Không có dữ liệu hiệu suất hợp lệ';
                    document.getElementById('responseTimeMessage').style.display = 'block';
                }
            } else {
                console.log('No performance data available:', perfData);
                document.getElementById('responseTimeMessage').textContent = 'Chưa có dữ liệu hiệu suất';
                document.getElementById('responseTimeMessage').style.display = 'block';
            }
        }

        function createPieChart(canvasId, labels, data, colors) {
            const ctx = document.getElementById(canvasId);
            if (!ctx) return;

            // Destroy existing chart if exists
            if (charts[canvasId]) {
                charts[canvasId].destroy();
            }

            charts[canvasId] = new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: labels,
                    datasets: [{
                        data: data,
                        backgroundColor: colors,
                        borderWidth: 3,
                        borderColor: '#fff'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                padding: 20,
                                font: {
                                    size: 13,
                                    weight: '600'
                                },
                                usePointStyle: true,
                                pointStyle: 'circle'
                            }
                        },
                        tooltip: {
                            backgroundColor: 'rgba(0, 0, 0, 0.8)',
                            padding: 12,
                            titleFont: {
                                size: 14,
                                weight: 'bold'
                            },
                            bodyFont: {
                                size: 13
                            },
                            cornerRadius: 8
                        }
                    }
                }
            });
        }

        function createBarChart(canvasId, labels, data, label, color) {
            try {
                const ctx = document.getElementById(canvasId);
                if (!ctx) {
                    console.error('Canvas not found:', canvasId);
                    return;
                }

                // Destroy existing chart if exists
                if (charts[canvasId]) {
                    charts[canvasId].destroy();
                }

                console.log('Creating chart for', canvasId, 'with labels:', labels, 'data:', data);
                charts[canvasId] = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: label,
                            data: data,
                            backgroundColor: color,
                            borderColor: color.replace('0.8', '1'),
                            borderWidth: 2,
                            borderRadius: 6,
                            borderSkipped: false,
                            barThickness: 40,
                            maxBarThickness: 50
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        layout: {
                            padding: {
                                left: 20,
                                right: 20,
                                top: 20,
                                bottom: 20
                            }
                        },
                        plugins: {
                            legend: {
                                display: false
                            },
                            tooltip: {
                                backgroundColor: 'rgba(0, 0, 0, 0.8)',
                                padding: 12,
                                titleFont: {
                                    size: 14,
                                    weight: 'bold'
                                },
                                bodyFont: {
                                    size: 13
                                },
                                cornerRadius: 8
                            }
                        },
                        scales: {
                            yAxes: [{
                                ticks: {
                                    beginAtZero: true,
                                    precision: 0,
                                    fontSize: 12,
                                    fontColor: '#666'
                                },
                                gridLines: {
                                    color: 'rgba(0, 0, 0, 0.05)',
                                    drawBorder: false
                                },
                                scaleLabel: {
                                    display: true,
                                    labelString: 'Số thao tác',
                                    fontSize: 14,
                                    fontColor: '#333'
                                }
                            }],
                            xAxes: [{
                                ticks: {
                                    maxRotation: 45,
                                    minRotation: 45,
                                    fontSize: 12,
                                    fontColor: '#666'
                                },
                                gridLines: {
                                    display: false,
                                    drawBorder: false
                                },
                                scaleLabel: {
                                    display: true,
                                    labelString: 'Bảng dữ liệu',
                                    fontSize: 14,
                                    fontColor: '#333'
                                }
                            }]
                        }
                    }
                });
            } catch (error) {
                console.error('Error creating chart', canvasId, ':', error);
            }
        }

        // Load stats on page load
        loadStatistics();

        // Refresh every 30 seconds
        setInterval(loadStatistics, 30000);
    </script>

    <!-- Settings Modal -->
    <div id="settingsModal" class="modal">
        <div class="modal-content" style="max-width: 500px;">
            <div class="modal-header">
                <h2>⚙️ Cài đặt giao diện</h2>
                <button class="modal-close" onclick="closeSettingsModal()">&times;</button>
            </div>
            <div class="modal-body">
                <div class="settings-section">
                    <h3 style="margin-bottom: 1.5rem; color: var(--text); font-size: 1.1rem;">🎨 Màu nền</h3>

                    <div class="form-group">
                        <label for="bgColor" style="font-weight: 600; font-size: 0.95rem;">Chọn màu nền:</label>
                        <div style="display: flex; gap: 1rem; align-items: center;">
                            <input type="color" id="bgColor" value="#f8fafc" oninput="updateBackgroundColor()"
                                style="width: 80px; height: 80px; border: 3px solid var(--border); border-radius: 12px; cursor: pointer; box-shadow: 0 4px 8px rgba(0,0,0,0.1);">
                            <div style="flex: 1;">
                                <input type="text" id="bgColorText" value="#f8fafc" onchange="updateBackgroundColorFromText()"
                                    placeholder="#RRGGBB"
                                    style="width: 100%; padding: 0.75rem; border: 2px solid var(--border); border-radius: 8px; font-family: monospace; font-size: 1rem; font-weight: 600;">
                                <small style="display: block; margin-top: 0.5rem; color: var(--secondary);">
                                    Ví dụ: #ffffff (trắng), #000000 (đen), #f0f0f0 (xám nhạt)
                                </small>
                            </div>
                        </div>
                    </div>

                    <div style="margin-top: 2rem; padding: 1rem; background: rgba(37, 99, 235, 0.05); border-radius: 8px; border-left: 4px solid var(--primary);">
                        <h4 style="margin-bottom: 0.75rem; color: var(--primary); font-size: 0.9rem;">💡 Gợi ý màu sắc</h4>
                        <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 0.75rem;">
                            <button onclick="applyPresetColor('#ffffff')" style="padding: 0.5rem; border: 2px solid #ddd; border-radius: 6px; cursor: pointer; background: #ffffff; aspect-ratio: 1;" title="Trắng"></button>
                            <button onclick="applyPresetColor('#f8fafc')" style="padding: 0.5rem; border: 2px solid #ddd; border-radius: 6px; cursor: pointer; background: #f8fafc; aspect-ratio: 1;" title="Xám nhạt"></button>
                            <button onclick="applyPresetColor('#e0e7ff')" style="padding: 0.5rem; border: 2px solid #ddd; border-radius: 6px; cursor: pointer; background: #e0e7ff; aspect-ratio: 1;" title="Xanh nhạt"></button>
                            <button onclick="applyPresetColor('#fef3c7')" style="padding: 0.5rem; border: 2px solid #ddd; border-radius: 6px; cursor: pointer; background: #fef3c7; aspect-ratio: 1;" title="Vàng nhạt"></button>
                            <button onclick="applyPresetColor('#dcfce7')" style="padding: 0.5rem; border: 2px solid #ddd; border-radius: 6px; cursor: pointer; background: #dcfce7; aspect-ratio: 1;" title="Xanh lá nhạt"></button>
                            <button onclick="applyPresetColor('#fee2e2')" style="padding: 0.5rem; border: 2px solid #ddd; border-radius: 6px; cursor: pointer; background: #fee2e2; aspect-ratio: 1;" title="Đỏ nhạt"></button>
                            <button onclick="applyPresetColor('#f3e8ff')" style="padding: 0.5rem; border: 2px solid #ddd; border-radius: 6px; cursor: pointer; background: #f3e8ff; aspect-ratio: 1;" title="Tím nhạt"></button>
                            <button onclick="applyPresetColor('#cffafe')" style="padding: 0.5rem; border: 2px solid #ddd; border-radius: 6px; cursor: pointer; background: #cffafe; aspect-ratio: 1;" title="Cyan nhạt"></button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" type="button" onclick="resetToDefault()">🔄 Khôi phục mặc định</button>
                <button class="btn btn-cancel" type="button" onclick="closeSettingsModal()">Đóng</button>
            </div>
        </div>
    </div>

    <!-- Site Status Panel (Left Side) -->
    <div id="siteStatusPanel" class="site-status-panel">
        <div class="site-status-panel-header">
            <h3>🔍 Site Status</h3>
            <div class="site-status-panel-controls">
                <button onclick="refreshSiteStatus()" id="panel-refresh-btn" title="Làm mới">
                    <span id="panel-refresh-icon">🔄</span>
                </button>
                <button onclick="toggleSiteStatusPanel()" title="Đóng">&times;</button>
            </div>
        </div>
        <div class="site-status-panel-content">
            <div id="site-status-compact">
                <div class="loading-spinner" style="text-align: center; padding: 2rem;">
                    <div class="spinner" style="width: 30px; height: 30px;"></div>
                    <div style="margin-top: 0.5rem; color: #666; font-size: 0.9rem;">Loading...</div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
