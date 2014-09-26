<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/* Look up vehicle data with SOAP requests */

class Vehicle
{
	function get_vin_data($vin)
	{
		if(strlen($vin) < 10)
			return array(
				'status' => 'error',
				'error' => 'VIN should be at least 10 characters.',
			);

		$prefix = substr($vin, 0, 8) . '*' . $vin[9];

		$ci = get_instance();
		$res = $ci->db->where('VinPrefix.VinPrefix.VinPrefix', $prefix)
			->select('model,make,year,body')
			->distinct()
			->join('VicDescriptions.VicDescriptions','VicDescriptions.VicDescriptions.vic = VinPrefix.VinPrefix.VIC')
			->get('VinPrefix.VinPrefix')->result_array();

		// crap load of models, throw out the body
		if(count($res) > 2)
		{
			$res = $ci->db->where('VinPrefix.VinPrefix.VinPrefix', $prefix)
				->select('model,make,year')
				->distinct()
				->join('VicDescriptions.VicDescriptions','VicDescriptions.VicDescriptions.vic = VinPrefix.VinPrefix.VIC')
				->get('VinPrefix.VinPrefix')->result_array();
			foreach($res as $i => $row)
				$res[$i]['body'] = '';
		}

		if(empty($res))
			return array(
				'status' => 'error',
				'error' => 'VIN data not found.',
			);

		if(count($res) > 2)
			return array(
				'status' => 'error',
				'error' => 'Too many models.',
			);
		
		if(count($res) == 2)
			foreach($res[0] as $i => $v)
				if($res[1][$i] !== $v)
					$res[0][$i] .= " / ".$res[1][$i];

		$response['status'] = 'success';
		$response['data'] = $res[0];
		$response['data']['style'] = '';
		
		return $response;
	}

	/* The only important function */
	function get_vin_data_chromedata($vin)
	{
		$response=array(
			'status'=>'error',
			'error'=>'',
		);
		/* Steve says they are variable length... 
		if(strlen($vin)==17)
		{
		*/
			$vin_object = $this->requestVinInfo($vin,false);
			$vin_data=array();
			$this->convertXmlObjToArr($vin_object,$vin_data);
			
			if($vin_data[0]['@attributes']['responsecode']!='Successful')
			{
				if(!empty($vehicle_info[0]['@attributes']['Description']))
					$response['error'] = trim($vehicle_info[0]['@attributes']['Description']);

				if(empty($response['error']))
					$response['error']='VIN number not found. Please try again.';
			}
			else
			{
				$response['status']='success';
				$response['data']=array(
					'year'=>$vin_data[1]['@attributes']['modelyear'],
					'make'=>$vin_data[1]['@attributes']['division'],
					'model'=>$vin_data[1]['@attributes']['modelname'],
					'style'=>$vin_data[1]['@attributes']['stylename'],
					'body'=>$vin_data[1]['@attributes']['bodytype'],
				);
			}
		/*
		}
		else
		{
			$response['error']='VIN must be 17 characters.';
		}
		*/

		return $response;
	}

	function convertXmlObjToArr($obj, &$arr) 
	{ 
	    $children = $obj->children(); 
	    foreach ($children as $elementName => $node) 
	    { 
	        $nextIdx = count($arr); 
	        $arr[$nextIdx] = array(); 
	        $arr[$nextIdx]['@name'] = strtolower((string)$elementName); 
	        $arr[$nextIdx]['@attributes'] = array(); 
	        $attributes = $node->attributes(); 
	        foreach ($attributes as $attributeName => $attributeValue) 
	        { 
	            $attribName = strtolower(trim((string)$attributeName)); 
	            $attribVal = trim((string)$attributeValue); 
	            $arr[$nextIdx]['@attributes'][$attribName] = $attribVal; 
	        } 
	        $text = (string)$node; 
	        $text = trim($text); 
	        if (strlen($text) > 0) 
	        { 
	            $arr[$nextIdx]['@text'] = $text; 
	        } 
	        $arr[$nextIdx]['@children'] = array(); 
	        $this->convertXmlObjToArr($node, $arr[$nextIdx]['@children']); 
	    } 
	    return; 
	}

	function requestParam($name) {
        if (array_key_exists($name, $_REQUEST))
            return $_REQUEST[$name];
        else
            return "";
    }

    function call_soap($xml) {
        $soapURL ="http://services.chromedata.com/Description/7a";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $soapURL);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);
        $header[] = "SOAPAction: ". "";
        $header[] = "MIME-Version: 1.0";
        $header[] = "Content-type: text/xml; charset=utf-8";
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        $result = curl_exec($ch);
        $start = strpos($result, "<S:Body>") + 8;
        $end = strrpos($result, "</S:Body>");
        if (($start <= 0) || ($end <= 0)) {
            echo("<!--\n\n" . $result . "\n\n-->\n");
            die("Response returned from '$soapURL' doesn't appear to be a SOAP document.");
        }
        $result = substr($result, $start, $end - $start);
        $doc = simplexml_load_string($result);
        return $doc;
    }

    function moneyFormat($dollarValue) {
        return "$$$" . $dollarValue;
    }

    function requestVinInfo($vin, $basic=true) 
    {
    	if($basic){
			$accountNumber = '285164';
			$accountSecret = '360ac7186ca44923';
	    } else {
			$accountNumber = '284618';
			$accountSecret = '5b8a96fe1fb54775';
	    }

	    $request = '
			<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:urn="urn:description7a.services.chrome.com">
			   <soapenv:Header/>
			   <soapenv:Body>
			      <urn:VersionInfoRequest>
			         <urn:accountInfo number="'.$accountNumber.'" secret="'.$accountSecret.'" country="US" language="en" behalfOf="?"/>
			      </urn:VersionInfoRequest>
			   </soapenv:Body>
			</soapenv:Envelope>
	    ';
	    $dataVersions = $this->call_soap($request);
	    $version = "";
	    foreach ($dataVersions as $data) {
	        if ($data->country == "US")
	            $version = $data->country . " " . $data->build . " (" . $data->date . ")";
	    }

	    $vehicleInfo = "";
	    // if (array_key_exists("vin", $_REQUEST)) {
	    if ($vin && $vin != '') {
	        $request = '
				<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:urn="urn:description7a.services.chrome.com">
				   <soapenv:Header/>
				   <soapenv:Body>
				      <urn:VehicleDescriptionRequest>
				         <urn:accountInfo number="'.$accountNumber.'" secret="'.$accountSecret.'" country="US" language="en" behalfOf="?"/>
				         <urn:vin>' . $vin . '</urn:vin>';
				        /*if (!empty($_REQUEST["limitingStyleID"]))
				            $request .= '
				         <urn:reducingStyleId>' . $_REQUEST["limitingStyleID"] . '</urn:reducingStyleId>';
				        if (!empty($_REQUEST["trimName"]))
				            $request .= '
				         <urn:trimName>' . $_REQUEST["trimName"] . '</urn:trimName>';
				        if (!empty($_REQUEST["manufacturerModelCode"]))
				            $request .= '
				         <urn:manufacturerModelCode>' . $_REQUEST["manufacturerModelCode"] . '</urn:manufacturerModelCode>';
				        if (!empty($_REQUEST["wheelBase"]))
				            $request .= '
				         <urn:wheelBase>' . $_REQUEST["wheelBase"] . '</urn:wheelBase>';
				        foreach (explode(",", $_REQUEST["oemOptionCodes"]) as $code)
				           if (!empty($code))
				                $request .= '
				         <urn:OEMOptionCode>' . $code . '</urn:OEMOptionCode>';
				        foreach (explode(",", $_REQUEST["equipmentDescriptions"]) as $equip)
				            if (!empty($equip))
				                $request .= '
				         <urn:equipmentDescription>' . $equip . '</urn:equipmentDescription>';
				        if (!empty($_REQUEST["exteriorColorName"]))
				            $request .= '
				         <urn:exteriorColorName>' . $_REQUEST["exteriorColorName"] . '</urn:exteriorColorName>';
				        if (!empty($_REQUEST["interiorColorName"]))
				            $request .= '
				         <urn:interiorColorName>' . $_REQUEST["interiorColorName"] . '</urn:interiorColorName>';
				        */
				         $request .= '
				      </urn:VehicleDescriptionRequest>
				   </soapenv:Body>
				</soapenv:Envelope>
			';
	        //$reducingStyleId = $_REQUEST["limitingStyleID"];
	        $vehicleInfo = $this->call_soap($request);
	    }
	    return $vehicleInfo;
	}

    //end new stuff

	// NuSoap interprets a single element in an array as not being an array
	// This function makes it consistent so that the iterators below work.
	function fixArray($possibleArray) {
		if( !is_array($possibleArray[0]) ){
			// make single element array
			$possibleArray = array($possibleArray);
		}
		return $possibleArray;
	}

	function request_decoded_vin($vin, $basic=true,$trim_name="", $model_code="", $wheel_base = 0, $option_codes = "", $equipment_codes = "", $color_name=""){

		$nusoap_include = __DIR__.'/../../assets/vin/nusoap.php';
		$wsdlcache_include = __DIR__.'/../../assets/vin/class.wsdlcache.php';

		//echo '  NUSOAP ('.$nusoap_include.'): '.is_file($nusoap_include);
		//echo '  WSDL ('.$wsdlcache_include.'): '.is_file($wsdlcache_include);
		
		include($wsdlcache_include);
		include($nusoap_include);

	    // Begin code for ADS 6 request using nusoap
		$wsdlURL = "http://services.chromedata.com/Description/7a?wsdl";
		$namespace="urn:description7a.services.chrome.com";
		// $wsdlURL ="http://platform.chrome.com/AutomotiveDescriptionService/AutomotiveDescriptionService6?WSDL";
		// $namespace="urn:description6.kp.chrome.com";



		$cache = new wsdlcache();
		$wsdl = $cache->get($wsdlURL);
		if ($wsdl == null) {
			$wsdl = new wsdl($wsdlURL);
			$cache->put($wsdl);
		}



		$client = new soapclient2($wsdl, true);
	    

	    $locale = array(
			"country" => "US",
			"language" => "en"
		);
	    if($basic){
			$accountNumber = '285164';
			$accountSecret = '360ac7186ca44923';
	    } else {
			$accountNumber = '284618';
			$accountSecret = '5b8a96fe1fb54775';
	    }

		$accountInfo = array(
		    "accountNumber" => $accountNumber,
		    "accountSecret" => $accountSecret,
		    //"accountNumber" => "284618",
		    //"accountSecret" => "5b8a96fe1fb54775",
			"locale" => $locale
		);


	    
	    // Get data version --displayed in html title
		$version = "";
		$dataVersionsRequest = array(
			"accountInfo" => $accountInfo
		);
		$dataVersions = $client->call("getDataVersions", array("request" => $dataVersionsRequest), $namespace, "", false, null, "document", "literal");
		$dataVersions = fixArray($dataVersions["dataVersion"]);
		for ($i = 0; $i < count($dataVersions); $i++ ) {
			$dataVersion = $dataVersions[$i];
			if ($dataVersion["country"] == "US") {
				$version = $dataVersion["country"] . " " . $dataVersion["build"] . " (" . $dataVersion["date"] . ")";
			}
		}
	    
	    $returnParameters = array(
			"useSafeStandards" => true,
			"excludeFleetOnlyStyles" => false,
			"includeAvailableEquipment" => true,
			"includeExtendedDescriptions" => true,
			"includeExtendedTechnicalSpecifications" => true,
			"includeRegionSpecificStyles" => true,
			"includeConsumerInformation" => true,
			"enableEnrichedVehicleEquipment" => false
		);
		$vinRequest = array(
			"accountInfo" => $accountInfo,
			"vin" => $vin,
			"manufacturerModelCode" => $model_code,
			"trimName" => $trim_name,
			"wheelBase" => floatval($wheel_base),
			"manufacturerOptionCodes" => explode(",", $option_codes),
			"equipmentDescriptions" => explode(",", $equipment_codes),
			"exteriorColorName" => $color_name,
			"returnParameters" => $returnParameters
		);
		
		$vehicleInfo = $client->call("getVehicleInformationFromVin", array("request" => $vinRequest), $namespace, "", false, null, "document", "literal");
	    return $vehicleInfo;
	    
	}

	function requestModelYears()
	{
		$output = array('success' => false, 'result' => array());
		
		//full account
		$accountNumber = '284618';
		$accountSecret = '5b8a96fe1fb54775';
		 
		$request = '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:urn="urn:description7a.services.chrome.com">
			   				<soapenv:Header/>
							<soapenv:Body>
								<urn:ModelYearsRequest>
							    	<urn:accountInfo number="'.$accountNumber.'" secret="'.$accountSecret.'" country="US" language="en" behalfOf="?"/>
								</urn:ModelYearsRequest>
							</soapenv:Body>
						</soapenv:Envelope>';

		$response = $this->call_soap($request);
		$response_status = $response->responseStatus->attributes();
		
		if($response_status['responseCode'] != 'Successful'){
			$output['success'] = false;
			$output['error_message'] = 'here is the problem:'.$response_status['Description'];
		} else {
			$output['success'] = true;
			
			$output['result'][''] = '';
			foreach($response->children()->modelYear as $element)
			{
				$modelYear = (string)$element;
				$output['result'][$modelYear] = $modelYear;
			}
		}
		
		return $output;
	}
	function requestDivisions($year)
	{
		if ($year > 0)
		{
			$output = array('success' => false, 'result' => array());
			$accountNumber = '284618';
			$accountSecret = '5b8a96fe1fb54775';

			$request = '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:urn="urn:description7a.services.chrome.com">
				   				<soapenv:Header/>
								<soapenv:Body>
									<urn:DivisionsRequest modelYear="'.intval($year).'">
								    	<urn:accountInfo number="'.$accountNumber.'" secret="'.$accountSecret.'" country="US" language="en" behalfOf="?"/>
									</urn:DivisionsRequest>
								</soapenv:Body>
							</soapenv:Envelope>';
			 
			$response = call_soap($request);
			$response_status = $response->responseStatus->attributes();
			
			if($response_status['responseCode'] != 'Successful')
			{
				$output['success'] = false;
				$output['error_message'] = 'here is the problem:'.$response_status['Description'];
			} 
			else 
			{
				$output['success'] = true;		
				
				foreach($response->division as $division) 
				{
					$id = (int)$division->attributes()->id;
					$name = (string)$division;
					$output['result'][$id] = $name;
				} 
			}
		}
		else
		{
			$output['success'] = true;
			$output['result'][''] = '';
		}
		
		return $output;
	}
	function requestModels($year, $division)
	{
		if ($year > 0 && $division > 0)
		{
		$output = array('success' => false, 'result' => array());
		
		$accountNumber = '284618';
		$accountSecret = '5b8a96fe1fb54775';
		 
		$request = '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:urn="urn:description7a.services.chrome.com">
			   				<soapenv:Header/>
							<soapenv:Body>
								<urn:ModelsRequest>
							    	<urn:accountInfo number="'.$accountNumber.'" secret="'.$accountSecret.'" country="US" language="en" behalfOf="?"/>
							    	<urn:modelYear>'.intval($year).'</urn:modelYear>
							    	<urn:divisionId>'.intval($division).'</urn:divisionId>
								</urn:ModelsRequest>
							</soapenv:Body>
						</soapenv:Envelope>';

		$response = $this->call_soap($request);
		$response_status = $response->responseStatus->attributes();
		
		if($response_status['responseCode'] != 'Successful'){
			$output['success'] = false;
			$output['error_message'] = 'here is the problem:'.$response_status['Description'];
		} else {
			$output['success'] = true;
			
			foreach($response->model as $element) 
			{
				$model = (string)$element;
				$output['result'][$model] = $model;
			} 
		}
		}
		else
		{
			$output['success'] = true;
			$output['result'][''] = '';
		}
		
		return $output;
	}
}

/* Author: Bain Mullins - bainmullins@gmail.com */
?>