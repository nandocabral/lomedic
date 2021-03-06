<?php

namespace App\Http\Middleware;

use Session;
use Closure;
use Illuminate\Support\Facades\Auth;

use App\Http\Models\Administracion\Empresas;
use App\Http\Models\Administracion\Usuarios;


class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        # Si existe session, redirige a empresa default
        if (Auth::guard($guard)->check()) {
            $empresa = Empresas::findOrFail(Auth::user()->fk_id_empresa);
            return redirect("/$empresa->conexion");
        }
        return $next($request);
    }
}
