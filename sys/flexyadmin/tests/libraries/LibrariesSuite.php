<?php

require_once 'URILibraryTest.php';

class LibrariesSuite extends CIUnit_Framework_TestSuite
{

    public static function suite ()
    {
        $suite = new LibrariesSuite('CodeIgniters Libraries Suite');
        $suite->addTestSuite('URILibraryTest');
        
        return $suite;
    }
}

?>