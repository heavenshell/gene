<?php
/**
 * Session service
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
 * @package   Gene_Services
 * @version   $id$
 * @copyright 2009-2010 Shinya Ohyanagi
 * @author    Shinya Ohyanagi <sohyanagi@gmail.com>
 * @license   New BSD License
 */

/**
 * Base class of session
 *
 * @category  Gene
 * @package   Gene_Services
 * @version   $id$
 * @copyright 2009-2010 Shinya Ohyanagi
 * @author    Shinya Ohyanagi <sohyanagi@@gmail.com>
 * @license   New BSD License
 */
class Gene_Services_Session extends Gene_Services_Model
{
    /**
     * Session namespace
     *
     * @var    mixed
     * @access protected
     */
    protected $_namespace = null;

    /**
     * Get namespace
     *
     * @param  mixed $namespace
     * @access public
     * @return mixed Session namespace
     */
    public function getNamespace()
    {
        return $this->_namespace;
    }

    /**
     * Set namespace
     *
     * @param  mixed $namespace Session namespace
     * @access public
     * @return Gene_Services_Session Fluent interface
     */
    public function setNamespace($namespace)
    {
        $this->_namespace = $namespace;
        return $this;
    }

    /**
     * Get value from session
     *
     * @param  mixed $key Session key name
     * @param  mixed $namespace Session namespace
     * @access public
     * @return mixed Session value or null
     */
    public function get($key, $namespace = null)
    {
        if (is_null($namespace)) {
            $namespace = $this->_namespace;
        }

        $session = new Zend_Session_Namespace($namespace);
        if (isset($session->{$key})) {
            return $session->{$key};
        }

        return null;
    }

    /**
     * Set session
     *
     * @param  mixed $key Session key
     * @param  mixed $value Session value
     * @param  mixed $namespace Session namespace
     * @access public
     * @return Gene_Services_Session Fluent interface
     */
    public function set($key, $value, $namespace = null)
    {
        if (is_null($namespace)) {
            $namespace = $this->_namespace;
        }

        $session = new Zend_Session_Namespace($namespace);
        $session->{$key} = $value;

        return $this;
    }

    /**
     * Get all session
     *
     * @param  mixed $namespace Session namespace
     * @access public
     * @return Zend_Session_Namespace Session object
     */
    public function getAll($namespace = null)
    {
        if (is_null($namespace)) {
            $namespace = $this->_namespace;
        }

        return new Zend_Session_Namespace($namespace);
    }

    /**
     * Remove session
     *
     * @param  mixed $namespace Session namespace
     * @access public
     * @return Gene_Services_Session Fluent interface
     */
    public function remove($namespace = null)
    {
        if (is_null($namespace)) {
            $namespace = $this->_namespace;
        }

        Zend_Session::namespaceUnset($namespace);
        return $this;
    }
}
