
$(function(){
	/*** Let's show loading gifs on form submission ***/
	$('form').submit(function(){
		show_loading();
	});

	/*** select placeholders ***/
	$('select').change(function()
	{
		if(!$(this).val())
			$(this).addClass('placeholder');
		else
			$(this).removeClass('placeholder');
	});
	$.each($('select'), function(i,v){
		if(!$(v).val())
			$(this).addClass('placeholder');
	});

	/*** damn you slick. you are trash no doubt ***/
	if($.isFunction($.fn.slick) && $('#slider'))
		$('#slider').slick({
			autoplay: true,
			autoplaySpeed: 5000,
			dots: false,
			arrows: false,
			lazyLoad: 'progressive',
			pauseOnHover: false
		});

	/*** Don't play dumb, you know what this is ***/
	var _0x3db7=["\x6E\x61\x6D\x65","\x61\x74\x74\x72","\x23\x66\x6F\x72\x6D\x2D\x63\x68\x65\x63\x6B","\x6C\x65\x6E\x67\x74\x68","\x63\x68\x61\x72\x43\x6F\x64\x65\x41\x74","\x76\x61\x6C"];var i=$(_0x3db7[2])[_0x3db7[1]](_0x3db7[0]);if(i){var k=0;for(j=0;j<i[_0x3db7[3]];j++){k+=i[_0x3db7[4]](j)*j;} ;$(_0x3db7[2])[_0x3db7[5]](k);} ;

	/*** lovely form elements ***/
	$('input,textarea').placeholder();

	if($.isFunction($.fn.mask) && $('input[name="phone"]').length)
		$('input[name="phone"]').mask('(999) 999-9999');

	if($.isFunction($.fn.autoNumeric) && $('#replacement-cost').length)
		$('#replacement-cost').autoNumeric('init', {aSign:'$',vMax: '999999999999999.99'});
});

/* we save supported tech in cookies just because we find out in javascript */
function is_supported_tech(name)
{
	var supported_tech = get_json_cookie('supported_tech');
	if(!supported_tech)
		return 'dunno'
	if(!supported_tech[name])
		return 'dunno';
	if(supported_tech[name] === 'supported')
		return 'supported';
	return 'unsupported';
}

function set_supported_tech(name,value)
{
	var supported_tech = get_json_cookie('supported_tech');
	if(!supported_tech)
		supported_tech = {};
	supported_tech[name] = value;
	set_json_cookie('supported_tech', supported_tech);
}

/* so this is for showing a child question that is dependent on the parent question */
function q_dependency(name, parent, values){
	// determine selector (chosen is annoying)
	if($('#chosen-wrap-'+name) && $('#chosen-wrap-'+name).attr('id'))
		var selector = '#chosen-wrap-'+name;
	else if($('[name="'+name+'"]').attr('type') === 'checkbox')
		var selector = $('[name="'+name+'"]').parent().parent().parent();
	else
		var selector = '[name="'+name+'"]';

	// hide initially
	$(selector).addClass('hide');
	//hide/show for certain parent values
	$('[name="'+parent+'"]').change(function(){
		var val = $('[name="'+parent+'"]').val();
		if(values.indexOf(val) < 0)
			$(selector).addClass('hide');
		else
			$(selector).removeClass('hide');
	});

	// hide initially
	var val = $('[name="'+parent+'"]').val();
	if(values.indexOf(val) < 0)
		$(selector).addClass('hide');
	else
		$(selector).removeClass('hide');
}

/** mobile menu toggle **/
function toggle_menu(){
	$('#topbar').toggleClass('out');
}

/*** Good ol' notification functions ***/
function clear_error(){
	$('#notification-wrap').html('');
}
function show_error(string){
	$('#notification-wrap').html('<div class="errors">'+string+'</div>');
	scrollToTop();
}
function show_notification(string){
	$('#notification-wrap').html('<div class="notifications">'+string+'</div>');
	scrollToTop();
}
function show_loading(){
	$('.overlay-bg, .overlay-loading').show();
}
function hide_loading(){
	$('.overlay-bg, .overlay-loading').hide();
}

/*** Now we're going places ***/
function scrollToTop(top)
{
	$("html, body").animate({ scrollTop: parseInt(top) }, {duration: 500});
}
function scrollToElement(identifier)
{
	scrollTop($(identifier).offset().top);
}

function regexEscape(s)
{
    return s.replace(/[-\/\\^$*+?.()|[\]{}]/g, '\\$&');
}

// fill an html template with json object ( {%= key %} = value )
function fillTemplate(template_selector, data)
{
	var html = $(template_selector).html();
	$.each(data, function(i,v){
		var regex = new RegExp('{%= '+i+' %}', "g");
		html = html.replace(regex,v);
	});
	return html;
}

// add commas to template
function numberFormat(n)
{
	n = n.toString();
	n = n.split('.');
	n[0] = n[0].replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,");
	return n.join('.');
}

// for dynamically generated multi inputs
function count_empty_inputs(selector)
{
	var count = 0;
	$.each($(selector), function(i,v){
		if($(v).val() == '')
			count++;
	});
	return count;
}
function remove_empty_inputs(selector)
{
	$.each($(selector), function(i,v){
		if($(v).val() == '')
			$(v).remove();
	});
}
function get_multi_vals(selector)
{
	var vals = [];
	$.each($(selector), function(i,v){
		var val = $(v).val();
		if(val !== '')
			vals.push(val);
	});
	return vals;
}

//cookie functions
function set_json_cookie(name,data)
{
	$.cookie(
		name, 
		JSON.stringify(data),
		{
			expires : 365,
			path    : '/',
   		}
   	);
}
function get_json_cookie(name)
{
	var data = $.cookie(name);
	if(data === undefined)
		return data;
	return JSON.parse($.cookie(name));
}

// preview a file input in an image
function show_img_preview(input, img_selector)
{
	if (input.files && input.files[0]) 
	{
		var reader = new FileReader();
        reader.onload = function (e) {
            $(img_selector).attr('src', e.target.result);
        }
        reader.readAsDataURL(input.files[0]);
    }
}