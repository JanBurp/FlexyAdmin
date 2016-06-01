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
	 * Zelfde als CodeIgniter origineel met deze aanpassingen:
	 * - Verwijderd url_suffix in admin kant
	 * - Voegt eventuele query keys toe aan eind
	 *
	 * @param	string|string[]	$uri	URI string or an array of segments
	 * @param	string	$protocol [NULL]
	 * @param	array	$add_query [NULL] ...
	 * @return	string
	 */
	public function site_url($uri = '', $protocol = NULL, $add_query = array() ) {
    if ($uri=='./') $uri ='';
    $url = parent::site_url($uri,$protocol);
    $is_admin = has_string('/admin',$url);

    // no url_suffix in admin
    if ($is_admin) {
      $url = str_replace($this->item('url_suffix'),'',$url);
    }
    
    // (lang) query on frontend
    else {
      $CI = &get_instance();
      // Splits query
      $query = explode('?',$url);
      $uri = $query[0];
      if (isset($query[1]))
        parse_str($query[1],$query);
      else 
        $query=array();
      // Keep current query keys
      if ($this->item('keep_query_keys')) {
        $server=$CI->input->server('QUERY_STRING');
        parse_str($server,$keys);
        // Keep only the set keys
        $keys=array_keep_keys($keys,$this->item('keep_query_keys'));
        $query=array_merge($query,$keys);
        // Set language if asked for
        if (isset($CI->site['language']) and in_array('lang',$this->item('keep_query_keys'))) $query['lang'] = $CI->site['language'];
      }
      // Merge with new?
      $query = array_merge($query,$add_query);
      // Add query parts
      if (!empty($query)) {
        $url=$uri.'?';
        foreach ($query as $key => $value) {
          $url.=$key.'='.$value.'&';
        }
        $url=substr($url,0,strlen($url)-1);
      }
      
    }
    
	  return $url; 
	}






  /**
	 * Zet een config item
	 *
	 * @param mixed $item string of array van strings
	 * @param mixed $value Waarde van het item
	 * @return $this
	 */
	public function set_item($item, $value) {
    $this->config = set_el( $this->config, $item, $value);
    return $this;
	}


	/**
	 * Aangepaste versie van ->item(), werkt hetzelfde als el() (array helpers).
	 *
	 * @param	mixed	$item	string of array van strings
	 * @param	mixed	$default	[NULL]
	 * @return mixed
	 */
	public function get_item( $item, $default=NULL) {
    return el( $item,$this->config, $default );
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
