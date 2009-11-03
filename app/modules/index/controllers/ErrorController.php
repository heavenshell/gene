<?php
/**
 * ErrorController
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
 * @category  Index
 * @package   Index_Controller
 * @version   $id$
 * @copyright 2009 Shinya Ohyanagi
 * @author    Shinya Ohyanagi <sohyanagi@gmail.com>
 * @license   New BSD License
 */

/**
 * ErrorController
 *
 * @category  Index
 * @package   Index_Controller
 * @version   $id$
 * @copyright 2009 Shinya Ohyanagi
 * @author    Shinya Ohyanagi <sohyanagi@gmail.com>
 * @license   New BSD License
 */
class ErrorController extends Zend_Controller_Action
{
    /**
     * errorAction
     *
     * @access public
     * @return void
     */
    public function errorAction()
    {
        $handler = $this->_getParam('error_handler');
        switch ($handler->type) {
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_CONTROLLER:
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ACTION:
                $this->getResponse()->setRawHeader('HTTP/1.1 404 Not Found')
                                    ->setHttpResponseCode(404);

                $error['message'] = '404 Page not found.';
                break;

            default:
                break;
        }
        $this->_traceback($handler);
        $this->view->assign('errors', $error);
    }

    /**
     * Traceback
     *
     * @param  ArrayObject $handler
     * @access private
     * @return ErrorController Fluent interface
     */
    private function _traceback(ArrayObject $handler)
    {
        $env       = $this->getInvokeArg('bootstrap')->getEnvironment();
        $exception = $handler->exception;
        $error     = array(
            'message' => $exception->getMessage(),
            'code'    => $exception->getCode(),
            'file'    => $exception->getFile(),
            'line'    => $exception->getLine()
        );
        $errors     = $exception->getTrace();
        $traceArray = array();
        if (is_array($errors)) {
            foreach ($errors as $key => $val) {
                $traceArray[$key]['file']     = $val['file'];
                $traceArray[$key]['line']     = $val['line'];
                $traceArray[$key]['class']    = $val['class'];
                $traceArray[$key]['type']     = $val['type'];
                $traceArray[$key]['function'] = $val['function'];
            }
        }

        if (strtolower($env) === 'production') {
            // Convert array to string format
            $errorMessage = print_r($error, true);
            $traceMessage = print_r($traceArray, true);
            $log = Gene::getParams('log');
            $log->write($errorMessage)->write($traceMessage);
        } else {
            $this->view->assign('traces', $traceArray);
        }
        return $this;
    }
}
