<?php

// native-worker.php

require __DIR__ . '/../vendor/autoload.php';

use Illuminate\Contracts\Console\Kernel;

if (!defined('STDIN')) {
    define('STDIN', fopen('php://stdin', 'r'));
}
if (!defined('STDOUT')) {
    define('STDOUT', fopen('php://stdout', 'w'));
}
if (!defined('STDERR')) {
    define('STDERR', fopen('php://stderr', 'w'));
}

$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Kernel::class);
$kernel->bootstrap();

$handler = static function (array $request): array  {
    $class = $request['class'] ?? '';
    $method = $request['method'] ?? '';
    $params = $request['params'] ?? [];

    $result = ['success' => false, 'data' => null, 'error' => null];

    if (empty($class) || empty($method)) {
        $result['error'] = 'Missing class or method parameter';
        return $result;
    }

    try {
        // 检查类是否存在
        if (!class_exists($class)) {
            $result['error'] = "Class not found: {$class}";
            return $result;
        }

        // 实例化类（通过 Laravel 容器）
        $instance = app($class);

        // 检查方法是否存在
        if (!method_exists($instance, $method)) {
            $result['error'] = "Method not found: {$class}::{$method}";
            return $result;
        }

        // 调用方法
        $data = call_user_func_array([$instance, $method], $params);

        $result['success'] = true;
        $result['data'] = $data;
    } catch (Throwable $e) {
        $result['error'] = $e->getMessage();
        $result['trace'] = $e->getTraceAsString();
    }

    return $result;
};

$maxRequests = (int)($_SERVER['MAX_REQUESTS'] ?? 0);
for ($nbRequests = 0; !$maxRequests || $nbRequests < $maxRequests; ++$nbRequests) {
    $keepRunning = \frankenphp_handle_request($handler);
    gc_collect_cycles();
    if (!$keepRunning) {
        break;
    }
}
