<?php
/**
 * Gene_View_Adapter_Phtmlc
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
 * @package   Gene_View
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
 * Specs for Gene_View_Adapter_Phtmlc
 *
 * @category  Gene
 * @package   Gene_View
 * @version   $id$
 * @copyright 2009-2010 Shinya Ohyanagi
 * @author    Shinya Ohyanagi <sohyanagi@gmail.com>
 * @license   New BSD License
 */
class Gene_View_Adapter_Phtmlcの動作Test extends PHPUnit_Framework_TestCase
{
    public function testPhtmlcインスタンスを生成できる()
    {
        $instance = new Gene_View_Adapter_Phtmlc();
        $this->assertTrue($instance instanceof Gene_View_Adapter_Phtmlc);

    }

    public function testインスタンス生成時にconfigを設定できる()
    {
        $appPath  = dirname(__FILE__);
        $path     = GENE_TEST_ROOT . '/var/config/view.ini';
        $config   = new Zend_Config_Ini($path);
        $instance = new Gene_View_Adapter_Phtmlc($config);
        $this->assertTrue($instance instanceof Gene_View_Adapter_Phtmlc);
    }

    public function testクラス名が設定されていない場合例外が発生する()
    {
        $param = array(
            array(
                'Phtmlc' => array(
                    'template' => array(
                        'suffix' => 'phtml'
                    )
                )
            )
        );

        $instance = new Gene_View_Adapter_Phtmlc($param);
        try {
            $view = $instance->getView('phtmlc');
        } catch (Gene_View_Exception $e) {
            $this->assertEquals($e->getMessage(), 'Invaild class name.');
            return;
        }
        $this->fail('Exception does not occured.');
    }

    public function testエンコードが設定されてない場合utf8が設定される()
    {
        $param = array(
            'className' => 'Revulo_View_Phtmlc',
            'template'  => array(
                'suffix' => 'phtml'
            ),
            'compilePath' => GENE_TEST_ROOT . '/var/cache/phtmlc/'
        );
        $instance = new Gene_View_Adapter_Phtmlc($param);
        $view     = $instance->getView('phtml');
        $this->assertEquals($view->getEncoding(), 'UTF-8');
    }

    public function testCompilepathが設定されていない場合例外が発生する()
    {
        $param = array(
            'className' => 'Revulo_View_Phtmlc',
            'template'  => array(
                'suffix' => 'phtml'
            )
        );
        $instance = new Gene_View_Adapter_Phtmlc($param);
        try {
            $view = $instance->getView('phtml');
        } catch (Gene_View_Exception $e) {
            $this->assertEquals($e->getMessage(), 'compilePath not found.');
            return;
        }
        $this->fail('Exception does not occured.');
    }

    public function testcompileFragmentsがonの場合trueが設定される()
    {
        $param = array(
            'className' => 'Revulo_View_Phtmlc',
            'template'  => array(
                'suffix' => 'phtml'
            ),
            'compilePath'      => GENE_TEST_ROOT . '/var/cache/phtmlc/',
            'compileFragments' => 'on'
        );
        $instance = new Gene_View_Adapter_Phtmlc($param);
        $view     = $instance->getView('phtml');
        $this->assertTrue($instance instanceof Gene_View_Adapter_Phtmlc);
    }

    public function testViewrendererに追加できる()
    {
        $path  = GENE_TEST_ROOT . '/library/var/view/';
        $spec  = ':module/:controller/:action.:suffix';
        $param = array(
            'template' => array(
                'engine' => 'Phtmlc',
                'path'   => $path,
                'spec'   => $spec
            ),
            'Phtmlc' => array(
                'className' => 'Revulo_View_Phtmlc',
                'template'  => array(
                    'suffix' => 'phtml'
                ),
                'compilePath'      => GENE_TEST_ROOT . '/var/cache/phtmlc/'
            )
        );
        $instance = new Gene_View_Adapter($param);
        $renderer = new Zend_Controller_Action_Helper_ViewRenderer();

        $instance->setViewRenderer($renderer);
        $this->assertEquals($renderer->getViewScriptPathSpec(), $spec);
        $this->assertEquals($renderer->getViewBasePathSpec(), $path);
    }

}
