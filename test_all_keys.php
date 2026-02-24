<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Test all available SerpAPI keys
$api_keys = include __DIR__ . '/config/api_keys.php';
$all_keys = $api_keys['serpapi_keys'] ?? [];

if (empty($all_keys)) {
    echo "ERROR: No SerpAPI keys configured\n";
    exit(1);
}

echo "Testing " . count($all_keys) . " API key(s)...\n\n";

$query = 'Karanganyar';
$working_key = null;

foreach ($all_keys as $index => $key) {
    echo "Testing key #" . ($index + 1) . ": " . substr($key, 0, 10) . "...\n";
    
    $params = [
        'engine' => 'google',
        'q' => $query,
        'tbm' => 'nws',
        'hl' => 'id',
        'gl' => 'id',
        'api_key' => $key,
        'num' => 5
    ];
    
    $url = 'https://serpapi.com/search.json?' . http_build_query($params);
    
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
    curl_setopt($ch, CURLOPT_TIMEOUT, 15);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0');
    
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if ($response === false) {
        echo "  ❌ Connection failed\n\n";
        continue;
    }
    
    echo "  HTTP Code: " . $http_code . "\n";
    
    $json = json_decode($response, true);
    
    if (isset($json['error'])) {
        echo "  ❌ Error: " . $json['error'] . "\n";
        if ($http_code == 429) {
            echo "  ⚠️  This key has run out of searches\n";
        }
        echo "\n";
        continue;
    }
    
    if (!isset($json['news_results']) || empty($json['news_results'])) {
        echo "  ⚠️  No news results returned\n\n";
        continue;
    }
    
    echo "  ✅ SUCCESS! Found " . count($json['news_results']) . " news items\n";
    echo "  First item: " . ($json['news_results'][0]['title'] ?? 'N/A') . "\n";
    $working_key = $key;
    echo "\n";
    break;
}

if ($working_key) {
    echo "✅ Found working API key!\n";
    echo "Key: " . substr($working_key, 0, 20) . "...\n";
    exit(0);
} else {
    echo "❌ No working API keys found\n";
    echo "\nPossible solutions:\n";
    echo "1. Get a new API key from https://serpapi.com/\n";
    echo "2. Wait for monthly reset if using free tier\n";
    echo "3. Upgrade your SerpAPI plan\n";
    exit(1);
}
?>
