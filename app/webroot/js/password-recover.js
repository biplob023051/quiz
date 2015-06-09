(function($) {

	var appData = $.parseJSON($("#app-data").text());
   	$('[data-toggle="tooltip"]').tooltip();
   	$("#UserPasswordRecoverForm").submit(function(event) {
		event.preventDefault();
		if ($("#UserEmail").val() == '') {
			$("#error-message").show();
			$("#error-message").html(lang_strings['empty_email']);
			return;
		}

		var verify = validateEmail($('#UserEmail').val());
		if (verify == false) {
			$("#error-message").show();
			$("#error-message").html(lang_strings['invalid_email']);
			return;
		}

		verify = validateEmailExistance($('#UserEmail').val());
		
		if (verify == false) {
			$("#error-message").show();
			$("#error-message").html(lang_strings['not_found_email']);
			return;
		}

		$('#UserPasswordRecoverForm').unbind('submit').submit();

	});

	$("#UserResetPasswordForm").submit(function(event) {
		event.preventDefault();
		if ($("#UserPassword").val() == '') {
			$("#error-message").show();
			$("#error-message").html(lang_strings['empty_password']);
			return;
		}

		if ($("#UserPassword").val().length < 8) {
			$("#error-message").show();
			$("#error-message").html(lang_strings['character_count']);
			return;
		}

		if ($("#UserPasswordVerify").val() == '') {
			$("#error-message").show();
			$("#error-message").html(lang_strings['varify_password']);
			return;
		}

		if ($("#UserPassword").val() != $("#UserPasswordVerify").val()) {
			$("#error-message").show();
			$("#error-message").html(lang_strings['varify_password']);
			return;
		}

		$('#UserResetPasswordForm').unbind('submit').submit();

	});

	function validateEmail(email) {
	    var re = /^([\w-]+(?:\.[\w-]+)*)@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$/i;
	    return re.test(email);
	}

	function validateEmailExistance(email) {
		var result;
	    $.ajax({
            dataType: 'json',
            url: appData.baseUrl + 'user/ajax_email_checking',
            type: 'post',
            async: false,
            data: {'email': email},
            success: function (response)
            {
                if (response.success || response.success === "true")
                {
                	result = true;
                } else {
                	result = false;
                }
            }
        });
	    return result;
	}

})(jQuery);