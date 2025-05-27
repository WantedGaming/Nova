<?php
require_once 'config/database.php';

$pageTitle = 'Home';

// Get some statistics for the hero section
try {
    $weaponCount = $pdo->query("SELECT COUNT(*) FROM weapon")->fetchColumn();
    $armorCount = $pdo->query("SELECT COUNT(*) FROM armor")->fetchColumn();
    $itemCount = $pdo->query("SELECT COUNT(*) FROM etcitem")->fetchColumn();
    $npcCount = $pdo->query("SELECT COUNT(*) FROM npc")->fetchColumn();
} catch (PDOException $e) {
    $weaponCount = $armorCount = $itemCount = $npcCount = 0;
}

include 'includes/header.php';
?>

<section class="hero">
    <div class="container">
        <div class="hero-content">
            <h1 class="hero-title">L1J-R <span>Database</span></h1>
            <p class="hero-description">
                Your ultimate resource for Lineage 1 weapons, armor, items, and monsters with detailed stats and locations.
            </p>
        </div>
    </div>
</section>

<section class="stats-section">
    <div class="container">
        <div class="stats-grid">
            <div class="stat-item">
                <span class="stat-number"><?php echo number_format($weaponCount); ?></span>
                <span class="stat-label">Weapons</span>
            </div>
            <div class="stat-item">
                <span class="stat-number"><?php echo number_format($armorCount); ?></span>
                <span class="stat-label">Armor Pieces</span>
            </div>
            <div class="stat-item">
                <span class="stat-number"><?php echo number_format($itemCount); ?></span>
                <span class="stat-label">Items</span>
            </div>
            <div class="stat-item">
                <span class="stat-number"><?php echo number_format($npcCount); ?></span>
                <span class="stat-label">NPCs & Monsters</span>
            </div>
        </div>
    </div>
</section>

<section class="features" id="categories">
    <div class="container">
        <h2 class="section-title">Database Categories</h2>
        <p class="section-subtitle">
            Browse through our comprehensive database of game content, featuring detailed information about weapons, armor, items, and more.
        </p>
        
        <div class="features-grid">
            <a href="public/weapon/weapon_list.php" class="feature-card">
                <h3 class="feature-title">Weapons</h3>
                <div class="feature-image">
                    <img src="assets/img/placeholders/weapons.png" alt="Weapons" />
                </div>
                <p class="feature-description">
                    Discover all weapons available in L1J, from basic swords to legendary artifacts.
                </p>
            </a>
            
            <a href="public/armor/armor_list.php" class="feature-card">
                <h3 class="feature-title">Armor</h3>
                <div class="feature-image">
                    <img src="assets/img/placeholders/armor.png" alt="Armor" />
                </div>
                <p class="feature-description">
                    Complete armor database including helmets, body armor, shields, and accessories.
                </p>
            </a>
            
            <a href="public/items/item_list.php" class="feature-card">
                <h3 class="feature-title">Items</h3>
                <div class="feature-image">
                    <img src="assets/img/placeholders/items.png" alt="Items" />
                </div>
                <p class="feature-description">
                    Explore consumables, crafting materials, quest items, and other miscellaneous items.
                </p>
            </a>
            
            <a href="public/dolls/doll_list.php" class="feature-card">
                <h3 class="feature-title">Magic Dolls</h3>
                <div class="feature-image">
                    <img src="assets/img/placeholders/dolls.png" alt="Magic Dolls" />
                </div>
                <p class="feature-description">
                    Discover magical companions and their abilities with summoning requirements.
                </p>
            </a>
            
            <a href="public/maps/map_list.php" class="feature-card">
                <h3 class="feature-title">Maps</h3>
                <div class="feature-image">
                    <img src="assets/img/placeholders/maps.png" alt="Maps" />
                </div>
                <p class="feature-description">
                    Navigate through all game zones and dungeons with teleport locations.
                </p>
            </a>
            
            <a href="public/monsters/monster_list.php" class="feature-card">
                <h3 class="feature-title">Monsters</h3>
                <div class="feature-image">
                    <img src="assets/img/placeholders/monsters.png" alt="Monsters" />
                </div>
                <p class="feature-description">
                    Complete bestiary with monster stats, locations, and drop information.
                </p>
            </a>
        </div>
    </div>
</section>

<section class="testimonials">
    <div class="container">
        <h2 class="section-title">What Players Say</h2>
        <p class="section-subtitle">
            Join thousands of players who trust our database for their L1J adventures.
        </p>
        
        <div class="testimonials-grid">
            <div class="testimonial-card">
                <div class="testimonial-content">
                    This database is incredibly comprehensive! I can find everything I need to optimize my character build and plan my hunting routes.
                </div>
                <div class="testimonial-author">
                    <div class="author-avatar">A</div>
                    <div>
                        <div class="author-name">ArchMage_Alex</div>
                        <div class="author-position">Level 80 Wizard</div>
                    </div>
                </div>
            </div>
            
            <div class="testimonial-card">
                <div class="testimonial-content">
                    The weapon and armor sections are fantastic. Easy to compare stats and find the perfect equipment for my class.
                </div>
                <div class="testimonial-author">
                    <div class="author-avatar">K</div>
                    <div>
                        <div class="author-name">KnightMaster</div>
                        <div class="author-position">Level 75 Knight</div>
                    </div>
                </div>
            </div>
            
            <div class="testimonial-card">
                <div class="testimonial-content">
                    Love the monster database! Perfect for finding the best hunting spots and drop rates. Saved me hours of research.
                </div>
                <div class="testimonial-author">
                    <div class="author-avatar">S</div>
                    <div>
                        <div class="author-name">ShadowHunter</div>
                        <div class="author-position">Level 85 Dark Elf</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>