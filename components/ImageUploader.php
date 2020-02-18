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
        $this->page['user'] = BackendAuth::getUser();
        if ($this->page['user']) {
//            $this->addJs('assets/js/plugin.js');
            $this->addCss('assets/imageuploader.css');
        }
    }

    public function getImage($id, $placeholder) {
        $urls = Images::where('item', $id)->pluck('url');
        return $urls->count() > 0 ? $urls[0] : $placeholder;
    }

    public function onRender()
    {
        $this->renderCount = $this->page['renderCountImage'] += 1;
    }
}
