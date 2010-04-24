<?php
/**
 * Base class of service
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
 * Base class of service
 *
 * @category  Gene
 * @package   Gene_Service
 * @version   $id$
 * @copyright 2009-2010 Shinya Ohyanagi
 * @author    Shinya Ohyanagi <sohyanagi@gmail.com>
 * @license   New BSD License
 */
abstract class Gene_Service_Abstract implements Gene_Service_Interface
{
    /**
     * Instance of model class
     *
     * @var    array
     * @access private
     */
    protected $_instances = array();

    /**
     * Args
     *
     * @var    mixed
     * @access protected
     */
    protected $_args = null;

    /**
     * App path
     *
     * @var    mixed
     * @access protected
     */
    protected $_appPath = null;

    /**
     * Message(s)
     *
     * @var    mixed
     * @access protected
     */
    protected $_messages = null;

    /**
     * Translate
     *
     * @var    mixed
     * @access protected
     */
    protected $_translate = array();

    /**
     * Gene_Translate
     *
     * @var    mixed
     * @access protected
     */
    protected $_translateObject = null;

    /**
     * Path to translates
     *
     * @var mixed
     * @access protected
     */
    protected $_translatePath = null;

    /**
     * setAppPath
     *
     * @param  mixed $path Application path
     * @access public
     * @return void
     */
    public function setAppPath($path)
    {
        $this->_args['appPath'] = $path;
        return $this;
    }

    /**
     * Get application directory path
     *
     * @access public
     * @return mixed Application path
     */
    public function getAppPath()
    {
        return $this->_args['appPath'];
    }

    /**
     * Set message
     *
     * @param  mixed $value
     * @access public
     * @return Gene_Service_Abstract Fluent interface
     */
    public function setMessages($value)
    {
        $this->_messages = $value;
        return $this;
    }

    /**
     * Get message
     *
     * @access public
     * @return mixed Message
     */
    public function getMessages()
    {
        return $this->_messages;
    }

    /**
     * getTranslateObject
     *
     * @param mixed $appPath
     * @access public
     * @return void
     */
    public function getTranslateObject($appPath = null)
    {
        if (!is_null($this->_translateObject)) {
            return $this->_translateObject;
        }
        if (is_null($appPath)) {
            $appPath = $this->getAppPath();
        }

        $translate = new Gene_Translate($appPath);
        $this->_translateObject = $translate;

        return $translate;
    }

    /**
     * Get system validator
     *
     * @param  string $file Validation file
     * @param  mixed $path Path to validation file
     * @access public
     * @return Zend_Translate Transelate object
     */
    public function getSystemTranslate($file = 'Zend_Validate.php', $path = null)
    {
        if (array_key_exists($file, $this->_translate)) {
            return $this->_translate[$file];
        }

        $instance  = $this->getTranslateObject();
        $default   = $instance->getValidateTranslate($file, $path);
        if (is_null($path)) {
            $path = $instance->getTranslatePath();
        }
        $translate = $instance->mergeTranslate($default, $path);

        // Set to property
        $this->_translate[$file] = $translate;

        return $translate;
    }

    /**
     * Get translate object
     *
     * @param  mixed $value Transelate file name
     * @param  string $type Transelate
     * @access public
     * @return Zend_Translate transelates
     */
    public function getTranslate($value, $type = 'ini')
    {
        if (array_key_exists($value, $this->_translate)) {
            return $this->_translate[$value];
        }
        $instance  = $this->getTranslateObject();
        $translate = $instance->getTranslate($value, $type);

        // Set to property
        $this->_translate[$value] = $translate;

        return $translate;
    }

    /**
     * Constructor
     *
     * @param  array $args Module name and path to model
     * @access public
     * @return void
     */
    public function __construct($args = null)
    {
        $this->_args = $args;
        $this->init();
    }

    /**
     * Initialize
     *
     * @access public
     * @return void
     */
    public function init()
    {
    }
}
