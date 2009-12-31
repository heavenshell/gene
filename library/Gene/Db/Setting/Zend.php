<?php
/**
 * Database setting for Zend_Db
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
 * Gene_Db_Setting_Zend
 *
 * @category  Gene
 * @package   Gene_Db
 * @version   $id$
 * @copyright 2009 Heavens hell
 * @author    Shinya Ohyanagi <sohyanagi@gmail.com>
 * @license   New BSD License
 */
class Gene_Db_Setting_Zend extends Gene_Db_Setting_Abstract
{
    /**
     * Database adapters
     *
     * @var    array
     * @access private
     */
    private $_db = array();

    /**
     * Load config
     *
     * @param  mixed $dbConfig
     * @access public
     * @throws Gene_Db_Exception
     * @return Gene_Db_Setting_Zend Fluent interface
     */
    public function load($config = null)
    {
        if (is_null($config)) {
            $config = $this->_config;
        }

        foreach ($config as $key => $val) {
            if (isset($val['adapter']) && isset($val['params'])) {
                $db = Zend_Db::factory($val['adapter'], $val['params']);
                $db->setFetchMode(Zend_Db::FETCH_ASSOC);
                $this->_db[$key] = $db;
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
     * @return mixed Imprements of Zend_Db_Adapter_Abstract
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

        Zend_Db_Table_Abstract::setDefaultAdapter($db);

        return $db;
    }
}
