<?php
/**
 * Service
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
 * Gene_Service
 *
 * @category  Gene
 * @package   Gene_Service
 * @version   $id$
 * @copyright 2009-2010 Shinya Ohyanagi
 * @author    Shinya Ohyanagi <sohyanagi@gmail.com>
 * @license   New BSD License
 */
class Gene_Service extends Gene_Service_Abstract
{
    /**
     * Validator
     *
     * @var    mixed
     * @access protected
     */
    protected $_validator = null;

    /**
     * Application translate object
     *
     * @var mixed
     * @access protected
     */
    protected $_apptranslate = null;

    /**
     * Session
     *
     * @var mixed
     * @access protected
     */
    protected $_session = null;

    /**
     * Run before
     *
     * @var    mixed
     * @access protected
     */
    protected $_before = array(
        'confirm'      => null,
        'create'       => null,
        'editconfirm'  => null,
        'update'       => null,
        'delteconfirm' => null,
        'delete'       => null
    );

    /**
     * Run after
     *
     * @var    array
     * @access protected
     */
    protected $_after = array(
        'confirm'      => null,
        'create'       => null,
        'editconfirm'  => null,
        'update'       => null,
        'delteconfirm' => null,
        'delete'       => null
    );

    /**
     * Run method
     *
     * <pre>
     *   This is a simple aop implemention.
     *   There are three advice method.
     *   1. before : Run before given method
     *   2. after  : Run after given method
     *   3. around : Override given method
     * </pre>
     *
     * @param  mixed $method Method name
     * @param  array $args Arguments to set method
     * @access public
     * @return mixed Execution of method
     */
    public function run($method, array $args = array())
    {
        $name = ucfirst($method);
        if (method_exists($this, 'before' . $name)) {
            $this->_before[$method] = 'before' . $name;
            $this->_before($method);
        }

        $around = false;
        if (method_exists($this, 'around' . $name)) {
            // If around + $method exists, override $method by aroud + $method.
            $around  = true;
            $default = $method;
            $method  = 'around' . $name;
        }

        $ret = null;
        if (count($args) > 0) {
            $ret = call_user_func_array(array($this, $method), $args);
        } else {
            $ret = $this->$method();
        }

        if ($around === true) {
            $method = $default;
        }

        if (method_exists($this, 'after' . $name)) {
            $this->_after[$method] = 'after' . $name;
            $this->_after($method);
        }

        return $ret;
    }

    /**
     * Get validator
     *
     * @param  mixed $name Validator class name
     * @param  string $appmessage Application message file name
     * @access public
     * @return mixed Validation class
     */
    public function getValidator($name, $appmessage = 'message.ini')
    {
        $system = $this->getSystemTranslate();
        $app    = $this->getTranslate($appmessage);
        $valid  = Gene::load($name);
        $valid->setValidatorTranslate($system)->setAppTranslate($app);
        $this->_apptranslate = $app;
        return $valid;
    }

    /**
     * Set default validation rules
     *
     * <pre>
     *   If you want to use an another rule, override this method in sub cluss.
     * </pre>
     *
     * @param  array $params
     * @access public
     * @return Gene_Service Fluent interface
     */
    public function setRules($name = null, array $params = array())
    {
        $session = isset($params['session']) ? $params['session'] : null;
        $id      = isset($params['id']) ? $params['id'] : null;
        $adapter = isset($params['adapter']) ? $params['adapter'] : null;
        if (!is_null($name) && method_exists($this->_validator, $name)) {
            $this->_validator->$name($session, $id, $adapter);
        } else {
            $this->_validator->setRules($session, $id, $adapter);
        }

        return $this;
    }

    /**
     * Verify data
     *
     * @param  array $params
     * @param  mixed $id
     * @param  mixed $adapter
     * @access public
     * @return true:Validat ok, false:Validat ng
     */
    public function isValid(array $params)
    {
        $ret = $this->_validator->isValid($params);
        if ($ret === false) {
            $this->setMessages($this->_validator->getErrorMessages());
            return false;
        }

        return true;
    }

    /**
     * Run before
     *
     * @param  mixed $name Method name
     * @access protected
     * @return mixed
     */
    protected function _before($name)
    {
        if (isset($this->_before[$name])
                && !is_null($this->_before[$name])) {
            if (is_array($this->_before[$name])) {
                $values = $this->_before[$name];
                foreach ($values as $key => $val) {
                    if (is_array($val) && count($val[0]) > 1) {
                        $ret = call_user_func_array($val[0], $val[1]);
                    } else {
                        $ret = call_user_func($val);
                    }
                }
                return $ret;
            }
            $method = $this->_before[$name];
            if (!method_exists($this, $method)) {
                return false;
            }
            return $this->$method();
        }
        return true;
    }

    /**
     * Run after
     *
     * @param  mixed $name Method name
     * @access protected
     * @return mixed
     */
    protected function _after($name)
    {
        if (isset($this->_after[$name])
                && !is_null($this->_after[$name])) {
            if (is_array($this->_after[$name])) {
                $values = $this->_after[$name];
                foreach ($values as $key => $val) {
                    if (is_array($val) && count($val[0]) > 1) {
                        $ret = call_user_func_array($val[0], $val[1]);
                    } else {
                        $ret = call_user_func($val);
                    }
                }
                return $ret;
            }
            $method = $this->_after[$name];
            if (!method_exists($this, $method)) {
                return false;
            }
            return $this->$method();
        }
        return true;
    }

    /**
     * Create
     *
     * <pre>
     *   If you do not want to use doCreate(),
     *   override this method in subclass.
     * </pre>
     *
     * @param  array $params
     * @access protected
     * @return mixed Result of dao's create
     */
    protected function _create(array $params)
    {
        return $this->_dao->doCreate($params);
    }

    /**
     * Update
     *
     * <pre>
     *   If you do not want to use doUpdate(),
     *   override this method in subclass.
     * </pre>
     *
     * @param  array $params
     * @access protected
     * @return mixed Request of dao's update
     */
    protected function _update(array $params)
    {
        return $this->_dao->doUpdate($params);
    }

    /**
     * Delete
     *
     * <pre>
     *   If you do not want to use doDelete(),
     *   override this method in subclass.
     * </pre>
     *
     * @param  array $params
     * @access protected
     * @return mixed Result of dao's delete
     */
    protected function _delete(array $params)
    {
        return $this->_dao->doDelete($params);
    }

    /**
     * Input form data
     *
     * @access public
     * @return mixed Session data if exists
     */
    public function input()
    {
        $data = $this->_session->get('create', $this->_session->getNamespace());
        if ($data === null) {
            return null;
        }

        return $data;
    }

    /**
     * Confirm create
     *
     * @params array $params Request data
     * @access public
     * @return mixed Request data or false, validation failed
     */
    public function confirm(array $params)
    {
        if ($this->_before(__FUNCTION__) === false) {
            return false;
        }

        $namespace = $this->_session->getNamespace();
        if ($namespace === null) {
            throw new UnexpectedValueException('Session namespace does not set.');
        }

        if (Zend_Session::namespaceIsset($namespace) === true) {
            $this->_session->remove($namespace);
        }
        $this->_session->set('create', $params, $namespace);

        $this->setRules('createRules', $params);
        if ($this->isValid($params) === false) {
            return false;
        }

        if ($this->_after(__FUNCTION__) === false) {
            return false;
        }

        return $params;
    }

    /**
     * Create
     *
     * @param  array $params Request data
     * @access public
     * @return mixed Result of dao execution
     */
    public function create(array $params)
    {
        if ($this->_before(__FUNCTION__) === false) {
            return false;
        }

        $this->setRules('createRules', $params);
        if ($this->isValid($params) === false) {
            return false;
        }

        $ret = $this->_create($params);
        if ($ret === false) {
            $this->setMessages($this->_apptranslate->_('Fail to create.'));
            return false;
        }

        $namespace = $this->_session->getNamespace();
        if (Zend_Session::namespaceIsset($namespace) === true) {
            $this->_session->remove($namespace);
        }

        if ($this->_after(__FUNCTION__) === false) {
            return false;
        }

        return $ret;
    }

    /**
     * Edit confirm
     *
     * @param  array $params Request data
     * @access public
     * @return mixed Result of dao execution
     */
    public function editconfirm(array $params)
    {
        if ($this->_before(__FUNCTION__) === false) {
            return false;
        }

        $namespace = $this->_session->getNamespace();
        if ($namespace === null) {
            throw new UnexpectedValueException('Session namespace does not set.');
        }

        if (Zend_Session::namespaceIsset($namespace) === true) {
            $this->_session->remove($namespace);
        }
        $this->_session->set('update', $params, $namespace);

        $this->setRules('updateRules', $params);
        if ($this->isValid($params) === false) {
            return false;
        }

        if ($this->_after(__FUNCTION__) === false) {
            return false;
        }

        return $params;
    }

    /**
     * Update
     *
     * @param  array $params Request data
     * @access public
     * @return mixed Result of dao execution
     */
    public function update(array $params)
    {
        if ($this->_before(__FUNCTION__) === false) {
            return false;
        }

        $this->setRules('updateRules', $params);
        if ($this->isValid($params) === false) {
            return false;
        }

        $ret = $this->_update($params);
        if ($ret === false) {
            $this->setMessages($this->_apptranslate->_('Fail to update.'));
            return false;
        }

        $namespace = $this->_session->getNamespace();
        if (Zend_Session::namespaceIsset($namespace) === true) {
            $this->_session->remove($namespace);
        }

        if ($this->_after(__FUNCTION__) === false) {
            return false;
        }

        return $ret;
    }

    /**
     * Delete confirm
     *
     * @param  array $params Request data
     * @access public
     * @return mixed Result of dao execution
     */
    public function deleteconfirm($params)
    {
        if ($this->_before(__FUNCTION__) === false) {
            return false;
        }

        $namespace = $this->_session->getNamespace();
        if ($namespace === null) {
            throw new UnexpectedValueException('Session namespace does not set.');
        }

        if (Zend_Session::namespaceIsset($namespace) === true) {
            $this->_session->remove($namespace);
        }

        $this->_session->set('delete', $params, $namespace);

        $this->setRules('deleteRules', $params);
        if ($this->isValid($params) === false) {
            return false;
        }

        if ($this->_after(__FUNCTION__) === false) {
            return false;
        }

        return $params;
    }

    /**
     * Delete
     *
     * @param  array $params Request data
     * @access public
     * @return mixed Result of dao execution
     */
    public function delete(array $params)
    {
        if ($this->_before(__FUNCTION__) === false) {
            return false;
        }

        $this->setRules('deleteRules', $params);
        if ($this->isValid($params) === false) {
            return false;
        }

        $ret = $this->_delete($params);
        if ($ret === false) {
            $this->setMessages($this->_apptranslate->_('Fail to update.'));
            return false;
        }

        if ($this->_after(__FUNCTION__) === false) {
            return false;
        }

        $namespace = $this->_session->getNamespace();
        if (Zend_Session::namespaceIsset($namespace) === true) {
            $this->_session->remove($namespace);
        }

        return $ret;
    }
}
