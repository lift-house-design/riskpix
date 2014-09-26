<div class="spacer20"></div>
<b>SEND NOTIFICATION</b>
<div class="spacer20"></div>
<button type="button" class="sms">Text to Home Owner</button>
<br/>
<button type="button" class="email">Email to Home Owner</button>
<div class="spacer20"></div>
<hr/>
<div class="spacer20"></div>
Copy URL for email
<div class="spacer20"></div>
<input class="copy" value="<?= $report_url ?>" onclick="this.value='<?= $report_url ?>';this.select()" onkeydown="this.value='<?= $report_url ?>';this.select()" onchange="this.value='<?= $report_url ?>';this.select()"/>
<script>
$(function(){
	$('input').select();
	<? if(empty($claim['phone'])){ ?>
		$('button.sms').attr('disabled','disabled');
	<? } ?>
	<? if(empty($claim['email'])){ ?>
		$('button.email').attr('disabled','disabled');
	<? } ?>
});
$('button.sms').click(function(e)
{
	$.get(
		'/claim/text_message/<?= $hash ?>',
		{},
		function(data)
		{
			if(!data.success)
				return;
			$('button.sms').attr('disabled','disabled');
			$('button.sms').html('Text sent!');
			//$('button.sms').html('Text sent to '+data.success);
		},
		'json'
	);
});
$('button.email').click(function(e)
{
	$.get(
		'/claim/email_message/<?= $hash ?>',
		{},
		function(data)
		{
			if(!data.success)
				return;
			$('button.email').attr('disabled','disabled');
			$('button.email').html('Email sent!');
			//$('button.email').html('Email sent to '+data.success);
		},
		'json'
	);
});
<?/*
$('button.estimator').click(function(e)
{
	var estimator = $('[name="estimator"]:checked').val();
	if(!estimator)
		return;
	$.get(
		'/claim/set_estimator/<?= $hash ?>/'+estimator,
		{},
		function(data)
		{
			if(!data.success)
				return;
			$('[name="estimator"]').attr('disabled','disabled');
			$('button.estimator').attr('disabled','disabled');
			$('button.estimator').html('Estimator Set!');
			//$('button.email').html('Email sent to '+data.success);
		},
		'json'
	);
});
*/?>
</script>