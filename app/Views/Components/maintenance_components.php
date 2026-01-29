<?php
/**
 * Maintenance Components
 */

/**
 * Render a site card with data table
 */
function renderSiteCard($siteId, $siteName, $siteClass, $data = []) {
    $siteLabel = strtoupper($siteName);
    ?>
    <div class="site-card <?php echo $siteClass; ?>" id="site-card-<?php echo $siteId; ?>">
        <div class="site-card-header">
            <div style="font-size: 1.1rem; font-weight: 700; color: #f9fafb; letter-spacing: -0.01em;">
                <?php echo htmlspecialchars($siteLabel); ?>
            </div>
            <div class="site-status-indicator"></div>
        </div>
        <div class="site-table-container">
            <div id="site-<?php echo $siteId; ?>-table">
                <?php renderSiteTable($data); ?>
            </div>
        </div>
    </div>
    <?php
}

/**
 * Render table for site data
 */
function renderSiteTable($data) {
    if (empty($data)) {
        echo '<div style="text-align:center; padding: 2rem 1rem; color: var(--slate-500); font-style:italic; background: rgba(255,255,255,0.02); border-radius: var(--radius-md); border: 1px dashed var(--glass-border);">Không có dữ liệu</div>';
        return;
    }

    $headers = array_keys($data[0]);
    ?>
    <table class="site-table">
        <thead>
            <tr>
                <?php foreach ($headers as $header): ?>
                    <th><?php echo htmlspecialchars(ucfirst($header)); ?></th>
                <?php endforeach; ?>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($data as $row): ?>
                <tr>
                    <?php foreach ($headers as $header): ?>
                        <td><?php echo htmlspecialchars($row[$header] ?? ''); ?></td>
                    <?php endforeach; ?>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php
}

/**
 * Render action card
 */
function renderActionCard($title, $description, $buttonText, $buttonClass, $buttonAction, $cardClass = '', $icon = '') {
    if (empty($icon)) {
        $icon = $cardClass === 'border-danger' ? '🧹' : ($cardClass === 'border-success' ? '🌱' : '🏗️');
    }
    ?>
    <div class="card <?php echo $cardClass; ?>">
        <div class="card-body" style="text-align: center;">
            <div style="font-size: 3rem; margin-bottom: 1rem;">
                <?php echo $icon; ?>
            </div>
            <h3 style="font-size: 1.25rem; font-weight: 600; margin-bottom: 0.5rem; color: var(--<?php echo $cardClass === 'border-danger' ? 'danger' : ($cardClass === 'border-success' ? 'success' : 'primary'); ?>);">
                <?php echo htmlspecialchars($title); ?>
            </h3>
            <p style="color: var(--slate-500); margin-bottom: 1.5rem;">
                <?php echo htmlspecialchars($description); ?>
            </p>
            <button class="btn <?php echo $buttonClass; ?> w-100" onclick="<?php echo $buttonAction; ?>">
                <?php echo htmlspecialchars($buttonText); ?>
            </button>
        </div>
    </div>
    <?php
}