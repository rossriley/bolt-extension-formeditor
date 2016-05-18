<?php

namespace Bolt\Extensions\Ross\FormEditor;

use Bolt\Application;
use Bolt\Asset\File\JavaScript;
use Bolt\Asset\File\Stylesheet;
use Bolt\Extension\SimpleExtension;

class Extension extends SimpleExtension
{
    const CONTAINER = 'extensions.formeditor';

    protected function registerAssets()
    {
        return [
            (new Stylesheet('formeditor.css'))->setZone('backend'),
            (new JavaScript('formeditor.js'))->setZone('backend'),
            (new JavaScript('jquery.sortable.js'))->setZone('backend'),
        ];
    }

    protected function registerTwigFunctions()
    {
        return [

        ];
    }

    protected function registerTwigPaths()
    {
        return [
            'twig' => ['position' => 'prepend', 'namespace' => 'bolt']
        ];
    }

    /**
     * {@inheritdoc}
     */
    protected function registerMenuEntries()
    {
        return [
            (new MenuEntry('formeditor', 'extensions/formeditor'))
                ->setLabel(Trans::__('Edit Forms'))
                ->setIcon('fa:pencil-square-o')
                ->setPermission('admin||root||developer||editor'),
        ];
    }

    /**
     * Mounts the editor onto /bolt/extensions/formeditor, and
     * adds the relevant assets.
     */
    public function initialize()
    {
        $path = $this->app['config']->get('general/branding/path').'/extensions/formeditor';

        if ($this->checkAuth()) {
            $this->app->mount($path, new Controller\FormEditorController());
        }
    }

    /**
     * Checks that the user has a non-guest role.
     *
     * @return bool
     */
    public function checkAuth()
    {
        $currentUser = $this->app['users']->getCurrentUser();
        $currentUserId = $currentUser['id'];
        foreach (['admin', 'root', 'developer', 'editor'] as $role) {
            if ($this->app['users']->hasRole($currentUserId, $role)) {
                return true;
            }
        }

        return false;
    }

}
