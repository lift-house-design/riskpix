<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Report extends App_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->data['progress'] = 'vehicle-info';

		$this->asides['topbar'] = 'topbar_report';
		$this->asides['footer'] = 'footer';
		$this->asides['notifications'] = 'notifications';
		
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
			if($_SERVER['REQUEST_URI'] !== '/report/7' && $this->data['claim']['status'] == 'Complete')
				redirect('/report/7');

			$this->db->where('hash', $hash)->update('claims', array('progress' => $_SERVER['REQUEST_URI']));
		}
	}

	public function start($hash)
	{		
		if($hash === 'logged_out')
		{
			$this->errors[] = '<h2>You have been automatically logged out.</h2>Please follow the link that was provided to you to continue your claim.';
			return;
		}
		if($hash === 'claim_not_found')
		{
			$this->errors[] = 'Claim not found.';
			return;
		}

		$this->data['claim'] = $this->db->where('hash',$hash)->get('claims')->row_array();
		if(empty($this->data['claim']))
		{
			$this->errors[] = 'Claim not found.';
			return;
		}

		$this->input->set_cookie('hash', $this->data['claim']['hash']);
		$this->db->where('hash', $this->data['claim']['hash'])
			->update('claims', array('user_agent' => substr($_SERVER['HTTP_USER_AGENT'],0,300))); 
		redirect($this->data['claim']['progress']);
	}

	public function page_1()
	{
		$this->db->where('id', $this->data['claim']['id'])->update('claims', array('status' => 'Processing'));
		$this->asides['topbar'] = 'insco';
		if(!empty($_POST))
			redirect('/report/2');
	}

	public function page_2()
	{
		if(!empty($_POST))
			redirect('/report/3');

		//if(!empty($this->data['claim']['vin']))
		//	redirect('/report/5');
	}

	public function page_3()
	{
		if(!empty($_POST))
			redirect('/report/4');
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
		// for resizing and preview
		$this->js[] = '/plugins/canvas-resize/binaryajax.js';
		$this->js[] = '/plugins/canvas-resize/exif.js';
		$this->js[] = '/plugins/canvas-resize/jquery.canvasResize.js';
		$this->js[] = '/plugins/canvas-resize/canvasResize.js';
		
		//data data
		$this->data['progress'] = 'take-photos';
		$this->data['photo_message'] = $this->_photo_message($page);
		$this->data['photo_page'] = $page;
		
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
			$this->_save_based_photo(
				$_POST['photo'],
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
			echo $e->getMessage();
			return;
		}

		// successful image, next page
		if($page == 6)
			redirect('/report/6');
		if($retake)
			redirect('/report/6/'.$page);

		redirect('/photo/' . ++$page);
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
			$report_url = $this->config->item('base_path') . '/v/' . $this->data['claim']['hash'];
			$reps = $this->db->where('insurer', $this->data['claim']['insurer'])->select('email')->get('user')->result_array();
			$emails = array();
			foreach($reps as $r)
				$emails[] = $r['email'];
			send_email(
				'Claim #'.$this->data['claim']['claim_number'].' - Damage Report Submitted',
				$this->data['claim']['name']." has completed their vehicle damage report. Follow the link below to view photos and vehicle information.<br/><br/><a href=\"$report_url\">$report_url</a>",
				$emails
			);
			redirect('/report/7');
		}
	}
	public function page_7()
	{
		$this->db->where('id', $this->data['claim']['id'])->update('claims', array('status' => 'Complete'));
		$this->data['progress'] = 'submit';
	}

	// photo page is the same, only the message changes
	public function _photo_message($page)
	{
		switch($page)
		{
			case 2: return 'Take a close-up shot of the vehicle that includes the damage.';
			case 3: return 'Take a close-up shot from the direction of the right side of the damage.';
			case 4: return 'Take a close-up shot from the direction of the left side of the damage.';
			case 5: return 'Take an additional photo of the damage.';
			case 6: return 'Take a wide shot of the vehicle that includes the damage. ';
			default: return 'Take a photo of your mileage. <div class="spacer10"></div><img class="mileage" src="/assets/img/mileage.png"/>';
		}
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
		$this->upload->img_to_png($path);
	}

	public function _make_thumb($path, $path_thumb)
	{
		$this->load->library('upload');
		copy($path, $path_thumb);
		$this->upload->img_to_png($path_thumb, 200);

	}
}