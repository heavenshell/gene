<?php
/**
 * Routes setting
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
 * @package   Gene_Application
 * @version   $id$
 * @copyright 2009-2010 Shinya Ohyanagi
 * @author    Shinya Ohyanagi <sohyanagi@gmail.com>
 * @license   New BSD License
 */

/**
 * Routes
 *
 * @category  Gene
 * @package   Gene_Application
 * @version   $id$
 * @copyright 2009-2010 Shinya Ohyanagi
 * @author    Shinya Ohyanagi <sohyanagi@gmail.com>
 * @license   New BSD License
 */
class Gene_Application_Setting_Routing extends Gene_Application_Setting_Abstract
{
    /**
     * Config
     *
     * @var    Zend_Config
     * @access protected
     */
    protected $_config = null;

    /**
     * Router
     *
     * @var    Zend_Controller_Router_Rewrite
     * @access private
     */
    private $_router = null;

    /**
     * Config
     *
     * @param  Zend_Config $config Config object
     * @access public
     * @return Gene_Application_Setting_Routing
     */
    public function setConfig($config)
    {
        if (!$config instanceof Zend_Config) {
            throw new Gene_Application_Setting_Exception('Config should be an instanceof Zend_Config.');
        }
        $this->_config = $config;
        return $this;
    }

    /**
     * Get router
     *
     * @access public
     * @return void
     */
    public function getRouter()
    {
        return $this->_router;
    }

    /**
     * Add router
     *
     * @param  Zend_Controller_Router_Rewrite $router
     * @param  mixed $config Config
     * @access public
     * @return Gene_Application_Setting_Routing Fluent interface
     */
    public function add(Zend_Controller_Router_Rewrite $router, $config = null)
    {
        if (is_null($config)) {
            $config = $this->_config;
        }

        if (is_null($config)) {
            return;
        }

        $router->addConfig($config);
        $this->_router = $router;

        return $this;
    }
}
