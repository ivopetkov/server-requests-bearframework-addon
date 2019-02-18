<?php

/*
 * Server requests addon for Bear Framework
 * https://github.com/ivopetkov/server-requests-bearframework-addon
 * Copyright (c) Ivo Petkov
 * Free to use under the MIT license.
 */

use BearFramework\App;
use IvoPetkov\HTML5DOMDocument;

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

$app
        ->addEventListener('beforeSendResponse', function(\BearFramework\App\BeforeSendResponseEventDetails $eventDetails) use ($app, $context, $path) {
            if ($eventDetails->response instanceof App\Response\HTML) {
                $initializeData = [
                    'url' => $app->urls->get($path)
                ];
                $html = '<script>var script=document.createElement(\'script\');script.src=\'' . $context->assets->getURL('assets/serverRequests.min.js', ['cacheMaxAge' => 999999999, 'version' => 1]) . '\';script.onload=function(){ivoPetkov.bearFrameworkAddons.serverRequests.initialize(' . json_encode($initializeData) . ');};document.head.appendChild(script);</script>';
                $domDocument = new HTML5DOMDocument();
                $domDocument->loadHTML($eventDetails->response->content, HTML5DOMDocument::ALLOW_DUPLICATE_IDS);
                $domDocument->insertHTML($html);
                $eventDetails->response->content = $domDocument->saveHTML();
            }
        });
