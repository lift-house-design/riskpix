<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
	
/* For Client Configuration */
class Configuration_model extends App_Model
{

	public function __construct()
	{
		parent::__construct();
	}

	public function save($data)
	{
		foreach($data as $name => $value)
			$this->db->where('name',$name)->update('configuration',array('value'=>$value));
	}

	public function get($name)
	{
		/*
		$res = $this->db->where('name',$name)->select('content')->get('content')->row_array();
		if(!$res)
			return '';
		return $res['content'];
		*/
	}
	public function get_all()
	{
		$res = $this->db->get('configuration')->result_array();
		return $res;
	}
	public function load()
	{
        $config = $this->db->get('configuration')->result_array();
        foreach($config as $c)
        	if($c['value'] !== '')
        		$this->config->set_item($c['name'], $c['value']);
	}
}