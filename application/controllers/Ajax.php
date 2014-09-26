<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
	This file is used for AJAX Requests. 
	It will output $this->data['out'] in json format.
*/

class Ajax extends App_Controller
{
	public function __construct()
	{
		$this->layout = false;
		$this->view = 'ajax.php';
		parent::__construct();
	}

	public function index()
	{
		$this->data['out'] = array('Hello','Hi');
	}
}