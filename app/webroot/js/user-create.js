(function ($) {

	var appData = $.parseJSON($("#app-data").text());

	$('[data-toggle="tooltip"]').tooltip();

	$("#UserCreateForm").submit(function(event) {
        event.preventDefault();
        // not empty name
        if ($('#UserName').val() == '') {
            $("#error-message").show();
            $("#error-message").html(lang_strings['empty_name']);
            return;
        }
        // check characters validation
        var verify = validateName($('#UserName').val());
        if (verify == false) {
            $("#error-message").show();
            $("#error-message").html(lang_strings['invalid_characters']);
            return;
        }

        // not empty email
        if ($('#UserEmail').val() == '') {
            $("#error-message").show();
            $("#error-message").html(lang_strings['empty_email']);
            return;
        }
        // validate email address
        verify = validateEmail($('#UserEmail').val());
        if (verify == false) {
            $("#error-message").show();
            $("#error-message").html(lang_strings['invalid_email']);
            return;
        }
        
        // password verify
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

        // check if empty captcha
        if ($("#UserCaptcha").val() == '') {
            $("#error-message").show();
            $("#error-message").html(lang_strings['empty_captcha']);
            return;
        }

        // check If email registered
        verify = validateEmailExistance($('#UserEmail').val());
        if (verify == false) {
            $("#error-message").show();
            $("#error-message").html(lang_strings['unique_email']);
            $("#email-exist").show();
            return;
        }
        $("#email-exist").hide();

        $("#error-message").hide();
        $('#UserCreateForm').unbind('submit').submit();
    });

	function validateEmail(email) {
	    var re = /^([\w-]+(?:\.[\w-]+)*)@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$/i;
	    return re.test(email);
	}

	function validateName(name) {
	    var re = /[a-zA-Z0-9]+/;
	    return re.test(name);
	}

	function validateEmailExistance(email) {
	    var result;
	    $.ajax({
	        dataType: 'json',
	        url: appData.baseUrl + 'user/ajax_user_checking',
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

    // modal for youtube video
    $(document).on('click', 'a#play_video', function (e) {
        var src = 'https://www.youtube.com/embed/' + url_src + '?autoplay=1';
        $('#video-modal iframe').attr('src', src);
        $('#video-modal').modal('show');
    });

    $(document).on('click', 'button#close', function (e) {
        $('#video-modal iframe').removeAttr('src');
        $('#video-modal').modal('hide');
    });

    $('#video-modal').on('hidden.bs.modal', function (e) {
        $('#video-modal iframe').removeAttr('src');
    });

})(jQuery);
