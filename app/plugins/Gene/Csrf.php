<?php
/**
 * Csrf plugin
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
 * @category  Plugins
 * @package   Plugins_Gene
 * @version   $id$
 * @copyright 2009-2011 Shinya Ohyanagi
 * @author    Shinya Ohyanagi <sohyanagi@gmail.com>
 * @license   New BSD License
 */

/**
 * Csrf plugin
 *
 * @category  Plugins
 * @package   Plugins_Gene
 * @version   $id$
 * @copyright 2009-2011 Shinya Ohyanagi
 * @author    Shinya Ohyanagi <sohyanagi@gmail.com>
 * @license   New BSD License
 */
class Plugins_Gene_Csrf extends Zend_Controller_Plugin_Abstract
{
    /**
     * Token key storage
     *
     * @var    mixed
     * @access protected
     */
    protected $_session = null;

    /**
     * Form hidden name
     *
     * @var    string
     * @access protected
     */
    protected $_keyName = '_token';

    /**
     * Default timeout sec
     *
     * @var    float
     * @access protected
     */
    protected $_timeout = 300;

    /**
     * Constructor
     *
     * @access public
     * @return void
     */
    public function __construct()
    {
        $this->_session = new Zend_Session_Namespace(__CLASS__);
    }

    /**
     * Generate token key
     *
     * @param  string $type Generate type
     * @access public
     * @return mixed Token key
     */
    public function generateToken($type = 'session')
    {
        if ($type === 'session') {
            Zend_Session::regenerateId();
            $token = sha1(Zend_Session::getId());
        } else {
            $token = sha1(uniqid(mt_rand(), true));
        }
        $this->_session->token = $token;
        return $token;
    }

    /**
     * Get token
     *
     * @access public
     * @return mixed Token
     */
    public function getToken()
    {
        return isset($this->_session->token) ? $this->_session->token : null;
    }

    /**
     * Validate session
     *
     * @param  mixed $value
     * @access public
     * @return bool true:Token same, false:Token not same
     */
    public function validate($value)
    {
        if (!isset($this->_session->token)) {
            return false;
        }

        if ($value === $this->_session->token) {
            return true;
        }

        return false;
    }
}
