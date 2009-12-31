<?php
/**
 * Smarty
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
 * @package   Gene_Smarty
 * @version   $id$
 * @copyright 2009-2010 Shinya Ohyanagi
 * @author    Shinya Ohyanagi <sohyanagi@gmail.com>
 * @license   New BSD License
 */

/**
 * @see Smary
 */
require_once 'Smarty/Smarty.class.php';

/**
 * Smarty
 *
 * @category  Gene
 * @package   Gene_Smarty
 * @version   $id$
 * @copyright 2009-2010 Shinya Ohyanagi
 * @author    Shinya Ohyanagi <sohyanagi@gmail.com>
 * @license   New BSD License
 */
class Gene_Smarty_View extends Zend_View_Abstract
{
    /**
     * Instances of Smarty
     *
     * @var    mixed
     * @access private
     */
    private $_view = null;

    /**
     * Constructor
     *
     * @param  mixed $config Config settings
     * @access public
     * @return void
     */
    public function __construct($config = null)
    {
        // Create Smarty instance
        $this->_view = new Smarty();

        if (isset($config)) {
            if ($config instanceof Zend_Config) {
                $config = $config->toArray();
            }
            foreach ($config as $key => $val) {
                $this->_view->{$key} = $val;
            }
        }
    }

    /**
     * Get Smarty instance
     *
     * @access public
     * @return Smarty Instance of Smarty
     */
    public function getEngine()
    {
        return $this->_view;
    }

    /**
     * assign
     *
     * @param  string|array The assignment strategy to use.
     * @param  mixed (Optional) If assigning a named variable, use this
     * as the value.
     * @access public
     * @return Gene_Smarty_View Fluent interface
     */
    public function assign($spec, $value = null)
    {
        if (is_array($spec)) {
            $this->_view->assign($spec);
            return;
        }
        $this->_view->assign($spec, $value);
        return $this;
    }

    /**
     * Render view
     *
     * @param  mixed $name Template name
     * @access public
     * @return string Result of renderer
     */
    public function render($name)
    {
        $file   = $this->_script($name);
        $render = $this->_view->fetch($file);

        return $render;
    }

    /**
     * Includes the view script in a scope with only public $this variables.
     *
     * @param string The view script to execute
     */
    protected function _run()
    {
    }
}
