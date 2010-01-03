<?php
/**
 * Gene_Service_Abstract
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
 * Specs for Gene_Service_Abstract
 *
 * @category  Gene
 * @package   Gene_Service
 * @version   $id$
 * @copyright 2009-2010 Shinya Ohyanagi
 * @author    Shinya Ohyanagi <sohyanagi@gmail.com>
 * @license   New BSD License
 */
class Gene_Service_Abstract動作Test extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $options = array(
            'env'      => 'testing',
            'resource' => array('Cache', 'Config', 'Path')
        );
        $path = GENE_APP_PATH;
        Gene::app($path, $options);
        require_once dirname(__FILE__) . '/var/Test/AbstractMock.php';
    }

    public function testインスタンスを生成できる()
    {
        $instance = new Test_AbstractMock();
        $this->assertTrue($instance instanceof Gene_Service_Abstract);
    }

    public function testインスタンス生成時にinitメソッドが実行する()
    {
        $instance = new Test_AbstractMock();
        $this->assertTrue($instance->init);
    }

    public function testインスタンス生成時にApplicationPathを設定できる()
    {
        $instance = new Test_AbstractMock(array('appPath' => GENE_APP_PATH));
        $this->assertSame($instance->getAppPath(), GENE_APP_PATH);
    }

    public function testApplicationPathを設定できる()
    {
        $instance = new Test_AbstractMock();
        $instance->setAppPath(GENE_APP_PATH);
        $this->assertSame($instance->getAppPath(), GENE_APP_PATH);
    }

    public function testApplicationPathを設定していない場合nullを応答する()
    {
        $instance = new Test_AbstractMock();
        $this->assertSame($instance->getAppPath(), null);
    }

    public function testTranslatePathを設定できる()
    {
        $path     = GENE_TEST_ROOT . '/var/locales';
        $instance = new Test_AbstractMock();
        $instance->setTranslatePath($path);
        $this->assertSame($instance->getTranslatePath(), $path);
    }

    public function testTranslatePathが設定されていない場合デフォルトのパスを応答する()
    {
        $instance = new Test_AbstractMock();
        $instance->setAppPath(GENE_APP_PATH);

        $path = GENE_APP_PATH . '/locales/';
        $this->assertSame($instance->getTranslatePath(), $path);
    }

    public function testCacheオブジェクトを取得できる()
    {
        $appPath  = GENE_TEST_ROOT . '/var';
        $instance = new Test_AbstractMock();
        $instance->setAppPath($appPath);
        $cache = $instance->getCacheFileObject($appPath);
        $this->assertTrue($cache instanceof Zend_Cache_Frontend_File);
    }

    public function testTranslateオブジェクトを取得できる()
    {
        $path     = GENE_TEST_ROOT . '/var/locales/';
        $appPath  = GENE_TEST_ROOT . '/var';
        $instance = new Test_AbstractMock();
        $instance->setAppPath($appPath)->setTranslatePath($path);
        $translate = $instance->getTranslate('message.ini');
        $this->assertTrue($translate instanceof Zend_Translate);
    }

    public function test拡張子がない場合でもTranslateオブジェクトを取得できる()
    {
        $path     = GENE_TEST_ROOT . '/var/locales/';
        $appPath  = GENE_TEST_ROOT . '/var';
        $instance = new Test_AbstractMock();
        $instance->setAppPath($appPath)->setTranslatePath($path);
        $translate = $instance->getTranslate('message');
        $this->assertTrue($translate instanceof Zend_Translate);
    }
}
