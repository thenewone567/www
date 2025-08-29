<?php
/**
 * User Profile Picture Upload Handler
 * Handles user profile picture uploads and management
 */

class UserProfileHandler
{
    private $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    private $maxFileSize = 5 * 1024 * 1024; // 5MB
    private $uploadDir;
    private $publicPath;

    public function __construct()
    {
        $this->uploadDir = APPROOT . DS . 'storage' . DS . 'uploads' . DS . 'users' . DS;
        $this->publicPath = 'storage/uploads/users/';

        // Ensure upload directory exists
        if (!is_dir($this->uploadDir)) {
            mkdir($this->uploadDir, 0755, true);
        }
    }

    /**
     * Upload user profile picture
     * @param array $file $_FILES array for the uploaded file
     * @param int $userId User ID for filename
     * @return array Result array with success status and message/filename
     */
    public function uploadProfilePicture($file, $userId)
    {
        try {
            // Validate file upload
            $validation = $this->validateUpload($file);
            if (!$validation['success']) {
                return $validation;
            }

            // Generate filename
            $fileExtension = pathinfo($file['name'], PATHINFO_EXTENSION);
            $fileName = 'user_' . $userId . '_' . time() . '.' . strtolower($fileExtension);
            $targetPath = $this->uploadDir . $fileName;

            // Remove old profile picture if exists
            $this->removeOldProfilePicture($userId);

            // Move uploaded file
            if (move_uploaded_file($file['tmp_name'], $targetPath)) {
                // Resize image for optimization
                $this->resizeImage($targetPath, 300, 300);

                return [
                    'success'  => true,
                    'filename' => $fileName,
                    'path'     => $this->publicPath . $fileName,
                    'message'  => 'Profile picture uploaded successfully'
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Failed to move uploaded file'
                ];
            }
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Upload error: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Validate uploaded file
     * @param array $file $_FILES array
     * @return array Validation result
     */
    private function validateUpload($file)
    {
        if ($file['error'] !== UPLOAD_ERR_OK) {
            return [
                'success' => false,
                'message' => 'File upload error: ' . $this->getUploadErrorMessage($file['error'])
            ];
        }

        if ($file['size'] > $this->maxFileSize) {
            return [
                'success' => false,
                'message' => 'File size exceeds 5MB limit'
            ];
        }

        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);

        if (!in_array($mimeType, $this->allowedTypes)) {
            return [
                'success' => false,
                'message' => 'Invalid file type. Only JPEG, PNG, GIF, and WebP are allowed'
            ];
        }

        return ['success' => true];
    }

    /**
     * Resize image to specified dimensions
     * @param string $imagePath Path to image file
     * @param int $width Target width
     * @param int $height Target height
     */
    private function resizeImage($imagePath, $width, $height)
    {
        $imageInfo = getimagesize($imagePath);
        if (!$imageInfo)
            return;

        $originalWidth = $imageInfo[0];
        $originalHeight = $imageInfo[1];
        $imageType = $imageInfo[2];

        // Skip if image is already smaller
        if ($originalWidth <= $width && $originalHeight <= $height) {
            return;
        }

        // Calculate new dimensions maintaining aspect ratio
        $ratio = min($width / $originalWidth, $height / $originalHeight);
        $newWidth = round($originalWidth * $ratio);
        $newHeight = round($originalHeight * $ratio);

        // Create image resource based on type
        switch ($imageType) {
            case IMAGETYPE_JPEG:
                $source = imagecreatefromjpeg($imagePath);
                break;
            case IMAGETYPE_PNG:
                $source = imagecreatefrompng($imagePath);
                break;
            case IMAGETYPE_GIF:
                $source = imagecreatefromgif($imagePath);
                break;
            case IMAGETYPE_WEBP:
                $source = imagecreatefromwebp($imagePath);
                break;
            default:
                return;
        }

        // Create new image
        $destination = imagecreatetruecolor($newWidth, $newHeight);

        // Preserve transparency for PNG and GIF
        if ($imageType == IMAGETYPE_PNG || $imageType == IMAGETYPE_GIF) {
            imagealphablending($destination, false);
            imagesavealpha($destination, true);
            $transparent = imagecolorallocatealpha($destination, 255, 255, 255, 127);
            imagefilledrectangle($destination, 0, 0, $newWidth, $newHeight, $transparent);
        }

        // Resize image
        imagecopyresampled($destination, $source, 0, 0, 0, 0, $newWidth, $newHeight, $originalWidth, $originalHeight);

        // Save resized image
        switch ($imageType) {
            case IMAGETYPE_JPEG:
                imagejpeg($destination, $imagePath, 85);
                break;
            case IMAGETYPE_PNG:
                imagepng($destination, $imagePath, 6);
                break;
            case IMAGETYPE_GIF:
                imagegif($destination, $imagePath);
                break;
            case IMAGETYPE_WEBP:
                imagewebp($destination, $imagePath, 85);
                break;
        }

        // Clean up memory
        imagedestroy($source);
        imagedestroy($destination);
    }

    /**
     * Remove old profile picture for user
     * @param int $userId User ID
     */
    private function removeOldProfilePicture($userId)
    {
        $pattern = $this->uploadDir . 'user_' . $userId . '_*';
        $oldFiles = glob($pattern);

        foreach ($oldFiles as $file) {
            if (is_file($file)) {
                unlink($file);
            }
        }
    }

    /**
     * Delete profile picture
     * @param string $filename Profile picture filename
     * @return bool Success status
     */
    public function deleteProfilePicture($filename)
    {
        if (empty($filename))
            return true;

        $filePath = $this->uploadDir . $filename;
        if (file_exists($filePath)) {
            return unlink($filePath);
        }
        return true;
    }

    /**
     * Get profile picture URL
     * @param string $filename Profile picture filename
     * @return string URL to profile picture or default avatar
     */
    public function getProfilePictureUrl($filename)
    {
        if (empty($filename)) {
            return URLROOT . '/storage/uploads/users/avatar.png';
        }

        $filePath = $this->uploadDir . $filename;
        if (file_exists($filePath)) {
            return URLROOT . '/' . $this->publicPath . $filename;
        }

        return URLROOT . '/storage/uploads/users/avatar.png';
    }

    /**
     * Get upload error message
     * @param int $error Upload error code
     * @return string Error message
     */
    private function getUploadErrorMessage($error)
    {
        switch ($error) {
            case UPLOAD_ERR_INI_SIZE:
                return 'File exceeds server upload limit';
            case UPLOAD_ERR_FORM_SIZE:
                return 'File exceeds form upload limit';
            case UPLOAD_ERR_PARTIAL:
                return 'File upload was incomplete';
            case UPLOAD_ERR_NO_FILE:
                return 'No file was uploaded';
            case UPLOAD_ERR_NO_TMP_DIR:
                return 'Temporary directory missing';
            case UPLOAD_ERR_CANT_WRITE:
                return 'Failed to write file to disk';
            case UPLOAD_ERR_EXTENSION:
                return 'Upload stopped by extension';
            default:
                return 'Unknown upload error';
        }
    }
}
?>