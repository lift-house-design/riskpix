<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Claim extends App_Controller
{
	protected $authenticate = array('dispatcher','insurer','administrator');

	public function __construct()
	{
		parent::__construct();
		$this->asides['topbar'] = 'topbar';
		$this->asides['footer'] = 'footer';
		$this->asides['notifications'] = 'notifications';

		//$this->less_css[] = 'claim.less';
		$this->min_css[1] = 'claim.css';
	}

	/* Ad hoc pages */

	public function index()
	{
		redirect('/claim/1');
	}

	public function edit($hash)
	{
		set_missing($this->data, 'name,email,phone,claim_number,vin,vin_override');

		if(!in_array('administrator', $this->user->data['roles']))
		{
			$this->db->where('user.insurer', $this->user->data['insurer']);
			$this->db->join('user', 'user.id = claims.user');
			if(!in_array('insurer', $this->user->data['roles']))
				$this->db->where('user', $this->user->data['id']);
		}
		$this->data['claim'] = $this->db->select('claims.*')->where('hash',$hash)->get('claims')->row_array();

		if(empty($this->data['claim']))
		{
			$this->errors[] = "Claim not found or not editable";
			return;
		}

		$rules = array(
			array('name','fullname'),
			array('email','email', (bool)trim($this->input->post('phone'))),
			array('phone','phone', (bool)trim($this->input->post('email'))),
			array('claim_number',''),
			array('vin','vin',true)
		);

		if(empty($_POST))
		{
			$this->data = array_merge($this->data, $this->data['claim']);
			return;
		}

		$post = $this->input->post();

		$this->models[] = 'claim';
		$this->load->library('valid');
		$err = $this->valid->validate(
			$post,
			$rules
		);

		$this->data = array_merge($this->data, $post);
		if($err)
		{
			if($err == 'Email is required')
				$err = 'Please enter an email and/or phone number';
			$this->errors[] = $err;
			return;
		}

		/* check vin */
		if(!empty($post['vin']))
		{
			$this->load->library('vehicle');
			$response = $this->vehicle->get_vin_data($post['vin']);

			if(!empty($response['data']))
			{
				$response['data']['vin'] = strtoupper($post['vin']);
				$post = array_merge($post, $response['data']);
			}
			elseif($this->input->post('vin_override') !== $this->input->post('vin'))
			{
				/* Just give a warning on VIN failure */
				$this->errors[] = "VIN <b>" . $post['vin'] . "</b> was not found in our records. <br/>If you are sure this is correct, you can resubmit this form to override this error.";
				$this->data['vin_override'] = $post['vin'];
				return;
			}
		}
		unset($post['vin_override']);
		//$post['next_resend'] = date('Y-m-d H:i:s', time()+86400);

		$this->db->where('hash', $hash)->update('claims', $post);
		redirect('/claim/dashboard');
	}

	public function dashboard()
	{
		$this->min_css[] = '/plugins/jquery-ui/css/trontastic/jquery-ui-1.10.3.custom.min.css';
		$this->min_js[] = '/plugins/jquery-ui/js/jquery-ui-1.10.3.custom.min.js';
		$this->min_css[] = '/plugins/datatables/css/jquery.dataTables.css';
		$this->min_js[] = '/plugins/datatables/js/jquery.dataTables.min.js';

		if(!in_array('administrator', $this->user->data['roles']))
		{
			$this->db->where('user.insurer', $this->user->data['insurer']);
			if(!in_array('insurer', $this->user->data['roles']))
				$this->db->where('claims.user', $this->user->data['id']);
		}
		$this->data['claims'] = $this->db
			->select('claims.*,insurer.name as insurer_name')
			->order_by('id','desc')
			->join('user', 'user.id = claims.user')
			->join('insurer','user.insurer = insurer.id','left')
			->get('claims')->result_array();

		$this->data['claims_table'] = array();
		foreach($this->data['claims'] as $i => $c)
		{
			$this->data['claims_table'][] = array(
				isset($c['insurer_name']) ? $c['insurer_name'] : '',
				$c['claim_number'],
				$c['status'],
				$c['name'],
				$c['phone'] && $c['email'] ? $c['phone']."<br/>".$c['email'] : $c['email'].$c['phone'],
				//$c['vin'].'<br/>'.$c['year'].' '.$c['model'].'<br/>'.$c['make'].'<br/>'.$c['body'],
				'<input type="button" onclick="claim_view(\''.$c['hash'].'\')" value="View"/><br/>'.
				'<input type="button" onclick="claim_edit(\''.$c['hash'].'\')" value="Edit"/><br/>'.
				($c['phone'] ? '<input type="button" onclick="claim_remind_text(this,\''.$c['hash'].'\')" value="Remind (Text)"/><br/>' : '').
				($c['email'] ? '<input type="button" onclick="claim_remind_email(this,\''.$c['hash'].'\')" value="Remind (Email)"/>' : '')
				//'<input type="button" onclick="claim_remind(this,\''.$c['hash'].'\')" value="Remind"/>'
			);
		}
	}

	public function view($hash)
	{
		$this->data['claim'] = $this->db->where('hash',$hash)->get('claims')->row_array();
		$this->data['photos'] = $this->db->where('claim_id', $this->data['claim']['id'])
			->order_by('photo_num')
			->get('photos')
			->result_array();
	}

	public function page_1()
	{

		set_missing($this->data, 'name,email,phone,claim_number,vin,vin_override');
		$rules = array(
			array('name','fullname'),
			array('email','email', (bool)trim($this->input->post('phone'))),
			array('phone','phone', (bool)trim($this->input->post('email'))),
			array('claim_number',''),
			array('vin','vin',true)
		);

		if(empty($_POST))
			return;

		$post = $this->input->post();

		$this->models[] = 'claim';
		$this->load->library('valid');
		$err = $this->valid->validate(
			$post,
			$rules
		);

		$this->data = array_merge($this->data, $post);
		if($err)
		{
			if($err == 'Email is required')
				$err = 'Please enter an email and/or phone number';
			$this->errors[] = $err;
			return;
		}

		/* check vin */
		if(!empty($post['vin']))
		{
			$this->load->library('vehicle');
			$response = $this->vehicle->get_vin_data($post['vin']);

			if(!empty($response['data']))
			{
				$response['data']['vin'] = strtoupper($post['vin']);
				$post = array_merge($post, $response['data']);
			}
			elseif($this->input->post('vin_override') !== $this->input->post('vin'))
			{
				/* Just give a warning on VIN failure */
				$this->errors[] = "VIN <b>" . $post['vin'] . "</b> was not found in our records. <br/>If you are sure this is correct, you can resubmit this form to override this error.";
				$this->data['vin_override'] = $post['vin'];
				return;
			}
		}
		unset($post['vin_override']);
		$post['next_resend'] = date('Y-m-d H:i:s', time()+86400);

		/*$post['insurer'] = $this->user->data['insurer'];*/
		$post['user'] = $this->user->data['id'];

		$this->db->insert('claims', $post);
		$id = $this->db->insert_id();

		$this->load->library('base62');
		$hash = $this->base62->convert($id+time(),10,62);
		$this->db->where('id',$id)->update('claims', array('hash' => $hash));
		$this->data['hash'] = $hash;
		$this->data['report_url'] = $this->config->item('base_path') . '/r/' . $hash;
		$this->data['claim'] = $this->db->where('hash',$hash)->get('claims')->row_array();
		$this->view = 'claim/page_2';
	}

	public function text_message($hash, $output=true)
	{
		$claim = $this->db->where('hash',$hash)->get('claims')->row_array();
		if(!$claim['phone'])
			$this->_json(array('success' => $claim['phone'], 'phone' => $claim['phone']));

		$report_url = $this->config->item('base_path') . '/r/' . $hash;
		$phone = '+1'.preg_replace('/\D/','',$claim['phone']);
		send_sms(
			"{$claim['name']}, follow the link below to complete your vehicle damage report:\n$report_url",
			$phone
		);
		if($output)
			$this->_json(array('success' => $claim['phone'], 'phone' => $phone));
	}

	public function email_message($hash, $output=true)
	{
		$claim = $this->db->where('hash',$hash)->get('claims')->row_array();
		if(!$claim['email'])
			$this->_json(array('success' => $claim['email']));
		$report_url = $this->config->item('base_path') . '/r/' . $hash;
		send_email(
			'Your [Insurance Company] Damage Report',
			"Hello {$claim['name']},<br/><br/>

			Thank you for agreeing to process your damage claim using RISKPIX.com. Follow the link below to send photos of your damaged vehicle, and we will expedite the processing your claim. If you have any questions please email us at or call us at 800-XXX-XXXX<br/><br/>
			<a href=\"$report_url\">$report_url</a><br/><br/>Please visit this site from a mobile device with built-in camera.",
			$claim['email']
		);
		if($output)
			$this->_json(array('success' => $claim['email']));
	}

	public function cron_resend()
	{
		set_time_limit(0);
		$this->view = false;
		$this->authenticate = false;
		$max_resends = $this->config->item('max_resends');
		$log = '';

		$claims = $this->db
			->where("status in ('New','Processing')")
			->where("resend_count <= $max_resends")
			->where('next_resend<now()')
			->get('claims')->result_array();

		echo "\n".date('Y-m-d H:i:s')."\tClaims to resend: ".count($claims)."\n";

		foreach($claims as $c)
		{
			// handle expiration
			if($c['resend_count'] >= $max_resends)
			{
				$this->db->where('id', $c['id'])->update('claims', array('status'=>'Expired'));
				echo date('Y-m-d H:i:s')."\tExpired Claim ID {$c['id']}\n";
				continue;
			}

			// send
			$c['resend_count'] = intval($c['resend_count']) + 1;
			if($c['email'])
			{
				$this->email_message($c['hash'], false);
				echo date('Y-m-d H:i:s')."\tEmailed Claim ID {$c['id']} to {$c['email']} ({$c['resend_count']} of $max_resends)\n";
			}
			if($c['phone'])
			{
				$this->text_message($c['hash'], false);
				echo date('Y-m-d H:i:s')."\tTexted Claim ID {$c['id']} to {$c['phone']} ({$c['resend_count']} of $max_resends)\n";
			}

			$this->db->where('id', $c['id'])
				->update(
					'claims',
					array(
						'resend_count' => $c['resend_count'],
						'next_resend' => date('Y-m-d H:i:s', time()+86400)
					)
				);
		}
	}
}
