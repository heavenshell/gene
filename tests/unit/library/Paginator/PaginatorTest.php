<?php
/**
 * Gene_Paginator
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
 * @package   Gene_Paginator
 * @version   $id$
 * @copyright 2009-2011 Shinya Ohyanagi
 * @author    Shinya Ohyanagi <sohyanagi@gmail.com>
 * @license   New BSD License
 */

/**
 * @see prepare
 */
require_once dirname(dirname(dirname(__FILE__))) . '/prepare.php';

/**
 * Specs for Gene_Paginator
 *
 * @category  Gene
 * @package   Gene_Paginator
 * @version   $id$
 * @copyright 2009-2011 Shinya Ohyanagi
 * @author    Shinya Ohyanagi <sohyanagi@gmail.com>
 * @license   New BSD License
 */
class Gene_Paginatorの動作Test extends PHPUnit_Framework_TestCase
{
    public function testPaginatorを取得できる()
    {
        $template  = 'var/index.phtml';
        $paginator = new Gene_Paginator();
        $config = array(
            'page'     => 1,
            'count'    => 100,
            'perpage'  => 10,
            'template' => $template
        );
        $result = $paginator->getPaginator($config);
        $this->assertTrue($result instanceof Zend_Paginator);
    }

    public function testPaginator生成時にperpageを設定できる()
    {
        require_once 'var/Mock.php';
        $template  = 'var/index.phtml';
        $paginator = new Gene_Paginator_Mock(array('perpage' => 20));
        $this->assertSame($paginator->getPerpage(), 20);
    }

    public function testPaginator生成時にperpageを設定しない場合デフォルト値を適応する()
    {
        require_once 'var/Mock.php';
        $template  = 'var/index.phtml';
        $paginator = new Gene_Paginator_Mock();
        $this->assertSame($paginator->getPerpage(), 10);
    }

    public function testPaginator生成時にstyleを設定できる()
    {
        require_once 'var/Mock.php';
        $template  = 'var/index.phtml';
        $paginator = new Gene_Paginator_Mock(array('style' => 'Elastic'));
        $this->assertSame($paginator->getStyle(), 'Elastic');
    }

    public function testPaginator生成時にstyleを設定しない場合デフォルト値を適応する()
    {
        require_once 'var/Mock.php';
        $template  = 'var/index.phtml';
        $paginator = new Gene_Paginator_Mock();
        $this->assertSame($paginator->getStyle(), 'Sliding');
    }

    public function testPaginator生成時にtemplateを設定できる()
    {
        require_once 'var/Mock.php';
        $template  = 'var/index.phtml';
        $paginator = new Gene_Paginator_Mock(array('template' => $template));
        $this->assertSame($paginator->getTemplate(), $template);
    }

    public function testPaginator生成時にtemplateを設定しない場合nullを応答する()
    {
        require_once 'var/Mock.php';
        $template  = 'var/index.phtml';
        $paginator = new Gene_Paginator_Mock();
        $this->assertSame($paginator->getTemplate(), null);
    }

    public function testPaginator生成時にzend_confgを設定できる()
    {
        require_once 'var/Mock.php';
        $config    = dirname(__FILE__) . '/var/config.ini';
        $ini       = new Zend_Config_Ini($config);
        $paginator = new Gene_Paginator_Mock($ini);
        $this->assertSame($paginator->getPerpage(), 100);
        $this->assertSame($paginator->getStyle(), 'Elastic');
        $this->assertSame($paginator->getTemplate(), 'var/index.phtml');
    }

    public function testOffsetは数値を応答する()
    {
        $paginator = new Gene_Paginator();
        $offset    = $paginator->getOffset(1);
        $this->assertTrue(is_int($offset));
    }

    public function testPageが1の場合offsetは0を応答する()
    {
        $paginator = new Gene_Paginator();
        $offset    = $paginator->getOffset(1);
        $this->assertSame($offset, 0);
    }

    public function testPageが2の場合offsetは10を応答する()
    {
        $paginator = new Gene_Paginator();
        $offset    = $paginator->getOffset(2);
        $this->assertSame($offset, 10);
    }

    public function testPageが10の場合offsetは90を応答する()
    {
        $paginator = new Gene_Paginator();
        $offset    = $paginator->getOffset(10);
        $this->assertSame($offset, 90);
    }

    public function testLimitを指定せずpageを1に指定した場合0を応答する()
    {
        $paginator = new Gene_Paginator();
        $offset    = $paginator->getOffset(1);
        $this->assertSame($offset, 0);
    }

    public function testLimitに20を指定しpageを1に指定した場合0を応答する()
    {
        $paginator = new Gene_Paginator();
        $offset    = $paginator->getOffset(1, 20);
        $this->assertSame($offset, 0);
    }

    public function testLimitに20を指定しpageを2に指定した場合20を応答する()
    {
        $paginator = new Gene_Paginator();
        $offset    = $paginator->getOffset(2, 20);
        $this->assertSame($offset, 20);
    }

    public function testLimitに10を指定しpageを20に指定した場合180を応答する()
    {
        $paginator = new Gene_Paginator();
        $offset    = $paginator->getOffset(10, 20);
        $this->assertSame($offset, 180);
    }
}
