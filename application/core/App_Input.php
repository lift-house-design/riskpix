<?php if  ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Custom input class.
 *
 * @author Nick Niebaum <nick@nickniebaum.com>
 */
class App_Input extends CI_Input
{
    /**
     * Simple modification of post() that accepts an array of keys and returns the data associated with those keys.
     *
     * @author Nick Niebaum <nick@nickniebaum.com>
     */
    public function post($index=NULL,$xss_clean=FALSE)
    {
        if(is_array($index))
        {
            if(empty($index))
            {
                return array();
            }
            else
            {
                $values=array();

                foreach($index as $key)
                {
                    $values[$key]=parent::post($key,$xss_clean);
                }

                return $values;
            }
        }
        else
        {
            return parent::post($index,$xss_clean);
        }
    }
}

/* End of file App_Input.php */
/* Location: ./application/core/App_Input.php */