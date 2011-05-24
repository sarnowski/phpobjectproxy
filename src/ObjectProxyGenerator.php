<?php
include('ObjectProxyCall.php');
include('ObjectProxy.php');
include('ObjectProxyException.php');

define('OPNL', "\n");

/**
 * Utility class to generate a typesafe proxied object.
 *
 * @author Tobias Sarnowski
 */
class ObjectProxyGenerator {

    /**
     * Generates a typesafe, proxied object.
     *
     * @static
     * @param string $className the original class' name
     * @param ObjectProxy $objectProxy the object proxy to use
     * @param array $constructorArgs arguments used for constructor invocation of the original class
     * @param boolean $lazy if the class should be initialized
     * @return object the resulting object
     */
    public static function generateObject(
        $className,
        ObjectProxy $objectProxy,
        $constructorArgs = array(),
        $lazy = true)
    {
        // the proxy's class name
        $proxyClassName = '_Proxy__'.$className;

        if (!class_exists($proxyClassName, false)) {

            // reflection class for the requested class
            $class = new ReflectionClass($className);

            // check prerequisites
            if ($class->isAbstract() || $class->isFinal()) {
                throw new ObjectProxyException("Class $className must not be final or abstract.");
            }
            if (in_array('ObjectProxy', $class->getInterfaceNames())) {
                throw new ObjectProxyException("Class $className is already proxied and cannot be proxied twice.");
            }

            // class definition
            $classSource  = "class $proxyClassName extends $className {".OPNL.OPNL;

            // fields
            $classSource .= 'private $proxy;'.OPNL;
            $classSource .= 'private $arguments;'.OPNL;
            $classSource .= 'private $instance;'.OPNL;
            $classSource .= 'private $class;'.OPNL;
            $classSource .= 'private $methods = array();'.OPNL;
            $classSource .= OPNL;

            // constructor method
            $classSource .= 'function __construct($proxy, $arguments) {'.OPNL;
            $classSource .= '    $this->proxy = $proxy;'.OPNL;
            $classSource .= '    $this->arguments = $arguments;'.OPNL;
            $classSource .= '    $this->class = new ReflectionClass("'.$className.'");'.OPNL;
            if (!$lazy) {
                $classSource .= '$this->__instantiate();'.OPNL;
            }
            $classSource .= '}'.OPNL.OPNL;

            // helper to set constructor arguments delayed
            $classSource .= 'function __setConstructorArgs($arguments) {'.OPNL;
            $classSource .= '    $this->arguments = $arguments;'.OPNL;
            $classSource .= '}'.OPNL.OPNL;

            // instantiation method
            $classSource .= 'function __instantiate() {'.OPNL;
            $classSource .= '    if ($this->class->getConstructor() != null) {'.OPNL;
            $classSource .= '        $this->instance = $this->class->newInstanceArgs($this->arguments);'.OPNL;
            $classSource .= '    } else {'.OPNL;
            $classSource .= '        $this->instance = $this->class->newInstance();'.OPNL;
            $classSource .= '    }'.OPNL;
            $classSource .= '    $this->proxy->onConstruct($this->instance);'.OPNL;
            $classSource .= '}'.OPNL.OPNL;

            // method caller helper
            $classSource .= 'function __callMethod($instance, $methodName, $arguments) {'.OPNL;
            $classSource .= '    if (!isset($this->methods[$methodName])) {'.OPNL;
            $classSource .= '        $this->methods[$methodName] = $this->class->getMethod($methodName);'.OPNL;
            $classSource .= '    }'.OPNL;
            $classSource .= '    $method = $this->methods[$methodName];'.OPNL;
            $classSource .= '    if (is_null($this->instance)) {'.OPNL;
            $classSource .= '        $this->__instantiate();'.OPNL;
            $classSource .= '    }'.OPNL;
            $classSource .= '    $call = new ObjectProxyCall($this->instance, $method, $arguments);'.OPNL;
            $classSource .= '    return $this->proxy->onCall($call);'.OPNL;
            $classSource .= '}'.OPNL.OPNL;

            // generate overridden functions
            foreach ($class->getMethods() as $method) {
                if ($method->isFinal() || $method->isAbstract() || $method->isPrivate()) {
                    continue;
                }

                if (substr($method->getName(), 0, 2) == '__') {
                    continue;
                }

                $methodKeywords = '';

                if ($method->isPublic()) {
                    $methodKeywords .= 'public ';
                }
                if ($method->isProtected()) {
                    $methodKeywords .= 'protected ';
                }

                $classSource .= $methodKeywords.'function '.$method->getName().'(';
                $parameters = array();
                foreach ($method->getParameters() as $parameter) {
                    $p = '$'.$parameter->getName();
                    if ($parameter->isPassedByReference()) {
                        $p = "&$p";
                    }
                    if ($parameter->isDefaultValueAvailable()) {
                        $p = "$p = '".$parameter->getDefaultValue()."'";
                    }
                    $parameters[] = $p;
                }
                $classSource .= implode(', ', $parameters);
                $classSource .= ') {'.OPNL;


                $parameters = array();
                foreach ($method->getParameters() as $parameter) {
                    $parameters[] = '$'.$parameter->getName();
                }
                $classSource .= 'return $this->__callMethod($this->instance, "'.$method->getName().'", array('.implode(', ', $parameters).'));'.OPNL;

                $classSource .= '}'.OPNL.OPNL;
            }

            // class built
            $classSource .= '}';

            // evaluate the source
            // TODO add option to store the code and just reinclude it the next time
            eval($classSource);
        }

        // now get the proxy class and use it
        $proxyClass = new ReflectionClass($proxyClassName);
        return $proxyClass->newInstanceArgs(array($objectProxy, $constructorArgs));
    }

}
