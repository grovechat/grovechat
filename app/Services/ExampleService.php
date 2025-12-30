<?php

namespace App\Services;

class ExampleService
{
    /**
     * 示例方法：返回问候语
     *
     * @param string $name
     * @return array
     */
    public function hello(string $name = 'World'): array
    {
        return [
            'message' => "Hello, {$name}!",
            'timestamp' => now()->toDateTimeString(),
        ];
    }

    /**
     * 示例方法：执行计算
     *
     * @param int $a
     * @param int $b
     * @return array
     */
    public function calculate(int $a, int $b): array
    {
        return [
            'sum' => $a + $b,
            'difference' => $a - $b,
            'product' => $a * $b,
            'quotient' => $b !== 0 ? $a / $b : null,
        ];
    }

    /**
     * 示例方法：获取系统信息
     *
     * @return array
     */
    public function getSystemInfo(): array
    {
        return [
            'php_version' => PHP_VERSION,
            'laravel_version' => app()->version(),
            'environment' => app()->environment(),
            'timezone' => config('app.timezone'),
        ];
    }
}
