<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Report extends App_Controller
{
	public function __construct()
	{
		$this->models[] = 'claims';

		parent::__construct();
		$this->data['progress'] = 'vehicle-info';

		$this->asides['topbar'] = 'topbar_report';
		$this->asides['footer'] = 'footer';
		$this->asides['notifications'] = 'notifications';

		array_unshift($this->min_css, '/plugins/chosen/chosen.min.css');
		$this->min_js[] = '/plugins/chosen/chosen.jquery.min.js';
		$this->min_js[] = 'jquery.cookie.js';
		
		//$this->less_css[] = 'report.less';
		$this->min_css[1] = 'report.css';
 
		$this->_check_hash();
	}

	/* Ad hoc pages */

	public function index()
	{
		$this->view = 'report/page_1';
		$this->page_1();
		return;
	}

	public function check_vin($vin)
	{
		$this->load->library('vehicle');
		$response = $this->vehicle->get_vin_data($vin);

		if(!empty($response['data']))
		{
			$response['data']['vin'] = strtoupper($vin);
			$this->db
				->where('hash', $this->data['claim']['hash'])
				->update('claims', $response['data']);
		}

		$this->_json($response);
	}

	public function _check_hash()
	{
		// redirect from short url
		if(stripos($_SERVER['REQUEST_URI'], '/r/') !== 0)
		{
			$hash = $this->input->cookie('hash');
			if(!$hash)
				redirect('/r/logged_out');

			$this->data['claim'] = $this->db->where('hash',$hash)->get('claims')->row_array();
			if(empty($this->data['claim']))
				redirect('/r/claim_not_found');

			// go to end if it's complete
			if($_SERVER['REQUEST_URI'] !== '/report/7' && in_array($this->data['claim']['status'], ['Complete','Pending Estimate']))
				redirect('/report/7');

			$this->db->where('hash', $hash)->update('claims', array('progress' => $_SERVER['REQUEST_URI']));
		}
	}

	public function start($hash)
	{		
		if($hash === 'logged_out')
		{
			$this->errors[] = '<h2>You have been automatically logged out.</h2>Please follow the link that was provided to you to continue your quote.';
			return;
		}
		if($hash === 'claim_not_found')
		{
			$this->errors[] = 'Quote not found.';
			return;
		}

		$this->data['claim'] = $this->db->where('hash',$hash)->get('claims')->row_array();
		if(empty($this->data['claim']))
		{
			$this->errors[] = 'Quote not found.';
			return;
		}

		$this->input->set_cookie('hash', $this->data['claim']['hash']);
		$this->db->where('hash', $this->data['claim']['hash'])
			->update('claims', array('user_agent' => substr($_SERVER['HTTP_USER_AGENT'],0,300))); 
		redirect($this->data['claim']['progress']);
	}

	public function page_1()
	{
		/* Just show the view if they have not clicked continue */
		if(empty($_POST)) return;

		/* send an email to dispatcher if the dispatcher exists */
		$report_url = $this->config->item('base_path') . '/d/' . $this->data['claim']['hash'];
		if($this->data['claim']['dispatcher'] > 0)
			send_email(
				'Quote #'.$this->data['claim']['claim_number'].' in Progress',
				"A homeowners insurance quote is in progress. Visit the link below to assign a cost estimator to handle the report once it is completed.<br/><br/><a href=\"$report_url\">$report_url</a>",
				$this->user->get_emails($this->data['claim']['dispatcher'])
			);

		/* Set report status to Processing */
		$this->db->where('id', $this->data['claim']['id'])->update('claims', array('status' => 'Processing'));

		if($this->data['claim']['type'] === 'home')
			redirect('/report/2');
		else
			redirect('/photo/1');
	}

	public function page_2()
	{
		$this->data['fields'] = $this->claims->page2_fields;

		set_missing($this->data, join(',', array_keys($this->data['fields'])));
		
		if(empty($_POST))
			return;

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
			$this->errors[] = $err;
			return;
		}

		// save it
		if(empty($this->data['claim']['home_data']))
			$data = array();
		else
			$data = json_decode($this->data['claim']['home_data'], true);
		
		$data = array_merge($data, $post);
		$data = json_encode($data);

		$this->db
			->where('hash', $this->data['claim']['hash'])
			->update('claims', array('home_data' => $data));
			
		redirect('/report/3');
	}

	public function page_3()
	{
		$this->data['fields'] = $this->claims->page3_fields;

		set_missing($this->data, join(',', array_keys($this->data['fields'])));
		
		if(empty($_POST))
			return;

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
			$this->errors[] = $err;
			return;
		}

		// save it
		if(empty($this->data['claim']['home_data']))
			$data = array();
		else
			$data = json_decode($this->data['claim']['home_data'], true);
		
		$data = array_merge($data, $post);
		$data = json_encode($data);

		$this->db
			->where('hash', $this->data['claim']['hash'])
			->update('claims', array('home_data' => $data));
			
		//redirect('/report/5');
		redirect('/photo/1');
	}

	public function page_4()
	{
		if(!empty($_POST))
		{
			$this->db->where('hash', $this->data['claim']['hash'])->update('claims', $this->input->post());
			redirect('/report/5');
		}
	}

	public function page_5()
	{
		$this->min_css[] = '/plugins/bxslider/jquery.bxslider.css';
		$this->min_js[] = '/plugins/bxslider/jquery.bxslider.min.js';
		$this->data['progress'] = 'take-photos';
		if(!empty($_POST))
			redirect('/photo/1');
	}

	public function photo($page=1, $retake=false)
	{
		$messages = $this->_photo_messages();
		$message_count = count($messages);

		if($page < 1 || $page > $message_count)
			redirect('/photo/1/'.$retake);
	
		// for resizing and preview
		$this->js[] = '/plugins/canvas-resize/binaryajax.js';
		$this->js[] = '/plugins/canvas-resize/exif.js';
		//$this->js[] = '/plugins/canvas-resize/jquery.canvasResize.js';
		$this->js[] = '/plugins/canvas-resize/canvasResize.js';
		
		//data data
		$this->data['progress'] = 'take-photos';
		$this->data['photo_message'] = $this->_photo_message($page);
		$this->data['photo_page'] = $page;
		$this->data['photo_count'] = $message_count;
		
		// just show form and die if no photo
		if(empty($_POST))
		{
			$this->data['action'] = "/photo/$page/$retake";
			$this->data['photo_message'] = $this->_photo_message($page);
			return;
		}
		// try to save the photo
		$path = __DIR__.'/../../assets/img/upload/';
		$path_thumb = __DIR__.'/../../assets/img/upload/thumb/';
		$url = '/assets/img/upload/';
		$url_thumb = '/assets/img/upload/thumb/';
		$name = $this->data['claim']['hash']."-$page.png";
		try{
			if(!file_exists($path))
				throw new Exception('Upload directory does not exist: '.$path);
			if(!file_exists($path_thumb))
				throw new Exception('Upload directory does not exist: '.$path_thumb);
			if(!empty($_POST['photo']))
				$this->_save_based_photo(
					$_POST['photo'],
					$path.$name
				);
			else
				$this->_save_binary_photo(
					$_FILES['photo'],
					$path.$name
				);
			$this->_make_thumb(
				$path.$name,
				$path_thumb.$name
			);
			$this->db->where('claim_id', $this->data['claim']['id'])
				->where('photo_num', $page)
				->delete('photos');
			$this->db->insert('photos', array(
				'claim_id' => $this->data['claim']['id'],
				'photo_num' => $page,
				'path' => $path.$name,
				'url' => $url.$name,
				'path_thumb' => $path_thumb.$name,
				'url_thumb' => $url_thumb.$name,
				'exif' => $_POST['exif']
			));
		}
		catch(Exception $e)
		{
			$this->_json(array('error' => $e->getMessage()));
		}

		// successful image, next page
		if($page >= $message_count)
			$this->_json(array('success' => '/report/6'));
		if($retake)
			$this->_json(array('success' => '/report/6/'.$page));

		$this->_json(array('success' => '/photo/' . ++$page));
	}

	public function page_6($refresh = 0)
	{
		$this->data['progress'] = 'take-photos';
		$this->data['refresh'] = $refresh;
		$this->data['photos'] = $this->db->where('claim_id', $this->data['claim']['id'])
			->order_by('photo_num')
			->get('photos')
			->result_array();
		if(!empty($_POST))
		{
			/* send an email to whoever cares */
			if($this->data['claim']['estimator'] > 0)
			{
				// notify cost estimator
				$report_url = $this->config->item('base_path') . '/e/' . $this->data['claim']['hash'];
				send_email(
					'Quote #'.$this->data['claim']['claim_number'].' Pending Estimate',
					"A homeowners insurance quote is waiting for a replacement cost estimate from you. Visit the link below to view photos and information provided by the homeowner and to provide a cost estimation.<br/><br/><a href=\"$report_url\">$report_url</a>",
					$this->user->get_emails($this->data['claim']['estimator'])
				);

				// set status to Pending Estimate
				$this->db->where('id', $this->data['claim']['id'])->update('claims', array('status' => 'Pending Estimate'));
			}
			elseif($this->data['claim']['dispatcher'] > 0)
			{
				// notify cost estimator
				$report_url = $this->config->item('base_path') . '/d/' . $this->data['claim']['hash'];
				send_email(
					'Quote #'.$this->data['claim']['claim_number'].' Pending Dispatch',
					"A homeowners insurance quote has been completed and is waiting for a replacement cost estimate. Visit the link below to view photos and information provided by the homeowner and to assign a cost estimator to handle the report.<br/><br/><a href=\"$report_url\">$report_url</a>",
					$this->user->get_emails($this->data['claim']['dispatcher'])
				);

				// set status to Pending Dispatch
				$this->db->where('id', $this->data['claim']['id'])->update('claims', array('status' => 'Pending Dispatch'));
			}
			else
			{
				// notify agent
				$report_url = $this->config->item('base_path') . '/v/' . $this->data['claim']['hash'];
				send_email(
					'Quote #'.$this->data['claim']['claim_number'].' Report Complete',
					$this->data['claim']['name']." has completed their homeowners insurance quote report. Follow the link below to view photos and home information.<br/><br/><a href=\"$report_url\">$report_url</a>",
					$this->user->get_emails($this->data['claim']['user'])
				);

				// set status to Complete
				$this->db->where('id', $this->data['claim']['id'])->update('claims', array('status' => 'Complete'));
			}
			redirect('/report/7');
		}
	}
	public function page_7()
	{
		$this->data['progress'] = 'submit';
	}

	public function _photo_messages()
	{
		if($this->data['claim']['type'] === 'home_photo_front_and_rear')
			return [
				'Take a photo of the Front of your Home.',
				'Take a photo of the Rear of your Home.',
			];

		$messages = array(
			'Take a photo of your Street Address.',
			'Take a photo of the Front Right of your Home.',
			'Take a photo of the Front Left of your Home.',
			'Take a photo of the Rear Left of your Home.',
			'Take a photo of the Rear Right of your Home.',
			'Take a photo of your Fence.',
			
			array('Take a photo of your Garage.', 'garage', array('Detached')),
			array('Take a photo of your Trampoline.', 'trampoline', array('Has Trampoline')),
			array('Take a photo of your Pool.', 'pool', array('Above Ground','Below Ground')),
			array('Take a photo of your Hot Tub.', 'hot_tub', array('Has Hot Tub')),
			array('Take a photo of your Dog(s).', 'dogs', true),
			
			'Take a photo of your Kitchen.',
			array('Take a photo of your Second Kitchen', 'kitchens', array(2, 3, '>3')),
			array('Take a photo of your Third Kitchen', 'kitchens', array(3, '>3')),
			array('Take a photo of your Forth Kitchen', 'kitchens', array('>3')),

			'Take a photo of your Master Bath.',
			array('Take a photo of your Second Bath', 'baths', array(1.5, 1.75, 2, 2.5, 2.75, 3, '>3')),
			array('Take a photo of your Third Kitchen', 'baths', array(2.5, 2.75, 3, '>3')),
			array('Take a photo of your Forth Kitchen', 'baths', array('>3'))
		);

		$required_messages = [];

		$data = json_decode($this->data['claim']['home_data'], true);

		foreach($messages as $i => $options)
		{
			if(!is_array($options))
			{
				$required_messages[] = $options;
				continue;
			}

			$message = $options[0];
			$name = $options[1];
			$values = $options[2];

			if(is_array($values))
			{
				if(!empty($data[$name]))
					if(in_array($data[$name], $values))
						$required_messages[] = $message;
			}
			else
			{
				if(!$values)
					$required_messages[] = $message;
				elseif(!empty($data[$name]))
					$required_messages[] = $message;
			}
		}

		return $required_messages;
	}

	// photo page is the same, only the message changes
	public function _photo_message($page)
	{
		$messages = $this->_photo_messages();
		return $messages[$page - 1];
	}

	// save a data_uri [output of canvas resize] photo into a binary file
	public function _save_based_photo(&$data, $path)
	{
		$data = explode(',',$data);
		if(count($data) !== 2)
			throw new Exception("Error uploading photo.");

		if(!preg_match('/^data\:image\/([^;]+)\;base64$/', $data[0], $match))
			throw new Exception("Error reading photo. Please try again.");

		$type = $match[1];
		$data = base64_decode($data[1]);
		file_put_contents($path, $data);
		$this->load->library('upload');
		$this->upload->img_to_png($path, 800, 10);
	}

	// save a data_uri [output of canvas resize] photo into a binary file
	public function _save_binary_photo(&$data, $path)
	{
		if(empty($_FILES['photo']['tmp_name']))
			throw new Exception('Error Uploading Photo.');
		copy($_FILES['photo']['tmp_name'], $path);
		$this->load->library('upload');
		$this->upload->img_to_png($path, 800, 10);
	}

	public function _make_thumb($path, $path_thumb)
	{
		$this->load->library('upload');
		copy($path, $path_thumb);
		$this->upload->img_to_png($path_thumb, 200, 15);
	}
}