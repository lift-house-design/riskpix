<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Authentication extends App_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->asides['topbar'] = 'topbar';
		$this->asides['footer'] = 'footer';
		$this->asides['notifications'] = 'notifications';
		//$this->less_css[] = 'application.less';
	}

	private function _login_redir()
	{
		$url = $this->input->cookie('target_url');
		if(!empty($this->user->data['redir_done']) || $url == '/')
			$url = $this->home_url();
		if(empty($url))
			$url = $this->home_url();
		if(empty($url))
			$url = '/';
		$this->input->set_cookie('target_url','');
		if($url != $_SERVER['REQUEST_URI'])
		{
			$this->user->data['redir_done'] = true;
			$this->session->set_userdata('user',$this->user->data);
			redirect($url);
		}
		if($url == $_SERVER['REQUEST_URI'])
			redirect('/');
		redirect('/contact');
	}

	public function home_url()
	{
		if($this->user->has_role('administrator'))
			return '/admin';
		if($this->user->has_role('agent'))
			return '/claim';
		if($this->user->has_role('dispatcher'))
			return '/dispatcher';
		if($this->user->has_role('estimator'))
			return '/estimator';
		return '/';
	}

	public function index()
	{
		redirect('/authentication/log_in');
	}

	public function expired_password()
	{
		if(empty($this->user->data['id']))
			$this->_login_redir();

		if(empty($_POST))
			return;

		$post = $this->input->post();

		if($post['password'] !== $post['confirm'])
			$this->errors[] = 'Passwords do not match.';
		elseif(empty($post['password']))
			$this->errors[] = 'Password is empty.';
		if(!empty($this->errors))
			return;

		$this->db
			->where('id', $this->user->data['id'])
			->update(
				'user',
				array(
					'password' => sha1($post['password']),
					'status' => 'ok'
				)
			);
		$this->_login_redir();
	}

	public function log_in()
	{
		if($this->user->logged_in)
			$this->_login_redir();

		if($this->input->post())
		{
			if($this->user->log_in())
			{
				if($this->user->data['status'] == 'pwdexpired')
					redirect('/authentication/expired_password');
				else
				{
					$this->_login_redir();
				}
			}else{
				//should show errors here
				$this->errors[] = 'Incorrect Information';
			}
		}
	}

	public function log_out()
	{
		$this->user->log_out();
		redirect('/');
	}

	public function forgot_password()
	{
		$rules=array(
			array(
				'field'=>'email',
				'label'=>'E-mail',
				'rules'=>'trim|required|max_length[64]|valid_email',
			),
		);

		$this->load->library('form_validation');
		$this->form_validation->set_rules($rules);

		if($this->form_validation->run()!==FALSE)
		{
			$user=$this->user->get_by(array(
				'email'=>$this->input->post('email'),
			));

			if(!empty($user))
			{
				$data=array(
					'confirm_code'=>$this->user->generate_confirm_code(),
				);
				if($this->user->update($user['id'],$data))
				{
					$url = site_url('/authentication/reset_password/'.$user['id'].'/'.$data['confirm_code']);
					$message = "<body>To reset your password, visit the link below:<br/>\n<br/>\n<a href=\"$url\">$url</a></body>";
					send_email("Password Reset", $message, $user['email']);
					$this->form_validation->reset_values();
					$this->notifications[] = 'You have been sent an e-mail with a link that will allow you to reset your password.';
				}
			}
			else
			{
				$this->form_validation->set_error('That e-mail address was not found. Please check your e-mail address and try again.');
			}
		}
	}

	public function reset_password($id,$confirm_code)
	{
		$this->data['password_reset']=FALSE;
		$this->data['confirmed']=FALSE;
		$this->data['id']=$id;
		$this->data['confirm_code']=$confirm_code;

		$this->load->library('form_validation');

		$user=$this->user->get_by(array(
			'id'=>$id,
			'confirm_code'=>$confirm_code,
		));

		if(!empty($user))
		{
			$this->data['confirmed']=TRUE;
			$this->data['email']=$user['email'];

			$rules=array(
				array(
					'field'=>'password',
					'label'=>'Password',
					'rules'=>'trim|required|sha1',
				),
				array(
					'field'=>'confirm_password',
					'label'=>'Confirm Password',
					'rules'=>'trim|required|matches[password]|sha1',
				),
			);

			$this->form_validation->set_rules($rules);

			if($this->form_validation->run()!==FALSE)
			{
				$data=array(
					'password'=>$this->input->post('password'),
					'confirm_code'=>NULL,
				);

				if($this->user->update($id,$data))
				{
					$this->data['password_reset']=TRUE;
				}
				else
				{
					$this->form_validation->set_error('There was a problem resetting your password. Please try again.');
				}
			}
		}
	}
}
