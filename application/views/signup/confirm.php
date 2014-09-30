<?php if($has_errors): ?>
<div class="errors"><?php echo validation_errors() ?></div>
<?php endif; ?>
<div class="h100">
    <div class="auto">
        <div class="contact">
            <div class="center540">
                <h2 class="oleo c0">sign up - confirm</h2>
                <?php var_dump($registration_data); ?>
                <h3>account details</h3>
                <div class="form-field">
                    <?php echo form_label('Company Name','field_company') ?>
                    <div class="field">
                        <?php echo $registration_data['step1']['company'] ?>
                    </div>
                </div>
                <div class="form-field">
                    <?php echo form_label('Name','field_name') ?>
                    <div class="field">
                        <?php echo $registration_data['step1']['name'] ?>
                    </div>
                </div>
                <div class="form-field">
                    <?php echo form_label('E-mail Address','field_email') ?>
                    <div class="field">
                        <?php echo $registration_data['step1']['email'] ?>
                    </div>
                </div>
                <div class="form-field">
                    <?php echo form_label('Company Phone','field_phone') ?>
                    <div class="field">
                        <?php echo $registration_data['step2']['phone'] ?>
                    </div>
                </div>
                <div class="form-field">
                    <?php echo form_label('Mobile Phone','field_mobile') ?>
                    <div class="field">
                        <?php echo $registration_data['step2']['mobile'] ?>
                    </div>
                </div>
                <div class="form-field">
                    <?php echo form_label('Address','field_address') ?>
                    <div class="field">
                        <?php echo $registration_data['step2']['address'] ?><br />
                        <?php echo $registration_data['step2']['city'].', '.$registration_data['step2']['state'].' '.$registration_data['step2']['zip'] ?>
                    </div>
                </div>
                <div class="form-field">
                    <?php echo form_label('Company Name','field_company') ?>
                    <div class="field">
                        <?php echo $registration_data['step1']['company'] ?>
                    </div>
                </div>
                <div class="form-field">
                    <?php echo form_label('Company Name','field_company') ?>
                    <div class="field">
                        <?php echo $registration_data['step1']['company'] ?>
                    </div>
                </div>
                
            </div>
        </div>
    </div>
</div>