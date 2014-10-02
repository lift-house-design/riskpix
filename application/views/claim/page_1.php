<div class="spacer20"></div>
<?php /* <b>Please answer the following questions about your home:</b> */?>
<b>Home Owner Information</b>
<div class="spacer20"></div>
<form method="post" class="w400max align-center">
	<?php foreach($fields as $k => $f){ ?>
		<?php if($f[1] == 'text'){ ?>
			<input type="text" name="<?php echo $k ?>" value="<?php eval("echo \$$k;"); ?>" placeholder="<?php echo $f[0] ?>"/>
		<?php }elseif($f[1] == 'select'){ ?>
			<?php echo eval("echo form_select(\$f[2], \$$k, '$k', \$f[0]);") ?>
		<?php }elseif($f[1] == 'textarea'){ ?>
			<textarea name="<?php echo $k ?>" placeholder="<?php echo $f[0] ?>"><?php eval("echo \$$k;"); ?></textarea>
		<?php }elseif($f[1] == 'checkbox'){ ?>
			<?php echo eval("echo form_checkbox(\$f[2], \$$k, '$k', \$f[0], 'class=\"pull-center estimator-radio w300\"');") ?>
		<?php } ?>
	<?php } ?>
	<?php /*
	<input type="text" name="vin" placeholder="VIN" value="<?php echo $vin ?>"/>
	<input type="hidden" name="vin_override" value="<?php echo $vin_override ?>" />
	<div class="align-center">
		<div class="w300 align-right pull-center f10">*required</div>
	</div>
	*/?>
	<div></div>
	<input type="submit" value="Create Report" />
</form>

<script>
$(function(){
	// handle dependant elements
	<?php 
	foreach($fields as $name => $f){
		if(!empty($f[4]))
			echo "q_dependency('".$name."', '".$f[4][0]."', ".json_encode($f[4][1]).");";
	}
	?>
});
</script>