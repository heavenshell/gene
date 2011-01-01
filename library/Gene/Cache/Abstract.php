<?php
/**
 * Cache
 *
 * PHP version 5.2
 *
 * Copyright (c) 2009-2011 Shinya Ohyanagi, All rights reserved.
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
 * @package   Gene_Cache
 * @version   $id$
 * @copyright 2009-2011 Shinya Ohyanagi
 * @author    Shinya Ohyanagi <sohyanagi@gmail.com>
 * @license   New BSD License
 */

/**
 * Cache
 *
 * @category  Gene
 * @package   Gene_Cache
 * @version   $id$
 * @copyright 2009-2011 Shinya Ohyanagi
 * @author    Shinya Ohyanagi <sohyanagi@gmail.com>
 * @license   New BSD License
 */
abstract class Gene_Cache_Abstract
{
    /**
     * Cache options
     *
     * @var    array
     * @access private
     */
    protected $_options = array(
        'frontend' => array(
            'automatic_serialization' => true,
            'lifetime' => null
        ),
        'backend' => array(
            'cache_dir' => null
        )
    );

    /**
     * Get instance
     *
     * @param  mixed $appPath
  
     * @access public
     * @return Gene_Cache Fluent interface
     */
    public static function getInstance()
    {
        static $obj;
        if ($obj instanceof Gene_Cache_Abstract) {
            return $obj;
        }

        return new self();
    }

    /**
     * Constructor
     *
     * @param  mixed $appPath App path
     * @access private
     * @return void
     */
    private function __construct($param = null)
    {
    }

    /**
     * setOptions
     *
     * @param mixed $value
     * @access public
     * @return void
     */
    public function setOptions($value)
    {
        $options = null;
        if ($value instanceof Zend_Config) {
            $options = $value->toArray();
        } else if (is_array($value)) {
            $options = $value;
        } else {
            throw new Gene_Cache_Exception();
        }

        if (isset($options['frontend'])) {
            $this->setFrontend($options['frontend']);
        }

        if (isset($options['backend'])) {
            $this->setBackend($options['backend']);
        }

        return $this;
    }

    /**
     * Get frontend
     *
     * @access public
     * @return mixed Frontend options
     */
    public function getFrontend()
    {
        if (isset($this->_options['frontend'])) {
            return $this->_options['frontend'];
        }

        return null;
    }

    /**
     * Set frontend options
     *
     * @param  array $value Frontend options
     * @access public
     * @return Gene_Cache Fluent interface
     */
    public function setFrontend(array $value)
    {
        $this->_options['frontend'] = array_merge(
            $this->_options['frontend'],
            $value
        );

        return $this;
    }

    /**
     * Get backend options
     *
     * @access public
     * @return mixed Backend options
     */
    public function getBackend()
    {
        if (isset($this->_options['backend'])) {
            return $this->_options['backend'];
        }

        return null;
    }

    /**
     * Set backend options
     *
     * @param  array $value Backend options
     * @access public
     * @return Gene_Cache Fluent interface
     */
    public function setBackend(array $value)
    {
        $this->_options['backend'] = array_merge(
            $this->_options['backend'],
            $value
        );
        return $this;
    }
}
