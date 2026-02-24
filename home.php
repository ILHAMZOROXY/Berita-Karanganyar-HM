<!-- 
    WEBSITE BERITA KARANGANYAR - INDEX PAGE
    Halaman ini adalah landing page dengan navigasi lengkap
-->
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Website Berita Karanganyar - Home</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/social-icons.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: #333;
            line-height: 1.6;
            min-height: 100vh;
        }
        
        .hero {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 80px 20px;
            text-align: center;
        }
        
        .hero h1 {
            font-size: 3rem;
            margin-bottom: 20px;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.2);
        }
        
        .hero p {
            font-size: 1.3rem;
            margin-bottom: 40px;
            opacity: 0.95;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }
        
        .quick-links {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin: 50px 0;
            padding: 0 20px;
        }
        
        .card {
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.15);
            text-align: center;
            transition: transform 0.3s, box-shadow 0.3s;
        }
        
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0,0,0,0.25);
        }
        
        .card h3 {
            color: #667eea;
            margin-bottom: 10px;
            font-size: 1.3rem;
        }
        
        .card p {
            color: #666;
            margin-bottom: 20px;
            font-size: 0.95rem;
        }
        
        .card a {
            display: inline-block;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 12px 30px;
            text-decoration: none;
            border-radius: 6px;
            font-weight: bold;
            transition: opacity 0.3s;
        }
        
        .card a:hover {
            opacity: 0.9;
        }
        
        .emoji {
            font-size: 2.5rem;
            margin-bottom: 10px;
        }
        
        .features {
            background: white;
            padding: 50px 20px;
            margin: 50px 0;
            border-radius: 12px;
        }
        
        .features h2 {
            text-align: center;
            color: #667eea;
            margin-bottom: 40px;
            font-size: 2rem;
        }
        
        .feature-list {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
            max-width: 1000px;
            margin: 0 auto;
        }
        
        .feature-item {
            display: flex;
            align-items: flex-start;
            gap: 15px;
        }
        
        .feature-item .icon {
            font-size: 1.8rem;
            margin-top: 5px;
        }
        
        .feature-item h4 {
            color: #333;
            margin-bottom: 5px;
        }
        
        .feature-item p {
            color: #666;
            font-size: 0.95rem;
        }
        
        .footer {
            background: rgba(0,0,0,0.5);
            color: white;
            text-align: center;
            padding: 30px 20px;
            margin-top: 50px;
        }
        
        .status {
            background: #d4edda;
            color: #155724;
            padding: 15px;
            border-radius: 6px;
            margin: 20px 0;
            border-left: 4px solid #28a745;
        }
        
        @media (max-width: 768px) {
            .hero h1 { font-size: 2rem; }
            .hero p { font-size: 1.1rem; }
            .feature-list { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
    <!-- Hero Section -->
    <div class="hero">
        <div class="container">
            <h1>📰 Website Berita Karanganyar</h1>
            <p>Berita terbaru Kabupaten Karanganyar dari Google News</p>
        </div>
    </div>
    
    <!-- Status -->
    <div class="container">
        <div class="status">
            ✅ <strong>Status:</strong> Website siap digunakan! Semua fitur telah diimplementasikan.
        </div>
    </div>
    
    <!-- Quick Links -->
    <div class="container">
        <div class="quick-links">
            <div class="card">
                <div class="emoji">📰</div>
                <h3>Lihat Berita</h3>
                <p>Baca berita terbaru Karanganyar dari Google News</p>
                <a href="index.php">Buka →</a>
            </div>
            
            <div class="card">
                <div class="emoji">ℹ️</div>
                <h3>Panduan Lengkap</h3>
                <p>Baca panduan lengkap dan tutorial setup</p>
                <a href="info.php">Buka →</a>
            </div>
            
            <div class="card">
                <div class="emoji">🔍</div>
                <h3>Cek System</h3>
                <p>Verifikasi kebutuhan system & requirements</p>
                <a href="check_system.php">Buka →</a>
            </div>
            
            <div class="card">
                <div class="emoji">🔧</div>
                <h3>Setup Database</h3>
                <p>Setup database lokal (opsional)</p>
                <a href="setup.php">Buka →</a>
            </div>
            
            <div class="card">
                <div class="emoji">⚙️</div>
                <h3>Panel Admin</h3>
                <p>Kelola berita lokal dan database</p>
                <a href="admin.php">Buka →</a>
            </div>
            
            <div class="card">
                <div class="emoji">🔄</div>
                <h3>Refresh API</h3>
                <p>Endpoint untuk refresh berita JSON</p>
                <a href="refresh_news.php">Test →</a>
            </div>
        </div>
    </div>
    
    <!-- Features -->
    <div class="features">
        <h2>✨ Fitur Unggulan</h2>
        <div class="feature-list">
            <div class="feature-item">
                <div class="icon">🌐</div>
                <div>
                    <h4>Google News Integration</h4>
                    <p>Ambil berita real-time dari Google News tanpa API key</p>
                </div>
            </div>
            
            <div class="feature-item">
                <div class="icon">⏰</div>
                <div>
                    <h4>Filter 24 Jam Otomatis</h4>
                    <p>Hanya menampilkan berita dari 24 jam terakhir</p>
                </div>
            </div>
            
            <div class="feature-item">
                <div class="icon">🔗</div>
                <div>
                    <h4>Direct Link ke Sumber</h4>
                    <p>Setiap berita langsung link ke artikel asli</p>
                </div>
            </div>
            
            <div class="feature-item">
                <div class="icon">💾</div>
                <div>
                    <h4>Smart Cache System</h4>
                    <p>Cache 1 jam untuk performa lebih cepat</p>
                </div>
            </div>
            
            <div class="feature-item">
                <div class="icon">🔄</div>
                <div>
                    <h4>Fallback System</h4>
                    <p>Otomatis fallback ke database lokal jika API error</p>
                </div>
            </div>
            
            <div class="feature-item">
                <div class="icon">📱</div>
                <div>
                    <h4>Responsive Design</h4>
                    <p>Tampilan sempurna di desktop, tablet, dan mobile</p>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Documentation -->
    <div class="container">
        <h2 style="text-align: center; color: white; margin: 40px 0 20px;">📚 Dokumentasi</h2>
        <div class="quick-links" style="margin-bottom: 50px;">
            <div class="card" style="background: rgba(255,255,255,0.95);">
                <h3>📖 README.md</h3>
                <p>Dokumentasi dasar website</p>
                <a href="#" onclick="alert('Buka file README.md di folder root'); return false;">Baca →</a>
            </div>
            
            <div class="card" style="background: rgba(255,255,255,0.95);">
                <h3>🚀 QUICKSTART.md</h3>
                <p>Panduan cepat setup website</p>
                <a href="#" onclick="alert('Buka file QUICKSTART.md di folder root'); return false;">Baca →</a>
            </div>
            
            <div class="card" style="background: rgba(255,255,255,0.95);">
                <h3>✨ UPDATES.md</h3>
                <p>Detail fitur v2.0 terbaru</p>
                <a href="#" onclick="alert('Buka file UPDATES.md di folder root'); return false;">Baca →</a>
            </div>
        </div>
    </div>
    
    <?php
    $footer_show_map = false;
    $footer_year = 2026;
    include __DIR__ . '/partials/site_footer.php';
    ?>

<?php include __DIR__ . '/partials/floating_widget.php'; ?>
</body>
</html>
