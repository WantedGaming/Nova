<?php
require_once '../../config/database.php';

// Check if user is admin
function checkAdminAccess() {
    if (!isset($_SESSION['user_id']) || $_SESSION['access_level'] < 1) {
        header('Location: /');
        exit;
    }
}

// Log admin activity
function logAdminActivity($admin_username, $activity_type, $description, $entity_type = null, $entity_id = null) {
    global $pdo;
    
    try {
        $stmt = $pdo->prepare("
            INSERT INTO admin_activity 
            (admin_username, activity_type, description, entity_type, entity_id, ip_address, user_agent, timestamp) 
            VALUES (?, ?, ?, ?, ?, ?, ?, NOW())
        ");
        
        $stmt->execute([
            $admin_username,
            $activity_type,
            $description,
            $entity_type,
            $entity_id,
            $_SERVER['REMOTE_ADDR'] ?? 'unknown',
            $_SERVER['HTTP_USER_AGENT'] ?? 'unknown'
        ]);
    } catch (PDOException $e) {
        error_log("Failed to log admin activity: " . $e->getMessage());
    }
}

// Get paginated results
function getPaginatedResults($query, $params = [], $page = 1, $perPage = 20) {
    global $pdo;
    
    $offset = ($page - 1) * $perPage;
    
    // Get total count
    $countQuery = "SELECT COUNT(*) FROM ($query) as count_table";
    $countStmt = $pdo->prepare($countQuery);
    $countStmt->execute($params);
    $totalItems = $countStmt->fetchColumn();
    
    // Get paginated results
    $paginatedQuery = $query . " LIMIT $perPage OFFSET $offset";
    $stmt = $pdo->prepare($paginatedQuery);
    $stmt->execute($params);
    $results = $stmt->fetchAll();
    
    return [
        'results' => $results,
        'total' => $totalItems,
        'page' => $page,
        'perPage' => $perPage,
        'totalPages' => ceil($totalItems / $perPage)
    ];
}

// Generate pagination HTML
function generatePagination($currentPage, $totalPages, $baseUrl) {
    if ($totalPages <= 1) return '';
    
    $html = '<div style="display: flex; justify-content: center; align-items: center; gap: 10px; margin: 30px 0;">';
    
    // Previous button
    if ($currentPage > 1) {
        $prevPage = $currentPage - 1;
        $html .= "<a href='{$baseUrl}?page={$prevPage}' style='padding: 8px 12px; background-color: var(--secondary); color: var(--text); text-decoration: none; border-radius: 4px; transition: all 0.3s ease;' onmouseover='this.style.backgroundColor=\"var(--accent)\"' onmouseout='this.style.backgroundColor=\"var(--secondary)\"'>← Previous</a>";
    }
    
    // Page numbers
    $start = max(1, $currentPage - 2);
    $end = min($totalPages, $currentPage + 2);
    
    if ($start > 1) {
        $html .= "<a href='{$baseUrl}?page=1' style='padding: 8px 12px; background-color: var(--secondary); color: var(--text); text-decoration: none; border-radius: 4px;'>1</a>";
        if ($start > 2) {
            $html .= "<span style='color: var(--text);'>...</span>";
        }
    }
    
    for ($i = $start; $i <= $end; $i++) {
        $activeStyle = $i == $currentPage ? 'background-color: var(--accent);' : 'background-color: var(--secondary);';
        $html .= "<a href='{$baseUrl}?page={$i}' style='padding: 8px 12px; {$activeStyle} color: var(--text); text-decoration: none; border-radius: 4px;'>{$i}</a>";
    }
    
    if ($end < $totalPages) {
        if ($end < $totalPages - 1) {
            $html .= "<span style='color: var(--text);'>...</span>";
        }
        $html .= "<a href='{$baseUrl}?page={$totalPages}' style='padding: 8px 12px; background-color: var(--secondary); color: var(--text); text-decoration: none; border-radius: 4px;'>{$totalPages}</a>";
    }
    
    // Next button
    if ($currentPage < $totalPages) {
        $nextPage = $currentPage + 1;
        $html .= "<a href='{$baseUrl}?page={$nextPage}' style='padding: 8px 12px; background-color: var(--secondary); color: var(--text); text-decoration: none; border-radius: 4px; transition: all 0.3s ease;' onmouseover='this.style.backgroundColor=\"var(--accent)\"' onmouseout='this.style.backgroundColor=\"var(--secondary)\"'>Next →</a>";
    }
    
    $html .= '</div>';
    return $html;
}

// Sanitize input
function sanitizeInput($input) {
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

// Generate admin breadcrumb
function generateBreadcrumb($items) {
    $html = '<nav style="margin-bottom: 30px;">';
    $html .= '<div style="display: flex; align-items: center; gap: 10px; color: rgba(255, 255, 255, 0.7);">';
    
    foreach ($items as $index => $item) {
        if ($index > 0) {
            $html .= '<span>›</span>';
        }
        
        if (isset($item['url'])) {
            $html .= "<a href='{$item['url']}' style='color: var(--accent); text-decoration: none;'>{$item['title']}</a>";
        } else {
            $html .= "<span style='color: var(--text);'>{$item['title']}</span>";
        }
    }
    
    $html .= '</div>';
    $html .= '</nav>';
    return $html;
}

// Format date for display
function formatDate($date) {
    return $date ? date('M j, Y H:i', strtotime($date)) : 'Never';
}

// Get item grade color
function getGradeColor($grade) {
    switch (strtoupper($grade)) {
        case 'ONLY': return '#ff6b35';
        case 'MYTH': return '#9d4edd';
        case 'LEGEND': return '#f72585';
        case 'HERO': return '#4cc9f0';
        case 'RARE': return '#7209b7';
        case 'ADVANC': return '#560bad';
        case 'NORMAL': 
        default: return '#ffffff';
    }
}

// Generate status badge
function generateStatusBadge($status, $activeText = 'Active', $inactiveText = 'Inactive') {
    $color = $status ? 'var(--success)' : 'var(--danger)';
    $text = $status ? $activeText : $inactiveText;
    return "<span style='padding: 4px 8px; background-color: {$color}; color: white; border-radius: 4px; font-size: 0.8rem;'>{$text}</span>";
}

// Export data to CSV
function exportToCSV($data, $filename, $headers = []) {
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=' . $filename);
    
    $output = fopen('php://output', 'w');
    
    // Add BOM for UTF-8
    fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
    
    // Add headers if provided
    if (!empty($headers)) {
        fputcsv($output, $headers);
    } elseif (!empty($data)) {
        // Use first row keys as headers
        fputcsv($output, array_keys($data[0]));
    }
    
    // Add data rows
    foreach ($data as $row) {
        fputcsv($output, $row);
    }
    
    fclose($output);
    exit;
}

// Backup database table
function backupTable($tableName) {
    global $pdo;
    
    try {
        $stmt = $pdo->query("SELECT * FROM `{$tableName}`");
        $data = $stmt->fetchAll();
        
        $filename = $tableName . '_backup_' . date('Y-m-d_H-i-s') . '.csv';
        exportToCSV($data, $filename);
        
    } catch (PDOException $e) {
        throw new Exception("Failed to backup table: " . $e->getMessage());
    }
}

// Class name mappings
function getClassName($classId) {
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
    
    return $classNames[$classId] ?? 'Unknown';
}

// Material type mappings
function getMaterialName($material) {
    $materials = [
        'NONE(-)' => 'None',
        'LIQUID(액체)' => 'Liquid',
        'WAX(밀랍)' => 'Wax',
        'VEGGY(식물성)' => 'Vegetable',
        'FLESH(동물성)' => 'Flesh',
        'PAPER(종이)' => 'Paper',
        'CLOTH(천)' => 'Cloth',
        'LEATHER(가죽)' => 'Leather',
        'WOOD(나무)' => 'Wood',
        'BONE(뼈)' => 'Bone',
        'DRAGON_HIDE(용비늘)' => 'Dragon Hide',
        'IRON(철)' => 'Iron',
        'METAL(금속)' => 'Metal',
        'COPPER(구리)' => 'Copper',
        'SILVER(은)' => 'Silver',
        'GOLD(금)' => 'Gold',
        'PLATINUM(백금)' => 'Platinum',
        'MITHRIL(미스릴)' => 'Mithril',
        'PLASTIC(블랙미스릴)' => 'Black Mithril',
        'GLASS(유리)' => 'Glass',
        'GEMSTONE(보석)' => 'Gemstone',
        'MINERAL(광석)' => 'Mineral',
        'ORIHARUKON(오리하루콘)' => 'Oriharukon',
        'DRANIUM(드라니움)' => 'Dranium'
    ];
    
    return $materials[$material] ?? $material;
}
?>