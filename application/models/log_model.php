<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
	
	class Log_model extends App_Model
	{
		public $error_email;

		public function __construct()
		{
			parent::__construct();
			$this->error_email = $this->config->item('error_email');
		}

		public function log($message,$data=array())
		{
			$this->db->set('time', 'NOW()', FALSE);
			$this->db->insert('log',array(
				'type' => 'log',
				'message' => $message,
				'data' => json_encode($data)
			));
		}

		public function error($message,$data=array(),$die=false)
		{
			$this->db->set('time', 'NOW()', FALSE);
			$this->db->insert('log',array(
				'type' => 'error',
				'message' => $message,
				'data' => json_encode($data)
			));
			ob_start();
			var_dump($data);
			$email = $message . "\n\n" . ob_get_contents();
			ob_end_clean();
			$subject = substr($message,0,120);
			send_email($subject,$email,$this->error_email,array());
			if($die)
				die;
		}
	}
	
/* End of file user_model.php */
/* Location: ./application/models/user_model.php */