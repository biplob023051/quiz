(function($) { 
	$("#StudentLiveForm").submit(function(event) {
		var validateStdError = checkValidation();
		if (validateStdError == false) {
			event.preventDefault();
		}
	});

	function checkValidation() {
		if ($("#StudentFname").val() == '') {
			$("#error-message").show();
			$("#error-message").html('First Name is Required');
			return false;
		}

		if ($("#StudentLname").val() == '') {
			$("#error-message").show();
			$("#error-message").html('Last Name is Required');
			return false;
		}

		if ($("#StudentClass").val() == '') {
			$("#error-message").show();
			$("#error-message").html('Class is Required');
			return false;
		}
		return true;
	}
	
    document.getElementById("StudentLiveForm").reset();
})(jQuery);