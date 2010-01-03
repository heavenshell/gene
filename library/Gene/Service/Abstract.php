<?php
/**
 * Base class of service
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
 * @package   Gene_Service
 * @version   $id$
 * @copyright 2009-2010 Shinya Ohyanagi
 * @author    Shinya Ohyanagi <sohyanagi@gmail.com>
 * @license   New BSD License
 */

/**
 * Base class of service
 *
 * @category  Gene
 * @package   Gene_Service
 * @version   $id$
 * @copyright 2009-2010 Shinya Ohyanagi
 * @author    Shinya Ohyanagi <sohyanagi@gmail.com>
 * @license   New BSD License
 */
abstract class Gene_Service_Abstract implements Gene_Service_Interface
{
    /**
     * Instance of model class
     *
     * @var    array
     * @access private
     */
    protected $_instances = array();

    /**
     * Args
     *
     * @var    mixed
     * @access protected
     */
    protected $_args = null;

    /**
     * App path
     *
     * @var    mixed
     * @access protected
     */
    protected $_appPath = null;

    /**
     * Translate
     *
     * @var    mixed
     * @access protected
     */
    protected $_translate = array();

    /**
     * Path to translates
     *
     * @var mixed
     * @access protected
     */
    protected $_translatePath = null;

    /**
     * setAppPath
     *
     * @param  mixed $path Application path
     * @access public
     * @return void
     */
    public function setAppPath($path)
    {
        $this->_args['appPath'] = $path;
        return $this;
    }

    /**
     * Get application directory path
     *
     * @access public
     * @return mixed Application path
     */
    public function getAppPath()
    {
        return $this->_args['appPath'];
    }

    /**
     * Set translate path
     *
     * @param  mixed $path Path to locale file directory
     * @access public
     * @return void
     */
    public function setTranslatePath($path)
    {
        $this->_translatePath = $path;
        return $this;
    }

    /**
     * Get translate path
     *
     * @param  mixed $value
     * @access public
     * @return mixed Locale directory path
     */
    public function getTranslatePath()
    {
        if (is_null($this->_translatePath)) {
            if (!is_null($this->getAppPath())) {
                $path = rtrim($this->getAppPath(), '\//') . DIRECTORY_SEPARATOR
                    . 'locales' . DIRECTORY_SEPARATOR;

                return $path;
            }
            return null;
        }

        return $this->_translatePath;
    }

    /**
     * Get cache object
     *
     * @param  mixed $path Path to master files
     * @param  string $name Cache object name
     * @access public
     * @return Zend_Cache Cache object
     */
    public function getCacheFileObject($path, $name = 'translates')
    {
        $instance = Gene_Cache_File::getInstance($this->getAppPath());
        $frontend = array(
            'master_files' => $instance->directorySearch($path)
        );

        $cache = $instance->setFrontend($frontend)->getCache($name);
        return $cache;
    }

    /**
     * Get translate object
     *
     * @param  mixed $value Transelate file name
     * @param  string $type Transelate
     * @access public
     * @return Zend_Translate transelates
     */
    public function getTranslate($value, $type = 'ini', $env = null)
    {
        if (array_key_exists($value, $this->_translate)) {
            return $this->_translate[$value];
        }

        $locale = new Zend_Locale();
        $lang   = $locale->getLanguage();
        $path   = $this->getTranslatePath() . $lang
                . DIRECTORY_SEPARATOR . $value;

        /**
         * Todo:Use lamda function when PHP5.3 run.
         * <code>
         *   $createPath = function() use ($path, $type) {
         *       $info = pathinfo($path)
         *       if (!isset($info['extension'])) {
         *           return $path .= '.' . $type;
         *       }
         *   };
         *   $path = $createPath($path, $type);
         * </code>
         */
        $info = pathinfo($path);
        if (!isset($info['extension'])) {
            $path .= '.' . $type;
        }

        if (!file_exists($path)) {
            $iterator = new DirectoryIterator($this->getTranslatePath());
            foreach ($iterator as $val) {
                if (!$val->isDot() && $val->getFilename() !== $lang) {
                    $locale = new Zend_Locale($val->getFilename());
                    $lang   = $locale->getLanguage();
                    $path   = $this->getTranslatePath() . $lang
                            . DIRECTORY_SEPARATOR . $value;

                    $info = pathinfo($path);
                    if (!isset($info['extension'])) {
                        $path .= '.' . $type;
                    }

                    break;
                }
            }
        }

        $cache = $this->getCacheFileObject($this->getTranslatePath() . $lang);

        /**
         * Create cache is same as Zend_Translate::setCache($cache);
         */
        $cacheId = pathinfo(str_replace('/', '_', $value), PATHINFO_FILENAME);
        if (!$translate = $cache->load($cacheId)) {
            $translate = new Zend_Translate($type, $path, $locale);
            $cache->save($translate);
        }

        $this->_translate[$value] = $translate;

        return $translate;
    }

    /**
     * Constructor
     *
     * @param  array $args Module name and path to model
     * @access public
     * @return void
     */
    public function __construct($args = null)
    {
        $this->_args = $args;
        $this->init();
    }

    /**
     * Initialize
     *
     * @access public
     * @return void
     */
    public function init()
    {
    }
}
