<?php
/**
 * Gene_Application_Setting_Plugin
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
 * @package   Gene_Application
 * @version   $id$
 * @copyright 2009-2010 Shinya Ohyanagi
 * @author    Shinya Ohyanagi <sohyanagi@gmail.com>
 * @license   New BSD License
 */

/**
 * @see prepare
 */
require_once dirname(dirname(dirname(dirname(__FILE__)))) . '/prepare.php';

/**
 * Specs for Gene_Application_Setting_Plugin
 *
 * @category  Gene
 * @package   Gene_Application
 * @version   $id$
 * @copyright 2009-2010 Shinya Ohyanagi
 * @author    Shinya Ohyanagi <sohyanagi@gmail.com>
 * @license   New BSD License
 */
class Gene_Application_Setting_Pluginの動作Test extends PHPUnit_Framework_TestCase
{
    public function testインスタンスを生成できる()
    {
        $plugin = new Gene_Application_Setting_Plugin();
        $this->assertTrue($plugin instanceof Gene_Application_Setting_Plugin);
    }

    public function testインスタンス生成時にconfigを設定できる()
    {
        $iniPath = GENE_TEST_ROOT . '/var/config/plugin.ini';
        $config  = Gene_Config::load($iniPath);
        $plugin  = new Gene_Application_Setting_Plugin($config);
        $this->assertTrue($plugin instanceof Gene_Application_Setting_Plugin);
    }

    public function test生成したオブジェクトにconfigを設定できる()
    {
        $iniPath = GENE_TEST_ROOT . '/var/config/plugin.ini';
        $config  = Gene_Config::load($iniPath);
        $plugin  = new Gene_Application_Setting_Plugin();
        $plugin->setConfig($config);
        $this->assertTrue($plugin instanceof Gene_Application_Setting_Plugin);
    }

    public function testinclude_pathにpluginのパスが無く設定ファイルにパスが無い場合例外が発生する()
    {
        $iniPath = GENE_TEST_ROOT . '/var/config/plugin.ini';
        $config  = Gene_Config::load($iniPath);
        $plugin  = new Gene_Application_Setting_Plugin($config);
        try {
            $plugins = $plugin->load()->getPlugin();
        } catch (Exception $e) {
            $this->assertRegExp("/include\(plugins\/Test\/Fuga.php\): failed to open stream: No such file or directory/i", $e->getMessage());
            return;
        }
        $this->fail('Exception does not occured.');
    }

    public function testinclude_pathにpluginのパスがある場合pluginを取得できる()
    {
        $path        = dirname(__FILE__) . '/var/';
        $includePath = get_include_path() . PATH_SEPARATOR . $path;
        set_include_path($includePath);

        $iniPath = GENE_TEST_ROOT . '/var/config/plugin.ini';
        $config  = Gene_Config::load($iniPath);
        $plugin  = new Gene_Application_Setting_Plugin($config);
        $plugins = $plugin->load()->getPlugin();
        $this->assertTrue($plugins['fuga'] instanceof Plugins_Test_Fuga);
    }

    public function test設定ファイルにパスがある場合pluginを取得できる()
    {
        $iniPath = GENE_TEST_ROOT . '/var/config/plugin.ini';
        $config  = Gene_Config::load($iniPath);
        $plugin  = new Gene_Application_Setting_Plugin($config);
        $plugins = $plugin->load()->getPlugin();
        $this->assertTrue($plugins['hoge'] instanceof Plugins_Test_Hoge);
    }

    public function test設定ファイルにpluginのクラス名のみで取得できる()
    {
        $iniPath = GENE_TEST_ROOT . '/var/config/plugin.ini';
        $config  = Gene_Config::load($iniPath);
        $plugin  = new Gene_Application_Setting_Plugin($config);
        $plugins = $plugin->load()->getPlugin();
        $this->assertTrue($plugins['fuga'] instanceof Plugins_Test_Fuga);
    }

    public function testpluginのプリフィックスと名前で得できる()
    {
        $iniPath = GENE_TEST_ROOT . '/var/config/plugin.ini';
        $config  = Gene_Config::load($iniPath);
        $plugin  = new Gene_Application_Setting_Plugin($config);
        $plugins = $plugin->load()->getPlugin();
        $this->assertTrue($plugins['hoge'] instanceof Plugins_Test_Hoge);
    }

    public function testplugin生成時に引数を設定できる()
    {
        $iniPath = GENE_TEST_ROOT . '/var/config/plugin.ini';
        $config  = Gene_Config::load($iniPath);
        $plugin  = new Gene_Application_Setting_Plugin($config);
        $plugins = $plugin->load()->getPlugin();
        $args    = $plugins['bar']->getArgs();
        $this->assertEquals($args, $config->plugin->bar->args);
    }

    public function testplugin生成時に複数の引数を設定できる()
    {
        $iniPath = GENE_TEST_ROOT . '/var/config/plugin.ini';
        $config  = Gene_Config::load($iniPath);
        $plugin  = new Gene_Application_Setting_Plugin($config);
        $plugins = $plugin->load()->getPlugin();
        $args    = $plugins['foo']->getArgs();
        $this->assertEquals($args, $config->plugin->foo->args->toArray());
    }
}
