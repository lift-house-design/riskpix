<?php


/*function form_checkbox($values, $val=false, $name='', $title=false, $attr='')
{
	$html = "<table $attr>";
	foreach($values as $value){
		$html .= "<tr><td>";
		$selected = $value == $val ? 'checked' : '';
		$html .= "<input type=\"checkbox\" name=\"$name\" value=\"Yes\"/>";
		$html .= "</td><td>$title</td></tr>";
	}
	$html .= "</table>";
	return $html;
}*/

function form_select($data, $val=false, $name='', $title=false, $attr='')
{
	if($title !== false)
		$attr .= ' data-placeholder="'.$title.'" placeholder="'.$title.'"';
	if($name)
		$attr .= ' name="'.$name.'"';
	
	// wrapper for hide/show chosen
	$html = "<span id=\"chosen-wrap-$name\">";

	$html .= "<select $attr>";
	
	$selected = empty($val) ? 'selected' : '';
	if($title !== false)
		$html .= "<option value=\"\" $selected disabled>$title</option>";
		//$html .= "<option value=\"\"></option>";
	
	foreach($data as $i => $s)
	{
		if(is_numeric($i))
			$i = $s;
		$selected = $i == $val ? 'selected' : '';
		$html .= "<option value=\"$i\" $selected>$s</option>";
	}
	return $html."</select></span>";
}

function set_missing(&$array, $indexes, $make_array=false)
{
	$array = empty($array) ? array() : $array;
	$array = is_array($array) ? $array : array($array);
	foreach(explode(',',$indexes) as $i)
	{
		if(!isset($array[$i]))
			$array[$i] = false;
		if($make_array && !is_array($array[$i]))
			$array[$i] = array();
	}
}

if(!function_exists('form_field'))
{
	function form_field($label, $name, $type='text', $params=array())
	{
		$CI=get_instance();

		if($type=='text')
			$type='input';

		if(is_array($params))
		{
			$params['id']=$name;
			$params['name']=$name;	
		}
		
		return $CI->load->view('asides/field',array(
			'label'=>$label,
			'name'=>$name,
			'type'=>$type,
			'params'=>$params,
		),TRUE);
	}
}