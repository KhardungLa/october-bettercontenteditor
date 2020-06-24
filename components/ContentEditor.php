<?php namespace DasRoteQuadrat\BetterContentEditor\Components;

use Illuminate\Support\Facades\Redirect;
use Lang;
use Carbon\Carbon;
use Log;
use Cache;
use File;
use BackendAuth;
use Session;
use Backend;
use App;
use Cms\Classes\Content;
use Cms\Classes\ComponentBase;
use DasRoteQuadrat\BetterContentEditor\Models\Content as CmsContent;
use DasRoteQuadrat\BetterContentEditor\Models\Settings;

class ContentEditor extends ComponentBase
{
    public $content;
    public $defaultFile;
    public $file;
    public $fixture;
    public $tools;
    public $class;
    public $buttons;
    public $palettes;
    public $renderCount;

    public function componentDetails()
    {
        return [
            'name'        => 'Content Editor',
            'description' => 'Edit your front-end content in page.'
        ];
    }

    public function defineProperties()
    {
        return [
            'file' => [
                'title'       => 'Content file',
                'description' => 'Content block filename to edit, optional',
                'default'     => '',
                'type'        => 'dropdown'
            ],
            'fixture' => [
                'title'       => 'Content block tag with disabled toolbox',
                'description' => 'Fixed name for content block, useful for inline texts (headers, spans...)',
                'default'     => ''
            ],
            'tools' => [
                'title'       => 'List of enabled tools',
                'description' => 'List of enabled tools for selected content (for all use *)',
                'default'     => ''
            ],
            'class' => [
                'title'       => 'CSS classes',
                'description' => 'CSS class or classes that should be applied to the content block when rendered',
                'default'     => ''
            ],
        ];
    }

    public function getFileOptions()
    {
        return Content::sortBy('baseFileName')->lists('baseFileName', 'fileName');
    }

    public function onRun()
    {
        $this->renderCount = 0;
        if ($this->checkEditor()) {

            $this->buttons = Settings::get('enabled_buttons');
            $this->palettes = Settings::get('style_palettes');

            // put content tools js + css
            $this->addCss('assets/content-tools.min.css');
            $this->addCss('assets/contenteditor.css');
        }
    }

    public function onRender()
    {
        $this->renderCount += 1;

        $this->defaultFile = $this->property('file');
        $this->file = $this->setFile($this->property('file'));
        $content = $this->getFile();

        if ($this->checkEditor()) {
            $this->fixture = $this->property('fixture');
            $this->tools = $this->property('tools');
            $this->class = $this->property('class');
            $this->page['localisations'] = Lang::get('dasrotequadrat.bettercontenteditor::lang.translations');
            $this->page['hasRevisions'] = $this->hasRevisions();
            $this->page['lang'] = App::getLocale();
            if ($this->page['lang'] !== 'en') {
                $this->page['translations'] = file_get_contents(__DIR__ .'/contenteditor/translations/' . $this->page['lang'] . '.json', FALSE, NULL);
            }
            $this->content = $content;
        } else {
            return Cache::remember('bettercontenteditor::content-' . $this->file, now()->addHours(24), function () use ($content) {
                return $this->renderPartial('@render.htm', ['content' => $content]);
            });
        }
    }

    protected function hasRevisions() {
        $result = false;
        $revisions = CmsContent::where('item', $this->getItemName())->first();
        if ($revisions && count($revisions->revision_history->filter(function($item) {return $item->old_value;}))) {
            $result = true;
        }
        return $result;
    }

    public function onSave()
    {
        if ($this->checkEditor()) {

            $fileName = post('file');

            if ($load = Content::load($this->getTheme(), $fileName)) {
                $fileContent = $load;
            } else {
                $fileContent = Content::inTheme($this->getTheme());
            }
            $contentToSave = post('content');

            $fileContent->fill([
                'fileName' => $fileName,
                'markup' => $contentToSave
            ]);

            $fileContent->save();

            $itemName = $this->getItemName($fileName);
            $contentStore = CmsContent::firstOrCreate(['item' => $itemName]);
            $contentStore->content = $contentToSave;
            $contentStore->save();
            Cache::forget('bettercontenteditor::content-' . $fileName);
        }
    }

    public function onRevisions()
    {
        $itemName = $this->getItemName(post('file'));
        if ($this->checkEditor()) {
            $revisions = CmsContent::where('item', $itemName)->first();
            return !$revisions ? [] : $revisions->revision_history->map(function($revision) {
                $newRevision = $revision;
                Carbon::setLocale( App::getLocale() );
                $newRevision['date'] = Carbon::createFromFormat( 'Y-m-d H:i:s', $revision->updated_at )->diffForHumans();
                return $revision;
            });
        }
    }

    public function getFile()
    {
        if (Content::load($this->getTheme(), $this->file)) {
            return $this->renderContent($this->file);
        } else if (Content::load($this->getTheme(), $this->defaultFile)) {
            return $this->renderContent($this->defaultFile);
        }
        return '';
    }

    public function onSignout()
    {
        BackendAuth::logout();
        Session::flush();
        return Redirect::to('/');
    }

    public function setFile($file)
    {
        if ($this->translateExists()) {
            return $this->setTranslateFile($file);
        }
        return $file;
    }

    public function setTranslateFile($file)
    {
        $translate = \RainLab\Translate\Classes\Translator::instance();
        $locales = \RainLab\Translate\Models\Locale::listEnabled();
        $defaultLocale = $translate->getDefaultLocale();
        $locale = $translate->getLocale();

        // Compability with Rainlab.StaticPage
        // StaticPages content does not append default locale to file.
        if (($this->fileExists($file) && $locale === $defaultLocale) || count($locales) < 2) {
            return $file;
        }

        $dotPos = strrpos($file, '.');
        $injectLocale = $dotPos == false ? strlen($file) - 1 : $dotPos;
        if ($dotPos == false) {
            return $file . '.'.$locale . '.htm';
        } else {
            return substr_replace($file, '.'.$locale, $injectLocale, 0);
        }
    }

    public function checkEditor()
    {
        $backendUser = BackendAuth::getUser();
        return $backendUser && $backendUser->hasAccess('dasrotequadrat.bettercontenteditor.editor');
    }

    public function fileExists($file) {
        return File::exists((new Content)->getFilePath($file));
    }

    public function translateExists()
    {
        return class_exists('\RainLab\Translate\Classes\Translator');
    }

    protected function getItemName($file = NULL) {
        return $this->getTheme()->getDirName() . '.' . ($file ? $file : $this->file);
    }
}
