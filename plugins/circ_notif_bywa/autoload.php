<?php
/**
 * PSR-4 style autoloader untuk namespace Cncw di dalam plugin.
 */

spl_autoload_register(static function (string $class): void {
    $prefix = 'Cncw\\';
    if (!str_starts_with($class, $prefix)) {
        return;
    }

    $relative = str_replace('\\', '/', substr($class, strlen($prefix)));
    $file = __DIR__ . '/src/Cncw/' . $relative . '.php';
    if (is_readable($file)) {
        require_once $file;
    }
});
