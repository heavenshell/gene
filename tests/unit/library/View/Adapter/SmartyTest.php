<?php
/**
 * Gene_View_Adapter_Smarty
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
 * Specs for Gene_View_Adapter_Smarty
 *
 * @category  Gene
 * @package   Gene_View
 * @version   $id$
 * @copyright 2009-2011 Shinya Ohyanagi
 * @author    Shinya Ohyanagi <sohyanagi@gmail.com>
 * @license   New BSD License
 */
class Gene_View_Adapter_Smartyの動作Test extends PHPUnit_Framework_TestCase
{
    public function testSmartyインスタンスを生成できる()
    {
        $instance = new Gene_View_Adapter_Smarty();
        $this->assertTrue($instance instanceof Gene_View_Adapter_Smarty);

    }

    public function testインスタンス生成時にconfigを設定できる()
    {
        $appPath  = dirname(__FILE__);
        $path     = GENE_TEST_ROOT . '/var/config/view.ini';
        $config   = new Zend_Config_Ini($path);
        $instance = new Gene_View_Adapter_Smarty($config);
        $this->assertTrue($instance instanceof Gene_View_Adapter_Smarty);
    }

    public function testクラス名が設定されていない場合例外が発生する()
    {
        $param = array(
            array(
                'Smarty' => array(
                    'template' => array(
                        'suffix' => 'tpl'
                    )
                )
            )
        );

        $instance = new Gene_View_Adapter_Smarty($param);
        try {
            $view = $instance->getView('smarty');
        } catch (Gene_View_Exception $e) {
            $this->assertEquals($e->getMessage(), 'Invaild class name.');
            return;
        }
        $this->fail('Exception does not occured.');
    }

    public function testエンコードが設定されてない場合utf8が設定される()
    {
        $param = array(
            'className' => 'Gene_Smarty_View',
            'template'  => array(
                'suffix' => 'tpl'
            ),
            'cache' => array(
                'path' => GENE_TEST_ROOT . '/var/cache/smarty/'
            )
        );
        $instance = new Gene_View_Adapter_Smarty($param);
        $view     = $instance->getView('smarty');
        $this->assertEquals($view->getEncoding(), 'UTF-8');
    }

    public function testViewrendererに追加できる()
    {
        $path  = GENE_TEST_ROOT . '/library/var/view/';
        $spec  = ':module/:controller/:action.:suffix';
        $param = array(
            'template' => array(
                'engine' => 'Smarty',
                'path'   => $path,
                'spec'   => $spec
            ),
            'Smarty' => array(
                'className' => 'Gene_Smarty_View',
                'template'  => array(
                    'suffix' => 'phtml'
                ),
                'cache' => array(
                    'path' => GENE_TEST_ROOT . '/var/cache/smarty/'
                )
            )
        );
        $instance = new Gene_View_Adapter($param);
        $renderer = new Zend_Controller_Action_Helper_ViewRenderer();

        $instance->setViewRenderer($renderer);
        $this->assertEquals($renderer->getViewScriptPathSpec(), $spec);
        $this->assertEquals($renderer->getViewBasePathSpec(), $path);
    }

    public function testCachepathが設定されていない場合例外が発生する()
    {
        $param = array(
            'template' => array(
                'engine' => 'Smarty'
            ),
            'Smarty' => array(
                'className' => 'Gene_Smarty_View',
                'template'  => array(
                    'suffix' => 'phtml'
                )
            )
        );

        $instance = new Gene_View_Adapter($param);
        try {
            $view = $instance->getView('Smarty');
        } catch (Gene_View_Exception $e) {
            $this->assertEquals($e->getMessage(), 'Cache path not found.');
            return;
        }
        $this->fail('Exception does not occured.');
    }

    public function testView生成時にcachingを「0」に設定できる()
    {
        $param = array(
            'template' => array(
                'engine' => 'Smarty'
            ),
            'Smarty' => array(
                'className' => 'Gene_Smarty_View',
                'template'  => array(
                    'suffix' => 'phtml'
                ),
                'cache' => array(
                    'path'    => GENE_TEST_ROOT . '/var/cache/smarty/',
                    'caching' => 0
                )
            )
        );
        $instance = new Gene_View_Adapter($param);
        $view     = $instance->getView('Smarty');
        $caching  = $view->getEngine()->caching;
        $this->assertEquals($caching, 0);
    }

    public function testView生成時にcachingを「1」に設定できる()
    {
        $param = array(
            'template' => array(
                'engine' => 'Smarty'
            ),
            'Smarty' => array(
                'className' => 'Gene_Smarty_View',
                'template'  => array(
                    'suffix' => 'phtml'
                ),
                'cache' => array(
                    'path'    => GENE_TEST_ROOT . '/var/cache/smarty/',
                    'caching' => 1
                )
            )
        );
        $instance = new Gene_View_Adapter($param);
        $view     = $instance->getView('Smarty');
        $caching  = $view->getEngine()->caching;
        $this->assertEquals($caching, 1);
    }

    public function testView生成時にcachingを「2」に設定できる()
    {
        $param = array(
            'template' => array(
                'engine' => 'Smarty'
            ),
            'Smarty' => array(
                'className' => 'Gene_Smarty_View',
                'template'  => array(
                    'suffix' => 'phtml'
                ),
                'cache' => array(
                    'path'    => GENE_TEST_ROOT . '/var/cache/smarty/',
                    'caching' => 2
                )
            )
        );
        $instance = new Gene_View_Adapter($param);
        $view     = $instance->getView('Smarty');
        $caching  = $view->getEngine()->caching;
        $this->assertEquals($caching, 2);
    }

    public function testView生成時にcachingが「0」「1」「2」以外は「0」が設定される()
    {
        $param = array(
            'template' => array(
                'engine' => 'Smarty'
            ),
            'Smarty' => array(
                'className' => 'Gene_Smarty_View',
                'template'  => array(
                    'suffix' => 'phtml'
                ),
                'cache' => array(
                    'path'    => GENE_TEST_ROOT . '/var/cache/smarty/',
                    'caching' => 3
                )
            )
        );
        $instance = new Gene_View_Adapter($param);
        $view     = $instance->getView('Smarty');
        $caching  = $view->getEngine()->caching;
        $this->assertEquals($caching, 0);
    }

    public function testView生成時にcachingの値を設定しない場合デフォルトが適用される()
    {
        $param = array(
            'template' => array(
                'engine' => 'Smarty'
            ),
            'Smarty' => array(
                'className' => 'Gene_Smarty_View',
                'template'  => array(
                    'suffix' => 'phtml'
                ),
                'cache' => array(
                    'path' => GENE_TEST_ROOT . '/var/cache/smarty/'
                )
            )
        );
        $instance = new Gene_View_Adapter($param);
        $view     = $instance->getView('Smarty');
        $caching  = $view->getEngine()->caching;
        $this->assertEquals($caching, 0);
    }

    public function testView生成時にキャッシュの有効時間を設定できる()
    {
        $param = array(
            'template' => array(
                'engine' => 'Smarty'
            ),
            'Smarty' => array(
                'className' => 'Gene_Smarty_View',
                'template'  => array(
                    'suffix' => 'phtml'
                ),
                'cache' => array(
                    'path'     => GENE_TEST_ROOT . '/var/cache/smarty/',
                    'lifetime' => 1440
                )
            )
        );
        $instance = new Gene_View_Adapter($param);
        $view     = $instance->getView('Smarty');
        $caching  = $view->getEngine()->cache_lifetime;
        $this->assertEquals($caching, 1440);
    }

    public function testView生成時にキャッシュの有効時間を設定しない場合デフォルトが適用される()
    {
        $param = array(
            'template' => array(
                'engine' => 'Smarty'
            ),
            'Smarty' => array(
                'className' => 'Gene_Smarty_View',
                'template'  => array(
                    'suffix' => 'phtml'
                ),
                'cache' => array(
                    'path' => GENE_TEST_ROOT . '/var/cache/smarty/'
                )
            )
        );
        $instance = new Gene_View_Adapter($param);
        $view     = $instance->getView('Smarty');
        $caching  = $view->getEngine()->cache_lifetime;
        $this->assertEquals($caching, 3600);
    }

    public function testView生成時にテンプレートディレクトリを設定できる()
    {
        $param = array(
            'template' => array(
                'engine' => 'Smarty'
            ),
            'Smarty' => array(
                'className' => 'Gene_Smarty_View',
                'template'  => array(
                    'suffix' => 'phtml',
                    'path'   => GENE_TEST_ROOT . '/var/template/'
                ),
                'cache' => array(
                    'path' => GENE_TEST_ROOT . '/var/cache/smarty/'
                )
            )
        );
        $instance = new Gene_View_Adapter($param);
        $view     = $instance->getView('Smarty');
        $template = $view->getEngine()->template_dir;
        $this->assertEquals($template, GENE_TEST_ROOT . '/var/template/');
    }

    public function testView生成時にテンプレートディレクトリを設定しない場合nullを応答する()
    {
        $param = array(
            'template' => array(
                'engine' => 'Smarty'
            ),
            'Smarty' => array(
                'className' => 'Gene_Smarty_View',
                'template'  => array(
                    'suffix' => 'phtml'
                ),
                'cache' => array(
                    'path' => GENE_TEST_ROOT . '/var/cache/smarty/'
                )
            )
        );
        $instance = new Gene_View_Adapter($param);
        $view     = $instance->getView('Smarty');
        $template = $view->getEngine()->template_dir;
        $this->assertEquals($template, null);
    }

    public function testView生成時にコンパイルディレクトリを設定できる()
    {
        $param = array(
            'template' => array(
                'engine' => 'Smarty'
            ),
            'Smarty' => array(
                'className' => 'Gene_Smarty_View',
                'template'  => array(
                    'suffix' => 'phtml'
                ),
                'cache' => array(
                    'path' => GENE_TEST_ROOT . '/var/cache/smarty/'
                ),
                'compile' => array(
                    'path' => GENE_TEST_ROOT . '/var/cache/smarty/'
                )
            )
        );
        $instance = new Gene_View_Adapter($param);
        $view     = $instance->getView('Smarty');
        $compile  = $view->getEngine()->compile_dir;
        $this->assertEquals($compile, GENE_TEST_ROOT . '/var/cache/smarty/');
    }

    public function testView生成時にコンパイルディレクトリを設定しない場合nullを設定する()
    {
        $param = array(
            'template' => array(
                'engine' => 'Smarty'
            ),
            'Smarty' => array(
                'className' => 'Gene_Smarty_View',
                'template'  => array(
                    'suffix' => 'phtml'
                ),
                'cache' => array(
                    'path' => GENE_TEST_ROOT . '/var/cache/smarty/'
                )
            )
        );
        $instance = new Gene_View_Adapter($param);
        $view     = $instance->getView('Smarty');
        $compile  = $view->getEngine()->compile_dir;
        $this->assertEquals($compile, null);
    }

    public function testView生成時に強制的にコンパイルを行う設定にできる()
    {
        $param = array(
            'template' => array(
                'engine' => 'Smarty'
            ),
            'Smarty' => array(
                'className' => 'Gene_Smarty_View',
                'template'  => array(
                    'suffix' => 'phtml'
                ),
                'cache' => array(
                    'path' => GENE_TEST_ROOT . '/var/cache/smarty/'
                ),
                'compile' => array(
                    'force' => 'true'
                )
            )
        );
        $instance = new Gene_View_Adapter($param);
        $view     = $instance->getView('Smarty');
        $compile  = $view->getEngine()->force_compile;
        $this->assertEquals($compile, true);
    }

    public function testView生成時に強制的にコンパイルを行う設定にしない場合デフォルトが適用される()
    {
        $param = array(
            'template' => array(
                'engine' => 'Smarty'
            ),
            'Smarty' => array(
                'className' => 'Gene_Smarty_View',
                'template'  => array(
                    'suffix' => 'phtml'
                ),
                'cache' => array(
                    'path' => GENE_TEST_ROOT . '/var/cache/smarty/'
                )
            )
        );
        $instance = new Gene_View_Adapter($param);
        $view     = $instance->getView('Smarty');
        $compile  = $view->getEngine()->force_compile;
        $this->assertEquals($compile, false);
    }


    public function testView生成時に設定ファイルを格納するディレクトリを設定できる()
    {
        $param = array(
            'template' => array(
                'engine' => 'Smarty'
            ),
            'Smarty' => array(
                'className' => 'Gene_Smarty_View',
                'template'  => array(
                    'suffix' => 'phtml'
                ),
                'cache' => array(
                    'path' => GENE_TEST_ROOT . '/var/cache/smarty/'
                ),
                'config' => array(
                    'path' => GENE_TEST_ROOT . '/var/cache/smarty/'
                )
            )
        );
        $instance = new Gene_View_Adapter($param);
        $view     = $instance->getView('Smarty');
        $config   = $view->getEngine()->config_dir;
        $this->assertEquals($config, GENE_TEST_ROOT . '/var/cache/smarty/');
    }

    public function testView生成時に設定ファイルを格納するディレクトリを設定しない場合nullを設定する()
    {
        $param = array(
            'template' => array(
                'engine' => 'Smarty'
            ),
            'Smarty' => array(
                'className' => 'Gene_Smarty_View',
                'template'  => array(
                    'suffix' => 'phtml'
                ),
                'cache' => array(
                    'path' => GENE_TEST_ROOT . '/var/cache/smarty/'
                )
            )
        );
        $instance = new Gene_View_Adapter($param);
        $view     = $instance->getView('Smarty');
        $config   = $view->getEngine()->config_dir;
        $this->assertEquals($config, null);
    }

    public function testView生成時にプラグインディレクトリを設定できる()
    {
        $param = array(
            'template' => array(
                'engine' => 'Smarty'
            ),
            'Smarty' => array(
                'className' => 'Gene_Smarty_View',
                'template'  => array(
                    'suffix' => 'phtml'
                ),
                'cache' => array(
                    'path' => GENE_TEST_ROOT . '/var/cache/smarty/'
                ),
                'plugin' => array(
                    'path' => array(0 => GENE_TEST_ROOT . '/var/cache/smarty/')
                )
            )
        );
        $instance = new Gene_View_Adapter($param);
        $view     = $instance->getView('Smarty');
        $plugin   = $view->getEngine()->plugin_dir;
        $this->assertEquals($plugin[0], GENE_TEST_ROOT . '/var/cache/smarty/');
    }

    public function test既に登録済みのプラグインディレクトリがある場合追加しない()
    {
        $param = array(
            'template' => array(
                'engine' => 'Smarty'
            ),
            'Smarty' => array(
                'className' => 'Gene_Smarty_View',
                'template'  => array(
                    'suffix' => 'phtml'
                ),
                'cache' => array(
                    'path' => GENE_TEST_ROOT . '/var/cache/smarty/'
                ),
                'plugin' => array(
                    'path' => array(
                        0 => GENE_TEST_ROOT . '/var/cache/smarty/',
                        1 => GENE_TEST_ROOT . '/var/cache/smarty/'
                    )
                )
            )
        );
        $instance = new Gene_View_Adapter($param);
        $view     = $instance->getView('Smarty');
        $plugin   = $view->getEngine()->plugin_dir;
        $this->assertEquals(count($plugin), 1);
    }

    public function testView生成時にデバッグモードをonに設定できる()
    {
        $param = array(
            'template' => array(
                'engine' => 'Smarty',
            ),
            'Smarty' => array(
                'className' => 'Gene_Smarty_View',
                'template'  => array(
                    'suffix' => 'phtml'
                ),
                'cache' => array(
                    'path' => GENE_TEST_ROOT . '/var/cache/smarty/'
                ),
                'debug' => array('enabled' => 'on')
            )
        );
        $instance = new Gene_View_Adapter($param);
        $view     = $instance->getView('Smarty');
        $debug    = $view->getEngine()->debugging;
        $this->assertEquals($debug, true);
    }

    public function testView生成時にデバッグモードを設定しない場合falseが設定される()
    {
        $param = array(
            'template' => array(
                'engine' => 'Smarty',
            ),
            'Smarty' => array(
                'className' => 'Gene_Smarty_View',
                'template'  => array(
                    'suffix' => 'phtml'
                ),
                'cache' => array(
                    'path' => GENE_TEST_ROOT . '/var/cache/smarty/'
                )
            )
        );
        $instance = new Gene_View_Adapter($param);
        $view     = $instance->getView('Smarty');
        $debug    = $view->getEngine()->debugging;
        $this->assertEquals($debug, false);
    }

    public function teseView生成時にデバッグモードにon以外の値が設定された場合falseが設定される()
    {
        $param = array(
            'template' => array(
                'engine' => 'Smarty',
            ),
            'Smarty' => array(
                'className' => 'Gene_Smarty_View',
                'template'  => array(
                    'suffix' => 'phtml'
                ),
                'cache' => array(
                    'path' => GENE_TEST_ROOT . '/var/cache/smarty/'
                ),
                'debug' => array('enabled' => true)
            )
        );
        $instance = new Gene_View_Adapter($param);
        $view     = $instance->getView('Smarty');
        $debug    = $view->getEngine()->debugging;
        $this->assertEquals($debug, false);
    }

    public function testView生成時にleftdelimterを設定できる()
    {
        $param = array(
            'template' => array(
                'engine' => 'Smarty',
            ),
            'Smarty' => array(
                'className' => 'Gene_Smarty_View',
                'template'  => array(
                    'suffix' => 'phtml'
                ),
                'cache' => array(
                    'path' => GENE_TEST_ROOT . '/var/cache/smarty/'
                ),
                'delimiter' => array('left' => '{{')
            )
        );
        $instance = new Gene_View_Adapter($param);
        $view     = $instance->getView('Smarty');
        $left     = $view->getEngine()->left_delimiter;
        $this->assertEquals($left, '{{');
    }

    public function testView生成時にrightdelimterを設定できる()
    {
        $param = array(
            'template' => array(
                'engine' => 'Smarty',
            ),
            'Smarty' => array(
                'className' => 'Gene_Smarty_View',
                'template'  => array(
                    'suffix' => 'phtml'
                ),
                'cache' => array(
                    'path' => GENE_TEST_ROOT . '/var/cache/smarty/'
                ),
                'delimiter' => array('right' => '}}')
            )
        );
        $instance = new Gene_View_Adapter($param);
        $view     = $instance->getView('Smarty');
        $left     = $view->getEngine()->right_delimiter;
        $this->assertEquals($left, '}}');
    }

    public function testView生成時にdelimiterを設定しない場合デフォルトが設定される()
    {
        $param = array(
            'template' => array(
                'engine' => 'Smarty',
            ),
            'Smarty' => array(
                'className' => 'Gene_Smarty_View',
                'template'  => array(
                    'suffix' => 'phtml'
                ),
                'cache' => array(
                    'path' => GENE_TEST_ROOT . '/var/cache/smarty/'
                )
            )
        );
        $instance = new Gene_View_Adapter($param);
        $view     = $instance->getView('Smarty');
        $engine   = $view->getEngine();
        $this->assertEquals($engine->left_delimiter, '{');
        $this->assertEquals($engine->right_delimiter, '}');
    }
}
