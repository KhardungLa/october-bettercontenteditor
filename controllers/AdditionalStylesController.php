<?php namespace DasRoteQuadrat\BetterContentEditor\Controllers;

use Response;
use Illuminate\Routing\Controller;
use DasRoteQuadrat\BetterContentEditor\Models\Settings;

class AdditionalStylesController extends Controller
{
    public function render()
    {
        return Response::make(Settings::renderCss(), 200)
            ->header('Content-Type', 'text/css')
            ->header('Cache-Control', 'public, max-age=86400');
    }
}
