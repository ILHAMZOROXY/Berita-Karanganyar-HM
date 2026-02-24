<?php
session_start();
include 'config/database.php';

$admin_cfg = [];
if (file_exists(__DIR__ . '/config/admin.php')) {
    $admin_cfg = include 'config/admin.php';
}

$success_msg = '';
$error_msg = '';

function ensureTableColumn($conn, $table, $column, $definition) {
    if (!$conn) return;
    $table_safe = $conn->real_escape_string($table);
    $column_safe = $conn->real_escape_string($column);
    $res = @$conn->query("SHOW COLUMNS FROM `{$table_safe}` LIKE '{$column_safe}'");
    if ($res && $res->num_rows > 0) {
        return;
    }
    @$conn->query("ALTER TABLE `{$table_safe}` ADD COLUMN {$definition}");
}

function ensureAdminTables($conn) {
    if (!$conn) return;

    // Ensure deleted_items table exists
    $create_sql = "CREATE TABLE IF NOT EXISTS deleted_items (
        id VARCHAR(100) PRIMARY KEY,
        source_type VARCHAR(20),
        original_id VARCHAR(100),
        deleted_at DATETIME DEFAULT CURRENT_TIMESTAMP
    );";
    @$conn->query($create_sql);

    // Ensure aduan_feedback table exists
    $create_aduan_sql = "CREATE TABLE IF NOT EXISTS aduan_feedback (
        id INT AUTO_INCREMENT PRIMARY KEY,
        rating TINYINT NOT NULL,
        pekerjaan VARCHAR(120) NOT NULL,
        kategori TEXT,
        saran TEXT,
        ip_address VARCHAR(64),
        user_agent VARCHAR(255),
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        KEY idx_created_at (created_at)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
    @$conn->query($create_aduan_sql);

    // Ensure news_items table exists (admin reads from DB for speed)
    $create_news_items_sql = "CREATE TABLE IF NOT EXISTS news_items (
        id CHAR(32) NOT NULL PRIMARY KEY,
        judul VARCHAR(255) NOT NULL,
        deskripsi TEXT,
        link TEXT,
        sumber VARCHAR(255),
        tanggal_publikasi VARCHAR(32),
        timestamp INT DEFAULT 0,
        tipe VARCHAR(32),
        gambar TEXT,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        KEY idx_timestamp (timestamp),
        KEY idx_updated_at (updated_at)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
    @$conn->query($create_news_items_sql);

    // Ensure contact_messages table exists (from Contact Us page)
    $create_contact_sql = "CREATE TABLE IF NOT EXISTS contact_messages (
        id INT AUTO_INCREMENT PRIMARY KEY,
        nama VARCHAR(120) NOT NULL,
        instansi VARCHAR(160),
        email VARCHAR(160) NOT NULL,
        telepon VARCHAR(60) NOT NULL,
        topik VARCHAR(80) NOT NULL,
        pesan TEXT NOT NULL,
        admin_reply TEXT,
        reply_status VARCHAR(20) DEFAULT 'baru',
        replied_by VARCHAR(100),
        replied_at DATETIME NULL,
        ip_address VARCHAR(64),
        user_agent VARCHAR(255),
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        KEY idx_created_at (created_at),
        KEY idx_reply_status (reply_status)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
    @$conn->query($create_contact_sql);

    // Backward compatibility for existing installations
    ensureTableColumn($conn, 'contact_messages', 'admin_reply', 'admin_reply TEXT NULL');
    ensureTableColumn($conn, 'contact_messages', 'reply_status', "reply_status VARCHAR(20) DEFAULT 'baru'");
    ensureTableColumn($conn, 'contact_messages', 'replied_by', 'replied_by VARCHAR(100) NULL');
    ensureTableColumn($conn, 'contact_messages', 'replied_at', 'replied_at DATETIME NULL');
}

// Logout (redirect to homepage by default)
if (isset($_GET['action']) && $_GET['action'] === 'logout') {
    // determine safe redirect target
    $redirect = 'index.php';
    if (!empty($_GET['goto'])) {
        $candidate = $_GET['goto'];
        // basic safety: allow only relative paths without protocol or traversal
        if (strpos($candidate, 'http') === false && strpos($candidate, '..') === false) {
            $redirect = $candidate;
        }
    }
    session_destroy();
    header('Location: ' . $redirect);
    exit;
}

// Handle login
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $user = trim($_POST['username'] ?? '');
    $pass = $_POST['password'] ?? '';
    if (!empty($admin_cfg) && $user === ($admin_cfg['username'] ?? '') && $pass === ($admin_cfg['password'] ?? '')) {
        $_SESSION['admin_logged_in'] = true;
        header('Location: admin.php');
        exit;
    } else {
        $error = 'Kredensial salah.';
    }
}

// Handle delete/restore actions (requires login)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete_restore') {
    if (!isset($_SESSION['admin_logged_in']) || !$_SESSION['admin_logged_in']) {
        die('Unauthorized');
    }
    ensureAdminTables($conn);
    $item_type = $_POST['item_type'] ?? 'serpapi';
    $op = $_POST['op'] ?? 'delete';

    if ($item_type === 'local') {
        $local_id = intval($_POST['local_id'] ?? 0);
        if ($local_id > 0) {
            if ($op === 'delete') {
                $stmt = $conn->prepare("UPDATE berita SET status='draft' WHERE id = ?");
            } else {
                $stmt = $conn->prepare("UPDATE berita SET status='published' WHERE id = ?");
            }
            if ($stmt) { $stmt->bind_param('i', $local_id); $stmt->execute(); }
        }
    } else {
        // Handle SerpAPI items (stored in deleted_items with source_type 'serpapi')
        $item_id = $_POST['item_id'] ?? '';
        if ($op === 'delete') {
            $stmt = $conn->prepare("REPLACE INTO deleted_items (id, source_type, original_id) VALUES (?, 'serpapi', ?)");
            if ($stmt) { $stmt->bind_param('ss', $item_id, $item_id); $stmt->execute(); }
        } else {
            $stmt = $conn->prepare("DELETE FROM deleted_items WHERE id = ?");
            if ($stmt) { $stmt->bind_param('s', $item_id); $stmt->execute(); }
        }
    }

    // Hapus cache SerpAPI sehingga perubahan segera terlihat di beranda
    $cacheFile = __DIR__ . '/cache/karanganyar_news.json';
    if (file_exists($cacheFile)) { @unlink($cacheFile); }

    header('Location: admin.php');
    exit;
}

// Handle clear cache
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'clear_cache') {
    if (!isset($_SESSION['admin_logged_in']) || !$_SESSION['admin_logged_in']) {
        die('Unauthorized');
    }
    ensureAdminTables($conn);
    $cacheFile = __DIR__ . '/cache/karanganyar_news.json';
    if (file_exists($cacheFile)) { 
        @unlink($cacheFile); 
        $success_msg = 'Cache berhasil dihapus. Berita akan direfresh dari SerpAPI.';
    } else {
        $error_msg = 'Cache sudah kosong.';
    }
}

// Handle permanent delete from recycle bin
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'empty_recycle') {
    if (!isset($_SESSION['admin_logged_in']) || !$_SESSION['admin_logged_in']) {
        die('Unauthorized');
    }
    ensureAdminTables($conn);
    $conn->query("DELETE FROM deleted_items");
    $cacheFile = __DIR__ . '/cache/karanganyar_news.json';
    if (file_exists($cacheFile)) { @unlink($cacheFile); }
    $success_msg = 'Recycle Bin berhasil dikosongkan.';
}

// Handle reply for contact messages
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'save_contact_reply') {
    if (!isset($_SESSION['admin_logged_in']) || !$_SESSION['admin_logged_in']) {
        die('Unauthorized');
    }
    ensureAdminTables($conn);

    $message_id = intval($_POST['message_id'] ?? 0);
    $reply_status = trim($_POST['reply_status'] ?? 'baru');
    $admin_reply = trim($_POST['admin_reply'] ?? '');
    $allowed_status = ['baru', 'diproses', 'selesai'];
    if (!in_array($reply_status, $allowed_status, true)) {
        $reply_status = 'baru';
    }

    if ($message_id <= 0) {
        $error_msg = 'ID pesan tidak valid.';
    } else {
        if ($admin_reply !== '') {
            $replied_by = trim((string) ($admin_cfg['username'] ?? 'admin'));
            $stmt = $conn->prepare("UPDATE contact_messages SET admin_reply = ?, reply_status = ?, replied_by = ?, replied_at = NOW() WHERE id = ?");
            if ($stmt) {
                $stmt->bind_param('sssi', $admin_reply, $reply_status, $replied_by, $message_id);
                if ($stmt->execute()) {
                    $success_msg = 'Balasan admin berhasil disimpan.';
                } else {
                    $error_msg = 'Gagal menyimpan balasan admin.';
                }
                $stmt->close();
            } else {
                $error_msg = 'Gagal menyiapkan penyimpanan balasan.';
            }
        } else {
            $stmt = $conn->prepare("UPDATE contact_messages SET reply_status = ? WHERE id = ?");
            if ($stmt) {
                $stmt->bind_param('si', $reply_status, $message_id);
                if ($stmt->execute()) {
                    $success_msg = 'Status pesan berhasil diperbarui.';
                } else {
                    $error_msg = 'Gagal memperbarui status pesan.';
                }
                $stmt->close();
            } else {
                $error_msg = 'Gagal menyiapkan pembaruan status.';
            }
        }
    }
}

// Handle saving Google Maps API key (from admin settings)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'save_api_key') {
    if (!isset($_SESSION['admin_logged_in']) || !$_SESSION['admin_logged_in']) {
        die('Unauthorized');
    }
    ensureAdminTables($conn);
    $new_key = trim($_POST['google_maps_api_key'] ?? '');
    // sanitize: remove surrounding whitespace
    $safe_key = str_replace("'", "\\'", $new_key);
    $config_path = __DIR__ . '/config/api_keys.php';
    $content = "<?php\nreturn [\n    'google_maps_api_key' => '" . $safe_key . "',\n];\n";
    if (@file_put_contents($config_path, $content) !== false) {
        $success_msg = 'API key berhasil disimpan.';
        // clear index cache if exists
        $cacheFile = __DIR__ . '/cache/karanganyar_news.json';
        if (file_exists($cacheFile)) { @unlink($cacheFile); }
    } else {
        $error_msg = 'Gagal menyimpan API key. Periksa izin pada folder config.';
    }
}

// If not logged in, show login form
if (!isset($_SESSION['admin_logged_in']) || !$_SESSION['admin_logged_in']) {
    ?>
    <!doctype html>
    <html lang="id">
    <head>
        <meta charset="utf-8">
        <title>Admin Login</title>
        <link rel="stylesheet" href="css/style.css">
        <style>body{background:#f7f9fc} .login-box{max-width:420px;margin:80px auto;padding:24px;background:#fff;border-radius:8px;box-shadow:0 6px 24px rgba(0,0,0,0.08)} .login-box input{width:100%;padding:8px;margin:8px 0;border:1px solid #ddd;border-radius:4px} .btn-baca{background:#1a237e;color:#fff;border:none;padding:8px 12px;border-radius:4px;cursor:pointer}</style>
    </head>
    <body>
    <div class="container">
        <div class="login-box">
            <h2>Login Admin</h2>
            <?php if (!empty($error)): ?><div style="color:#c00"><?php echo htmlspecialchars($error); ?></div><?php endif; ?>
            <form method="post">
                <input type="text" name="username" placeholder="Username" required autofocus>
                <input type="password" name="password" placeholder="Password" required>
                <div style="margin-top:12px"><button type="submit" name="login" class="btn-baca">Login</button></div>
            </form>
            <p style="margin-top:12px;font-size:0.9rem;color:#666">Gunakan kredensial admin untuk mengelola berita.</p>
        </div>
    </div>
    </body>
    </html>
    <?php
    exit;
}

// Logged in: show dashboard
ensureAdminTables($conn);

$active_tab = trim($_GET['tab'] ?? 'serpapi');
$allowed_tabs = ['serpapi', 'feedback', 'contact', 'settings', 'recycle'];
if (!in_array($active_tab, $allowed_tabs, true)) {
    $active_tab = 'serpapi';
}

// Ambil berita dari DB (tanpa fetch RSS/SerpAPI) untuk mempercepat load admin
$berita_list = [];
$limit_items = 200;
try {
    $sql = "SELECT id, judul, deskripsi, link, sumber, tanggal_publikasi, timestamp, tipe, gambar FROM news_items ORDER BY timestamp DESC, created_at DESC LIMIT " . (int) $limit_items;
    $resItems = $conn->query($sql);
    if ($resItems) {
        while ($r = $resItems->fetch_assoc()) {
            $berita_list[] = [
                'id' => (string) ($r['id'] ?? ''),
                'judul' => (string) ($r['judul'] ?? ''),
                'deskripsi' => (string) ($r['deskripsi'] ?? ''),
                'link' => (string) ($r['link'] ?? ''),
                'sumber' => (string) ($r['sumber'] ?? ''),
                'tanggal_publikasi' => (string) ($r['tanggal_publikasi'] ?? ''),
                'timestamp' => (int) ($r['timestamp'] ?? 0),
                'tipe' => (string) ($r['tipe'] ?? 'db'),
                'gambar' => (string) ($r['gambar'] ?? ''),
            ];
        }
    }
} catch (Exception $e) {
    $berita_list = [];
}

// Fallback jika DB kosong: pakai cache file (tanpa network)
if (empty($berita_list)) {
    $cacheFileFallback = __DIR__ . '/cache/karanganyar_news.json';
    if (file_exists($cacheFileFallback)) {
        $cached = json_decode(@file_get_contents($cacheFileFallback), true);
        if (is_array($cached) && !empty($cached)) {
            $berita_list = array_slice($cached, 0, $limit_items);
        }
    }
}

// Ambil daftar id yang dihapus untuk SerpAPI
$deleted = [];
$deleted_count = 0;
$res = $conn->query("SELECT id FROM deleted_items");
if ($res) { 
    while ($r = $res->fetch_assoc()) { 
        $deleted[$r['id']] = true; 
        $deleted_count++;
    } 
}

// Hitung berita aktif
$active_count = count($berita_list) - $deleted_count;

// Ambil aduan/feedback
$aduan_list = [];
$aduan_res = $conn->query("SELECT * FROM aduan_feedback ORDER BY created_at DESC LIMIT 200");
if ($aduan_res) { while ($r = $aduan_res->fetch_assoc()) { $aduan_list[] = $r; } }

// Ambil pesan Hubungi Kami
$contact_list = [];
$contact_res = $conn->query("SELECT id, nama, instansi, email, telepon, topik, pesan, admin_reply, reply_status, replied_by, replied_at, created_at FROM contact_messages ORDER BY created_at DESC LIMIT 300");
if ($contact_res) {
    while ($r = $contact_res->fetch_assoc()) {
        $contact_list[] = $r;
    }
}

$contact_total = count($contact_list);
$contact_baru = 0;
$contact_diproses = 0;
$contact_selesai = 0;
foreach ($contact_list as $cmsg) {
    $st = strtolower(trim((string) ($cmsg['reply_status'] ?? 'baru')));
    if ($st === 'selesai') {
        $contact_selesai++;
    } elseif ($st === 'diproses') {
        $contact_diproses++;
    } else {
        $contact_baru++;
    }
}

// Check cache info
$cacheFile = __DIR__ . '/cache/karanganyar_news.json';
$cache_exists = file_exists($cacheFile);
$cache_time = $cache_exists ? filemtime($cacheFile) : 0;
$cache_size = $cache_exists ? filesize($cacheFile) : 0;

?>
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Portal Berita Karanganyar</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        * { box-sizing: border-box; }
        body { 
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
        }
        .admin-wrapper {
            max-width: 1400px;
            margin: 0 auto;
            padding: 20px;
        }
        .admin-header {
            background: rgba(255,255,255,0.98);
            border-radius: 16px;
            padding: 24px 32px;
            margin-bottom: 24px;
            box-shadow: 0 8px 32px rgba(0,0,0,0.12);
            display: flex;
            justify-content: space-between;
            align-items: center;
            backdrop-filter: blur(10px);
        }
        .admin-header h1 {
            margin: 0;
            font-size: 1.6rem;
            color: #333;
            font-weight: 700;
        }
        .admin-header-actions {
            display: flex;
            gap: 12px;
            align-items: center;
        }
        .btn-logout {
            padding: 10px 20px;
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
            transition: transform 0.2s, box-shadow 0.2s;
            box-shadow: 0 4px 15px rgba(245, 87, 108, 0.3);
        }
        .btn-logout:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(245, 87, 108, 0.4);
        }
        .btn-home {
            padding: 10px 20px;
            background: rgba(255,255,255,0.9);
            color: #667eea;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.2s;
            border: 2px solid #667eea;
        }
        .btn-home:hover {
            background: #667eea;
            color: white;
        }
        .admin-tabs {
            background: rgba(255,255,255,0.98);
            border-radius: 16px;
            padding: 16px 24px;
            margin-bottom: 24px;
            box-shadow: 0 8px 32px rgba(0,0,0,0.12);
            display: flex;
            gap: 12px;
            overflow-x: auto;
        }
        .tab-btn {
            padding: 12px 24px;
            background: transparent;
            border: none;
            border-radius: 10px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            color: #666;
            white-space: nowrap;
        }
        .tab-btn.active {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
        }
        .tab-btn:hover:not(.active) {
            background: rgba(102, 126, 234, 0.1);
        }
        .tab-content {
            display: none;
        }
        .tab-content.active {
            display: block;
        }
        .admin-card {
            background: rgba(255,255,255,0.98);
            border-radius: 16px;
            padding: 28px;
            margin-bottom: 24px;
            box-shadow: 0 8px 32px rgba(0,0,0,0.12);
            backdrop-filter: blur(10px);
        }
        .admin-card h3 {
            margin: 0 0 20px 0;
            font-size: 1.4rem;
            color: #333;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .admin-card h3::before {
            content: '';
            width: 4px;
            height: 28px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 2px;
        }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 20px;
            margin-bottom: 24px;
        }
        .stat-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 14px;
            padding: 24px;
            color: white;
            box-shadow: 0 8px 24px rgba(102, 126, 234, 0.3);
            transition: transform 0.3s, box-shadow 0.3s;
        }
        .stat-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 32px rgba(102, 126, 234, 0.4);
        }
        .stat-card.green {
            background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
            box-shadow: 0 8px 24px rgba(56, 239, 125, 0.3);
        }
        .stat-card.orange {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            box-shadow: 0 8px 24px rgba(245, 87, 108, 0.3);
        }
        .stat-card.blue {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
            box-shadow: 0 8px 24px rgba(79, 172, 254, 0.3);
        }
        .stat-label {
            font-size: 0.9rem;
            opacity: 0.9;
            margin-bottom: 8px;
            font-weight: 500;
        }
        .stat-value {
            font-size: 2.2rem;
            font-weight: 800;
            margin: 0;
        }
        .admin-table-wrapper {
            overflow-x: auto;
            border-radius: 12px;
            box-shadow: 0 4px 16px rgba(0,0,0,0.08);
        }
        .admin-table {
            width: 100%;
            border-collapse: collapse;
            background: white;
        }
        .admin-table thead {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        .admin-table th {
            padding: 16px 12px;
            text-align: left;
            font-weight: 600;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .admin-table td {
            padding: 14px 12px;
            border-bottom: 1px solid #f0f0f0;
            color: #444;
        }
        .admin-table tbody tr {
            transition: background 0.2s;
        }
        .admin-table tbody tr:hover {
            background: rgba(102, 126, 234, 0.05);
        }
        .admin-actions {
            display: flex;
            gap: 8px;
            align-items: center;
        }
        .admin-actions form {
            display: inline-block;
            margin: 0;
        }
        .btn-baca {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: #fff;
            border: none;
            padding: 8px 16px;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 600;
            font-size: 0.85rem;
            transition: transform 0.2s, box-shadow 0.2s;
            box-shadow: 0 2px 8px rgba(102, 126, 234, 0.3);
        }
        .btn-baca:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
        }
        .btn-danger {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            box-shadow: 0 2px 8px rgba(245, 87, 108, 0.3);
        }
        .btn-danger:hover {
            box-shadow: 0 4px 12px rgba(245, 87, 108, 0.4);
        }
        .btn-success {
            background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
            box-shadow: 0 2px 8px rgba(56, 239, 125, 0.3);
        }
        .btn-success:hover {
            box-shadow: 0 4px 12px rgba(56, 239, 125, 0.4);
        }
        .link-view {
            color: #667eea;
            text-decoration: none;
            font-weight: 600;
            transition: color 0.2s;
        }
        .link-view:hover {
            color: #764ba2;
        }
        .rating-stars {
            color: #ffc107;
            font-size: 1.1rem;
            letter-spacing: 2px;
        }
        .badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
        }
        .badge-success {
            background: #d4edda;
            color: #155724;
        }
        .badge-warning {
            background: #fff3cd;
            color: #856404;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #333;
        }
        .form-group input[type=\"text\"],
        .form-group textarea {
            width: 100%;
            padding: 12px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 1rem;
            transition: border-color 0.3s;
        }
        .form-group input[type=\"text\"]:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: #667eea;
        }
        .alert {
            padding: 16px 20px;
            border-radius: 10px;
            margin-bottom: 20px;
            font-weight: 500;
        }
        .alert-success {
            background: #d4edda;
            color: #155724;
            border-left: 4px solid #28a745;
        }
        .alert-error {
            background: #f8d7da;
            color: #721c24;
            border-left: 4px solid #dc3545;
        }
        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: #999;
        }
        .empty-state svg {
            width: 80px;
            height: 80px;
            margin-bottom: 16px;
            opacity: 0.5;
        }
        @media (max-width: 768px) {
            .admin-header {
                flex-direction: column;
                gap: 16px;
                text-align: center;
            }
            .admin-tabs {
                flex-wrap: wrap;
            }
            .stat-card {
                min-width: 100%;
            }
        }
    </style>
</head>
<body>
<div class="admin-wrapper">
    <div class="admin-header">
        <div style="display:flex;align-items:center;gap:16px;">
            <img src="img/kominfo.png" alt="Logo Kominfo" style="height:48px;width:auto;">
            <h1 style="margin:0;">Dashboard Administrator</h1>
        </div>
        <div class="admin-header-actions">
            <a href="index.php" class="btn-home">🏠 Beranda</a>
            <a href="admin.php?action=logout&goto=index.php" class="btn-logout">🚪 Logout</a>
        </div>
    </div>

    <div class="admin-tabs">
        <button class="tab-btn <?php echo $active_tab === 'serpapi' ? 'active' : ''; ?>" onclick="switchTab('serpapi', this)">📰 Berita Karanganyar</button>
        <button class="tab-btn <?php echo $active_tab === 'feedback' ? 'active' : ''; ?>" onclick="switchTab('feedback', this)">💬 Feedback Pengunjung</button>
        <button class="tab-btn <?php echo $active_tab === 'contact' ? 'active' : ''; ?>" onclick="switchTab('contact', this)">📨 Hubungi Kami</button>
        <button class="tab-btn <?php echo $active_tab === 'settings' ? 'active' : ''; ?>" onclick="switchTab('settings', this)">⚙️ Pengaturan</button>
        <button class="tab-btn <?php echo $active_tab === 'recycle' ? 'active' : ''; ?>" onclick="switchTab('recycle', this)">🗑️ Recycle Bin</button>
    </div>

    <!-- Tab Content: SerpAPI News -->
    <div id="tab-serpapi" class="tab-content <?php echo $active_tab === 'serpapi' ? 'active' : ''; ?>">
        <div class="admin-card">
            <h3>Berita Kabupaten Karanganyar (via SerpAPI)</h3>
            <div class="admin-table-wrapper">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Judul Berita</th>
                            <th>Sumber</th>
                            <th>Tanggal Publikasi</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($berita_list as $b):
                            $id = $b['id'];
                            $isDeleted = isset($deleted[$id]);
                            ?>
                            <tr>
                                <td><?php echo htmlspecialchars($b['judul']); ?></td>
                                <td><?php echo htmlspecialchars($b['sumber']); ?></td>
                                <td><?php echo htmlspecialchars($b['tanggal_publikasi']); ?></td>
                                <td class="admin-actions">
                                    <?php if (!$isDeleted): ?>
                                        <form method="post">
                                            <input type="hidden" name="action" value="delete_restore">
                                            <input type="hidden" name="item_id" value="<?php echo htmlspecialchars($id); ?>">
                                            <input type="hidden" name="item_type" value="serpapi">
                                            <input type="hidden" name="op" value="delete">
                                            <button class="btn-baca btn-danger" type="submit">🗑️ Hapus</button>
                                        </form>
                                    <?php else: ?>
                                        <form method="post">
                                            <input type="hidden" name="action" value="delete_restore">
                                            <input type="hidden" name="item_id" value="<?php echo htmlspecialchars($id); ?>">
                                            <input type="hidden" name="item_type" value="serpapi">
                                            <input type="hidden" name="op" value="restore">
                                            <button class="btn-baca btn-success" type="submit">↩️ Kembalikan</button>
                                        </form>
                                    <?php endif; ?>
                                    <a href="<?php echo htmlspecialchars($b['link']); ?>" target="_blank" class="link-view">👁️ Lihat</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if (count($berita_list) === 0): ?>
                            <tr>
                                <td colspan="4" class="empty-state">
                                    <div>📭</div>
                                    <p>Belum ada berita</p>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Tab Content: Recycle Bin -->
    <div id="tab-recycle" class="tab-content <?php echo $active_tab === 'recycle' ? 'active' : ''; ?>">
        <div class="admin-card">
            <h3>🗑️ Recycle Bin - Berita yang Dihapus</h3>
            <p style="color:#666;margin-bottom:20px;">Berita yang dihapus dapat dikembalikan ke daftar utama.</p>
            <div class="admin-table-wrapper">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Judul Berita</th>
                            <th>Sumber</th>
                            <th>Tanggal Publikasi</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $deleted_items = [];
                        foreach ($berita_list as $b):
                            $id = $b['id'];
                            if (isset($deleted[$id])):
                                $deleted_items[] = $b;
                        ?>
                            <tr>
                                <td><?php echo htmlspecialchars($b['judul']); ?></td>
                                <td><?php echo htmlspecialchars($b['sumber']); ?></td>
                                <td><?php echo htmlspecialchars($b['tanggal_publikasi']); ?></td>
                                <td class="admin-actions">
                                    <form method="post">
                                        <input type="hidden" name="action" value="delete_restore">
                                        <input type="hidden" name="item_id" value="<?php echo htmlspecialchars($id); ?>">
                                        <input type="hidden" name="item_type" value="serpapi">
                                        <input type="hidden" name="op" value="restore">
                                        <button class="btn-baca btn-success" type="submit">↩️ Kembalikan</button>
                                    </form>
                                    <a href="<?php echo htmlspecialchars($b['link']); ?>" target="_blank" class="link-view">👁️ Lihat</a>
                                </td>
                            </tr>
                        <?php 
                            endif;
                        endforeach; 
                        ?>
                        <?php if (count($deleted_items) === 0): ?>
                            <tr>
                                <td colspan="4" class="empty-state">
                                    <div>✨</div>
                                    <p>Recycle Bin kosong. Tidak ada berita yang dihapus.</p>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Tab Content: Feedback -->
    <div id="tab-feedback" class="tab-content <?php echo $active_tab === 'feedback' ? 'active' : ''; ?>">
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-label">Total Feedback</div>
                <div class="stat-value"><?php echo count($aduan_list); ?></div>
            </div>
            <div class="stat-card green">
                <div class="stat-label">Rating Rata-rata</div>
                <div class="stat-value">
                    <?php
                    $avg = 0;
                    if (count($aduan_list) > 0) {
                        $sum = 0;
                        foreach ($aduan_list as $a) { $sum += (int) $a['rating']; }
                        $avg = $sum / count($aduan_list);
                    }
                    echo $avg > 0 ? number_format($avg, 1) : '0.0';
                    ?> ⭐
                </div>
            </div>
            <div class="stat-card orange">
                <div class="stat-label">Feedback Terbaru</div>
                <div class="stat-value"><?php echo count($aduan_list) > 0 ? date('d M', strtotime($aduan_list[0]['created_at'])) : '-'; ?></div>
            </div>
        </div>

        <div class="admin-card">
            <h3>Daftar Feedback Pengunjung</h3>
            <div class="admin-table-wrapper">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Rating</th>
                            <th>Pekerjaan</th>
                            <th>Kategori Perbaikan</th>
                            <th>Saran</th>
                            <th>Waktu</th>
                            <th>IP Address</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($aduan_list as $a): ?>
                            <?php
                            $kategori = '';
                            if (!empty($a['kategori'])) {
                                $decoded = json_decode($a['kategori'], true);
                                if (is_array($decoded)) {
                                    $kategori = implode(', ', $decoded);
                                } else {
                                    $kategori = $a['kategori'];
                                }
                            }
                            $rating = (int) $a['rating'];
                            $rating_label = str_repeat('★', max(0, $rating)) . str_repeat('☆', max(0, 5 - $rating));
                            ?>
                            <tr>
                                <td><span class="rating-stars"><?php echo htmlspecialchars($rating_label); ?></span></td>
                                <td><?php echo htmlspecialchars($a['pekerjaan']); ?></td>
                                <td><?php echo htmlspecialchars($kategori); ?></td>
                                <td><?php echo nl2br(htmlspecialchars(substr($a['saran'], 0, 100) . (strlen($a['saran']) > 100 ? '...' : ''))); ?></td>
                                <td><?php echo date('d M Y H:i', strtotime($a['created_at'])); ?></td>
                                <td style="font-family: monospace; font-size: 0.85rem;"><?php echo htmlspecialchars($a['ip_address']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if (count($aduan_list) === 0): ?>
                            <tr>
                                <td colspan="6" class="empty-state">
                                    <div>💬</div>
                                    <p>Belum ada feedback dari pengunjung</p>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Tab Content: Contact Messages -->
    <div id="tab-contact" class="tab-content <?php echo $active_tab === 'contact' ? 'active' : ''; ?>">
        <?php if (!empty($success_msg)): ?>
            <div class="alert alert-success">✓ <?php echo htmlspecialchars($success_msg); ?></div>
        <?php endif; ?>
        <?php if (!empty($error_msg)): ?>
            <div class="alert alert-error">✗ <?php echo htmlspecialchars($error_msg); ?></div>
        <?php endif; ?>

        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-label">Total Pesan</div>
                <div class="stat-value"><?php echo $contact_total; ?></div>
            </div>
            <div class="stat-card orange">
                <div class="stat-label">Pesan Baru</div>
                <div class="stat-value"><?php echo $contact_baru; ?></div>
            </div>
            <div class="stat-card blue">
                <div class="stat-label">Sedang Diproses</div>
                <div class="stat-value"><?php echo $contact_diproses; ?></div>
            </div>
            <div class="stat-card green">
                <div class="stat-label">Selesai Dijawab</div>
                <div class="stat-value"><?php echo $contact_selesai; ?></div>
            </div>
        </div>

        <div class="admin-card">
            <h3>Pesan Masuk Layanan Hubungi Kami</h3>
            <div class="admin-table-wrapper">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Pengirim</th>
                            <th>Topik</th>
                            <th>Pesan</th>
                            <th>Status</th>
                            <th>Balasan Admin</th>
                            <th>Waktu</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($contact_list as $c): ?>
                            <?php
                            $status = strtolower(trim((string) ($c['reply_status'] ?? 'baru')));
                            if (!in_array($status, ['baru', 'diproses', 'selesai'], true)) {
                                $status = 'baru';
                            }
                            $status_label = ucfirst($status);
                            $status_class = 'badge badge-warning';
                            if ($status === 'selesai') {
                                $status_class = 'badge badge-success';
                            } elseif ($status === 'diproses') {
                                $status_class = 'badge';
                            }
                            ?>
                            <tr>
                                <td>
                                    <div style="font-weight:700;color:#333;"><?php echo htmlspecialchars($c['nama']); ?></div>
                                    <?php if (!empty($c['instansi'])): ?><div style="font-size:0.85rem;color:#666;"><?php echo htmlspecialchars($c['instansi']); ?></div><?php endif; ?>
                                    <div style="font-size:0.85rem;"><a href="mailto:<?php echo htmlspecialchars($c['email']); ?>" class="link-view"><?php echo htmlspecialchars($c['email']); ?></a></div>
                                    <div style="font-size:0.82rem;color:#666;"><?php echo htmlspecialchars($c['telepon']); ?></div>
                                </td>
                                <td><?php echo htmlspecialchars(ucfirst((string) $c['topik'])); ?></td>
                                <td style="min-width:220px;"><?php echo nl2br(htmlspecialchars(substr((string) $c['pesan'], 0, 260) . (strlen((string) $c['pesan']) > 260 ? '...' : ''))); ?></td>
                                <td><span class="<?php echo $status_class; ?>"><?php echo htmlspecialchars($status_label); ?></span></td>
                                <td style="min-width:260px;">
                                    <form method="post" action="admin.php?tab=contact">
                                        <input type="hidden" name="action" value="save_contact_reply">
                                        <input type="hidden" name="message_id" value="<?php echo (int) $c['id']; ?>">
                                        <select name="reply_status" style="width:100%;padding:8px;border:1px solid #ddd;border-radius:8px;margin-bottom:8px;">
                                            <option value="baru" <?php echo $status === 'baru' ? 'selected' : ''; ?>>Baru</option>
                                            <option value="diproses" <?php echo $status === 'diproses' ? 'selected' : ''; ?>>Diproses</option>
                                            <option value="selesai" <?php echo $status === 'selesai' ? 'selected' : ''; ?>>Selesai</option>
                                        </select>
                                        <textarea name="admin_reply" rows="4" placeholder="Tulis jawaban admin..." style="width:100%;padding:10px;border:1px solid #ddd;border-radius:8px;resize:vertical;"><?php echo htmlspecialchars((string) ($c['admin_reply'] ?? '')); ?></textarea>
                                        <div style="margin-top:8px;display:flex;justify-content:space-between;gap:8px;align-items:center;">
                                            <small style="color:#666;display:block;">
                                                <?php if (!empty($c['replied_at'])): ?>
                                                    Dijawab: <?php echo date('d M Y H:i', strtotime((string) $c['replied_at'])); ?>
                                                    <?php if (!empty($c['replied_by'])): ?>oleh <?php echo htmlspecialchars((string) $c['replied_by']); ?><?php endif; ?>
                                                <?php else: ?>
                                                    Belum ada jawaban
                                                <?php endif; ?>
                                            </small>
                                            <button type="submit" class="btn-baca">💾 Simpan</button>
                                        </div>
                                    </form>
                                </td>
                                <td>
                                    <div><?php echo date('d M Y', strtotime((string) $c['created_at'])); ?></div>
                                    <div style="font-size:0.82rem;color:#666;"><?php echo date('H:i', strtotime((string) $c['created_at'])); ?> WIB</div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if (count($contact_list) === 0): ?>
                            <tr>
                                <td colspan="6" class="empty-state">
                                    <div>📨</div>
                                    <p>Belum ada pesan dari halaman Hubungi Kami</p>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Tab Content: Settings -->
    <div id="tab-settings" class="tab-content <?php echo $active_tab === 'settings' ? 'active' : ''; ?>">
        <!-- Statistics Overview -->
        <div class="stats-grid" style="margin-bottom:24px;">
            <div class="stat-card">
                <div class="stat-label">Total Berita</div>
                <div class="stat-value"><?php echo count($berita_list); ?></div>
            </div>
            <div class="stat-card green">
                <div class="stat-label">Berita Aktif</div>
                <div class="stat-value"><?php echo $active_count; ?></div>
            </div>
            <div class="stat-card orange">
                <div class="stat-label">Recycle Bin</div>
                <div class="stat-value"><?php echo $deleted_count; ?></div>
            </div>
            <div class="stat-card blue">
                <div class="stat-label">Pesan Hubungi Kami</div>
                <div class="stat-value"><?php echo $contact_total; ?></div>
            </div>
        </div>

        <?php if (!empty($success_msg)): ?>
            <div class="alert alert-success">✓ <?php echo htmlspecialchars($success_msg); ?></div>
        <?php endif; ?>
        <?php if (!empty($error_msg)): ?>
            <div class="alert alert-error">✗ <?php echo htmlspecialchars($error_msg); ?></div>
        <?php endif; ?>

        <!-- Cache Management -->
        <div class="admin-card" style="margin-bottom:24px;">
            <h3>🗄️ Manajemen Cache</h3>
            <p style="color:#666;margin-bottom:20px;">Kelola cache berita untuk meningkatkan performa website</p>
            
            <div style="background:#f8f9fa;padding:20px;border-radius:12px;margin-bottom:20px;">
                <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:16px;margin-bottom:16px;">
                    <div>
                        <div style="font-size:0.85rem;color:#666;margin-bottom:4px;">Status Cache</div>
                        <div style="font-weight:600;color:#333;">
                            <?php if ($cache_exists): ?>
                                <span style="color:#2e7d32;">✓ Aktif</span>
                            <?php else: ?>
                                <span style="color:#ed6c02;">⚠ Tidak Ada</span>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div>
                        <div style="font-size:0.85rem;color:#666;margin-bottom:4px;">Ukuran Cache</div>
                        <div style="font-weight:600;color:#333;">
                            <?php echo $cache_exists ? number_format($cache_size / 1024, 2) . ' KB' : '0 KB'; ?>
                        </div>
                    </div>
                    <div>
                        <div style="font-size:0.85rem;color:#666;margin-bottom:4px;">Terakhir Diperbarui</div>
                        <div style="font-weight:600;color:#333;">
                            <?php echo $cache_exists ? date('d M Y H:i', $cache_time) : '-'; ?>
                        </div>
                    </div>
                </div>
                
                <form method="post" style="display:inline-block;">
                    <input type="hidden" name="action" value="clear_cache">
                    <button type="submit" class="btn-baca btn-danger" onclick="return confirm('Hapus cache berita? Berita akan dimuat ulang dari SerpAPI.')">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="vertical-align:middle;margin-right:6px;">
                            <polyline points="3 6 5 6 21 6"></polyline>
                            <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                        </svg>
                        Hapus Cache
                    </button>
                </form>
            </div>
            
            <div style="padding:16px;background:#e3f2fd;border-left:4px solid #2196f3;border-radius:8px;">
                <h4 style="margin:0 0 8px 0;color:#1565c0;font-size:0.95rem;">ℹ️ Tentang Cache</h4>
                <ul style="margin:0;padding-left:20px;color:#1976d2;font-size:0.9rem;line-height:1.6;">
                    <li>Cache menyimpan berita dari SerpAPI agar loading lebih cepat</li>
                    <li>Hapus cache jika ingin memperbarui berita secara manual</li>
                    <li>Cache otomatis diperbarui setiap beberapa jam</li>
                </ul>
            </div>
        </div>

        <!-- Recycle Bin Management -->
        <div class="admin-card" style="margin-bottom:24px;">
            <h3>🗑️ Manajemen Recycle Bin</h3>
            <p style="color:#666;margin-bottom:20px;">Kosongkan recycle bin secara permanen</p>
            
            <div style="background:#fff3cd;padding:20px;border-radius:12px;border-left:4px solid #ffc107;margin-bottom:16px;">
                <div style="display:flex;align-items:start;gap:12px;">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#856404" stroke-width="2">
                        <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path>
                        <line x1="12" y1="9" x2="12" y2="13"></line>
                        <line x1="12" y1="17" x2="12.01" y2="17"></line>
                    </svg>
                    <div style="flex:1;">
                        <div style="font-weight:600;color:#856404;margin-bottom:6px;">Peringatan!</div>
                        <div style="color:#856404;font-size:0.9rem;">
                            Item yang dihapus dari Recycle Bin: <strong><?php echo $deleted_count; ?> berita</strong>
                            <br>Tindakan ini tidak dapat dibatalkan!
                        </div>
                    </div>
                </div>
            </div>
            
            <form method="post" style="display:inline-block;">
                <input type="hidden" name="action" value="empty_recycle">
                <button type="submit" class="btn-baca btn-danger" onclick="return confirm('Kosongkan Recycle Bin secara permanen? Tindakan ini TIDAK DAPAT dibatalkan!')" <?php echo $deleted_count === 0 ? 'disabled' : ''; ?>>
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="vertical-align:middle;margin-right:6px;">
                        <polyline points="3 6 5 6 21 6"></polyline>
                        <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                        <line x1="10" y1="11" x2="10" y2="17"></line>
                        <line x1="14" y1="11" x2="14" y2="17"></line>
                    </svg>
                    Kosongkan Recycle Bin (<?php echo $deleted_count; ?>)
                </button>
            </form>
        </div>

        <!-- Google Maps API -->
        <div class="admin-card">
            <h3>🗺️ Google Maps API</h3>
            <p style="color:#666;margin-bottom:24px;">Konfigurasi API untuk menampilkan peta Karanganyar</p>
            
            <?php
            // load current api key if exists (do not reveal full key)
            $current_api_key = '';
            if (file_exists(__DIR__ . '/config/api_keys.php')) {
                $k = include __DIR__ . '/config/api_keys.php';
                if (!empty($k['google_maps_api_key']) && $k['google_maps_api_key'] !== 'YOUR_ACTUAL_API_KEY_HERE') {
                    $current_api_key = $k['google_maps_api_key'];
                }
            }
            $masked = $current_api_key ? (substr($current_api_key,0,4) . str_repeat('*', max(6, strlen($current_api_key)-8)) . substr($current_api_key,-4)) : '';
            ?>
            
            <div style="background:#f8f9fa;padding:24px;border-radius:12px;margin-bottom:20px;">
                <form method="post" style="max-width:600px;">
                    <input type="hidden" name="action" value="save_api_key">
                    
                    <div class="form-group">
                        <label style="font-size:0.95rem;color:#555;">API Key</label>
                        <input type="text" name="google_maps_api_key" value="" placeholder="AIzaSyXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX" style="font-family:monospace;">
                        <?php if ($masked): ?>
                            <div style="margin-top:12px;padding:12px;background:#e8f5e9;border-radius:8px;display:flex;align-items:center;gap:10px;">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#2e7d32" stroke-width="2">
                                    <polyline points="20 6 9 17 4 12"></polyline>
                                </svg>
                                <div style="flex:1;">
                                    <div style="font-size:0.85rem;color:#2e7d32;font-weight:600;">API Key Aktif</div>
                                    <div style="font-size:0.9rem;color:#558b2f;font-family:monospace;margin-top:4px;"><?php echo htmlspecialchars($masked); ?></div>
                                </div>
                            </div>
                        <?php else: ?>
                            <div style="margin-top:12px;padding:12px;background:#fff3cd;border-radius:8px;display:flex;align-items:center;gap:10px;">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#856404" stroke-width="2">
                                    <circle cx="12" cy="12" r="10"></circle>
                                    <line x1="12" y1="8" x2="12" y2="12"></line>
                                    <line x1="12" y1="16" x2="12.01" y2="16"></line>
                                </svg>
                                <div style="font-size:0.9rem;color:#856404;">API Key belum dikonfigurasi. Peta tidak akan ditampilkan di beranda.</div>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <button type="submit" class="btn-baca" style="width:auto;">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="vertical-align:middle;margin-right:6px;">
                            <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path>
                            <polyline points="17 21 17 13 7 13 7 21"></polyline>
                            <polyline points="7 3 7 8 15 8"></polyline>
                        </svg>
                        Simpan Perubahan
                    </button>
                </form>
            </div>
            
            <div style="padding:16px;background:#e3f2fd;border-left:4px solid #2196f3;border-radius:8px;">
                <h4 style="margin:0 0 8px 0;color:#1565c0;font-size:0.95rem;">ℹ️ Informasi Penting</h4>
                <ul style="margin:0;padding-left:20px;color:#1976d2;font-size:0.9rem;line-height:1.6;">
                    <li>API Key digunakan untuk menampilkan peta lokasi Karanganyar di beranda</li>
                    <li>Dapatkan API Key gratis di <a href="https://console.cloud.google.com/" target="_blank" style="color:#1565c0;font-weight:600;">Google Cloud Console</a></li>
                    <li>Pastikan mengaktifkan Maps JavaScript API</li>
                    <li>Batasi API Key dengan referrer domain untuk keamanan</li>
                </ul>
            </div>
        </div>
    </div>

</div>

<script>
function switchTab(tabName, buttonEl) {
    // Hide all tab contents
    document.querySelectorAll('.tab-content').forEach(function(content) {
        content.classList.remove('active');
    });
    
    // Remove active class from all buttons
    document.querySelectorAll('.tab-btn').forEach(function(btn) {
        btn.classList.remove('active');
    });
    
    // Show selected tab content
    document.getElementById('tab-' + tabName).classList.add('active');
    
    // Add active class to clicked button
    if (buttonEl) {
        buttonEl.classList.add('active');
    }
}

// Confirmation for delete/restore actions
document.addEventListener('DOMContentLoaded', function(){
    document.querySelectorAll('form input[name="action"][value="delete_restore"]').forEach(function(inp){
        var form = inp.closest('form');
        if (!form) return;
        form.addEventListener('submit', function(e){
            var opEl = form.querySelector('input[name="op"]');
            var op = opEl ? opEl.value : '';
            var msg = (op === 'delete') ? 'Anda yakin ingin menghapus item ini?' : 'Anda yakin ingin mengembalikan item ini?';
            if (!confirm(msg)) { e.preventDefault(); }
        });
    });
});
</script>
</body>
</html>