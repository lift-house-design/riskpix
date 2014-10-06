<?php if($has_errors): ?>
    <div class="errors"><?php echo validation_errors() ?></div>
<?php endif; ?>
<div class="h100">
    <div class="auto">
        <div class="contact">
            <div class="center540">
                <h2 class="oleo c0">sign up - step 1 of 4</h2>
                <?php echo form_open() ?>
                <div class="form-field">
                    <?php echo form_label('Company Name','field-company') ?>
                    <div class="field">
                        <?php echo form_input(array(
                            'id'=>'field-company',
                            'name'=>'company',
                            'placeholder'=>'Your Company\'s Name',
                            'value'=>set_value('company'),
                        )) ?>
                    </div>
                </div>
                <div class="form-field">
                    <?php echo form_label('Full Name','field-name') ?>
                    <div class="field">
                        <?php echo form_input(array(
                            'id'=>'field-name',
                            'name'=>'name',
                            'placeholder'=>'Your Full Name',
                            'value'=>set_value('name'),
                        )) ?>
                    </div>
                </div>
                <div class="form-field">
                    <?php echo form_label('E-mail Address','field-email') ?>
                    <div class="field">
                        <?php echo form_input(array(
                            'id'=>'field-email',
                            'name'=>'email',
                            'placeholder'=>'Your E-mail',
                            'value'=>set_value('email'),
                        )) ?>
                    </div>
                </div>
                <div class="form-field">
                    <?php echo form_label('Confirm E-mail','field-confirm_email') ?>
                    <div class="field">
                        <?php echo form_input(array(
                            'id'=>'field-confirm_email',
                            'name'=>'confirm_email',
                            'placeholder'=>'Re-enter Your E-mail',
                            'value'=>set_value('confirm_email'),
                        )) ?>
                    </div>
                </div>
                <div class="form-field">
                    <?php echo form_label('Password','field-password') ?>
                    <div class="field">
                        <?php echo form_password(array(
                            'id'=>'field-password',
                            'name'=>'password',
                            'placeholder'=>'Your Password',
                            'value'=>set_value('password'),
                        )) ?>
                    </div>
                    <div class="hint">Must be at least 8 characters long.</div>
                </div>
                <div class="form-field">
                    <?php echo form_label('Confirm Password','field-confirm_password') ?>
                    <div class="field">
                        <?php echo form_password(array(
                            'id'=>'field-confirm_password',
                            'name'=>'confirm_password',
                            'placeholder'=>'Re-enter Your Password',
                            'value'=>set_value('confirm_password'),
                        )) ?>
                    </div>
                </div>
                <div class="form-buttons">
                    <?php echo form_submit(FALSE,'Proceed to Step 2') ?>
                </div>
                <?php echo form_close() ?>
            </div>
        </div>
    </div>
</div>
