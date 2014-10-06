<?php if($has_errors): ?>
<div class="errors"><?php echo validation_errors() ?></div>
<?php endif; ?>
<div class="h100">
    <div class="auto">
        <div class="contact">
            <div class="center540">
                <?php var_dump($user_data,$company_data,$plan_data); ?>
                <h2 class="oleo c0">sign up - confirm</h2>
                <p>Please check and confirm all the information below is correct before submitting your payment.</p>
                <h3 class="sub-heading">account information</h3>
                <div class="form-field">
                    <?php echo form_label('Company Name','field-company') ?>
                    <div class="value">
                        <?php echo $company_data['c_name'] ?>
                    </div>
                </div>
                <div class="form-field">
                    <?php echo form_label('Name','field-name') ?>
                    <div class="value">
                        <?php echo $user_data['first_name'].' '.$user_data['last_name'] ?>
                    </div>
                </div>
                <div class="form-field">
                    <?php echo form_label('E-mail Address','field-email') ?>
                    <div class="value">
                        <?php echo $user_data['email'] ?>
                    </div>
                </div>
                <div class="form-field">
                    <?php echo form_label('Company Phone','field-phone') ?>
                    <div class="value">
                        <?php echo $company_data['c_phone_main'] ?>
                    </div>
                </div>
                <div class="form-field">
                    <?php echo form_label('Mobile Phone','field-mobile') ?>
                    <div class="value">
                        <?php echo $user_data['phone'] ?>
                    </div>
                </div>
                <div class="form-field">
                    <?php echo form_label('Address','field-address') ?>
                    <div class="value">
                        <?php echo $company_data['c_address'] ?><br />
                        <?php echo $company_data['c_city'].', '.$company_data['c_state'].' '.$company_data['c_zipcode'] ?>
                    </div>
                </div>

                <h3 class="sub-heading">pricing plan information</h3>
                <div class="form-field">
                    <?php echo form_label('Pricing Plan','field-pricing') ?>
                    <div class="value">
                        <?php echo $plan_data['volume'] . ' @ $' . number_format($plan_data['price'],2) . 'ea. / ($' . number_format($plan_data['volume']*$plan_data['price'],2) . ')' ?>
                    </div>
                    <?php if($plan_data['rollover']): ?>
                        <div class="hint">
                            Unused monthly reports will rollover to next month.<br />
                            Unused monthly reports expire on: <span id="rollover-expiration"><?php echo date('m/d/Y',strtotime('+'.($plan_data['rollover_months']+1).' months')) ?></span>.
                        </div>
                    <?php endif; ?>
                </div>
                <?php if($plan_data['discount']): ?>
                <div class="form-field">
                    <?php echo form_label('Discount Code','field-discount') ?>
                    <div id="discount-code" class="value">
                        <?php echo strtoupper($plan_data['discount']); ?>
                    </div>
                </div>
                <?php endif; ?>
                <div class="form-field">
                    <?php echo form_label('Total Due','field-total') ?>
                    <div class="value">
                        <?php echo '$' . number_format($plan_data['volume']*$plan_data['price'],2) ?>
                    </div>
                </div>

                <h3>billing information</h3>

            </div>
        </div>
    </div>
</div>