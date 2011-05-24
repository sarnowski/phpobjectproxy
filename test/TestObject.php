<?php

/**
 *
 *
 * @author Tobias Sarnowski
 */
class TestObject extends AbstractObject implements Interface2 {

    private $name;
    private $ref;

    function __construct($name, $ref) {
        echo "called __construct()<br/>";
        $this->name = $name;
        $this->ref = $ref;
    }

    function imethod2() {
        echo "called imethod2()<br/>";
    }

    function getName() {
        return $this->name;
    }

    function getFullName() {
        return $this->getName().'{'.$this->ref->getName().'}';
    }
}
