<div class="instructions">
	<div class="spacer30"></div>
	<div class="text-block align-center w300 pull-center visible">
		<b>Photo <?php echo $photo_page ?> of <?php echo $photo_count ?></b>
		<div class="spacer5"></div>
		<?php echo $photo_message ?>
	</div>
	<div class="spacer30"></div>
</div>
<div class="w300 pull-center visible">
	<input type="file" name="image" accept="image/*;capture=camera" capture style="position:absolute;top:4px;left:20px;height:10px;width:10px"/>
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
	var photo_exif = {};
	$(function(){
		$('.take-photo, .retake-photo').click(function(){
			$('input[type="file"]').click();
		});
		$('input[type="file"]').change(function(e){
			show_loading();
			var file = e.target.files[0];
		    canvasResize(file, {
				width: 800,
				height: 800,
				quality: 100,
				callback: function(data, width, height) {
					if(!window.photo_exif)
						window.photo_exif = {};
					window.photo_data = data;
					get_coordinates();				
				}
		    });
		});
	});

	function submit_base64()
	{
		set_supported_tech('binary_transfer', 'unsupported');
		$.ajax({
		    type: 'POST',
		    url: $('form.retake').attr('action'),
		    data: {
		    	exif: JSON.stringify(window.photo_exif),
		    	photo: window.photo_data 
		    },
		    dataType: 'json'
		}).success(function(data) {
		    if(data.error)
		    {
		    	alert(data.error);
		    	location.reload();
		    }
		    else
		    	window.location = data.success;
		}).error(function(data,msg){
			alert('error: '+msg);
		    location.reload();
		});
	}

	function submit_form()
	{
		if(is_supported_tech('binary_transfer') === 'unsupported')
		{
			submit_base64();
			return;
		}
		/* try binary upload, fall back to base64 */
		try{
			var form = new FormData();
			var blob = b64toBlob(
					window.photo_data.substr(
						window.photo_data.indexOf(',')+1
					)
				);

			form.append('exif', JSON.stringify(window.photo_exif));
			form.append('photo', blob);

			$.ajax({
			    type: 'POST',
			    url: $('form.retake').attr('action'),
			    data: form,
			    processData: false,
			    contentType: false,
			    dataType: 'json'
			})
			.success(function(data) {
			    if(data.error)
			    {
					submit_base64();
			    	//alert(data.error);
			    	//location.reload();
			    }
			    else{
					set_supported_tech('binary_transfer', 'supported');
			    	window.location = data.success;
			    }
			})
			.error(function(){
				submit_base64();
			});
		}catch(e){
			submit_base64();
		}
	}

	function b64toBlob(b64Data, contentType, sliceSize) {
	    contentType = contentType || '';
	    sliceSize = sliceSize || 512;
	    var blob;

	    var byteCharacters = atob(b64Data);

        var len = byteCharacters.length;
        var buffer = new ArrayBuffer(len);
        var view = new Uint8Array(buffer);
        for ( var i = 0; i < len; i++) {
        	view[i] = byteCharacters.charCodeAt(i);
        }
        blob = new Blob([ view ]);
        if(!blob || !blob.size)
        	throw "blob failure";
        /* these were some fall backs that still didn't work on the galaxy s2
        try
        {
        	blob = new Blob([ view ]);
       	}
       	catch(e)
       	{
       		return view.buffer;
       		window.BlobBuilder = window.BlobBuilder ||
                window.WebKitBlobBuilder ||
                window.MozBlobBuilder ||
                window.MSBlobBuilder;

            if (e.name == 'TypeError' && window.BlobBuilder) {
		        var bb = new BlobBuilder();
		        bb.append(buffer);
		        blob = bb.getBlob();
		    }
		    else if (e.name == "InvalidStateError") {
		        // InvalidStateError (tested on FF13 WinXP)
		        out = new Blob([data], {type: datatype});
		    }
		    else {
		        // We're screwed, blob constructor unsupported entirely   
		    }   
       	}
       	*/

	    /* this doesn't work on galaxy S2
	    var byteArrays = [];
	    alert('loading byte array');
	    for (var offset = 0; offset < byteCharacters.length; offset += sliceSize) {
	        var slice = byteCharacters.slice(offset, offset + sliceSize);

	        var byteNumbers = new Array(slice.length);
	        for (var i = 0; i < slice.length; i++) {
	            byteNumbers[i] = slice.charCodeAt(i);
	        }

	        var byteArray = new Uint8Array(byteNumbers);

	        byteArrays.push(byteArray);
	    }
	    alert('creating blob');
	    var blob = new Blob(byteArrays, {type: contentType});
	    alert('blob created');
	    */

	    /* blob bulider is not widely supported
	    // write the bytes of the string to an ArrayBuffer
	    alert('building byte array');
	    var ab = new ArrayBuffer(byteCharacters.length);
	    var ia = new Uint8Array(ab);
	    for (var i = 0; i < byteCharacters.length; i++) {
	        ia[i] = byteCharacters.charCodeAt(i);
	    }

	    // write the ArrayBuffer to a blob, and you're done
	    alert('building blob');
	    var bb = new BlobBuilder();
	    alert('appending array');
	    bb.append(ab);
	    alert('getting blob');
	    blob = bb.getBlob('');
	    alert('done');
		*/
	    return blob;
	}

	function get_coordinates()
	{
		if(photo_exif.GPSLatitude)
		{
			console.log('GPS location received from photo');
			submit_form();
		}
		else if (navigator.geolocation)
		{
			if(is_supported_tech('geolocation') === 'unsupported')
				submit_form();
			else
				navigator.geolocation.getCurrentPosition(gps_position, gps_error, {timeout:5000});
		}
		else
		{
			set_supported_tech('geolocation', 'unsupported');
			console.log('Device does not support geolocation');
			submit_form();
		}
	}

	function gps_position(position)
	{
		window.photo_exif.GPSLatitude = position.coords.latitude;
		window.photo_exif.GPSLongitude = position.coords.longitude;
		window.photo_exif.GPSTimeStamp = position.timestamp;
		set_supported_tech('geolocation', 'supported');
		submit_form();
	}

	function gps_error(error)
	{
		// http://www.w3schools.com/html/tryit.asp?filename=tryhtml5_geolocation_error
		set_supported_tech('geolocation', 'unsupported');
		switch(error.code) 
		{
			case error.PERMISSION_DENIED:
				console.log("User denied the request for Geolocation.");
				break;
			case error.POSITION_UNAVAILABLE:
				console.log("Geolocation information is unavailable.");
				break;
			case error.TIMEOUT:
				console.log("The request to get geolocation timed out.");
				break;
			case error.UNKNOWN_ERROR:
				console.log("An unknown Geolocation error occurred.");
				break;
		}
		submit_form();
	}
</script>