<?php

    class App_Form_validation extends CI_Form_validation
    {
        public function __construct($rules=array())
        {
            parent::__construct($rules);

            $this->set_message('valid_phone','%s must be a valid phone number.');
        }

        public function set_error($message,$field=FALSE)
        {
            if($field===FALSE)
                $this->_error_array[]=$message;
            else
            {
                $this->_error_array[$field]=$message;
                $this->_field_data[$field]['error']=$message;
            }
        }
        
        public function get_errors()
        {
            return $this->_error_array;
        }

        public function reset_values()
        {
            foreach($this->_field_data as $key=>$field)
            {
                $this->_field_data[$key]['postdata']=NULL;
            }
        }

        // public function old_valid_phone($str)
        // {
        //     $regexp='/\(?(\d{3})\)?\s?(\d{3})[-\s]?(\d{4})/';

        //     if(preg_match($regexp,$str,$matches) && count($matches)>=4)
        //     {
        //         return '('.$matches[1].') '.$matches[2].'-'.$matches[3];
        //     }
        //     else
        //         return FALSE;
        // }

        /**
         * Validates a U.S. phone number in almost any format, and returns a formatted phone number.
         *
         * @see http://stackoverflow.com/questions/1937376/codeigniter-form-validation-with-phone-numbers#1937961
         */
        public function valid_phone($value)
        {
            $value=trim($value);
            if($value=='')
            {
                return TRUE;
            }
            else
            {
                if(preg_match('/^\(?[0-9]{3}\)?[-. ]?[0-9]{3}[-. ]?[0-9]{4}$/',$value))
                {
                    return preg_replace('/^\(?([0-9]{3})\)?[-. ]?([0-9]{3})[-. ]?([0-9]{4})$/','($1) $2-$3',$value);
                }
                else
                {
                    return FALSE;
                }
            }
        }
    }