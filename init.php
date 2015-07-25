<?php

namespace Bolt\Extensions\Ross\FormEditor;

if (isset($app)) {
    $app['extensions']->register(new Extension($app));
}
