<?php
class Test_Service_ModelMock extends Gene_Service_Model
{
    public function getLimit()
    {
        return $this->_limit;
    }

    public function getOffset()
    {
        return $this->_offset;
    }

    public function getInstances()
    {
        return $this->_instances;
    }
}
