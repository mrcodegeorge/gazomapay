<?php

require_once __DIR__ . '/RequestId.php';

class ApiResponse {

    public static function success(mixed $data = [], string $message = 'Success', int $statusCode = 200): void {
        http_response_code($statusCode);
        header('Content-Type: application/json; charset=utf-8');
        header('X-Request-Id: ' . RequestId::get());

        echo json_encode([
            'success' => true,
            'message' => $message,
            'data' => $data,
            'request_id' => RequestId::get()
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        exit;
    }

    public static function error(string $code, string $message, string $type = 'api_error', int $statusCode = 400, array $extra = []): void {
        http_response_code($statusCode);
        header('Content-Type: application/json; charset=utf-8');
        header('X-Request-Id: ' . RequestId::get());

        $payload = array_merge([
            'success' => false,
            'error' => [
                'code' => $code,
                'message' => $message,
                'type' => $type
            ],
            'request_id' => RequestId::get()
        ], $extra);

        echo json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        exit;
    }
}
