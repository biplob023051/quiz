(function ($) {

    var appData = $.parseJSON($("#app-data").text());

    $('[data-toggle="tooltip"]').tooltip();

    $(document).on('click', 'button#buy-button', function () {
       $('#buy-modal').modal('show');
    });

    $("#UserDisplayForm").submit(function(event) {
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
        $('#UserDisplayForm').unbind('submit').submit();
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
    
})(jQuery);