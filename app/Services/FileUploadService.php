<?php
namespace App\Services;
use App\Core\HttpException;
class FileUploadService
{
    public function optionalImage(?array $file): ?string
    {
        if (
            !$file ||
            ($file["error"] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE
        ) {
            return null;
        }
        return $this->uploadImage($file);
    }
    public function uploadImage(array $file): string
    {
        if (
            ($file["error"] ?? -1) !== UPLOAD_ERR_OK ||
            !is_string($file["tmp_name"] ?? null) ||
            !is_uploaded_file($file["tmp_name"])
        ) {
            throw new HttpException(
                422,
                "The image could not be uploaded. Please try again.",
            );
        }
        $tmp = $file["tmp_name"];
        if (filesize($tmp) > 2 * 1024 * 1024) {
            throw new HttpException(422, "Images must be 2 MB or smaller.");
        }
        $ext = self::imageExtension($tmp);
        $dir = dirname(__DIR__, 2) . "/public/uploads/products";
        if (!is_dir($dir) && !mkdir($dir, 0755, true) && !is_dir($dir)) {
            throw new \RuntimeException("Unable to create upload directory");
        }
        $filename = bin2hex(random_bytes(24)) . "." . $ext;
        if (!move_uploaded_file($tmp, $dir . "/" . $filename)) {
            throw new \RuntimeException("Unable to store uploaded image");
        }
        return "/uploads/products/" . $filename;
    }
    public static function imageExtension(string $path): string
    {
        $mime = (new \finfo(FILEINFO_MIME_TYPE))->file($path);
        $info = @getimagesize($path);
        $types = [
            "image/jpeg" => "jpg",
            "image/png" => "png",
            "image/webp" => "webp",
        ];
        if (
            !isset($types[$mime]) ||
            !$info ||
            $info["mime"] !== $mime ||
            $info[0] * $info[1] > 24000000
        ) {
            throw new HttpException(
                422,
                "Choose a valid JPEG, PNG, or WebP image (up to 24 megapixels).",
            );
        }
        return $types[$mime];
    }
    public function deleteImage(?string $path): void
    {
        if (
            $path &&
            preg_match(
                '#^/uploads/products/[a-f0-9]{48}\.(jpg|png|webp)$#',
                $path,
            )
        ) {
            $file = dirname(__DIR__, 2) . "/public" . $path;
            if (is_file($file)) {
                unlink($file);
            }
        }
    }
}
