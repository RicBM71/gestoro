<?php

namespace App\Http\Middleware;

use Closure;
use Carbon\Carbon;

class CheckPasswordExpirada
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {


        if ($request->user()->expira != 0 || is_null($request->user()->fecha_expira)){

            $f = Carbon::parse($request->user()->fecha_expira);
            $dias = $f->diffInDays(Carbon::now());

            if ($dias > ($request->user()->expira)  || is_null($request->user()->fecha_expira))
                return abort(423,'Password ha Expirado, debe reemplazarla');
        }



        return $next($request);
    }
}
