<?php

function html_table($data, $head=array(), $attr='')
{
	$html = "<table $attr>";
	if($head)
	{
		$html .= "<thead>";
		foreach($head as $h)
			$html .= "<th>$h</th>";
		$html .= "</thead>";
	}
	if(is_array($data))
	{
		foreach($data as $i => $row)
		{
			$html .= "<tr>";
			if(is_array($row))
				foreach($row as $cell)
					$html .= "<td>$cell</td>";
			else
				$html .= "<td>$i</td><td>$row</td>";
			$html .= "</tr>";
		}
	}
	return $html . "</table>";
}

if(!function_exists('css'))
{
	function css($css)
	{
		$html='';
		
		foreach($css as $css_file)
		{
			if(strpos($css_file,'//') === 0)
				$url = $css_file;
			elseif(strpos($css_file,'/') === 0)
				$url = "/assets" . $css_file;
			else
				$url = "/assets/css/" . $css_file;
			$html .= '<link href="'.$url.'" rel="stylesheet" type="text/css"/>'."\n";
		}
		return $html;
	}
}

/* Uses minify library... make sure it's installed */
if(!function_exists('min_css'))
{
	function min_css($css)
	{
		$html='';
		$min_url = '/min/b=assets&f=';
		$min_urls = array();
		foreach($css as $file)
		{
			if(strpos($file,'//') === 0)
				$html .= '<link href="'.$file.'" rel="stylesheet" type="text/css"/>'."\n";
			elseif(strpos($file,'/') === 0)
				$min_urls[] = substr($file,1);
			else
				$min_urls[] = 'css/'.$file;
		}
		if(!empty($min_urls))
			$html .= '<link href="' . $min_url . join(',',$min_urls) . '" rel="stylesheet" type="text/css"/>'."\n";

		return $html;
	}
}

if(!function_exists('js'))
{
	function js($js)
	{
		$html='';
		
		foreach($js as $js_file)
		{
			if(strpos($js_file,'//') === 0)
				$url = $js_file;
			elseif(strpos($js_file,'/') === 0)
				$url = "/assets" . $js_file;
			else
				$url = "/assets/js/" . $js_file;
			$html.='<script src="'.$url.'" type="text/javascript"></script>'."\n";
		}
		return $html;
	}
}

/* Uses minify library... make sure it's installed */
if(!function_exists('min_js'))
{
	function min_js($js)
	{
		$html='';
		$min_url = '/min/b=assets&f=';
		$min_urls = array();
		foreach($js as $file)
		{
			if(strpos($file,'//') === 0)
				$html .= '<script src="'.$file.'" type="text/javascript"></script>'."\n";
			elseif(strpos($file,'/') === 0)
				$min_urls[] = substr($file,1);
			else
				$min_urls[] = 'js/'.$file;
		}
		if(!empty($min_urls))
			$html .= '<script src="'.$min_url . join(',',$min_urls).'" type="text/javascript"></script>'."\n";

		return $html;
	}
}

/* 
	LessCSS should only be used for development. 
	Please compile your .less files before deployment, and make sure the less_css array is empty so that this function returns an empty string.
*/
if(!function_exists('less_css'))
{
	function less_css($css)
	{
		if(empty($css))
			return '';
		$html = '';

		foreach($css as $css_file)
		{
			$url = "/assets/less/" . $css_file;
			$html .= '<link href="'.$url.'" rel="stylesheet/less" type="text/css"/>'."\n";
		}
		$html .= '<script>less = {env: "development", poll: 1000};</script>'."\n";
		$html .= '<script src="/assets/less/less-1.6.1.min.js" type="text/javascript"></script>'."\n";
		$html .= '<script>less.watch()</script>'."\n";
		return $html;
	}
}

?>