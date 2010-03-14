<?php
class Test_Service_PhpDatamapper extends phpDataMapper_Model
{
    protected $table = 'gene_model_test';

    protected $fields = array(
        'id'         => array('type' => 'int', 'primary' => true),
        'name'       => array('type' => 'string', 'required' => true),
        'created_at' => array('type' => 'datetime')
    );
}
