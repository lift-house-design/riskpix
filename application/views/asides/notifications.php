<? if(!empty($notifications)){ ?>
	<div class="notifications">
		<?= implode('<br/>',$notifications) ?></li>
	</div>
<? } ?>
<? if(!empty($errors)){ ?>
	<div class="errors">
		<?= implode('<br/>',$errors) ?>
	</div>
<? } ?>
