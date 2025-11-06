# Upload Configuration for Multiple Environments

## Overview
This system automatically detects whether you're running on **local XAMPP** or your **school's server** and adjusts the upload paths accordingly.

## How It Works

### Local Environment (XAMPP)
- **File System Path**: `/Applications/XAMPP/xamppfiles/htdocs/25662026_Lab_02_Register_Login/uploads/`
- **Web Path**: `uploads/` (relative)
- **Database Stores**: `uploads/u1/p5/image123.jpg`
- **HTML Displays**: `<img src="../uploads/u1/p5/image123.jpg">`

### School Server Environment
- **File System Path**: `/home/username/uploads/` (outside public_html)
- **Web Path**: `/uploads/` (absolute from root)
- **Database Stores**: `/uploads/u1/p5/image123.jpg`
- **HTML Displays**: `<img src="/uploads/u1/p5/image123.jpg">`

## File Structure

### On School Server:
```
/home/username/
├── public_html/
│   ├── actions/
│   ├── admin/
│   ├── classes/
│   ├── controllers/
│   ├── settings/
│   │   └── upload_config.php    ← Detects environment
│   ├── view/
│   └── ... (all your PHP files)
└── uploads/                      ← Shared uploads folder (OUTSIDE public_html)
    ├── u1/                       ← User ID folders
    │   ├── p5/                   ← Product ID folders
    │   │   └── image123.jpg
    │   └── b2/                   ← Brand ID folders
    │       └── logo456.jpg
    └── BS_3.png                  ← Your test image
```

## Setup on School Server

### 1. Upload Files
Upload your entire `public_html` directory via SSH/SFTP.

### 2. Set Permissions
```bash
# Make uploads directory writable
chmod 755 ~/uploads
chmod 755 ~/public_html

# Make sure PHP can write to uploads
chmod -R 775 ~/uploads
```

### 3. Test Detection
Visit: `http://yourserver/public_html/test_update_images.php`

This will show:
- Upload Base Path (where files are stored)
- Upload Web Path (how they're accessed in HTML)
- Current Directory

## Modified Files

### Core Files:
1. **`settings/upload_config.php`** - NEW: Auto-detects environment
2. **`classes/image_helper.php`** - UPDATED: Uses upload config
3. **`view/all_product.php`** - UPDATED: Handles both path types
4. **`view/single_product.php`** - UPDATED: Handles both path types
5. **`test_update_images.php`** - UPDATED: Uses upload config

## How Images Are Displayed

The view files now check if the path starts with `/`:
- If YES → It's an absolute path (school server): `<img src="/uploads/...">`
- If NO → It's relative (local): `<img src="../uploads/...">`

## Testing

### Local (XAMPP):
1. Upload image via admin panel
2. Database stores: `uploads/u1/p5/image.jpg`
3. Browser loads: `http://localhost/.../uploads/u1/p5/image.jpg`

### School Server:
1. Upload image via admin panel
2. Database stores: `/uploads/u1/p5/image.jpg`
3. Browser loads: `http://school.edu/uploads/u1/p5/image.jpg`

## Troubleshooting

### Images not showing on school server:
```bash
# Check uploads directory exists and is accessible
ls -la ~/uploads

# Check web server can access it
# Add to .htaccess or Apache config if needed
```

### Permission errors:
```bash
# Fix ownership (replace username)
chown -R username:username ~/uploads

# Fix permissions
chmod -R 755 ~/uploads
```

### Path detection not working:
The system detects school server by checking if the path contains `public_html`.
If your server uses a different structure, edit `settings/upload_config.php`:
```php
// Change this line to match your server's structure
if (strpos(__DIR__, 'public_html') !== false && is_dir($uploadsOutside)) {
```

## Benefits
✅ No code changes needed when deploying  
✅ Works on both local and remote servers  
✅ Single codebase for all environments  
✅ Automatic path detection  
