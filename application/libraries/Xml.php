<?

/* For messing with XML data */
class Xml
{
	// output a sitemap from data array
	// $urls = array( 0 => array('loc' => 'http://google.com/, 'lastmod'=>'2013-12-12 11:11:11', 'changefreq' => 'daily', 'priority' => 0.8), 1 => ... )
    public function get_sitemap($urls)
    {
    	$out = '<?xml version="1.0" encoding="UTF-8"?>';
		$out .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';

		foreach($urls as $url)
		{
			$out .= '<url>';
			if(!empty($url['loc']))
				$out .= '<loc>'.$url['loc'].'</loc>';
			if(!empty($url['lastmod']))
				$out .= '<lastmod>'.$url['lastmod'].'</lastmod>';
			if(!empty($url['changefreq']))
				$out .= '<changefreq>'.$url['changefreq'].'</changefreq>';
			if(!empty($url['priority']))
				$out .= '<priority>'.$url['priority'].'</priority>';
			$out .= '</url>';
		}

		$out .= '</urlset>';  
    	return $out;
    }
}
?>
