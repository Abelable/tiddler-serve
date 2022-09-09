<?php

namespace App\Http\Middleware;

use App\Exceptions\BusinessException;
use App\Utils\CodeResponse;
use Illuminate\Auth\Middleware\Authenticate as Middleware;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param \Illuminate\Http\Request $request
     * @return string|null
     */
    protected function redirectTo($request)
    {
        if (!$request->expectsJson()) {
            if ($request->header('Authorization')) {
                throw new BusinessException(CodeResponse::FORBIDDEN, 'token已过期，请尝试刷新token');
            }
            throw new BusinessException(CodeResponse::UNAUTHORIZED, '未携带token访问');
        }
    }

    protected function authenticate($request, array $guards)
    {
        return parent::authenticate($request, $guards);
    }
}
