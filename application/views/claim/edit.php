<div class="spacer20"></div>
<form method="post">
	<input type="text" name="street_address" placeholder="Street Address" value="<?= $street_address ?>"/><br/>
	<input type="text" name="zip" placeholder="Zip Code" value="<?= $zip ?>"/><br/>
	<input type="text" name="name" placeholder="Name (first and last)" value="<?= $name ?>"/><br/>
	<input type="text" name="email" placeholder="Email" value="<?= $email ?>"/><br/>
	<input type="text" name="phone" placeholder="Mobile" value="<?= $phone ?>"/><br/>
	<input type="text" name="claim_number" placeholder="Policy Quote Number" value="<?= $claim_number ?>"/><br/>
	<?/*
	<input type="text" name="vin" placeholder="VIN" value="<?= $vin ?>"/>
	<input type="hidden" name="vin_override" value="<?= $vin_override ?>" />
	<div class="align-center">
		<div class="w300 align-right pull-center f10">*required</div>
	</div>
	*/?>
	<div class="spacer10"></div>
	<input type="submit" value="Update" />
</form>