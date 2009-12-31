<?php
/**
 * Gene_View_Adapter_Smarty
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
 * Create Smarty View object
 *
 * @package   Gene
 * @package   Gene_View
 * @version   $id$
 * @copyright 2009 Heavens Hell
 * @author    Shinya Ohyanagi <sohyanagi@gmail.com>
 * @license   New BSD License
 */
class Gene_View_Adapter_Smarty extends Gene_View_Adapter_Abstract
{
    /**
     * Get view instance
     *
     * @access public
     * @return Gene_Rune_View
     */
    public function getView()
    {
        if (!isset($this->_config)) {
            throw new Gene_View_Adapter_Exception('Configs does not set.');
        }

        if (!isset($this->_config['className'])) {
            throw new Gene_View_Exception('Invaild class name.');
        }

        // Load class
        if (!class_exists($this->_config['className'])) {
            Zend_Loader::loadClass($this->_config['className']);
        }

        // Cache setting
        if (isset($this->_config['cache']['path'])) {
            $config['cache_dir'] = $this->_config['cache']['path'];
        } else {
            throw new Gene_View_Exception('Cache path not found.');
        }

        $config['caching'] = 0;
        if (isset($this->_config['cache']['caching'])) {
            if ($this->_config['cache']['caching'] === 1
                    || $this->_config['cache']['caching'] === 2) {
                $config['caching'] = $this->_config['cache']['caching'];
            }
        }

        $config['cache_lifetime'] = 3600;
        if (isset($this->_config['cache']['lifetime'])) {
            $config['cache_lifetime'] = intval($this->_config['cache']['lifetime']);
        }

        // Template path
        $config['template_dir'] = null;
        if (isset($this->_config['template']['path'])) {
            $config['template_dir'] = $this->_config['template']['path'];
        }

        // Compile
        $config['compile_dir'] = null;
        if (isset($this->_config['compile']['path'])) {
            $config['compile_dir'] = $this->_config['compile']['path'];
        }

        $config['force_compile'] = false;
        if (isset($this->_config['compile']['force'])) {
            if (isset($this->_config['compile']['force'])) {
                if (strtolower($this->_config['compile']['force']) === 'true') {
                    $config['force_compile'] = true;
                }
            }
        }

        // Config
        $config['config_dir'] = null;
        if (isset($this->_config['config']['path'])) {
            $config['config_dir'] = $this->_config['config']['path'];
        }

        // Debug
        $config['debugging'] = false;
        if (isset($this->_config['debug']['enabled'])) {
            if (isset($this->_config['debug']['enabled'])) {
                if (strtolower($this->_config['debug']['enabled']) === 'on') {
                    $config['debugging'] = true;
                }
            }
        }

        // Plugin directory
        $config['plugin_dir'] = array();
        if (isset($this->_config['plugin']['path'])) {
            $plugins = array();
            if (is_array($this->_config['plugin']['path'])) {
                foreach ($this->_config['plugin']['path'] as $key => $val) {
                    if (in_array($val, $config['plugin_dir'])) {
                        continue;
                    }
                    $config['plugin_dir'][] = $val;
                }
            }
        }

        // Delimter
        $config['left_delimiter'] = '{';
        if (isset($this->_config['delimiter']['left'])) {
            $config['left_delimiter'] = $this->_config['delimiter']['left'];
        }
        $config['right_delimiter'] = '}';
        if (isset($this->_config['delimiter']['right'])) {
            $config['right_delimiter'] = $this->_config['delimiter']['right'];
        }

        // Encode
        $encode = 'UTF-8';
        if (isset($this->_config['encoding'])) {
            $encode = $this->_config['encoding'];
        }

        $class = $this->_config['className'];
        $view  = new $class($config);
        $view->setEncoding($encode);

        return $view;
    }
}
