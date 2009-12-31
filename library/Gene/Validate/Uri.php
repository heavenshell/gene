<?php
/**
 * Validate Uri
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
 * @package   Gene_Validate
 * @version   $id$
 * @copyright 2009-2010 Shinya Ohyanagi
 * @author    Shinya Ohyanagi <sohyanagi@gmail.com>
 * @license   New BSD License
 */

/**
 * Validate uri
 *
 * <pre>
 *   This is a simple validation for uri.
 * </pre>
 *
 * @category  Gene
 * @package   Gene_Validate
 * @version   $id$
 * @copyright 2009-2010 Shinya Ohyanagi
 * @author    Shinya Ohyanagi <sohyanagi@gmail.com>
 * @license   New BSD License
 */
class Gene_Validate_Uri extends Zend_Validate_Abstract
{
    /**
     * Error key
     */
    const NOT_URL = 'notUrl';

    /**
     * Message templates
     *
     * @var    array
     * @access protected
     */
    protected $_messageTemplates = array(
        self::NOT_URL => "'%value%' does not appear to be a valid url"
    );


    /**
     * Defined by Zend_Validate_Interface
     *
     * @param  string $value
     * @return bool
     */
    public function isValid($value)
    {
        $valueString = (string) $value;
        $this->_setValue($valueString);

        if (!Zend_Uri::check($valueString)) {
            $this->_error(self::NOT_URL);
            return false;
        }

        return true;
    }
}
