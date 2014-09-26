<?php if($has_errors): ?>
<div class="errors"><?php echo validation_errors() ?></div>
<?php endif; ?>
<div class="h100">
  <div class="auto">
    <div class="contact">
      <div class="center540">
        <h2 class="oleo c0">sign up - step 2 of 4</h2>
        <?php echo form_open() ?>
        <div class="form-field">
            <?php echo form_label('Billing Address','field_address') ?>
            <div class="field">
                <?php echo form_input(array(
                    'id'=>'field_address',
                    'name'=>'address',
                    'placeholder'=>'Street Address',
                    'value'=>set_value('address'),
                )) ?>
            </div>
        </div>
        <div class="form-field">
            <?php echo form_label('City','field_city') ?>
            <div class="field">
                <?php echo form_input(array(
                    'id'=>'field_city',
                    'name'=>'city',
                    'placeholder'=>'City',
                    'value'=>set_value('city'),
                )) ?>
            </div>
        </div>
        <div class="form-field">
            <?php echo form_label('State','field_state') ?>
            <div class="field">
                <?php echo form_dropdown('state',$states,set_value('state')) ?>
            </div>
        </div>
        <div class="form-field">
            <?php echo form_label('Zip Code','field_zip') ?>
            <div class="field">
                <?php echo form_input(array(
                    'id'=>'field_zip',
                    'name'=>'zip',
                    'placeholder'=>'Zip Code',
                    'value'=>set_value('zip'),
                )) ?>
            </div>
        </div>
        <div class="form-field">
            <?php echo form_label('Company Phone','field_phone') ?>
            <div class="field">
                <?php echo form_input(array(
                    'id'=>'field_phone',
                    'name'=>'phone',
                    'placeholder'=>'Company Phone',
                    'value'=>set_value('phone'),
                )) ?>
            </div>
        </div>
        <div class="form-field">
            <?php echo form_label('Mobile Phone','field_mobile') ?>
            <div class="field">
                <?php echo form_input(array(
                    'id'=>'field_mobile',
                    'name'=>'mobile',
                    'placeholder'=>'Mobile Phone',
                    'value'=>set_value('mobile'),
                )) ?>
            </div>
        </div>
        <div class="form-buttons">
            <?php echo form_submit(FALSE,'Proceed to Step 3') ?>
        </div>
        <?php echo form_close() ?>
      </div>
    </div>
  </div>
</div>
