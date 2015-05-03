<?php 
/**
* Include parser.php to parse the phpDoc style comments into easy to 
* read HTML.
*/
require('parser.php');
/**
* Runtime documentor for PHP
* 
* Displays all functions, constants and classes available at the point
* of initialisation.  Options available allow the system to only show
* user defined functions/constants/classes or to display all available.
* 
* @author Jan den Besten & Murray Picton
*/
class Doc {
	
	/**
	* Storge for object in singular pattern
	*/
	private static $instance;
	
	/**
	* Function storage array
	*/
	private $functions 	= array();
	
	/**
	* Class storage array
	*/
	private $classes 	= array();
	
	/**
	* Constant storage array
	*/
	private $constants 	= array();
	
  
  private $excluded_paths=array('codeigniter','__');
  private $excluded_files=array('index.php');
  
  
  
	/**
	* Initialise all variables and get all defined assets
	*
	* Gets all declared functions, classes and constants available
	* when called.  Accepts single parameter of whether or not to
	* get only user defined assets or all assets
	* 
	* @param bool $showall true = show system & user assets, false = only user assets
	*/
	public function __construct() {
		//Get lists
		$functions	= get_defined_functions();
		$classes	= get_declared_classes();
		$constants	= get_defined_constants(true);
		
		/**
		* Parse functions
		*/
		$this->functions = $this->parseFunctions($functions['user']); //Get our user functions
		
		foreach($this->parseClasses($classes) as $class) {
			if($class->isUserDefined()) $this->classes[] = $class; //Only get user defined classes
		}
		$this->constants = $constants['user']; //Only get user defined constants
		
		/**
		* Sort all my arrays into alphabetical order
		*/
		if(is_array($this->constants)) asort($this->constants);
		usort($this->functions, array($this, 'sort'));
		usort($this->classes, array($this, 'sort'));
	}
	
	/**
	* Parse functions
	*
	* Take a list of function names and return a list of ReflectionFunction
	* objects; one for each function
	*
	* @param array $functions Array of functions to parse
	* @return array Array of ReflectionFunction objects
	*/
	protected function parseFunctions($functions) {
		$functionList = array();
		foreach($functions as $func) {
			$functionList[] = new ReflectionFunction($func);
		}
		return $functionList;
	}
	
	/**
	* Parse classes
	*
	* Take a list of class names and return a list of ReflectionClass
	* objects; one for each class
	*
	* @param array $classes Array of classes to parse
	* @return array Array of ReflectionClass objects
	*/
	protected function parseClasses($classes) {
		$classList = array();
		foreach($classes as $class) {
			$classList[] = new ReflectionClass($class);
		}
		return $classList;
	}
	
	/**
	* Custom sort function that sorts according to short name
	*
	* @param mixed $item1 Reflection object with method getName
	* @param mixed $item2 Reflection object with method getName
	* @return int
	*/
	protected function sort($item1, $item2) {
		return strcmp($item1->getName(), $item2->getName());
	}
	
	
	/**
	* Parse the comment and return the parser object
	*
	* @param mixed $item ReflectionFunction or ReflectionClass object
	* @return Parser Parser object
	*/
	protected function parseComment($item) {
		if(!$comment = $item->getDocComment()) return false;
		
		$parser = new Parser($comment);
		$parser->parse();
		return $parser;
	}
	
	/**
	* Format a filepath according to settings
	*
	* @param string $filepath The filepath to format
	* @return string Formatted filepath
	*/
	protected function formatFilePath($filepath) {
		return str_replace('/Users/jan/Sites/FlexyAdmin/FlexyAdminDEMO/','',$filepath);
	}
	
  
  public function doc() {
    $doc=array();

    // Classes
    foreach ($this->classes as $class) {
      $file=$this->formatFilePath($class->getFileName());
      $comm=$this->docComment($class);
      if (!isset($comm['ignore']) and $this->not_excluded($file)) {
        // trace_($file);
        $properties=$class->getProperties(ReflectionProperty::IS_PUBLIC | ReflectionProperty::IS_PROTECTED);
        foreach ($properties as $key => $value) {
          $name=$value->getName();
          $properties[$name]['doc']=array();
          $properties[$name]=$this->docComment($value);
          $declareClass=$value->getDeclaringClass()->getName();
          if ($declareClass!=$class->getName()) {
            unset($properties[$name]);
            // $properties[$name]['doc']['inherited']=$declareClass;
            // if (substr($declareClass,0,2)=='CI') unset($properties[$name]);
          }
          unset($properties[$key]);
        }
        ksort($properties);
        $className=$class->getName();
        $parent=$class->getParentClass();
        if ($parent) $parent=$parent->getName();
        $doc['classes'][$className]=array(
          'parent'      => $parent,
          'file'        => $file,
          'lines'       => $class->getStartLine() . " - " . $class->getEndLine(),
          'doc'         => $comm,
          'properties'  => $properties,
          'methods'     => $this->docFunctions($class->getMethods(),$className),
        );
      }
    }

    // Helper functions
    $doc['functions']=array();
    $functions=$this->docFunctions($this->functions);

    foreach ($functions as $key => $value) {
      $file=basename($value['file']);
      $doc['functions'][$file][$key]=$value;
    }
    ksort($doc['functions']);
    return $doc;
  }
  
  private function not_excluded($file) {
    $excluded=in_array($file,$this->excluded_files);
    if (!$excluded) {
      foreach ($this->excluded_paths as $path) {
        $excluded=($excluded OR (strpos($file,$path)>0));
      }
    }
    return !$excluded;
  }

  private function docComment($obj) {
    $doc=$obj->getDocComment();
    if (!empty($doc)) {
      $p = new Parser($doc); 
      $p->parse();
      $params = $p->getParams();
      $description=$p->getDesc();
      if (!empty($description)) $params['description']=$description;
      $description=$p->getShortDesc();
      if (!empty($description)) $params['shortdescription']=$description;
      return $params;
    }
    return $doc;
  }
  
  private function docFunctions($functions,$class=false) {
    foreach ($functions as $key => $value) {
      $file=$this->formatFilePath($value->getFileName());
      $comm=$this->docComment($value);
      $name=$value->getName();
      if (!isset($comm['ignore']) and $this->not_excluded($file)) { // and (substr($name,0,1)!='_')) { // and !($class AND !$value->isPublic())) {
        $functions[$name]=array(
          'file'        => $this->formatFilePath($file),
          'lines'       => $value->getStartLine() . " - " . $value->getEndLine(),
          'doc'         => $comm,
        );
        if ($class) {
          unset($functions[$name]['file']);
          $declareClass=$value->getDeclaringClass()->getName();
          if ($declareClass!=$class) {
            unset($functions[$name]);
            // $functions[$name]['doc']['inherited']=$declareClass;
          }
        }
      }
      unset($functions[$key]);
    }
    ksort($functions);
    return $functions;
  }
  

  
}
?>