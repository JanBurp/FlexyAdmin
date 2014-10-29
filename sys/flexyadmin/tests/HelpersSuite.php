<?php

require_once 'helpers/StringHelperTest.php';
require_once 'helpers/ArrayHelperTest.php';

class HelpersSuite extends CIUnit_Framework_TestSuite
{

    public static function suite ()
    {
        $suite = new CIUnitPackage('Array and String Helpers Suite');
        $suite->addTestSuite('StringHelperTest');
        $suite->addTestSuite('ArrayHelperTest'); 
        
        return $suite;
    }
}

?>