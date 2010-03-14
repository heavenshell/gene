<?php
/**
 * Base class of bootstrap
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
 * @package   Base
 * @version   $id$
 * @copyright 2009-2010 Shinya Ohyanagi
 * @author    Shinya Ohyanagi <sohyanagi@gmail.com>
 * @license   New BSD License
 */

/**
 * Base class of bootstrap
 *
 * @category  Gene
 * @package   Base
 * @version   $id$
 * @copyright 2009-2010 Shinya Ohyanagi
 * @author    Shinya Ohyanagi <sohyanagi@gmail.com>
 * @license   New BSD License
 */
class Gene_Base
{
    /**
     * Gene Version
     */
    const GENE_VERSION = '0.3';

    /**
     * Loaded class's instance
     */
    protected static $_instance = null;

    /**
     * Application path
     */
    protected static $_appPath = null;

    /**
     * Params
     */
    protected static $_params = null;

    /**
     * Get params
     *
     * @param  mixed $key Key name
     * @access public
     * @return mixed
     */
    public static function getParams($key = null)
    {
        if (isset(self::$_params[$key])) {
            return self::$_params[$key];
        }

        return self::$_params;
    }

    /**
     * Load class
     *
     * @param  mixed $className Class name
     * @param  array $args Args
     * @access public
     * @return mixed Instance of $className
     */
    public static function load($className, array $args = array())
    {
        if (isset(self::$_instance[$className])) {
            return self::$_instance[$className];
        }

        if (count($args) > 0) {
            if (!isset($args['appPath'])) {
                $args['appPath'] = self::$_appPath;
            }
            // Create instance
            $reflection = new ReflectionClass($className);
            $instance   = $reflection->newInstanceArgs(array($args));
        } else {
            $args['appPath'] = self::$_appPath;
            $instance = new $className($args);
        }

        self::$_instance[$className] = $instance;

        return $instance;
    }

    /**
     * Setup application
     *
     * @param  mixed $appPath Path to application root
     * @param  array $options Options to setup application
     * @access public
     * @return Zend_Application Application
     */
    public static function app($appPath, array $options = array())
    {
        defined('GENE_APP_PATH') || define('GENE_APP_PATH', $appPath);
        defined('GENE_LIB_PATH') || define('GENE_LIB_PATH', dirname(__FILE__));

        self::$_appPath = GENE_APP_PATH;

        if (!isset($options['ini'])) {
            $options['ini'] = rtrim(GENE_APP_PATH, '\\/') . '/config/app.ini';
        }
        if (!isset($options['env'])) {
            $options['env'] = 'production';
        }

        require_once 'Zend/Application.php';
        $app = new Zend_Application(
            $options['env'],
            $options['ini']
        );

        $autoloader = $app->getAutoloader();
        $autoloader->setFallbackAutoloader(true)
                   ->suppressNotFoundWarnings(false);

        $app->getBootstrap()->setAppPath($appPath);
        if (isset($options['config'])) {
            $app->setConfigPath($options['config']);
        }

        $resources = null;
        if (isset($options['resources'])) {
            $resources = $options['resources'];
        }
        $bootstrap     = $app->getBootstrap()->bootstrap($resources);
        $params        = $bootstrap->getParams();
        self::$_params = $params;

        return $app;
    }

    /**
     * Run application
     *
     * @param  mixed $appPath Path to application root
     * @param  array $options Options to setup application
     * @access public
     * @return void
     */
    public static function run($appPath, array $options = array())
    {
        return self::app($appPath, $options)->run();
    }
}
