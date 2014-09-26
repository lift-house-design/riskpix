<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
	
/* For Client Configuration */
class Claims_model extends App_Model
{
	public $new_claim_fields = [
		'street_address' => ['Street Address', 'text', '', true],
		'zip' => ['Zip Code', 'text', 'int|pos'],
		'name' => ['Name (first and last)', 'text', 'fullname'],
		'email' => ['Email Address', 'text', 'email'],
		'phone' => ['Mobile Number', 'text', 'phone'],
		'claim_number' => ['Policy Quote Number', 'text'],
		'type' => ['Report Type', 'select', ['home' => 'Full Interior/Exterior', 'home_photo_only' => 'Photos Only - Interior/Exterior', 'home_photo_front_and_rear' => 'Front and Rear Photo Only']],
		'dispatcher' => ['Add Replacement Cost Estimation', 'checkbox', ['Yes'], true, ['type', ['home']]]
	];

	public $page2_fields = array(
		'year' => array('Year Built', 'text', 'int|pos', false),
		'sqft' => array('Square Footage', 'text', 'int|pos', false),
		'foundation' => array('Foundation', 'select', ['Slab', 'Basement', 'Other']),
		'stories' => array('Number of Stories', 'select', array(1, 1.5, 1.75, 2, 2.5, 2.75, 3, '>3'), false),
		'style' => array('Home Style', 'select', array('Bi-Level', 'Cape Cod', 'Colonial', 'Contemporary', 'Ranch', 'Split Level', 'Townhouse', 'Tudor', 'Victorian', 'Other'), false),
		'beds' => array('Number of Bedrooms', 'select', array('Studio', 1, 2, 3, 4, 5, 6, 7, 8, 9, 10), false),
		'baths' => array('Number of Bathrooms', 'select', array(0.5, 0.75, 1, 1.5, 1.75, 2, 2.5, 2.75, 3, '>3'), false),
		'bath_grade' => array('Bathroom Grade', 'select', ['Custom', 'Builders Grade', 'Basic', 'Designer', 'Semi-Custom'], false),
		'garage' => array('Garage Type', 'select', array('Attached','Detached','None'), false),
		'garage_size' => array('Garage Size', 'select', array('1 Car', '1.5 Car', '2 Car', '2.5 Car', '3 Car', '>3 Car'), false, array('garage', array('Attached','Detached'))),
		'kitchens' => array('Number of Kitchens', 'select', [1, 2, 3, '>3']),
		'kitchen_grade' => array('Kitchen Grade', 'select', ['Custom', 'Builders Grade', 'Basic', 'Designer', 'Semi-Custom'], false)
	);

	public $page3_fields = array(
		'roof_age' => array('Roof Age', 'select', array('1-5 Years','6-10 Years','11-15 Years','16-20 Years','21-25 Years','26+ Years'), false),
		'chimneys' => array('Chimneys', 'select', array('No Chimneys','1','2','3','4','5','>5'), false),
		'central_heating_age' => array('Central Heating Age', 'select', array('No Central Heating','1-5 Years','6-10 Years','11-15 Years','16-20 Years','21-25 Years','26+ Years'), false),
		'electrical_service' => array('Electrical Service', 'select', array('Circuit Breakers','Fuses','No Electrical Service'), false),
		'electrical_age' => array('Electrical Update/Inspection', 'select', array('Electrical Never Inspected','<1 year ago','1 year ago','2 years ago','3 years ago','4 years ago','5 years ago','>5 years ago'), false, array('electrical_service', array('Circuit Breakers','Fuses'))),
		'pool' => array('Pool', 'select', array('No Pool','Above Ground','Below Ground'), false),
		'pool_fence' => array('Pool Fence', 'select', array('No Pool Fence','<3 Feet Tall','3+ Feet Tall'), false, array('pool', array('Below Ground'))),
		'pool_gate' => array('Pool Gate', 'select', array('No Pool Gate','Unlocked Gate','Locked Gate'), false, array('pool_fence', array('3+ Feet Tall'))),
		'pool_slide' => array('Pool Slide', 'select', array('No Pool Slide','<5 Feet Tall','5+ Feet Tall'), false,  array('pool', array('Above Ground','Below Ground'))),
		'diving_board' => array('Diving Board', 'select', array('No Diving Board','Into <6 Feet of Water','Into 6+ Feet of Water'), false,  array('pool', array('Above Ground','Below Ground'))),
		'hot_tub' => array('Hot Tub', 'select', array('No Hot Tub','Has Hot Tub')),
		'trampoline' => array('Trampoline', 'select', array('No Trampoline','Has Trampoline')),
		'exterior_stairs' => array('Exterior Stairs', 'select', array('No Exterior Stairs','No Railing','All Have Railing')),
		'deck' => array('Deck', 'select', array('No Deck','No Railing','With Railing')),
		'furnaces' => array('List all Wood Burning Stoves, Fireplaces, Wood Burning Furnaces, Space Heaters, and Kerosene Heaters on the premises. Include types, models, UL approval, and how often they are cleaned.', 'textarea', '', true),
		'dogs' => array('List all of your dogs. Include weight and breed.', 'textarea', '', true),
		'work' => array('Is there any business, farming, or occupational pursuits done on the premises?', 'textarea', '', true),
		'losses' => array('Describe any property loss in the past 5 years (fire, theft, liability, etc.)', 'textarea', '', true)
	);

	public function __construct()
	{
		parent::__construct();
	}

	public function get_by_hash($hash)
	{
		$claim = $this->db->where('hash',$hash)->get('claims')->row_array();
		if($claim['replacement_cost'] > 0)
			$claim['replacement_cost'] = number_format($claim['replacement_cost'], 2);
		return $claim;
	}

	public function status($hash)
	{
		$res = $this->db->select('status')->where('hash',$hash)->get('claims')->row_array();
		if(!empty($res['status']))
			return $res['status'];
		else
			return 'Not Found';
	}

	public function delete($id)
	{
		$this->delete_photos($id);
		$this->db->where('id',$id)->delete('claims');
	}

	public function delete_all($user)
	{
		$claims = $this->db->select('id')->where('user',$user)->get('claims')->result_array();
		$this->compress($claims, 'id');
		foreach($claims as $id)
			$this->delete($id);
	}

	public function delete_photos($id)
	{
		$photos = $this->db->select('path,path_thumb')->where('claim_id',$id)->get('photos');
		foreach($photos as $p)
		{
			if(file_exists($p['thumb']))
				unlink($p['thumb']);
			if(file_exists($p['thumb_path']))
				unlink($p['thumb_path']);
		}
	}

	/* we dont even use this tho... */
	public function get_agent($hash)
	{
		$res = $this->db->select('user')->where('hash',$hash)->get('claims')->row_array();
		if(empty($res['user']))
			return 0;
		return $res['user'];
	}

	public function get_dispatchers()
	{
		$res = $this->db->select('user.*')->where('role','dispatcher')->join('user','user.id=role.user_id')->get('role')->result_array();
		return $res;
	}

	public function get_estimators($daddy = 0)
	{
		if($daddy)
			$this->db->where('daddy',$daddy);
		$res = $this->db->select('id,estimator_data')->where('role','estimator')->join('role', 'id=user_id')->get('user')->result_array();
		foreach($res as $i => $row)
			$res[$i]['estimator_data'] = json_decode($row['estimator_data'], true);
		return $res;
	}

	public function get_estimator_data($id)
	{
		$res = $this->db->select('estimator_data')->where('id',$id)->get('user')->row_array();
		if(empty($res['estimator_data']))
			return [];
		return json_decode($res['estimator_data'], true);
	}

	public function can_edit($hash)
	{
		if(in_array('administrator', $this->user->data['roles']))
			return true;

		if(in_array('agent', $this->user->data['roles']))
		{
			$res = $this->db->select('user')->where('hash', $hash)->get('claims')->row_array();
			if($res['user'] == $this->user->data['id'])
				return true;
		}
		return false;
	}

	public function can_estimate($hash)
	{
		if(in_array('administrator', $this->user->data['roles']))
			return true;

		if(in_array('estimator', $this->user->data['roles']))
		{
			$res = $this->db->select('estimator')->where('hash', $hash)->get('claims')->row_array();
			if($res['estimator'] == $this->user->data['id'])
				return true;
		}
		return false;
	}

	public function can_dispatch($hash)
	{
		if(in_array('administrator', $this->user->data['roles']))
			return true;

		if(in_array('dispatcher', $this->user->data['roles']))
		{
			$res = $this->db->select('dispatcher')->where('hash', $hash)->get('claims')->row_array();
			if($res['dispatcher'] == $this->user->data['id'])
				return true;
		}
		return false;
	}

	public function get_coordinates(&$photos)
	{
		// parse exif data
		$coordinates = array();
		$time = array(); // why are we not parsing time? lazy bastard. exif=UTC, html5=unix
		foreach($photos as $i => $p)
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
			return $coordinates;
		}
		return null;
	}
}