<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Estimator extends App_Controller
{
	protected $authenticate = array('estimator','administrator');

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
		redirect('/estimator/dashboard');
	}

	public function set_estimate($hash)
	{
		if(!$this->claims->can_estimate($hash))
			$this->_json(['error' => 'Permission Denied']);

		if($this->claims->status($hash) !== 'Pending Estimate')
			$this->_json(['error' => 'This report is not awaiting an estimate.']);

		$cost = $this->input->post('cost');
		try
		{
			$this->db->where('hash',$hash)->update('claims', ['replacement_cost' => $cost, 'status' => 'Complete']);

			// notify agent
			$this->data['claim'] = $this->db->where('hash',$hash)->get('claims')->row_array();
			$report_url = $this->config->item('base_path') . '/v/' . $hash;
			$estimator_data = $this->claims->get_estimator_data($this->user->data['id']);
			$estimator_name = empty($estimator_data['name']) ? $this->user->data['email'] : $estimator_data['name'];
			send_email(
				'Policy Quote #'.$this->data['claim']['claim_number'].' Replace Cost Estimate Complete',
				$this->data['claim']['name']." has completed their homeowners insurance quote, and a replacement cost estimate of <b>\$$cost</b> has been recommended by $estimator_name. Follow the link below to view the completed report.<br/><br/><a href=\"$report_url\">$report_url</a>",
				$this->user->get_emails($this->data['claim']['user'])
			);

			$this->_json(['success' => $cost]);
		}
		catch(Exception $e)
		{
			$this->_json(['error' => $e->getMessage()]);
		}
	}

	public function dashboard()
	{
		$this->min_css[] = '/plugins/jquery-ui/css/trontastic/jquery-ui-1.10.3.custom.min.css';
		$this->min_js[] = '/plugins/jquery-ui/js/jquery-ui-1.10.3.custom.min.js';
		$this->min_css[] = '/plugins/datatables/css/jquery.dataTables.css';
		$this->min_js[] = '/plugins/datatables/js/jquery.dataTables.min.js';

		if(!in_array('administrator', $this->user->data['roles']))
			$this->db->where('claims.estimator', $this->user->data['id']);
		
		$this->db->where("claims.status in ('Pending Estimate', 'Complete')");

		$this->data['claims'] = $this->db
			->order_by('claims.id','desc')
			->get('claims')->result_array();

		$this->data['claims_table'] = array();
		foreach($this->data['claims'] as $i => $c)
		{
			$this->data['claims_table'][] = array(
				$c['date'],
				date('n/j/y g:ia', strtotime($c['date'])),
				$c['claim_number'],
				$c['status'],
				'<input type="button" onclick="claim_view(\''.$c['hash'].'\')" value="View"/><br/>'
			);
		}
	}

	public function view($hash)
	{
		if(!$this->claims->can_estimate($hash))
			redirect('/estimator/dashboard');

		$this->min_css[] = '/plugins/jquery-ui/css/trontastic/jquery-ui-1.10.3.custom.min.css';
		$this->min_js[] = '/plugins/jquery-ui/js/jquery-ui-1.10.3.custom.min.js';
		$this->min_js[] = '/plugins/slick/slick/slick.min.js';
		$this->min_css[] = '/plugins/slick/slick/slick.css';
		$this->min_js[] = 'autoNumeric.js';

		/* claim data */
		$this->data['claim'] = $this->claims->get_by_hash($hash);
		$this->data['claim']['url'] = $this->config->item('base_url').'r/'.$this->data['claim']['hash'];
		$this->data['fields_we_care_about'] = array(
			'claim_number' => 'Policy Quote Number',
			'zip' => 'Zip Code',
			'name' => 'Owner Name',
			'email' => 'Email Address',
			'phone' => 'Mobile Number',
			'status' => 'Quote Status',
			'url' => 'Report Link',
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
}