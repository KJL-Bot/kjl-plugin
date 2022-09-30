(function( $ ) {
	'use strict';

	/**
	 * All of the code for your public-facing JavaScript source
	 * should reside in this file.
	 *
	 * Note: It has been assumed you will write jQuery code here, so the
	 * $ function reference has been prepared for usage within the scope
	 * of this function.
	 *
	 * This enables you to define handlers, for when the DOM is ready:
	 *
	 * $(function() {
	 *
	 * });
	 *
	 * When the window is loaded:
	 *
	 * $( window ).load(function() {
	 *
	 * });
	 *
	 * ...and/or other possibilities.
	 *
	 * Ideally, it is not considered best practise to attach more than a
	 * single DOM-ready or window-load handler for a particular page.
	 * Although scripts in the WordPress core, Plugins and Themes may be
	 * practising this, we should strive to set a better example in our own work.
	 */
	 var outputtext = document.getElementById('status');
 
	 $(document).ready(function() {

		function clearAllSortingInputs()
		{
			$('.books-filter').find('input:hidden').val('');
		}
		$('#toggleswitch_djlp').val($(this).is(':checked'));
		$('#toggleswitch_kimi').val($(this).is(':checked'));

		$('#filter_author').on('click', function() {
			clearAllSortingInputs();
			var hiddenField = $('#author_input'),
				val = hiddenField.val();
		
			hiddenField.val(val === "on" ? "off" : "on");
		});

		$('#filter_publisher').on('click', function() {
			clearAllSortingInputs();
			var hiddenField = $('#publisher_input'),
				val = hiddenField.val();
		
			hiddenField.val(val === "on" ? "off" : "on");
		});

		$('#filter_title').on('click', function() {
			clearAllSortingInputs();
			var hiddenField = $('#title_input'),
				val = hiddenField.val();
		
			hiddenField.val(val === "on" ? "off" : "on");
		});

		$('#filter_location').on('click', function() {
			clearAllSortingInputs();
			var hiddenField = $('#location_input'),
				val = hiddenField.val();
		
			hiddenField.val(val === "on" ? "off" : "on");
		});

		$('#filter_date').on('click', function() {
			clearAllSortingInputs();
			var hiddenField = $('#date_input'),
				val = hiddenField.val();
		
			hiddenField.val(val === "on" ? "off" : "on");
		});


		$('#toggleswitch_djlp').on('click',function(){
			var hiddenField = $('#toggleswitch_djlp_input'),
			val = hiddenField.val();
	
			hiddenField.val(val === "on" ? "off" : "on");
			if(this.checked) {
				$(this).prop('value', 'on');
			} else {
				$(this).prop('value', 'off');
			}
			$(this).parents('form:first').submit();
		});

		$('#toggleswitch_kimi').on('click',function(){
			var hiddenField = $('#toggleswitch_kimi_input'),
			val = hiddenField.val();
	
			hiddenField.val(val === "on" ? "off" : "on");
			if(this.checked) {
				$(this).prop('value', 'on');
			} else {
				$(this).prop('value', 'off');
			}
			$(this).parents('form:first').submit();
		});
	});

})( jQuery );
