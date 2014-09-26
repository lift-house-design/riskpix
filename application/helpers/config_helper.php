<?
function config_merge($array,$data){
	$ci =& get_instance();
	$ci->config->set_item(
		$array,
		array_merge(
			$ci->config->item($array),
			$data
		)
	);
}
?>