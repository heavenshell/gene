<?php
/**
 * Gene
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
 * @category
 * @package
 * @version   $id$
 * @copyright 2009-2011 Shinya Ohyanagi
 * @author    Shinya Ohyanagi <sohyanagi@gmail.com>
 * @license   New BSD License
 */

/**
 * @see prepare
 */
require_once dirname(dirname(__FILE__)) . '/prepare.php';

/**
 * Specs for Gene
 *
 * @category  Gene
 * @package   Gene_Service
 * @version   $id$
 * @copyright 2009-2011 Shinya Ohyanagi
 * @author    Shinya Ohyanagi <sohyanagi@gmail.com>
 * @license   New BSD License
 */
class Gene_Base動作Test extends PHPUnit_Framework_TestCase
{
    public function testappメソッドはzend_applicationオブジェクトを応答する()
    {
        $app  = Gene::app(GENE_APP_PATH);
        $this->assertTrue($app instanceof Zend_Application);
    }

    public function testappメソッドにoptionsでenvを設定できる()
    {
        $options = array(
            'env'       => 'testing',
            'resources' => array('Config', 'Path')
        );
        $path = GENE_APP_PATH;
        $app  = Gene::app($path, $options);
        $this->assertSame(
            $app->getBootstrap()->getEnvironment(),
            $options['env']
        );
    }

    public function test現在のenvを取得えきる()
    {
        $options = array(
            'env'       => 'testing',
            'resources' => array('Config', 'Path')
        );
        $path = GENE_APP_PATH;
        $app  = Gene::app($path, $options);
        $this->assertSame(Gene::getEnvironment(), $options['env']);
    }

    public function testgetcomponentメソッドに引数がない場合全て応答する()
    {
        $options = array(
            'env'       => 'testing',
        );
        $path = GENE_APP_PATH;
        $app  = Gene::app($path, $options);
        $this->assertTrue(is_array(Gene::getComponent()));
    }

    public function testgetcomponentメソッドにlogを指定した場合loggerを応答する()
    {
        $options = array(
            'env'       => 'testing',
            'resources' => array('Config', 'Log')
        );
        $path = GENE_APP_PATH;
        $app  = Gene::app($path, $options);
        $this->assertTrue(Gene::getComponent('log') instanceof Gene_Log);
    }

    public function testloadメソッドはモジュールをロードできる()
    {
        $options = array(
            'env'       => 'testing',
            'resources' => array('Config', 'Log')
        );
        $path = GENE_APP_PATH;
        Gene::app($path, $options);

        $class = Gene::load('Zend_Version');
        $this->assertTrue($class instanceof Zend_Version);
    }

    public function testloadメソッドでロードした場合オブジェクトをキャッシュする()
    {
        $options = array(
            'env'       => 'testing',
            'resources' => array('Config', 'Log')
        );
        $path = GENE_APP_PATH;
        Gene::app($path, $options);

        $class = Gene::load('Zend_Version');
        $clazz = Gene::load('Zend_Version');
        $this->assertSame(spl_object_hash($class), spl_object_hash($clazz));
    }

    public function testloadメソッドでロードしたキャッシュ済みオブジェクトを削除できる()
    {
        $options = array(
            'env'       => 'testing',
            'resources' => array('Config', 'Log')
        );
        $path = GENE_APP_PATH;
        Gene::app($path, $options);

        $class = Gene::load('Zend_Version');
        Gene::removeInstance('Zend_Version');
        $clazz = Gene::load('Zend_Version');
        $this->assertNotSame(spl_object_hash($class), spl_object_hash($clazz));
    }
}
