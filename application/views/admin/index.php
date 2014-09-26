<h2>Website Configuration</h2>
<form method="post">
	<? foreach($configuration as $i => $v){ ?>
		<b><?= ucfirst($v['label']) ?></b>
		<input name="<?= $v['name'] ?>" value="<?= $v['value'] ?>" placeholder="<?= $v['example'] ?>" type="text"/>
		<hr/>
	<? } ?>
	<input type="submit" name="action" value="Save Configuration"/>
</form>

<br/>
<hr/>
<br/>
<h2>Users</h2>
<table id="user-table">
	<? foreach($users as $u){ ?>
		<tr>
			<td><?= $u['email'] ?></td>	
			<td><?= $u['role'] ?></td>	
			<td><?= ($u['role'] == 'administrator' ? '' : '<a href="/admin/user_delete/'.$u['id'].'">Delete</a>') ?></td>
		</tr>
	<? } ?>
</table>
<br/>
<hr/>
<br/>
<h2>New User</h2>
<form method='post'>
	<? foreach($fields_add_user as $name => $f){ ?>
		<? if($f[1] == 'text'){ ?>
			<input type="text" name="<?= $name ?>" value="<? eval("echo \$$name;"); ?>" placeholder="<?= $f[0] ?>"/>
		<? }elseif($f[1] == 'select'){ ?>
			<?= eval("echo form_select(\$f[2], \$$name, '$name', \$f[0]);") ?>
		<? } ?>
		<br/>
	<? } ?>
	<input type="submit" name="action" value="Add User"/>
</form>

<script>
$(function(){
	// handle dependant elements
	<? 
	foreach($fields_add_user as $name => $f){
		if(!empty($f[4]))
			echo "q_dependency('".$name."', '".$f[4][0]."', ".json_encode($f[4][1]).");";
	}
	?>
});
</script>