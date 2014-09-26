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
                    <?php echo form_label('Company Name','field_company') ?>
                    <div class="field">
                        <?php echo form_input(array(
                            'id'=>'field_company',
                            'name'=>'company',
                            'placeholder'=>'Company Name',
                            'value'=>set_value('company'),
                        )) ?>
                    </div>
                </div>
                <div class="form-field">
                    <?php echo form_label('First &amp; Last Name','field_name') ?>
                    <div class="field">
                        <?php echo form_input(array(
                            'id'=>'field_name',
                            'name'=>'name',
                            'placeholder'=>'Your Name',
                            'value'=>set_value('name'),
                        )) ?>
                    </div>
                </div>
                <div class="form-field">
                    <?php echo form_label('Enter your email address','field_email') ?>
                    <div class="field">
                        <?php echo form_input(array(
                            'id'=>'field_email',
                            'name'=>'email',
                            'placeholder'=>'Email',
                            'value'=>set_value('email'),
                        )) ?>
                    </div>
                </div>
                <div class="form-field">
                    <?php echo form_label('Re-enter your email','field_email2') ?>
                    <div class="field">
                        <?php echo form_input(array(
                            'id'=>'field_email2',
                            'name'=>'email2',
                            'placeholder'=>'Re-enter Email',
                            'value'=>set_value('email2'),
                        )) ?>
                    </div>
                </div>
                <div class="form-field">
                    <?php echo form_label('Eight character password','field_password') ?>
                    <div class="field">
                        <?php echo form_input(array(
                            'id'=>'field_password',
                            'name'=>'password',
                            'placeholder'=>'Password',
                            'value'=>set_value('password'),
                        )) ?>
                    </div>
                </div>
                <div class="form-field">
                    <?php echo form_label('Re-enter your password','field_password2') ?>
                    <div class="field">
                        <?php echo form_input(array(
                            'id'=>'field_password2',
                            'name'=>'password2',
                            'placeholder'=>'Re-enter Password',
                            'value'=>set_value('password2'),
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
