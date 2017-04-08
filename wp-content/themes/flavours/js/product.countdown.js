/**
 * Part of buyfood project.
 *
 * @copyright  Copyright (C) 2016 {ORGANIZATION}. All rights reserved.
 * @license    GNU General Public License version 2 or later.
 */

jQuery(document).ready(function($) {
    var now = Date.now();
    var startDate;
    var endDate;
    // in product page
	if ($('#meta_start_date').val() !== '' && $('#meta_end_date').val() !== '') {

        startDate = Date.parse($('#meta_start_date').val());
        endDate = Date.parse($('#meta_end_date').val());

	    if (now > startDate && now < endDate) {
            $('div#clock').countdown($('#meta_end_date').val())
                .on('update.countdown', function (event) {
                    $(this).html(event.strftime('截止時間： %I:%M:%S'));
                    $('.clock-large').html(event.strftime('%I:%M:%S'));
                })
                .on('finish.countdown', function (event) {
                    $(this).hide();
                    $('.add-to-box').hide();
                    $('#outofdate').show();
                });
        } else {
            $('.add-to-box').hide();
            $('#outofdate').show();
        }
	}

    $('div.clockdate-full').hide();
    $('div.clockdate-full-mobile').hide();

	if ($('#product_start_date').val() !== '' && $('#product_end_date').val() !== ''){

        startDate = Date.parse($('#product_start_date').val());
        endDate = Date.parse($('#product_end_date').val());
        if (now > startDate && now < endDate) {

            $('div.clockdate-full').css('display', '');
            $('div.clockdate-full-mobile').css('display', '');
            $('div.clock-large').countdown($('#product_end_date').val())
                .on('update.countdown', function (event) {
                    $(this).html(event.strftime('%I:%M:%S'));
                })
                .on('finish.countdown', function (event) {
                    $(this).hide();
                    $('div.clockdate-full').hide();
                });

            $('div.clock-large-mobile').countdown($('#product_end_date').val())
                .on('update.countdown', function (event) {
                    $(this).html(event.strftime('%I:%M:%S'));
                })
                .on('finish.countdown', function (event) {
                    $(this).hide();
                    $('div.clockdate-full-mobile').hide();
                });

            $('div#limit-block').show();
            $('div#bannerclock').countdown($('#product_end_date').val())
                .on('update.countdown', function (event) {
                    $(this).html(event.strftime('%I:%M:%S'));
                })
                .on('finish.countdown', function (event) {
                    $(this).hide();
                    $('div.clockdate-full').hide();
                    $('div.clockdate-full-mobile').hide();
                });
        }
	}
});
