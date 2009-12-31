<?php
/**
 * Gene_Application_Setting_Layout
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
 * Specs for Gene_Application_Setting_Layout
 *
 * @category  Gene
 * @package   Gene_Application
 * @version   $id$
 * @copyright 2009-2010 Shinya Ohyanagi
 * @author    Shinya Ohyanagi <sohyanagi@gmail.com>
 * @license   New BSD License
 */
class Gene_Application_Setting_Layoutの動作Test extends PHPUnit_Framework_TestCase
{
    public function testインスタンスを生成できる()
    {
        $instance = new Gene_Application_Setting_Layout();
        $this->assertTrue($instance instanceof Gene_Application_Setting_Layout);
    }

    public function testインスタンス生成時にconfigを設定できる()
    {
        $appPath  = GENE_TEST_ROOT . '/var/config/layout.ini';
        $config   = Gene_Config::load($appPath);
        $instance = new Gene_Application_Setting_Layout($config);
        $this->assertTrue($instance instanceof Gene_Application_Setting_Layout);
    }

    public function test生成したオブジェクトにconfigを設定できる()
    {
        $appPath  = GENE_TEST_ROOT . '/var/config/layout.ini';
        $config   = Gene_Config::load($appPath);
        $instance = new Gene_Application_Setting_Layout();
        $instance->setConfig($config);
        $this->assertTrue($instance instanceof Gene_Application_Setting_Layout);
    }

    public function testLayoutパス取得の際にConfigが設定されてない場合例外が発生する()
    {
        try {
            $instance = new Gene_Application_Setting_Layout();
            $path     = $instance->getPath();
        } catch (Gene_Application_Setting_Exception $e) {
            $this->assertEquals($e->getMessage(), 'Invalid config.');
            return;
        }
        $this->fail('Exception does not occured.');
    }

    public function testLayoutパスを取得できる()
    {
        $appPath  = GENE_TEST_ROOT . '/var/config/layout.ini';
        $config   = Gene_Config::load($appPath);
        $instance = new Gene_Application_Setting_Layout($config);
        $path     = $instance->getPath();
        $expect   = array(
            'index' => dirname(__FILE__) . '/var/index/default.phtml',
            'admin' => dirname(__FILE__) . '/var/admin/default.phtml'
        );

        $this->assertEquals($path, $expect);
    }
}
