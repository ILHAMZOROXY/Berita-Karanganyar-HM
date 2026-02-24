<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include 'config/database.php';
include 'config/news_fetcher.php';

// Inisialisasi News Fetcher
$news_fetcher = new NewsFetcher();

// Fallback gambar super-ringan: favicon domain (burik tapi cepat & selalu ada)
function buildFaviconUrl($link) {
    $link = trim((string) $link);
    if ($link === '') return '';
    $host = parse_url($link, PHP_URL_HOST);
    if (!$host) {
        // handle URL tanpa scheme
        $host = parse_url('https://' . ltrim($link, '/'), PHP_URL_HOST);
    }
    if (!$host) return '';
    return 'https://www.google.com/s2/favicons?sz=256&domain=' . rawurlencode($host);
}

// Filter bulan dan tahun - Default ke bulan sekarang
$selected_month = isset($_GET['month']) ? intval($_GET['month']) : intval(date('n'));
$selected_year = isset($_GET['year']) ? intval($_GET['year']) : date('Y');

// Ambil berita dari database
$all_berita = [];

if (isset($conn) && $conn) {
    try {
        // Selalu filter berdasarkan bulan - Fix bug Januari
        $start_timestamp = mktime(0, 0, 0, $selected_month, 1, $selected_year);
        // Gunakan date('t') untuk mendapatkan jumlah hari dalam bulan
        $last_day = date('t', $start_timestamp);
        $end_timestamp = mktime(23, 59, 59, $selected_month, $last_day, $selected_year);
        
        $sql = "SELECT id, judul, deskripsi, link, sumber, tanggal_publikasi, timestamp, tipe, gambar 
                FROM news_items 
                WHERE timestamp >= ? AND timestamp <= ?
                ORDER BY timestamp DESC";
        
        $stmt = $conn->prepare($sql);
        if ($stmt) {
            $stmt->bind_param('ii', $start_timestamp, $end_timestamp);
            $stmt->execute();
            $result = $stmt->get_result();
            
            while ($row = $result->fetch_assoc()) {
                // Fallback untuk gambar kosong + marker placeholder
                $gambarRaw = trim((string) ($row['gambar'] ?? ''));
                $isPlaceholder = false;
                if ($gambarRaw === '' || $gambarRaw === 'img/no-image.png' || $gambarRaw === '/img/no-image.png') {
                    $gambar = 'img/no-image.png';
                    $isPlaceholder = true;
                } else {
                    $gambar = $gambarRaw;
                }
                
                $all_berita[] = [
                    'id' => $row['id'],
                    'judul' => $row['judul'],
                    'deskripsi' => $row['deskripsi'],
                    'link' => $row['link'],
                    'sumber' => $row['sumber'],
                    'tanggal_publikasi' => $row['tanggal_publikasi'],
                    'timestamp' => (int) $row['timestamp'],
                    'tipe' => $row['tipe'],
                    'gambar' => $gambar,
                    'is_placeholder' => $isPlaceholder,
                    'favicon' => buildFaviconUrl($row['link'] ?? ''),
                ];
            }
            $stmt->close();
        }
    } catch (Exception $e) {
        error_log("Error fetching archive news: " . $e->getMessage());
    }
}

// Filter berdasarkan pencarian
$query = isset($_GET['q']) ? trim($_GET['q']) : '';
if ($query !== '') {
    $searchNeedle = function_exists('mb_strtolower') ? mb_strtolower($query) : strtolower($query);
    $all_berita = array_values(array_filter($all_berita, function($item) use ($searchNeedle) {
        $judul = $item['judul'] ?? '';
        $deskripsi = $item['deskripsi'] ?? '';
        $sumber = $item['sumber'] ?? '';
        $tanggal = $item['tanggal_publikasi'] ?? '';
        $haystack = trim($judul . ' ' . $deskripsi . ' ' . $sumber . ' ' . $tanggal);
        $haystack = function_exists('mb_strtolower') ? mb_strtolower($haystack) : strtolower($haystack);
        return strpos($haystack, $searchNeedle) !== false;
    }));
}

// Nama bulan dalam bahasa Indonesia
$bulan_indonesia = [
    1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
    5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
    9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
];

$berita_count = count($all_berita);

// Pagination: tampilkan 12 berita per halaman
$perPage = 12;
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$totalPages = max(1, ceil($berita_count / $perPage));
if ($page > $totalPages) { $page = $totalPages; }
$offset = ($page - 1) * $perPage;
$berita_list = array_slice($all_berita, $offset, $perPage);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Arsip Berita - Website Berita Karanganyar</title>
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
                <form action="arsip.php" method="get" style="display:flex;align-items:center;">
                    <input type="text" name="q" placeholder="Cari arsip berita..." value="<?php echo htmlspecialchars($query); ?>" style="border:none;border-radius:22px 0 0 22px;padding:8px 18px;font-size:1em;outline:none;width:200px;">
                    <button type="submit" style="background:#f58220;border:none;border-radius:0 22px 22px 0;padding:8px 16px;color:#fff;font-size:1.2em;cursor:pointer;"><span style="font-size:1.1em;">&#128269;</span></button>
                </form>
            </div>
        </div>
        <!-- Main Header: Logo & Menu -->
        <header style="background:#fff;box-shadow:0 2px 8px rgba(0,0,0,0.04);border-bottom:2px solid #f58220;">
            <div class="container" style="display:flex;align-items:center;justify-content:space-between;padding:4px 0;">
                <div style="display:flex;align-items:center;gap:12px;">
                    <img src="img/kominfo.png" alt="Logo Kominfo" style="height:40px;width:auto;display:block;object-fit:contain;background:transparent;border-radius:0;padding:0;box-shadow:none;">
                    <div>
                        <span style="font-size:1.55rem;font-weight:900;color:#193a7b;letter-spacing:0.4px;">Berita Karanganyar</span><br>
                        <span style="font-size:0.82rem;color:#f58220;font-weight:600;">Portal Berita Resmi Pemerintah Daerah</span>
                    </div>
                </div>
                <a href="index.php" style="background:linear-gradient(135deg,#193a7b 0%,#2563a8 100%);color:#fff;border:none;padding:6px 14px;border-radius:8px;text-decoration:none;font-weight:700;font-size:0.9rem;box-shadow:0 2px 8px rgba(0,0,0,0.10);display:flex;align-items:center;gap:8px;transition:all 0.3s ease;"><span style="font-size:1.1em;">🏠</span> Beranda</a>
            </div>
        </header>

        <!-- Navigation Bar -->
        <nav style="background:#fff;border-bottom:2px solid #f58220;box-shadow:0 1px 4px rgba(0,0,0,0.03);">
            <div class="container">
                <ul style="display:flex;gap:14px;margin:0;padding:0;list-style:none;font-size:0.9rem;font-weight:700;align-items:center;min-height:26px;">
                    <li><a href="index.php" style="color:#193a7b;text-decoration:none;padding:5px 0 3px 0;display:inline-block;">Beranda</a></li>
                    <li><a href="arsip.php" class="nav-active" style="color:#f58220;text-decoration:none;padding:5px 0 3px 0;display:inline-block;border-bottom:2px solid #f58220;">Arsip Berita</a></li>
                    <li><a href="wisata.php" style="color:#193a7b;text-decoration:none;padding:5px 0 3px 0;display:inline-block;">Wisata</a></li>
                    <li style="position:relative;">
                        <a href="#" style="color:#193a7b;text-decoration:none;padding:5px 0 3px 0;display:inline-block;">Profile &#9662;</a>
                        <ul style="display:none;position:absolute;left:0;top:100%;background:#fff;color:#193a7b;min-width:210px;box-shadow:0 4px 16px rgba(0,0,0,0.10);border-radius:0 0 8px 8px;z-index:10;list-style:none;padding:8px 0;">
                            <li><a href="https://diskominfo.karanganyarkab.go.id/struktur-organisasi/" target="_blank" style="color:#193a7b;display:block;padding:8px 18px;">Struktur Organisasi</a></li>
                            <li><a href="https://diskominfo.karanganyarkab.go.id/tugas-dan-fungsi/" target="_blank" style="color:#193a7b;display:block;padding:8px 18px;">Tugas & Fungsi</a></li>
                            <li><a href="https://diskominfo.karanganyarkab.go.id/visi-misi/" target="_blank" style="color:#193a7b;display:block;padding:8px 18px;">Visi & Misi</a></li>
                            <li><a href="https://diskominfo.karanganyarkab.go.id/pejabat-struktural/" target="_blank" style="color:#193a7b;display:block;padding:8px 18px;">Pejabat Struktural</a></li>
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

    <!-- Main Content -->
    <main class="container" style="padding:24px 0;">
        <!-- Page Header -->
        <div style="background:linear-gradient(135deg,#193a7b 0%,#2563a8 100%);color:#fff;padding:24px;border-radius:16px;margin-bottom:28px;box-shadow:0 6px 20px rgba(0,0,0,0.15);position:relative;overflow:hidden;">
            <div style="position:absolute;top:-20px;right:-20px;width:150px;height:150px;background:rgba(255,255,255,0.05);border-radius:50%;z-index:0;"></div>
            <div style="position:absolute;bottom:-30px;left:-30px;width:200px;height:200px;background:rgba(255,255,255,0.03);border-radius:50%;z-index:0;"></div>
            <div style="position:relative;z-index:1;">
                <h1 style="margin:0 0 10px 0;font-size:2rem;font-weight:900;display:flex;align-items:center;gap:12px;">Arsip Berita <?php echo $bulan_indonesia[$selected_month]; ?></h1>
                <p style="margin:0;font-size:1.05rem;opacity:0.95;font-weight:500;">
                    📅 <?php echo $bulan_indonesia[$selected_month] . ' ' . $selected_year; ?> • <?php echo $berita_count; ?> Berita Tersedia
                </p>
            </div>
        </div>

        <!-- Filter Bulan - Design Horizontal (Tanpa Tahun) -->
        <div style="background:linear-gradient(135deg,#fff 0%,#f8fbff 100%);padding:20px;border-radius:16px;margin-bottom:28px;box-shadow:0 4px 16px rgba(0,0,0,0.08);border:1px solid #e8f4ff;">
            <div style="display:flex;align-items:center;gap:6px;justify-content:space-between;padding:4px 0;">
                <?php foreach ($bulan_indonesia as $num => $nama): ?>
                    <a href="arsip.php?month=<?php echo $num; ?>" style="background:<?php echo ($selected_month === $num) ? 'linear-gradient(135deg,#f58220 0%,#ff9d4d 100%)' : 'linear-gradient(135deg,#fff 0%,#f8f9fa 100%)'; ?>;color:<?php echo ($selected_month === $num) ? '#fff' : '#193a7b'; ?>;padding:10px 14px;border-radius:10px;text-decoration:none;font-weight:<?php echo ($selected_month === $num) ? '700' : '600'; ?>;text-align:center;transition:all 0.3s;border:2px solid <?php echo ($selected_month === $num) ? '#f58220' : '#e0e7ff'; ?>;white-space:nowrap;font-size:0.85rem;box-shadow:<?php echo ($selected_month === $num) ? '0 4px 12px rgba(245,130,32,0.3)' : '0 2px 6px rgba(0,0,0,0.04)'; ?>;transform:<?php echo ($selected_month === $num) ? 'translateY(-2px)' : 'translateY(0)'; ?>;flex:1;min-width:0;">
                        <?php echo $nama; ?>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>

        <?php if ($berita_count === 0): ?>
            <div style="background:#fff;padding:40px 20px;border-radius:12px;box-shadow:0 2px 8px rgba(0,0,0,0.05);text-align:center;">
                <p style="font-size:1.2rem;color:#666;margin:0;">
                    <?php if ($query !== ''): ?>
                        ❌ Tidak ada berita yang cocok dengan pencarian "<strong><?php echo htmlspecialchars($query); ?></strong>"
                    <?php else: ?>
                        📭 Belum ada arsip berita tersimpan
                    <?php endif; ?>
                </p>
            </div>
        <?php else: ?>
            <!-- News Grid -->
            <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:20px;margin-bottom:32px;">
                <?php foreach ($berita_list as $berita): ?>
                    <?php
                        $gambarVal = trim((string) ($berita['gambar'] ?? ''));
                        $faviconVal = trim((string) ($berita['favicon'] ?? ''));
                        $isPlaceholder = !empty($berita['is_placeholder']) || $gambarVal === '' || $gambarVal === 'img/no-image.png' || $gambarVal === '/img/no-image.png';

                        if ($isPlaceholder) {
                            $imgSrc = ($faviconVal !== '') ? $faviconVal : 'img/no-image.png';
                            $fallbackSrc = 'img/no-image.png';
                            $showOverlay = ($faviconVal === '');
                        } else {
                            $imgSrc = $gambarVal;
                            // jika thumbnail remote gagal load, jatuhkan ke favicon dulu (lebih sering berhasil), lalu no-image
                            $fallbackSrc = ($faviconVal !== '') ? $faviconVal : 'img/no-image.png';
                            $showOverlay = false;
                        }
                    ?>
                    <article class="news-card" style="background:#fff;border-radius:12px;overflow:hidden;box-shadow:0 2px 12px rgba(0,0,0,0.08);transition:box-shadow 0.25s ease;display:flex;flex-direction:column;">
                        <a href="<?php echo htmlspecialchars($berita['link']); ?>" target="_blank" style="text-decoration:none;color:inherit;display:flex;flex-direction:column;height:100%;">
                            <div class="news-image" style="position:relative;width:100%;padding-top:56.25%;background:#e8f4ff;overflow:hidden;">
                                <img
                                    src="<?php echo htmlspecialchars($imgSrc); ?>"
                                    data-fallback="<?php echo htmlspecialchars($fallbackSrc); ?>"
                                    alt="<?php echo htmlspecialchars($berita['judul']); ?>"
                                    loading="lazy"
                                    decoding="async"
                                    style="position:absolute;top:0;left:0;width:100%;height:100%;object-fit:cover;transition:transform 0.25s ease;background:#e8f4ff;display:block;"
                                    onload="if(this.nextElementSibling){this.nextElementSibling.style.display='none';}"
                                    onerror="if(!this.dataset.tried){this.dataset.tried='1';var fb=this.getAttribute('data-fallback');if(fb){this.src=fb;return;}}this.onerror=null;this.src='img/no-image.png';this.style.objectFit='contain';this.style.padding='20px';if(this.nextElementSibling){this.nextElementSibling.style.display='flex';}"
                                >
                                <div class="news-image-overlay" style="display:<?php echo $showOverlay ? 'flex' : 'none'; ?>;">Tidak ada gambar</div>
                            </div>
                            <div style="padding:16px;flex:1;display:flex;flex-direction:column;">
                                <h3 style="margin:0 0 10px 0;font-size:1.05rem;font-weight:700;color:#193a7b;line-height:1.4;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;"><?php echo $berita['judul']; ?></h3>
                                <p style="margin:0 0 12px 0;font-size:0.9rem;color:#666;line-height:1.5;display:-webkit-box;-webkit-line-clamp:3;-webkit-box-orient:vertical;overflow:hidden;flex:1;"><?php echo $berita['deskripsi']; ?></p>
                                <div style="display:flex;justify-content:space-between;align-items:center;font-size:0.8rem;color:#999;margin-top:auto;">
                                    <span style="font-weight:600;color:#f58220;"><?php echo htmlspecialchars($berita['sumber']); ?></span>
                                    <span><?php echo htmlspecialchars($berita['tanggal_publikasi']); ?></span>
                                </div>
                            </div>
                        </a>
                    </article>
                <?php endforeach; ?>
            </div>

            <!-- Pagination -->
            <?php if ($totalPages > 1): ?>
                <div style="display:flex;justify-content:center;gap:8px;margin-top:32px;flex-wrap:wrap;">
                    <?php
                    $params = [];
                    if ($query !== '') $params[] = 'q=' . urlencode($query);
                    if ($selected_month > 0) $params[] = 'month=' . $selected_month;
                    $param_string = !empty($params) ? '&' . implode('&', $params) : '';
                    ?>
                    
                    <?php if ($page > 1): ?>
                        <a href="?page=<?php echo ($page - 1) . $param_string; ?>" style="background:#193a7b;color:#fff;padding:8px 16px;border-radius:6px;text-decoration:none;font-weight:600;transition:all 0.3s;">« Sebelumnya</a>
                    <?php endif; ?>
                    
                    <?php
                    $start = max(1, $page - 2);
                    $end = min($totalPages, $page + 2);
                    for ($i = $start; $i <= $end; $i++):
                    ?>
                        <a href="?page=<?php echo $i . $param_string; ?>" style="background:<?php echo ($i === $page) ? '#f58220' : '#e0e0e0'; ?>;color:<?php echo ($i === $page) ? '#fff' : '#333'; ?>;padding:8px 14px;border-radius:6px;text-decoration:none;font-weight:600;transition:all 0.3s;"><?php echo $i; ?></a>
                    <?php endfor; ?>
                    
                    <?php if ($page < $totalPages): ?>
                        <a href="?page=<?php echo ($page + 1) . $param_string; ?>" style="background:#193a7b;color:#fff;padding:8px 16px;border-radius:6px;text-decoration:none;font-weight:600;transition:all 0.3s;">Berikutnya »</a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </main>

    <?php include 'partials/site_footer.php'; ?>

    <?php include __DIR__ . '/partials/floating_widget.php'; ?>

    <style>
        .news-card:hover {
            box-shadow:0 10px 28px rgba(0,0,0,0.12);
        }
        .news-card img {
            backface-visibility:hidden;
            transform:translateZ(0);
        }
        .news-card:hover img {
            transform:translateZ(0) scale(1.04);
        }
        .news-image-overlay {
            position:absolute;
            inset:0;
            display:flex;
            align-items:center;
            justify-content:center;
            gap:8px;
            font-weight:700;
            font-size:0.95rem;
            color:#193a7b;
            background:linear-gradient(135deg, rgba(232,244,255,0.96) 0%, rgba(255,255,255,0.96) 100%);
            text-shadow:0 1px 0 rgba(255,255,255,0.7);
        }
        .nav-active {
            color:#f58220 !important;
            border-bottom:2px solid #f58220;
        }
        /* Hover effect for month filters */
        a[href*="month"]:hover {
            box-shadow:0 6px 20px rgba(245,130,32,0.4) !important;
        }
    </style>

    <script>
        // Dropdown menu functionality
        document.querySelectorAll('nav li').forEach(li => {
            const submenu = li.querySelector('ul');
            if (submenu) {
                li.addEventListener('mouseenter', () => {
                    submenu.style.display = 'block';
                });
                li.addEventListener('mouseleave', () => {
                    submenu.style.display = 'none';
                });
            }
        });
    </script>
</body>
</html>
