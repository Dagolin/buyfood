/**
 * Part of buyfood project.
 *
 * @copyright  Copyright (C) 2016 {ORGANIZATION}. All rights reserved.
 * @license    GNU General Public License version 2 or later.
 */

jQuery(document).ready(function($) {

	$('#test').on('click', function(){

		// We'll pass this variable to the PHP function example_ajax_request
		var fruit = 'Banana';


		// This does the ajax request
		$.ajax({
			url: cert.ajax_url,
			data: {
				'action':'certi_check',
				'fruit' : fruit
			},
			success:function(data) {
				// This outputs the result of the ajax request
				console.log(data);
			},
			error: function(errorThrown){
				console.log(errorThrown);
			}
		});

		return false;
	});
});
