/**
 * Part of buyfood project.
 *
 * @copyright  Copyright (C) 2016 {ORGANIZATION}. All rights reserved.
 * @license    GNU General Public License version 2 or later.
 */

jQuery(document).ready(function($) {
	if ($('#twzipcode').twzipcode == undefined) {
		return;
	}

	// zipcode
	$('#twzipcode').twzipcode({
		'countyName'   : 'billing_state_temp',   // 預設值為 county
		'districtName' : 'billing_city_temp', // 預設值為 district
		'zipcodeName'  : 'billing_postcode_temp'   // 預設值為 zipcode
	});

	$('#twzipcode2').twzipcode({
		'countyName'   : 'shipping_state_temp',   // 預設值為 county
		'districtName' : 'shipping_city_temp', // 預設值為 district
		'zipcodeName'  : 'shipping_postcode_temp'   // 預設值為 zipcode
	});

	var oldstate = $('#billing_state_field > #billing_state').val();
	$('#billing_state_field > #billing_state').replaceWith($('select[name=billing_state_temp]'));
	$('select[name=billing_state_temp]').attr('name', 'billing_state');

	if (oldstate != null && oldstate != '') {
		$('select[name=billing_state]').val(oldstate);
	}

	var oldcity = $('#billing_city_field > #billing_city').val();
	$('#billing_city_field > #billing_city').replaceWith($('select[name=billing_city_temp]'));
	$('select[name=billing_city_temp]').attr('name', 'billing_city');

	if (oldcity != null && oldcity != '') {
		$('select[name=billing_city]').val(oldcity);
	}

	$('#billing_postcode_field > #billing_postcode').replaceWith($('input[name=billing_postcode_temp]'));
	$('input[name=billing_postcode_temp]').attr('name', 'billing_postcode');

	$('#shipping_city_field > #shipping_city').replaceWith($('select[name=shipping_city_temp]'));
	$('select[name=shipping_city_temp]').attr('name', 'shipping_city');

	$('#shipping_state_field > #shipping_state').replaceWith($('select[name=shipping_state_temp]'));
	$('select[name=shipping_state_temp]').attr('name', 'shipping_state');

	$('#shipping_postcode_field > #shipping_postcode').replaceWith($('input[name=shipping_postcode_temp]'));
	$('input[name=shipping_postcode_temp]').attr('name', 'shipping_postcode');

	$('#billing_cert').val('');
	$('#billing_phone_hidden').val($('#billing_phone').val());

	if ($('#billing_phone').val() != '') {
		skipCert();
	} else {
		showCert();
	}

	$('#billing_phone').on('change', function(obj){
		if ($(this).val() != $('#billing_phone_hidden').val() || $(this).val().replace(/\s/g,'') == '') {
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