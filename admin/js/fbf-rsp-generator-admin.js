(function( $ ) {
	'use strict';

	/**
	 * All of the code for your admin-facing JavaScript source
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


	$(document).ready(function(){
		$('.fbf-rsp-generator-delete-rule-form').submit(function(e){
			if(!confirm('Are you sure you want to delete this rule')){
				return false;
			}
		});

		// Return a helper with preserved width of cells
		var fixHelper = function(e, ui) {
			ui.children().each(function() {
				$(this).width($(this).width());
			});
			return ui;
		};

		$('#fbf_rsp_generator_price_match').bind('change', function(){
			console.log('price match change');
			console.log($(this).is(':checked'));
			if($(this).is(':checked')){
				$('#fbf_rsp_generator_rule_amount').prop('disabled', true);
				$('#fbf_rsp_generator_rule_amount_pound').prop('disabled', true);
				$('#fbf_rsp_generator_rule_amount').val('');
				$('#fbf_rsp_generator_price_match_addition_row').show();
			}else{
				$('#fbf_rsp_generator_rule_amount').prop('disabled', false);
				$('#fbf_rsp_generator_rule_amount_pound').prop('disabled', false);
				$('#fbf_rsp_generator_price_match_addition_row').hide();
			}
			return false;
		});

		$('#fbf-rsp-generator-rule-table tbody').sortable({
			helper: fixHelper,
			update: function(event, ui) {
				var sorted = $(this).sortable('toArray', { attribute: 'data-id' });
				var data = {
					action: 'sort_rule_rows',
					sorted: sorted,
				}

				console.log(ajax_object);

				$.ajax({
					data: data,
					url: ajax_object.ajax_url,
					type: 'POST',
					success: function(response){
						console.log(response);
					}
				})
			}
		}).disableSelection();
	});

})( jQuery );
