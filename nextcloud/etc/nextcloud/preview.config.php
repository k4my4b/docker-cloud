<?php
// Base preview settings
$CONFIG = [
    'enable_previews' => true,
    'preview_format' => 'webp',
    'cropimagepreviews' => false,
    'preview_max_x' => 2048,
    'preview_max_y' => 2048,
    'preview_max_filesize_image' => 50,
    'preview_max_memory' => 256,
    'enabledPreviewProviders' => [
        'OC\Preview\BMP',
        'OC\Preview\GIF',
        'OC\Preview\JPEG',
        'OC\Preview\Krita',
        'OC\Preview\MarkDown',
        'OC\Preview\MP3',
        'OC\Preview\OpenDocument',
        'OC\Preview\PNG',
        'OC\Preview\TXT',
        'OC\Preview\XBitmap',
    ],
];

// Set preview concurrency based on the number of available CPU cores
$cpuCores = intval(shell_exec('nproc') ?? 0);
$CONFIG['preview_concurrency_all'] = $cpuCores > 0 ? $cpuCores * 2 : 8;
$CONFIG['preview_concurrency_new'] = $cpuCores > 0 ? $cpuCores : 4;

// Set LibreOffice path if available
$libreofficePath = trim(shell_exec('which libreoffice') ?? '');
if (preg_match('/^\/[a-zA-Z0-9_\-\/\.]+$/', $libreofficePath)) {
    $CONFIG['preview_libreoffice_path'] = $libreofficePath;
}

// Set ffmpeg path if available and add movie preview provider
$ffmpegPath = trim(shell_exec('which ffmpeg') ?? '');
if (preg_match('/^\/[a-zA-Z0-9_\-\/\.]+$/', $ffmpegPath)) {
    $CONFIG['preview_ffmpeg_path'] = $ffmpegPath;
    $CONFIG['enabledPreviewProviders'][] = 'OC\Preview\Movie';
}

// Add Imaginary preview if IMAGINARY_HOST and IMAGINARY_PORT environment variables are set
$imaginaryHost = getenv('IMAGINARY_HOST');
$imaginaryPort = getenv('IMAGINARY_PORT');
if (!empty($imaginaryHost) && !empty($imaginaryPort)) {
    $CONFIG['preview_imaginary_url'] = sprintf('http://%s:%s', $imaginaryHost, $imaginaryPort);
    $CONFIG['enabledPreviewProviders'][] = 'OC\Preview\Imaginary';
    $CONFIG['enabledPreviewProviders'][] = 'OC\Preview\ImaginaryPDF';

    // Add Imaginary secret key if available
    $imaginarySecret = getenv('IMAGINARY_SECRET');
    if (!empty($imaginarySecret)) {
        $CONFIG['preview_imaginary_key'] = $imaginarySecret;
    }
}
