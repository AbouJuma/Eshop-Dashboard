<?php
require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== LIVE SERVER IMAGE 404 DIAGNOSIS ===\n\n";

echo "Common causes and solutions for live server image 404 errors:\n\n";

echo "1. 🔍 WEB SERVER CONFIGURATION:\n";
echo "   - Apache: Check .htaccess in public directory\n";
echo "   - Nginx: Check server block configuration\n";
echo "   - Ensure uploads directory is accessible\n\n";

echo "2. 📁 FILE PERMISSIONS:\n";
echo "   - Check public/uploads/ directory permissions\n";
echo "   - Should be 755 for directories, 644 for files\n";
echo "   - Web server user (www-data, apache, nginx) needs read access\n\n";

echo "3. 🔗 SYMBOLIC LINKS:\n";
echo "   - Run: php artisan storage:link on live server\n";
echo "   - Verify public/storage link exists and works\n\n";

echo "4. 🌐 URL REWRITE RULES:\n";
echo "   - Apache: Ensure mod_rewrite is enabled\n";
echo "   - Nginx: Ensure try_files directive is correct\n";
echo "   - Check AllowOverride All in Apache config\n\n";

echo "5. 🚀 DEPLOYMENT ISSUES:\n";
echo "   - Ensure uploads directory was deployed\n";
echo "   - Check if files exist on live server\n";
echo "   - Verify deployment script includes uploads/\n\n";

echo "=== TROUBLESHOOTING STEPS ===\n\n";

echo "Step 1: Check if files exist on live server\n";
echo "Create a PHP file on live server with:\n";
echo "<?php\n";
echo "echo 'Uploads directory: ' . __DIR__ . '/uploads' . \"\\n\";\n";
echo "echo 'Directory exists: ' . (is_dir(__DIR__ . '/uploads') ? 'YES' : 'NO') . \"\\n\";\n";
echo "echo 'Services directory: ' . __DIR__ . '/uploads/services' . \"\\n\";\n";
echo "echo 'Services exists: ' . (is_dir(__DIR__ . '/uploads/services') ? 'YES' : 'NO') . \"\\n\";\n";
echo "\$files = glob(__DIR__ . '/uploads/services/*');\n";
echo "echo 'Files in services: ' . count(\$files) . \"\\n\";\n";
echo "foreach (array_slice(\$files, 0, 3) as \$file) {\n";
echo "    echo '- ' . basename(\$file) . ' (' . filesize(\$file) . ' bytes)' . \"\\n\";\n";
echo "}\n";
echo "?>\n\n";

echo "Step 2: Check web server configuration\n";
echo "For Apache (.htaccess should be in public/):\n";
echo "<IfModule mod_rewrite.c>\n";
echo "    RewriteEngine On\n";
echo "    RewriteCond %{REQUEST_FILENAME} !-d\n";
echo "    RewriteCond %{REQUEST_FILENAME} !-f\n";
echo "    RewriteRule ^ index.php [L]\n";
echo "</IfModule>\n\n";

echo "For Nginx (server block):\n";
echo "location / {\n";
echo "    try_files \$uri \$uri/ /index.php?\$query_string;\n";
echo "}\n\n";

echo "Step 3: Check file permissions\n";
echo "Run these commands on live server:\n";
echo "find public/uploads -type d -exec chmod 755 {} \\;\n";
echo "find public/uploads -type f -exec chmod 644 {} \\;\n";
echo "chown -R www-data:www-data public/uploads  # Ubuntu/Debian\n";
echo "# or\n";
echo "chown -R apache:apache public/uploads     # CentOS/RHEL\n\n";

echo "Step 4: Test direct file access\n";
echo "Try accessing: https://yourdomain.com/uploads/services/1769780642.jpg\n";
echo "If this works, the issue is in your app URL generation\n";
echo "If this doesn't work, it's a web server/file permission issue\n\n";

echo "=== QUICK FIXES ===\n\n";

echo "1. Ensure public/uploads is in your .gitignore:\n";
echo "/public/uploads/*\n";
echo "!/public/uploads/.gitkeep\n\n";

echo "2. Upload missing directories if needed:\n";
echo "mkdir -p public/uploads/services\n";
echo "chmod 755 public/uploads public/uploads/services\n\n";

echo "3. Check Laravel APP_URL in .env:\n";
echo "APP_URL=https://yourdomain.com\n\n";

echo "4. Clear caches on live server:\n";
echo "php artisan cache:clear\n";
echo "php artisan config:clear\n";
echo "php artisan route:clear\n";
echo "php artisan view:clear\n\n";

echo "=== DEBUGGING INFO ===\n\n";

echo "Current local configuration:\n";
echo "APP_URL: " . config('app.url') . "\n";
echo "Public path: " . public_path() . "\n";
echo "Uploads exists: " . (is_dir(public_path('uploads')) ? 'YES' : 'NO') . "\n";
echo "Services exists: " . (is_dir(public_path('uploads/services')) ? 'YES' : 'NO') . "\n";

if (is_dir(public_path('uploads/services'))) {
    $files = glob(public_path('uploads/services/*'));
    echo "Service files count: " . count($files) . "\n";
    if (!empty($files)) {
        echo "Sample file: " . basename($files[0]) . " (" . filesize($files[0]) . " bytes)\n";
    }
}

echo "\nCopy this diagnostic file to your live server and run it there!\n";
