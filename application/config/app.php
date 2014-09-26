<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/* Database Configuration */
$config['database']=array(
	'hostname' => 'localhost',
	'database' => 'riskpix',
	'dbdriver' => 'mysql',
	'db_debug' => false,
);
// Useless
/* URL / Path Configuration */
$config['domain'] = $_SERVER['HTTP_HOST'];
$config['scheme'] = 'http';
if(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
	$config['scheme'] .= 's';
$config['base_path'] = $config['scheme'] . '://' . $config['domain'];
$config['full_path'] = $config['base_path'] . $_SERVER['REQUEST_URI'];
$config['assets_url'] = '/assets';
$config['module_path'] = APPPATH.'modules';

/* Metadata/SEO */
$config['site_name'] = 'RISKPIX';
$config['meta'] = array(
	'title' => "RISKPIX",
	'description' => "Insurance Quote Solutions",
	'keywords' =>'Insurance Quote Software, Insurance Quote System',
	'url' => $config['full_path'],
	'image' => '/assets/img/logo.png'
);
$config['copyright']='Copyright &copy; '.$config['site_name'].' '.date('Y').' All Rights Reserved.';
$config['seo_content'] = '';

/* Google Analytics */
$config['ga_code']=FALSE;

/* E-mail Notifications */
$config['contact_email'] = 'nickniebaum@gmail.com';
$config['email_notifications']=array(
	'sender_email'=>'no-reply@riskpix.com',
	'sender_name'=>'RISKPIX',
	'config'=>array(
		'protocol'=>'sendmail',
		'mailtype'=>'html',
	),
);

$config['error_email'] = 'nickniebaum@gmail.com';

/* SMS Notifications */
$config['sms_notifications']=array(
	'config'=>array(
		'mode'=>'prod',
		'account_sid'=>'ACbc39c48a6281fb1c17776a242fd592c9',
		'auth_token'=>'3e7012885483ba6d927af52f8c240d06',
		'api_version'=>'2010-04-01',
		'number'=>'+15126490010',
	),
);

/* Email/SMS resend */
$config['max_resends'] = 3;
