<?php
/**
 * Gene
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
 * @packagea  Gene
 * @version   $id$
 * @copyright 2009-2010 Shinya Ohyanagi
 * @author    Shinya Ohyanagi <sohyanagi@gmail.com>
 * @license   New BSD License
 */

// Check PHP version
if (version_compare(phpversion(), '5.2.4', '<') === true) {
    echo 'This application supports PHP 5.2.4 or newer.' .  PHP_EOL;
    exit(0);
}

// Set library directory to php.ini include_path
$baseDir     = realpath(dirname(__FILE__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR);
$includePath = get_include_path();
$libPath     = $baseDir . DIRECTORY_SEPARATOR . 'library';
if (!is_dir($libPath)) {
    echo 'Core library could not find in this system.' . PHP_EOL;
    exit(0);
}

// If library directory  already set in include_path, do not set.
if (!preg_match('"' . $libPath . '"', $includePath, $match)) {
    set_include_path($includePath . PATH_SEPARATOR . $libPath);
}

// Add app directory
$includePath = get_include_path();
$appPath     = $baseDir . DIRECTORY_SEPARATOR . 'app';
if (!is_dir($appPath)) {
    echo 'App directory could not find in this system.' . PHP_EOL;
    exit(0);
}

// If app directory already set in include_path, do not set.
if (!preg_match('"' . $appPath . '"', $includePath, $match)) {
    set_include_path($includePath . PATH_SEPARATOR . $appPath);
}

/**
 * @see Gene
 */
require_once 'Gene.php';
Gene::run($appPath);
