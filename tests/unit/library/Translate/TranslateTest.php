<?php
/**
 * Gene_Translate
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
 * @package   Gene_Translate
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
 * Specs for Gene_Translate
 *
 * @category  Gene
 * @package   Gene_Translate
 * @version   $id$
 * @copyright 2009-2010 Shinya Ohyanagi
 * @author    Shinya Ohyanagi <sohyanagi@gmail.com>
 * @license   New BSD License
 */
class Gene_Translateの動作Test extends PHPUnit_Framework_TestCase
{
    private static $_data = null;

    public static function setUpBeforeClass()
    {

        $path = GENE_TEST_ROOT . '/var/resources/languages/ja';
        ob_start();
        $data = include_once $path . '/Another.php';
        ob_end_clean();
        self::$_data = $data;
    }

    public function setUp()
    {
        $options = array(
            'env'       => 'testing',
            'resources' => array('Cache', 'Config', 'Path')
        );
        $path = GENE_APP_PATH;
        Gene::app($path, $options);
    }

    public function testインスタンスを生成できる()
    {
        $instance = new Gene_Translate();
        $this->assertTrue($instance instanceof Gene_Translate);
    }

    public function testユーザ定義のtranslateオブジェクトを取得できる()
    {
        $instance  = new Gene_Translate(GENE_TEST_ROOT . '/var', GENE_TEST_ROOT);
        $translate = $instance->getTranslate('message.ini');
        $this->assertTrue($translate instanceof Zend_Translate);
    }

    public function testシステムバリデーションのtranslateオブジェクトを取得できる()
    {
        $instance  = new Gene_Translate(GENE_APP_PATH);
        $translate = $instance->getValidateTranslate();
        $this->assertTrue($translate instanceof Zend_Translate);
    }

    public function test規定のディレクトリにファイルがある場合メッセージを上書きする()
    {
        $instance  = new Gene_Translate(GENE_APP_PATH);
        $path      = GENE_TEST_ROOT . '/var/resources/languages/ja';
        $translate = $instance->getValidateTranslate();
        $result    = $instance->mergeTranslate($translate, $path);
        $actual  = $result->getAdapter()->getMessages();
        $key = key(self::$_data);
        $this->assertSame($actual[$key], self::$_data[$key]);
    }

    public function test上書きしたメッセージがエラー時に表示する()
    {
        $instance  = new Gene_Translate(GENE_APP_PATH);
        $path      = GENE_TEST_ROOT . '/var/resources/languages/ja';
        $translate = $instance->getValidateTranslate();
        $result    = $instance->mergeTranslate($translate, $path);

        $key = key(self::$_data);

        Zend_Validate_Abstract::setDefaultTranslator($result);
        $validator = new Zend_Validate_EmailAddress();
        $validator->isValid('example@examle.c');
        $messages = $validator->getMessages();
        $this->assertSame(
            $messages[Zend_Validate_EmailAddress::INVALID_HOSTNAME],
            self::$_data[$key]
        );
    }
}
