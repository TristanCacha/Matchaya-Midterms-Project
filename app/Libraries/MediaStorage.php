<?php

namespace App\Libraries;

use CodeIgniter\HTTP\Files\UploadedFile;
use RuntimeException;

class MediaStorage
{
    /** @return string Stored filename or HTTPS URL. */
    public function save(UploadedFile $file, string $folder): string
    {
        if (! in_array($folder, ['products', 'avatars'], true)) throw new RuntimeException('Invalid media folder.');
        if (! $file->isValid()) throw new RuntimeException('The image could not be uploaded.');
        $mime = (new \finfo(FILEINFO_MIME_TYPE))->file($file->getTempName());
        if (! in_array($mime, ['image/jpeg', 'image/png', 'image/webp'], true)) throw new RuntimeException('Choose a JPEG, PNG, or WebP image.');
        $dimensions = @getimagesize($file->getTempName());
        if ($dimensions === false || $dimensions[0] > 4096 || $dimensions[1] > 4096) throw new RuntimeException('The image is invalid or larger than 4096 pixels.');
        $tempDir = WRITEPATH . 'uploads' . DIRECTORY_SEPARATOR . 'processing';
        if (! is_dir($tempDir) && ! mkdir($tempDir, 0775, true) && ! is_dir($tempDir)) throw new RuntimeException('Image storage is unavailable.');
        $rawName = $file->getRandomName();
        $file->move($tempDir, $rawName);
        $rawPath = $tempDir . DIRECTORY_SEPARATOR . $rawName;
        $name = bin2hex(random_bytes(16)) . '.jpg';
        $processed = $tempDir . DIRECTORY_SEPARATOR . $name;
        try {
            $size = $folder === 'products' ? 640 : 256;
            if ($this->driver() === 'cloud' && ! extension_loaded('gd')) {
                return $this->cloudUpload($rawPath, $folder, $name, $mime, $size);
            }
            if (! extension_loaded('gd')) throw new RuntimeException('GD is required for local image processing.');
            $image = service('image', 'gd')->withFile($rawPath);
            if ($mime === 'image/jpeg' && function_exists('exif_read_data')) {
                $exif = @exif_read_data($rawPath);
                $angle = match ((int) ($exif['Orientation'] ?? 1)) { 3 => 180, 6 => 270, 8 => 90, default => 0 };
                if ($angle !== 0) $image->rotate($angle);
            }
            $image->fit($size, $size, 'center')->save($processed, 85);
            if (! is_file($processed)) throw new RuntimeException('The image could not be prepared.');
            if ($this->driver() === 'cloud') return $this->cloudUpload($processed, $folder, $name, 'image/jpeg', $size);
            $destination = WRITEPATH . 'uploads' . DIRECTORY_SEPARATOR . $folder;
            if (! is_dir($destination) && ! mkdir($destination, 0775, true) && ! is_dir($destination)) throw new RuntimeException('Image storage is unavailable.');
            if (! rename($processed, $destination . DIRECTORY_SEPARATOR . $name)) throw new RuntimeException('The image could not be saved.');
            return $name;
        } finally {
            @unlink($rawPath);
            @unlink($processed);
        }
    }

    public function delete(?string $stored, string $folder): void
    {
        if ($stored === null || $stored === '' || ! in_array($folder, ['products', 'avatars'], true)) return;
        if (preg_match('/^[a-f0-9]{32}\.jpg$/', $stored)) {
            @unlink(WRITEPATH . 'uploads' . DIRECTORY_SEPARATOR . $folder . DIRECTORY_SEPARATOR . $stored);
            return;
        }
        if ($this->driver() === 'cloud' && preg_match('~^https://res\.cloudinary\.com/[^/]+/image/upload/(?:v\d+/)?matchaya/' . $folder . '/([a-f0-9]{32})\.jpg$~', $stored, $match)) {
            $this->cloudRequest('destroy', ['public_id' => 'matchaya/' . $folder . '/' . $match[1]]);
        }
    }

    private function driver(): string
    {
        return (string) (getenv('MEDIA_DRIVER') ?: 'local');
    }

    private function cloudUpload(string $path, string $folder, string $name, string $mime, int $size): string
    {
        $publicId = 'matchaya/' . $folder . '/' . pathinfo($name, PATHINFO_FILENAME);
        $fields = ['public_id' => $publicId];
        if ($mime !== 'image/jpeg' || ! extension_loaded('gd')) {
            $fields['transformation'] = 'c_fill,g_center,w_' . $size . ',h_' . $size . ',f_jpg,q_85';
        }
        $data = $this->cloudRequest('upload', $fields, $path, $mime);
        $url = (string) ($data['secure_url'] ?? '');
        if (! preg_match('~^https://res\.cloudinary\.com/[A-Za-z0-9_-]+/image/upload/(?:v\d+/)?matchaya/' . $folder . '/[a-f0-9]{32}\.jpg$~', $url)
            || strlen($url) > 255 || (int) ($data['width'] ?? 0) !== $size
            || (int) ($data['height'] ?? 0) !== $size || ($data['format'] ?? '') !== 'jpg') {
            throw new RuntimeException('Cloud image storage returned an invalid URL.');
        }
        return $url;
    }

    /** @return array<string, mixed> */
    private function cloudRequest(string $action, array $fields, ?string $path = null, string $mime = 'image/jpeg'): array
    {
        $cloud = (string) getenv('CLOUDINARY_CLOUD_NAME');
        $key = (string) getenv('CLOUDINARY_API_KEY');
        $secret = (string) getenv('CLOUDINARY_API_SECRET');
        if (! preg_match('/^[A-Za-z0-9_-]+$/', $cloud) || $key === '' || $secret === '' || ! function_exists('curl_init')) {
            throw new RuntimeException('Cloud image storage is not configured.');
        }
        $fields['timestamp'] = time();
        ksort($fields);
        $parts = [];
        foreach ($fields as $k => $v) $parts[] = $k . '=' . $v;
        $fields['api_key'] = $key;
        $fields['signature'] = sha1(implode('&', $parts) . $secret);
        if ($path !== null) $fields['file'] = new \CURLFile($path, $mime, basename($path));
        $handle = curl_init('https://api.cloudinary.com/v1_1/' . $cloud . '/image/' . $action);
        curl_setopt_array($handle, [CURLOPT_POST => true, CURLOPT_POSTFIELDS => $fields, CURLOPT_RETURNTRANSFER => true, CURLOPT_TIMEOUT => 20]);
        $body = curl_exec($handle);
        $status = curl_getinfo($handle, CURLINFO_HTTP_CODE);
        curl_close($handle);
        $decoded = is_string($body) ? json_decode($body, true) : null;
        if ($status < 200 || $status >= 300 || ! is_array($decoded)) throw new RuntimeException('Cloud image storage failed.');
        return $decoded;
    }
}
