<?php
/**
 * Database setting for Doctrine
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
 * @package   Gene_Db
 * @version   $id$
 * @copyright 2009-2010 Shinya Ohyanagi
 * @author    Shinya Ohyanagi <sohyanagi@gmail.com>
 * @license   New BSD License
 */

/**
 * Gene_Db_Setting_Doctrine
 *
 * @category  Gene
 * @package   Gene_Db
 * @version   $id$
 * @copyright 2009-2010 Shinya Ohyanagi
 * @author    Shinya Ohyanagi <sohyanagi@gmail.com>
 * @license   New BSD License
 */
class Gene_Db_Setting_Doctrine extends Gene_Db_Setting_Abstract
{
    /**
     * Database adapters
     *
     * @var    array
     * @access private
     */
    private $_db = array();

    /**
     * Doctrine setting
     *
     * @var    array
     * @access private
     */
    private $_doctrine = array();

    /**
     * Get options
     *
     * @param  mixed $key Property name
     * @access public
     * @return mixed
     */
    public function getOptions($key = null)
    {
        if (is_null($key)) {
            return array($this->_config, $this->_doctrine);
        }

        $property = '_' . ltrim($key, '_');
        if (property_exists($this, $property)) {
            return $this->{$property};
        }

        return null;
    }

    /**
     * init
     *
     * @access public
     * @return Gene_Db_Setting_Doctrine Fluent interface
     */
    public function init()
    {
        require_once 'Doctrine.php';
        $autoloader = Zend_Loader_Autoloader::getInstance();
        $autoloader->pushAutoloader(array('Doctrine', 'autoload'));
        spl_autoload_register(array('Doctrine', 'modelsAutoload'));

        $attributes = array(
            Doctrine_Core::ATTR_MODEL_LOADING           => Doctrine_Core::MODEL_LOADING_CONSERVATIVE,
            Doctrine_Core::ATTR_VALIDATE                => Doctrine_Core::VALIDATE_ALL,
            Doctrine_Core::ATTR_USE_DQL_CALLBACKS       => true,
            Doctrine_Core::ATTR_AUTO_ACCESSOR_OVERRIDE  => true,
            Doctrine_Core::ATTR_AUTO_FREE_QUERY_OBJECTS => true
        );
        $this->setAttribute($attributes);


        return $this;
    }

    /**
     * Set attribute to Doctrine
     *
     * @param  array $params
     * @access public
     * @return Gene_Db_Setting_Doctrine Fluent interface
     */
    public function setAttribute(array $params)
    {
        $manager = Doctrine_Manager::getInstance();
        foreach ($params as $key => $val) {
            $manager->setAttribute(
                $key,
                $val
            );
        }
        return $this;
    }

    /**
     * Set config
     *
     * @param  mixed $config Database settings
     * @access public
     * @return Gene_Db_Setting_Abstract Fluent interface
     */
    public function setConfig($config)
    {
        if ($config instanceof Zend_Config) {
            $this->_config   = $config->database->toArray();
            $this->_doctrine = $config->doctrine->toArray();
        } else if (is_array($config)) {
            $this->_config   = $config['database'];
            $this->_doctrine = $config['doctrine'];
        } else {
            throw new Gene_Db_Exception('Config setting is invalid.');
        }

        return $this;
    }

    /**
     * Load config
     *
     * @param  mixed $dbConfig
     * @access public
     * @throws Gene_Db_Exception
     * @return Gene_Db_Setting_Doctrine Fluent interface
     */
    public function load($config = null)
    {
        if (is_null($config)) {
            $config = $this->_config;
        }

        foreach ($config as $key => $val) {
            if (isset($val['adapter']) && isset($val['params'])) {
                $params = $val['params'];
                $dsn    = sprintf(
                    '%s://%s:%s@%s/%s',
                    strtolower($val['adapter']),
                    $params['username'],
                    $params['password'],
                    $params['host'],
                    $params['dbname']
                );

                $this->_db[$key] = Doctrine_Manager::connection($dsn, $params['dbname']);
            } else {
                throw new Gene_Db_Exception('Config setting is invalid.');
            }
        }

        $this->getDbAdapter();

        return $this;
    }

    /**
     * Get database adapter
     *
     * @param  mixed $key Database name
     * @access public
     * @return mixed Imprements of Doctrine_Db_Adapter_Abstract
     */
    public function getDbAdapter($key = null)
    {
        if (is_null($key)) {
            $db = is_array($this->_db) ? reset($this->_db) : null;
        } else {
            if (array_key_exists($key, $this->_db)) {
                $db = $this->_db[$key];
            } else {
                $db = is_array($this->_db) ? reset($this->_db) : null;
            }
        }
        if (is_null($db)) {
            throw new Gene_Db_Exception('Could not load database adapter.');
        }

        return $db;
    }
}
