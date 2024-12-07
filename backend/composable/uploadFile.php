<?php

function uploadFileData($file, $file_name) {
    // Define the upload directory
    $uploadDir = '../uploads/';
    
    // Create the directory if it doesn't exist
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    $fileTmpPath = $file['tmp_name'];
    $fileExtension = pathinfo($file_name, PATHINFO_EXTENSION);

    // Allowed file types
    $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];

    // Check if the file extension is allowed
    if (!in_array(strtolower($fileExtension), $allowedExtensions)) {
        return ['status' => 400, 'message' => 'Invalid file type. Only JPG, PNG, and GIF are allowed.'];
    }

    // Check if the file size is within the limit (2MB)
    $fileSize = $file['size'];
    if ($fileSize > 2 * 1024 * 1024) {
        return ['status' => 400, 'message' => 'File size exceeds the 2MB limit.'];
    }

    // Generate a new unique name for the file
    $newFileName = uniqid('product_', true) . '.' . $fileExtension;
    $destPath = $uploadDir . $newFileName;

    // Move the file to the destination folder
    if (move_uploaded_file($fileTmpPath, $destPath)) {
        // Construct the file URL correctly (web-accessible)
        $fileUrl = 'https://' . $_SERVER['HTTP_HOST'] . '/uploads/' . $newFileName;

        // Save this URL to the database (only HTTP(S) URL is needed)
        return ['status' => 200, 'file_url' => $fileUrl];
    } else {
        return ['status' => 500, 'message' => 'Failed to upload file.'];
    }
}
?>
