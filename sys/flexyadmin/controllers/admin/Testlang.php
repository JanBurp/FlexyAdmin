<?php
/**
 * Translation Tester
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

define('TESTLANG_VERSION', 'v1.1.0');

class Testlang extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->load->library('testlang_lib');
    }

    public function index ()
    {
        $this->load->helper('url');
        redirect('admin/testlang/summary');
    }

    public function lang_text ($idiom = 'english')
    {
        $this->load->helper(array('html', 'url'));

        $data['languages'] = $this->testlang_lib->get_available_languages();

        foreach ($data['languages'] as &$language)
        {

            $classname = ($language == $idiom) ? "btn btn-primary" : "btn btn-default";
            $language = anchor("admin/testlang/lang_text/$language", ucfirst($language), array('class' => $classname));
        }
        unset($language);

        $this->testlang_lib->set_idiom($idiom);
        $data['idiom'] = $idiom;
        $data['lang_text'] = $this->testlang_lib->get_lang_text();
        $data['default_idiom'] = $this->testlang_lib->get_default_idiom();
        $data['nb_error'] = $this->testlang_lib->get_nb_error();
        $data['js_footer'] = "
    <script type=\"text/javascript\">
        $(function() {
            $('#testlang-btn-show-error-only').click(function() {
                $('.testlang-file-no-error').toggle();
                $('.testlang-text-no-error').toggle();
            });
        });
    </script>";

        // Set view name and load template
        $data['view_name'] = 'testlang/lang_text';
        $this->load->view('testlang/template', $data);
    }

    public function summary ()
    {
        $this->load->helper(array('html', 'url'));

        $data['languages'] = array();
        $languages = $this->testlang_lib->get_available_languages();

        foreach ($languages as $i => $idiom)
        {
            $this->testlang_lib->set_idiom($idiom);
            $this->testlang_lib->get_lang_text(FALSE);
            $nb_error = $this->testlang_lib->get_nb_error();

            // Link title
            $title = ($i + 1) . ". " . ucfirst($idiom);

            if ($nb_error == 0)
            {
                $title .= ' <span class="label label-success pull-right">No error</span>';
            }
            else
            {
                $title .= ' <span class="label label-danger pull-right">' . $nb_error;
                $title .= ($nb_error == 1) ? " error" : " errors";
                $title .= '</span>';
            }

            // Link for each language
            $data['languages'][] = array(
                    'idiom' => $idiom,
                    'nb_error' => $nb_error,
                    'link' => anchor("admin/testlang/lang_text/$idiom", $title, array('class' => 'list-group-item')),
                    );
        }

        $data['nb_lang'] = count($languages);
        $data['nb_col'] = 3;
        $data['class_col'] = 'col-md-4';

        if ($data['nb_lang'] <= 15)
        {
            $data['nb_per_col'] =  5;
        }
        else
        {
            $data['nb_per_col'] =  ceil($data['nb_lang'] / $data['nb_col']);
        }

        // Set view name and load template
        $data['view_name'] = 'testlang/summary';
        $this->load->view('testlang/template', $data);
    }

    public function raw_data ()
    {
        $data['languages'] = $this->testlang_lib->get_available_languages();

        $this->testlang_lib->set_idiom('english');
        $data['english_text'] = $this->testlang_lib->get_lang_text();

        $this->testlang_lib->set_idiom('french');
        $data['french_text'] = $this->testlang_lib->get_lang_text();

        $this->load->view('testlang/raw_data', $data);
    }
}
