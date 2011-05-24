<?php

/**
 *
 *
 * @author Tobias Sarnowski
 */
class ObjectProxyCall {

    /**
     * @var object
     */
    private $instance;

    /**
     * @var ReflectionMethod
     */
    private $method;

    /**
     * @var array
     */
    private $arguments;

    function __construct($instance, $method, $arguments) {
        $this->instance = $instance;
        $this->method = $method;
        $this->arguments = $arguments;
    }

    /**
     * @return array
     */
    public function getArguments() {
        return $this->arguments;
    }

    /**
     * @return object
     */
    public function getInstance() {
        return $this->instance;
    }

    /**
     * @return \ReflectionMethod
     */
    public function getMethod() {
        return $this->method;
    }

    /**
     * Call the real message.
     *
     * @return mixed
     */
    public function call() {
        return $this->method->invokeArgs($this->instance, $this->arguments);
    }
}
