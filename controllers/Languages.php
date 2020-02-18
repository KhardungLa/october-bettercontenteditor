<?php namespace DasRoteQuadrat\BetterContentEditor\Controllers;

use Response;
use Illuminate\Routing\Controller;

class Languages extends Controller
{
    public function get($lang)
    {
        $translation = file_get_contents(__DIR__ .'/translations/'.$lang.'.json', FALSE, NULL);
        return $translation;
    }
}
