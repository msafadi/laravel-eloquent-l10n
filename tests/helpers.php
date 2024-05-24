<?php

use Safadi\Tests\TestApplication;

if (!function_exists('config_path')) {
    function config_path($path = '') {
        return $path;
    }
}

if (!function_exists('config')) {
    function config($key, $default = null) {
        [, $name] = explode('.', $key);
        return TestApplication::getInstance()->make('config')->get($name, $default);
    }
}

if (!function_exists('app_path')) {
    function app_path($path) {
        return __DIR__ . '/' . trim($path, '/\\');
    }
}
