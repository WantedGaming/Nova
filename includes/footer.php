    <footer>
        <div class="container">
            <div class="footer-grid">
                <div class="footer-col">
                    <h4>L1J Remastered</h4>
                    <p>Experience the classic Lineage 1 with modern enhancements. Join our community and embark on epic adventures in a world of magic and mystery.</p>
                    <div class="social-links">
                        <a href="#" class="social-link">📘</a>
                        <a href="#" class="social-link">🐦</a>
                        <a href="#" class="social-link">💬</a>
                        <a href="#" class="social-link">🎮</a>
                    </div>
                </div>
                
                <div class="footer-col">
                    <h4>Quick Links</h4>
                    <ul class="footer-links">
                        <li><a href="<?php echo $baseUrl; ?>">Home</a></li>
                        <li><a href="<?php echo $baseUrl; ?>download.php">Download</a></li>
                        <li><a href="<?php echo $baseUrl; ?>guides.php">Game Guides</a></li>
                        <li><a href="<?php echo $baseUrl; ?>events.php">Events</a></li>
                        <li><a href="<?php echo $baseUrl; ?>rankings.php">Rankings</a></li>
                    </ul>
                </div>
                
                <div class="footer-col">
                    <h4>Database</h4>
                    <ul class="footer-links">
                        <li><a href="<?php echo $baseUrl; ?>public/weapon/weapon_list.php">Weapons</a></li>
                        <li><a href="<?php echo $baseUrl; ?>public/armor/armor_list.php">Armor</a></li>
                        <li><a href="<?php echo $baseUrl; ?>public/items/item_list.php">Items</a></li>
                        <li><a href="<?php echo $baseUrl; ?>public/dolls/doll_list.php">Magic Dolls</a></li>
                        <li><a href="<?php echo $baseUrl; ?>public/maps/map_list.php">Maps</a></li>
                        <li><a href="<?php echo $baseUrl; ?>public/monsters/monster_list.php">Monsters</a></li>
                    </ul>
                </div>
                
                <div class="footer-col">
                    <h4>Newsletter</h4>
                    <p>Stay updated with the latest news and updates from L1J Remastered.</p>
                    <form class="newsletter-form" onsubmit="handleNewsletter(event)">
                        <input type="email" class="newsletter-input" placeholder="Enter your email" required>
                        <button type="submit" class="newsletter-button">Subscribe</button>
                    </form>
                </div>
            </div>
            
            <div class="copyright">
                <p>&copy; 2024 L1J Remastered. All rights reserved. | <a href="<?php echo $baseUrl; ?>privacy.php">Privacy Policy</a> | <a href="<?php echo $baseUrl; ?>terms.php">Terms of Service</a></p>
            </div>
        </div>
    </footer>

    <script>
        function handleNewsletter(event) {
            event.preventDefault();
            const email = event.target.querySelector('input').value;
            alert('Thank you for subscribing! We\'ll keep you updated.');
            event.target.reset();
        }
    </script>
</body>
</html>