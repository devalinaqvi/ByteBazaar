<?php

namespace App\Services;

class FileUploadService
{
    private string $uploadDir;

    public function __construct()
    {
        // Use realpath to get absolute path from project root
        $projectRoot = realpath(__DIR__ . '/../../');
        $this->uploadDir = $projectRoot . '/public/uploads/products/';

        // Create directory if it doesn't exist
        if (!is_dir($this->uploadDir)) {
            if (!mkdir($this->uploadDir, 0755, true)) {
                throw new \Exception("Failed to create upload directory: " . $this->uploadDir);
            }
        }

        // Check if directory is writable
        if (!is_writable($this->uploadDir)) {
            throw new \Exception("Upload directory is not writable: " . $this->uploadDir);
        }
    }

    public function uploadImage(array $file): string
    {
        // Check if file array is empty
        if (empty($file) || !isset($file['tmp_name']) || !isset($file['error'])) {
            throw new \Exception("No file provided");
        }

        // Check for upload errors
        if ($file['error'] !== UPLOAD_ERR_OK) {
            $errors = [
                UPLOAD_ERR_INI_SIZE => 'File exceeds upload_max_filesize',
                UPLOAD_ERR_FORM_SIZE => 'File exceeds MAX_FILE_SIZE',
                UPLOAD_ERR_PARTIAL => 'File was only partially uploaded',
                UPLOAD_ERR_NO_FILE => 'No file was uploaded',
                UPLOAD_ERR_NO_TMP_DIR => 'Missing temporary directory',
                UPLOAD_ERR_CANT_WRITE => 'Failed to write file',
                UPLOAD_ERR_EXTENSION => 'Extension not allowed'
            ];
            throw new \Exception($errors[$file['error']] ?? 'Unknown upload error');
        }

        $allowed = ['image/jpeg', 'image/png', 'image/webp'];

        if (!in_array($file['type'], $allowed)) {
            throw new \Exception("Invalid file type. Allowed: JPEG, PNG, WebP");
        }

        if ($file['size'] > 2 * 1024 * 1024) { // 2MB limit
            throw new \Exception("File too large. Max 2MB allowed");
        }

        // Validate temp file exists and is uploaded
        if (!is_uploaded_file($file['tmp_name'])) {
            throw new \Exception("Invalid uploaded file");
        }

        $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = uniqid('prod_', true) . '.' . $ext;
        $dest = $this->uploadDir . $filename;

        // Move uploaded file
        if (!move_uploaded_file($file['tmp_name'], $dest)) {
            throw new \Exception("Failed to move uploaded file");
        }

        // Set proper permissions on uploaded file
        chmod($dest, 0644);

        return "/uploads/products/" . $filename;
    }
}