<?php
$context = stream_context_create(['http' => ['ignore_errors' => true]]);
$content = file_get_contents('http://127.0.0.1:8001/debug-check', false, $context);
if (strpos($content, 'Verification Exception message') !== false) {
    echo "SUCCESS: Exception rendered correctly.\n";
} else {
    echo "FAILURE: " . substr($content, 0, 200) . "\n";
}
