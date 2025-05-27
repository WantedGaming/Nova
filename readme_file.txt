# L1J Remastered Database Website

A modern PHP-based website for browsing and managing Lineage 1 game database content.

## Features

- **Modern Dark Theme**: Clean, responsive design with smooth animations
- **Comprehensive Database**: Browse weapons, armor, items, magic dolls, maps, and monsters
- **Admin Panel**: Full administrative interface for content management
- **User Authentication**: Secure login system with role-based access
- **Responsive Design**: Works perfectly on desktop, tablet, and mobile devices
- **Search & Filter**: Advanced filtering and search capabilities
- **Pagination**: Efficient data browsing with pagination
- **Export Features**: CSV export functionality for data backup

## Technology Stack

- **Backend**: PHP 8+ with PDO
- **Database**: MySQL/MariaDB (l1j_remastered schema)
- **Frontend**: HTML5, CSS3, Vanilla JavaScript
- **Styling**: Custom CSS with CSS Grid and Flexbox
- **Authentication**: Session-based with access level controls

## File Structure

```
/root
├── config/
│   └── database.php              # Database configuration
├── includes/
│   ├── header.php               # Global header with navigation
│   └── footer.php               # Global footer
├── assets/
│   ├── css/
│   │   └── main.css             # Main stylesheet
│   ├── img/                     # Images and assets
│   └── js/                      # JavaScript files
├── auth/
│   └── login.php                # AJAX login handler
├── admin/
│   ├── dashboard.php            # Admin dashboard
│   ├── functions/
│   │   └── admin_functions.php  # Admin utility functions
│   └── categories/
│       ├── weapon/
│       │   ├── admin_weapon_list.php
│       │   └── admin_weapon_detail.php
│       └── armor/
│           ├── admin_armor_list.php
│           └── admin_armor_detail.php
├── public/
│   ├── weapon/
│   │   ├── weapon_list.php
│   │   └── weapon_detail.php
│   └── armor/
│       ├── armor_list.php
│       └── armor_detail.php
├── index.php                    # Homepage
└── logout.php                   # Logout handler
```

## Installation

1. **Database Setup**:
   - Import the provided SQL schema (`all_tables.sql`)
   - Create database named `l1j_remastered`

2. **Configuration**:
   - Update database credentials in `config/database.php`
   - Ensure web server has read/write permissions

3. **Web Server**:
   - Configure Apache/Nginx to serve from root directory
   - Enable PHP 8+ with PDO MySQL extension
   - Set document root to the project directory

4. **Admin Access**:
   - Create admin user in `accounts` table with `access_level = 1`
   - Login through the website interface

## Key Features

### Homepage
- Hero section with animated background
- Database statistics display
- Category grid with direct links
- Responsive testimonials section

### Admin Dashboard
- Real-time server statistics
- Recent login activity
- Top character rankings
- Quick action buttons
- Comprehensive admin tools

### Navigation
- Dropdown menus for easy access
- User authentication status
- Mobile-responsive design
- Breadcrumb navigation in admin

### Security
- Session-based authentication
- Access level verification
- SQL injection prevention with PDO
- Input sanitization
- Admin activity logging

## Database Schema

The website uses the standard L1J database schema with the following main tables:

- `weapon` - Weapon items and statistics
- `armor` - Armor pieces and sets
- `etcitem` - Miscellaneous items
- `npc` - NPCs and monsters
- `accounts` - User accounts with access levels
- `characters` - Character data
- `admin_activity` - Admin action logging

## Customization

### Styling
- All styles are in `assets/css/main.css`
- CSS custom properties for easy theme customization
- No inline styles for maintainability

### Colors
```css
:root {
    --text: #ffffff;
    --background: #030303;
    --primary: #080808;
    --secondary: #0a0a0a;
    --accent: #f94b1f;
    --success: #28a745;
    --warning: #ffc107;
    --danger: #dc3545;
}
```

### Adding New Categories
1. Create new directory in `public/` and `admin/categories/`
2. Add navigation links in `includes/header.php`
3. Create list and detail pages following existing patterns
4. Update admin dashboard with new statistics

## Configuration Options

### Database Settings
```php
// config/database.php
define('DB_HOST', 'localhost');
define('DB_NAME', 'l1j_remastered');
define('DB_USER', 'your_username');
define('DB_PASS', 'your_password');
```

### Admin Access Levels
- `0` - Regular user
- `1` - Admin (full access)
- `2` - Super admin (if needed for future features)

## API Endpoints

### Authentication
- `POST /auth/login.php` - User login
- `GET /logout.php` - User logout

### Admin Functions
- All admin functions are server-side rendered
- CSRF protection recommended for production
- Activity logging for audit trails

## Performance Optimization

### Database
- Indexed primary keys and foreign keys
- Pagination to limit query results
- Prepared statements for security and performance

### Frontend
- CSS animations for smooth user experience
- Lazy loading for large datasets
- Responsive images and assets

### Caching
- Consider implementing Redis/Memcached for production
- Browser caching for static assets
- Database query caching for frequently accessed data

## Security Considerations

### Production Deployment
1. **Environment Variables**: Move database credentials to environment variables
2. **HTTPS**: Enable SSL/TLS encryption
3. **Password Hashing**: Implement proper password hashing (bcrypt/Argon2)
4. **CSRF Protection**: Add CSRF tokens to forms
5. **Rate Limiting**: Implement login attempt limiting
6. **Input Validation**: Server-side validation for all inputs
7. **Error Handling**: Don't expose sensitive error information

### Current Security Features
- PDO prepared statements prevent SQL injection
- Session-based authentication
- Access level verification
- Input sanitization
- Admin activity logging

## Browser Support

- **Modern Browsers**: Chrome 80+, Firefox 75+, Safari 13+, Edge 80+
- **CSS Features**: Grid, Flexbox, Custom Properties
- **JavaScript**: ES6+ features used
- **Mobile**: Responsive design for all screen sizes

## Development Guidelines

### Code Style
- PHP: Follow PSR-4 autoloading standards
- CSS: Use BEM methodology for class naming
- JavaScript: ES6+ with proper error handling
- HTML: Semantic markup with accessibility considerations

### File Organization
- Separate concerns (logic, presentation, data)
- Reusable components in includes/
- Configuration in dedicated config files
- Assets organized by type

## Troubleshooting

### Common Issues

1. **Database Connection Errors**
   - Check database credentials in config/database.php
   - Verify MySQL service is running
   - Ensure database exists and user has permissions

2. **Login Issues**
   - Verify account exists in accounts table
   - Check access_level value (must be >= 1 for admin)
   - Clear browser cookies/session data

3. **Permission Errors**
   - Check file permissions on web directory
   - Ensure web server can read/write session files
   - Verify database user has SELECT/INSERT/UPDATE permissions

4. **Styling Issues**
   - Check CSS file path in includes/header.php
   - Verify CSS file exists and is readable
   - Clear browser cache

### Debug Mode
Enable PHP error reporting for development:
```php
// Add to top of any PHP file for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
```

## Future Enhancements

### Planned Features
- [ ] Advanced search with filters
- [ ] Character equipment simulator
- [ ] Drop rate calculator
- [ ] Interactive maps
- [ ] User favorites system
- [ ] API endpoints for mobile app
- [ ] Real-time server status
- [ ] Multi-language support

### Technical Improvements
- [ ] Implement caching layer
- [ ] Add automated testing
- [ ] API rate limiting
- [ ] Enhanced security measures
- [ ] Performance monitoring
- [ ] Automated backups

## Contributing

1. Fork the repository
2. Create feature branch (`git checkout -b feature/amazing-feature`)
3. Commit changes (`git commit -m 'Add amazing feature'`)
4. Push to branch (`git push origin feature/amazing-feature`)
5. Open Pull Request

## License

This project is licensed under the MIT License - see the LICENSE file for details.

## Support

For support and questions:
- Create an issue in the repository
- Check existing documentation
- Review troubleshooting section

## Acknowledgments

- Based on Lineage 1 game database structure
- Inspired by modern web design principles
- Built with accessibility and performance in mind