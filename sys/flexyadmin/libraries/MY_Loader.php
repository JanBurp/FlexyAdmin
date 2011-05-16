<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');


/**
 * extending the loader class... needs this in config file
 * $config['subclass_prefix'] = 'MY_';
 * see the docs http://codeigniter.com/user_guide/general/core_classes.html
 * 
 * @package    default
 * @author    HdotNET
 */


class MY_Loader extends CI_Loader {

    function MY_Loader()
    {
        parent::CI_Loader();
    }
    
    /**
     * This function lets users load and instantiate models.
     * updated model function to allow calling of remote files.
     * @access    public
     * @param    string    the name of the class
     * @param    string    name for the model
     * @param    bool    database connection
     * @return    void
     **/
    function model($model, $name = '', $db_conn = FALSE){
    
        $model = str_replace(EXT,'',$model);

        if (is_array($model))
        {
            foreach($model as $babe)
            {
                $this->model($babe);    
            }
            return;
        }

        if ($model == '')
        {
            return;
        }
    
        // Is the model in a sub-folder? If so, parse out the filename and path.
        if (strpos($model, '/') === FALSE)
        {
            $path = '';
        }
        else
        {
            $x = explode('/', $model);
            $model = end($x);            
            unset($x[count($x)-1]);
            $path = implode('/', $x).'/';
        }

        if ($name == '')
        {
            $name = $model;
        }
        
        if (in_array($name, $this->_ci_models, TRUE))
        {
            return;
        }
        
        $CI =& get_instance();
        if (isset($CI->$name))
        {
            show_error('The model name you are loading
 is the name of a resource that is already being used: '.$name);
        }
    
        $model = strtolower($model);
        
        $local_file = false;
        $remote_file = false;
        
        if ( ! file_exists(APPPATH.'models/'.$path.$model.EXT))
        {
            $local_file = false;
        }
        else
        {
            $local_file = true;
        }

        if ( !$local_file && ! file_exists($path.$model.EXT))
        {
            $remote_file = true;
        }
        else
        {
            $remote_file = true;
        }
        
        if(!$local_file && !$remote_file)
        {
            show_error('Unable to locate the model you have specified:<br />'.$path.$model);
        }
        
        if ($db_conn !== FALSE AND ! class_exists('CI_DB'))
        {
            if ($db_conn === TRUE)
                $db_conn = '';
        
            $CI->load->database($db_conn, FALSE, TRUE);
        }
    
        if ( ! class_exists('Model'))
        {
            load_class('Model', FALSE);
        }
        
        if($local_file)
        {
            require_once(APPPATH.'models/'.$path.$model.EXT);
        }
        else
        {
            require_once($path.$model.EXT);
        }
        
        $model = ucfirst($model);
                
        $CI->$name = new $model();
        $CI->$name->_assign_libraries();
        
        $this->_ci_models[] = $name;    
    }
}

?>
