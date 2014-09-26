<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class App_Loader extends CI_Loader
{
	/**
	 * Restores package paths to their original state (mostly the application path)
	 */
	public function reset_package_paths()
	{
		$this->_ci_library_paths = array(APPPATH, BASEPATH);
		$this->_ci_helper_paths = array(APPPATH, BASEPATH);
		$this->_ci_model_paths = array(APPPATH);
		$this->_ci_view_paths = array(APPPATH.'views/'	=> TRUE);
	}

	/**
	 * Same as add_package_path(), except it removes any previously added paths so that
	 * only the set path is used when loading resources. This is done to prevent collision
	 * between other modules that may share resource names
	 */
	public function set_package_path($path,$view_cascade=TRUE)
	{
		// Reset the default paths
		$this->reset_package_paths();
		
		// Now add the new path
		$this->add_package_path($path,$view_cascade);
	}
}