<h3>Verify your VIN</h3>
Locate your VIN on the driver's side of the car<br/>
where the dashboard meets the windshield <br/>
or the edge of the driver's door, or door frame.
<div class="pad10">
	<div class="text-block align-center pad0">
		<img src="/assets/img/vinnumber.png" alt="VIN location"/>
	</div>
</div>
<div class="sample-vin visible">
	<div class="visible">
		<span class="border iblock"></span>
	</div>
	<div class="visible">
		<span class="title iblock">
			sample VIN
		</span>
	</div>
	<div class="spacer10"></div>
	<div class="visible">
		<span class="content w300 iblock">
			5GZCZ43D13S812715
		</span>
	</div>
</div>
<div class="spacer20"></div>
<b class="f12 serif">
Please note: VIN never includes the letters I or O.<br/>
Please use the numbers 1 or 0 instead.
</b>
<div class="spacer20"></div>
<form method="post">
	<input type="text" name="VIN" value="<?php echo $claim['vin'] ?>" placeholder="Manually enter your VIN" value="<?php echo $claim['vin'] ?>"/><br/>
	<button id="check-VIN" type="button">Verify</button>
</form>

<script>
	$('#check-VIN').click(function(){
		vin = $.trim($('[name="VIN"]').val());
		/*if(!vin)
			return;

		if(vin.match(/[^a-zA-Z0-9]/))
		{
			alert('VIN should only contain letters and numbers.');
			return;
		}
		*/
		$('.overlay-bg').fadeIn(400);
		$('.overlay-circle').show(400);
					window.location = '/report/4';
		$.get(
			'/report/check_vin/'+encodeURIComponent($('[name="VIN"]').val()),
			{},
			function(data)
			{
				//if(!data.error)
					window.location = '/report/4';
				
				setTimeout(function(){
		//			if(confirm(data.error+ ' Please confirm that this VIN is correct: '+$('[name="VIN"]').val()))
						window.location = '/report/4';
					$('.overlay-bg').fadeOut(400);
					$('.overlay-circle').hide(400);
				}, 400);
			},
			'json'
		);
	});
</script>