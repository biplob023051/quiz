$("#QuizAdminPreviewForm :input").prop("disabled", true);

$(document).on('click', '#show-settings', function () {
    $('.settings-options').toggle();
});

function close_window() {
	close();
}