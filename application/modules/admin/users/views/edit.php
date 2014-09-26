<h1>Edit User</h1>
<h3>Account Details</h3>
<?php echo form_open('administration/users/edit/'.$data['id'],array(
	'class'=>'aligned',
	//'class'=>'stacked',
),array(
	'id'=>$data['id'],
	'_email'=>$data['email'],
)) ?>
<div class="required field">
	<?php echo form_label('E-mail','email') ?>
	<?php echo form_input(array(
		'name'=>'email',
		'id'=>'email',
		'value'=>$data['email'],
	)) ?>
</div>
<div class="required field">
	<?php echo form_label('First Name','first_name') ?>
	<?php echo form_input(array(
		'name'=>'first_name',
		'id'=>'first_name',
		'value'=>$data['first_name'],
	)) ?>
</div>
<div class="field">
	<?php echo form_label('Last Name','last_name') ?>
	<?php echo form_input(array(
		'name'=>'last_name',
		'id'=>'last_name',
		'value'=>$data['last_name'],
	)) ?>
</div>
<div class="field">
	<?php echo form_label('Phone','phone') ?>
	<div class="element">
		<?php echo form_input(array(
			'name'=>'phone',
			'id'=>'phone',
			'value'=>$data['phone'],
			'class'=>'phone',
		)) ?>
		<div class="checkbox field">
		<?php echo form_checkbox(array(
			'name'=>'phone_text_capable',
			'id'=>'phone_text_capable',
			'checked'=>$data['phone_text_capable'],
		)) ?>
		<?php echo form_label('Is this phone text capable?','phone_text_capable') ?>
		</div>
	</div>
</div>
<h3>Password</h3>
<div class="field">
	<?php echo form_label('Change Password','password') ?>
	<?php echo form_password(array(
		'name'=>'password',
		'id'=>'password',
	)) ?>
</div>
<div class="field">
	<?php echo form_label('Confirm Password','confirm_password') ?>
	<?php echo form_password(array(
		'name'=>'confirm_password',
		'id'=>'confirm_password',
	)) ?>
</div>
<h3>Roles and Permissions</h3>
<?php foreach($this->user->get_all_roles() as $role=>$role_description): ?>
<div class="checkbox field">
	<?php echo form_checkbox(array(
		'name'=>'roles[]',
		'id'=>'roles_'.$role,
		'value'=>$role,
		'checked'=>in_array($role,$data['roles']),
	)) ?>
	<?php echo form_label('<strong>'.ucwords(str_replace('_',' ',$role)).'</strong> - '.$role_description,'roles_'.$role) ?>
</div>
<?php endforeach; ?>
<div class="buttons">
	<input type="submit" value="Save User" />
	<?php echo anchor('administration/users','Cancel',array('class'=>'button')) ?>
</div>
</form>