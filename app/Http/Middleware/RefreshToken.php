<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Auth\AuthenticationException;
use Symfony\Component\HttpFoundation\Response;
use Tymon\JWTAuth\Http\Middleware\BaseMiddleware;
use Illuminate\Http\Exceptions\HttpResponseException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
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

            if($user = JWTAuth::parseToken()->authenticate()){
                return $next($request);
            }

            throw new AuthenticationException('Unauthorized', []);

        } catch (TokenExpiredException $e) {
            throw new HttpResponseException(response()->json(['message' => 'token expired'], 401));
        } catch (\Exception $e) {
            throw new AuthenticationException('Unauthorized', []);
        }
    }
}