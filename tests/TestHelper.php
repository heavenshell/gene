<?php
/**
 * Gene_TestHelper
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
 * @package   Gene_TestHelper
 * @version   $id$
 * @copyright 2009-2011 Shinya Ohyanagi
 * @author    Shinya Ohyanagi <sohyanagi@gmail.com>
 * @license   New BSD License
 */

/**
 * Gene_TestHelper
 *
 * @category  Gene
 * @package   Gene_Tests_Helper
 * @version   $id$
 * @copyright 2009-2011 Shinya Ohyanagi
 * @author    Shinya Ohyanagi <sohyanagi@gmail.com>
 * @license   New BSD License
 */
class Gene_TestHelper
{
    /**
     * Db adapter
     */
    private static $_adapter = null;

    /**
     * Get db adapter
     *
     * @access public
     * @return mixed
     */
    public static function getDbAdapter()
    {
        return self::$_adapter;
    }

    /**
     * trancate
     *
     * @param  mixed $ini
     * @param  mixed $sql
     * @param  string $section
     * @param  string $key
     * @access public
     * @return void
     */
    public static function trancate($ini, $sql, $section = 'testing', $key = 'default')
    {
        $config  = Gene_Config::load($ini)->{$section};
        $name    = $config->setting->className;
        if ($name !== 'Gene_Db_Setting_Zend') {
            if ($config->database->default->adapter !== 'Pdo_Mysql') {
                $config = $config->database->toArray();
                $config['default']['adapter'] = 'Pdo_Mysql';
            }
        }
        $db      = new Gene_Db_Setting_Zend($config);
        $adapter = $db->load()->getDbAdapter($key);
        $adapter->getConnection()->exec(file_get_contents($sql));
        self::$_adapter = $adapter;
    }
}
