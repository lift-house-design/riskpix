<?php if($has_errors): ?>
    <div class="errors"><?php echo validation_errors() ?></div>
<?php endif; ?>
<div class="h100">
    <div class="auto">
        <div class="contact">
            <div class="center540">
                <h2 class="oleo c0">sign up - step 1 of 4</h2>
                <!--form id="contact-form" method="post">
                <input id="form-check" type="hidden" name="00<?= sha1(rand(0,time())) ?>" value=""/>
                <table border="0" cellpadding="5" cellspacing="0">
                <tr>
                <td class="align-right">Company Name:</td>
                <td><input type="text" name="company" placeholder="Company Name" value="<?= set_value('company'); ?>"/></td>
                </tr>
                <tr>
                <td class="align-right">First &amp; Last Name:</td>
                <td><input type="text" name="name" placeholder="Your Name" value="<?= set_value('name'); ?>"/></td>
                </tr>
                <tr>
                <td class="align-right">Enter your email address:</td>
                <td><input type="text" name="email" placeholder="Email" value="<?= set_value('email'); ?>"/></td>
                </tr>
                <tr>
                <td class="align-right">Re-enter your email:</td>
                <td><input type="text" name="email2" placeholder="Re-enter Email" value="<?= set_value('email2'); ?>"/></td>
                </tr>
                <tr>
                <td class="align-right">Eight character password:</td>
                <td><input type="password" name="password" placeholder="Password" value="<?= set_value('password'); ?>"/></td>
                </tr>
                <tr>
                <td class="align-right">Re-enter your password:</td>
                <td><input type="password" name="password2" placeholder="Re-enter Password" value="<?= set_value('password2'); ?>"/></td>
                </tr>
                </table>
                <input type="submit" value="Procede to Step 2"/>
                </form-->
                <?php echo form_open() ?>
                <div class="form-field">
                    <label for="field_company">Company Name</label>
                    <div class="field">
                        <?php echo form_input(array(
                            'id'=>'field_company',
                            'name'=>'company',
                        )) ?>
                    </div>
                </div>
                <?php echo form_close() ?>
            </div>
        </div>
    </div>
</div>
