<?php

namespace CH\CHCore\Traits;

trait Cache
{
    protected static array $cache = [];

    protected static function getFromCache(
        string $namespace,
        string|int $key,
        callable $callback
    ): mixed {
        if (!isset(self::$cache[$namespace][$key])) {
            self::$cache[$namespace][$key] = $callback();
        }

        return self::$cache[$namespace][$key];
    }
}
