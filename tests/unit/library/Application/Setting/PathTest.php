<?php
/**
 * Gene_Application_Setting_Path
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
 * @package   Gene_Application
 * @version   $id$
 * @copyright 2009 Shinya Ohyanagi
 * @author    Shinya Ohyanagi <sohyanagi@gmail.com>
 * @license   New BSD License
 */

/**
 * @see prepare
 */
require_once dirname(dirname(dirname(dirname(__FILE__)))) . '/prepare.php';

/**
 * Specs for Gene_Application_Setting_Path
 *
 * @category  Gene
 * @package   Gene_Application
 * @version   $id$
 * @copyright 2009 Shinya Ohyanagi
 * @author    Shinya Ohyanagi <sohyanagi@gmail.com>
 * @license   New BSD License
 */
class Gene_Application_Setting_Pathの動作Test extends PHPUnit_Framework_TestCase
{
    public function testPathインスタンスを生成できる()
    {
        $instance = new Gene_Application_Setting_Path();
        $this->assertTrue($instance instanceof Gene_Application_Setting_Path);
    }

    public function testPathインスタンス生成時にconfigを設定できる()
    {
        $appPath  = GENE_TEST_ROOT . '/var/config/path.ini';
        $config   = Gene_Config::load($appPath);
        $instance = new Gene_Application_Setting_Path($config);
        $this->assertTrue($instance instanceof Gene_Application_Setting_Path);
    }

    public function testPathオブジェクトにconfigを設定できる()
    {
        $appPath  = GENE_TEST_ROOT . '/var/config/path.ini';
        $config   = Gene_Config::load($appPath);
        $instance = new Gene_Application_Setting_Path();
        $instance->setConfig($config);
        $this->assertTrue($instance instanceof Gene_Application_Setting_Path);
    }

    public function testパス取得の際にConfigが設定されてない場合例外が発生する()
    {
        try {
            $instance = new Gene_Application_Setting_Path();
            $path     = $instance->getLayoutPath();
        } catch (Gene_Application_Setting_Exception $e) {
            $this->assertEquals($e->getMessage(), 'Invalid config.');
            return;
        }
        $this->fail('Exception does not occured.');
    }

    public function test設定したパスからモジュール名とパスを取得できる()
    {
        $path     = dirname(__FILE__) . '/var/modules/';
        $instance = new Gene_Application_Setting_Path();
        $module   = $instance->getModuleResources($path);

        $namespace = array();
        $basePath  = array();

        foreach ($module as $key => $val) {
            if (key($val) === 'namespace') {
                $namespace[] = $val['namespace'];
            }
            if (key($val) === 'basePath') {
                $basePath[] = $val['basePath'];
            }
        }

        $i = 0;
        foreach ($namespace as $key => $val) {
            if (in_array($val, array('Admin', 'Index'))) {
                $i ++;
            }
        }
        $this->assertSame(count($namespace), $i);

        $i = 0;
        $expects = array(
            dirname(__FILE__) . '/var/modules/admin',
            dirname(__FILE__) . '/var/modules/index'
        );
        foreach ($basePath as $key => $val) {
            if (in_array($val, $expects)) {
                $i ++;
            }
        }

        $this->assertSame(count($basePath), $i);
    }

    public function testモジュールディレクトリのオートロードを設定できる()
    {
        $path     = dirname(__FILE__) . '/var/modules/';
        $instance = new Gene_Application_Setting_Path();
        $module   = $instance->getModuleResources($path);
        $instance->setResoucesToAutoloader($module);

        $indexModel = new Index_Models_Test();
        $this->assertTrue($indexModel instanceof Index_Models_Test);

        $indexService = new Index_Services_Test();
        $this->assertTrue($indexService instanceof Index_Services_Test);

        $valid = new Index_Services_Validation_Test();
        $this->assertTrue($valid instanceof Index_Services_Validation_Test);

        $adminModel = new Admin_Models_Test();
        $this->assertTrue($adminModel instanceof Admin_Models_Test);

        $adminService = new Admin_Services_Test();
        $this->assertTrue($adminService instanceof Admin_Services_Test);
    }

    public function test規定のディレクトリのオートロードを設定できる()
    {
        $path     = dirname(__FILE__) . '/var';
        $instance = new Gene_Application_Setting_Path();
        $instance->initDefaultResources($path);

        $services = new Services_Index_Test();
        $this->assertTrue($services instanceof Services_Index_Test);

        $services = new Services_Admin_Test();
        $this->assertTrue($services instanceof Services_Admin_Test);

        $models = new Models_Index_Test();
        $this->assertTrue($models instanceof Models_Index_Test);

        $models = new Models_Admin_Test();
        $this->assertTrue($models instanceof Models_Admin_Test);

        $indexModel = new Index_Models_Test();
        $this->assertTrue($indexModel instanceof Index_Models_Test);

        $indexService = new Index_Services_Test();
        $this->assertTrue($indexService instanceof Index_Services_Test);

        $valid = new Index_Services_Validation_Test();
        $this->assertTrue($valid instanceof Index_Services_Validation_Test);

        $adminModel = new Admin_Models_Test();
        $this->assertTrue($adminModel instanceof Admin_Models_Test);

        $adminService = new Admin_Services_Test();
        $this->assertTrue($adminService instanceof Admin_Services_Test);
    }

    public function test設定ファイルからオートロードを設定できる()
    {
        $appPath  = GENE_TEST_ROOT . '/var/config/path.ini';
        $config   = Gene_Config::load($appPath);
        $instance = new Gene_Application_Setting_Path($config);
        $instance->loadFromConfig();

        $model = new Index_Models_Test();
        $this->assertTrue($model instanceof Index_Models_Test);

        $services = new Index_Services_Test();
        $this->assertTrue($services instanceof Index_Services_Test);

        $view = new Index_Views_Test();
        $this->assertTrue($view instanceof Index_Views_Test);

        $view = new Views_Test();
        $this->assertTrue($view instanceof Views_Test);
    }


    public function test規定のディレクトリのオートロードを設定後に設定ファイルの値も設定できる()
    {
        $path     = dirname(__FILE__) . '/var';
        $appPath  = GENE_TEST_ROOT . '/var/config/path.ini';
        $config   = Gene_Config::load($appPath);
        $instance = new Gene_Application_Setting_Path($config);
        $instance->initDefaultResources($path)->loadFromConfig();

        $services = new Services_Index_Test();
        $this->assertTrue($services instanceof Services_Index_Test);

        $view = new Index_Views_Test();
        $this->assertTrue($view instanceof Index_Views_Test);

        $view = new Views_Test();
        $this->assertTrue($view instanceof Views_Test);
    }

    public function testモジュールのloaderを取得できる()
    {
        $path     = dirname(__FILE__) . '/var';
        $instance = new Gene_Application_Setting_Path();
        $loader   = $instance->initDefaultResources($path)
                             ->getModuleLoader('index');
        $this->assertTrue($loader instanceof Zend_Loader_Autoloader_Resource);
        $this->assertEquals($loader->getNamespace(), 'Index');

        $loader = $instance->getModuleLoader('admin');
        $this->assertTrue($loader instanceof Zend_Loader_Autoloader_Resource);
        $this->assertEquals($loader->getNamespace(), 'Admin');

        $loader = $instance->getModuleLoader();
        $this->assertTrue($loader instanceof Zend_Loader_Autoloader_Resource);
        $this->assertEquals($loader->getNamespace(), '');
    }

    public function testloaderに新しく設定を追加できる()
    {
        $path     = dirname(__FILE__) . '/var';
        $instance = new Gene_Application_Setting_Path();
        $instance->initDefaultResources($path);

        $loader = $instance->getModuleLoader('index');
        $params = array(
            'views' => array(
                'namespace' => 'Views',
                'path'      => 'views'
            )
        );
        $instance->addResource('index', $params);

        $index = new Index_Views_Test();
        $this->assertTrue($index instanceof Index_Views_Test);
    }
}
