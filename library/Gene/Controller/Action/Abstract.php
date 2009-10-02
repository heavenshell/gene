<?php
/**
 * Base class of ControllerAction
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
 * @package   Gene_Controller
 * @version   $id$
 * @copyright 2009 Shinya Ohyanagi
 * @author    Shinya Ohyanagi <sohyanagi@gmail.com>
 * @license   New BSD License
 */

/**
 * Base class of ControllerAction
 *
 * @category  Gene
 * @package   Gene_Controller
 * @version   $id$
 * @copyright 2009 Shinya Ohyanagi
 * @author    Shinya Ohyanagi <heavenshell@sohyanagi>
 * @license   New BSD License
 */
abstract class Gene_Controller_Action_Abstract extends Zend_Controller_Action
{
    /**
     * config
     *
     * @var    mixed
     * @access protected
     */
    protected $_configs;

    /**
     * Module name
     *
     * @var    string
     * @access protected
     */
    protected $_module = '';

    /**
     * Controller name
     *
     * @var    string
     * @access private
     */
    protected $_controller = '';

    /**
     * Action name
     *
     * @var    string
     * @access protected
     */
    protected $_action = '';

    /**
     * Path to models & services
     *
     * @var    mixed
     * @access protected
     */
    protected $_modelPaths;

    /**
     * Current request
     *
     * @var    Zend_Controller_Request_Http
     * @access protected
     */
    protected $_request;

    /**
     * Inputs for Zend_Filter_Input
     *
     * @var    mixed
     * @access private
     */
    protected $_inputs = null;

    /**
     * Filters for Zend_Filter_Input
     *
     * @var    mixed
     * @access private
     */
    protected $_filters = null;

    /**
     * Validateion rules
     *
     * @var    array
     * @access private
     */
    protected $_rules = null;

    /**
     * Plugins
     *
     * @var    mixed
     * @access protected
     */
    protected $_plugins = null;

    /**
     * Flag: is display enabled?
     *
     * @var    bool
     * @access protected
     */
    protected $_displayEnabled = true;

    /**
     * Error message
     *
     * @var    array
     * @access protected
     */
    protected $_errors = array();

    /**
     * Alias to assign
     *
     * @param  mixed $name Key to set template
     * @param  mixed $param Value to set template
     * @access public
     * @return Gene_Controller_Action_Abstract Fluent interface
     */
    public function assign($name, $param = null)
    {
        $this->view->assign($name, $param);
        return $this;
    }

    /**
     * Set error message to template
     *
     * @param  mixed $name Error message
     * @access protected
     * @return Gene_Controller_Action_Abstract Fluent interface
     */
    protected function _setError($name)
    {
        if (is_array($name)) {
            $this->_errors = array_merge($this->_errors, $name);
        } else {
            $this->_errors[] = $name;
        }
        $this->_setParam('errors', $this->_errors);
        return $this;
    }

    /**
     * Initialize
     *
     * @access public
     * @return void
     */
    public function init()
    {
        $this->_request    = $this->getRequest();
        $this->_module     = $this->_request->getModuleName();
        $this->_controller = $this->_request->getControllerName();
        $this->_action     = $this->_request->getActionName();

        $frontController   = $this->getFrontController();
        $this->_plugins    = $frontController->getPlugins();
        $this->_configs    = $this->getInvokeArg('config');

        // Initialize view
        $this->_initView();
    }

    /**
     * Initialize view setting
     *
     * @access protected
     * @return Gene_Controller_Action_Abstract Fluent interface
     */
    private function _initView()
    {
        if ($this->view instanceof Zend_View) {
            parent::initView();
        }
        return $this;
    }

    /**
     * Get form params
     *
     * @param  array $names
     * @access protected
     * @return mixed Parameters
     */
    protected function _getFormParams(array $names, $namespace)
    {
        // If request is back from other page(e.g. confirm page to index page),
        // set session value to form
        if ($this->_request->getPost('back') === null) {
            $params = $this->_mergeValue($names, $namespace);
        } else {
            $session = new Zend_Session_Namespace($namespace);
            $params = $session->params;
        }

        return $params;
    }

    /**
     * Merge session value and post value
     *
     * @param  array $names Value to merge
     * @param  mixed $namespace Session namespace
     * @access protected
     * @return array Merged param value
     */
    protected function _mergeValue(array $names, $namespace)
    {
        $session = new Zend_Session_Namespace($namespace);
        $params  = array();
        foreach ($names as $key => $val) {
            $params[$val] = $this->_request->getPost($val);
        }

        if (isset($session->params)) {
            $params = array_merge($session->params, $params);
        }

        return $params;
    }

    /**
     * Set default value
     *
     * @param  array $params Form elements as value
     * @access protected
     * @return Gene_Controller_Action_Abstract Fluent interface
     */
    protected function _setDefaultValues(array $params)
    {
        foreach ($params as $key => $val) {
            $this->assign($key, $val);
        }

        return $this;
    }

    /**
     * Get plugin object
     *
     * @param  string $class Plugin's class name
     * @access protected
     * @return mixed Plugin object
     */
    protected function _getPlugin($class)
    {
        if (!is_array($this->_plugins)) {
            return null;
        }

        foreach ($this->_plugins as $plugin) {
            if ($class === get_class($plugin)) {
                return $plugin;
            }
        }

        return null;
    }

    /**
     * _forward
     *
     * @param  mixed $action Action name
     * @param  string $controller Controller name
     * @param  string $module Module name
     * @param  mixed $params Parameters
     * @access private
     * @return void
     */
    protected function _go($action, $controller = null, $module = null, $params = null)
    {
        if ($this->_request->isXmlHttpRequest()) {
            return;
        }
        parent::_forward($action, $controller, $module, $params);
        return;
    }

    /**
     * Post dispatch
     *
     * @access protected
     * @return void
     */
    public function postDispatch()
    {
        // If error occurred, assign to template.
        if (is_array($this->_getParam('errors'))
                && count($this->_getParam('errors')) > 0) {
            $this->assign('errors', $this->_getParam('errors'));
        }
        // If request has 'message', assign to template.
        $message = $this->_getParam('message');
        if (isset($message) && !is_null($message)) {
            $this->assign('message', $message);
        }
    }
}
