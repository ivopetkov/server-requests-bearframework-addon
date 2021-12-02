<?php

/*
 * Server requests addon for Bear Framework
 * https://github.com/ivopetkov/server-requests-bearframework-addon
 * Copyright (c) Ivo Petkov
 * Free to use under the MIT license.
 */

/**
 * @runTestsInSeparateProcesses
 */
class CallbacksTest extends BearFramework\AddonTests\PHPUnitTestCase
{

    /**
     * 
     */
    public function testCallbacks()
    {
        $serverRequests = new IvoPetkov\BearFrameworkAddons\ServerRequests();
        $this->assertFalse($serverRequests->exists('name1'));
        $serverRequests->add('name1', function(array $data) {
            return $data['var1'];
        });
        $response = new \BearFramework\App\Response\JSON();
        $this->assertTrue($serverRequests->exists('name1'));
        $this->assertTrue($serverRequests->execute('name1', ['var1' => '123'], $response) === '123');
        $this->assertTrue($serverRequests->execute('name2', [], $response) === '');
        $this->assertTrue($serverRequests->execute('name2', [], $response) === '');
    }

    /**
     * 
     */
    public function testCookies()
    {
        $serverRequests = new IvoPetkov\BearFrameworkAddons\ServerRequests();
        $this->assertFalse($serverRequests->exists('name1'));
        $serverRequests->add('name1', function(array $data, \BearFramework\App\Response $response) {
            $response->cookies->set($response->cookies->make('X-Custom-1', 'value1'));
            return 'value2';
        });
        $response = new \BearFramework\App\Response\JSON();
        $this->assertTrue($serverRequests->exists('name1'));
        $this->assertTrue($serverRequests->execute('name1', [], $response) === 'value2');
        $this->assertTrue($response->cookies->get('X-Custom-1')->value === 'value1');
    }

}
