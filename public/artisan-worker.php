<?php

// schedule-worker.php

require __DIR__ . '/../vendor/autoload.php';

use Illuminate\Support\Facades\Artisan;
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
    // 模拟cli环境
    if (empty($_SERVER['PHP_SELF'])) {
        $_SERVER['PHP_SELF'] = 'artisan';
        $_SERVER['SCRIPT_NAME'] = 'artisan';
        $_SERVER['SCRIPT_FILENAME'] = 'artisan';
        $_SERVER['argv'] = ['artisan'];
        $_SERVER['argc'] = 1;
    }

    $command = $request['command'] ?? '';
    $result = ['output' => ''];
    if (!empty($command)) {
        try {
            Artisan::call($command);
            $result['output'] = Artisan::output();
        } catch (Throwable $e) {
            $result['output'] = "error: " . $e->getMessage() . "\n" . $e->getTraceAsString();
        }
    } else {
        $result['output'] = "error: No command provided";
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
