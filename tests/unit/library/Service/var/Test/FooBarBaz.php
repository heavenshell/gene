<?php
class Test_FooBarBaz extends Gene_Service
{
    public function init()
    {
        require_once 'Zend.php';
        $this->_session   = Gene::load('Gene_Service_Session');
        $this->_dao       = Gene::load('Gene_Service_Model')->getDao('Test_Service_Zend');
        $this->getTranslateObject()
             ->setTranslatePath(GENE_TEST_ROOT . '/var/locales/');
        $this->_validator = $this->getValidator('Test_Service_Validator', 'message.ini');

        $this->_before['confirm'] = array(
            array(new Test_Foo(), 'foo')
        );
        $this->_before['create'] = array(
            array(array(new Test_Bar(), 'foo'), array('Test_Bar::foo'))
        );

        $this->_before['editconfirm'] = array(
            array(new Test_Foo(), 'foo'),
            array(new Test_Baz(), 'foo')
        );

        $this->_before['update'] = array(
            'Test_Foo::bar',
            array('Test_Foo', 'baz'),
            array(new Test_Foo, 'baz')
        );
    }

    public function setNamespace()
    {
        $this->_session->setNamespace(__CLASS__);
        return $this;
    }
}

class Test_Foo
{
    public function foo()
    {
        echo __METHOD__;
    }

    public static function bar()
    {
        echo __METHOD__;
    }

    public static function baz()
    {
        echo __METHOD__;
    }
}

class Test_Bar
{
    public function foo($options)
    {
        echo $options;
    }
}

class Test_Baz
{
    public function foo()
    {
        echo __METHOD__;
    }
}
