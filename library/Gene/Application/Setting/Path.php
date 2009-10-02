<?php
/**
 * Path setting
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
 * Path setting
 *
 * @category  Gene
 * @package   Gene_Application
 * @version   $id$
 * @copyright 2009 Shinya Ohyanagi
 * @author    Shinya Ohyanagi <sohyanagi@gmail.com>
 * @license   New BSD License
 */
class Gene_Application_Setting_Path extends Gene_Application_Setting_Abstract
{
    /**
     * Modules autoloader
     *
     * @var    mixed
     * @access private
     */
    private $_loader = null;

    /**
     * Get path setting from config
     *
     * @access public
     * @throws Gene_Application_Setting_Exception
     * @return mixed Path setting
     */
    public function getLayoutPath()
    {
        if (is_null($this->_config)) {
            throw new Gene_Application_Setting_Exception('Invalid config.');
        }

        if (!isset($this->_config['layouts'])) {
            return null;
        }

        $config = $this->_config['layouts'];
        $path   = null;
        foreach ($config as $key => $val) {
            $path[$key] = $val;
        }

        return $path;
    }

    /**
     * Load path setting from config file
     *
     * @access public
     * @return Gene_Application_Setting_Path Fluent interface
     */
    public function loadFromConfig()
    {
        if (is_null($this->_config)) {
            throw new Gene_Application_Setting_Exception('Invalid config.');
        }

        if (!isset($this->_config['resources'])) {
            return null;
        }

        $config = $this->_config['resources'];
        $params = array();
        foreach ($config as $key => $val) {
            if (!isset($val['path'])
                    || !isset($val['namespace'])
                    || !isset($val['basename'])
                    || !isset($val['basePath'])) {
                continue;
            }

            $namespace = isset($val['namespace'])
                       ? $val['namespace']
                       : '';

            $basename = isset($val['basename'])
                      ? $val['basename']
                      : '';

            $name   = strtolower($namespace);
            $loader = $this->getModuleLoader($basename);
            if (is_null($loader)) {
                $params = array(
                    'namespace' => $basename,
                    'basePath'  => $val['basePath']
                );

                $this->setResoucesToAutoloader($params);
            }
            $resources = array(
                $name => array(
                    'namespace' => $namespace,
                    'path'      => $val['path']
                )
            );

            $this->addResource(strtolower($basename), $resources);
        }

        return $this;
    }

    /**
     * Autoload default resouces
     *
     * @param  mixed $path Path to app
     * @access public
     * @throws Gene_Application_Setting_Exception
     * @return Gene_Application_Setting_Path Fluent interface
     */
    public function initDefaultResources($path)
    {
        if (!is_dir($path)) {
            throw new Gene_Application_Setting_Exception('Invalid path name.');
        }

        // APPPATH/modules/{module name}
        $path    = rtrim($path, '\//');
        $modules = $path . DIRECTORY_SEPARATOR . 'modules';
        if (!is_dir($modules)) {
            throw new Gene_Application_Setting_Exception('Module not found.');
        }

        $resources = $this->getModuleResources($modules);

        // Default app's autoloader
        $default = array(
            'namespace' => '',
            'basePath'  => $path
        );

        $resources[] = $default;

        $this->setResoucesToAutoloader($resources);

        return $this;
    }

    /**
     * Get modules from directory
     *
     * @param  mixed $path Path to modules directory
     * @access public
     * @throws Gene_Application_Setting_Exception
     * @return array Modules path
     */
    public function getModuleResources($path)
    {
        if (!is_dir($path)) {
            throw new Gene_Application_Setting_Exception('Invalid path name.');
        }

        $resources = array();
        $iterator  = new DirectoryIterator($path);
        foreach ($iterator as $val) {
            if ($val->isDir() && !$val->isDot()) {
                $name = $val->getPathname();
                $resources[] = array(
                    'namespace' => ucfirst(basename($name)),
                    'basePath'  => $name
                );
            }
        }

        return $resources;
    }

    /**
     * Set resouces to autoloader
     *
     * @param  array $resources Resources
     * @access public
     * @return Gene_Application_Setting_Path Fluent interface
     */
    public function setResoucesToAutoloader(array $resources)
    {
        if (isset($resources['namespace']) && isset($resources['basePath'])) {
            $resources = array($resources);
        }

        foreach ($resources as $val) {
            $loader = new Zend_Loader_Autoloader_Resource($val);
            $loader->addResourceTypes(array(
                'services' => array(
                    'namespace' => 'Services',
                    'path'      => 'services'
                ),
                'models' => array(
                    'namespace' => 'Models',
                    'path'      => 'models'
                )
            ));
            $key = isset($val['namespace'])
                 ? strtolower($val['namespace'])
                 : '';

            $this->_loader[$key] = $loader;
        }

        return $this;
    }

    /**
     * Get modules loader
     *
     * @param  mixed $key Module name
     * @access public
     * @return mixed Module loader
     */
    public function getModuleLoader($key = null)
    {
        if (isset($this->_loader[$key])) {
            return $this->_loader[$key];
        }

        return null;
    }

    /**
     * Get modules loaders
     *
     * @param  mixed $key Module name
     * @access public
     * @return mixed Module loader(s)
     */
    public function getModuleLoaders($key = null)
    {
        if (isset($this->_loader[$key])) {
            return $this->_loader[$key];
        }

        return $this->_loader;
    }

    /**
     * Add resouces to loader
     *
     * @param  mixed $key Module name to add resouces
     * @param  array $value Resources
     * @access public
     * @return Gene_Application_Setting_Path Fluent interface
     */
    public function addResource($key, array $value)
    {
        $loader = $this->getModuleLoader($key);
        $loader->addResourceTypes($value);
        $this->_loader[$key] = $loader;

        return $this;
    }
}
