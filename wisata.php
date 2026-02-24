<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Wisata Karanganyar</title>
    <?php $asset_ver = @filemtime(__DIR__ . '/css/style.css') ?: time(); ?>
    <link rel="stylesheet" href="css/style.css?v=<?php echo $asset_ver; ?>">
    <link rel="stylesheet" href="css/social-icons.css">
</head>
<body>
        <!-- Topbar: Social & Search -->
        <div style="background:#16245c; border-bottom:3px solid #f58220; padding:0;">
            <div class="container" style="display:flex;align-items:center;justify-content:space-between;min-height:44px;">
                <div style="display:flex;align-items:center;gap:18px;">
                    <!-- Instagram -->
                    <a href="https://www.instagram.com/diskominfo_karanganyar?igsh=dGV2N3FqMnNjMDJq" target="_blank" rel="noopener" class="social-icon social-icon-instagram" aria-label="Instagram" title="Instagram">
                        <img src="img/instagram.png" alt="Instagram">
                    </a>
                    <!-- X (Twitter) -->
                    <a href="https://x.com/karanganyarkab" target="_blank" rel="noopener" class="social-icon social-icon-x" aria-label="X" title="X">
                        <img src="img/twitter.png" alt="X">
                    </a>
                    <!-- Facebook -->
                    <a href="https://www.facebook.com/diskominfo.kra/" target="_blank" rel="noopener" class="social-icon social-icon-facebook" aria-label="Facebook" title="Facebook">
                        <img src="img/facebook.png" alt="Facebook">
                    </a>
                    <!-- YouTube -->
                    <a href="https://www.youtube.com/@KabKaranganyar" target="_blank" rel="noopener" class="social-icon social-icon-youtube" aria-label="YouTube" title="YouTube">
                        <img src="img/youtube.png" alt="YouTube">
                    </a>
                    <!-- TikTok -->
                    <a href="https://www.tiktok.com/@diskominfokaranganyar" target="_blank" rel="noopener" class="social-icon social-icon-tiktok" aria-label="TikTok" title="TikTok">
                        <img src="img/tiktok.png" alt="TikTok">
                    </a>
                </div>
                <form action="wisata.php" method="get" style="display:flex;align-items:center;">
                    <input type="text" id="topbar-search" name="q" placeholder="Cari destinasi wisata Karanganyar..." style="border:none;border-radius:22px 0 0 22px;padding:8px 18px;font-size:1em;outline:none;width:200px;">
                    <button type="submit" style="background:#f58220;border:none;border-radius:0 22px 22px 0;padding:8px 16px;color:#fff;font-size:1.2em;cursor:pointer;"><span style="font-size:1.1em;">&#128269;</span></button>
                </form>
            </div>
        </div>
        <!-- Main Header: Logo & Menu -->
        <header style="background:#fff;box-shadow:0 2px 8px rgba(0,0,0,0.04);border-bottom:2px solid #f58220;position:static;top:auto;z-index:auto;">
            <div class="container" style="display:flex;align-items:center;justify-content:space-between;padding:4px 0;">
                <div style="display:flex;align-items:center;gap:12px;">
                    <img src="img/kominfo.png" alt="Logo Kominfo" style="height:40px;width:auto;display:block;object-fit:contain;background:transparent;border-radius:0;padding:0;box-shadow:none;">
                    <div>
                        <span style="font-size:1.55rem;font-weight:900;color:#193a7b;letter-spacing:0.4px;">Berita Karanganyar</span><br>
                        <span style="font-size:0.82rem;color:#f58220;font-weight:600;">Portal Berita Resmi Pemerintah Daerah</span>
                    </div>
                </div>
                <a href="index.php" style="background:linear-gradient(135deg,#f58220 0%,#fbb040 100%);color:#fff;border:none;padding:6px 14px;border-radius:8px;cursor:pointer;font-weight:700;font-size:0.9rem;box-shadow:0 2px 8px rgba(0,0,0,0.10);display:flex;align-items:center;gap:8px;transition:all 0.3s ease;text-decoration:none;">← Kembali ke Berita</a>
            </div>
        </header>

        <!-- Navigation Bar -->
        <nav style="background:#fff;border-bottom:2px solid #f58220;box-shadow:0 1px 4px rgba(0,0,0,0.03);position:static;top:auto;z-index:auto;">
            <div class="container">
                <ul style="display:flex;gap:14px;margin:0;padding:0;list-style:none;font-size:0.9rem;font-weight:700;align-items:center;min-height:26px;">
                    <li><a href="index.php" style="color:#193a7b;text-decoration:none;padding:5px 0 3px 0;display:inline-block;">Beranda</a></li>
                    <li><a href="arsip.php" style="color:#193a7b;text-decoration:none;padding:5px 0 3px 0;display:inline-block;">Arsip Berita</a></li>
                    <li><a href="wisata.php" class="nav-active" style="color:#193a7b;text-decoration:none;padding:5px 0 3px 0;display:inline-block;">Wisata</a></li>
                    <li style="position:relative;">
                        <a href="#" style="color:#193a7b;text-decoration:none;padding:5px 0 3px 0;display:inline-block;">Profile &#9662;</a>
                        <ul style="display:none;position:absolute;left:0;top:100%;background:#fff;color:#193a7b;min-width:210px;box-shadow:0 4px 16px rgba(0,0,0,0.10);border-radius:0 0 8px 8px;z-index:10;list-style:none;padding:8px 0;">
                            <li><a href="https://diskominfo.karanganyarkab.go.id/struktur-organisasi/" target="_blank" style="color:#193a7b;display:block;padding:8px 18px;">Struktur Organisasi</a></li>
                            <li><a href="https://diskominfo.karanganyarkab.go.id/tugas-dan-fungsi/" target="_blank" style="color:#193a7b;display:block;padding:8px 18px;">Tugas & Fungsi</a></li>
                            <li><a href="https://diskominfo.karanganyarkab.go.id/visi-misi/" target="_blank" style="color:#193a7b;display:block;padding:8px 18px;">Visi & Misi</a></li>
                            <li><a href="https://diskominfo.karanganyarkab.go.id/pejabat-struktural/" target="_blank" style="color:#193a7b;display:block;padding:8px 18px;">Pejabat Struktural</a></li>
                            <li><a href="admin.php" style="color:#193a7b;display:block;padding:8px 18px;">Admin</a></li>
                        </ul>
                    </li>
                    <li style="position:relative;">
                        <a href="#" style="color:#193a7b;text-decoration:none;padding:5px 0 3px 0;display:inline-block;">Galeri &#9662;</a>
                        <ul style="display:none;position:absolute;left:0;top:100%;background:#fff;color:#193a7b;min-width:180px;box-shadow:0 4px 16px rgba(0,0,0,0.10);border-radius:0 0 8px 8px;z-index:10;list-style:none;padding:8px 0;">
                            <li><a href="https://diskominfo.karanganyarkab.go.id/galeri/foto/" target="_blank" style="color:#193a7b;display:block;padding:8px 18px;">Foto</a></li>
                            <li><a href="https://diskominfo.karanganyarkab.go.id/galeri/video/" target="_blank" style="color:#193a7b;display:block;padding:8px 18px;">Video</a></li>
                            <li><a href="https://diskominfo.karanganyarkab.go.id/galeri/infografis/" target="_blank" style="color:#193a7b;display:block;padding:8px 18px;">Infografis</a></li>
                        </ul>
                    </li>
                    <li style="position:relative;">
                        <a href="#" style="color:#193a7b;text-decoration:none;padding:5px 0 3px 0;display:inline-block;">Informasi &#9662;</a>
                        <ul style="display:none;position:absolute;left:0;top:100%;background:#fff;color:#193a7b;min-width:180px;box-shadow:0 4px 16px rgba(0,0,0,0.10);border-radius:0 0 8px 8px;z-index:10;list-style:none;padding:8px 0;">
                            <li><a href="https://diskominfo.karanganyarkab.go.id/informasi/pengumuman/" target="_blank" style="color:#193a7b;display:block;padding:8px 18px;">Pengumuman</a></li>
                            <li><a href="https://diskominfo.karanganyarkab.go.id/informasi/artikel/" target="_blank" style="color:#193a7b;display:block;padding:8px 18px;">Artikel</a></li>
                            <li><a href="https://diskominfo.karanganyarkab.go.id/informasi/agenda/" target="_blank" style="color:#193a7b;display:block;padding:8px 18px;">Agenda</a></li>
                            <li><a href="https://diskominfo.karanganyarkab.go.id/informasi/siaran-pers/" target="_blank" style="color:#193a7b;display:block;padding:8px 18px;">Siaran Pers</a></li>
                        </ul>
                    </li>
                    <li style="position:relative;">
                        <a href="#" style="color:#193a7b;text-decoration:none;padding:5px 0 3px 0;display:inline-block;">PPID &#9662;</a>
                        <ul style="display:none;position:absolute;left:0;top:100%;background:#fff;color:#193a7b;min-width:180px;box-shadow:0 4px 16px rgba(0,0,0,0.10);border-radius:0 0 8px 8px;z-index:10;list-style:none;padding:8px 0;">
                            <li><a href="https://diskominfo.karanganyarkab.go.id/ppid/" target="_blank" style="color:#193a7b;display:block;padding:8px 18px;">PPID Utama</a></li>
                            <li><a href="https://diskominfo.karanganyarkab.go.id/ppid/daftar-informasi-publik/" target="_blank" style="color:#193a7b;display:block;padding:8px 18px;">Daftar Informasi Publik</a></li>
                            <li><a href="https://diskominfo.karanganyarkab.go.id/ppid/regulasi/" target="_blank" style="color:#193a7b;display:block;padding:8px 18px;">Regulasi</a></li>
                            <li><a href="https://diskominfo.karanganyarkab.go.id/ppid/permohonan-informasi/" target="_blank" style="color:#193a7b;display:block;padding:8px 18px;">Permohonan Informasi</a></li>
                        </ul>
                    </li>
                    <li style="position:relative;">
                        <a href="#" style="color:#193a7b;text-decoration:none;padding:5px 0 3px 0;display:inline-block;">Layanan &#9662;</a>
                        <ul style="display:none;position:absolute;left:0;top:100%;background:#fff;color:#193a7b;min-width:180px;box-shadow:0 4px 16px rgba(0,0,0,0.10);border-radius:0 0 8px 8px;z-index:10;list-style:none;padding:8px 0;">
                            <li><a href="https://diskominfo.karanganyarkab.go.id/layanan/permohonan-informasi/" target="_blank" style="color:#193a7b;display:block;padding:8px 18px;">Permohonan Informasi</a></li>
                            <li><a href="https://diskominfo.karanganyarkab.go.id/layanan/permohonan-ppid/" target="_blank" style="color:#193a7b;display:block;padding:8px 18px;">Permohonan PPID</a></li>
                            <li><a href="https://diskominfo.karanganyarkab.go.id/layanan/permohonan-ikp/" target="_blank" style="color:#193a7b;display:block;padding:8px 18px;">Permohonan IKP</a></li>
                        </ul>
                    </li>
                    <li style="position:relative;">
                        <a href="#" style="color:#193a7b;text-decoration:none;padding:5px 0 3px 0;display:inline-block;">Pengaduan &#9662;</a>
                        <ul style="display:none;position:absolute;left:0;top:100%;background:#fff;color:#193a7b;min-width:180px;box-shadow:0 4px 16px rgba(0,0,0,0.10);border-radius:0 0 8px 8px;z-index:10;list-style:none;padding:8px 0;">
                            <li><a href="https://diskominfo.karanganyarkab.go.id/aduan-aspirasi-publik/" target="_blank" style="color:#193a7b;display:block;padding:8px 18px;">Aduan/Aspirasi Publik</a></li>
                            <li><a href="https://diskominfo.karanganyarkab.go.id/faq/" target="_blank" style="color:#193a7b;display:block;padding:8px 18px;">FAQ</a></li>
                            <li><a href="https://diskominfo.karanganyarkab.go.id/whistleblowing-system/" target="_blank" style="color:#193a7b;display:block;padding:8px 18px;">Whistleblowing System</a></li>
                        </ul>
                    </li>
                    <li><a href="contact.php" style="color:#193a7b;text-decoration:none;padding:5px 0 3px 0;display:inline-block;">Hubungi Kami</a></li>
                    <li><a href="admin.php" style="color:#193a7b;text-decoration:none;padding:5px 0 3px 0;display:inline-block;">👤 Admin</a></li>
                </ul>
            </div>
        </nav>
        <script>
        // Dropdown menu logic
        document.querySelectorAll('nav ul > li').forEach(function(li) {
                li.addEventListener('mouseenter', function() {
                        var dropdown = li.querySelector('ul');
                        if(dropdown) dropdown.style.display = 'block';
                });
                li.addEventListener('mouseleave', function() {
                        var dropdown = li.querySelector('ul');
                        if(dropdown) dropdown.style.display = 'none';
                });
        });
        </script>

    <!-- Wisata Karanganyar Section -->
    <section id="wisata-karanganyar" class="wisata-section">
        <div class="wisata-hero">
            <div class="container wisata-hero-inner">
                <div class="wisata-hero-content">
                    <span class="wisata-kicker">Jelajahi Karanganyar</span>
                    <h2>Pesona Alam, Budaya, dan Petualangan</h2>
                    <p>Temukan keindahan lereng Lawu, air terjun yang menyegarkan, serta kekayaan budaya yang autentik di Kabupaten Karanganyar.</p>
                    <div class="wisata-hero-actions">
                        <a class="wisata-btn primary" href="#wisata-destinasi">Lihat Destinasi</a>
                        <a class="wisata-btn primary" href="#wisata-kuliner">Lihat Kuliner</a>
                        <a class="wisata-btn ghost" href="#wisata-video">Tonton Video</a>
                    </div>
                    <div class="wisata-hero-meta">
                        <div><strong>30+</strong><span>Destinasi Alam</span></div>
                        <div><strong>12+</strong><span>Wisata Budaya</span></div>
                        <div><strong>20+</strong><span>Kuliner Khas</span></div>
                    </div>
                </div>
                <div class="wisata-search-card">
                    <div class="wisata-search-title">Cari Destinasi</div>
                    <form class="wisata-search-form" action="wisata.php" method="get">
                        <input type="text" id="wisata-search" name="q" placeholder="Contoh: Tawangmangu, Candi Sukuh" autocomplete="off">
                        <select id="wisata-category" name="cat">
                            <option value="all">Semua Kategori</option>
                            <option value="alam">Alam & Petualangan</option>
                            <option value="budaya">Budaya & Sejarah</option>
                            <option value="kuliner">Kuliner</option>
                            <option value="keluarga">Keluarga</option>
                        </select>
                        <button type="submit" id="wisata-search-btn">Cari Sekarang</button>
                    </form>
                </div>
            </div>
        </div>

    <!-- Spotlight Ciri Khas Section -->
    <section class="spotlight-section">
        <div class="spotlight-overlay"></div>
        <div class="container spotlight-container">
            <div class="spotlight-text-content">
                <span class="spotlight-label">Spotlight</span>
                <h2 class="spotlight-title">Ciri Khas Karanganyar</h2>
                <p class="spotlight-desc">
                    Temukan keunikan budaya Kabupaten Karanganyar melalui karakteristik menariknya, seperti kerajinan tangan batik, candi bersejarah, dan spesial kuliner durian yang menggugah selera.
                </p>
                <a href="#wisata-destinasi" class="spotlight-btn-action">Temukan Ciri Khas Karanganyar →</a>
                
                <div class="spotlight-controls">
                    <div class="spotlight-counter">
                        <span class="current">01</span>
                        <div class="progress-bar"><div class="progress-fill"></div></div>
                        <span class="total">05</span>
                    </div>
                    <div class="spotlight-nav">
                         <button class="spotlight-arrow prev">&lt;</button>
                         <button class="spotlight-arrow next">&gt;</button>
                    </div>
                </div>
            </div>
            
            <div class="spotlight-cards-wrapper">
                <div class="spotlight-cards">
                    <!-- Card 1: Candi -->
                    <div class="spotlight-card">
                        <div class="spotlight-card-bg" style="background-image: url('img/candi sukuh.jpeg');"></div> 
                        <div class="spotlight-card-content">
                            <h3 class="spotlight-card-title">Candi Sukuh</h3>
                            <p class="spotlight-card-text">Sobat Pesona pastinya sudah tidak asing kan dengan Candi Sukuh? Terletak di Kabupaten Karanganyar, Jawa Tengah, candi yang eksotis di atas awan.</p>
                        </div>
                    </div>
                    <!-- Card 2: Batik -->
                    <div class="spotlight-card">
                       <div class="spotlight-card-bg" style="background-image: url('img/batik karanganyar.png');"></div>
                        <div class="spotlight-card-content">
                            <h3 class="spotlight-card-title">Batik Batik Girilayu</h3>
                            <p class="spotlight-card-text">Sentra batik tulis dengan motif khas yang telah ada sejak masa Mangkunegaran, melestarikan warisan budaya leluhur dengan bangga.</p>
                        </div>
                    </div>
                     <!-- Card 3: Durian -->
                    <div class="spotlight-card">
                       <div class="spotlight-card-bg" style="background-image: url('img/duren karanganyar.jpeg');"></div>
                        <div class="spotlight-card-content">
                            <h3 class="spotlight-card-title">Durian Karanganyar</h3>
                            <p class="spotlight-card-text">Nikmati sensasi durian lokal Karanganyar yang legit dan manis, primadona kuliner yang wajib dicoba saat berkunjung ke sini.</p>
                        </div>
                    </div>
                    <!-- Card 4: Kebun Teh -->
                    <div class="spotlight-card">
                       <div class="spotlight-card-bg" style="background-image: url('img/kebunteh keminung.jpeg');"></div>
                        <div class="spotlight-card-content">
                            <h3 class="spotlight-card-title">Kebun Teh Kemuning</h3>
                            <p class="spotlight-card-text">Hamparan hijau kebun teh yang menyejukkan mata dengan udara segar khas pegunungan Lawu, cocok untuk healing sejenak.</p>
                        </div>
                    </div>
                    <!-- Card 5: Telaga -->
                    <div class="spotlight-card">
                       <div class="spotlight-card-bg" style="background-image: url('img/telaga madirda.jpeg');"></div>
                        <div class="spotlight-card-content">
                            <h3 class="spotlight-card-title">Telaga Madirda</h3>
                            <p class="spotlight-card-text">Danau alami di kaki Gunung Lawu dengan air jernih dan suasana tenang, destinasi sempurna untuk camping dan piknik keluarga.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

        <div class="wisata-highlight" id="wisata-destinasi">
            <div class="container">
                <div class="wisata-section-header">
                    <div>
                        <h3>Destinasi Unggulan</h3>
                        <p>Rekomendasi wisata paling populer di Karanganyar untuk liburan singkat maupun panjang.</p>
                    </div>
                    <a class="wisata-link" href="#wisata-gallery">Lihat Semua →</a>
                </div>
                <div class="wisata-grid" id="wisata-grid">
                    <article class="wisata-card" data-category="alam" data-search="grojogan sewu air terjun tawangmangu alam">
                        <div class="wisata-photo" style="background-image:url('img/Grojogan sewu.jpg');"></div>
                        <div class="wisata-card-body">
                            <div class="wisata-tag">Alam</div>
                            <h4>Grojogan Sewu</h4>
                            <p>Air terjun legendaris di Tawangmangu dengan panorama hutan pinus dan jalur trekking yang nyaman.</p>
                            <div class="wisata-card-meta">📍 Tawangmangu</div>
                        </div>
                    </article>
                    <article class="wisata-card" data-category="budaya" data-search="candi sukuh budaya sejarah ngargoyoso">
                        <div class="wisata-photo" style="background-image:url('img/candi sukuh.jpeg');"></div>
                        <div class="wisata-card-body">
                            <div class="wisata-tag">Budaya</div>
                            <h4>Candi Sukuh</h4>
                            <p>Kompleks candi unik berciri relief khas, menawarkan pengalaman sejarah dan budaya yang mendalam.</p>
                            <div class="wisata-card-meta">📍 Ngargoyoso</div>
                        </div>
                    </article>
                    <article class="wisata-card" data-category="alam" data-search="kebun teh kemuning alam kemuning lawu">
                        <div class="wisata-photo" style="background-image:url('img/kebunteh keminung.jpeg');"></div>
                        <div class="wisata-card-body">
                            <div class="wisata-tag">Alam</div>
                            <h4>Kebun Teh Kemuning</h4>
                            <p>Hamparan teh hijau di lereng Lawu dengan udara sejuk dan spot foto yang memanjakan mata.</p>
                            <div class="wisata-card-meta">📍 Kemuning</div>
                        </div>
                    </article>
                    <article class="wisata-card" data-category="alam" data-search="telaga madirda danau tawangmangu alam keluarga">
                        <div class="wisata-photo" style="background-image:url('img/telaga madirda.jpeg');"></div>
                        <div class="wisata-card-body">
                            <div class="wisata-tag">Alam</div>
                            <h4>Telaga Madirda</h4>
                            <p>Danau alami yang tenang dengan refleksi langit biru, cocok untuk piknik keluarga.</p>
                            <div class="wisata-card-meta">📍 Tawangmangu</div>
                        </div>
                    </article>

                    <article class="wisata-card" data-category="budaya" data-search="candi cetho budaya sejarah ngargoyoso">
                        <div class="wisata-photo" style="background-image:url('img/candi cetho.png');"></div>
                        <div class="wisata-card-body">
                            <div class="wisata-tag">Budaya</div>
                            <h4>Candi Cetho</h4>
                            <p>Candi di lereng Lawu dengan suasana pegunungan yang sejuk dan panorama yang menenangkan.</p>
                            <div class="wisata-card-meta">📍 Ngargoyoso</div>
                        </div>
                    </article>

                    <article class="wisata-card" data-category="alam" data-search="air terjun ngudal ngudalan jenawi alam">
                        <div class="wisata-photo" style="background-image:url('img/air terjun ngudal.webp');"></div>
                        <div class="wisata-card-body">
                            <div class="wisata-tag">Alam</div>
                            <h4>Air Terjun Ngudal</h4>
                            <p>Destinasi air terjun yang cocok untuk pencinta alam dan suasana segar pegunungan.</p>
                            <div class="wisata-card-meta">📍 Karanganyar</div>
                        </div>
                    </article>

                    <article class="wisata-card" data-category="keluarga" data-search="pleseran keluarga alam karanganyar">
                        <div class="wisata-photo" style="background-image:url('img/pleseran.webp');"></div>
                        <div class="wisata-card-body">
                            <div class="wisata-tag">Keluarga</div>
                            <h4>Pleseran</h4>
                            <p>Area wisata untuk bersantai bareng keluarga, cocok untuk piknik ringan dan menikmati udara sejuk.</p>
                            <div class="wisata-card-meta">📍 Karanganyar</div>
                        </div>
                    </article>

                    <article class="wisata-card" data-category="budaya" data-search="batik girilayu budaya kerajinan karanganyar">
                        <div class="wisata-photo" style="background-image:url('img/batik karanganyar.png');"></div>
                        <div class="wisata-card-body">
                            <div class="wisata-tag">Budaya</div>
                            <h4>Batik Girilayu</h4>
                            <p>Sentra batik tulis dengan motif khas sebagai warisan budaya yang terus dijaga.</p>
                            <div class="wisata-card-meta">📍 Karanganyar</div>
                        </div>
                    </article>
                </div>
                <div id="wisata-no-result" style="display:none;margin-top:18px;padding:14px 18px;border-radius:8px;background:#fff3e0;color:#8d4b00;border:1px solid #ffd29e;">
                    Tidak ada destinasi yang cocok dengan pencarian Anda.
                </div>
            </div>
        </div>

        <div class="wisata-highlight" id="wisata-kuliner">
            <div class="container">
                <div class="wisata-section-header">
                    <div>
                        <h3>Destinasi Kuliner</h3>
                        <p>Rekomendasi sentra kuliner dan oleh-oleh khas untuk melengkapi perjalanan Anda.</p>
                    </div>
                </div>

                <div class="wisata-grid">
                    <article class="wisata-card" data-category="kuliner" data-search="durian karanganyar kuliner oleh oleh">
                        <div class="wisata-photo" style="background-image:url('img/duren karanganyar.jpeg');"></div>
                        <div class="wisata-card-body">
                            <div class="wisata-tag">Kuliner</div>
                            <h4>Durian Karanganyar</h4>
                            <p>Menikmati durian lokal yang populer sebagai kuliner musiman dan favorit wisatawan.</p>
                            <div class="wisata-card-meta">📍 Karanganyar</div>
                        </div>
                    </article>

                    <article class="wisata-card" data-category="kuliner" data-search="sate kelinci tawangmangu kuliner">
                        <div class="wisata-photo" style="background-image:url('img/sate.jpg');"></div>
                        <div class="wisata-card-body">
                            <div class="wisata-tag">Kuliner</div>
                            <h4>Sentra Sate Kelinci</h4>
                            <p>Alternatif kuliner khas daerah pegunungan yang sering dicari wisatawan.</p>
                            <div class="wisata-card-meta">📍 Tawangmangu</div>
                        </div>
                    </article>

                    <article class="wisata-card" data-category="kuliner" data-search="teh kemuning kuliner lereng lawu">
                        <div class="wisata-photo" style="background-image:url('img/kebunteh keminung.jpeg');"></div>
                        <div class="wisata-card-body">
                            <div class="wisata-tag">Kuliner</div>
                            <h4>Kuliner Lereng Lawu</h4>
                            <p>Menikmati sajian hangat dan camilan sederhana dengan suasana sejuk pegunungan.</p>
                            <div class="wisata-card-meta">📍 Kemuning</div>
                        </div>
                    </article>

                    <article class="wisata-card" data-category="kuliner" data-search="oleh oleh karanganyar kuliner jajanan">
                        <div class="wisata-photo" style="background-image:url('img/oleh oleh tradisonal.webp');"></div>
                        <div class="wisata-card-body">
                            <div class="wisata-tag">Kuliner</div>
                            <h4>Oleh-oleh Tradisional</h4>
                            <p>Pilihan jajanan dan oleh-oleh yang pas untuk dibawa pulang setelah berwisata.</p>
                            <div class="wisata-card-meta">📍 Karanganyar</div>
                        </div>
                    </article>
                </div>
            </div>
        </div>

        <div class="wisata-media" id="wisata-video">
            <div class="container wisata-media-inner">
                <div class="wisata-video">
                    <div class="wisata-media-title">Video Highlight</div>
                    <div class="wisata-video-frame">
                        <iframe src="https://www.youtube.com/embed/PPI6KWiJ93c?si=wxa1dzfB69JkbMpm" title="Wisata Karanganyar" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                    </div>
                    <p class="wisata-media-note">Ganti video dengan URL YouTube wisata Karanganyar favorit Anda.</p>
                </div>
                <div class="wisata-gallery" id="wisata-gallery">
                    <div class="wisata-media-title">Galeri Foto</div>
                    <div class="wisata-gallery-grid">
                        <div class="wisata-gallery-item" style="background-image:url('img/Grojogan sewu.jpg');" title="Grojogan Sewu"></div>
                        <div class="wisata-gallery-item" style="background-image:url('img/candi sukuh.jpeg');" title="Candi Sukuh"></div>
                        <div class="wisata-gallery-item" style="background-image:url('img/kebunteh keminung.jpeg');" title="Kebun Teh Kemuning"></div>
                        <div class="wisata-gallery-item" style="background-image:url('img/telaga madirda.jpeg');" title="Telaga Madirda"></div>
                        <div class="wisata-gallery-item" style="background-image:url('img/batik karanganyar.png');" title="Batik Girilayu"></div>
                        <div class="wisata-gallery-item" style="background-image:url('img/duren karanganyar.jpeg');" title="Durian Karanganyar"></div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <script>
        function normalizeText(text) {
            return (text || '').toLowerCase();
        }

        function scrollToFirstResult() {
            var firstVisible = document.querySelector('.wisata-card:not([style*="display: none"])');
            if (firstVisible && typeof firstVisible.scrollIntoView === 'function') {
                firstVisible.scrollIntoView({ behavior: 'smooth', block: 'start' });
                return true;
            }
            var destinasiSection = document.getElementById('wisata-destinasi');
            if (destinasiSection && typeof destinasiSection.scrollIntoView === 'function') {
                destinasiSection.scrollIntoView({ behavior: 'smooth', block: 'start' });
                return true;
            }
            return false;
        }

        function filterWisata(query, category) {
            var q = normalizeText(query).trim();
            var cat = normalizeText(category).trim();
            if (cat === '') cat = 'all';
            var cards = document.querySelectorAll('.wisata-card');
            var anyVisible = false;

            Array.prototype.forEach.call(cards, function(card) {
                var data = card.getAttribute('data-search') || card.innerText || '';
                var cardCat = normalizeText(card.getAttribute('data-category') || '');
                var matchQ = q === '' || normalizeText(data).includes(q);
                var matchCat = (cat === 'all') || (cardCat !== '' && cardCat === cat);
                var match = matchQ && matchCat;
                card.style.display = match ? '' : 'none';
                if (match) anyVisible = true;
            });

            var noResult = document.getElementById('wisata-no-result');
            if (noResult) {
                noResult.style.display = anyVisible ? 'none' : 'block';
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            var params = new URLSearchParams(window.location.search);
            var initialQuery = params.get('q') || '';
            var initialCat = params.get('cat') || 'all';

            var topbarInput = document.getElementById('topbar-search');
            var wisataInput = document.getElementById('wisata-search');
            var wisataButton = document.getElementById('wisata-search-btn');
            var wisataCategory = document.getElementById('wisata-category');
            var wisataForm = document.querySelector('form.wisata-search-form');

            if (topbarInput) topbarInput.value = initialQuery;
            if (wisataInput) wisataInput.value = initialQuery;

            if (wisataCategory) {
                wisataCategory.value = initialCat || 'all';
            }

            filterWisata(initialQuery, wisataCategory ? wisataCategory.value : (initialCat || 'all'));

            if ((initialQuery && initialQuery.trim() !== '') || (initialCat && initialCat !== 'all')) {
                setTimeout(function() {
                    scrollToFirstResult();
                }, 80);
            }

            function applySearchAndUpdateUrl(value, cat) {
                var q = value || '';
                var c = cat || 'all';
                if (topbarInput) topbarInput.value = q;

                var nextParams = new URLSearchParams(window.location.search);
                if (q.trim() !== '') nextParams.set('q', q); else nextParams.delete('q');
                if (c && c !== 'all') nextParams.set('cat', c); else nextParams.delete('cat');

                var nextUrl = window.location.pathname + (nextParams.toString() ? ('?' + nextParams.toString()) : '');
                if (typeof window.history !== 'undefined' && typeof window.history.replaceState === 'function') {
                    window.history.replaceState({}, '', nextUrl);
                }

                filterWisata(q, c);
                scrollToFirstResult();
            }

            if (wisataForm && wisataInput) {
                wisataForm.addEventListener('submit', function(event) {
                    event.preventDefault();
                    applySearchAndUpdateUrl(
                        wisataInput.value || '',
                        wisataCategory ? wisataCategory.value : (initialCat || 'all')
                    );
                });
            }

            if (wisataInput) {
                wisataInput.addEventListener('keydown', function(event) {
                    if (event.key === 'Enter') {
                        event.preventDefault();
                        applySearchAndUpdateUrl(
                            wisataInput.value || '',
                            wisataCategory ? wisataCategory.value : (initialCat || 'all')
                        );
                    }
                });
            }

            if (wisataCategory) {
                wisataCategory.addEventListener('change', function() {
                    var value = wisataInput ? (wisataInput.value || '') : '';
                    applySearchAndUpdateUrl(value, wisataCategory.value);
                });
            }

            // Spotlight Logic
            const spotlightContainer = document.querySelector('.spotlight-cards');
            const prevBtn = document.querySelector('.spotlight-arrow.prev');
            const nextBtn = document.querySelector('.spotlight-arrow.next');
            const currentCounter = document.querySelector('.spotlight-counter .current');
            const totalCounter = document.querySelector('.spotlight-counter .total');
            const progressFill = document.querySelector('.progress-fill');
            
            let scrollAmount = 0;
            const cardWidth = 300; // card width + gap
            const totalCards = document.querySelectorAll('.spotlight-card').length;
            
            // Set initial total
            totalCounter.textContent = totalCards < 10 ? '0' + totalCards : totalCards;

            function updateCounter() {
                const scrollLeft = document.querySelector('.spotlight-cards-wrapper').scrollLeft;
                const index = Math.round(scrollLeft / cardWidth) + 1;
                currentCounter.textContent = index < 10 ? '0' + index : index;
                
                // Update progress bar
                const progress = (index / totalCards) * 100;
                progressFill.style.width = `${progress}%`;
            }

            // Listen for scroll events
             document.querySelector('.spotlight-cards-wrapper').addEventListener('scroll', updateCounter);

            nextBtn.addEventListener('click', () => {
                const wrapper = document.querySelector('.spotlight-cards-wrapper');
                wrapper.scrollBy({ left: cardWidth, behavior: 'smooth' });
            });

            prevBtn.addEventListener('click', () => {
                const wrapper = document.querySelector('.spotlight-cards-wrapper');
                wrapper.scrollBy({ left: -cardWidth, behavior: 'smooth' });
            });
            
             // Initialize
            updateCounter();
        });
    </script>

    <?php
    $footer_show_map = true;
    $footer_year = 2026;
    include __DIR__ . '/partials/site_footer.php';
    ?>

    <?php include __DIR__ . '/partials/floating_widget.php'; ?>
</body>
</html>
