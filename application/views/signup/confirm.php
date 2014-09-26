<?php if($has_errors): ?>
  <div class="errors"><?php echo validation_errors() ?></div>
<?php endif; ?>
<div class="h100">
  <div class="auto">
    <div class="contact">
      <div class="center540">
        <h2 class="oleo c0">sign up - confirm your registration</h2>
        <form id="payment-form" method="post">
          <input id="form-check" type="hidden" name="00<?= sha1(rand(0,time())) ?>" value=""/>
          <span class="payment-errors"></span>
          <table border="0" cellpadding="5" cellspacing="0">
              <tr>
                <td class="align-right">Card Number:</td>
                <td><input type="text" size="20" data-stripe="number"<? if ($is_test_transaction == true) { ?> value="<?= $test_card_number; ?>"<? } ?>/></td>
              </tr>
              <tr>
                <td class="align-right">CVC Code:</td>
                <td><input type="text" size="4" data-stripe="cvc"<? if ($is_test_transaction == true) { ?> value="<?= $test_cvc_code; ?>"<? } ?>/></td>
              </tr>
              <tr>
                <td class="align-right">Expiration Date:</td>
                <td>
                  <select size="1" data-stripe="exp-month">
<?
$m = date('n');
for ($i=1; $i<=12; $i++) {
  $monthname = date("F", mktime(0, 0, 0, $i, 10));
?>
    <option value="<?= $i; ?>"<? if ($m == $i) { ?> selected<? } ?>><?= $monthname; ?></option>
<?
 }
?>
                  </select><br>
                  <select size="1" data-stripe="exp-year">
<?
$y = date('Y');
for ($i=$y; $i<=2021; $i++) {
?>
    <option value="<?= $i; ?>"><?= $i; ?></option>
<?
 }
?>
                  </select>
              </td>
              </tr>
          </table>
            <input type="submit" value="Submit Payment"/>
        </form>
      </div>
    </div>
  </div>
</div>