<h3>Verify your vehicle</h3>
Make corrections if necessary.
<div class="spacer20"></div>
<form method="post">
	<div class="vehicle-data-table">
		<div class="row">
			<div class="l">VIN</div>
			<div class="r"><?php echo $claim['vin'] ?></div>
		</div>
		<div class="row">
			<div class="l pad2t">Year</div>
			<div class="r"><input name="year" value="<?php echo $claim['year'] ?>" class="w100pc"/></div>
		</div>
		<div class="row">
			<div class="l pad2t">Make</div>
			<div class="r"><input name="make" value="<?php echo $claim['make'] ?>" class="w100pc"/></div>
		</div>
		<div class="row">
			<div class="l pad2t">Model</div>
			<div class="r"><input name="model" value="<?php echo $claim['model'] ?>" class="w100pc"/></div>
		</div>
		<div class="row">
			<div class="l pad2t">Body</div>
			<div class="r"><input name="body" value="<?php echo $claim['body'] ?>" class="w100pc"/></div>
		</div>
	</div>
	<div class="spacer30"></div>
	<input type="submit" value="Verify Vehicle"/>
</form>