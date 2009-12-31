<?php
/**
 * Gene_Application_Setting_Routing
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
 * Specs for Gene_Application_Setting_Routing
 *
 * @category  Gene
 * @package   Gene_Application
 * @version   $id$
 * @copyright 2009-2010 Shinya Ohyanagi
 * @author    Shinya Ohyanagi <sohyanagi@gmail.com>
 * @license   New BSD License
 */
class Gene_Application_Setting_Routingの動作Test extends PHPUnit_Framework_TestCase
{
    public function testインスタンスを生成できる()
    {
        $routing = new Gene_Application_Setting_Routing();
        $this->assertTrue($routing instanceof Gene_Application_Setting_Routing);
    }

    public function testインスタンス生成時にconfigを設定できる()
    {
        $appPath  = GENE_TEST_ROOT . '/var/config/routing.ini';
        $config   = Gene_Config::load($appPath);
        $routing  = new Gene_Application_Setting_Routing($config);
        $this->assertTrue($routing instanceof Gene_Application_Setting_Routing);
    }

    public function test生成したオブジェクトにconfigを設定できる()
    {
        $appPath = GENE_TEST_ROOT . '/var/config/routing.ini';
        $config  = Gene_Config::load($appPath);
        $routing = new Gene_Application_Setting_Routing();
        $routing->setConfig($config);
        $this->assertTrue($routing instanceof Gene_Application_Setting_Routing);
    }

    public function testルーティングを追加できる()
    {
        $appPath = GENE_TEST_ROOT . '/var/config/routing.ini';
        $config  = Gene_Config::load($appPath);
        $routing = new Gene_Application_Setting_Routing($config);

        $request = new Zend_Controller_Request_Http('http://localhost/');
        $front   = Zend_Controller_Front::getInstance();
        $router  = $front->getRouter();
        $actual  = $routing->add($router)->getRouter();

        $this->assertTrue($actual instanceof Zend_Controller_Router_Rewrite);
    }

    public function testデフォルトのルーティング先にディスパッチする()
    {
        $appPath = GENE_TEST_ROOT . '/var/config/routing.ini';
        $config  = Gene_Config::load($appPath);
        $routing = new Gene_Application_Setting_Routing($config->routes);

        $modules = dirname(__FILE__) . '/var/modules/';
        $front   = Zend_Controller_Front::getInstance();
        $router  = $front->getRouter();
        $routes  = $routing->add($router)->getRouter();

        $front->setDefaultModule('index')->addModuleDirectory($modules);
        $request = new Zend_Controller_Request_Http('http://localhost');


        $params = '/admin/index/list';
        $request->setRequestUri($params);
        $response = $front->returnResponse(true)
                          ->setParam('noViewRenderer', true)
                          ->dispatch($request);
        $body     = $response->getBody();
        $this->assertEquals($body, 'Admin_IndexController::listAction');
        $response->clearAllHeaders()->clearBody();
    }

    public function test追加したルーティング先にディスパッチする()
    {
        $appPath = GENE_TEST_ROOT . '/var/config/routing.ini';
        $config  = Gene_Config::load($appPath);
        $routing = new Gene_Application_Setting_Routing($config->routes);

        $modules = dirname(__FILE__) . '/var/modules/';
        $front   = Zend_Controller_Front::getInstance();
        $router  = $front->getRouter();
        $routes  = $routing->add($router)->getRouter();

        $front->setDefaultModule('index')->addModuleDirectory($modules);
        $request = new Zend_Controller_Request_Http('http://localhost');


        $params = '/admin/list';
        $request->setRequestUri($params);
        $response = $front->returnResponse(true)
                          ->setParam('noViewRenderer', true)
                          ->dispatch($request);
        $body     = $response->getBody();
        $this->assertEquals($body, 'Admin_IndexController::listAction');
        $response->clearAllHeaders()->clearBody();
    }
}
