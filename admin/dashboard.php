<?php
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
    ];

    // Recent activity
    $recentLogins = $pdo->query("
        SELECT login, lastactive 
        FROM accounts 
        WHERE lastactive IS NOT NULL 
        ORDER BY lastactive DESC 
        LIMIT 10
    ")->fetchAll();

    $topCharacters = $pdo->query("
        SELECT char_name, level, Class 
        FROM characters 
        ORDER BY level DESC 
        LIMIT 10
    ")->fetchAll();

} catch (PDOException $e) {
    $stats = array_fill_keys(['weapons', 'armor', 'items', 'npcs', 'accounts', 'characters', 'online_characters'], 0);
    $recentLogins = [];
    $topCharacters = [];
}

// Class names mapping
$classNames = [
    0 => 'Prince',
    1 => 'Knight', 
    2 => 'Elf',
    3 => 'Wizard',
    4 => 'Dark Elf',
    5 => 'Dragon Knight',
    6 => 'Illusionist',
    7 => 'Warrior',
    8 => 'Fencer',
    9 => 'Lancer'
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
                <a href="/admin/backup.php" style="display: flex; align-items: center; gap: 10px; padding: 15px; background-color: var(--secondary); color: var(--text); text-decoration: none; border-radius: 6px; transition: all 0.3s ease;" onmouseover="this.style.backgroundColor='rgba(249, 75, 31, 0.1)'" onmouseout="this.style.backgroundColor='var(--secondary)'">
                    <span style="font-size: 1.2rem;">💾</span>
                    <span>Database Backup</span>
                </a>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>