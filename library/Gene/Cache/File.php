<?php
/**
 * Gene_Cache_File
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
 * @package   Gene_Cache
 * @version   $id$
 * @copyright 2009-2010 Shinya Ohyanagi
 * @author    Shinya Ohyanagi <sohyanagi@gmail.com>
 * @license   New BSD License
 */

/**
 * File cache
 *
 * @category  Gene
 * @package   Gene_Cache
 * @version   $id$
 * @copyright 2009-2010 Shinya Ohyanagi
 * @author    Shinya Ohyanagi <sohyanagi@gmail.com>
 * @license   New BSD License
 */
class Gene_Cache_File extends Gene_Cache_Abstract
{
    /**
     * Cache object
     *
     * @var    mixed
     * @access private
     */
    private static $_cache = null;

    /**
     * Path to app direcotry
     *
     * @var    mixed
     * @access private
     */
    private $_appPath = null;

    /**
     * Path to cache direcotry
     *
     * @var    mixed
     * @access private
     */
    private $_cachePath = null;

    /**
     * Get instance
     *
     * @param  mixed $appPath
     * @access public
     * @return Gene_Cache Fluent interface
     */
    public static function getInstance($appPath = null)
    {
        static $obj;
        if ($obj instanceof Gene_Cache_Abstract
                && $obj->getAppPath() === $appPath) {
            return $obj;
        }

        if (is_null($appPath)) {
            $obj = new self();
        }

        return new self($appPath);
    }

    /**
     * Constructor
     *
     * @param  mixed $appPath App path
     * @access private
     * @return void
     */
    private function __construct($appPath = null)
    {
        if (!is_null($appPath)) {
            $this->_appPath = rtrim($appPath, '\//') . DIRECTORY_SEPARATOR;
        }
    }

    /**
     * Get app path
     *
     * @access public
     * @return mixed Path to app path
     */
    public function getAppPath()
    {
        return $this->_appPath;
    }

    /**
     * Set app path
     *
     * @param  mixed $path Path to app
     * @access public
     * @return Gene_Cache
     */
    public function setAppPath($path)
    {
        $this->_appPath = rtrim($path, '\//') . DIRECTORY_SEPARATOR;
        return $this;
    }

    /**
     * Get cache path
     *
     * @access public
     * @return mixed Path to cache direcotry
     */
    public function getCachePath()
    {
        if (is_null($this->_cachePath)) {
            if (is_null($this->_appPath)) {
                return null;
            }

            // Default cache path
            $cachesPath = rtrim($this->_appPath, '\//') . DIRECTORY_SEPARATOR
                        . 'var' . DIRECTORY_SEPARATOR
                        . 'cache' . DIRECTORY_SEPARATOR;

            return $cachesPath;
        }

        return $this->_cachePath;
    }

    /**
     * Set cache path
     *
     * @param  mixed $path Path to cache direcotry
     * @access public
     * @return Gene_Cache Fluent interface
     */
    public function setCachePath($path)
    {
        $this->_cachePath = $path;
        return $this;
    }

    /**
     * Get master file
     *
     * @access public
     * @return void
     */
    public function getMasterFiles()
    {
        if (isset($this->_options['frontend']['master_files'])) {
            return $this->_options['frontend']['master_files'];
        }
        return null;
    }

    /**
     * Set path to master files
     *
     * @param  mixed $path Path to master files
     * @access public
     * @return Gene_Cache_File Fluent interface
     */
    public function setMasterFiles($path)
    {
        if (is_string($path)) {
            $path = array($path);
        }

        $this->_options['frontend']['master_files'] = array_merge(
            $this->_options['frontend']['master_files'],
            $path
        );

        return $this;
    }

    /**
     * Search directory for get master files
     *
     * @param  mixed $path Path to config files
     * @param  array $extensions Allow to get file extensions
     * @access public
     * @return mixed Path to master files
     */
    public function directorySearch($path = null, array $extensions = array('ini', 'xml', 'php', 'yaml', 'yml'))
    {
        if (is_null($path)) {
            $path = $this->_appPath . 'config' . DIRECTORY_SEPARATOR;
        }

        $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path));

        $response = null;
        foreach ($iterator as $val) {
            if ($val->isFile()) {
                $name      = $val->getPathname();
                $pathArray = pathinfo($name);
                if (!isset($pathArray['extension'])) {
                    continue;
                }
                $extension = strtolower($pathArray['extension']);
                if (!in_array($extension, $extensions)) {
                    continue;
                }
                $response[] = $name;
            }
        }

        return $response;
    }


    /**
     * Get file cache object
     *
     * @param  mixed $name Name of cache
     * @param  mixed $masterpath Path to master file
     * @access public
     * @return Zend_Cache Fluent interface
     */
    public function getCache($name, $masterpath = null)
    {
        if (is_null($masterpath)) {
            $masterpath = $this->getMasterFiles();
            if (is_null($masterpath)) {
                throw new Gene_Cache_Exception('Master file not found.');
            } else if (is_string($masterpath)) {
                $masterpath = array($masterpath);
            }
        }

        $serial = sha1(serialize($masterpath));
        if (isset(self::$_cache[$name][$serial])
                && !is_null(self::$_cache[$name][$serial])) {
            return self::$_cache[$name][$serial];
        }

        $cachesPath = rtrim($this->getCachePath(), '\//') . DIRECTORY_SEPARATOR
                    . $name . DIRECTORY_SEPARATOR;

        // If cache direcotry not exists create it.
        if (!file_exists($cachesPath) && !is_dir($cachesPath)) {
            mkdir($cachesPath, 0777, true);
        }

        // Get frontend options
        $front = $this->getFrontend();

        // Add master file, such as config file.
        $front['master_files'] = $masterpath;

        // Get backend options
        $back = $this->getBackend();
        // Add cache direcotry path for output cache file.
        $back['cache_dir'] = $cachesPath;

        $cache = Zend_Cache::factory('File', 'File', $front, $back);

        // Add to instance property.
        self::$_cache[$name][$serial] = $cache;

        return $cache;
    }
}
