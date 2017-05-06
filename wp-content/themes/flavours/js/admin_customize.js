// Order page download order CSV
jQuery(document).ready(function($) {
	//

	$("input[name=date_to]").bind("change", function() {
		changeCSVUrl('date_to', $(this).val());
	});

	$("input[name=date_from]").bind("change", function() {
		changeCSVUrl('date_from', $(this).val());
	});

	$('#filter-by-date').change(function(event){
		changeCSVUrl('m', this.value);
	});

	$('#posts-filter').submit(function(event) {
		$('[name^=post]:checkbox:checked').each(function(index, element) {
			$('#posts-filter').append("<input type='hidden' name='post[]' value='" + $(element).val() + "'/>");
		});
	});

	function changeCSVUrl(key, value) {
		var url = $('#downloadOrderCSV').attr('href');

		var urlArray = url.split('&');

		var exists = false;

		for(var i = 0; i < urlArray.length; i++) {
			var pair = urlArray[i].split('=');

			if (pair[0] == key) {
				exists = true;
				pair[1] = value;
			}

			urlArray[i] = pair.join('=');
		}

		if (!exists) {
			urlArray.push(key + '=' + value);
		}

		$('#downloadOrderCSV').attr('href', urlArray.join('&'));
	}


	// Just to be sure that the input will be called
	$("#order_csv_upload_button").on("click", function(){
		$('#order_csv_upload_input').click(function(event) {
			event.stopPropagation();
		});
	});

	$('#order_csv_upload_input').on('change', prepareUpload);

	function prepareUpload(event) {
		var file = event.target.files;
		var parent = $("#" + event.target.id).parent();
		var data = new FormData();
		data.append("action", "csv_order_file_upload");
		$.each(file, function(key, value)
		{
			data.append("csv_order_file_upload", value);
		});

		$.ajax({
			url: 'admin-ajax.php',
			type: 'POST',
			data: data,
			cache: false,
			dataType: 'json',
			processData: false, // Don't process the files
			contentType: false, // Set content type to false as jQuery will tell the server its a query string request
			success: function(data, textStatus, jqXHR) {
				if (data.response == 'SUCCESS'){
					alert('上傳成功');
					location.reload();
				}
			}

		});

	}
});
