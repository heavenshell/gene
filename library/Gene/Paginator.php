<?php
/**
 * Paginator
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
 * <pre>
 *   This is a wrapper class of Zend_Paginator_Adapter_Null.
 *   If you want to use another adaper, such as Zend_Paginator_Adapter_Array,
 *   extend Gene_Paginator_Abstract.
 * </pre>
 *
 * @category  Gene
 * @package   Gene_Paginator
 * @version   $id$
 * @copyright 2009-2011 Shinya Ohyanagi
 * @author    Shinya Ohyanagi <sohyanagi@gmail.com>
 * @license   New BSD License
 */
class Gene_Paginator extends Gene_Paginator_Abstract
{
    /**
     * Get paginator
     *
     * @param  array $config
     * @throws Gene_Paginator_Exception Count not exists
     * @access public
     * @return Zend_Paginator Paginator object
     */
    public function getPaginator(array $config)
    {
        if (!isset($config['count'])) {
            throw new Gene_Paginator_Exception();
        }
        $page = 1;
        if (isset($config['page'])) {
            $page = $config['page'];
        }
        if (isset($config['perpage']) && !is_null($config['perpage'])) {
            $perpage = $config['perpage'];
        } else {
            $perpage = $this->_perpage;
        }
        if (isset($config['template']) && !is_null($config['template'])) {
            $template = $config['template'];
        } else {
            $template = $this->_template;
        }
        if (isset($config['style'])) {
            $style = $config['style'];
        } else {
            $style = $this->_style;
        }

        // Create paginator
        $adapter   = new Zend_Paginator_Adapter_Null($config['count']);
        $paginator = new Zend_Paginator($adapter);
        $paginator->setCurrentPageNumber($page)
                  ->setItemCountPerPage($perpage);
        Zend_Paginator::setDefaultScrollingStyle($style);
        Zend_View_Helper_PaginationControl::setDefaultViewPartial($template);

        return $paginator;
    }
}
