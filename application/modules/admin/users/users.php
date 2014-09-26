<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Users_admin_module extends Admin_module
{
	public $name='Users';

	public function index()
	{
		// Load dataTables
		$this->js[]=array(
			'file'=>'js/jquery.dataTables.min.js',
			'type'=>'plugins/datatables',
		);
		$this->css[]=array(
			'file'=>'css/jquery.dataTables.css',
			'type'=>'plugins/datatables',
		);
		$this->js[]='pages/administration-users-index.js';

		$this->data['entries']=$this->user->get_all();
	}

	public function create()
	{
		$this->load->library('form_validation');
		$this->form_validation->set_rules(array(
			array(
				'field'=>'email',
				'label'=>'E-mail',
				'rules'=>'trim|required|max_length[64]|valid_email|is_unique[user.email]',
			),
			array(
				'field'=>'first_name',
				'label'=>'First Name',
				'rules'=>'trim|required|max_length[32]',
			),
			array(
				'field'=>'last_name',
				'label'=>'Last Name',
				'rules'=>'trim|max_length[32]',
			),
			array(
				'field'=>'password',
				'label'=>'Password',
				'rules'=>'required|matches[confirm_password]|sha1',
			),
			array(
				'field'=>'confirm_password',
				'label'=>'Confirm Password',
				'rules'=>'required',
			),
		));

		if($this->form_validation->run()!==FALSE)
		{
			$data=$this->input->post();

			if($this->user->insert($this->input->post()))
			{
				if(empty($data['roles']))
					$data['roles']=array();

				$id=$this->db->insert_id();

				$this->user->save_roles($id,$data['roles']);

				$this->set_notification('The account was successfully created.');

				redirect('administration/users');
			}
		}

		$this->js[]='jquery.maskedinput.min.js';
		$this->js[]='pages/administration-users-save.js';
	}

	public function edit($id)
	{
		$this->load->library('form_validation');
		$this->form_validation->set_rules(array(
			array(
				'field'=>'email',
				'label'=>'E-mail',
				'rules'=>'trim|required|max_length[64]|valid_email'.( $this->input->post('email') != $this->input->post('_email') ? '|is_unique[user.email]' : '' ),
			),
			array(
				'field'=>'first_name',
				'label'=>'First Name',
				'rules'=>'trim|required|max_length[32]',
			),
			array(
				'field'=>'last_name',
				'label'=>'Last Name',
				'rules'=>'trim|max_length[32]',
			),
			array(
				'field'=>'password',
				'label'=>'Password',
				'rules'=>'matches[confirm_password]|sha1',
			),
		));

		if($this->form_validation->run()!==FALSE)
		{
			$data=$this->input->post();

			// Change password
			if(empty($data['password']))
				unset($data['password']);

			$data['phone_text_capable']=isset($data['phone_text_capable']) ? 1 : 0;

			if($this->user->update($data['id'],$data))
			{
				if(empty($data['roles']))
					$data['roles']=array();

				$this->user->save_roles($data['id'],$data['roles']);

				$this->set_notification('The account was successfully edited.');

				redirect('administration/users');
			}
		}

		$this->js[]='jquery.maskedinput.min.js';
		$this->js[]='pages/administration-users-save.js';

		$this->data['data']=$this->user->get($id);
	}

	public function delete($id)
	{
		$this->user->delete($id);
		$this->set_notification('The account was successfully deleted.');
		redirect('administration/users');
	}
}