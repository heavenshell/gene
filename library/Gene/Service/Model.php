<?php
/**
 * Model service
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
 * Base class of model service
 *
 * @category  Gene
 * @package   Gene_Service
 * @version   $id$
 * @copyright 2009-2010 Shinya Ohyanagi
 * @author    Shinya Ohyanagi <sohyanagi@gmail.com>
 * @license   New BSD License
 */
class Gene_Service_Model extends Gene_Services_Abstract
{
    /**
     * Instance of model class
     *
     * @var    array
     * @access private
     */
    protected $_instances = array();

    /**
     * Database adapter
     *
     * @var    mixed
     * @access protected
     */
    protected $_dbAdapter = null;

    /**
     * Limit
     *
     * @var mixed
     * @access protected
     */
    protected $_limit = null;

    /**
     * Offset
     *
     * @var mixed
     * @access protected
     */
    protected $_offset = null;

    /**
     * Set limit
     *
     * @param  mixed $value Limit
     * @access public
     * @return Gene_Services_Model Fluent interface
     */
    public function setLimit($value)
    {
        $this->_limit = $value;
        return $this;
    }

    /**
     * Set offset
     *
     * @param  mixed $value
     * @access public
     * @return Gene_Services_Model Fluent interface
     */
    public function setOffset($value)
    {
        $this->_offset = $value;
        return $this;
    }

    /**
     * Set database adapter
     *
     * @param  mixed $value
     * @access public
     * @return Gene_Services_Model
     */
    public function setDbAdapter($value)
    {
        $this->_dbAdapter = $value;
        return $this;
    }

    /**
     * Get database adapter
     *
     * @access public
     * @return mixed Database adapter
     */
    public function getDbAdapter()
    {
        return $this->_dbAdapter;
    }

    /**
     * Get dao
     *
     * @param  mixed $className Model class name
     * @param  mixed $path Path to model
     * @access public
     * @throws Gene_Services_Exception
     * @return Instance of model class
     */
    public function getDao($className, $adapter = 'default')
    {
        if (array_key_exists($className, $this->_instances)) {
            return $this->_instances[$className];
        }

        if (is_null($this->_dbAdapter)) {
            $db = Gene_Base::getParams('adapter');
            // If database adapter is instance of Zend_Db_Adapter_Abstract,
            // add to class variable for using transaction.
            $this->_dbAdapter = $db->getDbAdapter($adapter);
        }

        // Create model object.
        $instance = new $className();

        // Set to class variable.
        $this->_instances[$className] = $instance;

        return $instance;
    }
}
