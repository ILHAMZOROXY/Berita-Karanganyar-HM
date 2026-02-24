<?php
/**
 * Info Page - Panduan Lengkap Website Berita Karanganyar
 */
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Info & Panduan - Website Berita Karanganyar</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/social-icons.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: #333;
            line-height: 1.6;
            padding: 20px;
        }
        .container {
            max-width: 900px;
            margin: 0 auto;
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 40px;
            text-align: center;
        }
        .header h1 {
            font-size: 2.5rem;
            margin-bottom: 10px;
        }
        .header p {
            font-size: 1.1rem;
            opacity: 0.9;
        }
        .content {
            padding: 40px;
        }
        .section {
            margin-bottom: 40px;
        }
        .section h2 {
            color: #667eea;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #667eea;
        }
        .section h3 {
            color: #764ba2;
            margin-top: 20px;
            margin-bottom: 10px;
        }
        .step-box {
            background: #f0f4ff;
            border-left: 4px solid #667eea;
            padding: 20px;
            margin: 15px 0;
            border-radius: 4px;
        }
        .step-number {
            display: inline-block;
            background: #667eea;
            color: white;
            width: 30px;
            height: 30px;
            border-radius: 50%;
            text-align: center;
            line-height: 30px;
            margin-right: 10px;
            font-weight: bold;
        }
        .file-list {
            background: #f9f9f9;
            border: 1px solid #ddd;
            border-radius: 4px;
            overflow: hidden;
        }
        .file-item {
            padding: 15px;
            border-bottom: 1px solid #eee;
            display: flex;
            align-items: center;
        }
        .file-item:last-child {
            border-bottom: none;
        }
        .file-icon {
            font-size: 1.5rem;
            margin-right: 15px;
            min-width: 30px;
        }
        .file-info {
            flex: 1;
        }
        .file-name {
            font-weight: bold;
            color: #667eea;
            font-family: monospace;
            margin-bottom: 5px;
        }
        .file-desc {
            font-size: 0.9rem;
            color: #666;
        }
        .link-box {
            background: #e8f4f8;
            border-left: 4px solid #667eea;
            padding: 15px;
            margin: 15px 0;
            border-radius: 4px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .link-box a {
            background: #667eea;
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 4px;
            transition: background 0.3s;
        }
        .link-box a:hover {
            background: #764ba2;
        }
        .feature-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin: 20px 0;
        }
        .feature-card {
            background: #f0f4ff;
            padding: 20px;
            border-radius: 8px;
            border: 1px solid #e0e8ff;
        }
        .feature-card h4 {
            color: #667eea;
            margin-bottom: 10px;
            font-size: 1.1rem;
        }
        .feature-card p {
            font-size: 0.95rem;
            color: #666;
        }
        .footer {
            background: #f5f5f5;
            padding: 20px 40px;
            text-align: center;
            border-top: 1px solid #ddd;
            font-size: 0.9rem;
            color: #999;
        }
        @media (max-width: 768px) {
            .header h1 { font-size: 1.8rem; }
            .feature-grid { grid-template-columns: 1fr; }
            .content { padding: 20px; }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>📰 Website Berita Karanganyar</h1>
            <p>Panduan Lengkap & Quick Start</p>
        </div>

        <div class="content">
            <!-- Quick Start -->
            <div class="section">
                <h2>⚡ Quick Start (3 Langkah)</h2>
                
                <div class="step-box">
                    <span class="step-number">1</span>
                    <strong>Verifikasi System</strong>
                    <div style="margin-left: 40px; margin-top: 10px;">
                        Buka <a href="check_system.php" target="_blank">check_system.php</a> untuk memastikan server support.
                    </div>
                </div>

                <div class="step-box">
                    <span class="step-number">2</span>
                    <strong>Setup Database (Opsional)</strong>
                    <div style="margin-left: 40px; margin-top: 10px;">
                        Buka <a href="setup.php" target="_blank">setup.php</a> untuk setup database lokal sebagai fallback.
                    </div>
                </div>

                <div class="step-box">
                    <span class="step-number">3</span>
                    <strong>Buka Website!</strong>
                    <div style="margin-left: 40px; margin-top: 10px;">
                        Klik <a href="index.php" target="_blank">Kembali ke Beranda</a> untuk lihat berita.
                    </div>
                </div>
            </div>

            <!-- Fitur Utama -->
            <div class="section">
                <h2>✨ Fitur Utama</h2>
                
                <div class="feature-grid">
                    <div class="feature-card">
                        <h4>🌐 Google News Integration</h4>
                        <p>Ambil berita real-time tentang Karanganyar dari Google News RSS Feed</p>
                    </div>
                    <div class="feature-card">
                        <h4>⏰ Auto Filter 24 Jam</h4>
                        <p>Hanya menampilkan berita dari 24 jam terakhir secara otomatis</p>
                    </div>
                    <div class="feature-card">
                        <h4>🔗 Direct Link</h4>
                        <p>Setiap berita langsung link ke artikel asli di media penyedia</p>
                    </div>
                    <div class="feature-card">
                        <h4>💾 Smart Cache</h4>
                        <p>Cache cerdas selama 1 jam untuk performa lebih cepat</p>
                    </div>
                    <div class="feature-card">
                        <h4>🔄 Fallback System</h4>
                        <p>Jika API error, otomatis fallback ke database lokal</p>
                    </div>
                    <div class="feature-card">
                        <h4>📱 Responsive Design</h4>
                        <p>Tampilan sempurna di desktop, tablet, dan mobile</p>
                    </div>
                </div>
            </div>

            <!-- File Structure -->
            <div class="section">
                <h2>📁 Struktur File</h2>
                
                <div class="file-list">
                    <div class="file-item">
                        <div class="file-icon">📄</div>
                        <div class="file-info">
                            <div class="file-name">index.php</div>
                            <div class="file-desc">Halaman utama - menampilkan berita Karanganyar</div>
                        </div>
                    </div>
                    <div class="file-item">
                        <div class="file-icon">📄</div>
                        <div class="file-info">
                            <div class="file-name">config/news_fetcher.php</div>
                            <div class="file-desc">Class untuk fetch berita dari Google News RSS Feed</div>
                        </div>
                    </div>
                    <div class="file-item">
                        <div class="file-icon">📄</div>
                        <div class="file-info">
                            <div class="file-name">config/database.php</div>
                            <div class="file-desc">Konfigurasi database MySQL dengan error handling</div>
                        </div>
                    </div>
                    <div class="file-item">
                        <div class="file-icon">📄</div>
                        <div class="file-info">
                            <div class="file-name">refresh_news.php</div>
                            <div class="file-desc">API endpoint untuk refresh/update berita</div>
                        </div>
                    </div>
                    <div class="file-item">
                        <div class="file-icon">📄</div>
                        <div class="file-info">
                            <div class="file-name">check_system.php</div>
                            <div class="file-desc">Verifikasi system requirements</div>
                        </div>
                    </div>
                    <div class="file-item">
                        <div class="file-icon">📄</div>
                        <div class="file-info">
                            <div class="file-name">setup.php</div>
                            <div class="file-desc">Setup database dan data dummy</div>
                        </div>
                    </div>
                    <div class="file-item">
                        <div class="file-icon">📂</div>
                        <div class="file-info">
                            <div class="file-name">cache/</div>
                            <div class="file-desc">Folder untuk menyimpan cache berita JSON</div>
                        </div>
                    </div>
                    <div class="file-item">
                        <div class="file-icon">📂</div>
                        <div class="file-info">
                            <div class="file-name">css/</div>
                            <div class="file-desc">Folder stylesheet dan styling</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Links Penting -->
            <div class="section">
                <h2>🔗 Link Penting</h2>
                
                <div class="link-box">
                    <div>
                        <strong>Halaman Utama</strong>
                        <p style="font-size: 0.9rem; color: #666; margin-top: 5px;">Tampilkan berita Karanganyar</p>
                    </div>
                    <a href="index.php">Buka →</a>
                </div>

                <div class="link-box">
                    <div>
                        <strong>System Check</strong>
                        <p style="font-size: 0.9rem; color: #666; margin-top: 5px;">Verifikasi requirements</p>
                    </div>
                    <a href="check_system.php">Buka →</a>
                </div>

                <div class="link-box">
                    <div>
                        <strong>Setup Database</strong>
                        <p style="font-size: 0.9rem; color: #666; margin-top: 5px;">Setup database lokal (opsional)</p>
                    </div>
                    <a href="setup.php">Buka →</a>
                </div>

                <div class="link-box">
                    <div>
                        <strong>Refresh API</strong>
                        <p style="font-size: 0.9rem; color: #666; margin-top: 5px;">Update berita (JSON response)</p>
                    </div>
                    <a href="refresh_news.php">Buka →</a>
                </div>

                <div class="link-box">
                    <div>
                        <strong>Panel Admin</strong>
                        <p style="font-size: 0.9rem; color: #666; margin-top: 5px;">Manage berita lokal</p>
                    </div>
                    <a href="admin.php">Buka →</a>
                </div>
            </div>

            <!-- FAQ -->
            <div class="section">
                <h2>❓ Pertanyaan Umum</h2>
                
                <h3>❓ Berita tidak muncul, apa yang harus dilakukan?</h3>
                <p>
                    1. Buka <strong>check_system.php</strong> untuk verifikasi<br>
                    2. Cek koneksi internet<br>
                    3. Klik tombol "Refresh" di halaman utama<br>
                    4. Tunggu ~5 detik untuk fetch data
                </p>

                <h3>❓ Bagaimana cara ganti keyword pencarian?</h3>
                <p>Edit file <strong>config/news_fetcher.php</strong>, cari baris dengan <code>Karanganyar</code> dan ganti dengan keyword yang diinginkan.</p>

                <h3>❓ Berapa lama cache berita tersimpan?</h3>
                <p>Cache disimpan selama 1 jam (3600 detik). Bisa diubah di <strong>config/news_fetcher.php</strong>.</p>

            <?php include __DIR__ . '/partials/floating_widget.php'; ?>
                <h3>❓ Apakah perlu API key Google?</h3>
                <p>Tidak, website menggunakan Google News RSS Feed yang tidak perlu API key.</p>

                <h3>❓ Bagaimana jika Google News error?</h3>
                <p>Website akan otomatis fallback ke database lokal (jika sudah di-setup).</p>
            </div>

            <!-- Tips -->
            <div class="section">
                <h2>💡 Tips & Trik</h2>
                
                <h3>Customize Warna Tema</h3>
                <p>Edit <strong>css/style.css</strong>, cari gradient color dan ubah sesuai keinginan.</p>

                <h3>Ubah Durasi Cache</h3>
                <p>Edit <strong>config/news_fetcher.php</strong>, ubah nilai <code>cache_time</code>.</p>

                <h3>Tambah Berita Lokal</h3>
                <p>Buka <strong>admin.php</strong> untuk menambah berita lokal sebagai fallback.</p>

                <h3>Check Error Log</h3>
                <p>Buka browser DevTools (F12) dan cek tab Console untuk error details.</p>
            </div>
        </div>

        <?php
        $footer_show_map = false;
        $footer_year = 2026;
        include __DIR__ . '/partials/site_footer.php';
        ?>
    </div>
</body>
</html>
