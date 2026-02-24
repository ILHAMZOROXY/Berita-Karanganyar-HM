<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Direct test of SerpAPI call
$api_keys = include __DIR__ . '/config/api_keys.php';
$serpapi_key = $api_keys['serpapi_key'] ?? null;

if (!$serpapi_key) {
    echo "ERROR: SerpAPI key not found\n";
    exit(1);
}

echo "SerpAPI Key found: " . substr($serpapi_key, 0, 10) . "...\n\n";

$query = 'Karanganyar';
$params = [
    'engine' => 'google',
    'q' => $query,
    'tbm' => 'nws',
    'hl' => 'id',
    'gl' => 'id',
    'api_key' => $serpapi_key,
    'num' => 10,
    'start' => 0
];

$url = 'https://serpapi.com/search.json?' . http_build_query($params);
echo "URL: " . substr($url, 0, 50) . "...\n";
echo "Making request...\n";

$response = @file_get_contents($url);
if ($response === false) {
    echo "ERROR: Cannot fetch from SerpAPI\n";
    exit(1);
}

echo "Response received: " . strlen($response) . " bytes\n";

$json = json_decode($response, true);
if (!$json) {
    echo "ERROR: Invalid JSON response\n";
    echo "Response preview: " . substr($response, 0, 100) . "\n";
    exit(1);
}

if (!isset($json['news_results'])) {
    echo "ERROR: No news_results in response\n";
    echo "Keys: " . implode(', ', array_keys($json)) . "\n";
    exit(1);
}

echo "news_results count: " . count($json['news_results']) . "\n\n";

echo "=== Processing Results ===\n";
$count = 0;
foreach ($json['news_results'] as $i => $item) {
    $count++;
    echo "\nItem #$count:\n";
    echo "  title: " . (isset($item['title']) ? substr($item['title'], 0, 50) : 'MISSING') . "\n";
    echo "  link: " . (isset($item['link']) ? substr($item['link'], 0, 50) : 'MISSING') . "\n";
    echo "  published_at: " . (isset($item['published_at']) ? $item['published_at'] : 'MISSING') . "\n";
    
    // Test timestamp calculation
    if (!empty($item['published_at'])) {
        try {
            $dt = new DateTimeImmutable($item['published_at'], new DateTimeZone('UTC'));
            $timestamp = $dt->getTimestamp();
            echo "  timestamp: $timestamp\n";
            echo "  age (seconds): " . (time() - $timestamp) . "\n";
            echo "  age 30 days check: " . ((time() - $timestamp) > 2592000 ? 'SKIP' : 'OK') . "\n";
        } catch (Exception $e) {
            echo "  timestamp error: " . $e->getMessage() . "\n";
        }
    }
}

echo "\n=== Summary ===\n";
echo "Processed $count items successfully\n";

?>
