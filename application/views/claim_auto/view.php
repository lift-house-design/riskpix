<?php /* var_dump($claim); */?>
<div class="spacer20"></div>
<div class="vehicle-data-table">
	<?php foreach($claim as $i => $v){ ?>	
		<?php if(!$v) continue; ?>
		<div class="row">
			<div class="l"><?php echo $i ?></div>
			<div class="r"><?php echo $v ?></div>
		</div>
	<?php } ?>
</div>
<div class="spacer20"></div>
<div class="photos">
	<?php foreach($photos as $p){ ?>
		<img class="full" src="<?php echo $p['url'] ?>"/><br/>
	<?php } ?>
</div>