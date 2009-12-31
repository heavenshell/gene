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
 * @see prepare
 */
require_once dirname(dirname(dirname(__FILE__))) . '/prepare.php';

/**
 * Specs for Gene_Cache_File
 *
 * @category  Gene
 * @package   Gene_Cache
 * @version   $id$
 * @copyright 2009-2010 Shinya Ohyanagi
 * @author    Shinya Ohyanagi <sohyanagi@gmail.com>
 * @license   New BSD License
 */
class Gene_Cache_Fileの動作Test extends PHPUnit_Framework_TestCase
{
    public function testインスタンスを生成できる()
    {
        $instance = Gene_Cache_File::getInstance();
        $this->assertTrue($instance instanceof Gene_Cache_File);
    }

    public function testインスタンスをappPathを設定して生成できる()
    {
        $appPath  = GENE_APP_PATH;
        $instance = Gene_Cache_File::getInstance($appPath);
        $this->assertTrue($instance instanceof Gene_Cache_File);
    }

    public function testインスタンスを生成時にキャッシュする()
    {
        $instance1 = Gene_Cache_File::getInstance();
        $instance2 = Gene_Cache_File::getInstance();
        $this->assertEquals(
            spl_object_hash($instance1),
            spl_object_hash($instance2)
        );
    }

    public function testインスタンス生成時に前回と違うパスを設定した場合新規インスタンスを応答する()
    {
        $instance1 = Gene_Cache_File::getInstance();
        $instance2 = Gene_Cache_File::getInstance(GENE_APP_PATH);
        $this->assertNotEquals(
            spl_object_hash($instance1),
            spl_object_hash($instance2)
        );
    }

    public function testAppPathを取得できる()
    {
        $instance = Gene_Cache_File::getInstance(GENE_APP_PATH);
        $actual   = $instance->getAppPath();
        $this->assertEquals($actual, GENE_APP_PATH . '/');
    }

    public function testAppPathをセットできる()
    {
        $instance = Gene_Cache_File::getInstance();
        $actual   = $instance->setAppPath(GENE_APP_PATH)->getAppPath();
        $this->assertEquals($actual, GENE_APP_PATH . '/');
    }

    public function testCacheのpathを取得できる()
    {
        $appPath  = dirname(__FILE__);
        $instance = Gene_Cache_File::getInstance($appPath);
        $actual   = $instance->getCachePath();
        $this->assertEquals($actual, $appPath . '/var/cache/');
    }

    public function testCacheのpathを設定できる()
    {
        $appPath   = dirname(__FILE__);
        $cachePath = $appPath . '/var/cache/';
        $instance  = Gene_Cache_File::getInstance();
        $actual    = $instance->setCachePath($cachePath)->getCachePath();
        $this->assertEquals($actual, $cachePath);
    }

    public function testCacheのfrontendを取得できる()
    {
        $instance = Gene_Cache_File::getInstance(GENE_APP_PATH);
        $actual   = $instance->getFrontend();
        $this->assertEquals(
            $actual,
            array(
                'automatic_serialization' => true,
                'lifetime' => null
            )
        );
    }

    public function testCacheのfrontendを設定できる()
    {
        $appPath   = dirname(__FILE__);
        $cachePath = $appPath . '/var/cache/';
        $instance  = Gene_Cache_File::getInstance(GENE_APP_PATH);
        $options   = array(
            'master_files' => $appPath . '/var/config/test.ini'
        );
        $actual = $instance->setFrontend($options)->getFrontend();
        $this->assertEquals(
            $actual,
            array(
                'automatic_serialization' => true,
                'lifetime'     => null,
                'master_files' => $appPath . '/var/config/test.ini'
            )
        );
    }

    public function testCacheのbackendを取得できる()
    {
        $instance = Gene_Cache_File::getInstance(GENE_APP_PATH);
        $actual   = $instance->getBackend();
        $this->assertEquals(
            $actual,
            array(
                'cache_dir' => null
            )
        );
    }

    public function testCacheのbackendを設定できる()
    {
        $appPath   = dirname(__FILE__);
        $cachePath = $appPath . '/var/cache/';
        $instance  = Gene_Cache_File::getInstance(GENE_APP_PATH);
        $options   = array(
            'cache_dir' => $cachePath
        );
        $actual = $instance->setBackend($options)->getBackend();
        $this->assertEquals(
            $actual,
            array(
                'cache_dir' => $cachePath
            )
        );
    }

    public function testCacheのfrontendを設定ファイルから設定できる()
    {
        $appPath   = dirname(__FILE__);
        $cachePath = $appPath . '/var/cache/';
        $instance  = Gene_Cache_File::getInstance(GENE_APP_PATH);
        $options   = new Zend_Config_Ini($appPath . '/var/config/test.ini');
        $actual    = $instance->setOptions($options)->getFrontend();
        $this->assertEquals(
            $actual,
            array(
                'automatic_serialization' => true,
                'lifetime'     => 3600,
                'master_files' => $appPath . '/var/config/test.ini'
            )
        );
    }

    public function test複数のmasterfileを設定できる()
    {
        $appPath   = dirname(__FILE__);
        $cachePath = $appPath . '/var/cache/';
        $instance  = Gene_Cache_File::getInstance(GENE_APP_PATH);
        $options   = array(
            'master_files' => array(
                $appPath . '/var/config/test.ini',
                $appPath . '/var/config/test2.ini'
            )
        );
        $actual = $instance->setFrontend($options)->getFrontend();
        sort($actual['master_files']);
        $this->assertEquals(
            $actual,
            array(
                'automatic_serialization' => true,
                'lifetime'     => null,
                'master_files' => array(
                    $appPath . '/var/config/test.ini',
                    $appPath . '/var/config/test2.ini'
                )
            )
        );
    }

    public function test複数のmasterfileを設定ファイルから設定できる()
    {
        $appPath   = dirname(__FILE__);
        $cachePath = $appPath . '/var/cache/';
        $instance  = Gene_Cache_File::getInstance(GENE_APP_PATH);
        $options   = new Zend_Config_Ini($appPath . '/var/config/test2.ini');
        $actual    = $instance->setOptions($options)->getFrontend();
        sort($actual['master_files']);
        $this->assertEquals(
            $actual,
            array(
                'automatic_serialization' => true,
                'lifetime'     => 3600,
                'master_files' => array(
                    $appPath . '/var/config/test.ini',
                    $appPath . '/var/config/test2.ini'
                )
            )
        );
    }

    public function testデフォルトのディレクトリからmasterfileを取得できる()
    {
        $appPath   = dirname(__FILE__);
        $cachePath = $appPath . '/var/cache/';
        $instance  = Gene_Cache_File::getInstance($appPath . '/var/');
        $actual    = $instance->directorySearch();
        $expects   = array(
            $appPath . '/var/config/test.ini',
            $appPath . '/var/config/test.xml',
            $appPath . '/var/config/test2.ini'
        );
        sort($actual);
        sort($expects);
        $this->assertSame($actual, $expects);
    }

    public function test指定したディレクトリから特定の拡張子のmasterfileを取得できる()
    {
        $appPath   = dirname(__FILE__);
        $cachePath = $appPath . '/var/cache/';
        $instance  = Gene_Cache_File::getInstance($appPath . '/');
        $extension = array('xml');
        $actual    = $instance->directorySearch($appPath . '/var/config/', $extension);
        $this->assertEquals(
            $actual,
            array(
                $appPath . '/var/config/test.xml',
            )
        );
    }

    public function testCacheのbackendを設定ファイルから設定できる()
    {
        $appPath   = dirname(__FILE__);
        $cachePath = $appPath . '/var/cache/';
        $instance  = Gene_Cache_File::getInstance(GENE_APP_PATH);
        $options   = new Zend_Config_Ini($appPath . '/var/config/test.ini');
        $actual    = $instance->setOptions($options)->getBackend();
        $this->assertEquals(
            $actual,
            array(
                'cache_dir' => $cachePath
            )
        );
    }

    public function testCacheのfrontendとbackendを一度に設定できる()
    {
        $appPath   = dirname(__FILE__);
        $cachePath = $appPath . '/var/cache/';
        $instance  = Gene_Cache_File::getInstance(GENE_APP_PATH);
        $options   = array(
            'frontend' => array(
                'master_files' => $appPath . '/var/config/test.ini'
            ),
            'backend' => array(
                'cache_dir' => $cachePath
            )
        );
        $actual = $instance->setOptions($options)->getFrontend();
        $this->assertEquals(
            $actual,
            array(
                'automatic_serialization' => true,
                'lifetime'     => null,
                'master_files' => $appPath . '/var/config/test.ini'
            )
        );
        $actual = $instance->getBackend();
        $this->assertEquals(
            $actual,
            array(
                'cache_dir' => $cachePath
            )
        );
    }

    public function testCacheオブジェクトを取得できる()
    {
        $appPath   = dirname(__FILE__);
        $cachePath = $appPath . '/var/cache/';
        $instance  = Gene_Cache_File::getInstance(GENE_APP_PATH);
        $options   = array(
            'frontend' => array(
                'master_files' => $appPath . '/var/config/test.ini'
            ),
            'backend' => array(
                'cache_dir' => $cachePath
            )
        );

        $actual = $instance->setOptions($options)->getCache('test');
        $this->assertTrue($actual instanceof Zend_Cache_Frontend_File);
    }

    public function testCacheオブジェクトに複数のmasterfileを設定できる()
    {
        $appPath   = dirname(__FILE__);
        $cachePath = $appPath . '/var/cache/';
        $instance  = Gene_Cache_File::getInstance(GENE_APP_PATH);
        $options   = array(
            'frontend' => array(
                'master_files' => array(
                    $appPath . '/var/config/test.ini',
                    $appPath . '/var/config/test2.ini'
                )
            ),
            'backend' => array(
                'cache_dir' => $cachePath
            )
        );

        $actual = $instance->setOptions($options)->getCache('test');
        $this->assertTrue($actual instanceof Zend_Cache_Frontend_File);
    }
}
