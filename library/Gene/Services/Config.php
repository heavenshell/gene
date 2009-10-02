<?php
/**
 * Config service
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
 * @package   Gene_Services
 * @version   $id$
 * @copyright 2009 Shinya Ohyanagi
 * @author    Shinya Ohyanagi <sohyanagi@gmail.com>
 * @license   New BSD License
 */

/**
 * Base class of config
 *
 * @category  Gene
 * @package   Gene_Services
 * @version   $id$
 * @copyright 2009 Shinya Ohyanagi
 * @author    Shinya Ohyanagi <sohyanagi@@gmail.com>
 * @license   New BSD License
 */
class Gene_Services_Config extends Gene_Services_Abstract
{
    /**
     * Config file
     *
     * @var    array
     * @access protected
     */
    protected $_config = array();

    /**
     * Get config
     *
     * @param  mixed $fileName Config file to read
     * @param  mixed $path Path to config file
     * @access public
     * @throws Gene_Services_Exception
     * @return mixed Config file
     */
    public function getConfig($fileName, $section = null, $options = null, $dir = null)
    {
        if (array_key_exists($fileName, $this->_config)) {
            return $this->_config[$fileName];
        }

        if (!is_null($dir)) {
            $this->setPath($dir);
        }

        $paths = $this->getPath();
        $path  = '';
        if (is_array($paths) && count($paths) > 0) {
            foreach ($paths as $val) {
                $file = rtrim($val, '\//') . DIRECTORY_SEPARATOR . $fileName;
                if (file_exists($file) && is_file($file)) {
                    $path = $file;
                    break;
                }
            }
        } else {
            throw new Gene_Services_Exception('Config file does not set.');
        }

        $config = new Zend_Config_Ini($path, $section, $options);

        // Add to class variable.
        $this->_config[$fileName] = $config;

        return $config;
    }
}
