<?php
/**
 * Gene_Service_Model
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
 * @see prepare
 */
require_once dirname(dirname(dirname(__FILE__))) . '/prepare.php';

/**
 * Specs for Gene_Service_Model
 *
 * @category  Gene
 * @package   Gene_Service
 * @version   $id$
 * @copyright 2009-2010 Shinya Ohyanagi
 * @author    Shinya Ohyanagi <sohyanagi@gmail.com>
 * @license   New BSD License
 */
class Gene_Service_Model動作Test extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        require_once 'var/Test/Zend.php';
        $iniPath = GENE_TEST_ROOT . '/var/config/database.ini';
        $file    = GENE_TEST_ROOT . '/var/sql/create.sql';
        Gene_TestHelper::trancate($iniPath, $file, 'production');

        $options = array(
            'env'      => 'testing',
            'resource' => array('Cache', 'Config', 'Path', 'Db')
        );
        $path = GENE_APP_PATH;
        Gene::app($path, $options);
    }

    public function tearDown()
    {
        $adapter = Gene_TestHelper::getDbAdapter();
        $adapter->closeConnection();
    }

    public function testインスタンスを生成できる()
    {
        $instance = new Gene_Service_Model();
        $this->assertTrue($instance instanceof Gene_Service_Model);

        $instance = Gene::load('Gene_Service_Model');
        $this->assertTrue($instance instanceof Gene_Service_Model);
    }

    public function testDbadapterを取得できる()
    {
        $instance = Gene::load('Gene_Service_Model');
        $model    = $instance->getDao('Test_Service_Zend');
        $this->assertTrue($model->getAdapter() instanceof Zend_Db_Adapter_Pdo_Mysql);
    }

    public function testDbadpterを設定できる()
    {
        $adapter  = Gene::getParams('adapter')->getDbAdapter('slave');
        $instance = Gene::load('Gene_Service_Model');
        $instance->setDbAdapter($adapter);
        $this->assertSame($instance->getDbAdapter(), $adapter);
    }

    public function testLimitを設定できる()
    {
        require_once 'var/Test/ModelMock.php';
        $instance = Gene::load('Test_Service_ModelMock');
        $limit    = 10;
        $instance->setLimit($limit);
        $this->assertSame($instance->getLimit(), $limit);
    }

    public function testOffsetを設定できる()
    {
        require_once 'var/Test/ModelMock.php';
        $instance = Gene::load('Test_Service_ModelMock');
        $offset   = 10;
        $instance->setOffset($offset);
        $this->assertSame($instance->getOffset(), $offset);
    }

    public function testZend_Dbのモデルインスタンスを取得できる()
    {
        $instance = Gene::load('Gene_Service_Model');
        $model    = $instance->getDao('Test_Service_Zend');
        $this->assertTrue($model instanceof Test_Service_Zend);
    }

    public function testDoctrineのモデルインスタンスを取得できる()
    {
        $iniPath = GENE_TEST_ROOT . '/var/config/doctrine.ini';
        $config  = Gene_Config::load($iniPath);
        $db      = new Gene_Db_Setting_Doctrine($config->production);
        $adapter = $db->load()->getDbAdapter();

        require_once 'var/Test/Doctrine.php';
        $instance = Gene::load('Gene_Service_Model');
        $model    = $instance->setDbAdapter($adapter)->getDao('Test_Service_Doctrine');
        $this->assertTrue($model instanceof Test_Service_Doctrine);
    }

    public function test呼び出したモデルをプロパティにキャッシュする()
    {
        require_once 'var/Test/ModelMock.php';
        $instance = Gene::load('Test_Service_ModelMock');
        $model    = $instance->getDao('Test_Service_Zend');
        $object   = $instance->getInstances();
        $this->assertSame($model, $object['Test_Service_Zend']);
    }
}
