<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "=== Test News Fetcher ===\n\n";

// Load config
echo "Loading config/database.php...\n";
include 'config/database.php';
echo "✓ Database config loaded\n\n";

echo "Loading config/news_fetcher.php...\n";
include 'config/news_fetcher.php';
echo "✓ News Fetcher class loaded\n\n";

// Test instantiate
echo "Creating NewsFetcher instance...\n";
$news_fetcher = new NewsFetcher();
echo "✓ NewsFetcher instantiated\n\n";

// Test getNewsFromRSS
echo "Calling getNewsFromRSS(50)...\n";
$berita = $news_fetcher->getNewsFromRSS(50);
echo "✓ getNewsFromRSS completed\n";
echo "Result type: " . gettype($berita) . "\n";

if (is_array($berita)) {
    echo "Array count: " . count($berita) . "\n";
    if (count($berita) > 0) {
        echo "First item keys: " . implode(', ', array_keys($berita[0])) . "\n";
        echo "\nSample first item:\n";
        var_dump($berita[0]);
    }
} else {
    echo "ERROR: Expected array, got " . gettype($berita) . "\n";
    var_dump($berita);
}

?>
