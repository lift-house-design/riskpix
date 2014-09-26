<?

/* For data/form validation */
class Valid
{
	public function phone(&$phone)
    {
    	$phone = preg_replace('/(^1|[^\d]+)/','',$phone);

    	if(strlen($phone) != 10)
    		return " should be a valid 10-digit phone number";
		$phone = '('.substr($phone,0,3).') '.substr($phone,3,3).'-'.substr($phone,6,4);
    }

	public function fullname(&$name)
    {
    	$name = trim($name);
    	if(stripos($name,' ') === false)
    		return ' should contain first AND last name';
    	if(preg_match('/[^a-zA-Z.\-\' ]/', $name, $match))
    		return " should not contain '{$match[0]}'";
    }

    public function email(&$email)
    {
    	$email = trim($email);
    	if(!preg_match('/[a-zA-Z0-9.!#$%&\'*+\-\/=?\^_`{|}~]+\@[a-zA-Z0-9\.\-]{3,}/', $email))
    		return ' should be a valid email address';
    }

    public function vin(&$vin)
    {
    	$vin = strtoupper(trim($vin));
    	if(preg_match('/[^A-Z0-9]/', $vin))
    		return ' should only contain letters and numbers';
    	/* Steve says they are variable length...
        if(strlen($vin) != 17)
    		return ' should be 17 characters long';
        */
    }

    public function required(&$val)
    {
        $val = trim($val);
        if(!strlen($val))
            return ' is required';
    }

    public function int(&$val)
    {
        if((string)intval($val) !== (string)$val)
            return ' should be an integer';
    }

    public function pos(&$val)
    {
        if($val < 0)
           return ' should be positive';
    }

    /* 
    	$data is usually the $_POST array
	*/
    public function validate_lazy(&$data, $rules)
    {
        foreach($rules as $i => $rule)
        {
            // optional and empty, skip it
            if(isset($rule[3]) && $rule[3] && empty($data[$i]))
                continue;

            // this is a child question. only require if the parent has a certain value
            if(!empty($rule[4]))
            {
                $parent = $rule[4][0];
                if(!isset($rules[$parent]))
                    throw new Exception("Rule for $parent not found.");
                if(!isset($data[$parent]))
                    throw new Exception("Data for $parent not found.");

                $values = $rule[4][1];
                if(!in_array($data[$parent], $values))
                {
                    $data[$i] = ''; // clear it out
                    continue;
                }
            }

            if(empty($rule[2]))
                $rule[2] = '';

            // select type inputs are simple to validate
            if(is_array($rule[2]))
                if(!isset($data[$i]) || !(in_array($data[$i], $rule[2]) || in_array($data[$i], array_keys($rule[2]))))
                    return $rule[0]." is required";
                else
                    continue;

            // here we handle custom validation functions. seems solid, can't seem to hack/break it...
            $functions = preg_split('/\|/', $rule[2], 0, PREG_SPLIT_NO_EMPTY);
            $functions[] = 'required';
            foreach($functions as $function)
            {
                eval('$err = $this->'.$function.'($data[$i]);');
                if($err)
                    return $rule[0]." $err";
            }
        }
    }

    public function validate(&$data, $rules)
    {
        foreach($rules as $rule)
        {
            if(isset($rule[2]) && $rule[2] && empty($data[$rule[0]]))
                continue;
            if(empty($data[$rule[0]]))
                return $this->label($rule[0])." is required";
            $functions = preg_split('/\|/', $rule[1], 0, PREG_SPLIT_NO_EMPTY);
            foreach($functions as $function)
            {
                eval('$err = $this->'.$function.'($data[$rule[0]]);');
                if($err)
                    return $this->label($rule[0])." $err";
            }
        }
    }

    public function label($index)
    {
    	if($index == 'vin')
    		return 'VIN';
    	return ucwords(str_replace('_',' ',$index));
    }

    public function fill_empty(&$data, $rules)
    {
        foreach($rules as $rule)
            if(empty($data[$rule[0]]))
                $data[$rule[0]] = '';
    }

    public function make_empty(&$data, $rules)
    {
        foreach($rules as $rule)
            $data[$rule[0]] = '';
    }
}
?>
