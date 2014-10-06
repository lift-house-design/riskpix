<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Discount_model extends App_Model
{
    protected $_table='discount_codes';
    protected $primary_key='dc_ID';
    protected $protected_attributes=array('dc_ID');

    public function get_by_code($code,$only_active=TRUE)
    {
        $where=array('dc_code'=>$code);

        if($only_active)
        {
            $where['dc_expiration_date >=']=date('Y-m-d H:i:s');
        }

        return $this->get_by($where);
    }
}
    
/* End of file discount_model.php */
/* Location: ./application/models/discount_model.php */