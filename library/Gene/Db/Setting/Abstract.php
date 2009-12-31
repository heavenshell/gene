<?php
/**
 * Abstract database setting
 *
 * PHP version 5.2
 *
 * Copyright (c) 2009 Shinya Ohyanagi, All rights reserved.
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
 * @package   Gene_Db
 * @version   $id$
 * @copyright 2009 Shinya Ohyanagi
 * @author    Shinya Ohyanagi <sohyanagi@gmail.com>
 * @license   New BSD License
 */

/**
 * Gene_Db_Setting_Abstract
 *
 * @category  Gene
 * @package   Gene_Db
 * @version   $id$
 * @copyright 2009 Shinya Ohyanagi
 * @author    Shinya Ohyanagi <sohyanagi@gmail.com>
 * @license   New BSD License
 */
abstract class Gene_Db_Setting_Abstract
{
    /**
     * Config
     *
     * @var    mixed
     * @access protected
     */
    protected $_config = null;

    /**
     * Constructor
     *
     * @access public
     * @return void
     */
    public function __construct($config = null)
    {
        $this->init();
        if (!is_null($config)) {
            $this->setConfig($config);
        }
    }

    /**
     * init
     *
     * @access public
     * @return void
     */
    public function init()
    {
    }

    /**
     * Set config
     *
     * @param  mixed $config Database settings
     * @access public
     * @return Gene_Db_Setting_Abstract Fluent interface
     */
    public function setConfig($config)
    {
        if ($config instanceof Zend_Config) {
            $this->_config = $config->database->toArray();
        } else if (is_array($config)) {
            $this->_config = $config;
        } else {
            throw new Gene_Db_Exception('Config setting is invalid.');
        }

        return $this;
    }

    /**
     * Load database setting
     *
     * @param  mixed $value
     * @access public
     * @return Gene_Db_Setting_Abstract Fluent interface
     */
    abstract public function load($config = null);
}
