<?php
/**
 * View adapter
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
 * @package   Gene_View
 * @version   $id$
 * @copyright 2009-2010 Shinya Ohyanagi
 * @author    Shinya Ohyanagi <sohyanagi@gmail.com>
 * @license   New BSD License
 */

/**
 * Gene_View_Adapter
 *
 * @category  Gene
 * @package   Gene_View
 * @version   $id$
 * @copyright 2009-2010 Shinya Ohyanagi
 * @author    Shinya Ohyanagi <sohyanagi@gmail.com>
 * @license   New BSD License
 */
class Gene_View_Adapter
{
    /**
     * View object
     *
     * @var    mixed
     * @access private
     */
    private $_view = null;

    /**
     * Config
     *
     * @var    mixed
     * @access private
     */
    private $_config = null;

    /**
     * Constructor
     *
     * @param  mixed $config Config
     * @access public
     * @return void
     */
    public function __construct($config = null)
    {
        if (!is_null($config)) {
            $this->setConfig($config);
        }
    }

    /**
     * Set config
     *
     * @param  mixed $config
     * @throws Gene_View_Exception Invalid config
     * @access public
     * @return Gene_View_Adapter_Abstract Fluent interface
     */
    public function setConfig($config)
    {
        if ($config instanceof Zend_Config) {
            $this->_config = $config->toArray();
            return $this;
        } else if (is_array($config)) {
            $this->_config = $config;
            return $this;
        }

        throw new Gene_View_Exception('Invaild config.');
    }

    /**
     * Get template options
     *
     * @param  mixed $key Option key name
     * @access public
     * @return mixed Template options
     */
    public function getTemplateOptions($key = null)
    {
        if (isset($this->_config['template'][$key])) {
            return $this->_config['template'][$key];
        }

        if (isset($this->_config['template'])) {
            return $this->_config['template'];
        }

        return $null;
    }

    /**
     * Get view engine
     *
     * @param  mixed $key
     * @access public
     * @return mixed View instance
     */
    public function getEnigne($key)
    {
        if (isset($this->_view[$key])) {
            return $this->_view[$key];
        }

        return null;
    }

    /**
     * Get view
     *
     * @param  mixed $engine
     * @access public
     * @return mixed View instance
     */
    public function getView($engine = null)
    {
        if (is_null($engine)) {
            $engine = $this->getTemplateOptions('engine');
        }

        if (isset($this->_view[$engine])) {
            return $this->_view[$engine];
        }

        $className  = __CLASS__ . '_' . ucfirst($engine);
        $reflection = new ReflectionClass($className);
        $args       = array($this->_config[$engine]);
        $class      = $reflection->newInstanceArgs($args);
        $instance   = $class->getView();

        $this->_view[$engine] = $instance;

        return $instance;
    }

    /**
     * Add helper path to view
     *
     * @param  mixed $helper
     * @access public
     * @return Gene_View_Adapter Fluent interface
     */
    public function addHelperPath()
    {
        $engine = $this->getTemplateOptions('engine');
        $view   = isset($this->_view[$engine]) ? $this->_view[$engine] : null;
        if (is_null($view)) {
            throw new Gene_View_Exception('View engine not found.');
        }

        if (!method_exists($view, 'addHelperPath')) {
            throw new Gene_View_Exception('addHelperPath not found.');
        }

        $helper = $this->_config;
        if ($helper instanceof Zend_Config) {
            $helper = $helper->toArray();
        }

        if (isset($helper['helper']) && is_array($helper['helper'])) {
            foreach ($helper['helper'] as $key => $val) {
                if (isset($val['path']) && isset($val['prefix'])) {
                    $view->addHelperPath($val['path'], $val['prefix']);
                }
            }
        }

        return $this;
    }

    /**
     * Set layout
     *
     * @param  array $config Layout config
     * @access public
     * @return Gene_View_Adapter Fluent interface
     */
    public function setLayout()
    {
        $config = $this->_config;
        if (is_null($config) || !is_array($config)) {
            throw new Gene_View_Exception('config not found.');
        }

        $suffix = $this->getTemplateSuffix();
        if (isset($config['contentKey'])) {
            $contentKey = $config['contentKey'];
        } else {
            $contentKey = 'content';
        }

        $options = array(
            'contentKey' => $contentKey
        );

        if (isset($config['className'])) {
            $className = $config['className'];
            if (!class_exists($className, false)) {
                Zend_Loader::loadClass($className);
            }
            $layout = new $className($options, true);
        } else {
            $layout = new Zend_Layout($options, true);
        }
        $layout->setViewSuffix($suffix);

        return $this;
    }
    /**
     * Get suffix
     *
     * @access public
     * @return mixed Template file suffix
     */
    public function getTemplateSuffix()
    {
        $engine = $this->getTemplateOptions('engine');
        if (isset($this->_config[$engine]['template']['suffix'])) {
            return $this->_config[$engine]['template']['suffix'];
        }

        return null;
    }

    /**
     * Set view to view renderer
     *
     * @param  Zend_Controller_Action_Helper_ViewRenderer $renderer
     * @access public
     * @return Gene_View_Adapter Fluent interface
     */
    public function setViewRenderer(Zend_Controller_Action_Helper_ViewRenderer $renderer)
    {
        $view   = $this->getView();
        $spec   = $this->getTemplateOptions('spec');
        $path   = $this->getTemplateOptions('path');
        $suffix = $this->getTemplateSuffix();

        // Add view and template suffix to view renderer for load template
        // engine automatic.
        $viewRenderer = $renderer->setView($view)->setViewSuffix($suffix);

        // If template path exists in settings, set to renderer for change
        // view scripts path.
        if (!is_null($path)) {
            $viewRenderer->setViewBasePathSpec($path);
        }
        if (!is_null($spec)) {
            $viewRenderer->setViewScriptPathSpec($spec);
        }

        // Add to cotroller helper
        Zend_Controller_Action_HelperBroker::addHelper($viewRenderer);

        $this->addHelperPath()->setLayout();

        return $this;
    }
}
