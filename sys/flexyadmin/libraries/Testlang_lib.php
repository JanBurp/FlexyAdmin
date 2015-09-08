<?php
/**
 * Translation Tester Library
 *
 * An open source application to test your CodeIgniter translation files.
 * You need CodeIgniter 3.x to use this application.
 *
 * Just copy all the files in a working CodeIngniter application,
 * then go to http://yoursite.com/testlang
 * or to http://yoursite.com/index.php/testlang
 *
 * This content is released under the MIT License (MIT)
 *
 * Copyright (c) 2015, Alain Rivest <info@aldra.ca>
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * @package Translation Tester
 * @author Alain Rivest
 * @copyright Copyright (c) 2015, Alain Rivest <info@aldra.ca>
 * @license http://opensource.org/licenses/MIT MIT License
 * @link http://aldra.ca
 */
defined('BASEPATH') OR exit('No direct script access allowed');

class Testlang_lib
{
    /**
     * CI Singleton
     *
     */
    private $CI;

    /**
     * Idiom for original text
     *
     */
    private $default_idiom = '';

    /**
     * Idiom for translated text
     *
     */
    private $idiom = '';


    /**
     * List of available languages
     *
     */
    private $lang_list = NULL;

    /**
     * Language data (language, files)
     */
    private $lang_data = NULL;

    /**
     * Number of errors in this file
     */
    private $nb_error = 0;

    // --------------------------------------------------------------------

    /**
     * Class constructor
     *
     * @return    void
     */
    public function __construct()
    {
        $this->CI =& get_instance();

        $this->CI->load->helper('file');

        // Default to english
        $this->set_idiom('english');
        $this->set_default_idiom('english');

        log_message('debug', 'Testlang_lib Class Initialized');
    }

    public function set_idiom ($idiom)
    {
        $this->idiom = $idiom;

        // Reset language data
        $this->lang_data = NULL;
    }

    public function set_default_idiom ($default_idiom)
    {
        $this->default_idiom = $default_idiom;

        // Reset language data
        $this->lang_data = NULL;
    }

    public function get_idiom()
    {
        return $this->idiom;
    }

    public function get_default_idiom()
    {
        return $this->default_idiom;
    }

    public function get_available_languages()
    {
        if ($this->lang_list === NULL)
        {
            $app_files = $this->get_dir_names(APPPATH . 'language/');
            $sys_files = $this->get_dir_names(BASEPATH . 'language/');
            $this->lang_list = array_unique(array_merge($app_files, $sys_files));
        }

        return $this->lang_list;
    }

    public function get_lang_keys ($return_array = TRUE)
    {
        if ($this->lang_data === NULL)
        {
            $this->load_lang_keys();
        }

        if ($return_array)
        {
            return $this->lang_data;
        }
    }

    public function get_lang_text ($return_array = TRUE)
    {
        $this->get_lang_keys(FALSE);

        foreach ($this->lang_data as &$file)
        {
            if ( ! empty($file['filename']))
            {
                $lang_file = str_replace("_lang.php", "", $file['filename']);

                // Load this language file
                $this->CI->lang->load($lang_file, $this->idiom);

                // Get all text values
                foreach ($file['data'] as &$data)
                {
                    $data['text'] = $this->CI->lang->line($data['key']);
                }
                unset($data);
            }
        }
        unset($file);

        if ( ! empty($this->default_idiom) && $this->default_idiom != $this->idiom)
        {
            $this->add_default_lang_text();
        }

        $this->check_for_error();

        if ($return_array)
        {
            return $this->lang_data;
        }
    }

    /**
     * Get the number of errors
     *
     * @return number
     */
    public function get_nb_error()
    {
        return $this->nb_error;
    }

    /**
     * Get the number of errors
     *
     * @return number
     */
    private function check_for_error()
    {
        $this->nb_error = 0;

        foreach ($this->lang_data as &$file)
        {
            $file['nb_error'] = 0;

            foreach ($file['data'] as &$data)
            {
                if (empty($data['text']))
                {
                    $data['text_error'] = "Missing translation!";
                    $file['nb_error']++;
                    $this->nb_error++;
                }

                if (isset($data['default_text']) && empty($data['default_text']))
                {
                    $data['default_text_error'] = "This key doesn't exists in the default language file!";
                    $file['nb_error']++;
                    $this->nb_error++;
                }
            }
            unset($data);
        }
        unset($file);
    }


    // --------------------------------------------------------------------

    /**
     * Get directory names (all languages are in subdirectories)
     *
     * @param string $source_dir
     * @throws Exception
     * @return array
     */
    private function get_dir_names ($source_dir)
    {
        $dir_names = array();

        if ($fp = @opendir($source_dir))
        {
            // make sure $source_dir has a trailing slash on the initial call
            $source_dir = rtrim(realpath($source_dir), DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;

            while (FALSE !== ($file = readdir($fp)))
            {
                if (is_dir($source_dir.$file) && $file[0] !== '.')
                {
                    $dir_names[] = $file;
                }
            }

            closedir($fp);
        }
        else
        {
            throw new Exception("Can't read directory $source_dir");
        }

        sort($dir_names);

        return $dir_names;
    }

    /**
     * Load all language keys for this language
     *
     * @param unknown_type $lang_data
     */
    private function load_lang_keys ()
    {
        $this->lang_data = array();

        // Application
        $path = APPPATH . 'language/' . $this->idiom . '/';
        $this->get_keys_from_dir($path, 'application');

        // System
        $path = BASEPATH. 'language/' . $this->idiom . '/';
        $this->get_keys_from_dir($path, 'system');
    }

    /**
     * Load all language keys for the default language
     *
     * @param unknown_type $lang_data
     */
    private function load_default_lang_keys ()
    {
        // Initialize the data array only if it's not already initialized, else we add data to the existing array.
        if ($this->lang_data === NULL)
        {
            $this->lang_data = array();
        }

        // Application
        $path = APPPATH . 'language/' . $this->default_idiom . '/';
        $this->add_missing_keys_from_dir($path, 'application');

        // System
        $path = BASEPATH. 'language/' . $this->default_idiom . '/';
        $this->add_missing_keys_from_dir($path, 'system');
    }

    /**
     * Load all language keys from all files in this directory
     *
     * @param unknown_type $path
     * @param unknown_type $lang_data
     */
    private function get_keys_from_dir ($path, $directory)
    {
        // Get file names from path
        $filenames = get_filenames($path);

        if (is_array($filenames))
        {
            sort($filenames);

            foreach ($filenames as $filename)
            {
                if (substr($filename, -9) == "_lang.php")
                {
                    // Get all language keys from this file
                    $this->lang_data[] = array(
                            'filename' => $filename,
                            'directory' => $directory,
                            'data' => $this->get_keys_from_file($path . $filename));
                }
            }
        }
    }

    /**
     * Add the missing keys from all files in this directory
     *
     * @param string $path
     * @param string $directory
     */
    private function add_missing_keys_from_dir ($path, $directory)
    {
        // Get file names from path
        $filenames = get_filenames($path);

        if (is_array($filenames))
        {
            sort($filenames);

            foreach ($filenames as $filename)
            {
                if (substr($filename, -9) == "_lang.php")
                {
                    // Get all language keys from this file
                    $keys = $this->get_keys_from_file($path . $filename);

                    // Search this file in the data already loaded
                    $found_file = FALSE;
                    foreach ($this->lang_data as &$file)
                    {
                        if ($file['filename'] == $filename)
                        {
                            $found_file = TRUE;

                            // For each key of the default language, check if it's present or missing.
                            foreach ($keys as $key_data)
                            {
                                // Search this key
                                $found_key = FALSE;

                                foreach ($file['data'] as &$data)
                                {
                                    if ($data['key'] == $key_data['key'])
                                    {
                                        // Break out of the loop
                                        $found_key = TRUE;
                                        break;
                                    }
                                }
                                unset($data);

                                // Add the missing key
                                if ( ! $found_key)
                                {
                                    $file['data'][] = $key_data;
                                }
                            }

                            // Break out of the loop
                            break;
                        }
                    }
                    unset($file);

                    // Missing this entire file, add all the keys
                    if ( ! $found_file)
                    {
                        $this->lang_data[] = array(
                                'filename' => $filename,
                                'directory' => $directory,
                                'data' => $keys);
                    }
                }
            }
        }
    }

    /**
     * Get all language keys from this file.
     * @param unknown_type $langfile
     * @throws Exception
     * @return multitype:
     */
    private function get_keys_from_file ($langfile)
    {
        $found = file_exists($langfile);

        if ($found !== TRUE)
        {
            throw new Exception("Unable to load the file $langfile");
        }

        // Load the language file: it creates the $lang array
        include($langfile);

        if ( ! isset($lang) OR ! is_array($lang))
        {
            throw new Exception("Language file contains no data: $langfile");
        }

        // Return the language keys in the data array
        $data = array();

        foreach ($lang as $key => $val)
        {
            $data[] = array('key' => $key);
        }

        return $data;
    }

    private function add_default_lang_text ()
    {
        if ( ! empty($this->lang_data))
        {
            $this->CI->lang->reset_lang();

            // Add missing keys
            $this->load_default_lang_keys();

            foreach ($this->lang_data as &$file)
            {
                if ( ! empty($file['filename']))
                {
                    $lang_file = str_replace("_lang.php", "", $file['filename']);

                    // Load this language file
                    $this->CI->lang->load($lang_file, $this->default_idiom);

                    // Get all text values
                    foreach ($file['data'] as &$data)
                    {
                        $data['default_text'] = $this->CI->lang->line($data['key']);
                    }
                    unset($data);
                }
            }
            unset($file);
        }
    }

}
