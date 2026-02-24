<?php
// Debug SerpAPI response
require 'config/api_keys.php';
$keys = include __DIR__ . '/config/api_keys.php';
$api_key = $keys['serpapi_key'] ?? getenv('SERPAPI_KEY');
$url = 'https://serpapi.com/search.json?engine=google&q=Karanganyar&tbm=nws&hl=id&gl=id&api_key=' . rawurlencode($api_key) . '&num=10';
$response = @file_get_contents($url);
if ($response === false) {
    echo "ERROR_FETCH\n";
    exit(1);
}
$json = json_decode($response, true);
if (!$json) {
    echo "INVALID_JSON\n";
    exit(1);
}
file_put_contents(__DIR__ . '/cache/serpapi_raw.json', $response);
echo "Saved raw response to cache/serpapi_raw.json\n";
if (isset($json['news_results']) && is_array($json['news_results'])) {
    echo "news_results count: " . count($json['news_results']) . "\n\n";
    foreach ($json['news_results'] as $i => $item) {
        echo "Item #" . ($i+1) . " keys:\n";
        foreach ($item as $k => $v) {
            if (is_array($v)) echo "  $k => [array]\n";
            else echo "  $k => " . (strlen((string)$v) > 120 ? substr((string)$v,0,120) . '...' : (string)$v) . "\n";
        }
        echo "\n";
    }
} else {
    echo "no news_results field\n";
}

?>