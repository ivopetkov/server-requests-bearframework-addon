<?php

/*
 * Server requests addon for Bear Framework
 * https://github.com/ivopetkov/server-requests-bearframework-addon
 * Copyright (c) 2016-2017 Ivo Petkov
 * Free to use under the MIT license.
 */

/**
 * @runTestsInSeparateProcesses
 */
class CallbacksTest extends BearFrameworkAddonTestCase
{

    /**
     * 
     */
    public function testCallbacks()
    {
        $serverRequests = new IvoPetkov\BearFrameworkAddons\ServerRequests();
        $this->assertFalse($serverRequests->exists('name1'));
        $serverRequests->add('name1', function($data) {
            return $data['var1'];
        });
        $this->assertTrue($serverRequests->exists('name1'));
        $this->assertTrue($serverRequests->execute('name1', ['var1' => '123']) === '123');
        $this->assertTrue($serverRequests->execute('name2', []) === '');
    }

}
