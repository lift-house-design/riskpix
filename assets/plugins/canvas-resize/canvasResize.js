/*
 * 
 * canvasResize
 * 
 * Version: 1.2.0 
 * Date (d/m/y): 02/10/12
 * Update (d/m/y): 14/05/13
 * Original author: @gokercebeci 
 * Licensed under the MIT license
 * - This plugin working with binaryajax.js and exif.js 
 *   (It's under the MPL License http://www.nihilogic.dk/licenses/mpl-license.txt)
 * Demo: http://canvasResize.gokercebeci.com/
 * 
 * - I fixed iOS6 Safari's image file rendering issue for large size image (over mega-pixel)
 *   using few functions from https://github.com/stomita/ios-imagefile-megapixel
 *   (detectSubsampling, )
 *   And fixed orientation issue by using https://github.com/jseidelin/exif-js
 *   Thanks, Shinichi Tomita and Jacob Seidelin
 */

(function($) {
    var pluginName = 'canvasResize',
            methods = {
        newsize: function(w, h, W, H, C) {
            var c = C ? 'h' : '';
            if ((W && w > W) || (H && h > H)) {
                var r = w / h;
                if ((r >= 1 || H === 0) && W && !C) {
                    w = W;
                    h = (W / r) >> 0;
                } else if (C && r <= (W / H)) {
                    w = W;
                    h = (W / r) >> 0;
                    c = 'w';
                } else {
                    w = (H * r) >> 0;
                    h = H;
                }
            }
            return {
                'width': w,
                'height': h,
                'cropped': c
            };
        },
        dataURLtoBlob: function(data) {
            var mimeString = data.split(',')[0].split(':')[1].split(';')[0];
            var byteString = atob(data.split(',')[1]);
            var ab = new ArrayBuffer(byteString.length);
            var ia = new Uint8Array(ab);
            for (var i = 0; i < byteString.length; i++) {
                ia[i] = byteString.charCodeAt(i);
            }
            var bb = (window.BlobBuilder || window.WebKitBlobBuilder || window.MozBlobBuilder);
            if (bb) {
                //    console.log('BlobBuilder');        
                bb = new (window.BlobBuilder || window.WebKitBlobBuilder || window.MozBlobBuilder)();
                bb.append(ab);
                return bb.getBlob(mimeString);
            } else {
                //    console.log('Blob');  
                bb = new Blob([ab], {
                    'type': (mimeString)
                });
                return bb;
            }
        },
        /**
         * Detect subsampling in loaded image.
         * In iOS, larger images than 2M pixels may be subsampled in rendering.
         */
        detectSubsampling: function(img) {
            var iw = img.width, ih = img.height;
            //if (iw * ih > 1048576) { // subsampling may happen over megapixel image
            if (iw * ih > 262144) { // subsampling may happen over megapixel image
                var canvas = document.createElement('canvas');
                canvas.width = canvas.height = 1;
                var ctx = canvas.getContext('2d');
                ctx.drawImage(img, -iw + 1, 0);
                // subsampled image becomes half smaller in rendering size.
                // check alpha channel value to confirm image is covering edge pixel or not.
                // if alpha value is 0 image is not covering, hence subsampled.
                return ctx.getImageData(0, 0, 1, 1).data[3] === 0;
            } else {
                return false;
            }
        },
        /**
         * Update the orientation according to the specified rotation angle
         */
        rotate: function(orientation, angle) {
            var o = {
                // nothing
                1: {90: 6, 180: 3, 270: 8},
                // horizontal flip
                2: {90: 7, 180: 4, 270: 5},
                // 180 rotate left
                3: {90: 8, 180: 1, 270: 6},
                // vertical flip
                4: {90: 5, 180: 2, 270: 7},
                // vertical flip + 90 rotate right
                5: {90: 2, 180: 7, 270: 4},
                // 90 rotate right
                6: {90: 3, 180: 8, 270: 1},
                // horizontal flip + 90 rotate right
                7: {90: 4, 180: 5, 270: 2},
                // 90 rotate left
                8: {90: 1, 180: 6, 270: 3}
            };
            return o[orientation][angle] ? o[orientation][angle] : orientation;
        },
        /**
         * Transform canvas coordination according to specified frame size and orientation
         * Orientation value is from EXIF tag
         */
        transformCoordinate: function(canvas, width, height, orientation) {
            switch (orientation) {
                case 5:
                case 6:
                case 7:
                case 8:
                    canvas.width = height;
                    canvas.height = width;
                    break;
                default:
                    canvas.width = width;
                    canvas.height = height;
            }
            var ctx = canvas.getContext('2d');
            switch (orientation) {
                case 1:
                    // nothing
                    break;
                case 2:
                    // horizontal flip
                    ctx.translate(width, 0);
                    ctx.scale(-1, 1);
                    break;
                case 3:
                    // 180 rotate left
                    ctx.translate(width, height);
                    ctx.rotate(Math.PI);
                    break;
                case 4:
                    // vertical flip
                    ctx.translate(0, height);
                    ctx.scale(1, -1);
                    break;
                case 5:
                    // vertical flip + 90 rotate right
                    ctx.rotate(0.5 * Math.PI);
                    ctx.scale(1, -1);
                    break;
                case 6:
                    // 90 rotate right
                    ctx.rotate(0.5 * Math.PI);
                    ctx.translate(0, -height);
                    break;
                case 7:
                    // horizontal flip + 90 rotate right
                    ctx.rotate(0.5 * Math.PI);
                    ctx.translate(width, -height);
                    ctx.scale(-1, 1);
                    break;
                case 8:
                    // 90 rotate left
                    ctx.rotate(-0.5 * Math.PI);
                    ctx.translate(-width, 0);
                    break;
                default:
                    break;
            }
        },
        /**
         * Detecting vertical squash in loaded image.
         * Fixes a bug which squash image vertically while drawing into canvas for some images.
         */
        detectVerticalSquash: function(img) {
            var iw = img.naturalWidth, ih = img.naturalHeight;
            var canvas = document.createElement('canvas');
            canvas.width = 1;
            canvas.height = ih;
            var ctx = canvas.getContext('2d');
            ctx.drawImage(img, 0, 0);
            var data = ctx.getImageData(0, 0, 1, ih).data;
            // search image edge pixel position in case it is squashed vertically.
            var sy = 0;
            var ey = ih;
            var py = ih;
            while (py > sy) {
                var alpha = data[(py - 1) * 4 + 3];
                if (alpha === 0) {
                    ey = py;
                } else {
                    sy = py;
                }
                py = (ey + sy) >> 1;
            }
            var ratio = (py / ih);
            return (ratio===0)?1:ratio;
        },
        callback: function(d) {
            return d;
        },
        extend: function() {
            var target = arguments[0] || {}, a = 1, al = arguments.length, deep = false;
            if (target.constructor === Boolean) {
                deep = target;
                target = arguments[1] || {};
            }
            if (al === 1) {
                target = this;
                a = 0;
            }
            var prop;
            for (; a < al; a++)
                if ((prop = arguments[a]) !== null)
                    for (var i in prop) {
                        if (target === prop[i])
                            continue;
                        if (deep && typeof prop[i] === 'object' && target[i])
                            methods.extend(target[i], prop[i]);
                        else if (prop[i] !== undefined)
                            target[i] = prop[i];
                    }
            return target;
        },
        resample_hermite: function(canvas, W, H, W2, H2){
            //var time1 = Date.now();
            var img = canvas.getContext("2d").getImageData(0, 0, W, H);
            var img2 = canvas.getContext("2d").getImageData(0, 0, W2, H2);
            var data = img.data;
            var data2 = img2.data;
            var ratio_w = W / W2;
            var ratio_h = H / H2;
            var ratio_w_half = Math.ceil(ratio_w/2);
            var ratio_h_half = Math.ceil(ratio_h/2);
            
            for(var j = 0; j < H2; j++){
                for(var i = 0; i < W2; i++){
                    var x2 = (i + j*W2) * 4;
                    var weight = 0;
                    var weights = 0;
                    var weights_alpha = 0;
                    var gx_r = gx_g = gx_b = gx_a = 0;
                    var center_y = (j + 0.5) * ratio_h;
                    for(var yy = Math.floor(j * ratio_h); yy < (j + 1) * ratio_h; yy++){
                        var dy = Math.abs(center_y - (yy + 0.5)) / ratio_h_half;
                        var center_x = (i + 0.5) * ratio_w;
                        var w0 = dy*dy //pre-calc part of w
                        for(var xx = Math.floor(i * ratio_w); xx < (i + 1) * ratio_w; xx++){
                            var dx = Math.abs(center_x - (xx + 0.5)) / ratio_w_half;
                            var w = Math.sqrt(w0 + dx*dx);
                            if(w >= -1 && w <= 1){
                                //hermite filter
                                weight = 2 * w*w*w - 3*w*w + 1;
                                if(weight > 0){
                                    dx = 4*(xx + yy*W);
                                    //alpha
                                    gx_a += weight * data[dx + 3];
                                    weights_alpha += weight;
                                    //colors
                                    if(data[dx + 3] < 255)
                                        weight = weight * data[dx + 3] / 250;
                                    gx_r += weight * data[dx];
                                    gx_g += weight * data[dx + 1];
                                    gx_b += weight * data[dx + 2];
                                    weights += weight;
                                    }
                                }
                            }       
                        }
                    data2[x2]     = gx_r / weights;
                    data2[x2 + 1] = gx_g / weights;
                    data2[x2 + 2] = gx_b / weights;
                    data2[x2 + 3] = gx_a / weights_alpha;
                    }
                }
            //console.log("hermite = "+(Math.round(Date.now() - time1)/1000)+" s");
            
            //canvas.getContext("2d").clearRect(0, 0, Math.max(W, W2), Math.max(H, H2));
            /* garbage
            canvas.width = W;
            canvas.height = H;
            canvas.getContext("2d").clearRect(0, 0, W, H);
            canvas.getContext("2d").putImageData(img2, 0, 0);
            */
            canvas.width = W2;
            canvas.height = H2;
            canvas.getContext("2d").clearRect(0, 0, W2, H2);
            canvas.getContext("2d").putImageData(img2, 0, 0);
            
        }
    },
    defaults = {
        width: 300,
        height: 0,
        crop: false,
        quality: 80,
        rotate: 0,
        steps: 1,
        'callback': methods.callback
    };
    function Plugin(file, options) {
        this.file = file;
        // EXTEND
        this.options = methods.extend({}, defaults, options);
        this._defaults = defaults;
        this._name = pluginName;
        this.init();
    }
    Plugin.prototype = {
        init: function() {
            var $this = this;
            var file_type = this.file.type;

            var reader = new FileReader();
            reader.onloadend = function(e) 
            {
                delete this.file;
                delete $this.file;
                reader = null;
                
                // find position of EXIF tag
                var head = e.target.result.substr(0,100);
                var exif_start = head.indexOf('RXhpZg');
                var data_start = head.indexOf(',')+1;
                

                if(exif_start > 17)
                {
                    photo_exif = e.target.result.substr(data_start, 4000+data_start);
                    try{
                        // convert the first 4000 dataURL chars to binary for exif parsing
                        photo_exif = atob(photo_exif);
                        photo_exif = EXIF.readFromBinaryFile(
                            new BinaryFile(
                                photo_exif
                            )
                        );
                    }catch(e){
                        console.log("error parsing exif..");
                        console.log(e+': '+photo_exif);
                        photo_exif = {};

                    }
                }
                else
                    // no exif data
                    photo_exif = {};

                delete data_start;
                delete exif_start;

                var orientation = photo_exif['Orientation'] || 1;
                var img = new Image();

                img.onload = function(e) {

                    //orientation = methods.rotate(orientation, $this.options.rotate);
                    /*
                    var size = (orientation >= 5 && orientation <= 8)
                            ? methods.newsize(img.height, img.width, $this.options.width, $this.options.height, $this.options.crop)
                            : methods.newsize(img.width, img.height, $this.options.width, $this.options.height, $this.options.crop);
                    */
                    var size = methods.newsize(img.width, img.height, $this.options.width, $this.options.height, false);
                    //var width = size.width, height = size.height;
                    var canvas = document.createElement("canvas");
                   
                    /* no resampling */
                    //canvas.width = size.width;
                    //canvas.height = size.height;
                    //if(orientation >= 5 && orientation <= 8)
                    //{
                    //    canvas.width = size.height;
                    //    canvas.height = size.width;
                    //}
                    methods.transformCoordinate(canvas, size.width, size.height, orientation);
                    /*alert("image size: "+img.width+"x"+img.height);
                    alert("new size: "+size.width+"x"+size.height);
                    alert("canvas size: "+canvas.width+"x"+canvas.height);
                    */

                    // detect vertical squash
                    var vertSquashRatio = methods.detectVerticalSquash(img);

                    var ctx = canvas.getContext("2d");
//                    alert('squash: '+vertSquashRatio+' - '+img.width+'x'+img.height+' - '+canvas.width+'x'+canvas.height);
                    if(orientation >= 5 && orientation <= 8)
                    //    ctx.drawImage(img, 0, 0, canvas.height * vertSquashRatio, canvas.width * vertSquashRatio);
                        ctx.drawImage(img, 0, 0, canvas.height, canvas.width / vertSquashRatio);//, 0, 0, canvas.width, canvas.height / vertSquashRatio);
                    //    ctx.drawImage(img, 0, 0, canvas.height, canvas.width);
                    else
                    //    ctx.drawImage(img, 0, 0, canvas.width * vertSquashRatio, canvas.height * vertSquashRatio);
                        ctx.drawImage(img, 0, 0, canvas.width, canvas.height / vertSquashRatio);
                    //    ctx.drawImage(img, 0, 0, canvas.width, canvas.height);

                    /* resampling */
                    /*
                    canvas.width = img.width;
                    canvas.height = img.height;
                    methods.transformCoordinate(canvas, img.width, img.height, orientation);
                    var ctx = canvas.getContext("2d");
                    ctx.drawImage(img, 0, 0, img.width, img.height);
                    methods.resample_hermite(canvas, canvas.width, canvas.height, width, height);
                    */
              
                    /* multiple resampling (apparently sucks idk why)
                    console.log($this.options.steps);
                    var w1 = img.width;
                    var h1 = img.height;
                    var ratio = w1/h1;
                    var w_increment = (img.width - width) / $this.options.steps;
                    var h_increment = (img.height - height) / $this.options.steps;
                    var w2 = w1 - w_increment;
                    var h2 = h1 - h_increment; 
                    while(w2 >= width && h2 >= height)
                    {
                        // resample;
                        console.log(Math.round(w1)+'x'+Math.round(h1)+' - '+Math.round(w2)+'x'+Math.round(h2));
                        methods.resample_hermite(canvas, Math.round(w1), Math.round(h1), Math.round(w2), Math.round(h2));
                        w1 = w2;
                        h1 = h2;
                        w2 = w1 - w_increment;
                        h2 = h1 - h_increment; 
                    }
                    */

                    img = null;
                    ctx = null;
                    if (file_type === "image/png") {
                        $this.options.callback(
                            canvas.toDataURL(file_type), 
                            canvas.width, 
                            canvas.height
                        );
                        var dataURL = canvas.toDataURL(file_type);
                    } else {
                        $this.options.callback(
                            canvas.toDataURL("image/jpeg", ($this.options.quality * .01)),
                            canvas.width, 
                            canvas.height
                        );
                    }
                    canvas = null;
                };
                img.src = e.target.result;
                delete img;
            };
            reader.readAsDataURL(this.file);
            delete reader;
        }
    };
    $[pluginName] = function(file, options) {
        if (typeof file === 'string')
            return methods[file](options);
        else
            new Plugin(file, options);
    };

})(window);