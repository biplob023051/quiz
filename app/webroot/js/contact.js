(function ($) {
	$("#contactForm").submit(function(event) {
		event.preventDefault();
		if ($('#email').val() == '') {
			$("#error-message").show();
			$("#error-message").html(lang_strings['empty_email']);
			return;
		}
		var verify = validateEmail($('#email').val());
		if (verify == false) {
			$("#error-message").show();
			$("#error-message").html(lang_strings['invalid_email']);
			return;
		}
		if ($('#message').val() == '') {
			$("#error-message").show();
			$("#error-message").html(lang_strings['empty_message']);
			return;
		}
		if(grecaptcha.getResponse() == '') {
			$("#error-message").show();
			$("#error-message").html(lang_strings['empty_captcha']);
			return;
		} 
		$('#contactForm').unbind('submit').submit();
	});

	function validateEmail(email) {
	    var re = /^([\w-]+(?:\.[\w-]+)*)@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$/i;
	    return re.test(email);
	}

})(jQuery);