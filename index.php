<?php

/*
 * Server requests addon for Bear Framework
 * https://github.com/ivopetkov/server-requests-bearframework-addon
 * Copyright (c) Ivo Petkov
 * Free to use under the MIT license.
 */

use BearFramework\App;

$app = App::get();

$context = $app->contexts->get(__DIR__);

$context->assets
    ->addDir('assets');

$context->classes
    ->add('IvoPetkov\BearFrameworkAddons\ServerRequests', 'classes/ServerRequests.php');

$app->shortcuts
    ->add('serverRequests', function () {
        return new IvoPetkov\BearFrameworkAddons\ServerRequests();
    });

$path = '/-server-request-' . substr(base_convert(md5($app->request->base), 16, 36), 0, 6);

$app->routes
    ->add('POST ' . $path, function () use ($app) {
        $name = (string) $app->request->query->getValue('n');
        $formData = (string)$app->request->formData->getValue('d');
        $data = strlen($formData) > 0 ? json_decode($formData, true, 100, JSON_THROW_ON_ERROR) : [];
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
    ->add('serverRequests', function (IvoPetkov\BearFrameworkAddons\ClientPackage $package) use ($app, $context, $path): void {
        //$package->addJSCode(file_get_contents($context->dir . '/assets/serverRequests.js'));
        $package->addJSFile($context->assets->getURL('assets/serverRequests.min.js', ['cacheMaxAge' => 999999999, 'version' => 5]));
        $initializeData = [
            $app->urls->get($path)
        ];
        $package->get = 'ivoPetkov.bearFrameworkAddons.serverRequests.initialize(' . json_encode($initializeData) . ');return ivoPetkov.bearFrameworkAddons.serverRequests;';
    });
