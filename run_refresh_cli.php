<?php
// Temporary runner to force refresh when running from CLI
parse_str('force=1&meta=1', $_GET);
include __DIR__ . '/refresh_news.php';
