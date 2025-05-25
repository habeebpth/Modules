<?php
namespace Modules\Accounting\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckAccountingModule
{
    public function handle(Request $request, Closure $next)
    {
        if (!in_array('accounting', user()->modules ?? [])) {
            abort(403, 'Access denied to accounting module');
        }

        return $next($request);
    }
}
