<?php

namespace Core\Helpers;

class ResponseHelper
{
    public static function success($data = null, int $statusCode = 200, array $headers = []): void
    {
        self::json_response([
            'success' => true,
            'data' => $data,
            'timestamp' => time()
        ], $statusCode, $headers);
    }

    public static function error($message = 'An error occurred', int $statusCode = 400, array $headers = []): void
    {
        self::json_response([
            'success' => false,
            'error' => $message,
            'timestamp' => time()
        ], $statusCode, $headers);
    }

    public static function pagination($data, $total, $perPage, $currentPage, array $headers = []): void
    {
        $totalPages = ceil($total / $perPage);

        self::json_response([
            'success' => true,
            'data' => $data,
            'pagination' => [
                'total' => (int) $total,
                'per_page' => (int) $perPage,
                'current_page' => (int) $currentPage,
                'total_pages' => (int) $totalPages,
                'has_next' => $currentPage < $totalPages,
                'has_prev' => $currentPage > 1
            ],
            'timestamp' => time()
        ], 200, $headers);
    }

    public static function redirect(string $url, int $statusCode = 302, bool $secure = true): void
    {
        if ($secure && !preg_match('#^https?://#', $url)) {
            $url = self::getBaseUrl() . ltrim($url, '/');
        }

        http_response_code($statusCode);
        header("Location: {$url}");
        exit;
    }

    public static function download(string $path, string $fileName = null, string $contentType = null): void
    {
        if (!file_exists($path)) {
            self::error('File not found', 404);
        }

        $fileName = $fileName ?: basename($path);
        $fileSize = filesize($path);
        $contentType = $contentType ?: self::getMimeType($path);

        header('Content-Description: File Transfer');
        header('Content-Type: ' . $contentType);
        header('Content-Disposition: attachment; filename="' . $fileName . '"');
        header('Content-Length: ' . $fileSize);
        header('Pragma: public');
        header('Cache-Control: must-revalidate');
        header('Expires: 0');

        readfile($path);
        exit;
    }

    public static function json($data, int $statusCode = 200, array $headers = []): void
    {
        self::json_response($data, $statusCode, $headers);
    }

    public static function noContent(): void
    {
        http_response_code(204);
        exit;
    }

    private static function json_response(array $data, int $statusCode, array $headers = []): void
    {
        http_response_code($statusCode);
        header('Content-Type: application/json');

        foreach ($headers as $key => $value) {
            header("{$key}: {$value}");
        }

        echo json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        exit;
    }

    private static function getBaseUrl(): string
    {
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
        return "{$protocol}://{$host}";
    }

    private static function getMimeType(string $path): string
    {
        $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));

        $type = [
            'pdf' => 'application/pdf',
            'txt' => 'text/plain',
            'rtf' => 'application/rtf',
            'html' => 'text/html',
            'htm' => 'text/html',
            'doc' => 'application/msword',
            'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'xls' => 'application/vnd.ms-excel',
            'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'ppt' => 'application/vnd.ms-powerpoint',
            'pptx' => 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
            'odt' => 'application/vnd.oasis.opendocument.text',
            'ods' => 'application/vnd.oasis.opendocument.spreadsheet',
            'odp' => 'application/vnd.oasis.opendocument.presentation',
            'csv' => 'text/csv',
            'md' => 'text/markdown',
            'json' => 'application/json',
            'xml' => 'application/xml',
            'yaml' => 'application/x-yaml',
            'yml' => 'application/x-yaml',
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'gif' => 'image/gif',
            'bmp' => 'image/bmp',
            'webp' => 'image/webp',
            'svg' => 'image/svg+xml',
            'tif' => 'image/tiff',
            'tiff' => 'image/tiff',
            'ico' => 'image/vnd.microsoft.icon',
            'heic' => 'image/heic',
            'mp3' => 'audio/mpeg',
            'wav' => 'audio/wav',
            'ogg' => 'audio/ogg',
            'm4a' => 'audio/mp4',
            'flac' => 'audio/flac',
            'aac' => 'audio/aac',
            'mid' => 'audio/midi',
            'midi' => 'audio/midi',
            'mp4' => 'video/mp4',
            'avi' => 'video/x-msvideo',
            'mov' => 'video/quicktime',
            'wmv' => 'video/x-ms-wmv',
            'flv' => 'video/x-flv',
            'mkv' => 'video/x-matroska',
            'webm' => 'video/webm',
            'zip' => 'application/zip',
            'rar' => 'application/x-rar-compressed',
            '7z' => 'application/x-7z-compressed',
            'tar' => 'application/x-tar',
            'gz' => 'application/gzip',
            'bz2' => 'application/x-bzip2',
            'php' => 'application/x-httpd-php',
            'js' => 'application/javascript',
            'ts' => 'application/typescript',
            'css' => 'text/css',
            'java' => 'text/x-java-source',
            'py' => 'text/x-python',
            'cpp' => 'text/x-c',
            'c' => 'text/x-c',
            'h' => 'text/x-c',
            'ttf' => 'font/ttf',
            'otf' => 'font/otf',
            'woff' => 'font/woff',
            'woff2' => 'font/woff2',
            'ics' => 'text/calendar',
            'vcard' => 'text/vcard',
            'bin' => 'application/octet-stream',
            'exe' => 'application/vnd.microsoft.portable-executable',
            'apk' => 'application/vnd.android.package-archive',
            'iso' => 'application/x-iso9660-image',
        ];

        return $type[$extension] ?? 'application/octet-stream';
    }
}