<?php
/**
 * Gene_View_Adapter
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
 * @package   Gene_View
 * @version   $id$
 * @copyright 2009-2011 Shinya Ohyanagi
 * @author    Shinya Ohyanagi <sohyanagi@gmail.com>
 * @license   New BSD License
 */

/**
 * @see prepare
 */
require_once dirname(dirname(dirname(dirname(__FILE__)))) . '/prepare.php';

/**
 * Specs for Gene_View_Adapter
 *
 * @category  Gene
 * @package   Gene_View
 * @version   $id$
 * @copyright 2009-2011 Shinya Ohyanagi
 * @author    Shinya Ohyanagi <sohyanagi@gmail.com>
 * @license   New BSD License
 */
class Gene_View_Adapterの動作Test extends PHPUnit_Framework_TestCase
{
    public function testインスタンスを生成できる()
    {
        $instance = new Gene_View_Adapter();
        $this->assertTrue($instance instanceof Gene_View_Adapter);
    }

    public function testインスタンス生成時にconfigを設定できる()
    {
        $appPath  = dirname(__FILE__);
        $path     = GENE_TEST_ROOT . '/var/config/view.ini';
        $config   = new Zend_Config_Ini($path);
        $instance = new Gene_View_Adapter($config);
        $this->assertTrue($instance instanceof Gene_View_Adapter);
    }

    public function test設定したエンジンを取得できる()
    {
        $appPath  = dirname(__FILE__);
        $path     = GENE_TEST_ROOT . '/var/config/view.ini';
        $config   = new Zend_Config_Ini($path);
        $instance = new Gene_View_Adapter($config);
        $view     = $instance->getView('Phtml');
        $this->assertTrue($instance instanceof Gene_View_Adapter);
        $this->assertTrue($view instanceof Zend_View);
    }

    public function test設定ファイルに設定したエンジンを取得できる()
    {
        $appPath  = dirname(__FILE__);
        $path     = GENE_TEST_ROOT . '/var/config/view.ini';
        $config   = new Zend_Config_Ini($path);
        $instance = new Gene_View_Adapter($config);
        $view     = $instance->getView();
        $this->assertTrue($instance instanceof Gene_View_Adapter);
        $this->assertTrue($view instanceof Zend_View);
    }

    public function testCacheから読み込んだ設定ファイルからエンジン取得できる()
    {
        $appPath    = GENE_TEST_ROOT;
        $cachePath  = $appPath . '/var/cache';
        $configPath = $appPath . '/var/config/view.ini';
        $instance   = Gene_Cache_File::getInstance(GENE_TEST_ROOT);
        $options    = array(
            'frontend' => array(
                'master_files' => $configPath
            ),
            'backend' => array(
                'cache_dir' => $cachePath
            )
        );

        $cache = $instance->setOptions($options)->getCache('view');
        $config = Gene_Config::load($configPath, $cache);

        $instance = new Gene_View_Adapter($config);
        $view     = $instance->getView();
        $this->assertTrue($instance instanceof Gene_View_Adapter);
        $this->assertTrue($view instanceof Zend_View);
    }


    public function testViewのパスを取得できる()
    {
        $appPath  = dirname(__FILE__);
        $path     = GENE_TEST_ROOT . '/var/config/view.ini';
        $config   = new Zend_Config_Ini($path);
        $instance = new Gene_View_Adapter($config);
        $viewPath = $instance->getTemplateOptions('path');
        $this->assertEquals($viewPath, $config->template->path);
    }

    public function testViewの構成を取得できる()
    {
        $appPath  = dirname(__FILE__);
        $path     = GENE_TEST_ROOT . '/var/config/view.ini';
        $config   = new Zend_Config_Ini($path);
        $instance = new Gene_View_Adapter($config);
        $viewSpec = $instance->getTemplateOptions('spec');
        $this->assertEquals($viewSpec, $config->template->spec);
    }

    public function testヘルパーを追加できる()
    {
        $appPath  = dirname(__FILE__);
        $path     = GENE_TEST_ROOT . '/var/config/view.ini';
        $config   = new Zend_Config_Ini($path);
        $instance = new Gene_View_Adapter($config);
        $view     = $instance->getView();
        $instance->addHelperPath($config->helper);
        foreach ($config->helper as $key => $val) {
            $helper = $view->getHelperPath($val->prefix);
            $this->assertEquals($helper, $config->helper->path);
        }
    }

    public function testLayoutを設定できる()
    {
        $appPath  = dirname(__FILE__);
        $path     = GENE_TEST_ROOT . '/var/config/view.ini';
        $config   = new Zend_Config_Ini($path);
        $instance = new Gene_View_Adapter($config);
        $view     = $instance->getView();
        $instance->setLayout($config->toArray());

        $front  = Zend_Controller_Front::getInstance();
        $plugin = $front->getPlugin('Zend_Layout_Controller_Plugin_Layout');
        $layout = $plugin->getLayout();
        $this->assertEquals($layout->getContentKey(), 'content');
    }
}
