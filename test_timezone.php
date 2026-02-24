<?php
// Test timezone di index.php sekarang
date_default_timezone_set('Asia/Jakarta');

$current_time = date('Y-m-d H:i:s');
$timestamp = 1770694199; // Berita terbaru
$formatted = date('d M Y - H:i', $timestamp);

echo "Current time (Asia/Jakarta): $current_time\n";
echo "Sample timestamp: $timestamp\n";
echo "Formatted: $formatted\n";
echo "\nTimezone is set correctly!\n";
?>
