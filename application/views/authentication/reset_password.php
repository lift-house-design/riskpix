<?php if($password_reset): ?>
	<h1>Password Reset</h1>
	<p>Reset successful. Redirecting...</p>
	<meta http-equiv="refresh" content="3; url=/admin" />
<?php elseif($confirmed): ?>
	<h1>Reset Password</h1>
	<p>Hi, <strong><?php echo $email ?></strong>. Please enter your new password below.</p>
	<?php echo form_open('/authentication/reset_password/'.$id.'/'.$confirm_code,array('class'=>'aligned')) ?>
		<?php echo form_field('Password','password','password',array(
			'required'=>TRUE,
		)) ?>
		<?php echo form_field('Confirm Password','confirm_password','password',array(
			'required'=>TRUE,
		)) ?>
		<div class="buttons">
			<?php echo form_submit('reset_password', 'Reset Password') ?>
		</div>
	<?php echo form_close() ?>
<?php else: ?>
	<h1>Invalid Link</h1>
	<p>This link is no longer valid. Please make sure your link is correct and try again. Please wait a moment while you are being redirected, or <?php echo anchor('/','click here') ?>.</p>
	<meta http-equiv="refresh" content="5; url=/" />
<?php endif; ?>