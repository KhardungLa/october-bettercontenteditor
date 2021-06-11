<?php namespace DasRoteQuadrat\BetterContentEditor\Components;

use Log;
use BackendAuth;
use Cms\Classes\ComponentBase;
use DasRoteQuadrat\BetterContentEditor\Models\Images;

class ImageUploader extends ComponentBase {

    public $renderCount;

    public function componentDetails() {
        return [
            'name'        => 'ImageUploader',
            'description' => 'Lade Bilder wie beim ContentEditor im Frontend hoch'
        ];
    }

    public function onRun() {
        $this->renderCount = 0;
        $this->page['backendUser'] = BackendAuth::getUser();
    }

    public function getImage($id, $placeholder) {
        $theme = $this->getTheme()->getId();
        $urls = Images::where('item', $theme.'.'.$id)->pluck('url');
        return $urls->count() > 0 ? $urls[0] : $placeholder;
    }

    public function onRender()
    {
        $this->renderCount += 1;
    }
}
