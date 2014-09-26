<h1>Users</h1>
<?php echo anchor('administration/users/create','Create User',array('id'=>'create-user','class'=>'primary button')) ?>
<table id="users">
	<thead>
		<tr>
			<td>E-mail</td>
			<td>Name</td>
			<td></td>
		</tr>
	</thead>
	<tbody>
	<?php foreach($entries as $u): ?>
		<tr data-user-id="<?php echo $u['id'] ?>">
			<td><?php echo $u['email'] ?></td>
			<td><?php echo $u['first_name'].' '.$u['last_name'] ?></td>
			<td class="center controls">
				<?php echo anchor('administration/users/edit/'.$u['id'],'Edit',array('class'=>'button')) ?>
				<?php echo anchor('administration/users/delete/'.$u['id'],'Remove',array('class'=>'remove button')) ?>
			</td>
		</tr>
	<?php endforeach; ?>
	</tbody>
</table>