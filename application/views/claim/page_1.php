<div class="spacer20"></div>
<?/* <b>Please answer the following questions about your home:</b> */?>
<b>Home Owner Information</b>
<div class="spacer20"></div>
<form method="post" class="w400max align-center">
	<? foreach($fields as $k => $f){ ?>
		<? if($f[1] == 'text'){ ?>
			<input type="text" name="<?= $k ?>" value="<? eval("echo \$$k;"); ?>" placeholder="<?= $f[0] ?>"/>
		<? }elseif($f[1] == 'select'){ ?>
			<?= eval("echo form_select(\$f[2], \$$k, '$k', \$f[0]);") ?>
		<? }elseif($f[1] == 'textarea'){ ?>
			<textarea name="<?= $k ?>" placeholder="<?= $f[0] ?>"><? eval("echo \$$k;"); ?></textarea>
		<? }elseif($f[1] == 'checkbox'){ ?>
			<?= eval("echo form_checkbox(\$f[2], \$$k, '$k', \$f[0], 'class=\"pull-center estimator-radio w300\"');") ?>
		<? } ?>
	<? } ?>
	<?/*
	<input type="text" name="vin" placeholder="VIN" value="<?= $vin ?>"/>
	<input type="hidden" name="vin_override" value="<?= $vin_override ?>" />
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
	<? 
	foreach($fields as $name => $f){
		if(!empty($f[4]))
			echo "q_dependency('".$name."', '".$f[4][0]."', ".json_encode($f[4][1]).");";
	}
	?>
});
</script>