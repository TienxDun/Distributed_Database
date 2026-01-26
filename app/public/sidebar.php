<?php
/**
 * Shared Sidebar Component
 * @param string $page - Current page ('ui', 'logs', 'stats')
 */

function renderSidebar($page)
{
    $headers = [
        'ui' => ['title' => 'HUFLIT', 'subtitle' => 'Distributed DB'],
        'logs' => ['title' => '📋 Logs', 'subtitle' => 'Audit System'],
        'stats' => ['title' => '📊 Stats', 'subtitle' => 'Analytics Dashboard'],
        'maintenance' => ['title' => '⚙️ Admin', 'subtitle' => 'System Control']
    ];

    $navLinks = [
        'ui' => [
            ['href' => 'logs.php', 'icon' => '📋', 'text' => 'Audit Logs'],
            ['href' => 'stats.php', 'icon' => '📊', 'text' => 'Statistics'],
            ['href' => 'maintenance.php', 'icon' => '⚙️', 'text' => 'Quản trị']
        ],
        'logs' => [
            ['href' => 'ui.php', 'icon' => '🏠', 'text' => 'Home'],
            ['href' => 'stats.php', 'icon' => '📊', 'text' => 'Statistics'],
            ['href' => 'maintenance.php', 'icon' => '⚙️', 'text' => 'Quản trị']
        ],
        'stats' => [
            ['href' => 'ui.php', 'icon' => '🏠', 'text' => 'Home'],
            ['href' => 'logs.php', 'icon' => '📋', 'text' => 'Audit Logs'],
            ['href' => 'maintenance.php', 'icon' => '⚙️', 'text' => 'Quản trị']
        ],
        'maintenance' => [
            ['href' => 'ui.php', 'icon' => '🏠', 'text' => 'Home'],
            ['href' => 'logs.php', 'icon' => '📋', 'text' => 'Audit Logs'],
            ['href' => 'stats.php', 'icon' => '📊', 'text' => 'Statistics']
        ]
    ];

    $header = $headers[$page];
    $links = $navLinks[$page];
    ?>
    <nav class="sidebar">
        <div class="sidebar-header">
            <h2><?php echo $header['title']; ?></h2>
            <p><?php echo $header['subtitle']; ?></p>
        </div>

        <div class="sidebar-section">
            <h3 class="sidebar-section-title">📊 Navigation</h3>
            <ul class="sidebar-nav">
                <?php foreach ($links as $link): ?>
                    <li><a href="<?php echo $link['href']; ?>" class="sidebar-link">
                            <span class="sidebar-icon"><?php echo $link['icon']; ?></span>
                            <span class="sidebar-text"><?php echo $link['text']; ?></span>
                        </a></li>
                <?php endforeach; ?>
            </ul>
        </div>

        <div class="sidebar-section">
            <h3 class="sidebar-section-title">🗺️ Data Sites</h3>
            <div class="site-toggle-container">
                <label class="site-toggle-label" for="toggleSiteColumn">
                    <input type="checkbox" id="toggleSiteColumn" checked onchange="toggleSiteColumnVisibility()"
                        class="site-toggle-checkbox">
                    <div class="site-toggle-slider">
                        <span class="site-toggle-icon">🗺️</span>
                    </div>
                    <span class="site-toggle-text">
                        <div class="site-toggle-main-text">Show Site Column</div>
                        <div class="site-toggle-sub-text">Distributed data</div>
                    </span>
                </label>
            </div>
        </div>

        <div class="sidebar-section">
            <h3 class="sidebar-section-title">🎨 Theme</h3>
            <div class="theme-selector">
                <input type="radio" id="theme-blue" name="theme" value="blue">
                <label for="theme-blue" class="theme-option" data-theme="blue" title="Xanh đại dương"></label>

                <input type="radio" id="theme-pink" name="theme" value="pink">
                <label for="theme-pink" class="theme-option" data-theme="pink" title="Hồng đào"></label>

                <input type="radio" id="theme-green" name="theme" value="green" checked>
                <label for="theme-green" class="theme-option" data-theme="green" title="Xanh rừng"></label>
            </div>
        </div>
    </nav>
    <?php
}
?>