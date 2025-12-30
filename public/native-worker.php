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

    $result = ['data' => null, 'error' => null];

    if (empty($class) || empty($method)) {
        $result['error'] = 'Missing class or method parameter';
        return $result;
    }

    try {
        $result['data'] = app($class)->$method(...$params);
    } catch (Throwable $e) {
        $result['error'] = $e->getMessage();
        app()->make(\Psr\Log\LoggerInterface::class)->error($e->getMessage(), ['exception' => $e]);
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
