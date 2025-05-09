<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Http\Middleware\BaseMiddleware;

class RefreshToken extends BaseMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        try {
            $this->checkForToken($request);
            if ($request->user = JWTAuth::parseToken()->authenticate()) {
                return $next($request);
            }
            throw new AuthenticationException('Unathorized', []);

        } catch (TokenExpiredException $e) {
            throw new HttpResponseException(response()->json([
                "message" => 'token expired'
            ],401));
        } catch (\Exception $e) {
            throw new AuthenticationException('Unathorized', []);
        }
    }
}
