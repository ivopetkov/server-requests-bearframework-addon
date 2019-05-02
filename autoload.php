<?php

/*
 * Server requests addon for Bear Framework
 * https://github.com/ivopetkov/server-requests-bearframework-addon
 * Copyright (c) Ivo Petkov
 * Free to use under the MIT license.
 */

BearFramework\Addons::register('ivopetkov/server-requests-bearframework-addon', __DIR__, [
    'require' => [
        'ivopetkov/client-shortcuts-bearframework-addon'
    ]
]);
