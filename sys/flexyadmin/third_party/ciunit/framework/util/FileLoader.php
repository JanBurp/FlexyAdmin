<?php

/**
 * CIUnit
 *
 * Copyright (c) 2013, Agop Seropyan <agopseropyan@gmail.com>
 * All rights reserved.
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
 *   * Neither the name of Agop Seropyan nor the names of his
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
 * @package    CIUnit
 * @subpackage Util
 * @author     Agop Seropyan <agopseropyan@gmail.com>
 * @copyright  2012, Agop Seropyan <agopseropyan@gmail.com>
 * @license    http://www.opensource.org/licenses/BSD-3-Clause  The BSD 3-Clause License
 * @since      File available since Release 1.0.0
 */
class CIUnit_Util_FileLoader
{

    public static function checkAndLoad ($fileName)
    {
        $testsAvailable = self::collectTests(TRUE);
        
        if (FALSE === $testsAvailable)
            throw new CIUnit_Framework_Exception_CIUnitException("No tests found");
        
        foreach ($testsAvailable as $test) {
            //if($fileName == basename($test, '.php')) {
                self::load($test);
              //  break;
            //}
        }
    }

    public static function load ($filePath)
    {
      if (is_array($filePath)) $filePath=$filePath['path'];
      if (! file_exists($filePath) and is_readable($filePath))
          throw new CIUnit_Framework_Exception_CIUnitException(
                  sprintf("CIUnit can't open file %s for reading!", $filePath));
      
      if (! is_dir($filePath)) include_once $filePath;
      
      return $filePath;
    }

    /**
     * Scan the directory containing tests and build a directory map
     * 
     * @param unknown_type $fullPath
     * @param unknown_type $hidden
     * @throws CIUnit_Framework_Exception_CIUnitException
     * @return multitype:string |boolean
     */
    public static function collectTests ($fullPath = FALSE, $hidden = FALSE) {
      $ci = & get_instance();
      $ci->load->add_package_path(APPPATH . 'third_party/ciunit', FALSE);
      $ci->config->load('config');
      $paths=$ci->config->item('tests_paths');
      
      $testFiles=array();
      foreach ($paths as $path) {
        if (! file_exists($path) || ! is_readable($path)) throw new CIUnit_Framework_Exception_CIUnitException(sprintf("CIUnit can't open or read '%s'", $path));
        if (@$fp = @opendir($path)) {
          $files=read_map($path,'php,dir',TRUE,FALSE,FALSE,TRUE);
          $testFiles=array_merge($testFiles,$files);
        }
      }
      
      if ($testFiles) {
        $testFiles=not_filter_by($testFiles,'_');
        // combine suits and dirs
        foreach ($testFiles as $key => $file) {
          if ($file['type']=='dir') {
            $subfiles=$file['.'];
            $suite=$key.'suite.php';
            if (isset($subfiles[$suite])) {
              $suiteName=remove_suffix($subfiles[$suite]['name'],'.');
              $testFiles[$suiteName]=array_merge($file,$subfiles[$suite]);
              unset($testFiles[$suiteName]['.'][$suite]);
              unset($testFiles[$key]);
            }
          }
        }
      }
      // trace_($testFiles);
      return $testFiles;
    }
}

?>