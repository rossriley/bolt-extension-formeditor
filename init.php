<?php

namespace Bolt\Extensions\Ross\Formeditor;

if (isset($app)) {
    $app['extensions']->register(new Extension($app));
}
