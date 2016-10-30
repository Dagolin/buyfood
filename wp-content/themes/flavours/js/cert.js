/**
 * Part of buyfood project.
 *
 * @copyright  Copyright (C) 2016 {ORGANIZATION}. All rights reserved.
 * @license    GNU General Public License version 2 or later.
 */

jQuery(document).ready(function($) {
	$('#billing_cert').val('');
	$('#billing_phone_hidden').val($('#billing_phone').val());

	if ($('#billing_phone').val() != '') {
		skipCert();
	} else {
		showCert();
	}

	$('#billing_phone').on('change', function(obj){
		if ($(this).val() != $('#billing_phone_hidden').val()) {
			showCert();
		} else {
			skipCert();
		}
	});

	$('#cert_button').on('click', function(){
		var phone = $('#billing_phone').val().replace(/[^0-9\.]+/g, '');

		if (phone.length != 10) {
			alert('請輸入有效連絡電話(十位數, 09xxxxxxxx)，收取認證簡訊');

			return false;
		}

		$.ajax({
			dataType: 'json',
			url: cert.ajax_url,
			data: {
				'action':'cert_check',
				'phone': phone,
				'skip': $('#billing_skipcert').val()
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

function skipCert(){
	$('#billing_cert_field').hide();
	$('#billing_cert_button').hide();
	$('#billing_skipcert').val('true');
}

function showCert(){
	$('#billing_cert_field').show();
	$('#billing_cert_button').show();
	$('#billing_skipcert').val('false');
}