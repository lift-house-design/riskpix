<?php

function dms_to_dec($deg,$min,$sec,$ref)
{
	if($ref == 'N' || $ref == 'E')
		$ref = 1;
	else
		$ref = -1;

    return $ref * ($deg + ((($min * 60) + $sec) / 3600));
}

if(!function_exists('trace'))
{
	function trace()
	{
		$backtrace=debug_backtrace();

		echo '<table width="100%">';

		echo '<tr>';
		echo 	'<th>Line No.</th>';
		echo 	'<th>Filename</th>';
		echo 	'<th>Method/Function</th>';
		echo 	'<th>Args</th>';
		echo '</tr>';

		foreach($backtrace as $trace)
		{
			echo '<tr>';
			echo 	'<td>'.$trace['line'].'</td>';
			echo 	'<td>'.$trace['file'].'</td>';
			echo 	'<td>'.( isset($trace['class']) ? $trace['class'].'::'.$trace['function'] : $trace['function'] ).'</td>';
			echo 	'<td>'.count($trace['args']).'</td>';
			echo '</tr>';
		}

		echo '</table>';
	}
}

if(!function_exists('states_array'))
{
	function states_array($merge_with=array())
	{
		$states_array=array(
			'AL'=>'Alabama',
			'AK'=>'Alaska',
			'AZ'=>'Arizona',
			'AR'=>'Arkansas',
			'CA'=>'California',
			'CO'=>'Colorado',
			'CT'=>'Connecticut',
			'DE'=>'Delaware',
			'DC'=>'District Of Columbia',
			'FL'=>'Florida',
			'GA'=>'Georgia',
			'HI'=>'Hawaii',
			'ID'=>'Idaho',
			'IL'=>'Illinois',
			'IN'=>'Indiana',
			'IA'=>'Iowa',
			'KS'=>'Kansas',
			'KY'=>'Kentucky',
			'LA'=>'Louisiana',
			'ME'=>'Maine',
			'MD'=>'Maryland',
			'MA'=>'Massachusetts',
			'MI'=>'Michigan',
			'MN'=>'Minnesota',
			'MS'=>'Mississippi',
			'MO'=>'Missouri',
			'MT'=>'Montana',
			'NE'=>'Nebraska',
			'NV'=>'Nevada',
			'NH'=>'New Hampshire',
			'NJ'=>'New Jersey',
			'NM'=>'New Mexico',
			'NY'=>'New York',
			'NC'=>'North Carolina',
			'ND'=>'North Dakota',
			'OH'=>'Ohio',
			'OK'=>'Oklahoma',
			'OR'=>'Oregon',
			'PA'=>'Pennsylvania',
			'RI'=>'Rhode Island',
			'SC'=>'South Carolina',
			'SD'=>'South Dakota',
			'TN'=>'Tennessee',
			'TX'=>'Texas',
			'UT'=>'Utah',
			'VT'=>'Vermont',
			'VA'=>'Virginia',
			'WA'=>'Washington',
			'WV'=>'West Virginia',
			'WI'=>'Wisconsin',
			'WY'=>'Wyoming',
		);

		return array_merge($merge_with,$states_array);
	}
}

/**
 * Sends an e-mail
 *
 * @author Nick Niebaum <nick@nickniebaum.com>
 */
if(!function_exists('send_email'))
{
	function send_email($subject,$message,$to,$data=array())
	{
		static $email;

		$CI=get_instance();
		$config=$CI->config->item('email_notifications');

		if(isset($email))
		{
			$email->clear();
		}
		else
		{
			$CI->load->library('email');

			// Set a reference to the email library; this will also tell
			// this function to skip initialization on the next call
			$email=$CI->email;

			// $this->load->library('email',$config) vs. $this->email->initialize($config)
			// $this->email->initialize() will not authenticate you if your SMTP server requires it
			if(!empty($config['config']))
				$CI->load->library('email',$config['config']);
		}

		foreach($data as $k=>$v)
		{
			$subject=str_replace('{'.$k.'}',$v,$subject);
			$message=str_replace('{'.$k.'}',$v,$message);
		}

		$email->from($config['sender_email'],$config['sender_name']);
		$email->to($to);
		$email->bcc($CI->config->item('contact_email'));
		$email->subject($subject);
		$email->message($message);
		$email->set_mailtype("html");

		return $email->send();
	}
}

/**
 * Sends an SMS message using Twilio
 *
 * @author Nick Niebaum <nick@nickniebaum.com>
 */
if(!function_exists('send_sms'))
{
	function send_sms($message,$to,$data=array())
	{
		$CI=get_instance();
		$config=$CI->config->item('sms_notifications');
		$CI->load->library('twilio');

		foreach($data as $k=>$v)
		{
			$message=str_replace('{'.$k.'}',$v,$message);
		}
		$response = $CI->twilio->sms($config['config']['number'],$to,$message);

		return $response->IsError===FALSE;
	}
}

if(!function_exists('is_assoc_array'))
{
    function is_assoc_array($arr)
    {
        return is_array($arr) && count(array_filter(array_keys($arr),'is_string'));
    }
}

/*
| -------------------------------------------------------------------
|  Third-party Helpers & Polyfills
| -------------------------------------------------------------------
| Any useful functions and/or polyfills go below this line. Please
| remember to include credit where credit is due!
*/

/**
 * This file is part of the array_column library
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @copyright Copyright (c) 2013 Ben Ramsey <http://benramsey.com>
 * @license http://opensource.org/licenses/MIT MIT
 */

if (!function_exists('array_column')) {

    /**
     * Returns the values from a single column of the input array, identified by
     * the $columnKey.
     *
     * Optionally, you may provide an $indexKey to index the values in the returned
     * array by the values from the $indexKey column in the input array.
     *
     * @param array $input A multi-dimensional array (record set) from which to pull
     *                     a column of values.
     * @param mixed $columnKey The column of values to return. This value may be the
     *                         integer key of the column you wish to retrieve, or it
     *                         may be the string key name for an associative array.
     * @param mixed $indexKey (Optional.) The column to use as the index/keys for
     *                        the returned array. This value may be the integer key
     *                        of the column, or it may be the string key name.
     * @return array
     */
    function array_column($input = null, $columnKey = null, $indexKey = null)
    {
        // Using func_get_args() in order to check for proper number of
        // parameters and trigger errors exactly as the built-in array_column()
        // does in PHP 5.5.
        $argc = func_num_args();
        $params = func_get_args();

        if ($argc < 2) {
            trigger_error("array_column() expects at least 2 parameters, {$argc} given", E_USER_WARNING);
            return null;
        }

        if (!is_array($params[0])) {
            trigger_error('array_column() expects parameter 1 to be array, ' . gettype($params[0]) . ' given', E_USER_WARNING);
            return null;
        }

        if (!is_int($params[1])
            && !is_float($params[1])
            && !is_string($params[1])
            && $params[1] !== null
            && !(is_object($params[1]) && method_exists($params[1], '__toString'))
        ) {
            trigger_error('array_column(): The column key should be either a string or an integer', E_USER_WARNING);
            return false;
        }

        if (isset($params[2])
            && !is_int($params[2])
            && !is_float($params[2])
            && !is_string($params[2])
            && !(is_object($params[2]) && method_exists($params[2], '__toString'))
        ) {
            trigger_error('array_column(): The index key should be either a string or an integer', E_USER_WARNING);
            return false;
        }

        $paramsInput = $params[0];
        $paramsColumnKey = ($params[1] !== null) ? (string) $params[1] : null;

        $paramsIndexKey = null;
        if (isset($params[2])) {
            if (is_float($params[2]) || is_int($params[2])) {
                $paramsIndexKey = (int) $params[2];
            } else {
                $paramsIndexKey = (string) $params[2];
            }
        }

        $resultArray = array();

        foreach ($paramsInput as $row) {

            $key = $value = null;
            $keySet = $valueSet = false;

            if ($paramsIndexKey !== null && array_key_exists($paramsIndexKey, $row)) {
                $keySet = true;
                $key = (string) $row[$paramsIndexKey];
            }

            if ($paramsColumnKey === null) {
                $valueSet = true;
                $value = $row;
            } elseif (is_array($row) && array_key_exists($paramsColumnKey, $row)) {
                $valueSet = true;
                $value = $row[$paramsColumnKey];
            }

            if ($valueSet) {
                if ($keySet) {
                    $resultArray[$key] = $value;
                } else {
                    $resultArray[] = $value;
                }
            }

        }

        return $resultArray;
    }

}

/* End of file project_helper.php */
/* Location: ./application/helpers/project_helper.php */
