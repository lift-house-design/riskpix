<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Modules
{
	protected $_ci;

	protected $_module_instances=array();

	public function __construct()
	{
		$this->_ci=get_instance();
		$this->_ci->load->helper('file');
	}

	public function get_module_types()
	{
		$module_path=$this->_ci->config->item('module_path');
		return get_directories($module_path);
	}

	public function get_modules($type)
	{
		$module_path=$this->_ci->config->item('module_path');
		if($this->type_exists($type)===FALSE)
			return FALSE;

		$modules=array();

		foreach(get_directories($module_path.'/'.$type) as $dir)
		{
			if($this->module_exists($dir,$type)===TRUE)
				$modules[]=$dir;
		}

		return $modules;
	}

	/**
	 * Checks to see if specified module type exists (does the directory exist)
	 */
	public function type_exists($type)
	{
		$module_path=$this->_ci->config->item('module_path');
		return file_exists($module_path.'/'.$type);
	}

	/**
	 * Checks to see if the specified module and module type exists (does the module file exist)
	 */
	public function module_exists($name,$type)
	{
		$module_path=$this->_ci->config->item('module_path');
		return file_exists($module_path.'/'.$type.'/'.$name.'/'.$name.'.php');
	}

	public function get_instance($name,$type)
	{
		if(!empty($this->_module_instances))
		{
			if(!empty($this->_module_instances[$type][$name]))
			{
				$module=$this->_module_instances[$type][$name];
				$module->set_package_path();
				return $module;
			}
		}

		if($this->module_exists($name,$type)===FALSE)
			return FALSE;

		$module_path=$this->_ci->config->item('module_path');
		require_once($module_path.'/'.$type.'/'.$name.'/'.$name.'.php');

		$module_class=$name.'_'.$type.'_module';

		$CI=get_instance();
		$module=new $module_class($CI);
		$module->set_package_path();
		$this->_module_instances[$type][$name]=$module;

		return $module;
	}
}

class Module
{
	public $key;
	
	public $type;

	protected $_ci;

	public function __construct($CI)
	{
		$this->_ci=$CI;

		// Determine the module type and key
		$classname=strtolower(get_class($this));
		$classname_parts=explode('_',$classname);

		array_pop($classname_parts); // Remove the "_module" part
		$this->type=array_pop($classname_parts);
		$this->key=implode('_',$classname_parts);
	}

	public function set_package_path()
	{
		// Set this directory to the package paths in order to load components from it
		$module_path=$this->_ci->config->item('module_path');
		$this->_ci->load->set_package_path($module_path.'/'.$this->type.'/'.$this->key);
	}
}

class Admin_module extends Module
{
	public $name='Unknown Admin Module';

	public $data=array();

	public $js=array();

	public $css=array();

	public function __construct($CI)
	{
		parent::__construct($CI);

		$this->_ci->nav[$this->key]=$this->name;
	}

	/*
	|--------------------------------------------------------------------------
	| Magic Methods
	|--------------------------------------------------------------------------
	|
	| We want this module to behave as a controller, so any calls to $this
	| should be re-routed back to the administration controller. The exception
	| to this is array properties; they throw the "indirect modification of
	| an overloaded property error", so $this->data is handled in a different
	| way.
	|
	*/
	public function __get($name)
	{
		return $this->_ci->_module_get($name);
	}

	public function __set($name,$value)
	{
		$this->_ci->_module_set($name,$value);
	}

	public function __call($method,$args)
	{
		$this->_ci->_module_call($method,$args);
	}
}