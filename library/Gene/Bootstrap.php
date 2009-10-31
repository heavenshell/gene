<?php
/**
 * Bootstrap
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
 * @package   Gene_Bootstrap
 * @version   $id$
 * @copyright 2009 Shinya Ohyanagi
 * @author    Shinya Ohyanagi <sohyanagi@gmail.com>
 * @license   New BSD License
 */

/**
 * Gene_Bootstrap
 *
 * @category  Gene
 * @package   Gene_Bootstrap
 * @version   $id$
 * @copyright 2009 Shinya Ohyanagi
 * @author    Shinya Ohyanagi <sohyanagi@gmail.com>
 * @license   New BSD License
 */
class Gene_Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
    /**
     * Application path
     *
     * @var    mixed
     * @access private
     */
    private $_appPath = null;

    /**
     * Path to config direcotry
     *
     * @var    mixed
     * @access private
     */
    private $_configPath = null;

    /**
     * Cache for config file
     *
     * @var    mixed
     * @access private
     */
    private $_cache = null;

    /**
     * Params
     *
     * @var mixed
     * @access private
     */
    private $_params = null;

    /**
     * Retrieve action controller|services instantiation parameters
     *
     * @param  mixed $key Key name
     * @access public
     * @return mixed
     */
    public function getParams($key = null)
    {
        if (isset($this->_params[$key])) {
            return $this->_params[$key];
        }

        return $this->_params;
    }

    /**
     * Set param
     *
     * @param  mixed $key Key name
     * @param  mixed $value Value
     * @access public
     * @return Gene_Bootstrap Fluent interface
     */
    public function setParam($key, $value)
    {
        $this->_params[$key] = $value;
        return $this;
    }

    /**
     * Set application path
     *
     * @param  mixed $value
     * @access public
     * @return Gene_Bootstrap Fluent interface
     */
    public function setAppPath($path)
    {
        $this->_appPath = $path;
        return $this;
    }

    /**
     * Set config path
     *
     * @param mixed $path
     * @access public
     * @return void
     */
    public function setConfigPath($path)
    {
        $this->_configPath = $path;
        return $this;
    }

    /**
     * Prepare to create cache object
     *
     * @access protected
     * @return Gene_Bootstrap Fluent interface
     */
    protected function _initCache()
    {
        $instance = Gene_Cache_File::getInstance($this->_appPath);
        $frontend = array(
            'master_files' => $instance->directorySearch()
        );
        $this->_cache = $instance->setFrontend($frontend)->getCache('config');

        return $this;
    }

    /**
     * Init config path
     *
     * @access protected
     * @return Gene_Bootstrap Fluent interface
     */
    protected function _initConfig()
    {
        if ($this->_configPath === null) {
            $config = rtrim($this->_appPath, '\//') . DIRECTORY_SEPARATOR
                    . 'config' . DIRECTORY_SEPARATOR;

            $this->_configPath = $config;
        }

        return $this;
    }

    /**
     * Create logger
     *
     * @access protected
     * @return Gene_Bootstrap Fluent interface
     */
    protected function _initLog()
    {
        // Load config file
        $path   = $this->_configPath . 'log.ini';
        $config = Gene_Config::load($path, $this->_cache);
        if (is_null($config)) {
            return $this;
        }
        $env = $this->getEnvironment();
        if (isset($config->{$env})) {
            $config = $config->{$env};
        }

        // Create logger
        $log    = new Gene_Log($config);
        $logger = $log->createLogger();
        $this->setParam('log', $log);

        return $this;
    }

    /**
     * Init session
     *
     * @access protected
     * @return Gene_Bootstrap Fluent interface
     */
    protected function _initSession()
    {
        // Load config file
        $path   = $this->_configPath . 'session.ini';
        $config = Gene_Config::load($path, $this->_cache);
        $env    = $this->getEnvironment();
        if ($config instanceof Zend_Config) {
            Zend_Session::setOptions($config->{$env}->toArray());
        } else {
            if (is_array($config)) {
                Zend_Session::setOptions($config);
            }
        }

        return $this;
    }

    /**
     * Init autoloading
     *
     * <pre>
     *   $APP_PATH/modules/{*}
     *   $APP_PATH/services/{*}
     *   $APP_PATH/models/{*}
     * </pre>
     *
     * @access protected
     * @return Gene_Bootstrap Fluent interface
     */
    protected function _initPath()
    {
        $path     = $this->_configPath . 'path.ini';
        $config   = Gene_Config::load($path, $this->_cache);
        $instance = new Gene_Application_Setting_Path($config);
        $instance->initDefaultResources($this->_appPath);

        // Get paths such as layout.
        $configs = $config->toArray();
        foreach ($configs as $key => $val) {
            $this->setParam($key, $val);
        }

        return $this;
    }

    /**
     * Init view
     *
     * @access protected
     * @return Gene_Bootstrap Fluent interface
     */
    protected function _initView()
    {
        // Load config file
        $path   = $this->_configPath . 'view.ini';
        $config = Gene_Config::load($path, $this->_cache);
        if (is_null($config)) {
            return $this;
        }
        $view     = new Gene_View_Adapter($config);
        $renderer = new Zend_Controller_Action_Helper_ViewRenderer();
        $view->setViewRenderer($renderer);

        return $this;
    }

    /**
     * Init plugin
     *
     * @access protected
     * @return Gene_Bootstrap Fluent interface
     */
    protected function _initPlugin()
    {
        // Load plugin
        $path   = $this->_configPath . 'plugin.ini';
        $config = Gene_Config::load($path, $this->_cache);
        if (is_null($config)) {
            return $this;
        }
        $plugin = new Gene_Application_Setting_Plugin($config);

        // Register plugin to frontcontroller.
        $front   = Zend_Controller_Front::getInstance();
        $plugins = $plugin->load()->getPlugin();
        foreach ($plugins as $key => $val) {
            $front->registerPlugin($val);
        }

        return $this;
    }

    /**
     * Init routing
     *
     * @access protected
     * @return Gene_Bootstrap Fluent interface
     */
    protected function _initRouting()
    {
        $path   = $this->_configPath . 'routing.ini';
        $config = Gene_Config::load($path, $this->_cache);
        if (is_null($config->routes)) {
            return $this;
        }
        $router = new Gene_Application_Setting_Routing($config->routes);
        $front  = Zend_Controller_Front::getInstance();
        $router->add($front->getRouter());

        return $this;
    }

    /**
     * Init database
     *
     * @access public
     * @return Gene_Bootstrap Fluent interface
     */
    public function _initDb()
    {
        $path   = $this->_configPath . 'database.ini';
        $config = Gene_Config::load($path, $this->_cache);
        if (is_null($config)) {
            return $this;
        }
        $env   = $this->getEnvironment();
        $class = $config->{$env}->setting->className;
        if (!class_exists($class, false)) {
            Zend_Loader::loadClass($class);
        }
        $setting = new $class($config->{$env});
        $adapter = $setting->load();

        $this->setParam('adapter', $adapter);

        return $this;
    }

    /**
     * Init frontcontroller
     *
     * @access public
     * @return Gene_Bootstrap Fluent interface
     */
    public function _initFront()
    {
        $front = Zend_Controller_Front::getInstance();
        $front->setParam('config', $this->getParams());

        return $this;
    }
}
