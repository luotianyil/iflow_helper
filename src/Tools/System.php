<?php

namespace iflow\Helper\Tools;

class System {

    public static function extensionLoaded(array $extensions): array {
        $not_ext = [];
        foreach ($extensions as $ext) {
            if (!extension_loaded($ext)) $not_ext[] = $ext;
        }
        return $not_ext;
    }

    public static function isCli(string $OTHER_API = 'micro_sapi'): bool {
        return php_sapi_name() === 'cli' || php_sapi_name() === 'phpdbg' || php_sapi_name() === $OTHER_API;
    }
}