<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class RequestResponseLogger
{
    public function handle(Request $request, Closure $next)
    {
        //таймер
        $startTime = microtime(true);

        $method = $request->method();
        $url = $request->fullUrl();
        $headers = $request->headers->all();
        $body = $request->getContent();
        // маскирую чувствительные данные
        $maskedBody = $this->maskSensitiveData($body, $request->all());

        // Логирую входящий запрос
        Log::channel('daily')->info('Incoming Request', [
            'method' => $method,
            'url' => $url,
            'headers' => $headers,
            'body' => $maskedBody,
        ]);

        $response = $next($request);
        $endTime = microtime(true);
        $duration = $endTime - $startTime;


        $status = $response->status();
        $responseContent = $response->getContent();
        $size = strlen($responseContent);

        Log::channel('daily')->info('Outgoing Response', [
            'status' => $status,
            'response_size' => $size,
            'duration_ms' => round($duration * 1000, 2),
        ]);

        return $response;
    }

    protected function maskSensitiveData(string $body, array $allInput): false|string
    {
        $sensitiveKeys = ['password', 'password_confirmation', 'token', 'api_key', 'secret'];
        $dataArray = json_decode($body, true);

        if (json_last_error() === JSON_ERROR_NONE) {
            foreach ($sensitiveKeys as $key) {
                if (isset($dataArray[$key])) {
                    $dataArray[$key] = '***';
                }
            }
            return json_encode($dataArray);
        }
        return $body;
    }
}
