<?php namespace DasRoteQuadrat\BetterContentEditor\Components;

use Cms\Classes\ComponentBase;
use BackendAuth;
use DasRoteQuadrat\BetterContentEditor\Models\Images;

class ImageUploader extends ComponentBase {

    public $renderCount;

    public function componentDetails()
    {
        return [
            'name'        => 'ImageUploader',
            'description' => 'Lade Bilder wie beim ContentEditor im Frontend hoch'
        ];
    }

    public function onRun() {
        $this->renderCount = 0;
    }

    public function getImage($id, $placeholder) {
        $urls = Images::where('item', $id)->pluck('url');
        return $urls->count() > 0 ? $urls[0] : $placeholder;
    }

    public function onRender()
    {
        $this->renderCount += 1;
    }
}
