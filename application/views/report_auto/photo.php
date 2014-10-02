<div class="instructions">
	<div class="spacer30"></div>
	<div class="text-block align-center w300 pull-center visible">
		<b>Photo <?php echo $photo_page ?> of 6</b>
		<div class="spacer5"></div>
		<?php echo $photo_message ?>
	</div>
	<div class="spacer30"></div>
</div>
<div class="w300 pull-center">
	<input type="file" name="image" accept="image/*" capture style="position:absolute;top:4px;left:20px;height:10px;width:10px"/>
	<input type="button" class="take-photo" value="Take Photo"/>
	<form method="post" action="<?php echo $action ?>" class="retake hide">
		<div class="w300 pull-center">
			<input type="hidden" name="photo"/>
			<input type="hidden" name="exif"/>
			<div class="w50pc">
				<input type="button" class="retake-photo" value="Retake Photo"/>
			</div>
			<div class="w50pc">
				<input type="submit" class="use-photo" value="Use Photo"/>
			</div>
		</div>
	</form>
</div>

<div class="spacer10"></div>
<?php /*img class="preview hide w100pc" src="data:image/gif;base64,R0lGODlhAQABAAAAACH5BAEKAAEALAAAAAABAAEAAAICTAEAOw==" alt="preview"/*/?>

<script>
	var photo_exif;
	$(function(){
		$('.take-photo, .retake-photo').click(function(){
			$('input[type="file"]').click();
		});
		$('input[type="file"]').change(function(e){
			show_loading();
			var file = e.target.files[0];
		    canvasResize(file, {
				width: 1000,
				height: 1000,
				crop: false,
				quality: 80, // lets try 80..
				//rotate: 90,
				callback: function(data, width, height) {
					/*
					$('img.preview').attr('src', data).show();
					$('.take-photo, .instructions').hide();
					$('.retake').show();
					*/
					$('input[name="exif"]').val(JSON.stringify(photo_exif));

					$('input[name="photo"]').val(data);
					$('form.retake').submit();
					//hide_loading();
				}
		    });
		});
	});
</script>
