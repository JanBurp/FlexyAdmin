<?php
/**
* PHPDoc parser for use in Doqumentor
*
* Simple example usage: 
* $a = new Parser($string); 
* $a->parse();
* 
* @author Jan den Besten & Murray Picton
*/
class Parser {
	
	/**
	* The string that we want to parse
	*/
	private $string;
	/**
	* Storge for the short description
	*/
	private $shortDesc;
	/**
	* Storge for the long description
	*/
	private $longDesc;
	/**
	* Storge for all the PHPDoc parameters
	*/
	private $params;
	
  
	/**
	* Setup the initial object
	* 
	* @param string $string The string we want to parse
	*/
	public function __construct($string) {
		$this->string = $string;
		$this->setupParams();
	}
  
	/**
	* Setup the valid parameters
	* 
	* @param string $type NOT USED
	*/
	private function setupParams($type = "") {
		$params = array(
			"access"	=>	'',
      "package" => '',
			"author"	=>	'',
			"copyright"	=>	'',
			"deprecated"=>	'',
			"example"	=>	'',
			"ignore"	=>	'',
			"internal"	=>	'',
			"link"		=>	'',
			"param"		=>	'',
      "var"		=>	'',
			"return"	=> 	'',
			"see"		=>	'',
			"since"		=>	'',
			"tutorial"	=>	'',
			"version"	=>	''
		);
		
		$this->params = $params;
	}
  
  
	/**
	* Parse each line
	*
	* Takes an array containing all the lines in the string and stores
	* the parsed information in the object properties
	* 
	* @param array $lines An array of strings to be parsed
	*/
	private function parseLines($lines) {
    $desc = array();
		foreach($lines as $line) {
			$parsedLine = $this->parseLine($line); //Parse the line
			if ($parsedLine!==false){
			  if (empty($this->shortDesc)) {
          $this->shortDesc = $parsedLine; //Store the first line in the short description
        }
        else {
          $desc[] = $parsedLine; //Store the line in the long description
        }
			}
		}
		$this->longDesc = implode('<br />', $desc);
	}
  
	/**
	* Parse the line
	*
	* Takes a string and parses it as a PHPDoc comment
	* 
	* @param string $line The line to be parsed
	* @return mixed False if the line contains no parameters or paramaters
	* that aren't valid otherwise, the line that was passed in.
	*/
	private function parseLine($line) {
		//Trim the whitespace from the line
    $line = trim($line);
		if(empty($line)) return false; //Empty line
		
		if(strpos($line, '@') === 0) {
      $split=explode(' ',substr($line,1));
      $param=array_shift($split);
      $value=implode(' ',$split);
      // $param = substr($line, 1, strpos($line, ' ')-1); //Get the parameter name
      // $value = substr($line, strlen($param) + 2); //Get the value
			if ($this->setParam($param, $value)) return false; //Parse the line and return false if the parameter is valid
		}
		
		return $line;
	}
  
	
	
	/**
	* Set a parameter
	* 
	* @param string $param The parameter name to store
	* @param string $value The value to set
	* @return bool True = the parameter has been set, false = the parameter was invalid
	*/
	private function setParam($param, $value) {
		if (!array_key_exists($param, $this->params)) return false;
		
		if ($param == 'param' || $param == 'return') {
      $words=explode(' ',$value);
      $value=array();
      $value['type']=trim(array_shift($words),'()');
      $value['param']=trim(array_shift($words),'$');
      if (preg_match("/\[(.*?)\]/u", $value['param'],$matches)) {
        $value['default']=trim($matches[1],'[]"\'');
      }
      $value['desc']=implode(' ',$words);
		}

    if ($param=='return')
      $this->params[$param] = $value;
    else
      $this->params[$param][] = $value;
    
		return true;
	}
    
	/**
	* Parse the string
	*/
	public function parse() {
		//Get the comment
		if(preg_match('#^/\*\*(.*)\*/#s', $this->string, $comment) === false)
			die("Error");
			
		$comment = trim($comment[1]);
		
		//Get all the lines and strip the * from the first character
		if(preg_match_all('#^\s*\*(.*)#m', $comment, $lines) === false)
			die('Error');
		
		$this->parseLines($lines[1]);
	}
  
	/**
	* Get the short description
	*
	* @return string The short description
	*/
	public function getShortDesc() {
		return $this->shortDesc;
	}
  
	/**
	* Get the long description
	*
	* @return string The long description
	*/
	public function getDesc() {
		return $this->longDesc;
	}
  
	/**
	* Get the parameters
	*
	* @return array The parameters
	*/
	public function getParams() {
    $cleanParams=$this->params;
    foreach ($cleanParams as $key => $value) {
      if (empty($value)) unset($cleanParams[$key]);
    }
		return $cleanParams;
	}
}
?>