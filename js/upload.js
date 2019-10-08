var uploadFile = function () {

	Metronic.blockUI();

	var form_data = new FormData();
	var ins = document.getElementById('multiFiles').files.length;
	for (var x = 0; x < ins; x++) {
		form_data.append("files[]", document.getElementById('multiFiles').files[x]);
	}
	$.ajax({
		url: 'upload.php', // point to server-side PHP script 
		dataType: 'text', // what to expect back from the PHP script
		cache: false,
		contentType: false,
		processData: false,
		data: form_data,
		type: 'post',
		success: function (response) {
			$('#multiFiles').val('');
			Metronic.unblockUI();
			alert(response); // display success response from the PHP script
		},
		error: function (response) {
			Metronic.unblockUI();
			$('#msg').html(response); // display error response from the PHP script
		}
	});
	
}