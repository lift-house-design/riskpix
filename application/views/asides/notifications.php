<?php if(!empty($notifications)){ ?>
	<div class="notifications">
		<?php echo implode('<br/>',$notifications) ?></li>
	</div>
<?php } ?>
<?php if(!empty($errors)){ ?>
	<div class="errors">
		<?php echo implode('<br/>',$errors) ?>
	</div>
<?php } ?>
