<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Admin extends App_Controller
{
	protected $authenticate = array('administrator');

	public function __construct()
	{

		parent::__construct();

		$this->asides['topbar'] = 'topbar';
		$this->asides['footer'] = 'footer';
		$this->asides['notifications'] = 'notifications';
		$this->js[] = '/plugins/tinymce4.0.11/js/tinymce/tinymce.min.js';
		$this->min_js[] = '/plugins/fancybox2/jquery.fancybox.pack.js';
		$this->min_js[] = 'admin.js';
	}

	public function index()
	{
		$post = $this->input->post();
		if(is_array($post))
			$post = array_map('trim', $post);
		if(isset($post['action']))
		{
			$action = $post['action'];
			unset($post['action']);
			if($action === 'Save Content')
			{
				$this->content->update($post['name'],$post['content']);
			}
			elseif($action === 'Save Configuration')
			{
				$this->configuration->save($post);
			}
			elseif($action === 'Add User')
			{
				if(empty($post['email']) || empty($post['password']))
				{}
				else
				{
					$this->_add_user($post);
				}
			}
		}
		$this->data['configuration'] = $this->configuration->get_all();

		$this->data['users'] = $this->db->query('select id,email,role from user, role where id=user_id order by role,email')->result_array();

		$this->data['fields_add_user'] = array(
			'email' => array('Email Address', 'text', ''),
			'password' => array('Password', 'text', ''),
			'role' => array('Role', 'select', array('agent','estimator','dispatcher','administrator')),
			'estimator_name' => array('Estimator Name', 'text', '', false, array('role', array('estimator')))
		);
		set_missing($this->data, join(',', array_keys($this->data['fields_add_user'])));
	}

	private function _add_user(&$post)
	{
		if(empty($post['role']))
			$post['role'] = 'agent';
		$role = $post['role'];
		unset($post['role']);
		$post['password'] = sha1($post['password']);

		if(!empty($post['estimator_name']))
		{
			$post['estimator_data'] = json_encode(array('name'=>$post['estimator_name']));
		}
		unset($post['estimator_name']);
		$post['daddy'] = $this->user->data['id'];
		
		$this->db->insert('user',$post);
		$this->db->insert('role',array('user_id'=>$this->db->insert_id(),'role' => $role));
	}

	public function user_delete($id)
	{
		$this->load->model('claims_model','claims');
		$id = intval($id);
		$res = $this->db->query('select role from role where user_id='.$id)->row_array();
		if($res['role'] != 'administrator')
		{
			$this->claims->delete_all($id);
			$this->db->where('user_id',$id)->delete('role');
			$this->db->where('id',$id)->delete('user');
		}
		redirect('/admin/');
	}
}
