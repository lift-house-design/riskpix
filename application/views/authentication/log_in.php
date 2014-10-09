<div class="spacer40"></div>
<?php echo form_open() ?>
    <div class="form-field">
        <?php echo form_label('E-mail Address','field-email') ?>
        <div class="field">
            <?php echo form_input(array(
                'id'=>'field-email',
                'name'=>'email',
                'placeholder'=>'E-mail Address',
                'value'=>set_value('email'),
            )) ?>
        </div>
    </div>
    <div class="form-field">
        <?php echo form_label('Password','field-password') ?>
        <div class="field">
            <?php echo form_password(array(
                'id'=>'field-password',
                'name'=>'password',
                'placeholder'=>'Password',
                'value'=>set_value('password'),
            )) ?>
        </div>
        <div class="hint">
            <a href="/authentication/forgot_password">Forgot Your Passord? Click Here</a>
        </div>
    </div>
    <div class="form-buttons">
        <?php echo form_submit(FALSE,'Log In') ?>
    </div>
<?php echo form_close() ?>
<script>
    $(function(){
        $(':input[name="email"]').focus();
    });
</script>