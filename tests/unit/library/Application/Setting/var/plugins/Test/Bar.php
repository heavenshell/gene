<?php
class Plugins_Test_Bar extends Zend_Controller_Plugin_Abstract
{
    private $_args = null;

    public function __construct($args)
    {
        $this->_args = $args;
    }

    public function getArgs()
    {
        return $this->_args;
    }
}
