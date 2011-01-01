<?php
/**
 * Mail
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
 * @package   Gene_Mail
 * @version   $id$
 * @copyright 2009-2011 Shinya Ohyanagi
 * @author    Shinya Ohyanagi <sohyanagi@gmail.com>
 * @license   New BSD License
 */

/**
 * Mail
 *
 * @category  Gene
 * @package   Gene_Mail
 * @version   $id$
 * @copyright 2009-2011 Shinya Ohyanagi
 * @author    Shinya Ohyanagi <sohyanagi@gmail.com>
 * @license   New BSD License
 */
class Gene_Mail extends Zend_Mail
{
    /**
     * Default charset
     */
    const DEFAULT_CAHRSET = 'ISO-2022-JP';

    /**
     * Constructor
     *
     * @param array $options File upload options
     * @access public
     * @return void
     */
    public function __construct($charset = self::DEFAULT_CAHRSET)
    {
        parent::__construct($charset);
    }

    /**
     * Sets the subject of the message
     *
     * <pre>
     *   Convert encoding and call parent class method.
     * </pre>
     *
     * @param  string $value Subject
     * @access public
     * @return Zend_Mail Fluent inteface
     */
    public function setSubject($value)
    {
        $value = $this->_encodeMailHeader($value);
        return parent::setSubject($value);
    }

    /**
     * Sets the text body for the message
     *
     * <pre>
     *   Convert encoding and call parent class method.
     * </pre>
     *
     * @param  mixed $txt Body text
     * @param  mixed $charset Charaset
     * @param  mixed $encoding Encoding
     * @access public
     * @return Zend_Mail Fluent interface
     */
    public function setBodyText($txt, $charset = self::DEFAULT_CAHRSET, $encoding = Zend_Mime::ENCODING_7BIT)
    {
        $txt = mb_convert_encoding($txt, self::DEFAULT_CAHRSET, mb_detect_encoding($txt));
        return parent::setBodyText($txt, $charset, $encoding);
    }

    /**
     * Encode mail header and delete "\r","\n"."\t"
     *
     * @see    http://nonn-et-twk.net/twk/node/155
     * @param  mixed $value Subject
     * @access private
     * @return string Encoded string
     */
    private function _encodeMailHeader($value)
    {
        $value = mb_encode_mimeheader($value, self::DEFAULT_CAHRSET);
        $value = strtr($value, array("\r" => '', "\n" => '', "\t" => ''));
        return $value;
    }
}
