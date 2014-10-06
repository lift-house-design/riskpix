<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/* For Client Configuration */
class Pricing_model extends App_Model
{
    protected $_table='pricing';
    protected $primary_key='p_ID';
    protected $protected_attributes=array('p_ID');

    public function get_dropdown()
    {
        $dropdown=array();
        $rows=$this
            ->order_by('p_volume, p_roll_over')
            ->get_many_by('p_expiration_date >= NOW()');

        foreach($rows as $row)
        {
            $dropdown[ $row['p_ID'] ]=$row['p_volume'] . ' @ $' . number_format($row['p_price'],2) . 'ea. / ($' . number_format($row['p_volume']*$row['p_price'],2) . ')';
        }

        return $dropdown;
    }

    public function get_rollover_expirations($date_format='m/d/Y')
    {
        $expirations=array();
        $rows=$this->get_many_by('p_expiration_date >= NOW()');

        foreach($rows as $row)
        {
            $expirations[ $row['p_ID'] ]=date($date_format,strtotime('+'.($row['p_roll_months']+1).' months'));
        }

        return $expirations;
    }

}
