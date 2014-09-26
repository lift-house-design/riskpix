<?php

if (!function_exists('asset'))
{
	function asset($path,$type=FALSE)
	{
		// Ignore absolute paths and same-domain paths (//)
		if(substr($path,0,7)=='http://' || substr($path,0,8)=='https://' || substr($path,0,2)=='//')
			return $path;
		
		$asset_url=get_instance()->config->item('assets_url');
		return '/'.trim($asset_url,'/').'/'.( empty($type) ? '' : $type.'/' ).$path;
	}
}