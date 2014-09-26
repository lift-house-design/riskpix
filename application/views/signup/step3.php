<?php if($has_errors): ?>
  <div class="errors"><?php echo validation_errors() ?></div>
<?php endif; ?>
<div class="h100">
  <div class="auto">
    <div class="contact">
      <div class="center540">
        <h2 class="oleo c0">sign up - step 3 of 4</h2>
        <form id="contact-form" method="post">
          <input id="form-check" type="hidden" name="00<?= sha1(rand(0,time())) ?>" value=""/>

          <table border="0" cellpadding="5" cellspacing="0">
              <tr>
                <td class="align-right">Chose your monthly plan:</td>
                <td><?= form_dropdown('pricing', $pricing_options, set_value('pricing'));?><br>
                  Unused monthly reports: will rollover to next month.
                  Unused monthly reports expire on: date here.
                </td>
              </tr>
              <tr>
                <td class="align-right">Enter your discount code:</td>
                <td><input type="text" name="discount" placeholder="I don't have one" value="<?php echo set_value('discount') ?>"/></td>
              </tr>
              <!-- <tr>
                <td class="align-right" valign="top"></td>
                <td><input type="checkbox" name="terms"/> I accept the <a href="/terms">terms and conditions</a>.</td>
              </tr> -->
          </table>
            <input type="submit" value="Proceed to Step 4"/>
        </form>
      </div>
    </div>
  </div>
</div>
