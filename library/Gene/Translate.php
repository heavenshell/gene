<?php
/**
 * Translate
 *
 * PHP version 5.2
 *
 * Copyright (c) 2009-2011 Shinya Ohyanagi, All rights reserved.
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
 * @package   Gene_Translate
 * @version   $id$
 * @copyright 2009-2011 Shinya Ohyanagi
 * @author    Shinya Ohyanagi <sohyanagi@gmail.com>
 * @license   New BSD License
 */

/**
 * Translate
 *
 * @category  Gene
 * @package   Gene_Translate
 * @version   $id$
 * @copyright 2009-2011 Shinya Ohyanagi
 * @author    Shinya Ohyanagi <sohyanagi@gmail.com>
 * @license   New BSD License
 */
class Gene_Translate
{
    /**
     * Application path
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
     * @var    array
     * @access protected
     */
    protected $_translatePath = array(
        'locales'   => null,
        'resources' => null
    );

    /**
     * Path to cache.
     *
     * @var    mixed
     * @access protected
     */
    protected $_cachePath = null;

    /**
     * Constructor.
     *
     * @access public
     * @return void
     */
    public function __construct($appPath = null, $cachePath = null)
    {
        $this->_appPath   = $appPath;
        $this->_cachePath = $cachePath;
    }

    /**
     * Set application directory path.
     *
     * @param  mixed $path Application path
     * @access public
     * @return Gene_Translate Fluent interface
     */
    public function setAppPath($path)
    {
        $this->_appPath = $path;
        return $this;
    }

    /**
     * Get application directory path.
     *
     * @access public
     * @return mixed Application path
     */
    public function getAppPath()
    {
        return $this->_appPath;
    }

    /**
     * Get cache object.
     *
     * @param  mixed $path Path to master files
     * @param  string $name Cache object name
     * @access public
     * @return Zend_Cache Cache object
     */
    public function getCacheFileObject($path, $name = 'translates')
    {
        $cachePath = $this->_cachePath;
        if (is_null($cachePath)) {
            $cachePath = $this->getAppPath();
        }

        $instance  = Gene_Cache_File::getInstance($cachePath);
        $frontend  = array(
            'master_files' => $instance->directorySearch($path)
        );

        $cache = $instance->setFrontend($frontend)->getCache($name);
        return $cache;
    }

    /**
     * Set translate path
     *
     * @param  mixed $path Path to locale file directory
     * @access public
     * @return Gene_Translate Fluent interface
     */
    public function setTranslatePath($path, $key = 'locales')
    {
        $this->_translatePath[$key] = $path;
        return $this;
    }

    /**
     * Get translate path
     *
     * @param  mixed $type
     * @access public
     * @return mixed Locale directory path
     */
    public function getTranslatePath($type = 'locales')
    {
        $path = null;
        if (!isset($this->_translatePath[$type])) {
            $this->_translatePath[$type] = null;
            $appPath = $this->getAppPath();
            if (!is_null($appPath)) {
                $path = rtrim($appPath, '\//') . DIRECTORY_SEPARATOR
                      . $type . DIRECTORY_SEPARATOR;
            }
        }

        if (is_null($this->_translatePath[$type])) {
            return $path;
        }

        return $this->_translatePath[$type];
    }

    /**
     * Get system translate
     *
     * @param  string $value Filename
     * @param  mixed $path Path to resources directory
     * @access public
     * @return Zend_Translate
     */
    public function getValidateTranslate($file = 'Zend_Validate.php', $path = null)
    {
        if (array_key_exists($file, $this->_translate)) {
            return $this->_translate[$file];
        }
        $appPath = $this->getAppPath();
        $locale  = new Zend_Locale();
        $lang    = $locale->getLanguage();
        if (is_null($path)) {
            $path = $appPath . DIRECTORY_SEPARATOR . 'resources'
                  . DIRECTORY_SEPARATOR . 'languages'
                  . DIRECTORY_SEPARATOR . $lang;
        }

        $path      = $this->_getPath($path, $lang, 'resources/languages');
        $scan      = array('scan' => Zend_Translate::LOCALE_DIRECTORY);
        $options   = array(
            'adapter' => 'array',
            'content' => $path,
            'locale'  => $locale,
            'scan'    => $scan,
        );
        $translate = new Zend_Translate($options);
        $this->_translate[$file] = $translate;

        return $translate;
    }

    /**
     * Merge yet antoehr translate file to Zend_Validate.php
     *
     * @param  Zend_Translate $translate
     * @param  mixed $path Merge from
     * @param  string $default Merge to
     * @access public
     * @return Zend_Translate Translate object
     */
    public function mergeTranslate(Zend_Translate $translate, $path, $default = 'Zend_Validate.php')
    {
        $cache = $this->getCacheFileObject($path, 'mergetranslate');
        $cacheId = pathinfo(str_replace('/', '_', $path), PATHINFO_FILENAME);
        if (!$data = $cache->load($cacheId)) {
            $iterator = new DirectoryIterator($path);
            foreach ($iterator as $key => $val) {
                // If another translate file found in same directory,
                // Add to default translate file.
                $file = $val->getFilename();
                if ($val->isFile() && $file !== $default) {
                    $translate->addTranslation($path . DIRECTORY_SEPARATOR . $file);
                }
            }
            $cache->save($translate);
        } else {
            $translate = $data;
        }

        $this->_translate[$default] = $translate;
        return $translate;
    }

    /**
     * Get translate object
     *
     * @param  mixed $value Transelate file name
     * @param  string $type Transelate
     * @access public
     * @return Zend_Translate transelates
     */
    public function getTranslate($value, $type = 'ini')
    {
        if (array_key_exists($value, $this->_translate)) {
            return $this->_translate[$value];
        }

        $path   = $this->getTranslatePath();
        $locale = new Zend_Locale();
        $lang   = $locale->getLanguage();
        $path   = $path . $lang . DIRECTORY_SEPARATOR . $value;
        $info   = pathinfo($path);
        if (!isset($info['extension'])) {
            $path .= '.' . $type;
        }

        $path  = $this->_getPath($path, $lang, 'locales');
        $cache = $this->getCacheFileObject($path);

        /**
         * Zend_Translate::setCache($cache); create cache,
         * but cache Zend_Translate it self.
         */
        $cacheId = pathinfo(str_replace('/', '_', $value), PATHINFO_FILENAME);
        if (!$translate = $cache->load($cacheId)) {
            $options = array(
                'adapter' => $type,
                'content' => $path,
                'locale'  => $locale->getLanguage()
            );
            $translate = new Zend_Translate($options);
            $translate = new Zend_Translate($type, $path, $locale);
            $cache->save($translate);
        }

        $this->_translate[$value] = $translate;

        return $translate;
    }

    /**
     * Get path
     *
     * @param  mixed $path Path to resources
     * @param  mixed $lang Language
     * @param  string $type locale|resources
     * @access private
     * @return mixed Path to direcotry
     */
    private function _getPath($path, $lang, $type = 'locale')
    {
        if (file_exists($path)) {
            return dirname($path);
        } else {
            $translatePath = $this->getTranslatePath($type);
            $iterator = new DirectoryIterator($translatePath);
            foreach ($iterator as $val) {
                // If translate of locale direcotry|file not found,
                // use exsist translate file as default.
                if (!$val->isDot() && $val->getFilename() !== $lang) {
                    $locale = new Zend_Locale($val->getFilename());
                    $lang   = $locale->getLanguage();
                    $path   = $translatePath . $lang . DIRECTORY_SEPARATOR;
                    break;
                }
            }
        }

        return $path;
    }
}
