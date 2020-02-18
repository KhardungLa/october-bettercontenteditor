<?php namespace DasRoteQuadrat\BetterContentEditor;

use Backend;
use System\Classes\PluginBase;

/**
 * BetterContentEditor Plugin Information File
 */
class Plugin extends PluginBase
{
    /**
     * Returns information about this plugin.
     *
     * @return array
     */
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
            'samuell.contenteditor.editor' => [
                'tab' => 'Content Editor',
                'label' => 'Allow to use content editor on frontend'
            ],
            'samuell.contenteditor.access_settings' => [
                'tab' => 'Content Editor',
                'label' => 'Access content editor settings'
            ],
        ];
    }
}
