<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Claim extends App_Controller
{
	protected $authenticate = array('agent','insurer','administrator');

	public function __construct()
	{
		$this->models[] = 'claims';

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
		if(!$this->claims->can_edit($hash))
			redirect('/claim/dashboard');

		set_missing($this->data, 'name,email,phone,claim_number');

		$this->data['claim'] = $this->db->select('claims.*')->where('hash',$hash)->get('claims')->row_array();

		if(empty($this->data['claim']))
		{
			$this->errors[] = "Report not found or not editable";
			return;
		}

		$rules = array(
			array('street_address','',true),
			array('zip','int|pos'),
			array('name','fullname'),
			array('email','email'/*, (bool)trim($this->input->post('phone'))*/),
			array('phone','phone'/*, (bool)trim($this->input->post('email'))*/),
			array('claim_number','')
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
			/*
			if($err == 'Email is required')
				$err = 'Please enter an email and/or phone number';
			*/
			$err = str_replace('Claim Number','Policy Quote Number', $err);
			$this->errors[] = $err;
			return;
		}

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
			->order_by('date','desc')
			->join('user', 'user.id = claims.user')
			->join('insurer','user.insurer = insurer.id','left')
			->get('claims')->result_array();

		$this->data['claims_table'] = array();
		foreach($this->data['claims'] as $i => $c)
		{
			$buttons = '';
			if(in_array($c['status'],['New','Pending']))
			{
				if($c['phone'])
					$buttons .= '<input type="button" onclick="claim_remind_text(this,\''.$c['hash'].'\')" value="Remind (Text)"/><br/>';
				if($c['email'])
					$buttons .= '<input type="button" onclick="claim_remind_email(this,\''.$c['hash'].'\')" value="Remind (Email)"/>';
			}
			$this->data['claims_table'][] = array(
				//isset($c['insurer_name']) ? $c['insurer_name'] : '',
				$c['date'],
				date('n/j/y g:ia', strtotime($c['date'])),
				$c['claim_number'],
				$c['status'],
				$c['name'],
				$c['phone'] && $c['email'] ? $c['phone']."<br/>".$c['email'] : $c['email'].$c['phone'],
				//$c['vin'].'<br/>'.$c['year'].' '.$c['model'].'<br/>'.$c['make'].'<br/>'.$c['body'],
				'<input type="button" onclick="claim_view(\''.$c['hash'].'\')" value="View"/><br/>'.
				'<input type="button" onclick="claim_edit(\''.$c['hash'].'\')" value="Edit"/><br/>'.
				$buttons
			);
		}
	}

	public function view($hash)
	{
		$this->min_css[] = '/plugins/jquery-ui/css/trontastic/jquery-ui-1.10.3.custom.min.css';
		$this->min_js[] = '/plugins/jquery-ui/js/jquery-ui-1.10.3.custom.min.js';
		$this->min_js[] = '/plugins/slick/slick/slick.min.js';
		$this->min_css[] = '/plugins/slick/slick/slick.css';

		/* claim data */
		$this->data['claim'] = $this->claims->get_by_hash($hash);
		$this->data['claim']['url'] = $this->config->item('base_url').'r/'.$this->data['claim']['hash'];

		$this->data['fields_we_care_about'] = array(
			'claim_number' => 'Policy Quote Number',
			'street_address' => 'Street Address',
			'zip' => 'Zip Code',
			'name' => 'Owner Name',
			'email' => 'Email Address',
			'phone' => 'Mobile Number',
			'status' => 'Report Status',
			'url' => 'Report Link'
		);

		// home data
		$this->data['home_data'] = json_decode($this->data['claim']['home_data'], true);
		unset($this->data['claim']['home_data']);
		$rules = array_merge($this->claims->page2_fields, $this->claims->page3_fields);
		$this->data['home_fields_we_care_about'] = array();
		foreach($rules as $name => $rule)
			$this->data['home_fields_we_care_about'][$name] = $rule[0];

		// custom home labels
		$this->data['home_fields_we_care_about']['furnaces'] = 'Fire Hazards';
		$this->data['home_fields_we_care_about']['dogs'] = 'Dogs';
		$this->data['home_fields_we_care_about']['work'] = 'On-Premise Business';
		$this->data['home_fields_we_care_about']['losses'] = 'Recent Losses';
		$this->data['home_fields_we_care_about']['electrical_age'] = 'Electrical Inspection';
		$this->data['home_fields_we_care_about']['beds'] = 'Bedrooms';
		$this->data['home_fields_we_care_about']['baths'] = 'Bathrooms';

		// photos
		$this->data['photos'] = $this->db->where('claim_id', $this->data['claim']['id'])
			->order_by('photo_num')
			->get('photos')
			->result_array();

		// parse exif data
		$coordinates = array();
		$time = array(); // why are we not parsing time? lazy bastard. exif=UTC, html5=unix
		foreach($this->data['photos'] as $i => $p)
		{
			$exif = json_decode($p['exif'], true);
			if(!empty($exif['GPSLatitude']) && !empty($exif['GPSLongitude']))
			{
				if(is_array($exif['GPSLatitude']))
				{
					// DMS bullshit up in this bitch
					if(empty($exif['GPSLatitudeRef']))
						$exif['GPSLatitudeRef'] = 'N';
					if(empty($exif['GPSLongitudeRef']))
						$exif['GPSLongitudeRef'] = 'W';

					$coordinates['lat'][] = dms_to_dec(
						$exif['GPSLatitude'][0],
						$exif['GPSLatitude'][1],
						$exif['GPSLatitude'][2],
						$exif['GPSLatitudeRef']
					);
					$coordinates['lon'][] = dms_to_dec(
						$exif['GPSLongitude'][0],
						$exif['GPSLongitude'][1],
						$exif['GPSLongitude'][2],
						$exif['GPSLongitudeRef']
					);
				}
				elseif(is_numeric($exif['GPSLatitude']))
				{
					// Smart people use decimals
					$coordinates['lat'][] = $exif['GPSLatitude'];
					$coordinates['lon'][] = $exif['GPSLongitude'];
				}
			}
		}

		if(!empty($coordinates))
		{
			$coordinates['lat'] = array_sum($coordinates['lat']) / count($coordinates['lat']);
			$coordinates['lon'] = array_sum($coordinates['lon']) / count($coordinates['lon']);
			$this->data['photo_coordinates'] = $coordinates;
		}
	}

	public function page_1()
	{
		$this->data['fields'] = $this->claims->new_claim_fields;

		set_missing($this->data, join(',', array_keys($this->data['fields'])));

		$this->data['dispatchers'] = $this->claims->get_dispatchers();
		if(empty($this->data['dispatchers']))
			unset($this->data['fields']['dispatcher']);

		if(empty($_POST))
		{
			$this->min_js[] = 'jquery.maskedinput.min.js';
			return;
		}

		$post = $this->input->post();

		$this->models[] = 'claim';
		$this->load->library('valid');
		$err = $this->valid->validate_lazy(
			$post,
			$this->data['fields']
		);

		$this->data = array_merge($this->data, $post);
		if($err)
		{
			/*
			if($err == 'Email is required')
				$err = 'Please enter an email and/or phone number';
			*/
			$err = str_replace('Claim Number','Policy Quote Number', $err);
			$this->errors[] = $err;
			return;
		}

		$post['next_resend'] = date('Y-m-d H:i:s', time()+86400);

		/*$post['insurer'] = $this->user->data['insurer'];*/
		$post['user'] = $this->user->data['id'];
		$post['date'] = date('Y-m-d H:i:s e');

		if(!empty($post['dispatcher']) && $post['dispatcher'] == 'Yes')
			$post['dispatcher'] = $this->data['dispatchers'][array_rand($this->data['dispatchers'])]['id'];
		else
			$post['dispatcher'] = 0;

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

	public function set_estimator($hash,$id)
	{
		if(!$this->claims->can_edit($hash))
			$this->_json(['error' => 'Permission Denied']);
		$this->db->where('hash',$hash)->update('claims', ['estimator' => $id]);
		$this->_json(['success' => $id]);
	}

	public function text_message($hash, $output=true)
	{
		$claim = $this->db->where('hash',$hash)->get('claims')->row_array();
		if(!$claim['phone'])
			$this->_json(array('success' => $claim['phone'], 'phone' => $claim['phone']));

		$report_url = $this->config->item('base_path') . '/r/' . $hash;
		$phone = '+1'.preg_replace('/\D/','',$claim['phone']);
		send_sms(
			"{$claim['name']}, follow the link below to complete your home insurance quote:\n$report_url",
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
			'Your Homeowners Insurance Quote',
			"Hello {$claim['name']},<br/><br/>

			Thank you for using RISKPIX.com for your Homeowners Insurance quote. Follow the link below to answer questions and send photos of your home so we can expedite the processing of your quote. If you have any questions please email us at or call us at 800-XXX-XXXX<br/><br/>
			<a href=\"$report_url\">$report_url</a><br/><br/>Please visit this page from a mobile device with built-in camera.",
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

		echo "\n".date('Y-m-d H:i:s')."\tQuotes to resend: ".count($claims)."\n";

		foreach($claims as $c)
		{
			// handle expiration
			if($c['resend_count'] >= $max_resends)
			{
				$this->db->where('id', $c['id'])->update('claims', array('status'=>'Expired'));
				echo date('Y-m-d H:i:s')."\tExpired Quote ID {$c['id']}\n";
				continue;
			}

			// send
			$c['resend_count'] = intval($c['resend_count']) + 1;
			if($c['email'])
			{
				$this->email_message($c['hash'], false);
				echo date('Y-m-d H:i:s')."\tEmailed Quote ID {$c['id']} to {$c['email']} ({$c['resend_count']} of $max_resends)\n";
			}
			if($c['phone'])
			{
				$this->text_message($c['hash'], false);
				echo date('Y-m-d H:i:s')."\tTexted Quote ID {$c['id']} to {$c['phone']} ({$c['resend_count']} of $max_resends)\n";
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
