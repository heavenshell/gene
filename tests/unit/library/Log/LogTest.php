<?php
/**
 * Gene_Log
 *
 * PHP version 5.2
 *
 * Copyright (c) 2009 Shinya Ohyanagi, All rights reserved.
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
 * @package   Gene_Log
 * @version   $id$
 * @copyright 2009 Shinya Ohyanagi
 * @author    Shinya Ohyanagi <sohyanagi@gmail.com>
 * @license   New BSD License
 */

/**
 * @see prepare
 */
require_once dirname(dirname(dirname(__FILE__))) . '/prepare.php';

/**
 * Specs for Gene_Log
 *
 * @category  Gene
 * @package   Gene_Log
 * @version   $id$
 * @copyright 2009 Shinya Ohyanagi
 * @author    Shinya Ohyanagi <sohyanagi@gmail.com>
 * @license   New BSD License
 */
class Gene_Logの動作Test extends PHPUnit_Framework_TestCase
{
    public function testLogインスタンスを生成できる()
    {
        $instance = new Gene_Log();
        $this->assertTrue($instance instanceof Gene_Log);
    }

    public function test設定したパスにログファイルが生成される()
    {
        $appPath  = dirname(__file__);
        $logPath = $appPath . '/var/log/debug.log';
        $config  = array(
            'file' => array(
                'default' => array(
                    'path'   => $appPath . '/var/log/',
                    'name'   => 'debug',
                    'suffix' => '.log'
                )
            )
        );

        $instance = new Gene_Log($config);
        $this->assertTrue($instance instanceof Gene_Log);
        $instance->createLogger();

        $this->assertFileExists($logPath);
        if (file_exists($logPath) && is_file($logPath)) {
            unlink($logPath);
        }
    }

    public function test設定ファイルに設定したパスにログが生成される()
    {
        $appPath  = dirname(__file__);
        $config   = new Zend_Config_Ini($appPath . '/var/config/log.ini');
        $instance = new Gene_Log($config->production->toArray());
        $logger   = $instance->createLogger();
        $logPath  = $appPath . '/var/log/debug.log';
        $this->assertFileExists($logPath);
        if (file_exists($logPath) && is_file($logPath)) {
            unlink($logPath);
        }
    }

    public function testzend_config_ini形式の設定ファイルを設定できる()
    {
        $appPath  = dirname(__file__);
        $config   = new Zend_Config_Ini($appPath . '/var/config/log.ini');
        $instance = new Gene_Log();
        $logger   = $instance->setConfig($config->production)->createLogger();
        $logPath  = $appPath . '/var/log/debug.log';
        $this->assertFileExists($logPath);
        if (file_exists($logPath) && is_file($logPath)) {
            unlink($logPath);
        }
    }

    public function test配列で設定ファイルを設定できる()
    {
        $appPath  = dirname(__file__);
        $config   = new Zend_Config_Ini($appPath . '/var/config/log.ini');
        $instance = new Gene_Log();
        $logger   = $instance->setConfig($config->production->toArray())
                             ->createLogger();
        $logPath  = $appPath . '/var/log/debug.log';
        $this->assertFileExists($logPath);
        if (file_exists($logPath) && is_file($logPath)) {
            unlink($logPath);
        }
    }

    public function testLoggerを複数生成できる()
    {
        $appPath  = dirname(__file__);
        $config   = new Zend_Config_Ini($appPath . '/var/config/log.ini');
        $instance = new Gene_Log();
        $logger   = $instance->setConfig($config->production->toArray())
                             ->createLogger();
        $logPath  = $appPath . '/var/log/debug.log';
        $this->assertFileExists($logPath);

        $logPath2 = $appPath . '/var/log/other.log';
        $logger2  = $instance->createLogger('other');
        $this->assertFileExists($logPath2);

        if (file_exists($logPath) && is_file($logPath)) {
            unlink($logPath);
        }
        if (file_exists($logPath2) && is_file($logPath2)) {
            unlink($logPath2);
        }
    }

    public function test存在しない名前空間のLoggerを生成した場合例外が発生する()
    {
        $appPath  = dirname(__file__);
        $config   = new Zend_Config_Ini($appPath . '/var/config/log.ini');
        $instance = new Gene_Log();
        $message  = null;
        try {
            $logger = $instance->setConfig($config->production->toArray())
                               ->createLogger('NotExists');
        } catch (Gene_Log_Exception $e) {
            $message = $e->getMessage();
            $this->assertEquals($message, 'Config not found.');
            return;
        }
        $this->fail('Exception does not occured.');
    }

    public function test存在しないパスを設定した場合例外が発生する()
    {
        $appPath  = dirname(__file__);
        $config  = array(
            'file' => array(
                'default' => array(
                    'path'   => $appPath . '/var/log/notfound/',
                    'name'   => 'debug',
                    'suffix' => '.log'
                )
            )
        );

        $instance = new Gene_Log();
        $message  = null;
        try {
            $logger = $instance->setConfig($config)
                               ->createLogger();
        } catch (Gene_Log_Exception $e) {
            $message = $e->getMessage();
            $this->assertEquals($message, 'Log file path invalid.');
            return;
        }
        $this->fail('Exception does not occured.');
    }

    public function testファイルにログを書き込める()
    {
        $appPath  = dirname(__file__);
        $config   = new Zend_Config_Ini($appPath . '/var/config/log.ini');
        $instance = new Gene_Log();
        $logger   = $instance->setConfig($config->production->toArray())
                             ->createLogger();

        $instance->write('This is a test.');

        $logPath  = $appPath . '/var/log/debug.log';
        $this->assertFileExists($logPath);

        $logfile = file_get_contents($appPath . '/var/log/debug.log');
        $this->assertRegExp("/[INFO (6): This is a test.]/", $logfile);

        if (file_exists($logPath) && is_file($logPath)) {
            unlink($logPath);
        }
    }

    public function test設定したデータベースにログを書き込める()
    {
        $appPath  = dirname(__file__);
        $obj      = Db_Fixture::load($appPath . '/var/config/database.json');
        $pdo      = $obj->getConnection();
        $file     = file_get_contents($appPath . '/testdata/create.sql');
        $stmt     = $pdo->prepare($file);
        $ret      = $stmt->execute();
        $stmt     = null;

        $config   = new Zend_Config_Ini($appPath . '/var/config/log.ini');
        $instance = new Gene_Log($config->production->toArray());
        $logger   = $instance->createLogger('default', 'db');
        $logger->info('This is a test.');

        $stmt = $pdo->prepare('SELECT * FROM gene_log_test');
        $ret  = $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stmt = null;
        foreach ($rows as $row) {
            $this->assertEquals($row['priority'], Zend_Log::INFO);
            $this->assertEquals($row['message'], 'This is a test.');
        }
        $obj->after();
    }
}
