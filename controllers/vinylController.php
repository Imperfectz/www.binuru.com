<?php

session_start();
require_once '../bl/vinylManagement.php';

$vm = new VinylManagement();

/* ---------------------------------------------------------------
 *  Helper: handle a single file-upload field
 *  Returns the relative web path on success, null if no file,
 *  or outputs an error and exits on failure.
 * ------------------------------------------------------------- */
function handleImageUpload($fieldName, $subDir)
{
    // No file selected for this field — skip silently
    if (!isset($_FILES[$fieldName]) || $_FILES[$fieldName]['error'] === UPLOAD_ERR_NO_FILE || empty($_FILES[$fieldName]['name'])) {
        return null;
    }

    $file     = $_FILES[$fieldName];
    $allowed  = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
    $maxBytes = 5 * 1024 * 1024; // 5 MB

    if ($file['error'] !== UPLOAD_ERR_OK) {
        http_response_code(400);
        echo 'Upload error code ' . $file['error'];
        exit;
    }

    // Use finfo if available, fall back to mime_content_type
    if (function_exists('finfo_open')) {
        $finfo    = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);
    } else {
        $mimeType = mime_content_type($file['tmp_name']);
    }

    if (!in_array($mimeType, $allowed, true)) {
        http_response_code(400);
        echo 'Invalid file type (' . $mimeType . '). Only JPEG, PNG, WEBP, or GIF are allowed.';
        exit;
    }

    if ($file['size'] > $maxBytes) {
        http_response_code(400);
        echo 'File too large. Maximum size is 5 MB.';
        exit;
    }

    // Build upload directory path (works on Windows & Linux)
    $uploadDir = rtrim(str_replace('\\', '/', realpath(__DIR__ . '/..')), '/') . '/uploads/' . $subDir . '/';

    if (!is_dir($uploadDir)) {
        if (!mkdir($uploadDir, 0755, true)) {
            http_response_code(500);
            echo 'Could not create upload directory: ' . $uploadDir;
            exit;
        }
    }

    if (!is_writable($uploadDir)) {
        http_response_code(500);
        echo 'Upload directory is not writable: ' . $uploadDir;
        exit;
    }

    $ext      = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $filename = uniqid('img_', true) . '.' . $ext;
    $destPath = $uploadDir . $filename;

    if (!move_uploaded_file($file['tmp_name'], $destPath)) {
        http_response_code(500);
        echo 'Failed to move uploaded file to: ' . $destPath;
        exit;
    }

    return 'uploads/' . $subDir . '/' . $filename;
}

/* ---------------------------------------------------------------
 *  Add or Edit vinyl
 * ------------------------------------------------------------- */
if (isset($_POST['v_title'], $_POST['v_price'], $_POST['v_stock'])) {

    $id     = isset($_POST['v_id']) && $_POST['v_id'] !== '' ? (int)$_POST['v_id'] : null;
    $title  = trim($_POST['v_title']);
    $artist = !empty($_POST['v_artist']) ? (int)$_POST['v_artist'] : null;
    $genre  = !empty($_POST['v_genre'])  ? (int)$_POST['v_genre']  : null;
    $price  = (float)$_POST['v_price'];
    $stock  = (int)$_POST['v_stock'];
    $year   = !empty($_POST['v_year'])   ? (int)$_POST['v_year']   : null;
    $desc   = trim($_POST['v_desc'] ?? '');

    // --- Server-side field validation ---
    $errors = [];
    if ($title === '')  $errors[] = 'Title is required.';
    if ($price <= 0)    $errors[] = 'Price must be greater than 0.';
    if ($stock < 0)     $errors[] = 'Stock cannot be negative.';
    if ($year !== null && ($year < 1900 || $year > (int)date('Y'))) {
        $errors[] = 'Release year must be between 1900 and ' . date('Y') . '.';
    }

    if (!empty($errors)) {
        http_response_code(422);
        echo implode(' ', $errors);
        exit;
    }

    $coverImage = handleImageUpload('v_cover_image', 'covers');
    $vinylImage = handleImageUpload('v_vinyl_image', 'vinyls');

    if ($id) {
        $vm->editVinyl($id, $title, $artist, $genre, $price, $stock, $year, $desc, $coverImage, $vinylImage);
    } else {
        $vm->addVinyl($title, $artist, $genre, $price, $stock, $year, $desc, $coverImage, $vinylImage);
    }
    exit;
}

/* ---------------------------------------------------------------
 *  Delete vinyl
 * ------------------------------------------------------------- */
if (isset($_POST['v_delete_id'])) {
    $vm->removeVinyl((int)$_POST['v_delete_id']);
    exit;
}
