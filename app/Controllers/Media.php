<?php

namespace App\Controllers;

use CodeIgniter\HTTP\ResponseInterface;

class Media extends BaseController
{
    public function show(string $folder, string $name): ResponseInterface
    {
        if (! in_array($folder, ['products', 'avatars'], true) || ! preg_match('/^[a-f0-9]{32}\.jpg$/', $name)) {
            return $this->response->setStatusCode(404);
        }
        $path = WRITEPATH . 'uploads' . DIRECTORY_SEPARATOR . $folder . DIRECTORY_SEPARATOR . $name;
        if (! is_file($path)) return $this->response->setStatusCode(404);
        return $this->response->setHeader('Content-Type', 'image/jpeg')
            ->setHeader('Cache-Control', 'public, max-age=31536000, immutable')
            ->setBody((string) file_get_contents($path));
    }
}
