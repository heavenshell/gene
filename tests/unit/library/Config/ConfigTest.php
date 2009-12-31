<?php
/**
 * Gene_Config
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
 * @package   Gene_Config
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
 * Specs for Gene_Config
 *
 * @category  Gene
 * @package   Gene_Config
 * @version   $id$
 * @copyright 2009-2010 Shinya Ohyanagi
 * @author    Shinya Ohyanagi <sohyanagi@gmail.com>
 * @license   New BSD License
 */
class Gene_Configの動作Test extends PHPUnit_Framework_TestCase
{
    public function test設定ファイルを読み込める()
    {
        $appPath = dirname(__FILE__) . '/var/config/test.ini';
        $config  = Gene_Config::load($appPath);

        $this->assertTrue($config instanceof Zend_Config_Ini);
    }

    public function testキャッシュを設定しファイルを読み込める()
    {
        $appPath   = dirname(__FILE__);
        $cachePath = $appPath . '/var/cache/';
        $instance  = Gene_Cache_File::getInstance($appPath . '/');
        $options   = array(
            'frontend' => array(
                'master_files' => $appPath . '/var/config/test.ini'
            ),
            'backend' => array(
                'cache_dir' => $cachePath
            )
        );

        $cache  = $instance->setOptions($options)->getCache('test');
        $config = Gene_Config::load($appPath . '/var/config/test.ini', $cache);

        $this->assertTrue($config instanceof Zend_Config_Ini);
        $cache->clean(Zend_Cache::CLEANING_MODE_ALL);
    }

    public function testキャッシュがない場合はキャッシュを生成する()
    {
        $appPath    = dirname(__FILE__);
        $cachePath  = $appPath . '/var/cache/';
        $configPath = $appPath . '/var/config/';
        $instance   = Gene_Cache_File::getInstance($appPath . '/');
        $options    = array(
            'frontend' => array(
                'master_files' => $appPath . '/var/config/test.ini'
            ),
            'backend' => array(
                'cache_dir' => $cachePath
            )
        );

        $cache  = $instance->setOptions($options)->getCache('test');
        $cache->clean(Zend_Cache::CLEANING_MODE_ALL);
        $config = Gene_Config::load($appPath . '/var/config/test.ini', $cache);

        $this->assertTrue($config instanceof Zend_Config_Ini);


        $path  = pathinfo(
            str_replace('/', '_', $configPath . 'test'),
            PATHINFO_FILENAME
        );
        $files = array(
            'zend_cache---' . $path,
            'zend_cache---internal-metadatas---' . $path
        );


        $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($cachePath . 'test'));
        $actual = array();
        foreach ($iterator as $val) {
            if ($val->isFile()) {
                $actual[] = $val->getFilename();
            }
        }
        sort($files);
        sort($actual);

        $this->assertEquals($actual, $files);
        $cache->clean(Zend_Cache::CLEANING_MODE_ALL);
    }
}
