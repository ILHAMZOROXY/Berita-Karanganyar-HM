<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Direct test of SerpAPI call using cURL
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
echo "URL: " . $url . "\n\n";
echo "Making request with cURL...\n";

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
curl_setopt($ch, CURLOPT_TIMEOUT, 30);
curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36');

$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curl_error = curl_error($ch);
curl_close($ch);

if ($response === false) {
    echo "ERROR: cURL failed\n";
    echo "Error: " . $curl_error . "\n";
    exit(1);
}

echo "HTTP Code: " . $http_code . "\n";
echo "Response received: " . strlen($response) . " bytes\n\n";

$json = json_decode($response, true);
if (!$json) {
    echo "ERROR: Invalid JSON response\n";
    echo "Response preview: " . substr($response, 0, 200) . "\n";
    exit(1);
}

// Check for API errors
if (isset($json['error'])) {
    echo "ERROR from SerpAPI: " . $json['error'] . "\n";
    if (isset($json['message'])) {
        echo "Message: " . $json['message'] . "\n";
    }
    exit(1);
}

if (!isset($json['news_results'])) {
    echo "WARNING: No news_results in response\n";
    echo "Available keys: " . implode(', ', array_keys($json)) . "\n";
    echo "\nFull response:\n";
    echo json_encode($json, JSON_PRETTY_PRINT) . "\n";
    exit(1);
}

echo "SUCCESS! Found " . count($json['news_results']) . " news items\n\n";

// Display first 3 items
$count = min(3, count($json['news_results']));
for ($i = 0; $i < $count; $i++) {
    $item = $json['news_results'][$i];
    echo "Item #" . ($i + 1) . ":\n";
    echo "  Title: " . ($item['title'] ?? 'N/A') . "\n";
    echo "  Source: " . ($item['source'] ?? 'N/A') . "\n";
    echo "  Link: " . ($item['link'] ?? 'N/A') . "\n";
    echo "  Date: " . ($item['date'] ?? 'N/A') . "\n";
    echo "\n";
}

echo "\nSerpAPI is working correctly!\n";
?>
