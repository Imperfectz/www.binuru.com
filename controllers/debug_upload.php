<?php
// TEMPORARY DEBUG — delete after fixing
header('Content-Type: text/plain');
echo "=== POST ===\n";
print_r($_POST);
echo "\n=== FILES ===\n";
print_r($_FILES);
echo "\n=== php.ini upload settings ===\n";
echo "file_uploads: "       . ini_get('file_uploads')       . "\n";
echo "upload_max_filesize: " . ini_get('upload_max_filesize') . "\n";
echo "post_max_size: "       . ini_get('post_max_size')       . "\n";
echo "upload_tmp_dir: "      . ini_get('upload_tmp_dir')      . "\n";
echo "\n=== Upload dir writable? ===\n";
$dir = __DIR__ . '/../uploads/covers/';
echo "Path: $dir\n";
echo "Exists: " . (is_dir($dir) ? 'yes' : 'no') . "\n";
echo "Writable: " . (is_writable(dirname($dir)) ? 'yes' : 'no') . "\n";
