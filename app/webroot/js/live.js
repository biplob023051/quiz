(function($) { 
	$("#StudentLiveForm").submit(function(event) {
		event.preventDefault();
		var validateStdError = checkValidation();
		if (validateStdError == true) {
			$('html, body').animate({
		        scrollTop: $(".page-header").offset().top
		    }, 500);
		} else {
			var infoModal = $('#confirm-submit');
			var str = '';
			$("#questions").find('.form-group :input').each(function(){
	            if ($(this).val() == '') {
	            	str += $(this).closest('tr').attr('id').match(/\d+/) + ',';
	            }          
	        });

	        if (str.length > 0) {
	        	str = str.slice(0,-1);
	        	str = 'Questions ' + str + ' unanswered. Turn in your quiz?';
	        	
	        } else {
	        	str = 'All questions answered. Turn in your quiz?';
	        } 
	        infoModal.find('.modal-body').html(str);
	        infoModal.modal('show');
	        
	        $(document).on('click', 'button#confirmed', function () {
	        	$('#StudentLiveForm').unbind('submit').submit();
	        });
		}

	});

	function checkValidation() {
		if ($("#StudentFname").val() == '') {
			$("#error-message").show();
			$("#error-message").html('First Name is Required');
			return true;
		}

		if ($("#StudentLname").val() == '') {
			$("#error-message").show();
			$("#error-message").html('Last Name is Required');
			return true;
		}

		if ($("#StudentClass").val() == '') {
			$("#error-message").show();
			$("#error-message").html('Class is Required');
			return true;
		}
		return false;
	}
	
    document.getElementById("StudentLiveForm").reset();
})(jQuery);