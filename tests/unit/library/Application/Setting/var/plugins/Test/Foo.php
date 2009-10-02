<?php
class Plugins_Test_Foo extends Zend_Controller_Plugin_Abstract
{
    private $_args = null;

    public function __construct($args1, $args2)
    {
        $this->_args = array($args1, $args2);
    }

    public function getArgs()
    {
        return $this->_args;
    }
}
