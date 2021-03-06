<?php if($has_errors): ?>
<div class="errors"><?php echo validation_errors() ?></div>
<?php endif; ?>
<div class="h100">
    <div class="auto">
        <div class="contact">
            <div class="center540">
                <?php //var_dump($user_data,$company_data,$plan_data); ?>
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

                <h3 class="sub-heading">billing information</h3>
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
                        <?php echo '$' . number_format($total,2) ?>
                    </div>
                </div>
                <?php echo form_open() ?>
                <?php if($total > 0): ?>
                    <h3 class="sub-heading">payment method</h3>
                    <div class="accordion">
                        <a>Invoice</a>
                        <div>
                          <div class="form-field">
                                <?php echo form_label('Address','field-invoice_address') ?>
                                <div class="field">
                                    <?php echo form_input(array(
                                        'id'=>'field-invoice_address',
                                        'name'=>'invoice_address',
                                        'placeholder'=>'Street Address',
                                        'value'=>set_value('invoice_address'),
                                    )) ?>
                                </div>
                            </div>
                            <div class="form-field">
                                <?php echo form_label('City','field-invoice_city') ?>
                                <div class="field">
                                    <?php echo form_input(array(
                                        'id'=>'field-invoice_city',
                                        'name'=>'invoice_city',
                                        'placeholder'=>'City',
                                        'value'=>set_value('invoice_city'),
                                    )) ?>
                                </div>
                            </div>
                            <div class="form-field">
                                <?php echo form_label('State','field-invoice_state') ?>
                                <div class="field">
                                    <?php echo form_dropdown('invoice_state',$states,set_value('invoice_state')) ?>
                                </div>
                            </div>
                            <div class="form-field">
                                <?php echo form_label('Zip Code','field-invoice_zip') ?>
                                <div class="field">
                                    <?php echo form_input(array(
                                        'id'=>'field-invoice_zip',
                                        'name'=>'invoice_zip',
                                        'placeholder'=>'Zip Code',
                                        'value'=>set_value('invoice_zip'),
                                    )) ?>
                                </div>
                            </div>
                        </div>
                        <a>Credit Card</a>
                        <div>
                            <div class="form-field">
                                <?php echo form_label('Card Number','field-cc_number') ?>
                                <div class="cc-num field">
                                    <?php echo form_input(array(
                                        'id'=>'field-cc_number',
                                        'class'=>'number',
                                        'name'=>'cc_number',
                                        'placeholder'=>'Card Number',
                                        'value'=>set_value('cc_number'),
                                    )) ?>
                                    <?php echo form_input(array(
                                        'id'=>'field-cc_cvc',
                                        'class'=>'cvc',
                                        'name'=>'cc_cvc',
                                        'placeholder'=>'CVC',
                                        'value'=>set_value('cc_cvc'),
                                    )) ?>
                                </div>
                                <div class="hint">
                                    <a href="http://en.wikipedia.org/wiki/Card_security_code" target="_blank">What is a CVC?</a>
                                </div>
                            </div>
                            <div class="form-field">
                                <?php echo form_label('Expiration Date','field-cc_exp') ?>
                                <div class="cc-exp field">
                                    <?php echo form_dropdown('cc_exp_month',$months,set_value('cc_exp_month')) ?>
                                    <?php echo form_dropdown('cc_exp_year',$years,set_value('cc_exp_year')) ?>
                                </div>
                            </div>
                            
                        </div>
                    </div>
                <?php endif; ?>
                <div class="form-field">
                    <?php echo form_label('Terms &amp; Conditions','field-accept_terms') ?>
                    <div class="field">
                        <?php echo form_checkbox('accept_terms','1',false) ?> I agree to and accept the <a href="" target="_blank">terms &amp; conditions</a>.
                        <?php if($total > 0): ?>
                            I also agree to allow Riskpix.com to charge my credit card or send an invoice to the address I've entered above.
                        <?php endif; ?>
                    </div>
                </div>
                
                <div class="form-buttons">
                    <?php echo form_submit(FALSE,'Complete Registration') ?>
                </div>
                <?php echo form_close() ?>
            </div>
        </div>
    </div>
</div>
<script>
    $(function(){
        $('.accordion')
            .children('a')
            .click(function(){
                var header=$(this),
                    panel=$(this).next();
                //Expand or collapse this panel
                panel.slideToggle('fast',function(){
                   // If collapsed
                    if(panel.filter(':hidden').length)
                    {
                        header.removeClass('selected');
                    }
                    else
                    {
                        header.addClass('selected');
                    }
                });

                //Hide the other panels
                $('.accordion > div')
                    .not(panel)
                    .slideUp('fast')
                    .prev()
                    .removeClass('selected');
            });
    });
</script>