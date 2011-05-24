<?php

/**
 *
 *
 * @author Tobias Sarnowski
 */
abstract class AbstractObject implements Interface1 {

    function __construct($text, $blub) {

    }

    function imethod1($text) {
        echo "called imethod1($text)<br/>";
    }
}
