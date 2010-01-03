<?php
/**
 * Gene_Service_Session
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
 * @package   Gene_Service
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
 * Specs for Gene_Service_Session
 *
 * @category  Gene
 * @package   Gene_Service
 * @version   $id$
 * @copyright 2009-2010 Shinya Ohyanagi
 * @author    Shinya Ohyanagi <sohyanagi@gmail.com>
 * @license   New BSD License
 */
class Gene_Service_Session動作Test extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $options = array(
            'env'      => 'testing',
            'resource' => array('Cache', 'Config', 'Path', 'Session')
        );
        $path = GENE_APP_PATH;
        Gene::app($path, $options);
        Zend_Session::$_unitTestEnabled = true;
    }

    public function testSessionインスタンスを生成できる()
    {
        $instance = new Gene_Service_Session();
        $this->assertTrue($instance instanceof Gene_Service_Session);

        $instance = Gene::load('Gene_Service_Session');
        $this->assertTrue($instance instanceof Gene_Service_Session);
    }

    public function testSessionの名前空間が設定されていない場合nullを応答する()
    {
        $namespace = 'namespace1';
        $instance  = Gene::load('Gene_Service_Session');
        $this->assertSame($instance->getNamespace(), null);
    }

    public function testSessionの名前空間を設定できる()
    {
        $namespace = 'namespace2';
        $instance  = Gene::load('Gene_Service_Session');
        $instance->setNamespace($namespace);
        $this->assertSame($instance->getNamespace(), $namespace);
    }

    public function testSessionの名前空間を取得できる()
    {
        $namespace = 'namespace3';
        $instance  = Gene::load('Gene_Service_Session');
        $instance->setNamespace($namespace);
        $this->assertSame($instance->getNamespace(), $namespace);
    }

    public function testSessionを設定できる()
    {
        $namespace = 'test';
        $key       = 'foo';
        $value     = 'bar';
        $instance  = Gene::load('Gene_Service_Session');
        $instance->set($key, $value, $namespace);
        $session = new Zend_Session_Namespace($namespace);
        $this->assertSame($session->{$key}, $value);
        Zend_Session::namespaceUnset($namespace);
    }

    public function testSessionを取得できる()
    {
        $namespace = 'test1';
        $key       = 'bar';
        $value     = 'foo';
        $instance  = Gene::load('Gene_Service_Session');
        $instance->setNamespace($namespace)->set($key, $value);
        $session = $instance->get($key);
        $this->assertSame($session, $value);
        Zend_Session::namespaceUnset($namespace);
    }

    public function testSessionを名前空間を指定して取得できる()
    {
        $namespace = 'test2';
        $key       = 'fizz';
        $value     = 'baz';
        $instance  = Gene::load('Gene_Service_Session');
        $instance->set($key, $value, $namespace);
        $session = $instance->get($key, $namespace);
        $this->assertSame($session, $value);
        Zend_Session::namespaceUnset($namespace);
    }

    public function test設定した全てのsessionを取得できる()
    {
        $namespace = 'test3';
        $key1      = 'foo';
        $value1    = 'bar';
        $key2      = 'fizz';
        $value2    = 'baz';
        $instance  = Gene::load('Gene_Service_Session');
        $instance->set($key1, $value1, $namespace)
                 ->set($key2, $value2, $namespace);

        $session = $instance->getAll($namespace);
        $this->assertSame($session->{$key1}, $value1);
        $this->assertSame($session->{$key2}, $value2);

        Zend_Session::namespaceUnset($namespace);
    }

    public function testセッションを消去できる()
    {
        $namespace = 'test2';
        $key       = 'fizz';
        $value     = 'baz';
        $instance  = Gene::load('Gene_Service_Session');
        $instance->set($key, $value, $namespace);
        $instance->remove($namespace);
        $session = $instance->get($key, $namespace);
        $this->assertSame($session, null);
    }
}
