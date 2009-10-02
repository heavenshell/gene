<?php
/**
 * Base class of service
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
 * @package   Gene_Services
 * @version   $id$
 * @copyright 2009 Shinya Ohyanagi
 * @author    Shinya Ohyanagi <sohyanagi@gmail.com>
 * @license   New BSD License
 */

/**
 * Base class of service
 *
 * @category  Gene
 * @package   Gene_Services
 * @version   $id$
 * @copyright 2009 Shinya Ohyanagi
 * @author    Shinya Ohyanagi <sohyanagi@gmail.com>
 * @license   New BSD License
 */
abstract class Gene_Services_Abstract
{
    /**
     * Instance of model class
     *
     * @var    array
     * @access private
     */
    protected $_instances = array();

    /**
     * App path
     *
     * @var    mixed
     * @access protected
     */
    protected $_appPath = null;

    /**
     * Model name
     *
     * @var    mixed
     * @access private
     */
    private $_name = null;

    /**
     * Path to model
     *
     * @var    array
     * @access private
     */
    private $_path = array();

    /**
     * Path to cache directory
     *
     * @var    string
     * @access private
     */
    private $_cachePath = null;

    /**
     * Cache object
     *
     * @var    mixed
     * @access private
     */
    private $_cache = null;

    /**
     * Get model name
     *
     * @access public
     * @return string Model name
     */
    public function getName()
    {
        return $this->_name;
    }

    /**
     * Set class name
     *
     * @param  mixed $value
     * @access public
     * @return Gene_Services_Abstract Fluent interface
     */
    public function setName($value)
    {
        $this->_name = $value;
        return $this;
    }

    /**
     * Get path to targets
     *
     * @access public
     * @return array Path to targets
     */
    public function getPath()
    {
        return $this->_path;
    }

    /**
     * Set path to targets
     *
     * @param  mixed $value Path to targets
     * @access public
     * @return Gene_Services_Abstract Fluent interface
     */
    public function setPath($value)
    {
        if (is_array($value)) {
            foreach ($value as $val) {
                $this->setPath($val);
            }
        } else {
            if (!in_array($value, $this->_path)) {
                $this->_path[] = $value;
            }
        }
        return $this;
    }

    /**
     * Get cache directory path
     *
     * @access public
     * @return void
     */
    public function getCachePath()
    {
        return $this->_cachePath;
    }

    /**
     * Set cache directory path
     *
     * @param  mixed $value
     * @access public
     * @return Gene_Services_Abstract Fluent interface
     */
    public function setCachePath($value)
    {
        $this->_cachePath = $value;
        return $this;
    }

    /**
     * Get module config ini
     *
     * @param  mixed $name
     * @access public
     * @return Zend_Config_Ini Config object
     */
    public function getModuleConfigIni($name, $section = null)
    {
        $modulePath = $this->_appPath . 'modules' . DIRECTORY_SEPARATOR;
        if (!file_exists($modulePath . $name)) {
            throw new Gene_Services_Exception($name . 'not found.');
        }
        $config = new Zend_Config_Ini($modulePath . $name, $section);

        return $config;
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
        if (isset($args['name'])) {
            $this->_name = $args['name'];
        }
        if (isset($args['path'])) {
            $this->setPath($args['path']);
        }
        if (isset($args['appPath'])) {
            $this->_appPath = $args['appPath'];
        }

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
