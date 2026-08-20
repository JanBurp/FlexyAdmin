<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/** \ingroup core
 * Uitbreiding op CI Input
 *
 * pocketarc/codeigniter's CI_Input dropped the whole eager "sanitize globals on
 * construct" pipeline that stock CI3 3.1.13 had: it no longer cleans $_GET/$_POST/
 * $_COOKIE keys and values on every request, and no longer honours
 * $config['global_xss_filtering'] at all (xss_clean is only ever applied when a
 * caller explicitly passes TRUE to get()/post()/etc.). Some FlexyAdmin sites rely
 * on that global behaviour for safety, so it's restored here exactly as stock CI3
 * 3.1.13 had it - see sys/vendor/pocketarc/codeigniter (or the CI3 user guide) for
 * the original CI_Input::__construct()/_sanitize_globals()/_clean_input_data().
 *
 * @author Jan den Besten
 */

class MY_Input extends CI_Input {

  /**
   * Allow GET array flag
   *
   * If set to FALSE, then $_GET will be set to an empty array.
   */
  protected $_allow_get_array = TRUE;

  /**
   * Standardize new lines flag
   *
   * If set to TRUE, then newlines are standardized.
   */
  protected $_standardize_newlines;

  /**
   * Enable XSS flag
   *
   * Determines whether the XSS filter is always active when
   * GET, POST or COOKIE data is encountered. Set automatically
   * based on $config['global_xss_filtering'].
   */
  protected $_enable_xss = FALSE;

  /**
   * CI_Utf8 instance, used by _clean_input_data()/_clean_input_keys()
   */
  protected $uni;

  /**
   * pocketarc/codeigniter's CI_Input::__construct() requires the CI_Security instance
   * (stock CI3 3.1.13's didn't take any argument) - accept and forward it here, then
   * restore the global sanitizing stock CI3 used to do (see class docblock).
   */
  public function __construct($security = NULL) {
    parent::__construct($security);

    $this->_allow_get_array      = (config_item('allow_get_array') !== FALSE);
    $this->_enable_xss           = (config_item('global_xss_filtering') === TRUE);
    $this->_standardize_newlines = (bool) config_item('standardize_newlines');

    // Do we need the UTF-8 class?
    if (UTF8_ENABLED === TRUE)
    {
      $this->uni =& load_class('Utf8', 'core');
    }

    // Sanitize global arrays
    $this->_sanitize_globals();
  }

  /**
   * Fetch from array
   *
   * pocketarc/codeigniter's _fetch_from_array() always defaults $xss_clean to FALSE,
   * so there is no way left to tell "caller didn't specify" apart from "caller
   * explicitly opted out". Stock CI3 defaulted it (and every public getter below)
   * to NULL for that reason, falling back to $_enable_xss ($config['global_xss_filtering'])
   * only when the caller didn't pass an actual boolean. Restore that here.
   *
   * @param	array	&$array
   * @param	mixed	$index
   * @param	bool	$xss_clean
   * @return	mixed
   */
  protected function _fetch_from_array(&$array, $index = NULL, $xss_clean = NULL)
  {
    is_bool($xss_clean) OR $xss_clean = $this->_enable_xss;
    return parent::_fetch_from_array($array, $index, $xss_clean);
  }

  // The methods below only change the $xss_clean default back to NULL (see
  // _fetch_from_array() above) and forward to pocketarc's own implementation,
  // which internally calls back into $this->_fetch_from_array() / $this->get()
  // / $this->post() - so this file's overrides still apply.

  public function get($index = NULL, $xss_clean = NULL)
  {
    return parent::get($index, $xss_clean);
  }

  public function post($index = NULL, $xss_clean = NULL)
  {
    return parent::post($index, $xss_clean);
  }

  public function post_get($index, $xss_clean = NULL)
  {
    return parent::post_get($index, $xss_clean);
  }

  public function get_post($index, $xss_clean = NULL)
  {
    return parent::get_post($index, $xss_clean);
  }

  public function cookie($index = NULL, $xss_clean = NULL)
  {
    return parent::cookie($index, $xss_clean);
  }

  public function server($index, $xss_clean = NULL)
  {
    return parent::server($index, $xss_clean);
  }

  public function input_stream($index = NULL, $xss_clean = NULL)
  {
    return parent::input_stream($index, $xss_clean);
  }

  public function user_agent($xss_clean = NULL)
  {
    return parent::user_agent($xss_clean);
  }

  public function request_headers($xss_clean = NULL)
  {
    return parent::request_headers($xss_clean);
  }

  public function get_request_header($index, $xss_clean = NULL)
  {
    return parent::get_request_header($index, $xss_clean);
  }

  /**
   * Sanitize Globals
   *
   * Internal method serving for the following purposes:
   *
   *	- Unsets $_GET data, if query strings are not enabled
   *	- Cleans POST, COOKIE and SERVER data
   *	- Standardizes newline characters (depending on config)
   *
   * Restored from stock CI3 3.1.13 - see class docblock.
   *
   * @return	void
   */
  protected function _sanitize_globals()
  {
    // Is $_GET data allowed? If not we'll set the $_GET to an empty array
    if ($this->_allow_get_array === FALSE)
    {
      $_GET = array();
    }
    elseif (is_array($_GET))
    {
      foreach ($_GET as $key => $val)
      {
        $_GET[$this->_clean_input_keys($key)] = $this->_clean_input_data($val);
      }
    }

    // Clean $_POST Data
    if (is_array($_POST))
    {
      foreach ($_POST as $key => $val)
      {
        $_POST[$this->_clean_input_keys($key)] = $this->_clean_input_data($val);
      }
    }

    // Clean $_COOKIE Data
    if (is_array($_COOKIE))
    {
      // Also get rid of specially treated cookies that might be set by a server
      // or silly application, that are of no use to a CI application anyway
      // but that when present will trip our 'Disallowed Key Characters' alarm
      // http://www.ietf.org/rfc/rfc2109.txt
      // note that the key names below are single quoted strings, and are not PHP variables
      unset(
        $_COOKIE['$Version'],
        $_COOKIE['$Path'],
        $_COOKIE['$Domain']
      );

      foreach ($_COOKIE as $key => $val)
      {
        if (($cookie_key = $this->_clean_input_keys($key)) !== FALSE)
        {
          $_COOKIE[$cookie_key] = $this->_clean_input_data($val);
        }
        else
        {
          unset($_COOKIE[$key]);
        }
      }
    }

    // Sanitize PHP_SELF
    if (isset($_SERVER['PHP_SELF']))
    {
      $_SERVER['PHP_SELF'] = strip_tags($_SERVER['PHP_SELF']);
    }

    log_message('debug', 'Global POST, GET and COOKIE data sanitized');
  }

  /**
   * Clean Input Data
   *
   * Internal method that aids in escaping data and
   * standardizing newline characters to PHP_EOL.
   *
   * Restored from stock CI3 3.1.13 - see class docblock. The magic_quotes_gpc
   * handling from the original (PHP < 5.4) was dropped, since that function
   * no longer exists as of PHP 8.0.
   *
   * @param	string|string[]	$str	Input string(s)
   * @return	string
   */
  protected function _clean_input_data($str)
  {
    if (is_array($str))
    {
      $new_array = array();
      foreach (array_keys($str) as $key)
      {
        $new_array[$this->_clean_input_keys($key)] = $this->_clean_input_data($str[$key]);
      }
      return $new_array;
    }

    // Clean UTF-8 if supported
    if (UTF8_ENABLED === TRUE)
    {
      $str = $this->uni->clean_string($str);
    }

    // Remove control characters
    $str = remove_invisible_characters($str, FALSE);

    // Standardize newlines if needed
    if ($this->_standardize_newlines === TRUE)
    {
      return preg_replace('/(?:\r\n|[\r\n])/', PHP_EOL, $str);
    }

    return $str;
  }

	/**
	 * Clean Keys
	 *
	 * Internal method that helps to prevent malicious users
	 * from trying to exploit keys we make sure that keys are
	 * only named with alpha-numeric text and a few other items.
	 *
	 * Jdb:Added fieldname in error 2015-05-02
	 *
	 * @param	string	$str	Input string
	 * @param	bool	$fatal	Whether to terminate script exection
	 *				or to return FALSE if an invalid
	 *				key is encountered
	 * @return	string|bool
	 */
	protected function _clean_input_keys($str, $fatal = TRUE)
	{
		if ( ! preg_match('/^[a-z0-9:_\/|-]+$/i', $str))
		{
			if ($fatal === TRUE)
			{
				return FALSE;
			}
			else
			{
				set_status_header(503);
				echo 'Disallowed Key Characters. <b>'.$str.'</b>'; // JdB 2015-05-02
				exit(7); // EXIT_USER_INPUT
			}
		}

		// Clean UTF-8 if supported
		if (UTF8_ENABLED === TRUE)
		{
			return $this->uni->clean_string($str);
		}

		return $str;
	}


}

/* End of file MY_Input.php */
