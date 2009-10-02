<?php
/**
 * Gene_Validate_Uri
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
 * @package   Gene_Validate
 * @version   $id$
 * @copyright 2009 Shinya Ohyanagi
 * @author    Shinya Ohyanagi <sohyanagi@gmail.com>
 * @license   New BSD License
 */

/**
 * @see prepare
 */
require_once dirname(dirname(dirname(__FILE__))) . '/prepare.php';

/**
 * Specs for Gene_Validate
 *
 * @category  Gene
 * @package   Gene_Validate
 * @version   $id$
 * @copyright 2009 Shinya Ohyanagi
 * @author    Shinya Ohyanagi <sohyanagi@gmail.com>
 * @license   New BSD License
 */
class Gene_Validate_Uriの動作Test extends PHPUnit_Framework_TestCase
{
    public function testインスタンスを生成できる()
    {
        $instance = new Gene_Validate_Uri();
        $this->assertTrue($instance instanceof Gene_Validate_Uri);
    }

    public function test正しいUriを設定した場合trueを応答する()
    {
        $valid = new Gene_Validate_Uri();
        $ret   = $valid->isValid('http://www.example.com');
        $this->assertTrue($ret);
    }

    public function testlocalhostを設定した場合trueを応答する()
    {
        $valid = new Gene_Validate_Uri();
        $ret   = $valid->isValid('http://localhost');
        $this->assertTrue($ret);
    }

    public function testipアドレスを設定した場合trueを応答する()
    {
        $valid = new Gene_Validate_Uri();
        $ret   = $valid->isValid('http://120.0.0.1');
        $this->assertTrue($ret);
    }

    public function testUriスキームがhttpsの場合trueを応答する()
    {
        $valid = new Gene_Validate_Uri();
        $ret   = $valid->isValid('https://example.com');
        $this->assertTrue($ret);
    }

    public function testUri以外の場合falseを応答する()
    {
        $valid = new Gene_Validate_Uri();
        $ret   = $valid->isValid('example.com');
        $this->assertFalse($ret);
    }

    public function testzend_filter_inputを使用しuriの場合trueを応答する()
    {
        $params = array(
            'uri' => 'http://example.com'
        );

        $validations = array(
            'uri' => array(
                new Gene_Validate_Uri(),
                'message' => array(
                    0 => 'Invalid uri'
                )
            )
        );

        $validator = new Zend_Filter_Input(null, $validations);
        $ret = $validator->setData($params)->isValid();
        $this->assertTrue($ret);
    }

    public function testzend_filter_inputを使用しuriでない場合falseを応答する()
    {
        $params = array(
            'uri' => 'test'
        );

        $validations = array(
            'uri' => array(
                new Gene_Validate_Uri(),
                'message' => array(
                    0 => 'Invalid uri'
                )
            )
        );

        $validator = new Zend_Filter_Input(null, $validations);
        $ret = $validator->setData($params)->isValid();
        $this->assertFalse($ret);

    }
}
