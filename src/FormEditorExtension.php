<?php

namespace Bolt\Extensions\Ross\FormEditor;

use Bolt\Application;
use Bolt\Extension\SimpleExtension;
use Bolt\Extensions\Ross\FormEditor\Controller\FormEditorController;

class FormEditorExtension extends SimpleExtension
{
    const CONTAINER = 'extensions.formeditor';

    protected function registerTwigPaths()
    {
        return [
            'templates/*' => ['position' => 'prepend']
        ];
    }

    protected function registerBackendControllers()
    {
        return [
            '/extensions/formeditor' =>  new FormEditorController(),
        ];
    }

    protected function registerMenuEntries()
    {
        $editForms = (new MenuEntry('passwordProtect', '/bolt/extensions/formeditor'))
            ->setLabel('Edit Forms')
            ->setIcon('fa:pencil-square-o');

        return [
            $editForms
        ];
    }

}
