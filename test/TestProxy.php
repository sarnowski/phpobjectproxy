<?php

/**
 *
 *
 * @author Tobias Sarnowski
 */
class TestProxy implements ObjectProxy {

    /**
     * Will be called on a method call.
     *
     * @param ObjectProxyCall $call the method call
     * @return mixed
     */
    function onCall(ObjectProxyCall $call) {
        echo "before call<br/>";
        $result = $call->call();
        echo "after call<br/>";
        return $result;
    }

    /**
     * Will be called for object creation.
     *
     * @param object $instance the object instance
     * @return object
     */
    function onConstruct($instance) {
        echo "after construction<br/>";
    }
}
