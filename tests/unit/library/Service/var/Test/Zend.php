<?php
class Test_Service_Zend extends Zend_Db_Table_Abstract
{
    protected $_name = 'gene_model_test';

    protected $_primary = 'id';

    public function doCreate(array $params)
    {
        return $this->insert($params);
    }

    public function doUpdate(array $params)
    {
        $where = array('id = ?' => $params['id']);
        return $this->update($params, $where);
    }

    public function doDelete(array $params)
    {
        $where = array('id = ?' => $params['id']);
        return $this->delete($params, $where);
    }
}
