<?php
// Simple inline test
error_reporting(E_ALL);
ini_set('display_errors', 1);

$api_keys = @include __DIR__ . '/config/api_keys.php';
if (!$api_keys) {
    die("Cannot load api_keys.php");
}

$serpapi_key = $api_keys['serpapi_key'] ?? null;
if (!$serpapi_key) {
    die("No SerpAPI key found");
}

// Test direct API call
$params = [
    'engine' => 'google',
    'q' => 'Karanganyar',
    'tbm' => 'nws',
    'hl' => 'id',
    'gl' => 'id',
    'api_key' => $serpapi_key,
    'num' => 5,
    'start' => 0
];
$url = 'https://serpapi.com/search.json?' . http_build_query($params);
$response = @file_get_contents($url);
if (!$response) {
    die("Cannot fetch from SerpAPI");
}
$json = json_decode($response, true);
if (!$json || !isset($json['news_results'])) {
    die("Invalid SerpAPI response");
}

echo "SUCCESS! Got " . count($json['news_results']) . " news results\n";
echo "First result title: " . substr($json['news_results'][0]['title'], 0, 50) . "\n";
?>
