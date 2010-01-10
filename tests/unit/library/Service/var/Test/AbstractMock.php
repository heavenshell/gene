<?php
class Test_Service_AbstractMock extends Gene_Service_Abstract
{
    public $init = false;

    public function init()
    {
        $this->init = true;
    }
}
