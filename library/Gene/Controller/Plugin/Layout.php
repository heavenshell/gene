<?php
/**
 * Layout plugin
 *
 * PHP version 5.2
 *
 * Copyright (c) 2009 Heavens hell, All rights reserved.
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
 *   * Neither the name of Heavens hell nor the names of his
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
 * @copyright 2009 Heavens hell
 * @author    Heavens hell <heavenshell.jp@gmail.com>
 * @license   New BSD License
 */

/**
 * Layout plugin
 *
 * @category  Gene
 * @package   Gene_Controller
 * @version   $id$
 * @copyright 2009 Heavens hell
 * @author    Heavens hell <heavenshell.jp@gmail.com>
 * @license   New BSD License
 */
class Gene_Controller_Plugin_Layout extends Zend_Controller_Plugin_Abstract
{
    /**
     * Buffering layout file path
     *
     * @var    array
     * @access private
     */
    private $_layoutPath = array();

    /**
     * Set layout
     *
     * @param  Zend_Controller_Request_Abstract $request Request parameters
     * @access public
     * @return Gene_Controller_Plugin_Layout Fluent interface
     */
    public function preDispatch(Zend_Controller_Request_Abstract $request)
    {
        $front  = Zend_Controller_Front::getInstance();
        $config = $front->getParam('config');

        if (!isset($config['layouts'])) {
            return;
        }

        $plugin = $front->getPlugin('Zend_Layout_Controller_Plugin_Layout');
        if ($plugin === false) {
            return;
        }
        $layout = $plugin->getLayout();

        // If request is ajax, disable layout.
        if ($request->isXmlHttpRequest() === true) {
            $layout->disableLayout();
            return $this;
        }

        // Get module name. controller name.
        $module     = $request->getModuleName();
        $controller = $request->getControllerName();

        if (isset($this->_layoutPath[$module][$controller])) {
            $file = $this->_layoutPath[$module][$controller];
        } else {
            // Get layout file from path.ini.
            if (isset($config['layouts'][$module][$controller])
                    && is_file($config['layouts'][$module][$controller])) {
                $file = $config['layouts'][$module][$controller];
            } else if (isset($config['layouts'][$module])) {
                if (is_array($config['layouts'][$module])) {
                    $file = $config['layouts'][$module][0];
                } else {
                    $file = $config['layouts'][$module];
                }
            } else {
                //$file = reset($config['layouts']);
                $layoutPath = rtrim($config['rootDir'], '\//')
                            . DIRECTORY_SEPARATOR . 'app'
                            . DIRECTORY_SEPARATOR . 'layouts'
                            . DIRECTORY_SEPARATOR . $module . '.'
                            . $layout->getViewSuffix();

                $file = $layoutPath;
            }
        }

        $this->_layoutPath[$module][$controller] = $file;

        $pathArray = pathinfo($file);
        $layout->setLayoutPath($pathArray['dirname'])
               ->setLayout($pathArray['filename'])
               ->setViewSuffix($pathArray['extension']);

        return $this;
    }
}
