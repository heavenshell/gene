<?php
/**
 * Gene_View_Adapter_Phtml
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
 * @package   Gene_View
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
 * Specs for Gene_View_Adapter_Phtml
 *
 * @category  Gene
 * @package   Gene_View
 * @version   $id$
 * @copyright 2009 Shinya Ohyanagi
 * @author    Shinya Ohyanagi <sohyanagi@gmail.com>
 * @license   New BSD License
 */
class Gene_View_Adapter_Phtmlの動作Test extends PHPUnit_Framework_TestCase
{
    public function testPhtmlインスタンスを生成できる()
    {
        $instance = new Gene_View_Adapter_Phtml();
        $this->assertTrue($instance instanceof Gene_View_Adapter_Phtml);

    }

    public function testインスタンス生成時にconfigを設定できる()
    {
        $appPath  = dirname(__FILE__);
        $path     = GENE_TEST_ROOT . '/var/config/view.ini';
        $config   = new Zend_Config_Ini($path);
        $instance = new Gene_View_Adapter_Phtml($config);
        $this->assertTrue($instance instanceof Gene_View_Adapter_Phtml);
    }

    public function testクラス名が設定されていない場合例外が発生する()
    {
        $param = array(
            array(
                'Phtml' => array(
                    'template' => array(
                        'suffix' => 'phtml'
                    )
                )
            )
        );

        $instance = new Gene_View_Adapter_Phtml($param);
        try {
            $view = $instance->getView('phtml');
        } catch (Gene_View_Exception $e) {
            $this->assertEquals($e->getMessage(), 'Invaild class name.');
            return;
        }
        $this->fail('Exception does not occured.');
    }

    public function testエンコードが設定されてない場合utf8が設定される()
    {
        $param = array(
            'className' => 'Zend_View',
            'template'  => array(
                'suffix' => 'phtml'
            )
        );
        $instance = new Gene_View_Adapter_Phtml($param);
        $view     = $instance->getView('phtml');
        $this->assertEquals($view->getEncoding(), 'UTF-8');
    }

    public function testStreamwrapperflagが設定されてない場合falseが設定される()
    {
        $param = array(
            'className' => 'Zend_View',
            'template'  => array(
                'suffix' => 'phtml'
            ),
        );
        $instance = new Gene_View_Adapter_Phtml($param);
        $view     = $instance->getView('phtml');
        $this->assertEquals($view->useStreamWrapper(), false);
    }

    public function testStreamwrapperflagがonの場合trueが設定される()
    {
        $param = array(
            'className' => 'Zend_View',
            'template'  => array(
                'suffix' => 'phtml'
            ),
            'streamWrapperFlag' => 'on'
        );
        $instance = new Gene_View_Adapter_Phtml($param);
        $view     = $instance->getView('phtml');
        $this->assertEquals($view->useStreamWrapper(), true);
    }

    public function testStreamwrapperflagがon以外の場合falseが設定される()
    {
        $param = array(
            'className' => 'Zend_View',
            'template'  => array(
                'suffix' => 'phtml'
            ),
            'streamWrapperFlag' => true
        );
        $instance = new Gene_View_Adapter_Phtml($param);
        $view     = $instance->getView('phtml');
        $this->assertEquals($view->useStreamWrapper(), false);
    }

    public function testViewrendererに追加できる()
    {
        $path  = GENE_TEST_ROOT . '/library/var/view/';
        $spec  = ':module/:controller/:action.:suffix';
        $param = array(
            'template' => array(
                'engine' => 'Phtml',
                'path'   => $path,
                'spec'   => $spec
            ),
            'Phtml' => array(
                'className' => 'Zend_View',
                'template'  => array(
                    'suffix' => 'phtml'
                )
            )
        );
        $instance = new Gene_View_Adapter($param);
        $renderer = new Zend_Controller_Action_Helper_ViewRenderer();

        $instance->setViewRenderer($renderer);
        $this->assertEquals($renderer->getViewScriptPathSpec(), $spec);
        $this->assertEquals($renderer->getViewBasePathSpec(), $path);
    }
}
