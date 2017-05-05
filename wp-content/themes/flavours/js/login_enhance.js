/**
 * Part of buyfood project.
 *
 * @copyright  Copyright (C) 2016 {ORGANIZATION}. All rights reserved.
 * @license    GNU General Public License version 2 or later.
 */

jQuery(document).ready(function($) {
	var reload_flag = true;
	setTimeout(function(){
		if (reload_flag) {
			if($('.theChampFacebookLogin').css('display') == 'none') {
				window.fbAsyncInit();
			}else {
				reload_flag = false;
			}

		}
	}, 5000);
});
