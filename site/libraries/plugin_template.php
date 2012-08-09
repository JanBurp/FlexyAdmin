<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Plugin_template
 *
 * Gebruik dit als basis voor je eigen plugins
 *
 * @author Jan den Besten
 */
 class Plugin_template extends Plugin {

	/**
	 * _trigger
	 *
	 * If you need dynamic triggers (see in the config: $config['trigger'] and $config['trigger_method']) use this function and return a trigger array. That will be merged $config['trigger'] in the config.
	 *
	 * @return array
	 * @author Jan den Besten
	 */
	public function _trigger() {
		$trigger=array();
		return $trigger;
	}


	/**
	 * _admin_logout
	 *
	 * The standard function which will be called when a user is logging out of FlexyAdmin ($config['logout_method'] must be set in the config)
	 *
	 * @return string if the return string is empty login is successfull, if not return the messages that will be shows on logout
	 * @author Jan den Besten
	 */
	public function _admin_logout() {
    // return '';
	}


	/**
	 * _admin_api
	 *
	 * The standard function which is called in admin with this urr: 'admin/plugin/_name_/_args..' where _name_ is the name of you're plugin and args can be extra uri segments. ($config['admin_api_method'] must be set in the config)
	 *
	 * @return string Return the HTML the plugin needs to show (use views for it!)
	 * @author Jan den Besten
	 */
	public function _admin_api($args=NULL) {
    return $this->CI->load->view('plugin_template',array(),true);
	}


	/**
	 * _after_update()
	 *
	 * This is called when $config['after_update_method'] and the appropriate triggers are set in config and offcourse when a soma data is being updated in FlexyAdmin
	 *
	 * @return array with changed data
	 * @author Jan den Besten
	 */
	public function _after_update() {
		return $this->newData;
	}


	/**
	 * _after_delete()
	 *
	 * Same as _after_update(), but now it will be called after some data has been deleted in FlexyAdmin (according to the trigger settings offcourse)
	 *
	 * @return array with changed data
	 * @author Jan den Besten
	 */
	public function _after_delete() {
		return false;
	}


}

?>