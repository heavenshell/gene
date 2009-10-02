<?php
/**
 * Plugin setting
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
 * @package   Gene_Application
 * @version   $id$
 * @copyright 2009 Shinya Ohyanagi
 * @author    Shinya Ohyanagi <sohyanagi@gmail.com>
 * @license   New BSD License
 */

/**
 * Plugin settings
 *
 * @category  Gene
 * @package   Gene_Application
 * @version   $id$
 * @copyright 2009 Shinya Ohyanagi
 * @author    Shinya Ohyanagi <sohyanagi@gmail.com>
 * @license   New BSD License
 */
class Gene_Application_Setting_Plugin extends Gene_Application_Setting_Abstract
{
    /**
     * Plugins
     *
     * @var    mixed
     * @access private
     */
    private $_plugin = null;

    /**
     * Get plugin
     *
     * @access public
     * @return void
     */
    public function getPlugin()
    {
        return $this->_plugin;
    }


    /**
     * Load plugin from config
     *
     * @access public
     * @return Gene_Application_Setting_Plugin Fluent interface
     */
    public function load()
    {
        if (is_null($this->_config)) {
            throw new Gene_Application_Setting_Exception('Invalid config.');
        }

        $plugin = null;
        $config = $this->_config['plugin'];
        foreach ($config as $key => $val) {
            $prefix = '';
            if (isset($val['prefix'])) {
                $prefix = rtrim($val['prefix'], '_') . '_';
            }
            $className = null;
            if (isset($val['name'])) {
                $className = $prefix . $val['name'];
            }

            if (!class_exists($className, false)) {
                $path = isset($val['path']) ? $val['path'] : null;
                Zend_Loader::loadClass($className, $path);
            }
            if (isset($val['args'])) {
                $args = array();
                if (is_array($val['args'])) {
                    $args = $val['args'];
                } else {
                    $args = array($val['args']);
                }
                $reflection = new ReflectionClass($className);
                $class      = $reflection->newInstanceArgs($args);
            } else {
                $class = new $className;
            }

            $plugin[$key] = $class;
        }

        $this->_plugin = $plugin;

        return $this;
    }
}
