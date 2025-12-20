<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

class HandleLocale
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $locale = $request->cookie('locale') ?? 'zh-CN';

        // Laravel 使用下划线格式的 locale (zh_CN)
        $laravelLocale = str_replace('-', '_', $locale);

        // 设置应用的语言环境
        App::setLocale($laravelLocale);

        return $next($request);
    }
}