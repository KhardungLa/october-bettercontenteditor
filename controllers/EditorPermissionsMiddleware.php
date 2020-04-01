<?php namespace DasRoteQuadrat\BetterContentEditor\Controllers;

use Closure;
use Illuminate\Support\Facades\Log;
use Backend\Facades\BackendAuth;

class EditorPermissionsMiddleware
{
    public function handle($request, Closure $next)
    {
        $backendUser = BackendAuth::getUser();
        if ($backendUser && $backendUser->hasAccess('dasrotequadrat.bettercontenteditor.editor')) {
            return $next($request);
        }

        return abort(404);
    }
}
