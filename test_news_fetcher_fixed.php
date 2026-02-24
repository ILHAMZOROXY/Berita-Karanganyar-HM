<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "Testing News Fetcher with improved cURL support...\n\n";

include 'config/database.php';
include 'config/news_fetcher.php';

$fetcher = new NewsFetcher();

echo "Fetching news from SerpAPI...\n";
$news = $fetcher->getNewsFromRSS(5);

if (empty($news)) {
    echo "❌ No news returned\n";
    exit(1);
}

echo "✅ SUCCESS! Retrieved " . count($news) . " news items\n\n";

foreach ($news as $index => $item) {
    echo "Item #" . ($index + 1) . ":\n";
    echo "  Judul: " . $item['judul'] . "\n";
    echo "  Sumber: " . $item['sumber'] . "\n";
    echo "  Tanggal: " . $item['tanggal_publikasi'] . "\n";
    echo "  Link: " . substr($item['link'], 0, 60) . "...\n";
    echo "\n";
}

echo "✅ News fetcher is working correctly!\n";
?>
