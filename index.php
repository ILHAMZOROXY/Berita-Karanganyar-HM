<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

date_default_timezone_set('Asia/Jakarta');

include 'config/database.php';
include 'config/news_fetcher.php';
$news_fetcher = new NewsFetcher();

function buildFaviconUrl($link) {
    $link = trim((string) $link);
    if ($link === '') return '';
    $host = parse_url($link, PHP_URL_HOST);
    if (!$host) {
        $host = parse_url('https://' . ltrim($link, '/'), PHP_URL_HOST);
    }
    if (!$host) return '';
    return 'https://www.google.com/s2/favicons?sz=256&domain=' . rawurlencode($host);
}

$using_stale_cache = false;
$all_berita = $news_fetcher->getNewsFromStorage(60);

if (!is_array($all_berita) || count($all_berita) === 0) {
    $all_berita = $news_fetcher->getNewsFromRSS(60);
}
if (!is_array($all_berita) || count($all_berita) === 0) {
    $cache_file = __DIR__ . '/cache/karanganyar_news.json';
    if (file_exists($cache_file)) {
        $cached = json_decode(@file_get_contents($cache_file), true);
        if (is_array($cached) && count($cached) > 0) {
            $all_berita = $cached;
            $using_stale_cache = true;
        }
    }
    if (!is_array($all_berita) || count($all_berita) === 0) {
        echo '<!DOCTYPE html><html><head><meta charset="UTF-8"><title>Error</title><style>body{background:#111;color:#fff;font-family:sans-serif;text-align:center;padding:80px;}h1{font-size:2.2em;}p{font-size:1.2em;}</style></head><body><h1>❌ Tidak dapat memuat berita</h1><p>Terjadi masalah saat mengambil data berita.<br>Silakan cek koneksi internet, API key, atau coba refresh beberapa saat lagi.</p></body></html>';
        exit;
    }
}
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

usort($all_berita, function($a, $b) {
    $ta = isset($a['timestamp']) ? (int) $a['timestamp'] : 0;
    $tb = isset($b['timestamp']) ? (int) $b['timestamp'] : 0;
    if ($ta <= 0) {
        $ta = isset($a['tanggal_publikasi']) ? (int) strtotime($a['tanggal_publikasi']) : 0;
    }
    if ($tb <= 0) {
        $tb = isset($b['tanggal_publikasi']) ? (int) strtotime($b['tanggal_publikasi']) : 0;
    }
    if ($ta === $tb) return 0;
    return ($ta > $tb) ? -1 : 1;
});
$berita_count = count($all_berita);

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
    <title>Website Berita - Informasi Terkini</title>
    <?php $asset_ver = @filemtime(__DIR__ . '/css/style.css') ?: time(); ?>
    <link rel="stylesheet" href="css/style.css?v=<?php echo $asset_ver; ?>">
    <link rel="stylesheet" href="css/social-icons.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@700;900&display=swap" rel="stylesheet">
    <?php $ui_ver = @filemtime(__DIR__ . '/js/ui_effects.js') ?: time(); ?>
    <script src="js/ui_effects.js?v=<?php echo $ui_ver; ?>" defer></script>
</head>
<body>
        <div style="background:#16245c; border-bottom:3px solid #f58220; padding:4px 0;position:sticky;top:0;z-index:100;">
            <div class="container" style="display:flex;align-items:center;flex-wrap:wrap;min-height:56px;gap:16px;padding-left:0;padding-right:0;">
                <div style="display:flex;align-items:center;gap:14px;margin-left:0;flex:0 0 auto;min-width:max-content;">
                    <img src="img/kominfo.png" alt="Logo Kominfo" style="height:36px;width:auto;display:block;object-fit:contain;background:transparent;border-radius:0;padding:0;box-shadow:none;">
                    <div style="display:flex;flex-direction:column;justify-content:center;line-height:1;">
                        <span style="font-family:'Poppins',sans-serif;font-size:1.32rem;font-weight:900;color:#fff;letter-spacing:0.5px;white-space:nowrap;line-height:1.25;">Berita Karanganyar</span>
                        <span style="font-size:0.6rem;color:#fbb040;font-weight:600;letter-spacing:0.3px;white-space:nowrap;margin-top:2px;">Portal Berita Resmi Pemerintah Daerah</span>
                    </div>
                </div>
                
                <div id="weather-widget-container" style="flex:1 1 380px;min-width:320px;max-width:480px;position:relative;margin:0 10px;display:flex;justify-content:center;">
                    <div id="weather-scroll-wrapper" style="overflow-x:auto;overflow-y:hidden;scroll-behavior:smooth;scrollbar-width:none;-ms-overflow-style:none;">
                        <div id="weather-cards" style="display:flex;gap:10px;padding:2px 0;">
                        </div>
                    </div>
                </div>
                
                <div style="display:flex;align-items:center;gap:8px;margin-left:auto;flex:0 0 auto;width:auto;justify-content:flex-end;">
                    <form action="index.php" method="get" style="display:flex;align-items:center;">
                        <input type="text" name="q" placeholder="Cari berita atau wisata..." value="<?php echo htmlspecialchars($query); ?>" style="border:none;border-radius:22px 0 0 22px;padding:8px 18px;font-size:1em;outline:none;width:260px;">
                        <button type="submit" style="background:#f58220;border:none;border-radius:0 22px 22px 0;padding:8px 16px;color:#fff;font-size:1.2em;cursor:pointer;"><span style="font-size:1.1em;">&#128269;</span></button>
                    </form>
                    <button id="btn-refresh-top" onclick="refreshNews()" style="background:linear-gradient(135deg,#f58220 0%,#fbb040 100%);color:#fff;border:none;padding:8px 18px;border-radius:8px;cursor:pointer;font-weight:700;font-size:0.9rem;box-shadow:0 2px 8px rgba(0,0,0,0.10);display:flex;align-items:center;gap:6px;transition:all 0.3s ease;white-space:nowrap;margin-right:0;"><span style="font-size:1.1em;">&#x21bb;</span> Refresh</button>
                </div>
            </div>
        </div>
        <!-- Navigation Bar -->
        <nav style="background:#fff;border-bottom:2px solid #f58220;box-shadow:0 1px 4px rgba(0,0,0,0.03);">
            <div class="container">
                <ul style="display:flex;gap:14px;margin:0;padding:0;list-style:none;font-size:0.9rem;font-weight:700;align-items:center;min-height:26px;">
                    <li><a href="index.php" class="nav-active" style="color:#193a7b;text-decoration:none;padding:5px 0 3px 0;display:inline-block;">Beranda</a></li>
                    <li><a href="arsip.php" style="color:#193a7b;text-decoration:none;padding:5px 0 3px 0;display:inline-block;">Arsip Berita</a></li>
                    <li><a href="wisata.php" style="color:#193a7b;text-decoration:none;padding:5px 0 3px 0;display:inline-block;">Wisata</a></li>
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

    <main>
        <div class="container">
            <?php if (count($berita_list) > 0): ?>
                <div class="berita-grid">
                    <?php foreach ($berita_list as $berita): ?>
                        <?php
                            $linkVal = isset($berita['link']) ? (string) $berita['link'] : '';
                            $idVal = isset($berita['id']) ? (string) $berita['id'] : '';
                            $gambarVal = trim((string) ($berita['gambar'] ?? ''));
                            $faviconVal = buildFaviconUrl($linkVal);

                            $isPlaceholder = ($gambarVal === '' || $gambarVal === 'img/no-image.png' || $gambarVal === '/img/no-image.png');
                            if ($isPlaceholder) {
                                $imgSrc = ($faviconVal !== '') ? $faviconVal : 'img/no-image.png';
                                $fallbackSrc = 'img/no-image.png';
                            } else {
                                $imgSrc = $gambarVal;
                                $fallbackSrc = ($faviconVal !== '') ? $faviconVal : 'img/no-image.png';
                            }
                        ?>
                        <div class="berita-card">
                            <div class="berita-image" role="img">
                                <img
                                    class="thumb js-news-thumb"
                                    src="<?php echo htmlspecialchars($imgSrc); ?>"
                                    alt="Gambar Berita"
                                    loading="lazy"
                                    decoding="async"
                                    data-url="<?php echo htmlspecialchars($linkVal); ?>"
                                    data-id="<?php echo htmlspecialchars($idVal); ?>"
                                    data-fallback="<?php echo htmlspecialchars($fallbackSrc); ?>"
                                    onerror="if(!this.dataset.tried){this.dataset.tried='1';var fb=this.getAttribute('data-fallback');if(fb){this.src=fb;return;}}this.onerror=null;this.src='img/no-image.png';"
                                >
                                <div class="source-badge"><?php echo htmlspecialchars(substr($berita['sumber'], 0, 40)); ?></div>
                            </div>
                            <div class="berita-content">
                                <h2><?php echo htmlspecialchars($berita['judul']); ?></h2>
                                <div class="berita-meta">
                                    <span>📰 <?php echo htmlspecialchars($berita['sumber']); ?></span>
                                    <span>🕐 <?php echo ($berita['timestamp'] > 0) ? date('d M Y - H:i', $berita['timestamp']) : htmlspecialchars($berita['tanggal_publikasi']); ?></span>
                                </div>
                                <p><?php echo htmlspecialchars($berita['deskripsi']); ?></p>
                                <a href="<?php echo htmlspecialchars($berita['link']); ?>" target="_blank" class="btn-baca">Baca di Sumber →</a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                <?php if ($totalPages > 1): ?>
                    <div style="text-align:center; margin-top:18px;">
                        <?php if ($page > 1): ?>
                            <a href="?page=<?php echo $page-1; ?><?php echo $query !== '' ? '&q=' . urlencode($query) : ''; ?>" style="margin-right:8px; text-decoration:none; color:#1a237e;">&larr; Sebelumnya</a>
                        <?php endif; ?>

                        <?php for ($p = 1; $p <= $totalPages; $p++): ?>
                            <?php if ($p == $page): ?>
                                <strong style="margin:0 6px; color:#1a237e"><?php echo $p; ?></strong>
                            <?php else: ?>
                                <a href="?page=<?php echo $p; ?><?php echo $query !== '' ? '&q=' . urlencode($query) : ''; ?>" style="margin:0 6px; text-decoration:none; color:#666"><?php echo $p; ?></a>
                            <?php endif; ?>
                        <?php endfor; ?>

                        <?php if ($page < $totalPages): ?>
                            <a href="?page=<?php echo $page+1; ?><?php echo $query !== '' ? '&q=' . urlencode($query) : ''; ?>" style="margin-left:8px; text-decoration:none; color:#1a237e;">Berikutnya &rarr;</a>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            <?php else: ?>
                <div class="empty-state">
                    <h2>Tidak ada berita</h2>
                    <p>Belum ada berita tentang Karanganyar dalam 24 jam terakhir. Coba refresh halaman dalam beberapa saat.</p>
                </div>
            <?php endif; ?>
        </div>
        </main>

    <script>
    (function() {
        const thumbs = Array.from(document.querySelectorAll('.js-news-thumb'));
        if (!thumbs.length) return;

        const maxConcurrency = 2;
        const maxUpgradesPerPage = 10; // jaga supaya ringan
        const startDelayMs = 15_000;   // mulai setelah halaman kebuka
        const retryIntervalMs = 60_000; // ulangi tiap 1 menit
        const totalDurationMs = 4 * 60_000; // jalan sampai 4 menit

        let inFlight = 0;
        let upgradedCount = 0;
        const queued = new Set();

        function canUpgrade(img) {
            if (!img || !img.dataset) return false;
            const url = (img.dataset.url || '').trim();
            if (!url) return false;
            if (img.dataset.upgraded === '1') return false;
            if (img.dataset.upgrading === '1') return false;
            return true;
        }

        async function upgradeOne(img) {
            if (!canUpgrade(img)) return;
            if (upgradedCount >= maxUpgradesPerPage) return;
            if (inFlight >= maxConcurrency) return;

            const url = (img.dataset.url || '').trim();
            const id = (img.dataset.id || '').trim();
            img.dataset.upgrading = '1';
            inFlight++;
            try {
                const params = new URLSearchParams({ url });
                if (id && id.length === 32) params.set('id', id);
                const res = await fetch('og_image.php?' + params.toString(), { cache: 'no-store' });
                const data = await res.json();
                const image = data && data.ok ? (data.image || '') : '';
                if (image) {
                    img.src = image;
                    img.dataset.upgraded = '1';
                    upgradedCount++;
                }
            } catch (e) {
            } finally {
                img.dataset.upgrading = '0';
                inFlight--;
            }
        }

        function scheduleVisibleUpgrades() {
            const visible = thumbs.filter(img => {
                if (!canUpgrade(img)) return false;
                const r = img.getBoundingClientRect();
                return r.bottom >= 0 && r.right >= 0 && r.top <= (window.innerHeight || document.documentElement.clientHeight);
            });

            const candidates = visible.concat(thumbs.filter(canUpgrade));
            for (const img of candidates) {
                if (upgradedCount >= maxUpgradesPerPage) break;
                if (inFlight >= maxConcurrency) break;
                if (queued.has(img)) continue;
                queued.add(img);
                upgradeOne(img).finally(() => queued.delete(img));
            }
        }

        const startedAt = Date.now();
        setTimeout(() => {
            scheduleVisibleUpgrades();
            const t = setInterval(() => {
                if ((Date.now() - startedAt) > totalDurationMs) {
                    clearInterval(t);
                    return;
                }
                scheduleVisibleUpgrades();
            }, retryIntervalMs);

            window.addEventListener('scroll', () => scheduleVisibleUpgrades(), { passive: true });
        }, startDelayMs);
    })();
    </script>

        <section id="digital-landing" style="background:#0a2342;color:#fff;padding:64px 0 48px 0;position:relative;overflow:hidden;">
            <canvas id="particle-bg" style="position:absolute;left:0;top:0;width:100%;height:100%;z-index:0;pointer-events:none;"></canvas>
            <canvas id="network-bg" style="position:absolute;left:0;top:0;width:100%;height:100%;z-index:1;pointer-events:none;opacity:1;"></canvas>
            <img src="img/bg.png" alt="Peta Digital Indonesia" style="position:absolute;left:0;top:0;width:100%;height:100%;object-fit:cover;z-index:0;pointer-events:none;opacity:1;">
            <div class="container" style="position:relative;z-index:2;max-width:1200px;">
                <div style="max-width:700px;margin:0 0 48px 0;text-align:left;">
                      <h2 style="font-size:2.8rem;font-weight:900;line-height:1.13;margin-bottom:18px;text-shadow:0 4px 24px #0a2342c0;letter-spacing:0.5px;">Transformasi Digital<br>Diskominfo Karanganyar</h2>
                      <p style="font-size:1.22rem;opacity:0.93;line-height:1.7;margin-bottom:28px;max-width:600px;">Diskominfo Karanganyar berkomitmen mendorong keterbukaan informasi, penguatan infrastruktur TIK, dan pelayanan publik berbasis digital untuk kemajuan masyarakat Karanganyar.</p>
                    <a href="#" style="color:#1de9b6;font-weight:700;font-size:1.13em;text-decoration:none;display:inline-flex;align-items:center;gap:8px;background:rgba(29,233,182,0.10);border-radius:24px;padding:10px 28px 10px 18px;box-shadow:0 2px 12px #1de9b622;transition:background 0.2s;">→ Baca Selengkapnya</a>
                </div>
                <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(240px,1fr));gap:32px;">
                    <div class="digital-card" style="background:rgba(255,255,255,0.10);border-radius:18px;padding:32px 26px;text-align:left;box-shadow:0 2px 16px rgba(30,60,114,0.13);backdrop-filter:blur(2px);display:flex;flex-direction:column;align-items:flex-start;gap:12px;min-height:210px;">
                        <div style="font-size:2.6em;line-height:1;">🌐</div>
                        <div style="font-weight:800;font-size:1.13em;letter-spacing:0.2px;">LAYANAN INFORMASI PUBLIK</div>
                        <div style="font-size:1.01em;opacity:0.90;">Memberikan layanan informasi publik yang transparan, cepat, dan akurat untuk masyarakat Karanganyar.</div>
                    </div>
                    <div class="digital-card" style="background:rgba(255,255,255,0.10);border-radius:18px;padding:32px 26px;text-align:left;box-shadow:0 2px 16px rgba(30,60,114,0.13);backdrop-filter:blur(2px);display:flex;flex-direction:column;align-items:flex-start;gap:12px;min-height:210px;">
                        <div style="font-size:2.6em;line-height:1;">💻</div>
                        <div style="font-weight:800;font-size:1.13em;letter-spacing:0.2px;">TEKNOLOGI DAN INFRASTRUKTUR</div>
                        <div style="font-size:1.01em;opacity:0.90;">Mengembangkan infrastruktur TIK dan sistem digital untuk mendukung pelayanan publik yang efisien dan modern.</div>
                    </div>
                    <div class="digital-card" style="background:rgba(255,255,255,0.10);border-radius:18px;padding:32px 26px;text-align:left;box-shadow:0 2px 16px rgba(30,60,114,0.13);backdrop-filter:blur(2px);display:flex;flex-direction:column;align-items:flex-start;gap:12px;min-height:210px;">
                        <div style="font-size:2.6em;line-height:1;">📢</div>
                        <div style="font-weight:800;font-size:1.13em;letter-spacing:0.2px;">DISSEMINASI INFORMASI</div>
                        <div style="font-size:1.01em;opacity:0.90;">Menyebarluaskan informasi pembangunan daerah dan kebijakan pemerintah secara efektif kepada masyarakat.</div>
                    </div>
                    <div class="digital-card" style="background:rgba(255,255,255,0.10);border-radius:18px;padding:32px 26px;text-align:left;box-shadow:0 2px 16px rgba(30,60,114,0.13);backdrop-filter:blur(2px);display:flex;flex-direction:column;align-items:flex-start;gap:12px;min-height:210px;">
                        <div style="font-size:2.6em;line-height:1;">🤝</div>
                        <div style="font-weight:800;font-size:1.13em;letter-spacing:0.2px;">KOLABORASI & PARTISIPASI</div>
                        <div style="font-size:1.01em;opacity:0.90;">Mendorong kolaborasi dan partisipasi aktif masyarakat serta stakeholder dalam transformasi digital Karanganyar.</div>
                    </div>
                </div>
            </div>
            <!-- Animasi background garis peta -->
            <svg id="digital-bg-anim" width="100%" height="100%" viewBox="0 0 1600 600" style="position:absolute;left:0;top:0;width:100%;height:100%;z-index:1;pointer-events:none;opacity:0.22;">
                <polyline id="animline1" points="0,400 400,300 800,350 1200,250 1600,300" stroke="#fff" stroke-width="2" fill="none"/>
                <polyline id="animline2" points="0,500 300,420 700,480 1100,410 1600,470" stroke="#1de9b6" stroke-width="2" fill="none"/>
                <!-- Titik-titik animasi -->
                <circle id="dot1" cx="200" cy="320" r="6" fill="#1de9b6" opacity="0.7"/>
                <circle id="dot2" cx="600" cy="420" r="5" fill="#fff" opacity="0.7"/>
                <circle id="dot3" cx="1000" cy="280" r="7" fill="#1de9b6" opacity="0.7"/>
                <circle id="dot4" cx="1400" cy="370" r="5" fill="#fff" opacity="0.7"/>
                <circle id="dot5" cx="900" cy="500" r="4" fill="#1de9b6" opacity="0.7"/>
                <!-- Garis informatika -->
                <line id="infline1" x1="300" y1="200" x2="500" y2="100" stroke="#1de9b6" stroke-width="2" opacity="0.5"/>
                <line id="infline2" x1="1200" y1="180" x2="1350" y2="300" stroke="#fff" stroke-width="2" opacity="0.5"/>
                <line id="infline3" x1="700" y1="100" x2="900" y2="200" stroke="#1de9b6" stroke-width="2" opacity="0.5"/>
            </svg>
        </section>
        <script>
        // Particle background animation (bintang/point kecil)
        function startParticleBG() {
            const canvas = document.getElementById('particle-bg');
            if (!canvas) return;
            const ctx = canvas.getContext('2d');
            let w = canvas.width = canvas.offsetWidth = window.innerWidth;
            let h = canvas.height = canvas.offsetHeight = document.getElementById('digital-landing').offsetHeight;
            let particles = [];
            const N = 48;
            for (let i = 0; i < N; i++) {
                particles.push({
                    x: Math.random() * w,
                    y: Math.random() * h,
                    r: Math.random() * 1.7 + 0.7,
                    dx: (Math.random() - 0.5) * 0.18,
                    dy: (Math.random() - 0.5) * 0.18,
                    o: Math.random() * 0.5 + 0.3
                });
            }
            function draw() {
                ctx.clearRect(0, 0, w, h);
                for (let p of particles) {
                    ctx.beginPath();
                    ctx.arc(p.x, p.y, p.r, 0, 2 * Math.PI);
                    ctx.fillStyle = `rgba(29,233,182,${p.o})`;
                    ctx.shadowColor = '#1de9b6';
                    ctx.shadowBlur = 8;
                    ctx.fill();
                    ctx.shadowBlur = 0;
                    // Gerak
                    p.x += p.dx;
                    p.y += p.dy;
                    if (p.x < 0 || p.x > w) p.dx *= -1;
                    if (p.y < 0 || p.y > h) p.dy *= -1;
                }
                requestAnimationFrame(draw);
            }
            draw();
            window.addEventListener('resize', () => {
                w = canvas.width = canvas.offsetWidth = window.innerWidth;
                h = canvas.height = canvas.offsetHeight = document.getElementById('digital-landing').offsetHeight;
            });
        }
        startParticleBG();
        </script>
        <script>
        // Animasi garis SVG dan titik informatika
        function animatePolyline(id, amplitude, speed) {
            const poly = document.getElementById(id);
            if (!poly) return;
            const base = poly.getAttribute('points').split(' ').map(pt => pt.split(',').map(Number));
            let t = 0;
            setInterval(() => {
                t += 0.03 * speed;
                const pts = base.map(([x, y], i) => [x, y + Math.sin(t + i) * amplitude]);
                poly.setAttribute('points', pts.map(pt => pt.join(',')).join(' '));
            }, 30);
        }
        animatePolyline('animline1', 18, 1);
        animatePolyline('animline2', 12, 1.5);

        // Animasi titik-titik bergerak
        function animateDot(id, baseX, baseY, ampX, ampY, speed, phase=0) {
            const dot = document.getElementById(id);
            if (!dot) return;
            let t = phase;
            setInterval(() => {
                t += 0.03 * speed;
                const x = baseX + Math.sin(t) * ampX;
                const y = baseY + Math.cos(t) * ampY;
                dot.setAttribute('cx', x);
                dot.setAttribute('cy', y);
            }, 30);
        }
        animateDot('dot1', 200, 320, 18, 10, 1.2);
        animateDot('dot2', 600, 420, 14, 8, 1.5, 1);
        animateDot('dot3', 1000, 280, 20, 12, 1.1, 2);
        animateDot('dot4', 1400, 370, 16, 9, 1.3, 3);
        animateDot('dot5', 900, 500, 12, 7, 1.7, 4);

        // Animasi garis informatika (panjang-pendek)
        function animateLine(id, base, amp, speed, phase=0) {
            const line = document.getElementById(id);
            if (!line) return;
            let t = phase;
            setInterval(() => {
                t += 0.03 * speed;
                const dx = Math.sin(t) * amp;
                const dy = Math.cos(t) * amp;
                line.setAttribute('x2', base.x2 + dx);
                line.setAttribute('y2', base.y2 + dy);
            }, 30);
        }
        animateLine('infline1', {x2:500,y2:100}, 18, 1.1);
        animateLine('infline2', {x2:1350,y2:300}, 14, 1.3, 1);
        animateLine('infline3', {x2:900,y2:200}, 16, 1.5, 2);
        </script>

    <?php
        // Footer taskbar (gunakan layout yang sama persis di semua halaman)
        $footer_show_map = true;
        $footer_year = date('Y');
        include __DIR__ . '/partials/site_footer.php';
    ?>

    <?php include __DIR__ . '/partials/floating_widget.php'; ?>

    <script>
        function getRefreshButtonEl() {
            return document.getElementById('btn-refresh-top') || document.getElementById('btn-refresh');
        }

        function refreshNews() {
            const btn = getRefreshButtonEl();
            const originalHtml = btn ? btn.innerHTML : null;
            if (btn) {
                btn.disabled = true;
                btn.textContent = '⏳ Sedang memuat...';
            }
            
            fetch('refresh_news.php?force=1&meta=1', { cache: 'no-store' })
                .then(response => response.json())
                .then(data => {
                    if (data && data.success) {
                        const msg = data.message ? String(data.message) : (data.refreshed ? 'Berita berhasil diperbarui' : 'Tidak ada pembaruan');
                        const count = (typeof data.count === 'number') ? data.count : null;
                        alert((data.refreshed ? '✅ ' : 'ℹ️ ') + msg + (count !== null ? ` (total ${count})` : ''));
                        if (data.refreshed) {
                            location.reload();
                        }
                    } else {
                        alert('❌ Gagal memperbarui berita');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('❌ Error: ' + error.message);
                })
                .finally(() => {
                    if (btn) {
                        btn.disabled = false;
                        if (originalHtml !== null) btn.innerHTML = originalHtml;
                    }
                });
        }

        // Auto-refresh berita setiap 10 menit (600000 ms)
        function autoRefreshNews() {
            fetch('refresh_news.php?auto=1&meta=1', { cache: 'no-store' })
                .then(response => response.json())
                .then(data => {
                    if (data && data.success && data.refreshed) {
                        console.log('✅ Auto-refresh: berita diperbarui');
                        location.reload();
                    }
                })
                .catch(error => {
                    console.error('Auto-refresh error:', error);
                });
        }
        
        // Set interval untuk auto-refresh setiap 10 menit
        setInterval(autoRefreshNews, 600000); // 600000 ms = 10 menit

        // Weather Widget Script - Horizontal Scroll Version
        const weatherIcons = {
            'Cerah': '<path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8z"/><path d="M12 6c-3.31 0-6 2.69-6 6s2.69 6 6 6 6-2.69 6-6-2.69-6-6-6z"/>',
            'Berawan': '<path d="M19.35 10.04C18.67 6.59 15.64 4 12 4 9.11 4 6.6 5.64 5.35 8.04 2.34 8.36 0 10.91 0 14c0 3.31 2.69 6 6 6h13c2.76 0 5-2.24 5-5 0-2.64-2.05-4.78-4.65-4.96z"/>',
            'Hujan': '<path d="M4.5 16.5h1v2h-1v-2zm3 0h1v2h-1v-2zm3 0h1v2h-1v-2zm3 0h1v2h-1v-2zm3 0h1v2h-1v-2zM19.35 10.04C18.67 6.59 15.64 4 12 4 9.11 4 6.6 5.64 5.35 8.04 2.34 8.36 0 10.91 0 14c0 3.31 2.69 6 6 6h13c2.76 0 5-2.24 5-5 0-2.64-2.05-4.78-4.65-4.96z"/>',
            'Hujan Rintik': '<path d="M4.5 16.5h1v1.5h-1v-1.5zm3 0h1v1.5h-1v-1.5zm3 0h1v1.5h-1v-1.5zm3 0h1v1.5h-1v-1.5zm3 0h1v1.5h-1v-1.5zM19.35 10.04C18.67 6.59 15.64 4 12 4 9.11 4 6.6 5.64 5.35 8.04 2.34 8.36 0 10.91 0 14c0 3.31 2.69 6 6 6h13c2.76 0 5-2.24 5-5 0-2.64-2.05-4.78-4.65-4.96z"/>',
            'Mendung': '<path d="M19.35 10.04C18.67 6.59 15.64 4 12 4 9.11 4 6.6 5.64 5.35 8.04 2.34 8.36 0 10.91 0 14c0 3.31 2.69 6 6 6h13c2.76 0 5-2.24 5-5 0-2.64-2.05-4.78-4.65-4.96z"/><circle cx="8" cy="17" r="1" opacity="0.5"/><circle cx="12" cy="17" r="1" opacity="0.5"/><circle cx="16" cy="17" r="1" opacity="0.5"/>'
        };

        const weatherData = {
            'Karanganyar': { temp: 27, condition: 'Cerah', humidity: 70 },
            'Jaten': { temp: 28, condition: 'Berawan', humidity: 68 },
            'Colomadu': { temp: 27, condition: 'Cerah', humidity: 65 },
            'Gondangrejo': { temp: 26, condition: 'Berawan', humidity: 72 },
            'Kebakkramat': { temp: 25, condition: 'Hujan Rintik', humidity: 80 },
            'Mojogedang': { temp: 24, condition: 'Mendung', humidity: 75 },
            'Jatipuro': { temp: 23, condition: 'Hujan Rintik', humidity: 82 },
            'Jatiyoso': { temp: 22, condition: 'Mendung', humidity: 78 },
            'Jumapolo': { temp: 26, condition: 'Berawan', humidity: 70 },
            'Jumantono': { temp: 25, condition: 'Cerah', humidity: 68 },
            'Matesih': { temp: 24, condition: 'Berawan', humidity: 73 },
            'Tawangmangu': { temp: 20, condition: 'Hujan Rintik', humidity: 85 },
            'Ngargoyoso': { temp: 19, condition: 'Hujan', humidity: 88 },
            'Karangpandan': { temp: 23, condition: 'Mendung', humidity: 76 },
            'Kerjo': { temp: 25, condition: 'Berawan', humidity: 71 },
            'Jenawi': { temp: 21, condition: 'Hujan Rintik', humidity: 83 },
            'Tasikmadu': { temp: 27, condition: 'Cerah', humidity: 67 }
        };

        let currentTime = '';

        function updateClock() {
            const now = new Date();
            const hours = String(now.getHours()).padStart(2, '0');
            const minutes = String(now.getMinutes()).padStart(2, '0');
            const seconds = String(now.getSeconds()).padStart(2, '0');
            currentTime = hours + ':' + minutes + ':' + seconds;
            
            // Update all time displays in cards
            const timeElements = document.querySelectorAll('.weather-card-time');
            timeElements.forEach(el => {
                el.textContent = currentTime;
            });
        }

        function getWeatherIconColor(condition) {
            if (condition.includes('Hujan')) {
                return '#6FB1FC';
            } else if (condition === 'Mendung' || condition === 'Berawan') {
                return '#B0BEC5';
            } else {
                return '#FBB040';
            }
        }

        function createWeatherCard(kecamatan, data) {
            const iconPath = weatherIcons[data.condition] || weatherIcons['Cerah'];
            const iconColor = getWeatherIconColor(data.condition);
            
            return `
                <div class="weather-card" style="width:84px;min-width:84px;box-sizing:border-box;background:rgba(255,255,255,0.1);backdrop-filter:blur(10px);padding:4px 6px;border-radius:8px;border:1px solid rgba(255,255,255,0.2);display:flex;flex-direction:column;gap:3px;cursor:pointer;transition:all 0.3s ease;">
                    <div style="display:flex;align-items:center;justify-content:space-between;gap:6px;">
                        <span style="color:#fff;font-weight:700;font-size:0.6rem;">${kecamatan}</span>
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="currentColor" style="color:${iconColor};flex-shrink:0;">
                            ${iconPath}
                        </svg>
                    </div>
                    <div style="display:flex;align-items:baseline;gap:4px;">
                        <span style="color:#fff;font-weight:800;font-size:0.9rem;">${data.temp}°</span>
                        <span style="color:#fff;font-size:0.56rem;opacity:0.8;max-width:48px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">${data.condition}</span>
                    </div>
                    <div style="display:flex;align-items:center;gap:4px;">
                        <span class="weather-card-time" style="color:#fff;font-size:0.52rem;opacity:0.7;font-family:monospace;">${currentTime}</span>
                        <span style="color:#fff;font-size:0.52rem;opacity:0.7;">WIB</span>
                    </div>
                </div>
            `;
        }

        function initWeatherCards() {
            const container = document.getElementById('weather-cards');
            if (!container) return;
            
            let cardsHTML = '';
            for (const [kecamatan, data] of Object.entries(weatherData)) {
                cardsHTML += createWeatherCard(kecamatan, data);
            }
            
            container.innerHTML = cardsHTML;
            
            // Add hover effect
            const cards = container.querySelectorAll('.weather-card');
            cards.forEach(card => {
                card.addEventListener('mouseenter', function() {
                    this.style.background = 'rgba(255,255,255,0.15)';
                    this.style.transform = 'translateY(-2px)';
                    this.style.boxShadow = '0 4px 12px rgba(0,0,0,0.2)';
                });
                card.addEventListener('mouseleave', function() {
                    this.style.background = 'rgba(255,255,255,0.1)';
                    this.style.transform = 'translateY(0)';
                    this.style.boxShadow = 'none';
                });
            });
            
            // Hide scrollbar
            const wrapper = document.getElementById('weather-scroll-wrapper');
            if (wrapper) {
                wrapper.style.scrollbarWidth = 'none';
                wrapper.style.msOverflowStyle = 'none';
                // Add webkit scrollbar hide
                const style = document.createElement('style');
                style.textContent = '#weather-scroll-wrapper::-webkit-scrollbar { display: none; }';
                document.head.appendChild(style);
            }
        }

        // Initialize
        updateClock();
        setInterval(updateClock, 1000);
        initWeatherCards();

        function updateWeather(kecamatan) {
            const data = weatherData[kecamatan];
            if (data) {
                document.getElementById('weather-temp').textContent = `${data.temp}°C`;
                document.getElementById('weather-desc').textContent = data.condition;
                
                // Update icon
                const iconSvg = document.getElementById('weather-icon');
                const iconPath = weatherIcons[data.condition] || weatherIcons['Cerah'];
                iconSvg.innerHTML = iconPath;
                
                // Update color based on condition
                if (data.condition.includes('Hujan')) {
                    iconSvg.style.color = '#6FB1FC';
                } else if (data.condition === 'Mendung' || data.condition === 'Berawan') {
                    iconSvg.style.color = '#B0BEC5';
                } else {
                    iconSvg.style.color = '#FBB040';
                }
            }
        }

        // Initialize
        updateClock();
        setInterval(updateClock, 1000);
        updateWeather('Karanganyar');

        // Optional: Fetch real weather data from API
        // Uncomment and configure if you have weather API key
        /*
        async function fetchRealWeather(kecamatan) {
            try {
                const API_KEY = 'YOUR_API_KEY_HERE';
                const url = `https://api.openweathermap.org/data/2.5/weather?q=${kecamatan},ID&units=metric&appid=${API_KEY}&lang=id`;
                const response = await fetch(url);
                const data = await response.json();
                
                if (data.main) {
                    document.getElementById('weather-temp').textContent = `${Math.round(data.main.temp)}°C`;
                    document.getElementById('weather-desc').textContent = data.weather[0].description;
                }
            } catch (error) {
                console.error('Error fetching weather:', error);
            }
        }
        */
    </script>
</body>
</html>
