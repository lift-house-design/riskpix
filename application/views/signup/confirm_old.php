<script type="text/javascript" src="https://js.stripe.com/v2/"></script>
<script type="text/javascript">
  // This identifies your website in the createToken call below
  Stripe.setPublishableKey('<?php if ($is_test_transaction == true) { ?><?php echo $test_stripe_public_key; ?><?php } else { ?><?php echo $stripe_public_key; ?><?php } ?>');

    var stripeResponseHandler = function(status, response) {
      var $form = $('#payment-form');

      if (response.error) {
        // Show the errors on the form
        $form.find('.payment-errors').text(response.error.message);
        $form.find('button').prop('disabled', false);
      } else {
        // token contains id, last4, and card type
        var token = response.id;
        // Insert the token into the form so it gets submitted to the server
        $form.append($('<input type="hidden" name="stripeToken" />').val(token));
        // and re-submit
        $form.get(0).submit();
      }
    };

    jQuery(function($) {
      $('#payment-form').submit(function(e) {
        var $form = $(this);

        // Disable the submit button to prevent repeated clicks
        $form.find('button').prop('disabled', true);

        Stripe.card.createToken($form, stripeResponseHandler);

        // Prevent the form from submitting with the default action
        return false;
      });
    });

  // ...
</script>
<?php if($has_errors): ?>
  <div class="errors"><?php echo validation_errors() ?></div>
<?php endif; ?>
<div class="h100">
  <div class="auto">
    <div class="contact">
      <div class="center540">
        <h2 class="oleo c0">sign up - step 4</h2>
        <form id="payment-form" method="post">
          <input id="form-check" type="hidden" name="00<?php echo sha1(rand(0,time())) ?>" value=""/>
          <span class="payment-errors"></span>
          <table border="0" cellpadding="5" cellspacing="0">
              <tr>
                <td class="align-right">Card Number:</td>
                <td><input type="text" size="20" data-stripe="number"<?php if ($is_test_transaction == true) { ?> value="<?php echo $test_card_number; ?>"<?php } ?>/></td>
              </tr>
              <tr>
                <td class="align-right">CVC Code:</td>
                <td><input type="text" size="4" data-stripe="cvc"<?php if ($is_test_transaction == true) { ?> value="<?php echo $test_cvc_code; ?>"<?php } ?>/></td>
              </tr>
              <tr>
                <td class="align-right">Expiration Date:</td>
                <td>
                  <select size="1" data-stripe="exp-month">
<?php $m = date('n');
for ($i=1; $i<=12; $i++) {
  $monthname = date("F", mktime(0, 0, 0, $i, 10));
?>
    <option value="<?php echo $i; ?>"<?php if ($m == $i) { ?> selected<?php } ?>><?php echo $monthname; ?></option>
<?php  }
?>
                  </select><br>
                  <select size="1" data-stripe="exp-year">
<?php $y = date('Y');
for ($i=$y; $i<=2021; $i++) {
?>
    <option value="<?php echo $i; ?>"><?php echo $i; ?></option>
<?php  }
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
