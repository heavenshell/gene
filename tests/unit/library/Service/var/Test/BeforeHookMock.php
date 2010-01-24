<?php
class Test_BeforeHookMock extends Gene_Service
{
    public $methodResult = null;

    public function init()
    {
       require_once 'Zend.php';
       $this->_session   = Gene::load('Gene_Service_Session');
       $this->_dao       = $this->getDao('Test_Service_Zend');
       $this->setTranslatePath(GENE_TEST_ROOT . '/var/locales/');
       $this->_validator = $this->getValidator('Test_Service_Validator', 'message.ini');
       $this->_before['confirm']        = 'beforeConfirm';
       $this->_before['create']         = 'beforeCreate';
       $this->_before['editconfirm']    = 'beforeEditconfirm';
       $this->_before['update']         = 'beforeUpdate';
       $this->_before['deleteconfirm']  = 'beforeDeleteconfirm';
       $this->_before['delete']         = 'beforeDelete';

    }

    public function setNamespace()
    {
        $this->_session->setNamespace(__CLASS__);
        return $this;
    }

    public function beforeConfirm()
    {
        $this->methodResult = __METHOD__;
    }

    public function beforeCreate()
    {
        $this->methodResult = __METHOD__;
    }

    public function beforeEditconfirm()
    {
        $this->methodResult = __METHOD__;
    }

    public function beforeUpdate()
    {
        $this->methodResult = __METHOD__;
    }

    public function beforeDeleteconfirm()
    {
        $this->methodResult = __METHOD__;
    }

    public function beforeDelete()
    {
        $this->methodResult = __METHOD__;
    }



}
