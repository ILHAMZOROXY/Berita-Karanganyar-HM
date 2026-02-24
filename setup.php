<?php
// File setup.php - untuk membuat database secara otomatis

$host = 'localhost';
$username = 'root';
$password = '';
$database = 'website_berita';

// Koneksi tanpa database dulu
$conn = new mysqli($host, $username, $password);

if ($conn->connect_error) {
    die("❌ Koneksi gagal: " . $conn->connect_error . "<br>Pastikan MySQL server sudah berjalan!");
}

echo "✅ Koneksi MySQL berhasil<br>";

// Buat database
$sql_create_db = "CREATE DATABASE IF NOT EXISTS $database";
if ($conn->query($sql_create_db)) {
    echo "✅ Database '$database' berhasil dibuat<br>";
} else {
    die("❌ Error membuat database: " . $conn->error);
}

// Pilih database
$conn->select_db($database);

// Buat tabel berita
$sql_create_table = "CREATE TABLE IF NOT EXISTS berita (
    id INT AUTO_INCREMENT PRIMARY KEY,
    judul VARCHAR(255) NOT NULL,
    konten LONGTEXT NOT NULL,
    penulis VARCHAR(100) NOT NULL,
    gambar VARCHAR(255),
    tanggal_publikasi DATETIME DEFAULT CURRENT_TIMESTAMP,
    tanggal_dibuat DATETIME DEFAULT CURRENT_TIMESTAMP,
    status ENUM('draft', 'published') DEFAULT 'published',
    views INT DEFAULT 0
)";

if ($conn->query($sql_create_table)) {
    echo "✅ Tabel 'berita' berhasil dibuat<br>";
} else {
    die("❌ Error membuat tabel: " . $conn->error);
}

// Cek apakah sudah ada data
$check = $conn->query("SELECT COUNT(*) as total FROM berita");
$row = $check->fetch_assoc();

if ($row['total'] == 0) {
    // Insert data dummy
    $sql_insert = "INSERT INTO berita (judul, konten, penulis, gambar, tanggal_publikasi, status) VALUES
        ('Berita Terbaru Hari Ini', 'Ini adalah konten berita terbaru yang diterbitkan hari ini. Berita ini hanya akan ditampilkan jika dipublikasikan dalam 24 jam terakhir.', 'Admin', '', NOW(), 'published'),
        ('Update Teknologi Terkini', 'Perkembangan teknologi terus berkembang dengan pesat. Kami menyajikan update terbaru tentang inovasi teknologi yang mengubah dunia.', 'Teknologi', '', NOW() - INTERVAL 6 HOUR, 'published'),
        ('Berita Ekonomi', 'Pertumbuhan ekonomi Indonesia menunjukkan tren positif. Berbagai sektor industri mulai berkembang dengan signifikan.', 'Ekonomi', '', NOW() - INTERVAL 12 HOUR, 'published'),
        ('Olahraga Nasional', 'Tim sepak bola Indonesia berhasil meraih kemenangan besar di level internasional. Prestasi ini menjadi kebanggaan bagi seluruh masyarakat.', 'Olahraga', '', NOW() - INTERVAL 20 HOUR, 'published')";

    if ($conn->query($sql_insert)) {
        echo "✅ Data dummy berhasil ditambahkan (4 berita)<br>";
    } else {
        echo "⚠️ Error menambah data dummy: " . $conn->error . "<br>";
    }
} else {
    echo "ℹ️ Database sudah berisi " . $row['total'] . " berita<br>";
}

$conn->close();

echo "<hr>";
echo "<h2>✅ Setup Selesai!</h2>";
echo "<p>Database dan tabel sudah siap digunakan.</p>";
echo "<p><a href='index.php' style='background: #667eea; color: white; padding: 10px 20px; text-decoration: none; border-radius: 4px;'>➜ Buka Website</a></p>";
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Setup Website Berita</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 600px;
            margin: 50px auto;
            padding: 20px;
            background: #f5f5f5;
        }
        body > * {
            background: white;
            padding: 20px;
            border-radius: 8px;
            margin: 10px 0;
        }
    </style>
</head>
<body>
</body>
</html>
