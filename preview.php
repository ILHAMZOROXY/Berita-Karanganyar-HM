<?php
/**
 * PREVIEW - Struktur tampilan berita yang sudah diperbaiki
 * File ini menunjukkan struktur UI dari widget berita
 */
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Preview UI Berita - Website Berita Karanganyar</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f5f5f5;
            padding: 20px;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
        }

        h1 {
            color: #667eea;
            margin-bottom: 10px;
            text-align: center;
        }

        .subtitle {
            text-align: center;
            color: #666;
            margin-bottom: 30px;
        }

        .preview-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 25px;
            margin-bottom: 40px;
        }

        .preview-card {
            background: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            transition: transform 0.3s, box-shadow 0.3s;
            display: flex;
            flex-direction: column;
            height: 100%;
        }

        .preview-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 8px 20px rgba(102, 126, 234, 0.2);
        }

        .preview-image {
            width: 100%;
            height: 180px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 0.95rem;
            font-weight: 500;
            padding: 15px;
            text-align: center;
        }

        .preview-content {
            padding: 20px;
            flex: 1;
            display: flex;
            flex-direction: column;
        }

        .preview-content h2 {
            font-size: 1.15rem;
            margin-bottom: 10px;
            color: #1a1a1a;
            font-weight: 600;
            line-height: 1.4;
        }

        .preview-meta {
            font-size: 0.8rem;
            color: #888;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 1px solid #eee;
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
        }

        .preview-content p {
            color: #666;
            margin-bottom: 15px;
            display: -webkit-box;
            -webkit-line-clamp: 3;
            line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
            font-size: 0.95rem;
            line-height: 1.5;
        }

        .preview-btn {
            display: inline-block;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 10px 15px;
            text-decoration: none;
            border-radius: 4px;
            font-weight: 500;
            font-size: 0.9rem;
            margin-top: auto;
            width: fit-content;
            transition: opacity 0.3s, transform 0.2s;
        }

        .preview-btn:hover {
            opacity: 0.9;
            transform: translateX(3px);
        }

        .legend {
            background: white;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 30px;
            border-left: 4px solid #667eea;
        }

        .legend h3 {
            color: #667eea;
            margin-bottom: 15px;
        }

        .legend-item {
            margin-bottom: 10px;
            padding: 10px;
            background: #f9f9f9;
            border-radius: 4px;
        }

        .legend-item strong {
            color: #667eea;
        }

        .info {
            background: #e8f4f8;
            border-left: 4px solid #667eea;
            padding: 15px;
            margin-top: 20px;
            border-radius: 4px;
        }

        @media (max-width: 768px) {
            .preview-grid {
                grid-template-columns: 1fr;
            }
            .preview-image { height: 150px; }
            .preview-content { padding: 15px; }
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>📰 Preview UI Berita - Struktur Terbaru</h1>
        <p class="subtitle">Tampilan widget berita yang sudah diperbaiki</p>

        <div class="legend">
            <h3>✨ Perubahan UI yang Sudah Dilakukan:</h3>
            <div class="legend-item">
                <strong>1. Header Berita (Warna Gradient)</strong> - Menampilkan nama sumber media dengan background gradient biru-ungu
            </div>
            <div class="legend-item">
                <strong>2. Judul Berita (Bold)</strong> - Judul yang jelas dan mudah dibaca dengan font weight 600
            </div>
            <div class="legend-item">
                <strong>3. Meta Information</strong> - Sumber media + Tanggal publikasi dengan separator garis
            </div>
            <div class="legend-item">
                <strong>4. Deskripsi Berita (3 baris)</strong> - Preview konten dengan max 3 baris teks
            </div>
            <div class="legend-item">
                <strong>5. Tombol Baca</strong> - CTA button untuk membuka berita di sumber asli
            </div>
            <div class="legend-item">
                <strong>6. Card Effects</strong> - Hover effect dengan shadow yang lebih halus
            </div>
            <div class="legend-item">
                <strong>7. Responsive</strong> - Responsive di semua ukuran dengan grid yang adjust
            </div>
        </div>

        <h2 style="margin-bottom: 20px; color: #333;">Contoh Tampilan Berita:</h2>
        
        <div class="preview-grid">
            <!-- Sample 1 -->
            <div class="preview-card">
                <div class="preview-image">
                    📰 Kompas.com
                </div>
                <div class="preview-content">
                    <h2>Sebab Pendakian Gunung Lawu via Karanganyar Ditutup Februari 2026</h2>
                    <div class="preview-meta">
                        <span>📰 Kompas.com</span>
                        <span>🕐 27 Jan 2026 - 00:06</span>
                    </div>
                    <p>Sebab Pendakian Gunung Lawu via Karanganyar Ditutup Februari 2026 - Kompas. Jalur pendakian Gunung Lawu melalui Desa Sukuh, Karanganyar, Jawa Tengah ditutup pada bulan Februari 2026 untuk keperluan...</p>
                    <a href="#" class="preview-btn">Baca di Sumber →</a>
                </div>
            </div>

            <!-- Sample 2 -->
            <div class="preview-card">
                <div class="preview-image">
                    📰 Radar Semarang
                </div>
                <div class="preview-content">
                    <h2>Hari Ke 8 Pendaki Hilang di Bukit Mongkrang Gunung Lawu, Pencarian Dimulai dengan Sholat Istianah</h2>
                    <div class="preview-meta">
                        <span>📰 Radar Semarang</span>
                        <span>🕐 26 Jan 2026 - 23:29</span>
                    </div>
                    <p>Hari Ke 8 Pendaki Hilang di Bukit Mongkrang Gunung Lawu - Pencarian terhadap seorang pendaki asal Surabaya yang hilang di area Bukit Mongkrang, Gunung Lawu, Karanganyar terus dilakukan...</p>
                    <a href="#" class="preview-btn">Baca di Sumber →</a>
                </div>
            </div>

            <!-- Sample 3 -->
            <div class="preview-card">
                <div class="preview-image">
                    📰 TribunWow.com
                </div>
                <div class="preview-content">
                    <h2>Daftar 8 Besar Liga 3 2025/2026: Persika Karanganyar, RANS Nusantara, Persiba Bantul</h2>
                    <div class="preview-meta">
                        <span>📰 TribunWow.com</span>
                        <span>🕐 26 Jan 2026 - 23:25</span>
                    </div>
                    <p>Daftar 8 Besar Liga 3 2025/2026 telah diumumkan dengan adanya beberapa klub dari berbagai daerah. Persika Karanganyar, RANS Nusantara, dan Persiba Bantul menjadi beberapa nama yang lolos...</p>
                    <a href="#" class="preview-btn">Baca di Sumber →</a>
                </div>
            </div>
        </div>

        <div class="info">
            <strong>ℹ️ Catatan:</strong> 
            <p style="margin-top: 10px;">
                Tampilan widget berita di atas adalah struktur yang sudah diperbaiki. Setiap card akan menampilkan:
            </p>
            <ul style="margin-top: 10px; margin-left: 20px;">
                <li>Header dengan background gradient (nama sumber media)</li>
                <li>Judul berita yang jelas dan bold</li>
                <li>Meta info (sumber + tanggal)</li>
                <li>Deskripsi berita (max 3 baris)</li>
                <li>Tombol "Baca di Sumber" dengan link ke berita asli</li>
                <li>Hover effect yang smooth</li>
            </ul>
            <p style="margin-top: 15px;">
                <strong>Akses halaman berita:</strong> <a href="index.php" style="color: #667eea; text-decoration: none; font-weight: bold;">http://localhost/berita/index.php</a>
            </p>
        </div>
    </div>
</body>
</html>
