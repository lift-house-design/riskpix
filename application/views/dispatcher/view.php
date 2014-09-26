<div class="accordion">

	<h3>Report Information</h3>
	<div>
		<div class="vehicle-data-table">
			<? foreach($fields_we_care_about as $i => $label){ ?>	
				<? if(empty($claim[$i])) continue; ?>
				<div class="row">
					<div class="l"><?= $label ?></div>
					<div class="r"><?= $claim[$i] ?></div>
				</div>
			<? } ?>
		</div>
	</div>

	<h3>Home Information</h3>
	<div>
		<div class="vehicle-data-table">
			<? foreach($fields_we_care_about as $i => $label){ ?>	
				<? if(empty($claim[$i])) continue; ?>
				<div class="row">
					<div class="l"><?= $label ?></div>
					<div class="r"><?= $claim[$i] ?></div>
				</div>
			<? } ?>
			<? if(!empty($home_data)){ ?>
				<? foreach($home_fields_we_care_about as $i => $label){ ?>	
					<? if(empty($home_data[$i])) continue; ?>
					<div class="row">
						<div class="l"><?= $label ?></div>
						<div class="r"><?= $home_data[$i] ?></div>
					</div>
				<? } ?>
			<? } ?>
		</div>
	</div>

	<? if(!empty($photo_coordinates)){ ?>
		<h3 id="map-title">Map</h3>
		<div>
			<div class="spacer20"></div>
			<b>Photo Location</b>
			<div class="spacer20"></div>
			<iframe id="gmap" width="425" height="350" frameborder="0" scrolling="no" marginheight="0" marginwidth="0" src="https://maps.google.com/maps?t=m&amp;q=loc:<?= $photo_coordinates['lat'] ?>+<?= $photo_coordinates['lon'] ?>&amp;ie=UTF8&amp;z=12&amp;ll=<?= $photo_coordinates['lat'] ?>,<?= $photo_coordinates['lon'] ?>&amp;output=embed"></iframe>
			<div class="spacer40"></div>
		</div>
	<? } ?>

	<? if(!empty($photos)){ ?>
		<h3 id="photo-title">Photos</h3>
		<div>
			<div class="slick">
				<? foreach($photos as $p){ ?>
					<div>
						<img class="full" src="<?= $p['url'] ?>"/>
					</div>
				<? } ?>
			</div>
		</div>
	<? } ?>

	<h3>Replacement Cost Estimator</h3>
	<div>
		<? if($claim['estimator'] > 0){ ?>
			<div class="spacer20"></div>
			<b><?= $estimator_data['name'] ?><?= ($claim['replacement_cost'] > 0 ? ' - $'.$claim['replacement_cost'] : '') ?></b>
			<div class="spacer20"></div>
		<? }else/*if($claim['status'] == 'Pending Dispatch')*/{ ?>
			<div class="spacer20"></div>
			<? if(!empty($estimators)){ ?>
				<table class="pull-center estimator-radio w300">
					<? foreach($estimators as $e){ ?>
						<tr>
							<td>
								<input type="checkbox" name="estimator" value="<?= $e['id'] ?>"/>
							</td>
							<td class="lato f17">
								<?= $e['estimator_data']['name'] ?>
							</td>
						</tr>
					<? } ?>
				</table>
			<? } ?>
			<div class="spacer20"></div>
			<button class="estimator">Assign Estimator</button>
			<div class="spacer20"></div>
		<? }/*else{ ?>
			<div class="spacer20"></div>
			<b>No Replacement Cost Required</b>
			<div class="spacer20"></div>
		<? }*/ ?>
	</div>
</div>

<script>
$(function(){
	var map_good = false;
	$('.accordion').accordion({ heightStyle: "content", collapsible: true });
	$('.slick').slick({
		autoplay: false,
		dots: true,
		infinite: true,
		lazyLoad: 'progressive'
	});
	$('#photo-title').click(function(){
		if($('#photo-title').hasClass('ui-accordion-header-active'))
			setTimeout(function(){$('.slick').slickGoTo(1);},200);
	});
	$('#map-title').click(function(){
		if(map_good)
			return
		if($('#map-title').hasClass('ui-accordion-header-active'))
			setTimeout(function(){$('#gmap').attr('src',$('#gmap').attr('src'));},000);
		map_good = true;
	});
	$('button.estimator').click(function(e)
	{
		var estimator = $('[name="estimator"]:checked').val();
		if(!estimator)
			return;
		$.post(
			"/dispatcher/set_estimator/<?= $claim['hash'] ?>/",
			{estimator: estimator},
			function(data)
			{
				if(!data.success)
					return;
				$('[name="estimator"]').attr('disabled','disabled');
				$('button.estimator').attr('disabled','disabled');
				$('button.estimator').html('Estimator Set!');
				window.location.reload();
				//$('button.email').html('Email sent to '+data.success);
			},
			'json'
		);
	});
});

</script>