<h2>Website Configuration</h2>
<form method="post">
	<?php foreach($configuration as $i => $v){ ?>
		<b><?php echo ucfirst($v['label']) ?></b>
		<input name="<?php echo $v['name'] ?>" value="<?php echo $v['value'] ?>" placeholder="<?php echo $v['example'] ?>" type="text"/>
		<hr/>
	<?php } ?>
	<input type="submit" name="action" value="Save Configuration"/>
</form>

<br/>
<hr/>
<br/>
<h2>Users</h2>
<table id="user-table">
	<?php foreach($users as $u){ ?>
		<tr>
			<td><?php echo $u['email'] ?></td>	
			<td><?php echo $u['role'] ?></td>	
			<td><?php echo ($u['role'] == 'administrator' ? '' : '<a href="/admin/user_delete/'.$u['id'].'">Delete</a>') ?></td>
		</tr>
	<?php } ?>
</table>
<br/>
<hr/>
<br/>
<h2>New User</h2>
<form method='post'>
	<?php foreach($fields_add_user as $name => $f){ ?>
		<?php if($f[1] == 'text'){ ?>
			<input type="text" name="<?php echo $name ?>" value="<?php eval("echo \$$name;"); ?>" placeholder="<?php echo $f[0] ?>"/>
		<?php }elseif($f[1] == 'select'){ ?>
			<?php echo eval("echo form_select(\$f[2], \$$name, '$name', \$f[0]);") ?>
		<?php } ?>
		<br/>
	<?php } ?>
	<input type="submit" name="action" value="Add User"/>
</form>

<script>
$(function(){
	// handle dependant elements
	<?php 
	foreach($fields_add_user as $name => $f){
		if(!empty($f[4]))
			echo "q_dependency('".$name."', '".$f[4][0]."', ".json_encode($f[4][1]).");";
	}
	?>
});
</script>