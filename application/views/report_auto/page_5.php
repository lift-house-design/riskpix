sample photos and tips
<div class="spacer10"></div>
<?/*
<div class="w300 pull-center pad4 visible">
	<ul class="tip-slider">
		<li><img src="/assets/img/capture_it_all.jpg"  title="Capture all of the damage"/></li>
		<li><img src="/assets/img/keep_it_clean.jpg" title="Clean off dust and dirt"/></li>
		<li><img src="/assets/img/no_glare.jpg" title="Avoid glare and lens flare"/></li>
		<li><img src="/assets/img/no_night_photos.jpg" title="Make sure there is proper lighting"/></li>
	</ul>
</div>*/?>
<form method="post">
	<input type="submit" name="next" value="Take Photos"/>
</form>

<script>
$(function(){
	$('.tip-slider').bxSlider({
		auto: true,
  		captions: true,
  		pager: true
	});
});
</script>