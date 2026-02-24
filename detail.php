<?php
include 'config/database.php';

// Ambil ID dari URL
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id <= 0) {
    header('Location: index.php');
    exit;
}

// Query untuk mengambil detail berita menggunakan prepared statement
$sql = "SELECT * FROM berita WHERE id = ? AND status = 'published'";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    die("Prepare failed: " . $conn->error);
}

$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if (!$result || $result->num_rows == 0) {
    header('Location: index.php');
    exit;
}

$berita = $result->fetch_assoc();

// Update view count menggunakan prepared statement
$update_sql = "UPDATE berita SET views = views + 1 WHERE id = ?";
$update_stmt = $conn->prepare($update_sql);
if ($update_stmt) {
    $update_stmt->bind_param("i", $id);
    $update_stmt->execute();
    $update_stmt->close();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($berita['judul']); ?> - Website Berita</title>
    <link rel="stylesheet" href="css/style.css">
        <link rel="stylesheet" href="css/social-icons.css">
</head>
<body>
    <!-- Header -->
    <header>
        <div class="container">
            <h1>📰 Website Berita</h1>
            <p>Informasi terkini dan berita terbaru setiap hari</p>
        </div>
    </header>

    <!-- Navigation -->
    <nav>
        <div class="container">
            <ul>
                <li><a href="index.php">🏠 Beranda</a></li>
                <li><a href="admin.php">⚙️ Kelola Berita</a></li>
            </ul>
        </div>
    </nav>

    <!-- Main Content -->
    <main>
        <div class="container">
            <a href="index.php" class="btn-kembali">← Kembali ke Beranda</a>

            <div class="berita-detail">
                <!-- Gambar -->
                <?php if ($berita['gambar']): ?>
                    <div class="berita-detail-image">
                        <img src="img/<?php echo htmlspecialchars($berita['gambar']); ?>" 
                             alt="<?php echo htmlspecialchars($berita['judul']); ?>">
                    </div>
                <?php endif; ?>

                <!-- Judul -->
                <h1><?php echo htmlspecialchars($berita['judul']); ?></h1>

                <!-- Meta Information -->
                <div class="berita-detail-meta">
                    <span>✍️ Penulis: <strong><?php echo htmlspecialchars($berita['penulis']); ?></strong></span>
                    <span>📅 Dipublikasikan: <strong><?php echo date('d M Y - H:i', strtotime($berita['tanggal_publikasi'])); ?></strong></span>
                    <span>👁️ Dibaca: <strong><?php echo $berita['views']; ?> kali</strong></span>
                </div>

                <!-- Content -->
                <div class="berita-detail-text">
                    <?php echo nl2br(htmlspecialchars($berita['konten'])); ?>
                </div>
            </div>
        </div>
    </main>

        <?php
        $footer_show_map = false;
        $footer_year = 2026;
        include __DIR__ . '/partials/site_footer.php';
        ?>
    <?php include __DIR__ . '/partials/floating_widget.php'; ?>
</body>
</html>
