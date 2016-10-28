/**
 * Part of buyfood project.
 *
 * @copyright  Copyright (C) 2016 {ORGANIZATION}. All rights reserved.
 * @license    GNU General Public License version 2 or later.
 */

jQuery(document).ready(function($) {

	$('#cert_button').on('click', function(){
		var phone = $('#billing_phone').val().replace(/[^0-9\.]+/g, '');

		if (phone == "") {
			alert('請輸入有效連絡電話，以收取認證簡訊');

			return false;
		}



		$.ajax({
			dataType: 'json',
			url: cert.ajax_url,
			data: {
				'action':'cert_check',
				'phone': phone
			},
			success:function(data) {
				// This outputs the result of the ajax request
				alert(data.message);
			},
			error: function(errorThrown){
				alert('系統繁忙中，請稍後。');
			}
		});

		return false;
	});
});
