<?php
/**
 * Gene_Service_Validator
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
 * Specs for Gene_Service_Validator
 *
 * @category  Gene
 * @package   Gene_Service
 * @version   $id$
 * @copyright 2009-2010 Shinya Ohyanagi
 * @author    Shinya Ohyanagi <sohyanagi@gmail.com>
 * @license   New BSD License
 */
class Gene_Service_Validator動作Test extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        require_once dirname(__FILE__) . '/var/Test/Validator.php';
    }

    public function testVaidatorインスタンスを生成できる()
    {
        $instance = new Test_Service_Validator();
        $this->assertTrue($instance instanceof Test_Service_Validator);
    }

    public function test値が正しい場合trueを応答する()
    {
        $data = array(
            'test1' => 'abcdefg'
        );
        $validator = new Test_Service_Validator();
        $valid     = $validator->isValid($data);
        $messages  = $validator->getErrorMessages();
        $this->assertTrue($valid);
    }

    public function test値が誤っている場合falseを応答する()
    {
        $data = array(
            'test1' => '!"#$%&'
        );
        $validator = new Test_Service_Validator();
        $valid     = $validator->isValid($data);
        $messages  = $validator->getErrorMessages();
        $this->assertFalse($valid);
    }

    public function testエラーの場合エラーメッセージを取得できる()
    {
        $data = array(
            'test1' => '!"#$%&'
        );

        $instance  = new Gene_Translate(GENE_APP_PATH);
        $translate = $instance->getValidateTranslate();

        $validator = new Test_Service_Validator();
        $valid     = $validator->setValidatorTranslate($translate)
                               ->isValid($data);

        $messages = $validator->getErrorMessages();
        $expects  = $translate->getAdapter()->getMessages();
        $this->assertFalse($valid);

        $alnum    = new Zend_Validate_Alnum();
        $template = $alnum->getMessageTemplates();

        $expect   = str_replace(
            '%value%',
            $data['test1'],
            $expects[$template[Zend_Validate_Alnum::NOT_ALNUM]]
        );
        $this->assertSame($expect, $messages[0]);
    }

    public function testRequestを設定しバリデーションできる()
    {
        $data = array(
            'test1' => '!"#$%&'
        );
        $validator = new Test_Service_Validator();
        $valid     = $validator->setRequest($data)->isValid();
        $messages  = $validator->getErrorMessages();
        $this->assertFalse($valid);
    }

    public function test翻訳ファイルをvalidatorに設定できる()
    {
        $data = array(
            'test1' => '!"#$%&'
        );

        $instance  = new Gene_Translate(GENE_APP_PATH);
        $translate = $instance->getValidateTranslate();


        $validator = new Test_Service_Validator();
        $valid     = $validator->setValidatorTranslate($translate)
                               ->isValid($data);

        $messages  = $validator->getErrorMessages();
        $expects   = $translate->getAdapter()->getMessages();
        $this->assertFalse($valid);

        $alnum    = new Zend_Validate_Alnum();
        $template = $alnum->getMessageTemplates();

        $expect   = str_replace(
            '%value%',
            $data['test1'],
            $expects[$template[Zend_Validate_Alnum::NOT_ALNUM]]
        );
        $this->assertSame($expect, $messages[0]);
    }
}
