<?php

namespace Bolt\Extensions\Ross\FormEditor;

use Bolt\Application;
use Bolt\BaseExtension;

class Extension extends BaseExtension
{
    const CONTAINER = 'extensions.formeditor';

    /**
     * Constructor adds an additional Twig path if we are in the Backend.
     *
     * @param Application $app
     */
    public function __construct(Application $app)
    {
        parent::__construct($app);
        if ($this->app['config']->getWhichEnd() == 'backend') {
            $this->app['twig.loader.filesystem']->prependPath(__DIR__.'/twig');
        }
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
            $this->addJavascript('assets/jquery.sortable.min.js', 1);
            $this->addJavascript('assets/formeditor.js', 1);
            $this->addCss('assets/formeditor.css');
            $this->addMenuOption('Edit Forms', $this->app['resources']->getUrl('bolt').'extensions/formeditor', 'fa:pencil-square-o');
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

    /**
     * @return string Extension name
     */
    public function getName()
    {
        return 'formeditor';
    }
}
