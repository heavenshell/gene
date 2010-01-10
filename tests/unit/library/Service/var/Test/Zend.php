<?php
class Test_Service_Zend extends Zend_Db_Table_Abstract
{
    /**
     * Table name
     *
     * @var    string
     * @access protected
     */
    protected $_name = 'gene_model_test';

    /**
     * Primary key
     *
     * @var    string
     * @access protected
     */
    protected $_primary = 'id';
}
