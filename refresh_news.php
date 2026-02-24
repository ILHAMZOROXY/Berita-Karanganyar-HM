<?php
/**
 * API endpoint untuk refresh berita dari Google News
 * Gunakan: refresh_news.php atau refresh_news.php?force=1
 */

include 'config/database.php';
include 'config/news_fetcher.php';

// Cek apakah user meminta force refresh
$force = isset($_GET['force']) ? true : false;
// Auto mode: refresh hanya jika cache sudah melewati TTL
$auto = isset($_GET['auto']) ? true : false;
// Meta mode: return metadata saja (tanpa payload data penuh)
$meta = isset($_GET['meta']) ? true : false;

// Inisialisasi fetcher
$news_fetcher = new NewsFetcher();

$cache_file = __DIR__ . '/cache/karanganyar_news.json';
$cache_mtime_before = file_exists($cache_file) ? (int) @filemtime($cache_file) : 0;
$cache_age_before = $cache_mtime_before > 0 ? (time() - $cache_mtime_before) : null;
$cache_ttl = method_exists($news_fetcher, 'getCacheTtlSeconds') ? (int) $news_fetcher->getCacheTtlSeconds() : 300;

// Untuk auto refresh: jika cache masih fresh, jangan lakukan apa-apa
if ($auto && !$force && $cache_mtime_before > 0 && $cache_age_before !== null && $cache_age_before < $cache_ttl) {
    header('Content-Type: application/json');
    echo json_encode([
        'success' => true,
        'refreshed' => false,
        'skipped' => true,
        'skip_reason' => 'cache_fresh',
        'message' => 'Cache masih fresh, refresh dilewati',
        'count' => null,
        'cache_mtime' => $cache_mtime_before,
        'cache_age' => $cache_age_before,
        'cache_ttl' => $cache_ttl,
        'timestamp' => date('Y-m-d H:i:s')
    ]);
    exit;
}

// Anti-spam: lock file supaya tidak banyak request refresh barengan
$lock_path = __DIR__ . '/cache/refresh.lock';
$lock_fp = @fopen($lock_path, 'c');
if ($lock_fp) {
    if (!@flock($lock_fp, LOCK_EX | LOCK_NB)) {
        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'refreshed' => false,
            'skipped' => true,
            'skip_reason' => 'locked',
            'message' => 'Refresh sedang berjalan, permintaan dilewati',
            'count' => null,
            'cache_mtime' => $cache_mtime_before,
            'cache_age' => $cache_age_before,
            'cache_ttl' => $cache_ttl,
            'timestamp' => date('Y-m-d H:i:s')
        ]);
        exit;
    }
}

// Jika force refresh, clear cache terlebih dahulu
if ($force) {
    $news_fetcher->clearCache();
}

// Ambil berita baru (minta lebih untuk memastikan minimum 48)
$berita_list = $news_fetcher->getNewsFromRSS(60);

// Upgrade gambar ke og:image secara terbatas saat refresh (biar beranda tetap cepat).
// Jangan lakukan di mode meta agar request ringan.
if (!$meta && is_array($berita_list) && !empty($berita_list)) {
    $maxEnhance = 12;
    $enhanced = 0;
    $startEnhance = microtime(true);
    foreach ($berita_list as $idx => $item) {
        if ($enhanced >= $maxEnhance) break;
        if ((microtime(true) - $startEnhance) > 10) break;
        $link = $item['link'] ?? '';
        if (empty($link)) continue;

        $currentImg = (string) ($item['gambar'] ?? '');
        $isPlaceholder = (strpos($currentImg, 'img/no-image.png') !== false) || (strpos($currentImg, '/img/no-image.png') !== false);
        $isLikelyLowRes = (strpos($currentImg, 'encrypted-tbn0.gstatic.com') !== false)
            || (strpos($currentImg, 'tbn') !== false)
            || (strpos($currentImg, 'serpapi.com/') !== false);
        if (!empty($currentImg) && !$isPlaceholder && !$isLikelyLowRes) {
            continue;
        }

        $og = $news_fetcher->getOgImage($link, $currentImg);
        if (!empty($og) && $og !== $currentImg) {
            $berita_list[$idx]['gambar'] = $og;
            $enhanced++;
        }
    }

    // Simpan ulang hasil yang sudah di-upgrade
    if (method_exists($news_fetcher, 'saveNewsToStorage')) {
        $news_fetcher->saveNewsToStorage($berita_list);
    }
}

$cache_mtime_after = file_exists($cache_file) ? (int) @filemtime($cache_file) : 0;
$cache_age_after = $cache_mtime_after > 0 ? (time() - $cache_mtime_after) : null;
$refreshed = ($force && $cache_mtime_after > 0) || ($cache_mtime_after > 0 && $cache_mtime_after !== $cache_mtime_before);

if ($lock_fp) {
    @flock($lock_fp, LOCK_UN);
    @fclose($lock_fp);
}

// Return JSON response
header('Content-Type: application/json');
echo json_encode([
    'success' => true,
    'refreshed' => $refreshed,
    'skipped' => false,
    'message' => $refreshed ? 'Berita berhasil diperbarui' : 'Tidak ada pembaruan (menggunakan cache)',
    'count' => is_array($berita_list) ? count($berita_list) : 0,
    'cache_mtime' => $cache_mtime_after,
    'cache_age' => $cache_age_after,
    'cache_ttl' => $cache_ttl,
    'data' => $meta ? null : $berita_list,
    'timestamp' => date('Y-m-d H:i:s')
]);
?>
