<?php
/**
 * Application
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
class Gene_Services_App extends Gene_Services_Abstract
{
    /**
     * Requests
     *
     * @var    array
     * @access protected
     */
    protected $_requests = null;

    /**
     * Plugin
     *
     * @var    mixed
     * @access protected
     */
    protected $_plugins = null;

    /**
     * Translate
     *
     * @var    mixed
     * @access protected
     */
    protected $_translate = array();

    /**
     * Messages
     *
     * @var    mixed
     * @access protected
     */
    protected $_messages = null;


    /**
     * Limit
     *
     * @var    mixed
     * @access protected
     */
    protected $_limit = null;

    /**
     * Offset
     *
     * @var    mixed
     * @access protected
     */
    protected $_offset = null;

    /**
     * Set limit
     *
     * @param  mixed $value Limit
     * @access public
     * @return Gene_Services_App Fluent interface
     */
    public function setLimit($value)
    {
        $this->_limit = $value;
        return $this;
    }

    /**
     * Get limit
     *
     * @access public
     * @return mixed Limit
     */
    public function getLimit()
    {
        return $this->_limit;
    }

    /**
     * Set offset
     *
     * @param  mixed $value Offset
     * @access public
     * @return Gene_Services_App Fluent interface
     */
    public function setOffset($value)
    {
        $this->_offset = $value;
        return $this;
    }

    /**
     * Get offset
     *
     * @access public
     * @return mixed offset
     */
    public function getOffset()
    {
        return $this->_offset;
    }

    /**
     * Set limit and offset
     *
     * @param  mixed $limit Limit
     * @param  mixed $offset Offset
     * @access public
     * @return Gene_Services_App Fluent interface
     */
    public function setLimitOffset($limit, $offset)
    {
        $this->setLimit($limit)->setOffset($offset);
        return $this;
    }

    /**
     * Get messages
     *
     * @access public
     * @return mixed Messages
     */
    public function getMessages()
    {
        return $this->_messages;
    }

    /**
     * Set messages
     *
     * @param  mixed $value Messages
     * @access public
     * @return Gene_Services_App Fluent interface
     */
    public function setMessages($value)
    {
        $this->_messages = $value;
        return $this;
    }

    /**
     * setTranslatePath
     *
     * @param  mixed $value
     * @access public
     * @return void
     */
    public function getTranslatePath()
    {
        if (!is_null($this->_appPath)) {
            $path = rtrim($this->_appPath, '\//') . DIRECTORY_SEPARATOR
                  . 'locales' . DIRECTORY_SEPARATOR;

            return $path;
        }

        return null;
    }

    /**
     * Get cache object
     *
     * @param  mixed $path Path to master files
     * @access public
     * @return Zend_Cache Cache object
     */
    public function getCache($path)
    {
        $instance = Gene_Cache_File::getInstance($this->_appPath);
        $frontend = array(
            'master_files' => $instance->directorySearch($path)
        );

        $cache = $instance->setFrontend($frontend)->getCache('translates');
        return $cache;
    }

    /**
     * Set cache object
     *
     * @param  mixed $value Cache object
     * @access public
     * @return Gene_Services_App Fluent interface
     */
    public function setCache($value)
    {
        $this->_cache = $value;
        return $this;
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

        if (!file_exists($path)) {
            $iterator = new DirectoryIterator($this->getTranslatePath());
            foreach ($iterator as $val) {
                if (!$val->isDot() && $val->getFilename() !== $lang) {
                    $locale = new Zend_Locale($val->getFilename());
                    $lang   = $locale->getLanguage();
                    $path   = $this->getTranslatePath() . $lang
                            . DIRECTORY_SEPARATOR . $value;
                    break;
                }
            }
        }

        $cache  = $this->getCache($this->getTranslatePath() . $lang);

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
     * Set requests
     *
     * @param  mixed $value Requests
     * @access public
     * @return Gene_Services_App Fluent interface
     */
    public function setRequests($value)
    {
        $this->_requests = $value;
        return $this;
    }

    /**
     * Set plugins
     *
     * @param  mixed $value Plugin
     * @access public
     * @return Gene_Services_App Fluent interface
     */
    public function setPlugins($value)
    {
        if ($value instanceof Zend_Controller_Plugin_Abstract) {
            $key = get_class($value);
            $this->_plugins[$key] = $value;
            return $this;
        }

        $this->_plugins = $value;
        return $this;
    }

    /**
     * Get paginator
     *
     * @param  mixed $page Page id
     * @param  mixed $count Total count
     * @param  mixed $perPage Perpage
     * @param  mixed $path Path to template
     * @access public
     * @return Zend_Paginator Paginator object
     */
    public function getPaginator($page, $count, $perpage, $path)
    {
        // Create paginator
        $adapter   = new Zend_Paginator_Adapter_Null($count);
        $paginator = new Zend_Paginator($adapter);
        $paginator->setCurrentPageNumber($page)
                  ->setItemCountPerPage($perpage);
        Zend_Paginator::setDefaultScrollingStyle('Sliding');
        Zend_View_Helper_PaginationControl::setDefaultViewPartial($path);

        return $paginator;
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
     * Get offset
     *
     * @param  mixed $page Page number
     * @param  mixed $limit Limit
     * @access protected
     * @return int Offset
     */
    protected function _getOffset($page, $limit = 10)
    {
        if (!is_numeric($page)) {
            return 0;
        }
        $offset = (intval($page) - 1) * $limit;
        return $offset;
    }
}
