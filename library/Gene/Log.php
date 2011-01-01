<?php
/**
 * Log
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
 * @package   Gene_Log
 * @version   $id$
 * @copyright 2009-2011 Shinya Ohyanagi
 * @author    Shinya Ohyanagi <sohyanagi@gmail.com>
 * @license   New BSD License
 */

/**
 * Gene_Log
 *
 * @category  Gene
 * @package   Gene_Log
 * @version   $id$
 * @copyright 2009-2011 Shinya Ohyanagi
 * @author    Shinya Ohyanagi <sohyanagi@gmail.com>
 * @license   New BSD License
 */
class Gene_Log extends Zend_Log
{
    /**
     * Config options for Zend_Log
     *
     * @var    mixed
     * @access private
     */
    private $_config = null;

    /**
     * _logger
     *
     * @var    array
     * @access private
     */
    private $_logger = array();

    /**
     * Mail instance
     *
     * @var    mixed
     * @access private
     */
    private $_mail = null;

    /**
     * Constructor
     *
     * @access private
     * @return void
     */
    public function __construct($config = null)
    {
        if (!is_null($config)) {
            $this->setConfig($config);
        }
    }

    /**
     * Set config
     *
     * @param  mixed $config Configs for create logger
     * @access public
     * @return Gene_Log Fluent interface
     */
    public function setConfig($config)
    {
        if ($config instanceof Zend_Config) {
            $config = $config->toArray();
        }

        $this->_config = $config;
        return $this;
    }

    /**
     * Create logger
     *
     * @param  string $namespace Namespace of logger
     * @param  string $type Type of logger
     * @param  mixed $config Configs for create logger
     * @access public
     * @return Zend_Log Logger
     */
    public function createLogger($namespace = 'default', $type = 'file', $config = null)
    {
        if (!is_null($config)) {
            $this->_config = $config;
        }

        if (strtolower($type) === 'db') {
            $logger = $this->createDb($namespace, $this->_config);
        } else {
            $logger = $this->createFile($namespace, $this->_config);
        }

        // If mail option is enabled, create Zend_Log_Writer_Mock instance.
        if (isset($this->_config['mail'])) {
            if (!isset($this->_logger['mock'])) {
                $this->_logger['mock'] = new Zend_Log_Writer_Mock();
            }
            $logger->addWriter($this->_logger['mock']);
        }

        return $logger;
    }

    /**
     * Remove logger instance
     *
     * @param  string $namespace Namespace of logger
     * @param  string $type
     * @access public
     * @return Gene_Log Fluent interface
     */
    public function removeLogger($namespace = 'default', $type = 'file')
    {
        if (array_key_exists($this->_logger[$type][$namespace], $this->_logger)) {
            unset($this->_logger[$type][$namespace]);
        }

        return $this;
    }

    /**
     * Create file logger
     *
     * @param  string $namespace Config namespace
     * @param  mixed $config Configs for create logger
     * @access public
     * @throws Gene_Log_Exception
     * @return Zend_Log Instance of logger
     */
    public function createFile($namespace = 'default', $config = null)
    {
        if (!is_null($config)) {
            $this->_config = $config;
        }

        if (isset($this->_logger['file'][$namespace])) {
            return $this->_logger['file'][$namespace];
        }

        $options = null;
        if (isset($this->_config['file'][$namespace])) {
            $options = $this->_config['file'][$namespace];
        } else {
            throw new Gene_Log_Exception('Config not found.');
        }

        if (!isset($options['path'])
                || !is_writable($options['path'])) {
            throw new Gene_Log_Exception('Log file path invalid.');
        }

        if (!isset($options['name'])) {
            $options['name'] = 'debug';
        }

        if (isset($options['suffix'])) {
            $options['suffix'] = '.' . ltrim($options['suffix'], '.');
        } else {
            $options['suffix'] = '.log';
        }


        $path   = $options['path'] . $options['name'] . $options['suffix'];
        $writer = new Zend_Log_Writer_Stream($path);
        $logger = new Zend_Log($writer);

        // Cache logger instance
        $this->_logger['file'][$namespace] = $logger;

        return $logger;
    }

    /**
     * Create db logger
     *
     * @param  mixed $config Database option
     * @access public
     * @throws Gene_Log_Exception
     * @return Zend_Log_Writer_Db
     */
    public function createDb($namespace = 'default', $config = null)
    {
        if (is_null($config)) {
            $config = $this->_config['db'];
        }
        if (!isset($this->_config['db'])) {
            throw new Gene_Log_Exception('Db setting invalid.');
        }

        if (isset($this->_logger['db'][$namespace])) {
            return $this->_logger['db'][$namespace];
        }

        if (!class_exists('Zend_Loader', false)) {
            require_once 'Zend_Loader';
        }
        Zend_Loader::loadClass('Zend_Db');

        if (!isset($config['db']['adapter'])) {
            throw new Gene_Log_Exception('Db adapter invalid.');
        }

        $adapter = $config['db']['adapter'];
        if (!isset($config['db']['param'])) {
            throw new Gene_Log_Exception('Db param invalid.');
        }

        $params  = $config['db']['param'];
        $db      = Zend_Db::factory($adapter, $params);

        $table   = null;
        if (isset($config['db']['table']['name'])) {
            $table = $config['db']['table']['name'];
        } else {
            throw new Gene_Log_Exception('Table name not found.');
        }

        $column = null;
        if (isset($config['db']['column'])
                && is_array($config['db']['column'])) {
            foreach ($config['db']['column'] as $key => $val) {
                $column[$key] = $val;
            }
        } else {
            throw new Gene_Log_Exception('Colmun not found.');
        }

        $writer  = new Zend_Log_Writer_Db($db, $table, $column);
        $logger  = new Zend_Log($writer);
        $this->_logger['db'] = $logger;

        return $logger;
    }

    /**
     * Get logger
     *
     * @param  string $type Logger type
     * @access public
     * @return Zend_Log_Writer Logger
     */
    public function getLogger($namespace = 'default', $type = 'file')
    {
        if ($type === 'file') {
            return isset($this->_logger['file'][$namespace])
                   ? $this->_logger['file'][$namespace]
                   : null;
        }

        return isset($this->_logger[$type]) ? $this->_logger[$type] : null;
    }

    /**
     * Convert to string
     *
     * @param  mixed $value
     * @access public
     * @return string Converted string
     */
    public function toString($value)
    {
        return print_r($value, true);
    }

    /**
     * Write to log
     *
     * @param  mixed $value Message to log
     * @param  mixed $priority Priority of message
     * @param  string $namespace Namespace of logger
     * @param  string $type Logger type
     * @access public
     * @return Gene_Log Fluent interface
     */
    public function write($value, $priority = Zend_Log::INFO, $namespace = 'default', $type = 'file')
    {
        if (is_array($value) || is_object($value)) {
            $value = $this->toString($value);
        }

        $logger = $this->getLogger($type, $namespace);
        if (is_null($logger)) {
            $logger = $this->createLogger($namespace, $type);
        }

        $logger->log($value, $priority);

        if (isset($this->_logger['mock']->events)) {
            $message = implode(PHP_EOL, end($this->_logger['mock']->events));

            // Set to class variable for sending email.
            // ex.
            //   $log->write('message')->send();
            $this->_config['mail']['message'] = $message;

            // If priority of log is smaller than min level, Send mail.
            if (isset($this->_config['mail']['minlevel'])
                    && is_numeric($this->_config['mail']['minlevel'])) {
                if (intval($this->_config['mail']['minlevel'] <= $priority)) {
                    return $this;
                }

                $this->send($message);

                // Clear events
                $this->_logger['mock'] = array();
            }
        }

        return $this;
    }

    /**
     * Send mail
     *
     * @param  mixed $message Mail message
     * @param  mixed $subject Mail subject
     * @access public
     * @throws Gene_Log_Exception
     * @return Gene_Log Fluent interface
     */
    public function send($message = null, $subject = null)
    {
        if (is_null($this->_config)) {
            throw Gene_Log_Exception('Mail config invalid.');
        }

        if (is_null($this->_mail)) {
            if (class_exists('Gene_Mail', false)) {
                require_once 'Gene/Mail.php';
            }
            $this->_mail = new Gene_Mail();
        }

        $transport = null;
        try {
            if (isset($this->_config['smtp']['host'])
                    && isset($this->_config['smtp']['port'])) {
                $config    = $this->_config['smtp'];
                $transport = new Zend_Mail_Transport_Smtp($config['host'], $config);
            }
            $from = '';
            if (isset($this->_config['mail']['from'])) {
                $from = $this->_config['mail']['from'];
            }

            $to = $this->_config['mail']['to'];

            // If args is null, try to use class variables.
            if (is_null($subject)) {
                $subject = isset($this->_config['mail']['subject'])
                         ? $this->_config['mail']['subject']
                         : '';
            }

            if (is_null($message)) {
                $message = isset($this->_config['mail']['message'])
                         ? $this->_config['mail']['message']
                         : '';
            }

            $from = '';
            if (isset($this->_config['mail']['from'])) {
                $from = $this->_config['mail']['from'];
            }

            $this->_mail->setFrom($from)
                        ->addTo($to)
                        ->setSubject($subject)
                        ->setBodyText($message)
                        ->send($transport);

        } catch (Exception $e) {
            throw Gene_Log_Exception($e->getMessage());
        }

        return $this;
    }
}
