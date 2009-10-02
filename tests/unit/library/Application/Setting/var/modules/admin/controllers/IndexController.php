<?php
class Admin_IndexController extends Zend_Controller_Action
{
    public function indexAction()
    {
        $response = $this->getResponse();
        $response->setBody(__METHOD__);
    }

    public function listAction()
    {
        $response = $this->getResponse();
        $response->setBody(__METHOD__);
    }

    public function itemAction()
    {
        $response = $this->getResponse();
        $response->setBody('fuga');
    }
}
