<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Test Timezone & Berita</title>
    <style>
        body { font-family: Arial; padding: 20px; background: #f5f5f5; }
        .box { background: white; padding: 20px; margin: 10px 0; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        h2 { color: #1a237e; }
        .status { padding: 10px; border-radius: 4px; margin: 10px 0; }
        .success { background: #d4edda; color: #155724; }
        .info { background: #d1ecf1; color: #0c5460; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        td { padding: 8px; border-bottom: 1px solid #ddd; }
        td:first-child { font-weight: bold; width: 200px; }
    </style>
</head>
<body>
    <h1>🔍 Test Timezone & Berita</h1>
    
    <?php
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
    
    // Set timezone
    date_default_timezone_set('Asia/Jakarta');
    
    // Load berita dari cache
    $cache_file = __DIR__ . '/cache/karanganyar_news.json';
    $berita_list = [];
    
    if (file_exists($cache_file)) {
        $berita_list = json_decode(file_get_contents($cache_file), true);
    }
    ?>
    
    <div class="box">
        <h2>📅 Timezone & Waktu Server</h2>
        <table>
            <tr>
                <td>Timezone PHP:</td>
                <td><strong><?php echo date_default_timezone_get(); ?></strong></td>
            </tr>
            <tr>
                <td>Waktu Server Sekarang:</td>
                <td><strong><?php echo date('Y-m-d H:i:s'); ?></strong></td>
            </tr>
            <tr>
                <td>Format Tampilan:</td>
                <td><strong><?php echo date('d M Y - H:i'); ?></strong></td>
            </tr>
        </table>
    </div>
    
    <div class="box">
        <h2>📰 Status Berita</h2>
        <div class="status <?php echo count($berita_list) > 0 ? 'success' : 'error'; ?>">
            <?php if (count($berita_list) > 0): ?>
                ✅ <strong><?php echo count($berita_list); ?> berita</strong> berhasil dimuat dari cache
            <?php else: ?>
                ❌ Tidak ada berita di cache
            <?php endif; ?>
        </div>
        
        <?php if (file_exists($cache_file)): ?>
            <table>
                <tr>
                    <td>File Cache:</td>
                    <td><?php echo basename($cache_file); ?></td>
                </tr>
                <tr>
                    <td>Terakhir diupdate:</td>
                    <td><?php echo date('Y-m-d H:i:s', filemtime($cache_file)); ?></td>
                </tr>
                <tr>
                    <td>Umur cache:</td>
                    <td><?php echo round((time() - filemtime($cache_file)) / 60, 1); ?> menit</td>
                </tr>
            </table>
        <?php endif; ?>
    </div>
    
    <?php if (count($berita_list) > 0): ?>
        <div class="box">
            <h2>📝 Berita Terbaru (5 teratas)</h2>
            <table>
                <tr style="background: #f8f9fa; font-weight: bold;">
                    <td>Tanggal Publikasi</td>
                    <td>Judul</td>
                    <td>Sumber</td>
                </tr>
                <?php $count = 0; ?>
                <?php foreach ($berita_list as $berita): ?>
                    <?php if ($count >= 5) break; ?>
                    <tr>
                        <td style="white-space: nowrap;">
                            <?php 
                            if ($berita['timestamp'] > 0) {
                                echo '<strong>' . date('d M Y - H:i', $berita['timestamp']) . '</strong>';
                            } else {
                                echo htmlspecialchars($berita['tanggal_publikasi']);
                            }
                            ?>
                        </td>
                        <td><?php echo htmlspecialchars(substr($berita['judul'], 0, 80)); ?></td>
                        <td><?php echo htmlspecialchars($berita['sumber']); ?></td>
                    </tr>
                    <?php $count++; ?>
                <?php endforeach; ?>
            </table>
        </div>
        
        <div class="box">
            <div class="status success">
                ✅ Timezone: <strong>Asia/Jakarta</strong> sudah terkonfigurasi dengan benar<br>
                ✅ Berita: Sudah terupdate dengan <strong><?php echo count($berita_list); ?> artikel</strong><br>
                ✅ Format waktu: <strong><?php echo date('d M Y - H:i', $berita_list[0]['timestamp']); ?></strong>
            </div>
        </div>
    <?php endif; ?>
    
    <div class="box">
        <h2>🔗 Quick Links</h2>
        <p>
            <a href="index.php" style="padding: 8px 16px; background: #1a237e; color: white; text-decoration: none; border-radius: 4px; display: inline-block;">
                ← Kembali ke Halaman Utama
            </a>
        </p>
    </div>
</body>
</html>
