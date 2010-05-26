<?php
/**
 * Gene_Db
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
 * @see prepare
 */
require_once dirname(dirname(dirname(__FILE__))) . '/prepare.php';

/**
 * Specs for Gene_Db_Setting_PhpDataMapper
 *
 * @category  Gene
 * @package   Gene_Db
 * @version   $id$
 * @copyright 2009-2010 Shinya Ohyanagi
 * @author    Shinya Ohyanagi <sohyanagi@gmail.com>
 * @license   New BSD License
 */
class Gene_Db_Setting_PhpDataMapperの動作Test extends PHPUnit_Framework_TestCase
{
    private $_config = null;

    public function setUp()
    {
        $iniPath       = GENE_TEST_ROOT . '/var/config/phpdatamapper.ini';
        $this->_config = Gene_Config::load($iniPath);
    }

    public function test設定ファイルから読み込んだ接続情報を設定できる()
    {
        $db      = new Gene_Db_Setting_PhpDataMapper($this->_config->production);
        $adapter = $db->load();

        $this->assertTrue($adapter instanceof Gene_Db_Setting_PhpDataMapper);
    }

    public function test設定ファイルから読み込んだ接続情報を配列に変換し設定できる()
    {
        $db = new Gene_Db_Setting_PhpDataMapper($this->_config->production->toArray());
        $this->assertTrue($db->load() instanceof Gene_Db_Setting_PhpDataMapper);
    }

    public function test配列に設定した接続情報を設定できる()
    {
        $config = array(
            'database' => array(
                'default' => array(
                    'adapter' => 'Mysql',
                    'params'  => array(
                        'host'     => 'localhost',
                        'port'     => '3306',
                        'username' => 'gene_test',
                        'password' => 'gene_test',
                        'dbname'   => 'gene_test'
                    )
                )
            )
        );
        $db = new Gene_Db_Setting_PhpDataMapper($config);
        $this->assertTrue($db->load() instanceof Gene_Db_Setting_PhpDataMapper);
    }

    public function test配列とzend_config以外を設定した場合例外が発生する()
    {
        try {
            $db = new Gene_Db_Setting_PhpDataMapper('hoge');
            $db->load();
        } catch (Gene_Db_Exception $e) {
            $this->assertEquals($e->getMessage(), 'Config setting is invalid.');
            return;
        }

        $this->fail('Exception does not occured.');
    }

    public function test接続情報を取得できる()
    {
        $db = new Gene_Db_Setting_PhpDataMapper($this->_config->production);
        $this->assertEquals(
            $db->load()->getDbAdapter()->dsn(),
            sprintf(
                'mysql:host=%s;dbname=%s',
                $this->_config->production->database->default->params->host,
                $this->_config->production->database->default->params->dbname
            )
        );
    }

    public function test複数の接続情報を設定し任意の接続情報を取得できる()
    {
        $db = new Gene_Db_Setting_PhpDataMapper($this->_config->testing);
        $this->assertEquals(
            $db->load()->getDbAdapter('slave')->dsn(),
             sprintf(
                'mysql:host=%s;dbname=%s',
                $this->_config->testing->database->slave->params->host,
                $this->_config->testing->database->slave->params->dbname
            )
        );
    }

    public function testデータベースアダプターを取得できる()
    {
        $db      = new Gene_Db_Setting_PhpDataMapper($this->_config->production);
        $adapter = $db->load()->getDbAdapter();
        $this->assertTrue($db->load()->getDbAdapter() instanceof phpDataMapper_Adapter_Mysql);
    }
}
