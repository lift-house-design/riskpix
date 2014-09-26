<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/* For Client Configuration */
class Pricing_model extends App_Model
{
  protected $_table='pricing';

  public function __construct()
  {
    parent::__construct();
  }

  public function get_pricing_options() {
    $pricing = array();
    $rows = $this->db->query('SELECT p_ID,p_volume,p_price,p_roll_over,p_roll_months FROM pricing WHERE p_expiration_date > NOW() ORDER BY p_volume,p_roll_over')->result_array();

    foreach ($rows as $p) {
      $k = $p['p_ID'];
      $v = $p['p_volume'] . ' @ $' . number_format($p['p_price'],2) . 'ea. / ($' . number_format($p['p_volume']*$p['p_price'],2) . ')';
      $pricing[$k] = $v;
    }

    return $pricing;
  }

}
