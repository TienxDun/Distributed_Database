// Stats page JavaScript

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

// Removed: window.toggleAutoRefresh = toggleAutoRefresh;
// Removed: window.updateAutoRefreshTime = updateAutoRefreshTime;

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
    // Removed: loadAutoRefreshSettings();

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
        <div class="stats-grid">
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