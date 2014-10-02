Claim Created!<br/>
<br/>
Send the link below to the vehicle owner:<br/>
<br/>
<input class="copy" value="<?php echo $report_url ?>" onclick="this.value='<?php echo $report_url ?>';this.select()" onkeydown="this.value='<?php echo $report_url ?>';this.select()" onchange="this.value='<?php echo $report_url ?>';this.select()"/>
<form>
<button type="button" class="sms">Text to Vehicle Owner</button>
<br/>
<button type="button" class="email">Email to Vehicle Owner</button>
</form>
<script>
$(function(){
	$('input').select();
	<?php if(empty($claim['phone'])){ ?>
		$('button.sms').attr('disabled','disabled');
	<?php } ?>
	<?php if(empty($claim['email'])){ ?>
		$('button.email').attr('disabled','disabled');
	<?php } ?>
});
$('button.sms').click(function(e)
{
	$.get(
		'/claim/text_message/<?php echo $hash ?>',
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
		'/claim/email_message/<?php echo $hash ?>',
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
</script>