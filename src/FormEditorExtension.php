<?php

namespace Bolt\Extensions\Ross\FormEditor;

use Bolt\Application;
use Bolt\Asset\File\JavaScript;
use Bolt\Asset\File\Stylesheet;
use Bolt\Controller\Zone;
use Bolt\Extension\SimpleExtension;
use Bolt\Extensions\Ross\FormEditor\Controller\FormEditorController;
use Bolt\Menu\MenuEntry;

class FormEditorExtension extends SimpleExtension
{
    const NAME = 'Bolt/FormEditor';

    protected function registerTwigPaths()
    {
        return [
            'templates' => ['position' => 'prepend']
        ];
    }

    protected function registerBackendControllers()
    {
        return [
            '/extensions/formeditor' =>  new FormEditorController($this->getConfig()),
        ];
    }

    protected function registerAssets()
    {
        $sortable = (new JavaScript())
            ->setFileName('jquery.sortable.min.js')
            ->setLate(true)
            ->setZone(Zone::BACKEND)
        ;

        $editorjs = (new JavaScript())
            ->setFileName('formeditor.js')
            ->setLate(true)
            ->setZone(Zone::BACKEND)
        ;

        $editorcss = (new Stylesheet())
            ->setFileName('formeditor.css')
            ->setZone(Zone::BACKEND)
        ;

        return [
            $sortable,
            $editorjs,
            $editorcss
        ];
    }

    protected function registerMenuEntries()
    {
        $editForms = (new MenuEntry('passwordProtect', '/extensions/formeditor'))
            ->setLabel('Edit Forms')
            ->setIcon('fa:pencil-square-o');

        return [
            $editForms
        ];
    }

}
