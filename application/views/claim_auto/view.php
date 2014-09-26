<?/* var_dump($claim); */?>
<div class="spacer20"></div>
<div class="vehicle-data-table">
	<? foreach($claim as $i => $v){ ?>	
		<? if(!$v) continue; ?>
		<div class="row">
			<div class="l"><?= $i ?></div>
			<div class="r"><?= $v ?></div>
		</div>
	<? } ?>
</div>
<div class="spacer20"></div>
<div class="photos">
	<? foreach($photos as $p){ ?>
		<img class="full" src="<?= $p['url'] ?>"/><br/>
	<? } ?>
</div>