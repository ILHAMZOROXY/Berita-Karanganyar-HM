<?php
/**
 * System Check - Verifikasi requirements website berita
 */

echo "<!DOCTYPE html>";
echo "<html lang='id'>";
echo "<head>";
echo "<meta charset='UTF-8'>";
echo "<meta name='viewport' content='width=device-width, initial-scale=1.0'>";
echo "<title>System Check - Website Berita Karanganyar</title>";
echo "<style>";
echo "body { font-family: Arial, sans-serif; max-width: 800px; margin: 30px auto; padding: 20px; background: #f5f5f5; }";
echo ".section { background: white; padding: 20px; margin: 20px 0; border-radius: 8px; }";
echo ".check { padding: 10px; margin: 10px 0; border-radius: 4px; display: flex; align-items: center; }";
echo ".check-pass { background: #d4edda; color: #155724; border-left: 4px solid #28a745; }";
echo ".check-fail { background: #f8d7da; color: #721c24; border-left: 4px solid #dc3545; }";
echo ".check-warn { background: #fff3cd; color: #856404; border-left: 4px solid #ffc107; }";
echo ".icon { margin-right: 10px; font-size: 1.2em; }";
echo "h1, h2 { color: #333; }";
echo "a { color: #667eea; text-decoration: none; }";
echo "a:hover { text-decoration: underline; }";
echo "</style>";
echo "</head>";
echo "<body>";
echo "<h1>🔍 System Check - Website Berita Karanganyar</h1>";
echo "<hr>";

// Check PHP Version
echo "<div class='section'>";
echo "<h2>📌 PHP Version</h2>";
$php_version = phpversion();
if (version_compare($php_version, '5.4', '>=')) {
    echo "<div class='check check-pass'>";
    echo "<span class='icon'>✅</span>";
    echo "<span>PHP Version: <strong>$php_version</strong> (OK)</span>";
    echo "</div>";
} else {
    echo "<div class='check check-fail'>";
    echo "<span class='icon'>❌</span>";
    echo "<span>PHP Version: <strong>$php_version</strong> (Minimal 5.4 diperlukan)</span>";
    echo "</div>";
}
echo "</div>";

// Check Extensions
echo "<div class='section'>";
echo "<h2>🔧 Required Extensions</h2>";

$extensions = [
    'mysqli' => 'MySQLi Database',
    'simplexml' => 'SimpleXML (untuk parse RSS)',
    'curl' => 'cURL (untuk HTTP requests)',
    'json' => 'JSON (untuk encode/decode)'
];

foreach ($extensions as $ext => $name) {
    if (extension_loaded($ext)) {
        echo "<div class='check check-pass'>";
        echo "<span class='icon'>✅</span>";
        echo "<span>$name ($ext) - Installed</span>";
        echo "</div>";
    } else {
        echo "<div class='check check-fail'>";
        echo "<span class='icon'>❌</span>";
        echo "<span>$name ($ext) - <strong>NOT FOUND</strong></span>";
        echo "</div>";
    }
}
echo "</div>";

// Check PHP Config
echo "<div class='section'>";
echo "<h2>⚙️ PHP Configuration</h2>";

$allow_url_fopen = ini_get('allow_url_fopen');
if ($allow_url_fopen) {
    echo "<div class='check check-pass'>";
    echo "<span class='icon'>✅</span>";
    echo "<span>allow_url_fopen: <strong>ON</strong> (Diperlukan untuk fetch RSS)</span>";
    echo "</div>";
} else {
    echo "<div class='check check-fail'>";
    echo "<span class='icon'>❌</span>";
    echo "<span>allow_url_fopen: <strong>OFF</strong> (Diperlukan untuk fetch RSS)</span>";
    echo "<br><em style='font-size: 0.9em;'>Hubungi hosting untuk enable ini di php.ini</em>";
    echo "</div>";
}

$upload_max = ini_get('upload_max_filesize');
echo "<div class='check check-pass'>";
echo "<span class='icon'>ℹ️</span>";
echo "<span>upload_max_filesize: <strong>$upload_max</strong></span>";
echo "</div>";

$memory_limit = ini_get('memory_limit');
echo "<div class='check check-pass'>";
echo "<span class='icon'>ℹ️</span>";
echo "<span>memory_limit: <strong>$memory_limit</strong></span>";
echo "</div>";

echo "</div>";

// Check Folders
echo "<div class='section'>";
echo "<h2>📁 Folder Permissions</h2>";

$folders = [
    '/cache' => 'Cache folder',
    '/img' => 'Images folder',
    '/config' => 'Config folder'
];

$base_path = dirname(__FILE__);
foreach ($folders as $folder => $desc) {
    $path = $base_path . $folder;
    if (is_dir($path)) {
        if (is_writable($path)) {
            echo "<div class='check check-pass'>";
            echo "<span class='icon'>✅</span>";
            echo "<span>$desc ($folder) - Readable & Writable</span>";
            echo "</div>";
        } else {
            echo "<div class='check check-warn'>";
            echo "<span class='icon'>⚠️</span>";
            echo "<span>$desc ($folder) - Read-only</span>";
            echo "</div>";
        }
    } else {
        echo "<div class='check check-fail'>";
        echo "<span class='icon'>❌</span>";
        echo "<span>$desc ($folder) - <strong>NOT FOUND</strong></span>";
        echo "</div>";
    }
}
echo "</div>";

// Check Files
echo "<div class='section'>";
echo "<h2>📄 Required Files</h2>";

$files = [
    'config/database.php' => 'Database Config',
    'config/news_fetcher.php' => 'News Fetcher Class',
    'index.php' => 'Halaman Utama',
    'refresh_news.php' => 'Refresh API',
    'css/style.css' => 'Stylesheet'
];

foreach ($files as $file => $desc) {
    $path = $base_path . '/' . $file;
    if (file_exists($path)) {
        echo "<div class='check check-pass'>";
        echo "<span class='icon'>✅</span>";
        echo "<span>$desc ($file)</span>";
        echo "</div>";
    } else {
        echo "<div class='check check-fail'>";
        echo "<span class='icon'>❌</span>";
        echo "<span>$desc ($file) - <strong>NOT FOUND</strong></span>";
        echo "</div>";
    }
}
echo "</div>";

// Network Check
echo "<div class='section'>";
echo "<h2>🌐 Network & API</h2>";

// Cek koneksi internet
if (@fsockopen("www.google.com", 443, $errno, $errstr, 3)) {
    echo "<div class='check check-pass'>";
    echo "<span class='icon'>✅</span>";
    echo "<span>Internet Connection - OK</span>";
    echo "</div>";
} else {
    echo "<div class='check check-fail'>";
    echo "<span class='icon'>❌</span>";
    echo "<span>Internet Connection - <strong>FAILED</strong></span>";
    echo "</div>";
}

// Cek Google News RSS
$context = stream_context_create(['http' => ['timeout' => 5]]);
$test_rss = @file_get_contents('https://news.google.com/rss/search?q=test', false, $context);
if ($test_rss !== false) {
    echo "<div class='check check-pass'>";
    echo "<span class='icon'>✅</span>";
    echo "<span>Google News RSS - Accessible</span>";
    echo "</div>";
} else {
    echo "<div class='check check-fail'>";
    echo "<span class='icon'>❌</span>";
    echo "<span>Google News RSS - <strong>NOT ACCESSIBLE</strong></span>";
    echo "<br><em style='font-size: 0.9em;'>Cek koneksi internet atau firewall blocking</em>";
    echo "</div>";
}

echo "</div>";

// Summary
echo "<div class='section' style='background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;'>";
echo "<h2 style='color: white;'>✅ Summary</h2>";
echo "<p>Jika semua check di atas menunjukkan ✅, website sudah siap digunakan!</p>";
echo "<p><a href='index.php' style='color: white; text-decoration: underline;'>➜ Buka Website</a></p>";
echo "</div>";

echo "</body>";
echo "</html>";
?>
