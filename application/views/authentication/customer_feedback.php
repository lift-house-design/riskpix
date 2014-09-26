<?/*<h1>Welcome to TableQuick!</h1>
<p>Restaurant ID: <?=$user_id?></p>
<p>Table Number: <?=$table_number?></p>
<p>Server Name: <?=$server_name?></p>
*/?>
<? if(isset($comment)){ ?>
	<div style="width:100%;text-align:center">
		<h2>Thank you for your input!</h2>
		<h3>Your comments ensure that we provide the best service possible.</h3>
		<p><?=$comment?></p>
	</div>
	<? return; ?>
<? }else{ ?>
	<form id="feedback-form" action="/site/customer_feedback" method="post" style="width:100%;text-align:center">
		<h1>Comments <?=($server_name ? "regarding $server_name" : "")?></h1>
		<div id="feedback-errors"></div>
		<input type="hidden" value="<?=$user_id?>" name="user_id"/>
		<input type="hidden" value="<?=$table_number?>" name="table_number"/>
		<input type="hidden" value="<?=$server_name?>" name="server_name"/>
		<input name="name" placeholder="Your Name" style="margin-bottom:10px;width:50%;min-width:300px"/><br/>
		<input name="phone" placeholder="Your Phone Number" style="margin-bottom:10px;width:50%;min-width:300px"/><br/>
		<textarea name="comment" style="width:50%;min-width:300px;height:200px" placeholder="Comments"></textarea><br/>
		<input type="submit" style="margin-top:10px;width:50%;min-width:300px"/>
	</form> 
	<script>
		// just for the demos, avoids form submit
		$(document).ready(function(){
		jQuery.validator.setDefaults({
			//debug: true,
			success: "valid"
		});
		$( "#feedback-form" ).validate({
			errorPlacement: function(error, element) {
     			error.appendTo( $('#feedback-errors') );
   			},
   			groups: {
   				nameGroup: "name phone comment"
   			},
			rules: {
				name: {
					required: true
				},
				phone: { required: true, phoneUS: true},
		    	comment: {
		    		required: true
		    	}
		  	},
		  	messages: {
		  		name: "Please enter your name.",
		  		phone: "Please enter your phone number.",
		  		comment: "Please enter your comments about our service."
		  	}
		});
	});
	</script>
<? } ?>