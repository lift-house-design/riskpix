<div id="topbar">
	<a href="javascript:toggle_menu()">
		<span>&#9776; <b>Menu</b></span>
		<f class="mainlogo"><b>RISK</b>PIX</f>
	</a>
<?php if(!$logged_in){ ?>
	<a href="/authentication/log_in">
		Log In
	</a>
<?php } ?>

	<?php if(strlen($_SERVER['REQUEST_URI']) > 1){ ?>
		<a href="/" class="dark">
			Home
		</a>
	<?php } ?>

	<?php if(!$logged_in){ ?>
		<a href="/signup" class="dark">
			Sign Up
		</a>
		<a href="/about" class="dark">
			About Us
		</a>
		<a href="/news" class="dark">
			News &amp; Events
		</a>
		<a href="/contact" class="dark">
			Contact Us
		</a>
	<?php }else{ ?>
		<?php if(array_intersect($user['roles'], array('agent','administrator'))){ ?>
			<a href="/claim" class="dark">
				Order Report
			</a>
			<a href="/claim/dashboard" class="dark">
				Report Dashboard
			</a>
		<?php } ?>
		<?php if(array_intersect($user['roles'], array('estimator','administrator'))){ ?>
			<a href="/estimator" class="dark">
				Estimate Dashboard
			</a>
		<?php } ?>
		<?php if(array_intersect($user['roles'], array('dispatcher','administrator'))){ ?>
			<a href="/dispatcher" class="dark">
				Dispatch Dashboard
			</a>
		<?php } ?>
		<a href="/contact" class="dark">
			Contact Us
		</a>
		<a href="/authentication/log_out" class="dark">
			Log Out
		</a>
	<?php } ?>

</div>
<div class="spacer60"></div>
