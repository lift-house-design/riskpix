<div class="spacer20"></div>
<?php /* <b>Please answer the following questions about your home:</b> */?>
<b>Basic Information</b>
<div class="spacer20"></div>

<form method="post" class="w400max align-center">
	<?php foreach($fields as $name => $f){ ?>
		<?php if($f[1] == 'text'){ ?>
			<input type="text" name="<?php echo $name ?>" value="<?php eval("echo \$$name;"); ?>" placeholder="<?php echo $f[0] ?>"/>
		<?php }elseif($f[1] == 'select'){ ?>
			<?php echo eval("echo form_select(\$f[2], \$$name, '$name', \$f[0]);") ?>
		<?php } ?>
	<?php } ?>
	<input type="submit" value="NEXT" />
	<div class="spacer20"></div>
	<div class="spacer20"></div>
	<div class="spacer20"></div>
</form>
<script>
$(function(){
	/*
	$('select').chosen({
		disable_search_threshold: 4, 
		allow_single_deselect: true
	});
	*/
	// handle dependant elements
	<?php 
	foreach($fields as $name => $f){
		if(!empty($f[4]))
			echo "q_dependency('".$name."', '".$f[4][0]."', ".json_encode($f[4][1]).");";
	}
	?>
});
</script>