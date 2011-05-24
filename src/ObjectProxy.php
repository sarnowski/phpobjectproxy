<?php

/**
 * Used to act as a proxy before the real object.
 *
 * @author Tobias Sarnowski
 */
interface ObjectProxy {

    /**
     * Will be called for object creation.
     *
     * @abstract
     * @param object $instance the object instance
     * @return object
     */
    function onConstruct($instance);

    /**
     * Will be called on a method call.
     *
     * @abstract
     * @param ObjectProxyCall $call the method call
     * @return mixed
     */
    function onCall(ObjectProxyCall $call);

}
