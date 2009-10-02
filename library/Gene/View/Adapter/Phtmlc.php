<?php
/**
 * Gene_View_Adapter_Phtmlc
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
 * @package   Gene_View
 * @version   $id$
 * @copyright 2009 Shinya Ohyanagi
 * @author    Shinya Ohyanagi <sohyanagi@gmail.com>
 * @license   New BSD License
 */

/**
 * Create Phtmlc View object
 *
 * @package   Gene
 * @package   Gene_View
 * @version   $id$
 * @copyright 2009 Shinya Ohyanagi
 * @author    Shinya Ohyanagi <sohyanagi@gmail.com>
 * @license   New BSD License
 */
class Gene_View_Adapter_Phtmlc extends Gene_View_Adapter_Abstract
{
    /**
     * Get view instance
     *
     * @access public
     * @return Revulo_View_Phtmlc
     */
    public function getView()
    {
        if (!isset($this->_config)) {
            throw new Gene_View_Exception('Configs does not set.');
        }

        if (!isset($this->_config['className'])) {
            throw new Gene_View_Exception('Invaild class name.');
        }

        $compileFragments = false;
        if (isset($this->_config['compileFragments'])) {
            if (strtolower($this->_config['compileFragments']) === 'on') {
                $compileFragments = true;
            }
        }

        $encode = 'UTF-8';
        if (isset($this->_config['encoding'])) {
            $encode = $this->_config['encoding'];
        }

        // Check directory exists.
        $path = null;
        if (isset($this->_config['compilePath'])) {
            $path = $this->_config['compilePath'];
        }
        if (!is_dir($path)) {
            if (is_null($path)) {
                throw new Gene_View_Exception('compilePath not found.');
            }
            mkdir($path, 0777);
        }

        // Check compilepath directory access right
        if (!is_writable($path)) {
            throw new Gene_View_Exception('compilePath does not have access rights.');
        }

        if (!class_exists($this->_config['className'])) {
            Zend_Loader::loadClass($this->_config['className']);
        }

        $class = $this->_config['className'];
        $view  = new $class();
        $view->setCompilePath($path)
             ->compileFragments($compileFragments)
             ->setEncoding($encode);

        return $view;
    }
}
