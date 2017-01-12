<?php

/*
 * Server requests addon for Bear Framework
 * https://github.com/ivopetkov/server-requests-bearframework-addon
 * Copyright (c) 2016-2017 Ivo Petkov
 * Free to use under the MIT license.
 */

use BearFramework\App;

$app = App::get();

$context = $app->getContext(__FILE__);

$context->assets->addDir('assets');
$context->classes->add('IvoPetkov\ServerRequests', 'classes/ServerRequests.php');

$app->defineProperty('serverRequests', [
    'init' => function() {
        return new IvoPetkov\ServerRequests();
    },
    'readonly' => true
]);

$path = '/server-request-' . md5($app->request->base);

$app->routes->add($path, function() use ($app) {
    $name = (string) $app->request->query->get('n');
    $postedValues = $app->request->data->getList();
    $data = [];
    foreach ($postedValues as $postedValue) {
        $data[$postedValue['name']] = $postedValue['value'];
    }
    if ($app->serverRequests->exists($name)) {
        $result = ['status' => '1', 'text' => (string) $app->serverRequests->execute($name, $data)];
    } else {
        $result = ['status' => '0'];
    }
    $response = new App\Response\JSON(json_encode($result));
    $response->headers->set('X-Robots-Tag', 'noindex');
    $response->headers->set('Cache-Control', 'private, max-age=0');
    return $response;
}, ['POST']);

$app->hooks->add('responseCreated', function($response) use ($app, $context, $path) {
    if ($response instanceof App\Response\HTML) {
        $domDocument = new IvoPetkov\HTML5DOMDocument();
        $domDocument->loadHTML($response->content);
        $initializeData = [
            'url' => $app->urls->get($path)
        ];
        $html = '<script>var script=document.createElement(\'script\');script.src=\'' . $context->assets->getUrl('assets/serverRequests.js') . '\';script.onload=function(){ivoPetkov.bearFramework.addons.serverRequests.initialize(' . json_encode($initializeData) . ');};document.head.appendChild(script);</script>';
        $domDocument->insertHTML($html);
        $response->content = $domDocument->saveHTML();
    }
});
