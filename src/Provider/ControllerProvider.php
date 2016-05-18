<?php
namespace Bolt\Extensions\Ross\FormEditor\Provider;

use Bolt\Extensions\Ross\FormEditor\Controller;
use Silex\Application;
use Silex\ServiceProviderInterface;

/**
 * Class ControllerProvider
 * @author Ross Riley <riley.ross@gmail.com>
 */
class ControllerProvider implements ServiceProviderInterface
{
    /**
     * {@inheritdoc}
     */
    public function register(Application $app)
    {
        $app['formeditor.controller.backend'] = $app->share(
            function ($app) {
                return new Controller\FormEditorController();
            }
        );
    }
}