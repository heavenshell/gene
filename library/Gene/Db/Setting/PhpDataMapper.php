<?php
/**
 * Database setting for phpDataMapper
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
 * Gene_Db_Setting_PhpDataMapper
 *
 * @category  Gene
 * @package   Gene_Db
 * @version   $id$
 * @copyright 2009-2010 Shinya Ohyanagi
 * @author    Shinya Ohyanagi <sohyanagi@gmail.com>
 * @license   New BSD License
 */
class Gene_Db_Setting_PhpDataMapper extends Gene_Db_Setting_Abstract
{
    /**
     * Database adapters
     *
     * @var    array
     * @access private
     */
    private $_db = array();

    /**
     * init
     *
     * @access public
     * @return Gene_Db_Setting_Doctrine Fluent interface
     */
    public function init()
    {
        require_once 'phpDataMapper/Database/Adapter/Mysql.php';
    }

    /**
     * Set config
     *
     * @param  mixed $config Database settings
     * @access public
     * @throws Gene_Db_Exception $config is not instanceof Zend_Config or array
     * @return Gene_Db_Setting_Abstract Fluent interface
     */
    public function setConfig($config)
    {
        if ($config instanceof Zend_Config) {
            $this->_config = $config->database->toArray();
        } else if (is_array($config)) {
            $this->_config = $config['database'];
        } else {
            throw new Gene_Db_Exception('Config setting is invalid.');
        }

        return $this;
    }

    /**
     * Load config
     *
     * @param  mixed $config Database config
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
            if (isset($val['params'])) {
                $this->_db[$key] = new phpDataMapper_Database_Adapter_Mysql(
                    $val['params']['host'],
                    $val['params']['dbname'],
                    $val['params']['username'],
                    $val['params']['password']
                );
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
     * @throws Gene_Db_Exception Could not load database adapter
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
