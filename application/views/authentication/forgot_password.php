<h2>Forgotten Password</h2>
<p>Enter your e-mail address below<br/> and you will be sent a link<br/>to retrieve your password.</p>
<br/>
<?php echo form_open('/authentication/forgot_password',array('class'=>'aligned')) ?>
	<?php echo form_field('<b>E-mail Address</b>','email','text',array(
		'value'=>set_value('email'),
		'required'=>TRUE,
		/*'placeholder'=>'E-mail Address'*/
	)) ?>
	<div class="buttons">
		<?php echo form_submit('retrieve_password', 'Retrieve Password') ?>
	</div>
<?php echo form_close() ?>