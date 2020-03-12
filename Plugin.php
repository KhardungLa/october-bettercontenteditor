<?php namespace DasRoteQuadrat\BetterContentEditor;

use Log;
use Event;
use BackendAuth;
use Session;
use Illuminate\Support\Facades\Redirect;
use Backend;
use System\Classes\PluginBase;

/**
 * BetterContentEditor Plugin Information File
 */
class Plugin extends PluginBase
{
    public $elevated = true;

    public function boot() {
        Event::listen('backend.user.login', function($user) {
            if ($user->role->code === 'publisher') {
                Session::put('redirectAfterLogin', '/');
            }
        });
        Event::listen('backend.page.beforeDisplay', function($controller, $action, $params) {
            if ($redirectAfterLogin = Session::pull('redirectAfterLogin', null)) {
                return Redirect::to($redirectAfterLogin); // do redirect
            }
        });
    }

    public function pluginDetails()
    {
        return [
            'name' => 'Better Content Editor',
            'description' => 'Der etwas bessere Content Editor',
            'author' => 'DasRoteQuadrat',
            'icon' => 'icon-edit'
        ];
    }

    public function registerComponents()
    {
        return [
            'DasRoteQuadrat\BetterContentEditor\Components\ImageUploader' => 'imageuploader',
            'DasRoteQuadrat\BetterContentEditor\Components\ContentEditor' => 'contenteditor',
        ];
    }

    public function registerSettings()
    {
        return [
            'settings' => [
                'label' => 'Content Editor Settings',
                'description' => 'Manage main editor settings.',
                'icon' => 'icon-cog',
                'class' => 'DasRoteQuadrat\BetterContentEditor\Models\Settings',
                'order' => 500,
                'permissions' => ['dasrotequadrat.bettercontenteditor.access_settings']
            ]
        ];
    }

    public function registerPermissions()
    {
        return [
            'dasrotequadrat.bettercontenteditor.editor' => [
                'tab' => 'Content Editor',
                'label' => 'Allow to use content editor on frontend'
            ],
            'dasrotequadrat.bettercontenteditor.access_settings' => [
                'tab' => 'Content Editor',
                'label' => 'Access content editor settings'
            ],
        ];
    }
}
