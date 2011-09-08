<?

// This is an example of the webapp way of calling pages (file/method/args)
// In config set: $config['uri_as_modules']=TRUE;


class App extends Module {

	public function index() {
		$this->CI->site['content'] = __METHOD__;
	}

	public function test() {
		$args=func_get_args();
		if (isset($args[0])) $args=$args[0];
		$this->CI->site['content'] = __METHOD__.'('.implode('|',$args).')';
	}


}

?>