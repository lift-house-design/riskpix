<?php
	$required=!empty($params['required']) ? TRUE : FALSE;
	unset($params['required']);

	$confirm_sms=!empty($params['confirm_sms']) ? TRUE : FALSE;
	unset($params['confirm_sms']);

	$display=!empty($params['display']) ? $params['display'] : FALSE;
	unset($params['display']);

	if($type=='phone')
	{
		$params['class']=empty($params['class']) ? 'phone' : $params['class'].' phone';
	}

	if($required)
	{
		$params['class']=empty($params['class']) ? 'required' : $params['class'].' required';
	}
?>
<div class="field<?php echo ( $required ? ' required' : '' ) ?>">
	<?php echo form_label($label, $name) ?>
	<div class="<?php echo $type ?> element">
	<?php switch($type):
		/*
		|--------------------------------------------------------------------------
		| Phone
		|--------------------------------------------------------------------------
		*/
			case 'phone': ?>
				<?php echo call_user_func('form_input',$params) ?>
				<?php if($confirm_sms): ?>
					<div class="checkbox field">
						<?php echo form_checkbox(array(
							'id'=>$name.'_text_capable',
							'name'=>$name.'_text_capable',
							'checked'=>TRUE,
						)) ?>
						<?php echo form_label('Is this phone text capable?',$name.'_text_capable') ?>
					</div>
				<?php endif; ?>
			<?php break; ?>
		<?php
		/*
		|--------------------------------------------------------------------------
		| Read Only
		|--------------------------------------------------------------------------
		*/
		?>
		<?php case 'readonly':
			if(is_array($params) && isset($params['value']))
				$value=$params['value'];
			else
				$value=$params;
			?>
			<span><?php echo $display ? $display : $value ?></span>
			<?php echo call_user_func('form_hidden',$name,$value) ?>
			<?php break; ?>
		<?php
		/*
		|--------------------------------------------------------------------------
		| Default: Text
		|--------------------------------------------------------------------------
		*/
		?>
		<?php default: ?>
			<?php echo call_user_func('form_'.$type,$params) ?>
			<?php break; ?>
	<?php endswitch; ?>
	</div>
</div>