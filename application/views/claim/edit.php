<div class="spacer20"></div>
<form method="post">
	<input type="text" name="street_address" placeholder="Street Address" value="<?php echo $street_address ?>"/><br/>
	<input type="text" name="zip" placeholder="Zip Code" value="<?php echo $zip ?>"/><br/>
	<input type="text" name="name" placeholder="Name (first and last)" value="<?php echo $name ?>"/><br/>
	<input type="text" name="email" placeholder="Email" value="<?php echo $email ?>"/><br/>
	<input type="text" name="phone" placeholder="Mobile" value="<?php echo $phone ?>"/><br/>
	<input type="text" name="claim_number" placeholder="Policy Quote Number" value="<?php echo $claim_number ?>"/><br/>
	<?php /*
	<input type="text" name="vin" placeholder="VIN" value="<?php echo $vin ?>"/>
	<input type="hidden" name="vin_override" value="<?php echo $vin_override ?>" />
	<div class="align-center">
		<div class="w300 align-right pull-center f10">*required</div>
	</div>
	*/?>
	<div class="spacer10"></div>
	<input type="submit" value="Update" />
</form>