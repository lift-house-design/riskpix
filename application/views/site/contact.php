<div class="h100">
	<div class="auto">
		<div class="contact">
			<div class="center540">
				<h2 class="oleo c0">general inquiry</h2>
				<form id="contact-form" method="post">
					<input id="form-check" type="hidden" name="00<?php echo sha1(rand(0,time())) ?>" value=""/>
					<input type="text" name="name" placeholder="Name (first and last)" value="<?php echo $name ?>"/>
					<input type="text" name="phone" placeholder="Mobile Phone" value="<?php echo $phone ?>"/>
					<input type="text" name="email" placeholder="Email" value="<?php echo $email ?>"/>
					<textarea name="message" placeholder="How can we help you?" class="tall"><?php echo $message ?></textarea><br>
					<input type="submit" value="SEND"/>
				</form>
			</div>
		</div>
	</div>
</div>
