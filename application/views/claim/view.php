<div class="accordion">
	<h3>Report Information</h3>
	<div>
		<div class="vehicle-data-table">
			<?php foreach($fields_we_care_about as $i => $label){ ?>	
				<?php if(empty($claim[$i])) continue; ?>
				<div class="row">
					<div class="l"><?php echo $label ?></div>
					<div class="r"><?php echo $claim[$i] ?></div>
				</div>
			<?php } ?>
		</div>
	</div>

	<?php if(!empty($home_data)){ ?>
		<h3>Home Information</h3>
		<div>
			<div class="vehicle-data-table">
				<?php foreach($home_fields_we_care_about as $i => $label){ ?>	
					<?php if(empty($home_data[$i])) continue; ?>
					<div class="row">
						<div class="l"><?php echo $label ?></div>
						<div class="r"><?php echo $home_data[$i] ?></div>
					</div>
				<?php } ?>
			</div>
		</div>
	<?php } ?>

	<?php if(!empty($photo_coordinates)){ ?>
		<h3 id="map-title">Map</h3>
		<div>
			<div class="spacer20"></div>
			<b>Photo Location</b>
			<div class="spacer20"></div>
			<iframe id="gmap" width="425" height="350" frameborder="0" scrolling="no" marginheight="0" marginwidth="0" src="https://maps.google.com/maps?t=m&amp;q=loc:<?php echo $photo_coordinates['lat'] ?>+<?php echo $photo_coordinates['lon'] ?>&amp;ie=UTF8&amp;z=12&amp;ll=<?php echo $photo_coordinates['lat'] ?>,<?php echo $photo_coordinates['lon'] ?>&amp;output=embed"></iframe>
			<div class="spacer40"></div>
		</div>
	<?php } ?>

	<?php if(!empty($photos)){ ?>
		<h3 id="photo-title">Photos</h3>
		<div>
			<div class="slick">
				<?php foreach($photos as $p){ ?>
					<div>
						<img class="full" src="<?php echo $p['url'] ?>"/>
					</div>
				<?php } ?>
			</div>
		</div>
	<?php } ?>

	<?php if($claim['replacement_cost'] > 0){ ?>
		<h3 id="photo-title">Replacement Cost Estimate</h3>
		<div>
				<div class="spacer20"></div>
				<b>$<?php echo $claim['replacement_cost'] ?></b>
				<div class="spacer20"></div>
		</div>
	<?php } ?>
</div>

<script>
var map_good = false;
$(function(){
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
			$('#replacement-cost').val().replace(/[^0-9\.]/,'')
		).toFixed(2);
		if(window.confirm('Confirm: $'+cost))
		{
			show_loading();
			$.post(
				"/estimator/set_estimate/<?php echo $claim['hash'] ?>",
				{ cost: cost },
				function(data){
					hide_loading();
					if(data.success)
						window.location = '/estimator/dashboard';
					else
						alert('Error: '+data.error);
				},
				'json'
			).error(function(data,msg){
				alert('Error: '+msg);
				hide_loading();
			});
		}
	});
});

</script>