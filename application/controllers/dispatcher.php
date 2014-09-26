<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Dispatcher extends App_Controller
{
	protected $authenticate = array('dispatcher','administrator');

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
		redirect('/dispatcher/dashboard');
	}

	public function set_estimator($hash)
	{
		if(!$this->claims->can_dispatch($hash))
			$this->_json(['error' => 'Permission Denied']);

		if(!in_array($this->claims->status($hash), ['Pending Dispatch', 'New', 'Processing']))
			$this->_json(['error' => 'This report is not awaiting dispatch.']);

		$estimator = $this->input->post('estimator');
		try
		{
			/* get claim data */
			$this->data['claim'] = $this->db->select('claim_number,status')->where('hash',$hash)->get('claims')->row_array();
			
			/* Set Estimator */
			$this->db->where('hash',$hash)->update('claims', ['estimator' => $estimator]);
			
			/* Set Status (Pending Dispatch => Pending Estimate) */
			if($this->data['claim']['status'] === 'Pending Dispatch')
				$this->db->where('hash',$hash)->update('claims', ['status' => 'Pending Estimate']);

			/* Notify the Estimator */
			$report_url = $this->config->item('base_path') . '/e/' . $hash;
			if($this->data['claim']['status'] === 'Pending Dispatch')
				send_email(
					'Quote #'.$this->data['claim']['claim_number'].' Pending Estimation',
					"A homeowners insurance quote is waiting for a replacement cost estimate from you. Visit the link below to view photos and information provided by the homeowner and to provide a cost estimation.<br/><br/><a href=\"$report_url\">$report_url</a>",
					$this->user->get_emails($estimator)
				);
			else
				send_email(
					'Quote #'.$this->data['claim']['claim_number'].' Assigned to You',
					"A homeowners insurance quote has been assigned to you. Visit the link below to view photos and information provided by the homeowner and to provide a cost estimation once the report is complete.<br/><br/><a href=\"$report_url\">$report_url</a>",
					$this->user->get_emails($estimator)
				);

			// set status to Pending Estimate
			$this->_json(['success' => $estimator]);
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
			$this->db->where('claims.dispatcher', $this->user->data['id']);
		
		$this->db->where("claims.status in ('Pending Dispatch', 'Pending Estimate', 'Complete')");

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
		if(!$this->claims->can_dispatch($hash))
			redirect('/dispatcher/dashboard');

		$this->min_css[] = '/plugins/jquery-ui/css/trontastic/jquery-ui-1.10.3.custom.min.css';
		$this->min_js[] = '/plugins/jquery-ui/js/jquery-ui-1.10.3.custom.min.js';
		$this->min_js[] = '/plugins/slick/slick/slick.min.js';
		$this->min_css[] = '/plugins/slick/slick/slick.css';

		$this->data['estimators'] = $this->claims->get_estimators();

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

		$this->data['photo_coordinates'] = $this->claims->get_coordinates($this->data['photos']);

		// estimator
		if($this->data['claim']['estimator'])
			$this->data['estimator_data'] = $this->claims->get_estimator_data($this->data['claim']['estimator']);
	}
}