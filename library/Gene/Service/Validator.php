<?php
/**
 * Validator
 *
 * PHP version 5.2
 *
 * Copyright (c) 2009-2010 Shinya Ohyanagi, All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions
 * are met:
 *
 *   * Redistributions of source code must retain the above copyright
 *     notice, this list of conditions and the following disclaimer.
 *
 *   * Redistributions in binary form must reproduce the above copyright
 *     notice, this list of conditions and the following disclaimer in
 *     the documentation and/or other materials provided with the
 *     distribution.
 *
 *   * Neither the name of Shinya Ohyanagi nor the names of his
 *     contributors may be used to endorse or promote products derived
 *     from this software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS
 * FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE
 * COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT,
 * INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING,
 * BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER
 * CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT
 * LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN
 * ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 *
 * @category  Gene
 * @package   Gene_Service
 * @version   $id$
 * @copyright 2009-2010 Shinya Ohyanagi
 * @author    Shinya Ohyanagi <sohyanagi@gmail.com>
 * @license   New BSD License
 */

/**
 * Base class of validator
 *
 * @category  Gene
 * @package   Gene_Service
 * @version   $id$
 * @copyright 2009-2010 Shinya Ohyanagi
 * @author    Shinya Ohyanagi <sohyanagi@gmail.com>
 * @license   New BSD License
 */
abstract class Gene_Service_Validator
{
    /**
     * Validator object
     *
     * @var    mixed
     * @access private
     */
    protected $_validator = null;

    /**
     * Validation rules
     *
     * @var    mixed
     * @access protected
     */
    protected $_rules = null;

    /**
     * Input filters
     *
     * @var    mixed
     * @access protected
     */
    protected $_filters = null;

    /**
     * Request parameters
     *
     * @var    mixed
     * @access protected
     */
    protected $_request = null;

    /**
     * Error messages
     *
     * @var    mixed
     * @access protected
     */
    protected $_errorMessages = null;

    /**
     * Validator's default messages
     *
     * @var    mixed
     * @access protected
     */
    protected $_validatorTranslate = null;

    /**
     * Translate object for application messages
     *
     * @var    mixed
     * @access protected
     */
    protected $_appTranslate = null;

    /**
     * Set validator's default  messages
     *
     * @param  mixed $value
     * @access public
     * @return Gene_Service_Validator Fluent interface
     */
    public function setValidatorTranslate(Zend_Translate $value)
    {
        $this->_validatorTranslate = $value;
        return $this;
    }

    /**
     * Set translate object for applicaton messages
     *
     * @param  Zend_Translate $value
     * @access public
     * @return Gene_Service_Validator Fluent interface
     */
    public function setAppTranslate(Zend_Translate $value)
    {
        $this->_appTranslate = $value;
        return $this;
    }

    /**
     * Get translate object
     *
     * @access protected
     * @return Zend_Translate Translate object
     */
    protected function _getTranslate()
    {
        return $this->_appTranslate;
    }

    /**
     * Set validation rules
     *
     * @param  $session Session data
     * @param  $adapter Database adapter
     * @access protected
     * @return mixed Validation rules
     */
    abstract public function setRules($session = null, $id = null, $adapter = null);

    /**
     * Set filters
     *
     * @param  mixed $value
     * @access public
     * @return Gene_Service_Validator Fluent interface
     */
    public function setFilters($value)
    {
        $this->_filters = $value;
        return $this;
    }

    /**
     * setRequest
     *
     * @param  mixed $request Request parameters
     * @access public
     * @return Gene_Service_Validator Fluent interface
     */
    public function setRequest($request)
    {
        $this->_request = $request;
        return $this;
    }

    /**
     * Get error messages
     *
     * @access public
     * @return mixed Error messages
     */
    public function getErrorMessages()
    {
        return $this->_errorMessages;
    }

    /**
     * Execute validate
     *
     * @param  mixed $data Input data
     * @access public
     * @return boolean Result of validation
     */
    public function isValid($data = null)
    {
        if (!is_null($this->_validatorTranslate)) {
            Zend_Validate_Abstract::setDefaultTranslator($this->_validatorTranslate);
        }

        if (is_null($this->_rules)) {
            // Set validation rules, which is defined in sub class.
            $this->setRules();
        }
        $this->_validator = new Zend_Filter_Input($this->_filters, $this->_rules);

        // If $data is null, use $_request data.
        if (is_null($data)) {
            $data = $this->_request;
        }
        $this->_validator->setData($data);
        $ret = $this->_validator->isValid();
        if ($ret === false) {
            $errors = $this->_validator->getInvalid();
            $messages = array();
            if (is_array($errors)) {
                foreach ($errors as $fields) {
                    foreach ($fields as $key => $val) {
                        $messages[] = $val;
                    }
                }
            }
            $this->_errorMessages = $messages;
        }

        return $ret;
    }
}
