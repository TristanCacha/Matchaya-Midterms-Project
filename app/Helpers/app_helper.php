<?php

if (! function_exists('initials')) {
    function initials(string $name): string
    {
        $parts = preg_split('/\s+/', trim($name), -1, PREG_SPLIT_NO_EMPTY) ?: [];
        $letters = '';
        foreach (array_slice($parts, 0, 2) as $part) $letters .= mb_strtoupper(mb_substr($part, 0, 1));
        return $letters !== '' ? $letters : 'M';
    }
}

if (! function_exists('peso')) {
    function peso(int|float|string $amount): string
    {
        return '₱' . number_format((float) $amount, 2, '.', ',');
    }
}

if (! function_exists('media_url')) {
    function media_url(?string $name, string $folder): string
    {
        if ($name === null || $name === '') return base_url('assets/img/placeholder.svg');
        if (preg_match('~^demo/products/[a-z0-9-]+\.webp$~', $name)) return base_url('assets/' . $name);
        if (preg_match('/^sample-(straight|milk-drinks|hojicha|seasonal|food|retail)\.svg$/', $name)) return base_url('assets/img/' . $name);
        if (preg_match('~^https://res\.cloudinary\.com/[A-Za-z0-9_-]+/image/upload/~', $name)) return $name;
        return site_url('media/' . $folder . '/' . rawurlencode($name));
    }
}

if (! function_exists('media_srcset')) {
    function media_srcset(?string $name): string
    {
        if ($name === null || ! preg_match('~^demo/products/([a-z0-9-]+)\.webp$~', $name, $match)) return '';
        return base_url('assets/' . $name) . ' 640w, ' . base_url('assets/demo/products/' . $match[1] . '@2x.webp') . ' 1280w';
    }
}

if (! function_exists('media_alt')) {
    function media_alt(?string $name, string $fallback): string
    {
        static $alts = null;
        if ($alts === null) {
            $alts = [];
            $path = ROOTPATH . 'database/seeds/image-manifest.json';
            if (is_file($path)) {
                foreach (json_decode((string) file_get_contents($path), true) ?: [] as $entry) {
                    $alts['demo/products/' . $entry['slug'] . '.webp'] = $entry['alt'];
                }
            }
        }
        return $alts[$name ?? ''] ?? $fallback;
    }
}
