<?php

use CodeIgniter\Boot;
use Config\Paths;

// CodeIgniter's public directory remains the web root; this function is the
// front controller for dynamic routes on Vercel.
define('FCPATH', dirname(__DIR__) . '/public' . DIRECTORY_SEPARATOR);
chdir(FCPATH);

require dirname(__DIR__) . '/app/Config/Paths.php';
$paths = new Paths();

if (getenv('VERCEL') === '1') {
    $paths->writableDirectory = '/tmp/matchaya-writable';
    foreach (['cache', 'logs', 'session', 'uploads', 'debugbar'] as $directory) {
        $path = $paths->writableDirectory . DIRECTORY_SEPARATOR . $directory;
        if (! is_dir($path)) {
            mkdir($path, 0775, true);
        }
    }

    date_default_timezone_set('Asia/Manila');
}

require $paths->systemDirectory . '/Boot.php';

exit(Boot::bootWeb($paths));
