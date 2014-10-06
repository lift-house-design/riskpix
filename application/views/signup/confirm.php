<?php if($has_errors): ?>
<div class="errors"><?php echo validation_errors() ?></div>
<?php endif; ?>
<div class="h100">
    <div class="auto">
        <div class="contact">
            <div class="center540">
                <h2 class="oleo c0">sign up - confirm</h2>
                <?php var_dump($registration_data); ?>
                <h3 class="sub-heading">account information</h3>
                <div class="form-field">
                    <?php echo form_label('Company Name','field-company') ?>
                    <div class="value">
                        <?php echo $registration_data['company']['c_name'] ?>
                    </div>
                </div>
                <div class="form-field">
                    <?php echo form_label('Name','field-name') ?>
                    <div class="value">
                        <?php echo $registration_data['user']['first_name'].' '.$registration_data['user']['last_name'] ?>
                    </div>
                </div>
                <div class="form-field">
                    <?php echo form_label('E-mail Address','field-email') ?>
                    <div class="value">
                        <?php echo $registration_data['user']['email'] ?>
                    </div>
                </div>
                <div class="form-field">
                    <?php echo form_label('Company Phone','field-phone') ?>
                    <div class="value">
                        <?php echo $registration_data['company']['c_phone_main'] ?>
                    </div>
                </div>
                <div class="form-field">
                    <?php echo form_label('Mobile Phone','field-mobile') ?>
                    <div class="value">
                        <?php echo $registration_data['user']['phone'] ?>
                    </div>
                </div>
                <div class="form-field">
                    <?php echo form_label('Address','field-address') ?>
                    <div class="value">
                        <?php echo $registration_data['company']['c_address'] ?><br />
                        <?php echo $registration_data['company']['c_city'].', '.$registration_data['company']['c_state'].' '.$registration_data['company']['c_zipcode'] ?>
                    </div>
                </div>

                <h3 class="sub-heading">billing information</h3>
                <div class="form-field">
                    <?php echo form_label('Monthly Plan','field-pricing') ?>
                    <div class="field">
                        <?php //echo $registration_data['step1']['pricing'] ?>
                    </div>
                </div>
                <div class="form-field">
                    <?php echo form_label('Discount Code','field-discount') ?>
                    <div class="field">
                        <?php //echo $registration_data['step1']['company'] ?>
                    </div>
                </div>
                
            </div>
        </div>
    </div>
</div>