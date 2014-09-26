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

	<h3>Replacement Cost Estimate</h3>
	<div>
		<? if($claim['status'] == 'Pending Estimate'){ ?>
			<div class="spacer20"></div>
			<input id="replacement-cost" placeholder="$123,456,789.00"/>
			<br/>
			<button id="replacement-cost-submit">Submit</button>
			<div class="spacer20"></div>
		<? }elseif($claim['replacement_cost'] > 0){ ?>
			<div class="spacer20"></div>
			<b>$<?= $claim['replacement_cost'] ?></b>
			<div class="spacer20"></div>
		<? }elseif($claim['status'] == 'Complete'){ ?>
			<div class="spacer20"></div>
			<b>No estimate required.</b>
			<div class="spacer20"></div>
		<? }else{ ?>
			<div class="spacer20"></div>
			<b>Waiting for the homeowner to complete their report.</b>
			<div class="spacer20"></div>
		<? } ?>
	</div>
</div>
<div id="replacement-confirm" title="Confirm Your Estimate">
  <p></p>
</div>

<style>
.ui-dialog-buttonset, .ui-dialog{ overflow:visible; }
.ui-button{ height:40px }
</style>

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
	$('#replacement-cost-submit').click(function(){
		var cost = parseFloat(
			$('#replacement-cost').val().replace(/[^0-9\.]/g,'')
		).toFixed(2);

		if(!cost || isNaN(cost)) return;

		$( "#replacement-confirm p" ).html('Confirm Amount: '+$('#replacement-cost').val());
		$( "#replacement-confirm" ).dialog({
			buttons: [
				{
					text: "Confirm", 
					click: function() { 
						replacement_cost_submit(cost);
					} 
				},
				{
					text: "Cancel", 
					click: function() { 
						$( this ).dialog( "close" ); 
					} 
				} 
			] 
		});
	});
});

function replacement_cost_submit(cost)
{
	show_loading();
	$.post(
		"/estimator/set_estimate/<?= $claim['hash'] ?>",
		{ cost: cost },
		function(data){
			hide_loading();
			if(data.success)
				//window.location = '/estimator/dashboard';
				window.location.reload();
			else
				alert('Error: '+data.error);
		},
		'json'
	).error(function(data,msg){
		alert('Error: '+msg);
		hide_loading();
	});
}
</script>