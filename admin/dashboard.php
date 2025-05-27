<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once '../config/database.php';

// Check if user is logged in and has admin access
if (!isset($_SESSION['user_id']) || $_SESSION['access_level'] < 1) {
    header('Location: /');
    exit;
}

$pageTitle = 'Admin Dashboard';

// Get database statistics
try {
    $stats = [
        'weapons' => $pdo->query("SELECT COUNT(*) FROM weapon")->fetchColumn(),
        'armor' => $pdo->query("SELECT COUNT(*) FROM armor")->fetchColumn(),
        'items' => $pdo->query("SELECT COUNT(*) FROM etcitem")->fetchColumn(),
        'npcs' => $pdo->query("SELECT COUNT(*) FROM npc")->fetchColumn(),
        'accounts' => $pdo->query("SELECT COUNT(*) FROM accounts")->fetchColumn(),
        'characters' => $pdo->query("SELECT COUNT(*) FROM characters")->fetchColumn(),
        'online_characters' => $pdo->query("SELECT COUNT(*) FROM characters WHERE OnlineStatus = 1")->fetchColumn(),
        'admin_accounts' => $pdo->query("SELECT COUNT(*) FROM accounts WHERE access_level >= 1")->fetchColumn(),
    ];

    // Recent activity
    $recentLogins = $pdo->query("
        SELECT login, lastactive 
        FROM accounts 
        WHERE lastactive IS NOT NULL 
        ORDER BY lastactive DESC 
        LIMIT 5
    ")->fetchAll();

    $topCharacters = $pdo->query("
        SELECT char_name, level, Class 
        FROM characters 
        ORDER BY level DESC 
        LIMIT 5
    ")->fetchAll();

    // System stats
    $serverUptime = "24 hours 15 minutes"; // You can calculate this dynamically
    $avgPlayersOnline = round($stats['online_characters'] * 1.2); // Example calculation

} catch (PDOException $e) {
    $stats = array_fill_keys(['weapons', 'armor', 'items', 'npcs', 'accounts', 'characters', 'online_characters', 'admin_accounts'], 0);
    $recentLogins = [];
    $topCharacters = [];
    $serverUptime = "Unknown";
    $avgPlayersOnline = 0;
}

// Class names mapping
$classNames = [
    0 => 'Prince', 1 => 'Knight', 2 => 'Elf', 3 => 'Wizard', 4 => 'Dark Elf',
    5 => 'Dragon Knight', 6 => 'Illusionist', 7 => 'Warrior', 8 => 'Fencer', 9 => 'Lancer'
];

include '../includes/header.php';
?>

<div style="margin-top: 80px; min-height: calc(100vh - 80px); background-color: var(--background);">
    <div class="container" style="padding: 40px 20px;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 40px;">
            <div>
                <h1 style="font-size: 2.5rem; font-weight: 800; color: var(--text); margin-bottom: 10px;">
                    Admin Dashboard
                </h1>
                <p style="color: rgba(255, 255, 255, 0.7); font-size: 1.1rem;">
                    Welcome back, <?php echo htmlspecialchars($_SESSION['username']); ?>
                </p>
            </div>
            <div style="color: var(--accent); font-size: 3rem;">⚙️</div>
        </div>

        <!-- Statistics Cards -->
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin-bottom: 40px;">
            <div class="feature-card" style="text-align: center; padding: 30px;">
                <div style="font-size: 2rem; margin-bottom: 15px;">⚔️</div>
                <div style="font-size: 2.5rem; font-weight: 800; color: var(--accent); margin-bottom: 5px;">
                    <?php echo number_format($stats['weapons']); ?>
                </div>
                <div style="color: rgba(255, 255, 255, 0.8);">Weapons</div>
                <a href="/admin/categories/weapon/admin_weapon_list.php" style="display: inline-block; margin-top: 15px; padding: 8px 16px; background-color: var(--accent); color: var(--text); text-decoration: none; border-radius: 4px; font-size: 0.9rem;">Manage</a>
            </div>

            <div class="feature-card" style="text-align: center; padding: 30px;">
                <div style="font-size: 2rem; margin-bottom: 15px;">🛡️</div>
                <div style="font-size: 2.5rem; font-weight: 800; color: var(--accent); margin-bottom: 5px;">
                    <?php echo number_format($stats['armor']); ?>
                </div>
                <div style="color: rgba(255, 255, 255, 0.8);">Armor Pieces</div>
                <a href="/admin/categories/armor/admin_armor_list.php" style="display: inline-block; margin-top: 15px; padding: 8px 16px; background-color: var(--accent); color: var(--text); text-decoration: none; border-radius: 4px; font-size: 0.9rem;">Manage</a>
            </div>

            <div class="feature-card" style="text-align: center; padding: 30px;">
                <div style="font-size: 2rem; margin-bottom: 15px;">💎</div>
                <div style="font-size: 2.5rem; font-weight: 800; color: var(--accent); margin-bottom: 5px;">
                    <?php echo number_format($stats['items']); ?>
                </div>
                <div style="color: rgba(255, 255, 255, 0.8);">Items</div>
                <a href="/admin/items/admin_item_list.php" style="display: inline-block; margin-top: 15px; padding: 8px 16px; background-color: var(--accent); color: var(--text); text-decoration: none; border-radius: 4px; font-size: 0.9rem;">Manage</a>
            </div>

            <div class="feature-card" style="text-align: center; padding: 30px;">
                <div style="font-size: 2rem; margin-bottom: 15px;">👹</div>
                <div style="font-size: 2.5rem; font-weight: 800; color: var(--accent); margin-bottom: 5px;">
                    <?php echo number_format($stats['npcs']); ?>
                </div>
                <div style="color: rgba(255, 255, 255, 0.8);">NPCs</div>
                <a href="/admin/npcs/admin_npc_list.php" style="display: inline-block; margin-top: 15px; padding: 8px 16px; background-color: var(--accent); color: var(--text); text-decoration: none; border-radius: 4px; font-size: 0.9rem;">Manage</a>
            </div>
        </div>

        <!-- Server Statistics -->
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-bottom: 40px;">
            <div class="feature-card" style="text-align: center; padding: 25px;">
                <div style="font-size: 1.5rem; margin-bottom: 10px;">👥</div>
                <div style="font-size: 2rem; font-weight: 800; color: var(--accent); margin-bottom: 5px;">
                    <?php echo number_format($stats['accounts']); ?>
                </div>
                <div style="color: rgba(255, 255, 255, 0.8); font-size: 0.9rem;">Total Accounts</div>
            </div>

            <div class="feature-card" style="text-align: center; padding: 25px;">
                <div style="font-size: 1.5rem; margin-bottom: 10px;">🧙‍♂️</div>
                <div style="font-size: 2rem; font-weight: 800; color: var(--accent); margin-bottom: 5px;">
                    <?php echo number_format($stats['characters']); ?>
                </div>
                <div style="color: rgba(255, 255, 255, 0.8); font-size: 0.9rem;">Total Characters</div>
            </div>

            <div class="feature-card" style="text-align: center; padding: 25px;">
                <div style="font-size: 1.5rem; margin-bottom: 10px;">🟢</div>
                <div style="font-size: 2rem; font-weight: 800; color: var(--success); margin-bottom: 5px;">
                    <?php echo number_format($stats['online_characters']); ?>
                </div>
                <div style="color: rgba(255, 255, 255, 0.8); font-size: 0.9rem;">Online Now</div>
            </div>
        </div>

        <!-- Recent Activity & Top Characters -->
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 30px; margin-bottom: 40px;">
            <!-- Recent Logins -->
            <div class="feature-card" style="padding: 30px;">
                <h3 style="color: var(--text); margin-bottom: 20px; font-size: 1.3rem; font-weight: 600;">Recent Logins</h3>
                <div style="max-height: 300px; overflow-y: auto;">
                    <?php if (!empty($recentLogins)): ?>
                        <?php foreach ($recentLogins as $login): ?>
                            <div style="display: flex; justify-content: space-between; align-items: center; padding: 10px 0; border-bottom: 1px solid rgba(255, 255, 255, 0.1);">
                                <div>
                                    <div style="font-weight: 500; color: var(--text);"><?php echo htmlspecialchars($login['login']); ?></div>
                                </div>
                                <div style="color: rgba(255, 255, 255, 0.6); font-size: 0.9rem;">
                                    <?php echo $login['lastactive'] ? date('M j, H:i', strtotime($login['lastactive'])) : 'Never'; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p style="color: rgba(255, 255, 255, 0.6); text-align: center; padding: 20px;">No recent login data</p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Top Characters -->
            <div class="feature-card" style="padding: 30px;">
                <h3 style="color: var(--text); margin-bottom: 20px; font-size: 1.3rem; font-weight: 600;">Top Characters</h3>
                <div style="max-height: 300px; overflow-y: auto;">
                    <?php if (!empty($topCharacters)): ?>
                        <?php foreach ($topCharacters as $index => $char): ?>
                            <div style="display: flex; justify-content: space-between; align-items: center; padding: 10px 0; border-bottom: 1px solid rgba(255, 255, 255, 0.1);">
                                <div style="display: flex; align-items: center; gap: 10px;">
                                    <div style="background-color: var(--accent); color: var(--text); width: 25px; height: 25px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 0.8rem; font-weight: 600;">
                                        <?php echo $index + 1; ?>
                                    </div>
                                    <div>
                                        <div style="font-weight: 500; color: var(--text);"><?php echo htmlspecialchars($char['char_name']); ?></div>
                                        <div style="color: rgba(255, 255, 255, 0.6); font-size: 0.9rem;">
                                            <?php echo $classNames[$char['Class']] ?? 'Unknown'; ?>
                                        </div>
                                    </div>
                                </div>
                                <div style="color: var(--accent); font-weight: 600;">Lv. <?php echo $char['level']; ?></div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p style="color: rgba(255, 255, 255, 0.6); text-align: center; padding: 20px;">No character data</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="feature-card" style="padding: 30px;">
            <h3 style="color: var(--text); margin-bottom: 20px; font-size: 1.3rem; font-weight: 600;">Quick Actions</h3>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px;">
                <a href="/admin/categories/weapon/admin_weapon_list.php" style="display: flex; align-items: center; gap: 10px; padding: 15px; background-color: var(--secondary); color: var(--text); text-decoration: none; border-radius: 6px; transition: all 0.3s ease;" onmouseover="this.style.backgroundColor='rgba(249, 75, 31, 0.1)'" onmouseout="this.style.backgroundColor='var(--secondary)'">
                    <span style="font-size: 1.2rem;">⚔️</span>
                    <span>Manage Weapons</span>
                </a>
                <a href="/admin/categories/armor/admin_armor_list.php" style="display: flex; align-items: center; gap: 10px; padding: 15px; background-color: var(--secondary); color: var(--text); text-decoration: none; border-radius: 6px; transition: all 0.3s ease;" onmouseover="this.style.backgroundColor='rgba(249, 75, 31, 0.1)'" onmouseout="this.style.backgroundColor='var(--secondary)'">
                    <span style="font-size: 1.2rem;">🛡️</span>
                    <span>Manage Armor</span>
                </a>
                <a href="/admin/users.php" style="display: flex; align-items: center; gap: 10px; padding: 15px; background-color: var(--secondary); color: var(--text); text-decoration: none; border-radius: 6px; transition: all 0.3s ease;" onmouseover="this.style.backgroundColor='rgba(249, 75, 31, 0.1)'" onmouseout="this.style.backgroundColor='var(--secondary)'">
                    <span style="font-size: 1.2rem;">👥</span>
                    <span>User Management</span>
                </a>
                <a href="/admin/settings.php" style="display: flex; align-items: center; gap: 10px; padding: 15px; background-color: var(--secondary); color: var(--text); text-decoration: none; border-radius: 6px; transition: all 0.3s ease;" onmouseover="this.style.backgroundColor='rgba(249, 75, 31, 0.1)'" onmouseout="this.style.backgroundColor='var(--secondary)'">
                    <span style="font-size: 1.2rem;">⚙️</span>
                    <span>Settings</span>
                </a>
                <a href="/admin/logs.php" style="display: flex; align-items: center; gap: 10px; padding: 15px; background-color: var(--secondary); color: var(--text); text-decoration: none; border-radius: 6px; transition: all 0.3s ease;" onmouseover="this.style.backgroundColor='rgba(249, 75, 31, 0.1)'" onmouseout="this.style.backgroundColor='var(--secondary)'">
                    <span style="font-size: 1.2rem;">📋</span>
                    <span>View Logs</span>
                </a>


<style>
/* Admin Layout */
.admin-layout {
    display: flex;
    min-height: 100vh;
    background-color: var(--background);
    margin-top: 80px;
}

/* Admin Sidebar */
.admin-sidebar {
    width: 280px;
    background-color: var(--primary);
    border-right: 1px solid rgba(255, 255, 255, 0.1);
    display: flex;
    flex-direction: column;
    position: fixed;
    left: 0;
    top: 80px;
    height: calc(100vh - 80px);
    overflow-y: auto;
    z-index: 100;
}

.admin-brand {
    padding: 30px 20px;
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    display: flex;
    align-items: center;
    gap: 12px;
}

.brand-icon {
    font-size: 2rem;
    color: var(--accent);
}

.brand-title {
    font-size: 1.4rem;
    font-weight: 800;
    color: var(--text);
    line-height: 1;
}

.brand-subtitle {
    font-size: 0.8rem;
    color: rgba(255, 255, 255, 0.6);
    text-transform: uppercase;
    letter-spacing: 1px;
}

/* Admin Navigation */
.admin-nav {
    flex: 1;
    padding: 20px 0;
}

.nav-section {
    margin-bottom: 30px;
}

.nav-title {
    padding: 0 20px 10px;
    font-size: 0.7rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 1px;
    color: rgba(255, 255, 255, 0.5);
}

.nav-item {
    display: flex;
    align-items: center;
    padding: 12px 20px;
    color: rgba(255, 255, 255, 0.8);
    text-decoration: none;
    transition: all 0.2s ease;
    position: relative;
}

.nav-item:hover {
    background-color: rgba(255, 255, 255, 0.05);
    color: var(--text);
}

.nav-item.active {
    background-color: rgba(249, 75, 31, 0.1);
    color: var(--accent);
    border-right: 3px solid var(--accent);
}

.nav-icon {
    font-size: 1.1rem;
    margin-right: 12px;
    width: 20px;
    text-align: center;
}

.nav-text {
    flex: 1;
    font-size: 0.9rem;
    font-weight: 500;
}

.nav-badge {
    background-color: rgba(255, 255, 255, 0.2);
    color: var(--text);
    padding: 2px 8px;
    border-radius: 10px;
    font-size: 0.7rem;
    font-weight: 600;
}

.nav-item.active .nav-badge {
    background-color: var(--accent);
}

/* Admin User Info */
.admin-user-info {
    padding: 20px;
    border-top: 1px solid rgba(255, 255, 255, 0.1);
    display: flex;
    align-items: center;
    gap: 12px;
}

.user-avatar-large {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background-color: var(--accent);
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
    color: var(--text);
}

.user-details {
    flex: 1;
}

.user-name {
    font-size: 0.9rem;
    font-weight: 600;
    color: var(--text);
    line-height: 1;
}

.user-role {
    font-size: 0.7rem;
    color: rgba(255, 255, 255, 0.6);
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.logout-btn {
    color: rgba(255, 255, 255, 0.6);
    font-size: 1.2rem;
    transition: all 0.2s ease;
    text-decoration: none;
}

.logout-btn:hover {
    color: var(--danger);
}

/* Main Content */
.admin-content {
    flex: 1;
    margin-left: 280px;
    background-color: var(--background);
}

/* Admin Header */
.admin-header {
    background-color: var(--primary);
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    padding: 20px 30px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.page-title {
    font-size: 1.8rem;
    font-weight: 700;
    color: var(--text);
    margin: 0 0 5px 0;
}

.breadcrumb {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 0.8rem;
    color: rgba(255, 255, 255, 0.6);
}

.breadcrumb-item.active {
    color: var(--accent);
}

.breadcrumb-separator {
    color: rgba(255, 255, 255, 0.4);
}

.header-stats {
    display: flex;
    gap: 30px;
}

.stat-item {
    text-align: right;
}

.stat-label {
    display: block;
    font-size: 0.7rem;
    color: rgba(255, 255, 255, 0.6);
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-bottom: 2px;
}

.stat-value {
    font-size: 0.9rem;
    font-weight: 600;
    color: var(--text);
}

.stat-value.online {
    color: var(--success);
}

/* Dashboard Grid */
.dashboard-grid {
    padding: 30px;
    display: flex;
    flex-direction: column;
    gap: 30px;
}

/* Overview Cards */
.overview-cards {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
}

.metric-card {
    background-color: var(--primary);
    border-radius: 8px;
    padding: 24px;
    display: flex;
    align-items: center;
    gap: 16px;
    border-left: 4px solid var(--accent);
    transition: all 0.3s ease;
}

.metric-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.2);
}

.metric-card.primary { border-left-color: var(--accent); }
.metric-card.success { border-left-color: var(--success); }
.metric-card.warning { border-left-color: var(--warning); }
.metric-card.info { border-left-color: var(--info); }

.metric-icon {
    font-size: 2.5rem;
    opacity: 0.8;
}

.metric-value {
    font-size: 2rem;
    font-weight: 800;
    color: var(--text);
    line-height: 1;
    margin-bottom: 4px;
}

.metric-label {
    font-size: 0.9rem;
    color: rgba(255, 255, 255, 0.8);
    font-weight: 500;
    margin-bottom: 4px;
}

.metric-trend {
    font-size: 0.7rem;
    color: rgba(255, 255, 255, 0.6);
}

/* Dashboard Section */
.dashboard-section {
    background-color: var(--primary);
    border-radius: 8px;
    overflow: hidden;
}

.section-header {
    padding: 20px 24px;
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.section-title {
    font-size: 1.2rem;
    font-weight: 600;
    color: var(--text);
    margin: 0;
}

.section-actions {
    display: flex;
    gap: 10px;
}

.btn-small {
    padding: 6px 12px;
    background-color: var(--secondary);
    border: none;
    border-radius: 4px;
    color: var(--text);
    font-size: 0.8rem;
    cursor: pointer;
    transition: all 0.2s ease;
}

.btn-small:hover {
    background-color: var(--accent);
}

/* Stats Grid */
.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1px;
    background-color: rgba(255, 255, 255, 0.1);
}

.stat-card {
    background-color: var(--primary);
    padding: 20px;
    display: flex;
    align-items: center;
    gap: 16px;
    transition: all 0.2s ease;
}

.stat-card:hover {
    background-color: rgba(255, 255, 255, 0.02);
}

.stat-icon {
    font-size: 1.5rem;
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 8px;
}

.stat-icon.weapon { background-color: rgba(249, 75, 31, 0.2); }
.stat-icon.armor { background-color: rgba(40, 167, 69, 0.2); }
.stat-icon.items { background-color: rgba(255, 193, 7, 0.2); }
.stat-icon.npcs { background-color: rgba(220, 53, 69, 0.2); }

.stat-info {
    flex: 1;
}

.stat-number {
    font-size: 1.4rem;
    font-weight: 700;
    color: var(--text);
    line-height: 1;
    margin-bottom: 4px;
}

.stat-name {
    font-size: 0.8rem;
    color: rgba(255, 255, 255, 0.7);
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.manage-link {
    color: var(--accent);
    font-size: 0.8rem;
    font-weight: 500;
    text-decoration: none;
    transition: all 0.2s ease;
}

.manage-link:hover {
    color: var(--text);
}

/* Activity Panels */
.activity-panels {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
}

.panel {
    background-color: var(--primary);
    border-radius: 8px;
    overflow: hidden;
}

.panel-header {
    padding: 20px 24px;
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.panel-title {
    font-size: 1rem;
    font-weight: 600;
    color: var(--text);
    margin: 0;
}

.panel-badge {
    background-color: rgba(255, 255, 255, 0.1);
    color: var(--text);
    padding: 4px 8px;
    border-radius: 12px;
    font-size: 0.7rem;
    font-weight: 500;
}

.panel-content {
    padding: 0;
    max-height: 300px;
    overflow-y: auto;
}

.activity-item {
    padding: 16px 24px;
    border-bottom: 1px solid rgba(255, 255, 255, 0.05);
    display: flex;
    align-items: center;
    gap: 12px;
    transition: all 0.2s ease;
}

.activity-item:hover {
    background-color: rgba(255, 255, 255, 0.02);
}

.activity-item:last-child {
    border-bottom: none;
}

.activity-avatar {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    background-color: var(--accent);
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
    font-size: 0.8rem;
    color: var(--text);
}

.activity-rank {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    background-color: var(--secondary);
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
    font-size: 0.8rem;
    color: var(--accent);
}

.activity-info {
    flex: 1;
}

.activity-name {
    font-size: 0.9rem;
    font-weight: 500;
    color: var(--text);
    line-height: 1;
    margin-bottom: 2px;
}

.activity-time {
    font-size: 0.7rem;
    color: rgba(255, 255, 255, 0.6);
}

.activity-status {
    font-size: 0.8rem;
}

.activity-level {
    font-size: 0.8rem;
    font-weight: 600;
    color: var(--accent);
    background-color: rgba(249, 75, 31, 0.1);
    padding: 4px 8px;
    border-radius: 12px;
}

.empty-state {
    padding: 40px 24px;
    text-align: center;
    color: rgba(255, 255, 255, 0.5);
    font-size: 0.9rem;
}

/* Actions Grid */
.actions-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 16px;
    padding: 24px;
}

.action-card {
    background-color: var(--secondary);
    border-radius: 8px;
    padding: 20px;
    display: flex;
    align-items: center;
    gap: 16px;
    text-decoration: none;
    color: var(--text);
    transition: all 0.3s ease;
    border: 1px solid rgba(255, 255, 255, 0.1);
}

.action-card:hover {
    background-color: rgba(249, 75, 31, 0.1);
    border-color: var(--accent);
    transform: translateY(-2px);
}

.action-icon {
    font-size: 1.5rem;
    opacity: 0.8;
}

.action-title {
    font-size: 0.9rem;
    font-weight: 600;
    color: var(--text);
    margin-bottom: 4px;
}

.action-desc {
    font-size: 0.7rem;
    color: rgba(255, 255, 255, 0.6);
}

/* Responsive */
@media (max-width: 1024px) {
    .admin-sidebar {
        width: 240px;
    }
    .admin-content {
        margin-left: 240px;
    }
    .overview-cards {
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    }
    .activity-panels {
        grid-template-columns: 1fr;
    }
}

@media (max-width: 768px) {
    .admin-sidebar {
        transform: translateX(-100%);
        transition: transform 0.3s ease;
    }
    .admin-content {
        margin-left: 0;
    }
    .overview-cards {
        grid-template-columns: 1fr;
    }
    .actions-grid {
        grid-template-columns: 1fr;
    }
    .header-stats {
        display: none;
    }
}
</style>

<?php include '../includes/footer.php'; ?>