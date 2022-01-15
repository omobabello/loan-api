<?php

namespace App\Http\Middleware;

use App\Traits\ApiResponse;
use Closure;
use Illuminate\Cache\RateLimiter;
use Symfony\Component\HttpFoundation\Response;

class ThrottleRequests
{
    use ApiResponse;

    protected $limiter;
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */

    public function __construct(RateLimiter $limiter)
    {
        $this->limiter = $limiter;
    }

    public function handle($request, Closure $next, $maxAttempts = 60, $decayMinutes = 1)
    {
        $key = $this->resolveRequestSignature($request);

        if ($this->limiter->tooManyAttempts($key, $maxAttempts, $decayMinutes)) {
            $response = new Response('Too many requests', Response::HTTP_TOO_MANY_REQUESTS);
            return $this->addHeaders(
                $this->error(Response::HTTP_TOO_MANY_REQUESTS, "Too many requests"),
                $maxAttempts,
                $this->limiter->retriesLeft($key, $maxAttempts),
                $this->limiter->availableIn($key)
            );
        }

        $this->limiter->hit($key, $decayMinutes * 60);

        $response = $next($request);

        return $this->addHeaders($response,$maxAttempts,$this->limiter->retriesLeft($key, $maxAttempts));
    }

    protected function addHeaders(Response $response, $maxAttempts, $remainingAttempts, $retryAfter = null)
    {
        $headers = [
            'X-RateLimit-Limit'     => $maxAttempts,
            'X-RateLimit-Remaining' => $remainingAttempts,
        ];

        if (!is_null($retryAfter)) {
            $headers['Retry-After'] = $retryAfter;
        }

        $response->headers->add($headers);

        return $response;
    }

    protected function resolveRequestSignature($request)
    {
        return sha1("{$request->method()}-{$request->getHost()}-{$request->path()}-{$request->ip()}");
    }
}
