<div class="spacer20"></div>
<?/* <b>Please answer the following questions about your home:</b> */?>
<b>Basic Information</b>
<div class="spacer20"></div>

<form method="post" class="w400max align-center">
	<? foreach($fields as $name => $f){ ?>
		<? if($f[1] == 'text'){ ?>
			<input type="text" name="<?= $name ?>" value="<? eval("echo \$$name;"); ?>" placeholder="<?= $f[0] ?>"/>
		<? }elseif($f[1] == 'select'){ ?>
			<?= eval("echo form_select(\$f[2], \$$name, '$name', \$f[0]);") ?>
		<? } ?>
	<? } ?>
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
	<? 
	foreach($fields as $name => $f){
		if(!empty($f[4]))
			echo "q_dependency('".$name."', '".$f[4][0]."', ".json_encode($f[4][1]).");";
	}
	?>
});
</script>