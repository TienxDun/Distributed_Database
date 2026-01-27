<?php
/**
 * Shared Sidebar Component
 * Updated for Modern UI Redesign
 */

function renderSidebar($activePage = 'ui')
{
    $navItems = [
        ['href' => 'ui.php', 'icon' => '🏠', 'text' => 'Trang chủ'],
        ['href' => 'logs.php', 'icon' => '📋', 'text' => 'Logs Hệ thống'],
        ['href' => 'stats.php', 'icon' => '📊', 'text' => 'Thống kê'],
        ['href' => 'maintenance.php', 'icon' => '⚙️', 'text' => 'Quản trị Admin'],
    ];

    ?>
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <div class="brand">
                <span style="font-size: 1.5rem;">🎓</span>
                <span>HUFLIT DB</span>
            </div>
        </div>

        <div class="sidebar-content">
            <div class="nav-section-title">Main Navigation</div>
            <nav class="nav-list">
                <?php foreach ($navItems as $item): ?>
                    <a href="<?php echo htmlspecialchars($item['href']); ?>"
                       class="nav-item <?php echo basename($item['href'], '.php') === $activePage ? 'active' : ''; ?>">
                        <span class="nav-icon"><?php echo $item['icon']; ?></span>
                        <span class="nav-text"><?php echo htmlspecialchars($item['text']); ?></span>
                    </a>
                <?php endforeach; ?>
            </nav>

            <div class="nav-section-title">System Info</div>
            <div style="padding: 0.5rem 0.75rem; font-size: 0.75rem; color: var(--slate-500);">
                <p>Distributed Database</p>
                <p>Version 2.0.0</p>
                <p><?php echo date('Y-m-d'); ?></p>
            </div>
        </div>

        <div class="sidebar-footer">
            <div class="user-profile">
                <div class="user-avatar">A</div>
                <div class="user-info">
                    <div class="user-name">Admin User</div>
                    <div class="user-role">System Admin</div>
                </div>
            </div>
        </div>
    </aside>
    <?php
}
?>