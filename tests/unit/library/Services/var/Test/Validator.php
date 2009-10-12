<?php
class Test_Validator extends Gene_Services_Validator
{
    protected function _setDefaultRules()
    {
        $validations = array(
            'test1' => array(
                new Zend_Validate_Alnum()
            )
        );
        $this->_rules = $validations;
        return $this;
    }

    public function setRules2()
    {
        $validations = array(
            'test2' => array(
                new Zend_Validate_Alnum()
            ),
            'test3' => array(
                new Zend_Validate_Alnum()
            ),

        );
        $this->_rules = $validations;
        return $this;

    }
}