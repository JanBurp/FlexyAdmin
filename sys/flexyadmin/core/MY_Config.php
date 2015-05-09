<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/** \ingroup core
 * Uitbreiding op [CI_Config](http://codeigniter.com/user_guide/libraries/config.html)
 * 
 * Grootste aanpassing is het kunnen onderverdelen van config items in secties, zie deze [thread](http://codeigniter.com/forums/viewthread/175199/)
 * 
 * @author Jan den Besten
 */

Class MY_Config extends CI_Config {

  /**
   * @author Jan den Besten
   */
	public function __construct() {
		parent::__construct();
		array_push($this->_config_paths,SITEPATH);
	}

	/**
	 * Site URL
	 *
	 * Returns base_url . index_page [. uri_string]
	 *
	 * @uses	CI_Config::_uri_string()
	 *
	 * @param	string|string[]	$uri	URI string or an array of segments
	 * @param	string	$protocol
	 * @return	string
	 */
	public function site_url($uri = '', $protocol = NULL) {
    $url = parent::site_url($uri,$protocol);
    // no url_suffix in admin
    if (has_string('/admin',$url)) {
      $url = str_replace($this->item('url_suffix'),'',$url);
    }
	  return $url; 
	}


/**
	* Zet een config item, eventueel met een sectie
	*
	* @param string $item Naam van het item
	* @param string $value Waarde van het item
	* @param string $section an optional section to save the item 
	* @return void
	*/
	public function set_item($item, $value, $section="") {
		if( $section === "" )
			$this->config[$item] = $value;    
		else
			$this->config[$section][$item] = $value;
	}


  /**
   * Laad een config bestand.
   * Als hetzelfde bestand in sys en in site/config bestaat:
   * Laad eerst de sys config, dan de site config en merge deze samen (zo kunnen site specifieke instellingen standaard instellingen overrulen)
   *
   * @param string $file 
   * @param string $use_sections default=FALSE
   * @param string $fail_gracefully default=FALSE
   * @return void
   * @author Jan den Besten
   */
	public function load($file = '', $use_sections = FALSE, $fail_gracefully = FALSE )	{
		
		$file = ($file == '') ? 'config' : str_replace('.php', '', $file);
		$loaded = FALSE;

		// First select paths's
		$check_locations = defined('ENVIRONMENT')	? array(ENVIRONMENT.'/'.$file, $file)	: array($file);
		$locations=array();
		foreach ($this->_config_paths as $path) {
			if (defined('ENVIRONMENT')) $locations[].=$path.'config/'.ENVIRONMENT;
			$locations[]=$path.'config';
		}
    
		// Load from all locations, and load and override config
		foreach ($locations as $location) {

			$file_path = $location.'/'.$file.'.php';

      // echo "$file_path<br/>";

			// Allready loaded?
      if ( !in_array($file_path, $this->is_loaded, TRUE))  {

				// Exists?
				if (file_exists($file_path)) {

          // echo "FOUND: $file_path<br/>";

					// Load
					include($file_path);
          
					// Add to config
					if ( isset($config) AND is_array($config))	{
            
            // echo"<pre>";  print_r($config); echo "</pre>";

						if ($use_sections === TRUE)	{
							if (isset($this->config[$file])) {
								$this->config[$file] = $this->_array_merge_recursive_distinct($this->config[$file], $config);
							}
							else {
								$this->config[$file] = $config;
							}
						}
						else {
							$this->config = $this->_array_merge_recursive_distinct($this->config, $config);
						}

					}
					else {
						// show_error('Your '.$file_path.' file does not appear to contain a valid configuration array.');
					}

					$this->is_loaded[] = $file_path;
					unset($config);
					$loaded=TRUE;

          // echo "LOADED: $file_path<br/>";

					log_message('debug', 'Config file loaded: '.$file_path);
				}

			}
		}

		if ($loaded === FALSE) {
			// File not found
			if ($fail_gracefully === TRUE) {
				return FALSE;
			}
      // echo("NOT FOUND: $file_path<br>");
      
      // show_error('The configuration file '.$file_path.' does not exist.');
		}

		return $loaded;
	}
  

  /**
   * Verwijder een item uit de config
   *
   * @param string $name Item
   * @return void
   * @author Jan den Besten
   */
  public function unload($name) {
    unset($this->config[$name]);
    $key=in_array_like($name,$this->is_loaded);
    while ($key) {
      unset($this->is_loaded[$key]);
      $key=in_array_like($name,$this->is_loaded);
    }
  }
  
  
  
  /**
   * Same as in array_helper, but that one isn't loaded yet.
   *
   * @param string $array1 
   * @param string $array2 
   * @return void
   * @author Jan den Besten
   */
  private function _array_merge_recursive_distinct(array &$array1, array &$array2 ) {
    $merged = $array1;
    foreach ( $array2 as $key => &$value ) {
      if ( is_array ( $value ) && isset ( $merged [$key] ) && is_array ( $merged [$key] ) ) {
        $merged [$key] = $this->_array_merge_recursive_distinct ( $merged [$key], $value );
      }
      else {
        $merged [$key] = $value;
      }
    }
    return $merged;
  }
  
  


// --------------------------------------------------------------------

}
