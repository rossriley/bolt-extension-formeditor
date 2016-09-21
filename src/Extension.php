<?php

namespace Bolt\Extensions\Ross\FormEditor;

use Bolt\Application;
use Bolt\Asset\File\JavaScript;
use Bolt\Asset\File\Stylesheet;
use Bolt\Extension\SimpleExtension;
use Bolt\Extensions\Ross\FormEditor\Provider\ControllerProvider;
use Bolt\Menu\MenuEntry;
use Bolt\Translation\Translator as Trans;

class Extension extends SimpleExtension
{
    const CONTAINER = 'extensions.formeditor';

    /**
     * {@inheritdoc}
     */
    public function getServiceProviders()
    {
        return [
            $this,
            new ControllerProvider($this->getConfig()),
        ];
    }

    protected function registerAssets()
    {
        return [
            (new Stylesheet('formeditor.css'))->setZone('backend'),
            (new JavaScript('formeditor.js'))->setZone('backend'),
            (new JavaScript('jquery.sortable.min.js'))->setZone('backend'),
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
            'twig/formeditor' => ['namespace' => 'formeditor']
        ];
    }

    /**
     * {@inheritdoc}
     */
    protected function registerMenuEntries()
    {
        return [
            (new MenuEntry('formeditor', 'formeditor'))
                ->setLabel(Trans::__('Form editor'))
                ->setIcon('fa:pencil-square-o')
                ->setPermission('admin||root||developer||editor'),
        ];
    }

    /**
     * {@inheritdoc}
     */
    protected function registerBackendControllers()
    {
        $app = $this->getContainer();
        return [
            '/extend/formeditor' => $app['formeditor.controller.backend'],
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
