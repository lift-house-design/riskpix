<?php if(!empty($claim['claim_number'])){ ?>
	<div class="spacer50"></div>
<?php } ?>
<div id="footer">
	<?php if(!empty($claim['claim_number'])){ ?>
		<div id="claim-number" class="align-center pad20">
			<b>Your Policy Quote Number: <?php echo $claim['claim_number'] ?></b>
		</div>
	<?php } ?>
	<?php /*
	<div class="spacer10 teal1bg"></div>
	<div class="spacer5"></div>
	<div class="orangebg pad10">
		<?php /*<i>powered by:</i><br/>
		<a href="/">
			<img src="/assets/img/text_logo.png"/>
		</a>
	</div>
	*/?>
	<div class="spacer10"></div>
</div>