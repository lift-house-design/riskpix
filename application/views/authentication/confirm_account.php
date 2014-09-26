<?php if($confirmed): ?>
	<h1>Account Confirmed</h1>
	<p>Thank you for confirming your account! You may now log in using the e-mail address <strong><?php echo $email ?></strong> and password you entered when registering. 
<?php else: ?>
	<h1>Invalid Link</h1>
	<p>This link is no longer valid. Please make sure your link is correct and try again.
<?php endif; ?>
Please wait a moment while you are being redirected, or <?php echo anchor('/','click here') ?>.</p>
<meta http-equiv="refresh" content="5; url=/" />