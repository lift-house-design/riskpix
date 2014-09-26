<?php

if(!function_exists('get_directories'))
{
	function get_directories($path,$full_path=FALSE)
	{
		$directories=glob($path.'/*',GLOB_ONLYDIR);

		if($full_path)
			return $directories;

		$directory_names=array();

		foreach($directories as $dir)
		{
			$directory_names[]=trim(str_replace(dirname($dir),'',$dir),'/\\');
		}
		
		return $directory_names;
	}
}