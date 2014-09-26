// the semi-colon before function invocation is a safety net against concatenated
// scripts and/or other plugins which may not be closed properly.
;(function ( $, window, document, undefined ) {

	// undefined is used here as the undefined global variable in ECMAScript 3 is
	// mutable (ie. it can be changed by someone else). undefined isn't really being
	// passed in so we can ensure the value of it is truly undefined. In ES5, undefined
	// can no longer be modified.

	// window and document are passed through as local variable rather than global
	// as this (slightly) quickens the resolution process and can be more efficiently
	// minified (especially when both are regularly referenced in your plugin).

	// Create the defaults once
	var pluginName = "contentTable",
		defaults = {
			useDataTables: true,
			dataTableOptions: {
				bLengthChange: false,
				bInfo: false,
				bFilter: false,
				bPaginate: false,
			},
			contentClass: 'content-row',
			selectedClass: 'selected',
			url: undefined,
			allowMultiple: false,
			onComplete: undefined,
		};

	// The actual plugin constructor
	function Plugin( element, options ) {
		this.element = element;

		// jQuery has an extend method which merges the contents of two or
		// more objects, storing the result in the first object. The first object
		// is generally empty as we don't want to alter the default options for
		// future instances of the plugin
		this.options = $.extend( {}, defaults, options );

		this._defaults = defaults;
		this._name = pluginName;

		this.init();
	}

	Plugin.prototype = {

		init: function() {
			// Place initialization logic here
			// You already have access to the DOM element and
			// the options via the instance, e.g. this.element
			// and this.options
			// you can add more functions like the one below and
			// call them like so: this.yourOtherFunction(this.element, this.options).

			var plugin=this;

			// If enabled, init the dataTable
			if(this.options.useDataTables===true)
				$(this.element).dataTable(this.options.dataTableOptions);

			// Add event handler for viewing a project's invoice
			$(this.element)
				.on('click','> tbody > tr:not(.'+this.options.contentClass+')',function(e){
					plugin.openContent(this);
				});
		},

		openContent: function(row){

			// If the content is not already open
			if($(row).next('.'+this.options.contentClass).length==0)
			{
				if(!this.options.allowMultiple)
				{
					// Close all other open rows
					this.closeAllContent();
				}
				

				// Determine the number of cells being used in each row
				var cellCount=$(row)
					.children('td, th')
					.length;

				// Get needed options in scope
				var plugin=this;
				var contentClass=this.options.contentClass;
 				var selectedClass=this.options.selectedClass;
 				var onComplete=this.options.onComplete;

				// Create a row below the selected row containing the invoice content
				$('<td>')
					.attr('colspan',cellCount)
					.load(this.options.url,function(response){
						$('<tr>')
							.addClass(plugin.options.contentClass)
							.append(this)
							.insertAfter(row);

						$(row).addClass(plugin.options.selectedClass);

						// Callback onComplete if set
						if(typeof plugin.options.onComplete == 'function')
							plugin.options.onComplete.call(row,response,plugin);
					});
			}
			else
			{
				$(row)
					.removeClass('selected')
					.next('.'+this.options.contentClass)
					.remove();
			}
		},

		closeAllContent: function(){
			$(this.element)
				.find('.'+this.options.contentClass)
				.remove();
			$(this.element)
				.find('.'+this.options.selectedClass)
				.removeClass(this.options.selectedClass);
		}                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    
	};

	// A really lightweight plugin wrapper around the constructor,
	// preventing against multiple instantiations
	$.fn[pluginName] = function ( options ) {
		return this.each(function () {
			if (!$.data(this, "plugin_" + pluginName)) {
				$.data(this, "plugin_" + pluginName, new Plugin( this, options ));
			}
		});A                                                                                                     
	};

})( jQuery, window, document );