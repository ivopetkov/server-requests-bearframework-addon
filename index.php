<?php

/*
 * Server requests addon for Bear Framework
 * https://github.com/ivopetkov/server-requests-bearframework-addon
 * Copyright (c) Ivo Petkov
 * Free to use under the MIT license.
 */

use BearFramework\App;

$app = App::get();

$context = $app->contexts->get(__FILE__);

$context->assets
        ->addDir('assets');

$context->classes
        ->add('IvoPetkov\BearFrameworkAddons\ServerRequests', 'classes/ServerRequests.php');

$app->shortcuts
        ->add('serverRequests', function() {
            return new IvoPetkov\BearFrameworkAddons\ServerRequests();
        });

$path = '/-server-request-' . md5($app->request->base);

$app->routes
        ->add('POST ' . $path, function() use ($app) {
            $name = (string) $app->request->query->getValue('n');
            $formData = $app->request->formData->getList();
            $data = [];
            foreach ($formData as $postedValue) {
                $data[$postedValue->name] = $postedValue->value;
            }
            $response = new App\Response\JSON();
            if ($app->serverRequests->exists($name)) {
                $result = ['status' => '1', 'text' => (string) $app->serverRequests->execute($name, $data, $response)];
            } else {
                $result = ['status' => '0'];
            }
            $response->content = json_encode($result);
            $response->headers
            ->set($response->headers->make('X-Robots-Tag', 'noindex, nofollow'))
            ->set($response->headers->make('Cache-Control', 'private, max-age=0'));
            return $response;
        });

$app->clientPackages
        ->add('serverRequests', md5('1' . $path), function(IvoPetkov\BearFrameworkAddons\ClientPackage $package) use ($app, $context, $path) {
            $package->addJSFile($context->assets->getURL('assets/serverRequests.min.js', ['cacheMaxAge' => 999999999, 'version' => 2]));
            $initializeData = [
                $app->urls->get($path)
            ];
            $package->init = 'ivoPetkov.bearFrameworkAddons.serverRequests.initialize(' . json_encode($initializeData) . ');';
            $package->get = 'return ivoPetkov.bearFrameworkAddons.serverRequests;';
        });

