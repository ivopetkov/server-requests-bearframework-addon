<?php

/*
 * Server requests addon for Bear Framework
 * https://github.com/ivopetkov/server-requests-bearframework-addon
 * Copyright (c) 2016-2017 Ivo Petkov
 * Free to use under the MIT license.
 */

namespace IvoPetkov;

/**
 *
 */
class ServerRequests
{

    /**
     * Array containing the added callbacks
     * 
     * @var array 
     */
    private $callbacks = [];

    /**
     * Register a named callback
     * 
     * @param string $name
     * @param callable $callback
     */
    public function add(string $name, callable $callback): void
    {
        $this->callbacks[$name] = $callback;
    }

    /**
     * Checks whether a callback with the name specified exists
     * 
     * @param string $name The name of the callback
     * @return bool Returns TRUE if a callback with the name specified is added. FALSE otherwise.
     */
    public function exists(string $name): bool
    {
        return isset($this->callbacks[$name]);
    }

    /**
     * Executes the callback for the name specified
     * 
     * @param string $name The name of the callback
     * @param array $data The data that will be passed to the callback
     * @return string Returns the callback result
     */
    public function execute(string $name, array $data): string
    {
        if (isset($this->callbacks[$name])) {
            return (string) call_user_func($this->callbacks[$name], $data);
        }
        return '';
    }

}
