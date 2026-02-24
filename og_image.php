<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

header('Content-Type: application/json; charset=utf-8');

// DB optional (untuk persist gambar ke news_items)
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/config/news_fetcher.php';

function is_private_host(string $host): bool {
    $hostLower = strtolower($host);
    if ($hostLower === 'localhost' || $hostLower === 'localhost.localdomain') return true;

    // If host is an IP, block private/reserved ranges
    if (filter_var($host, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
        $ipLong = ip2long($host);
        if ($ipLong === false) return true;

        $ranges = [
            ['0.0.0.0', '0.255.255.255'],
            ['10.0.0.0', '10.255.255.255'],
            ['127.0.0.0', '127.255.255.255'],
            ['169.254.0.0', '169.254.255.255'],
            ['172.16.0.0', '172.31.255.255'],
            ['192.168.0.0', '192.168.255.255'],
            ['224.0.0.0', '239.255.255.255'],
            ['240.0.0.0', '255.255.255.255'],
        ];

        foreach ($ranges as [$start, $end]) {
            $s = ip2long($start);
            $e = ip2long($end);
            if ($s !== false && $e !== false && $ipLong >= $s && $ipLong <= $e) return true;
        }

        return false;
    }

    if (filter_var($host, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)) {
        return ($hostLower === '::1');
    }

    return false;
}

$url = isset($_GET['url']) ? trim((string) $_GET['url']) : '';
$id = isset($_GET['id']) ? trim((string) $_GET['id']) : '';
if ($url === '') {
    echo json_encode(['ok' => false, 'error' => 'missing_url']);
    exit;
}

if ($id !== '' && (!preg_match('/^[a-f0-9]{32}$/i', $id))) {
    // Jika id tidak valid, abaikan saja (jangan fail request)
    $id = '';
}

$parts = @parse_url($url);
if (!is_array($parts) || empty($parts['scheme']) || empty($parts['host'])) {
    echo json_encode(['ok' => false, 'error' => 'invalid_url']);
    exit;
}

$scheme = strtolower((string) $parts['scheme']);
if ($scheme !== 'http' && $scheme !== 'https') {
    echo json_encode(['ok' => false, 'error' => 'unsupported_scheme']);
    exit;
}

$host = (string) $parts['host'];
if (is_private_host($host)) {
    echo json_encode(['ok' => false, 'error' => 'blocked_host']);
    exit;
}

try {
    $fetcher = new NewsFetcher();
    $image = $fetcher->getOgImage($url, '');
    if ($id !== '' && $image !== '') {
        $fetcher->updateNewsImageInDb($id, $image);
    }
    echo json_encode(['ok' => true, 'image' => (string) $image]);
} catch (Exception $e) {
    echo json_encode(['ok' => false, 'error' => 'exception']);
}
