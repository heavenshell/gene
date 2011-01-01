<?php
/**
 * Abstract paginator
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
 * Gene_Paginator
 *
 * @category  Gene
 * @package   Gene_Paginator
 * @version   $id$
 * @copyright 2009-2011 Shinya Ohyanagi
 * @author    Shinya Ohyanagi <sohyanagi@gmail.com>
 * @license   New BSD License
 */
abstract class Gene_Paginator_Abstract
{
    /**
     * Paginator perpage
     *
     * @var    float
     * @access protected
     */
    protected $_perpage = 10;

    /**
     * Path to paginator template
     *
     * @var    mixed
     * @access protected
     */
    protected $_template = null;

    /**
     * Paginator style
     *
     * @var    string
     * @access protected
     */
    protected $_style = 'Sliding';

    /**
     * Constructor
     *
     * @access public
     * @return void
     */
    public function __construct($config = null)
    {
        if ($config instanceof Zend_Config) {
            if (isset($config->perpage) && is_numeric($config->perpage)) {
                $this->_perpage = intval($config->perpage);
            }
            if (isset($config->template)) {
                $this->_template = $config->template;
            }
            if (isset($config->style)) {
                $this->_style = $config->style;
            }
        } else if (is_array($config)) {
            if (isset($config['perpage']) && is_numeric($config['perpage'])) {
                $this->_perpage = intval($config['perpage']);
            }
            if (isset($config['template'])) {
                $this->_template = $config['template'];
            }
            if (isset($config['style'])) {
                $this->_style = $config['style'];
            }
        }
    }

    /**
     * Get offset
     *
     * @param  mixed $page Page number
     * @param  mixed $limit Limit
     * @access protected
     * @return int Offset
     */
    public function getOffset($page, $limit = 10)
    {
        if (!is_numeric($page)) {
            return 0;
        }
        $offset = (intval($page) - 1) * $limit;
        return $offset;
    }

    /**
     * Get paginator
     *
     * @param  mixed $page Page id
     * @param  mixed $count Total count
     * @param  mixed $perPage Perpage
     * @param  mixed $path Path to template
     * @access public
     * @return Zend_Paginator Paginator object
     */
    abstract public function getPaginator(array $value);
}
