<div class="spacer20"></div>
<form method="post">
	<input type="text" name="name" placeholder="first & last name*" value="<?= $name ?>"/><br/>
	<input type="text" name="email" placeholder="policy holder email" value="<?= $email ?>"/><br/>
	<input type="text" name="phone" placeholder="policy holder mobile number" value="<?= $phone ?>"/><br/>
	<?/*<input type="text" name="policy_number" placeholder="policy number" value="<?= $policy_number ?>"/><br/>*/?>
	<input type="text" name="claim_number" placeholder="enter claim number*" value="<?= $claim_number ?>"/><br/>
	<input type="text" name="vin" placeholder="VIN" value="<?= $vin ?>"/>
	<input type="hidden" name="vin_override" value="<?= $vin_override ?>" />
	<div class="align-center">
		<div class="w300 align-right pull-center f10">*required</div>
	</div>
	<div class="spacer10"></div>
	<input type="submit" value="Create" />
</form>