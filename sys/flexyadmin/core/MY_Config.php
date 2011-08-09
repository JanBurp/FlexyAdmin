<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');


/**
 * See http://codeigniter.com/forums/viewthread/175199/
 */


Class MY_Config extends CI_Config
{
    function __construct() {
        parent::__construct();
    }

    /**
     * Set a config file item
     *
     * @access    public
     * @param    string    the config item key
     * @param    string    the config item value
     * @param   string  an optional section to save the item 
     * @return    void
     */
    function set_item($item, $value, $section="") {
			if( $section === "" )
				$this->config[$item] = $value;    
			else
				$this->config[$section][$item] = $value;
		}

// --------------------------------------------------------------------

}
