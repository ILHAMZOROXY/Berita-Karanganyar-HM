<?php
header('Content-Type: application/json; charset=utf-8');

include 'config/database.php';

// Ensure table exists
$create_sql = "CREATE TABLE IF NOT EXISTS aduan_feedback (
    id INT AUTO_INCREMENT PRIMARY KEY,
    rating TINYINT NOT NULL,
    pekerjaan VARCHAR(120) NOT NULL,
    kategori TEXT,
    saran TEXT,
    ip_address VARCHAR(64),
    user_agent VARCHAR(255),
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
@$conn->query($create_sql);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Metode tidak diizinkan.']);
    exit;
}

$rating = intval($_POST['rating'] ?? 0);
$pekerjaan = trim($_POST['pekerjaan'] ?? '');
$kategori = $_POST['kategori'] ?? [];
$saran = trim($_POST['saran'] ?? '');

if ($rating < 1 || $rating > 5) {
    echo json_encode(['success' => false, 'message' => 'Rating tidak valid.']);
    exit;
}

if ($pekerjaan === '') {
    echo json_encode(['success' => false, 'message' => 'Pekerjaan wajib dipilih.']);
    exit;
}

if (!is_array($kategori)) {
    $kategori = [];
}

$kategori_json = json_encode(array_values($kategori), JSON_UNESCAPED_UNICODE);
$ip = $_SERVER['REMOTE_ADDR'] ?? '';
$ua = $_SERVER['HTTP_USER_AGENT'] ?? '';

$stmt = $conn->prepare("INSERT INTO aduan_feedback (rating, pekerjaan, kategori, saran, ip_address, user_agent) VALUES (?, ?, ?, ?, ?, ?)");
if (!$stmt) {
    echo json_encode(['success' => false, 'message' => 'Gagal menyiapkan penyimpanan.']);
    exit;
}

$stmt->bind_param('isssss', $rating, $pekerjaan, $kategori_json, $saran, $ip, $ua);
$ok = $stmt->execute();

if ($ok) {
    echo json_encode(['success' => true, 'message' => 'Terima kasih. Aduan Anda berhasil dikirim.']);
    exit;
}

echo json_encode(['success' => false, 'message' => 'Gagal menyimpan aduan.']);
