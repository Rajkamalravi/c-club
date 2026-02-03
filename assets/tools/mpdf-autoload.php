<?php
spl_autoload_register(function ($class) {
    $prefixes = [
        'Mpdf\\' => __DIR__ . '/mpdf/src/',
        'setasign\\Fpdi\\' => __DIR__ . '/fpdi/src/',
        'Psr\\Log\\' => __DIR__ . '/psr/log/',
        'Psr\\Http\\Message\\' => __DIR__ . '/psr/http-message/',
    ];

    foreach ($prefixes as $prefix => $base_dir) {
        $len = strlen($prefix);
        if (strncmp($prefix, $class, $len) !== 0) {
            continue;
        }

        $relative_class = substr($class, $len);
        $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';

        if (file_exists($file)) {
            require $file;
            return;
        }
    }
});
?>
