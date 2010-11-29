<?php
/**
 * Gene_Service
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
 * Specs for Gene_Service
 *
 * @category  Gene
 * @package   Gene_Service
 * @version   $id$
 * @copyright 2009-2010 Shinya Ohyanagi
 * @author    Shinya Ohyanagi <sohyanagi@gmail.com>
 * @license   New BSD License
 */
class Gene_Serviceの動作Test extends PHPUnit_Framework_TestCase
{
    public static function setUpBeforeClass()
    {
        require_once 'var/Test/ServiceMock.php';
        require_once 'var/Test/BeforeHookMock.php';
        require_once 'var/Test/AfterHookMock.php';
        require_once 'var/Test/Validator.php';
        $iniPath = GENE_TEST_ROOT . '/var/config/database.ini';
        $file    = GENE_TEST_ROOT . '/var/sql/create.sql';
        Gene_TestHelper::trancate($iniPath, $file, 'production');

        $options = array(
            'env'       => 'testing',
            'resources' => array('Cache', 'Config', 'Path', 'Db')
        );
        $path = GENE_APP_PATH;
        Gene::app($path, $options);
        Zend_Session::$_unitTestEnabled = true;

    }

    public static function tearDownAfterClass()
    {
        $adapter = Gene_TestHelper::getDbAdapter();
        $adapter->closeConnection();
    }

    public function testインスタンスを生成できる()
    {
        $service = new Gene_Service();
        $this->assertTrue($service instanceof Gene_Service);

        $service = Gene::load('Gene_Service');
        $this->assertTrue($service instanceof Gene_Service);
    }

    public function testValidatorを取得できる()
    {
        $service   = Gene::load('Test_ServiceMock');
        $validator = $service->getValidator('Test_Service_Validator', 'message.ini');
        $this->assertTrue($validator instanceof Gene_Service_Validator);
    }

    public function testValidationを実行しエラーの場合はfalseを応答する()
    {
        $data = array(
            'test1' => '!"#$%&'
        );
        $service = Gene::load('Test_ServiceMock');
        $valid   = $service->getValidator('Test_Service_Validator', 'message.ini');
        $result  = $valid->isValid($data);
        $this->assertFalse($result);
    }

    public function testValidationを実行しエラーの場合はエラーメッセージを取得できる()
    {
        $data = array(
            'test1' => '!"#$%&'
        );
        $service   = Gene::load('Test_ServiceMock');
        $service->setAppPath(GENE_TEST_ROOT . '/var');
        $valid     = $service->getValidator('Test_Service_Validator', 'message.ini');
        $result    = $valid->isValid($data);
        $alnum     = new Zend_Validate_Alnum();
        $template  = $alnum->getMessageTemplates();
        $messages  = $valid->getErrorMessages();
        $translate = $service->getSystemTranslate();
        $expects   = $translate->getAdapter()->getMessages();
        $expect    = str_replace(
            '%value%',
            $data['test1'],
            $expects[$template[Zend_Validate_Alnum::NOT_ALNUM]]
        );
        $this->assertSame($expect, $messages[0]);
    }

    public function testValidationを実行し正常の場合はtrueを応答する()
    {
        $data = array(
            'test1' => 'test'
        );
        $service = Gene::load('Test_ServiceMock');
        $valid   = $service->getValidator('Test_Service_Validator', 'message.ini');
        $result  = $valid->isValid($data);
        $this->assertTrue($result);
    }

    public function testValidationを実行した場合デフォルトのruleが動作する()
    {
        $data = array(
            'test1' => 'test'
        );
        $service = Gene::load('Test_ServiceMock');
        $valid   = $service->getValidator('Test_Service_Validator', 'message.ini');
        $result  = $valid->isValid($data);
        $this->assertTrue($result);
    }

    public function testValidationのruleをデフォルト以外を利用できる()
    {
        $data = array(
            'name' => '!"#$%&'
        );
        $service = Gene::load('Test_ServiceMock');
        $result  = $service->setRules('createRules')->isValid($data);
        $this->assertFalse($result);
    }

    public function testconfirmを実行した際にsessionの名前空間が設定されていない場合例外が発生する()
    {
        $service = Gene::load('Test_ServiceMock');
        try {
            $service->confirm(array());
        } catch (UnexpectedValueException $e) {
            $this->assertSame($e->getMessage(), 'Session namespace does not set.');
            return;
        }
        $this->fail('Test failed.');
    }

    public function testconfirmがを実行した際にsessionに値を格納する()
    {
        Zend_Session::start(true);
        $data = array(
            'name' => '!"#$%&'
        );
        $service = Gene::load('Test_ServiceMock');
        $result  = $service->setNamespace()->confirm($data);
        $session = new Zend_Session_Namespace('Test_ServiceMock');
        $this->assertSame($session->create, $data);
    }

    public function testconfirmを実行した際にcreaterulesが適応される()
    {
        Zend_Session::start(true);
        $data = array(
            'name' => '!"#$%&'
        );
        $service = Gene::load('Test_ServiceMock');
        $result  = $service->setNamespace()->confirm($data);
        $this->assertFalse($result);
    }

    public function testconfirmを実行した際にvalidationエラーの場合falseを応答する()
    {
        Zend_Session::start(true);
        $data = array(
            'name' => '!"#$%&'
        );
        $service = Gene::load('Test_ServiceMock');
        $result  = $service->setNamespace()->confirm($data);
        $this->assertFalse($result);
    }

    public function testconfirmが正常終了した場合入力した値が応答する()
    {
        Zend_Session::start(true);
        $data = array(
            'name' => 'test'
        );
        $service = Gene::load('Test_ServiceMock');
        $result  = $service->setNamespace()->confirm($data);
        $this->assertSame($result, $data);
    }

    public function testcreateを実行した際にcreaterulesが適応する()
    {
        Zend_Session::start(true);
        $data = array(
            'name' => '!"#$%&'
        );
        $service = Gene::load('Test_ServiceMock');
        $result  = $service->setNamespace()->create($data);
        $this->assertFalse($result);
    }

    public function testcreateを実行した際にvalidationエラーの場合falseを応答する()
    {
        Zend_Session::start(true);
        $data = array(
            'name' => '!"#$%&'
        );
        $service = Gene::load('Test_ServiceMock');
        $result  = $service->setNamespace()->create($data);
        $this->assertFalse($result);
    }

    public function testcreateが正常終了した場合実行結果を応答する()
    {
        Zend_Session::start(true);
        $data = array(
            'name' => 'test'
        );
        $service = Gene::load('Test_ServiceMock');
        $result  = $service->setNamespace()->create($data);
        $this->assertSame($result, '1');
    }

    public function testcreateが正常終了した場合データベースに登録する()
    {
        Zend_Session::start(true);
        $data = array(
            'name' => 'test2'
        );
        $service = Gene::load('Test_ServiceMock');
        $result  = $service->setNamespace()->create($data);
        $model   = Gene::load('Test_Service_Zend');
        $row     = $model->fetchRow(
            $model->select()->where('name = ?', $data['name'])
        );
        $this->assertSame($row->name, $data['name']);
    }

    public function testinputを実行した際にsessionに値がない場合nullを応答する()
    {
        Zend_Session::start(true);
        $data = array(
            'name' => '!"#$%&'
        );
        $service = Gene::load('Test_ServiceMock');
        $result  = $service->setNamespace()->input();
        $this->assertSame($result, null);
    }

    public function testinputを実行した際にsessionに値がある場合sessionを応答する()
    {
        Zend_Session::start(true);
        $data = array(
            'name' => '!"#$%&'
        );
        $service = Gene::load('Test_ServiceMock');
        $result  = $service->setNamespace()->confirm($data);
        $this->assertSame($service->input(), $data);
    }

    public function testeditconfirmがを実行した際にsessionに値を格納する()
    {
        Zend_Session::start(true);
        $data = array(
            'name' => '!"#$%&'
        );
        $service = Gene::load('Test_ServiceMock');
        $result  = $service->setNamespace()->editconfirm($data);
        $session = new Zend_Session_Namespace('Test_ServiceMock');
        $this->assertSame($session->update, $data);
    }

    public function testeditconfirmを実行した際にupdaterulesを適応する()
    {
        Zend_Session::start(true);
        $data = array(
            'name' => '!"#$%&'
        );

        $service = Gene::load('Test_ServiceMock');
        $service->create($data);

        $result  = $service->setNamespace()->editconfirm($data);
        $this->assertFalse($result);
    }

    public function testeditconfirmを実行した際にvalidationがエラーの場合falseを応答する()
    {
        Zend_Session::start(true);
        $data = array(
            'name' => '!"#$%&'
        );
        $service = Gene::load('Test_ServiceMock');
        $result  = $service->setNamespace()->editconfirm($data);
        $this->assertFalse($result);
    }

    public function testeditconfirmが正常終了した場合入力した値を応答する()
    {
        Zend_Session::start(true);
        $data = array(
            'name' => 'test'
        );
        $service = Gene::load('Test_ServiceMock');
        $result  = $service->setNamespace()->editconfirm($data);
        $this->assertSame($result, $data);
    }

    public function testupdateを実行した際にupdaterulesを適応する()
    {
        Zend_Session::start(true);
        $data = array(
            'name' => '!"#$%&'
        );
        $service = Gene::load('Test_ServiceMock');
        $result  = $service->setNamespace()->update($data);
        $this->assertFalse($result);
    }

    public function testupdateを実行した際にvalidationがエラーの場合falseを応答する()
    {
        Zend_Session::start(true);
        $data = array(
            'name' => '!"#$%&'
        );
        $service = Gene::load('Test_ServiceMock');
        $result  = $service->setNamespace()->update($data);
        $this->assertFalse($result);
    }

    public function testupdateが正常終了した場合実行結果を応答する()
    {
        Zend_Session::start(true);
        $data = array(
            'name' => 'test'
        );
        $service = Gene::load('Test_ServiceMock');
        $result  = $service->setNamespace()->create($data);
        $update  = array(
            'id'   => $result,
            'name' => 'hoge'
        );
        $result = $service->update($update);
        $this->assertSame($result, 1);
    }

    public function testupdateが正常終了した場合データベースを更新する()
    {
        Zend_Session::start(true);
        $data = array(
            'name' => 'test'
        );
        $service = Gene::load('Test_ServiceMock');
        $result  = $service->setNamespace()->create($data);
        $update  = array(
            'id'   => $result,
            'name' => 'hoge'
        );
        $service->update($update);

        $model = Gene::load('Test_Service_Zend');
        $row   = $model->fetchRow($model->select()->where('id = ?', $result));
        $this->assertSame($row->name, $update['name']);
    }

    public function testdeleteconfirmがを実行した際にsessionに値を格納する()
    {
        Zend_Session::start(true);
        $data = array(
            'name' => '!"#$%&'
        );
        $service = Gene::load('Test_ServiceMock');
        $result  = $service->setNamespace()->deleteconfirm($data);
        $session = new Zend_Session_Namespace('Test_ServiceMock');
        $this->assertSame($session->delete, $data);
    }

    public function testdeleteconfirmを実行した際にdeleterulesを適応する()
    {
        Zend_Session::start(true);
        $data = array(
            'id' => null
        );

        $service = Gene::load('Test_ServiceMock');
        $service->create($data);

        $result  = $service->setNamespace()->deleteconfirm($data);
        $this->assertFalse($result);
    }

    public function testdeleteconfirmを実行した際にvalidationエラーの場合falseを応答する()
    {
        Zend_Session::start(true);
        $data = array(
            'id' => null
        );
        $service = Gene::load('Test_ServiceMock');
        $result  = $service->setNamespace()->deleteconfirm($data);
        $this->assertFalse($result);
    }

    public function testdeleteconfirmが正常終了した場合入力した値を応答する()
    {
        Zend_Session::start(true);
        $data = array(
            'id' => '1'
        );
        $service = Gene::load('Test_ServiceMock');
        $result  = $service->setNamespace()->deleteconfirm($data);
        $this->assertSame($result, $data);
    }

    public function testdeleteを実行した際にdeleterulesを適応する()
    {
        Zend_Session::start(true);
        $data = array(
            'id' => null
        );
        $service = Gene::load('Test_ServiceMock');
        $result  = $service->setNamespace()->delete($data);
        $this->assertFalse($result);
    }

    public function testdeleteを実行した際にvalidationエラーの場合falseを応答する()
    {
        Zend_Session::start(true);
        $data = array(
            'id' => null
        );
        $service = Gene::load('Test_ServiceMock');
        $result  = $service->setNamespace()->delete($data);
        $this->assertFalse($result);
    }

    public function testdeleteが正常終了した場合実行結果を応答する()
    {
        Zend_Session::start(true);
        $data = array(
            'name' => 'test'
        );
        $service = Gene::load('Test_ServiceMock');
        $id      = $service->setNamespace()->create($data);
        $delete  = array(
            'id' => $id,
        );
        $result = $service->delete($delete);
        $this->assertSame($result, intval($id));
    }

    public function testdeleteが正常終了した場合データベースから削除する()
    {
        Zend_Session::start(true);
        $data = array(
            'name' => 'test'
        );
        $service = Gene::load('Test_ServiceMock');
        $result  = $service->setNamespace()->create($data);
        $delete  = array(
            'id' => $result
        );
        $service->delete($delete);

        $model = Gene::load('Test_Service_Zend');
        $row   = $model->fetchRow($model->select()->where('id = ?', $result));
        $this->assertSame($row, null);
    }

    public function testconfirm実行時にhookメソッドが動作する()
    {
        Zend_Session::start(true);
        $data = array(
            'name' => '!"#$%&'
        );
        $service = Gene::load('Test_BeforeHookMock');
        $result  = $service->setNamespace()->confirm($data);
        $buffer  = $service->methodResult;
        $this->assertSame($buffer, 'Test_BeforeHookMock::beforeConfirm');
    }

    public function testconfirm実行完了時にhookメソッドが動作する()
    {
        Zend_Session::start(true);
        $data = array(
            'name' => 'test'
        );
        $service = Gene::load('Test_AfterHookMock');
        $result  = $service->setNamespace()->confirm($data);
        $buffer  = $service->methodResult;
        $this->assertSame($buffer, 'Test_AfterHookMock::afterConfirm');
    }

    public function testcreate実行時にhookメソッドが動作する()
    {
        Zend_Session::start(true);
        $data = array(
            'name' => '!"#$%&'
        );
        $service = Gene::load('Test_BeforeHookMock');
        $result  = $service->setNamespace()->create($data);
        $buffer  = $service->methodResult;
        $this->assertSame($buffer, 'Test_BeforeHookMock::beforeCreate');
    }

    public function testcreate実行完了時にhookメソッドが動作する()
    {
        Zend_Session::start(true);
        $data = array(
            'name' => 'test'
        );
        $service = Gene::load('Test_AfterHookMock');
        $result  = $service->setNamespace()->create($data);
        $buffer  = $service->methodResult;
        $this->assertSame($buffer, 'Test_AfterHookMock::afterCreate');
    }

    public function testeditconfirm実行時にhookメソッドが動作する()
    {
        Zend_Session::start(true);
        $data = array(
            'name' => '!"#$%&'
        );
        $service = Gene::load('Test_BeforeHookMock');
        $result  = $service->setNamespace()->editconfirm($data);
        $buffer  = $service->methodResult;
        $this->assertSame($buffer, 'Test_BeforeHookMock::beforeEditconfirm');
    }

    public function testeditconfirm実行完了時にhookメソッドが動作する()
    {
        Zend_Session::start(true);
        $data = array(
            'name' => 'test'
        );
        $service = Gene::load('Test_AfterHookMock');
        $result  = $service->setNamespace()->editconfirm($data);
        $buffer  = $service->methodResult;
        $this->assertSame($buffer, 'Test_AfterHookMock::afterEditconfirm');
    }

    public function testupdate実行時にhookメソッドが動作する()
    {
        Zend_Session::start(true);
        $data = array(
            'name' => '!"#$%&'
        );
        $service = Gene::load('Test_BeforeHookMock');
        $result  = $service->setNamespace()->update($data);
        $buffer  = $service->methodResult;
        $this->assertSame($buffer, 'Test_BeforeHookMock::beforeUpdate');
    }

    public function testupdate実行完了時にhookメソッドが動作する()
    {
        Zend_Session::start(true);
        $data = array(
            'name' => 'test'
        );
        $service = Gene::load('Test_ServiceMock');
        $result  = $service->setNamespace()->create($data);
        $update  = array(
            'id'   => $result,
            'name' => 'hoge'
        );
        $service = Gene::load('Test_AfterHookMock');
        $result  = $service->setNamespace()->update($update);
        $buffer  = $service->methodResult;
        $this->assertSame($buffer, 'Test_AfterHookMock::afterUpdate');
    }

    public function testdeleteconfirm実行時にhookメソッドが動作する()
    {
        Zend_Session::start(true);
        $data = array(
            'name' => '!"#$%&'
        );
        $service = Gene::load('Test_BeforeHookMock');
        $result  = $service->setNamespace()->deleteconfirm($data);
        $buffer  = $service->methodResult;
        $this->assertSame($buffer, 'Test_BeforeHookMock::beforeDeleteconfirm');
    }

    public function testdeleteconfirm実行完了時にhookメソッドが動作する()
    {
        Zend_Session::start(true);
        $data = array(
            'name' => 'test'
        );
        $service = Gene::load('Test_AfterHookMock');
        $result  = $service->setNamespace()->deleteconfirm($data);
        $buffer  = $service->methodResult;
        $this->assertSame($buffer, 'Test_AfterHookMock::afterDeleteconfirm');
    }

    public function testdelete実行時にhookメソッドが動作する()
    {
        Zend_Session::start(true);
        $data = array(
            'name' => 'test'
        );
        $service = Gene::load('Test_ServiceMock');
        $result  = $service->setNamespace()->create($data);
        $delete  = array(
            'id' => $result
        );
        $service = Gene::load('Test_BeforeHookMock');
        $result  = $service->setNamespace()->delete($delete);
        $buffer  = $service->methodResult;
        $this->assertSame($buffer, 'Test_BeforeHookMock::beforeDelete');
    }

    public function testdelete実行完了時にhookメソッドが動作する()
    {
        Zend_Session::start(true);
        $data = array(
            'name' => 'test'
        );
        $service = Gene::load('Test_ServiceMock');
        $result  = $service->setNamespace()->create($data);
        $delete  = array(
            'id' => $result
        );
        $service = Gene::load('Test_AfterHookMock');
        $result  = $service->setNamespace()->delete($delete);
        $buffer  = $service->methodResult;
        $this->assertSame($buffer, 'Test_AfterHookMock::afterDelete');
    }

    public function test設定されたオブジェクトのメソッドがhookメソッド実行時に動作する()
    {
        Zend_Session::start(true);
        $data = array(
            'name' => 'test'
        );
        require_once 'var/Test/FooBarBaz.php';
        $service = Gene::load('Test_FooBarBaz');
        ob_start();
        $result  = $service->setNamespace()->confirm($data);
        $buffer  = ob_get_contents();
        ob_end_clean();
        $this->assertSame($buffer, 'Test_Foo::foo');
    }

    public function test設定されたオブジェクトのメソッドがhookメソッドが実行時に引数付きで動作する()
    {
        Zend_Session::start(true);
        $data = array(
            'name' => 'test'
        );
        require_once 'var/Test/FooBarBaz.php';
        $service = Gene::load('Test_FooBarBaz');
        ob_start();
        $result  = $service->setNamespace()->create($data);
        $buffer  = ob_get_contents();
        ob_end_clean();
        $this->assertSame($buffer, 'Test_Bar::foo');
    }

    public function test複数の設定した場合設定したメソッド数分動作する()
    {
        Zend_Session::start(true);
        $data = array(
            'name' => 'test'
        );
        require_once 'var/Test/FooBarBaz.php';
        $service = Gene::load('Test_FooBarBaz');
        ob_start();
        $result  = $service->setNamespace()->editconfirm($data);
        $buffer  = ob_get_contents();
        ob_end_clean();
        $this->assertSame($buffer, 'Test_Foo::fooTest_Baz::foo');
    }

    public function test設定されたクラスメソッドをhookメソッド実行時に動作する()
    {
        Zend_Session::start(true);
        $data = array(
            'name' => 'test'
        );
        $service = Gene::load('Test_ServiceMock');
        $result  = $service->setNamespace()->create($data);
        $update  = array(
            'id' => $result
        );
        require_once 'var/Test/FooBarBaz.php';
        $service = Gene::load('Test_FooBarBaz');
        ob_start();
        $result  = $service->setNamespace()->update($update);
        $buffer  = ob_get_contents();
        ob_end_clean();
        $this->assertSame($buffer, 'Test_Foo::barTest_Foo::bazTest_Foo::baz');
    }

    public function testbefore修飾子があるメソッドが実行前に動作する()
    {
        require_once 'var/Test/Aop.php';
        $service = Gene::load('Test_BeforeFoo');
        ob_start();
        $service->run('foo');
        $buffer  = ob_get_contents();
        ob_end_clean();
        $this->assertSame($buffer, 'beforeFoofoo');

        ob_start();
        $service->run('bar', array('foo'));
        $buffer  = ob_get_contents();
        ob_end_clean();
        $this->assertSame($buffer, 'beforeBarfoobar');
    }

    public function testafter修飾子があるメソッドが実行後に動作する()
    {
        require_once 'var/Test/Aop.php';
        $service = Gene::load('Test_AfterFoo');
        ob_start();
        $service->run('foo');
        $buffer  = ob_get_contents();
        ob_end_clean();
        $this->assertSame($buffer, 'fooafterFoo');

        ob_start();
        $service->run('bar', array('foo'));
        $buffer  = ob_get_contents();
        ob_end_clean();
        $this->assertSame($buffer, 'foobarafterBar');

    }

    public function testaround修飾子があるメソッドが実行メソッドを上書きする()
    {
        require_once 'var/Test/Aop.php';
        $service = Gene::load('Test_AroundFoo');
        ob_start();
        $service->run('foo');
        $buffer  = ob_get_contents();
        ob_end_clean();
        $this->assertSame($buffer, 'aroundFoo');

        ob_start();
        $service->run('bar', array('foo'));
        $buffer  = ob_get_contents();
        ob_end_clean();
        $this->assertSame($buffer, 'fooaroundBar');
    }
}
