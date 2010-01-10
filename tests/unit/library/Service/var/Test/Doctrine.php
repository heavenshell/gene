<?php
class Test_Service_Doctrine extends Doctrine_Record
{
    public function setTableDefinition()
    {
        $this->setTableName('gene_model_test');
        $this->hasColumn('id', 'integer', 4, array(
            'type'          => 'integer',
            'length'        => 4,
            'fixed'         => false,
            'unsigned'      => true,
            'primary'       => true,
            'autoincrement' => true,
        ));
        $this->hasColumn('name', 'string', 100, array(
            'type'          => 'string',
            'length'        => 100,
            'fixed'         => false,
            'unsigned'      => false,
            'primary'       => false,
            'notnull'       => true,
            'autoincrement' => false,
        ));
        $this->hasColumn('created_at', 'timestamp', null, array(
            'type'          => 'timestamp',
            'fixed'         => false,
            'unsigned'      => false,
            'primary'       => false,
            'notnull'       => true,
            'autoincrement' => false,
        ));
    }
}
