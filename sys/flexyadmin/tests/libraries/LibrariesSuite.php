<?php

require_once 'URILibraryTest.php';
require_once 'FormValidationTest.php';

class LibrariesSuite extends CIUnit_Framework_TestSuite
{

    public static function suite ()
    {
        $suite = new LibrariesSuite('CodeIgniters Libraries Suite');
        $suite->addTestSuite('URILibraryTest');
        $suite->addTestSuite('FormValidationTest');
        
        return $suite;
    }
}

?>