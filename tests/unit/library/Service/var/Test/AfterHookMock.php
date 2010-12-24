<?php
class Test_AfterHookMock extends Gene_Service
{
    public $methodResult = null;

    public function init()
    {
       require_once 'Zend.php';
       $this->_session   = Gene::load('Gene_Service_Session');
       $this->_dao       = Gene::load('Gene_Service_Model')->getDao('Test_Service_Zend');
       $this->getTranslateObject()
            ->setTranslatePath(GENE_TEST_ROOT . '/var/locales/');
       $this->_validator = $this->getValidator('Test_Service_Validator', 'message.ini');

       $this->_after['confirm']        = 'afterConfirm';
       $this->_after['create']         = 'afterCreate';
       $this->_after['editconfirm']    = 'afterEditconfirm';
       $this->_after['update']         = 'afterUpdate';
       $this->_after['deleteconfirm']  = 'afterDeleteconfirm';
       $this->_after['delete']         = 'afterDelete';
    }

    public function setNamespace()
    {
        $this->_session->setNamespace(__CLASS__);
        return $this;
    }

    public function afterConfirm()
    {
        $this->methodResult = __METHOD__;
    }

    public function afterCreate()
    {
        $this->methodResult = __METHOD__;
    }

    public function afterEditconfirm()
    {
        $this->methodResult = __METHOD__;
    }

    public function afterUpdate()
    {
        $this->methodResult = __METHOD__;
    }

    public function afterDeleteconfirm()
    {
        $this->methodResult = __METHOD__;
    }

    public function afterDelete()
    {
        $this->methodResult = __METHOD__;
    }



}
