<?php
/**
 * Gene
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
 * @packagea  Gene
 * @version   $id$
 * @copyright 2009-2011 Shinya Ohyanagi
 * @author    Shinya Ohyanagi <sohyanagi@gmail.com>
 * @license   New BSD License
 */

$rootPath = dirname(dirname(dirname(__FILE__)));
$libPath  = $rootPath . DIRECTORY_SEPARATOR . 'library';
$appPath  = $rootPath . DIRECTORY_SEPARATOR . 'app';
set_include_path(get_include_path()
    . PATH_SEPARATOR . $libPath
    . PATH_SEPARATOR . $appPath
);
error_reporting(E_ALL | E_STRICT);
defined('GENE_ROOT_PATH') || define('GENE_ROOT_PATH', $rootPath);
defined('GENE_LIB_PATH') || define('GENE_LIB_PATH', $libPath . DIRECTORY_SEPARATOR . 'Gene');
defined('GENE_APP_PATH') || define('GENE_APP_PATH', $appPath);
defined('GENE_TEST_ROOT') || define('GENE_TEST_ROOT', dirname(__FILE__));

require_once 'Zend/Loader/Autoloader.php';
$autoloader = Zend_Loader_Autoloader::getInstance();
$autoloader->setFallbackAutoloader(true)
           ->suppressNotFoundWarnings(false);

require_once dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'TestHelper.php';

echo 'Versions:' . PHP_EOL;
echo '  PHP ' . phpversion() . PHP_EOL;
echo '  Zend Framework ' . Zend_Version::VERSION . PHP_EOL;
echo '  Gene ' . Gene::VERSION . PHP_EOL;
echo PHP_EOL;
