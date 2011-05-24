<?php
/**
 *
 *
 * @author Tobias Sarnowski
 */

include('../src/ObjectProxyGenerator.php');

include('Interface1.php');
include('Interface2.php');
include('AbstractObject.php');
include('TestObject.php');
include('TestProxy.php');


echo "Creating new test proxy...<br/>";
$proxy = new TestProxy();

echo "Generating proxied object...<br/>";
$obj1 = ObjectProxyGenerator::generateObject('TestObject', $proxy, array('TestObj1'));
$obj2 = ObjectProxyGenerator::generateObject('TestObject', $proxy, array('TestObj2'));

echo "Cyclic dependency...<br/>";
$obj1->__setConstructorArgs(array('TestObj1', $obj2));
$obj2->__setConstructorArgs(array('TestObj2', $obj1));

echo "Calling first method...<br/>";
$obj1->imethod1('Bla1');

echo "Calling second method...<br/>";
$obj1->imethod2();

echo "Cyclic:<br/>";
echo "Obj1: ".$obj1->getFullName()."<br/>";
echo "Obj2: ".$obj2->getFullName()."<br/>";
