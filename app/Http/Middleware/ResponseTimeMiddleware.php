<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class ResponseTimeMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $request->start_time = microtime(true);
        
        return $next($request);
    }

    /**
     * リクエスト処理後の終了処理
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Symfony\Component\HttpFoundation\Response  $response
     * @return void
     */
    public function terminate(Request $request, Response $response)
    {
        $duration = (microtime(true) - $request->start_time) * 1000;
        
        // ルートの基本パスを取得（動的パラメータを除く）
        $route = $request->route();
        $routeName = $route ? ($route->getName() ?? $route->uri()) : $request->path();
        
        // APIバージョンやバリエーションを除いた基本パスを抽出するロジックを追加できます
        // 例: /api/v1/users/123 -> /api/users
        
        // SLI/SLO計測に最適化されたログフォーマット
        Log::channel('stderr')->info('api_response_time', [
            'type' => 'http_request',
            'method' => $request->method(),
            'path' => $routeName,
            'status_code' => $response->getStatusCode(),
            'duration_ms' => round($duration, 2),
            'success' => $response->isSuccessful(),  // 200番台のレスポンスかどうか
            'error' => !$response->isSuccessful(),   // エラーレスポンスかどうか
            'service' => 'laravel-app',              // サービス名（複数サービスがある場合に識別用）
            'environment' => env('APP_ENV', 'production')
        ]);
    }
}