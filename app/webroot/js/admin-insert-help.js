(function ($) {
	var appData = $.parseJSON($("#app-data").text());

	tinymce.init({selector:'textarea'});

	jQuery('#select-0').fineUploader({
		request: {
			endpoint: appData.baseUrl + 'upload/video/' + video_id
		},
		text: {
			uploadButton: lang_strings['upload_button']
		},
		validation: {
			allowedExtensions: ['jpg', 'jpeg', 'gif', 'png'],
			sizeLimit: 10 * 1024 * 1024
		},
		multiple: false
	}).on('complete', function(event, id, fileName, response) {
		$('#HelpPhoto').val(response.filename);
		$('#item-avatar').attr('src', response.avatar);
	});
})(jQuery);