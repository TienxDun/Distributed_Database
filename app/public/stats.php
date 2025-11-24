<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Statistics Dashboard - HUFLIT MongoDB</title>
    <link rel="stylesheet" href="styles.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>📊 Statistics Dashboard</h1>
            <p>Thống kê và phân tích - HUFLIT Distributed Database</p>
        </div>

        <div class="stats-nav">
            <a href="ui.php">← Về trang chính</a>
            <a href="logs.php">📋 Audit Logs</a>
        </div>

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
                <canvas id="responseTimeChart"></canvas>
            </div>
        </div>

        <div class="last-updated" id="lastUpdated"></div>
    </div>

    <script>
        // Load background color from localStorage
        const savedBgColor = localStorage.getItem('bgColor');
        if (savedBgColor) {
            document.body.style.backgroundColor = savedBgColor;
        }

        let charts = {};

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
                const labels = perfData.avg_response_time_by_endpoint.map(item => item._id);
                const times = perfData.avg_response_time_by_endpoint.map(item => parseFloat(item.avg_time.toFixed(2)));
                createBarChart('responseTimeChart', labels, times, 'Thời gian TB (ms)', 'rgba(239, 68, 68, 0.8)');
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
            const ctx = document.getElementById(canvasId);
            if (!ctx) return;

            // Destroy existing chart if exists
            if (charts[canvasId]) {
                charts[canvasId].destroy();
            }

            charts[canvasId] = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: label,
                        data: data,
                        backgroundColor: color,
                        borderRadius: 10,
                        borderSkipped: false
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
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
                        y: {
                            beginAtZero: true,
                            ticks: {
                                precision: 0,
                                font: {
                                    size: 12
                                }
                            },
                            grid: {
                                color: 'rgba(0, 0, 0, 0.05)'
                            }
                        },
                        x: {
                            ticks: {
                                maxRotation: 45,
                                minRotation: 45,
                                font: {
                                    size: 12
                                }
                            },
                            grid: {
                                display: false
                            }
                        }
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
