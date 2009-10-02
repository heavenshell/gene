<?php
/**
 * Base class of table class
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
 * @category  Models
 * @package   Models_Tables
 * @version   $id$
 * @copyright 2009 Shinya Ohyanagi
 * @author    Shinya Ohyanagi <sohyanagi@gmail.com>
 * @license   New BSD License
 */

/**
 * Models_Tables
 *
 * @category  Models
 * @package   Models_Tables
 * @version   $id$
 * @copyright 2009 Shinya Ohyanagi
 * @author    Shinya Ohyanagi <sohyanagi@gmail.com>
 * @license   New BSD License
 */
class Models_Tables extends Zend_Db_Table_Abstract
{
    /**
     * Primary key
     *
     * @var    string
     * @access protected
     */
    protected $_primary = 'id';

    /**
     * Insert
     *
     * @param  array $params
     * @access public
     * @return bool true:Success to inset, false:Fail to insert
     */
    public function insertData(array $params)
    {
        $this->_db->beginTransaction();
        try {
            $id = $this->insert($params);
        } catch(Exception $e) {
            $this->_db->rollBack();
            return false;
        }
        $ret = $this->_db->commit();
        return $id;
    }


    /**
     * Upadate
     *
     * @param  array $params
     * @access public
     * @return mixed Update count or return false when fail to update
     */
    public function updateData(array $params)
    {
        if (!isset($params[$this->_primary])) {
            return false;
        }

        $where = $this->_db->quoteInto('id = ?', $params[$this->_primary]);
        $this->_db->beginTransaction();

        $params['updated_at'] = isset($params['updated_at'])
                              ? $params['updated_at']
                              : $this->datetime();

        try {
            $ret = $this->update($params, $where);
        } catch(Exception $e) {
            $this->_db->rollBack();
            return false;
        }

        $this->_db->commit();
        if ($ret === false || $ret === 0) {
            return false;
        }

        return $ret;
    }

    /**
     * Delete user
     *
     * @param  array $params
     * @access public
     * @return bool true:Success to delte, false:Fail to delete
     */
    public function deleteData(array $params)
    {
        if (!isset($params[$this->_primary])) {
            return false;
        }

        $where = $this->_db->quoteInto('id = ?', $params[$this->_primary]);
        $this->_db->beginTransaction();

        try {
            $ret = $this->delete($where);
        } catch(Exception $e) {
            $this->_db->rollBack();
            return false;
        }

        $this->_db->commit();
        if ($ret === false || $ret === 0) {
            return false;
        }

        return $ret;
    }

    /**
     * Soft deleteable
     *
     * @param  array $params
     * @access public
     * @return bool true:Success to update, false:Fail to update
     */
    public function softDeleteable(array $params)
    {
        $where = $this->_db->quoteInto('id = ?', $params[$this->_primary]);
        $this->_db->beginTransaction();

        $updated_at = isset($params['updated_at'])
                    ? $params['updated_at']
                    : strftime('%Y-%m-%d %H:%M:%S', $this->timestamp());
        $data = array(
            'delete_flag' => 1,
            'updated_at'  => $updated_at
        );
        try {
            $ret = $this->update($data, $where);
        } catch(Exception $e) {
            $this->_db->rollBack();
            return false;
        }
        $this->_db->commit();
        if ($ret === false || $ret === 0) {
            return false;
        }

        return $ret;
    }

    /**
     * Find all
     *
     * @access public
     * @return Zend_Db_Table_Abstract Data set
     */
    public function findAll($limit = null, $offset = null, $order = array('id'))
    {
        $select = $this->select()->where('delete_flag = ?', 0);
        if (!is_null($offset) && !is_null($limit)) {
            $select->limit($limit, $offset);
        }
        $select->order($order);

        return $this->fetchAll($select);
    }

    /**
     * Find by conditions
     *
     * @param  array $params
     * @access public
     * @return Zend_Db_Table_Abstract Data set
     */
    public function findByConditions(array $params, $limit = null, $offset = null, $order = array('id'))
    {
        $select = $this->select();
        foreach ($params as $key => $val) {
            $select->where($key . ' = ?', $val);
        }
        if (!is_null($limit) && !is_null($offset)) {
            $select->limit($limit, $offset);
        }
        $select->order($order);

        return $this->fetchAll($select);
    }

    /**
     * Find user data by id
     *
     * @param  mixed $id Id
     * @access public
     * @return Zend_Db_Table_Abstract Fluent interface
     */
    public function findById($id)
    {
        $select = $this->select()->where('id = ?', $id)
                                 ->where('delete_flag = ?', 0);

        return $this->fetchRow($select);
    }

    /**
     * Get date time
     *
     * @param  string $format Format string
     * @access public
     * @return string Datetime
     */
    public function datetime($format = '%Y-%m-%d %H:%M:%S')
    {
        return strftime($format, time());
    }
}
