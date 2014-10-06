<?php if($has_errors): ?>
  <div class="errors"><?php echo validation_errors() ?></div>
<?php endif; ?>
<div class="h100">
  <div class="auto">
    <div class="contact">
      <div class="center540">
        <h2 class="oleo c0">sign up - step 3 of 4</h2>
        <?php echo form_open() ?>
        <div class="form-field">
            <?php echo form_label('Choose a Monthly Plan','field-pricing') ?>
            <div class="field">
                <?php echo form_dropdown('pricing', $pricing_options, set_value('pricing'),'id="field-pricing"') ?>
            </div>
            <div class="hint">
                Unused monthly reports will rollover to next month.<br />
                Unused monthly reports expire on: <span id="rollover-expiration"></span>.
            </div>
        </div>
        <div class="form-field">
            <?php echo form_label('Discount Code','field-discount') ?>
            <div class="field">
                <?php echo form_input(array(
                    'id'=>'field-discount',
                    'name'=>'discount',
                    'placeholder'=>'I don\'t have one',
                    'value'=>set_value('discount'),
                )) ?>
            </div>
        </div>
        <div class="form-buttons">
            <?php echo form_submit(FALSE,'Proceed to Step 4') ?>
        </div>
        <?php echo form_close() ?>
      </div>
    </div>
  </div>
</div>
<script>
    $(function(){
        rollover_expirations=<?php echo json_encode($rollover_expirations) ?>;

        $('#field-pricing')
            .on('change',function(){
                var selected=$(this).val(),
                    expiration=rollover_expirations[selected];

                $('#rollover-expiration').html(expiration);
            })
            .change();
    });
</script>