<?php
class Test_Service_Validator extends Gene_Service_Validator
{
    public function setRules($session = null, $id = null, $adapter = null)
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

    public function createRules()
    {
        $validations = array(
            'name' => array(
                new Zend_Validate_NotEmpty(),
                new Zend_Validate_Regex('/^[0-9A-Za-z+\_]*$/ '),
                new Zend_Validate_StringLength(1, 100, 'utf-8'),
                'messages' => array(
                    0 => $this->_translate->_('Please enter user name.'),
                    1 => $this->_translate->_('Invalid user name.'),
                    2 => $this->_translate->_('User name should enter less than 100.'),
                )
            )
        );
        $this->_rules = $validations;
        return $this;
    }

    public function updateRules()
    {
        $validations = array(
            'id' => array(
                new Zend_Validate_Digits(),
                'messages' => array(
                    0 => $this->_translate->_('Id should be a number.')
                )
            ),
            'name' => array(
                new Zend_Validate_NotEmpty(),
                new Zend_Validate_Regex('/^[0-9A-Za-z+\_]*$/ '),
                new Zend_Validate_StringLength(1, 100, 'utf-8'),
                'messages' => array(
                    0 => $this->_translate->_('Please enter user name.'),
                    1 => $this->_translate->_('Invalid user name.'),
                    2 => $this->_translate->_('User name should enter less than 100.'),
                )
            )
        );
        $this->_rules = $validations;
        return $this;
    }

    public function deleteRules()
    {
        $validations = array(
            'id' => array(
                new Zend_Validate_Digits(),
                'messages' => array(
                    0 => $this->_translate->_('Id should be a number.')
                )
            )
        );
        $this->_rules = $validations;
        return $this;
    }





}
