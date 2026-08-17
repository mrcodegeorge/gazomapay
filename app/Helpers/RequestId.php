<?php

class RequestId {
    private static ?string $currentId = null;

    public static function get(): string {
        if (self::$currentId === null) {
            self::$currentId = 'req_' . bin2hex(random_bytes(10));
        }
        return self::$currentId;
    }
}
