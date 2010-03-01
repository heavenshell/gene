<?php
class Test_ServiceMock extends Gene_Service
{
    public function init()
    {
       require_once 'Zend.php';
       $this->_session   = Gene::load('Gene_Service_Session');
       $this->_dao       = $this->getDao('Test_Service_Zend');
       $this->getTranslateObject()
            ->setTranslatePath(GENE_TEST_ROOT . '/var/locales/');
       $this->_validator = $this->getValidator('Test_Service_Validator', 'message.ini');
    }

    public function setNamespace()
    {
        $this->_session->setNamespace(__CLASS__);
        return $this;
    }
}
