<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/* For Client Configuration */
class Company_model extends App_Model
{
  protected $_table='company';
  protected $primary_key = 'c_ID';

  public function __construct()
  {
    parent::__construct();
  }

  public function get_company() {

    //$pricing = array();
    //$rows = $this->db->query('SELECT p_ID,p_volume,p_price,p_roll_over,p_roll_months FROM pricing WHERE p_expiration_date > NOW() ORDER BY p_volume,p_roll_over')->result_array();


    //return $pricing;
  }

}
