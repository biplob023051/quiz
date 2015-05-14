(function($) { 
   	
   	$("#StudentLiveForm").submit(function(event) {
		event.preventDefault();
		$("#error-message").hide();
		var validateStdError = checkValidation();
		if (validateStdError == true) {
			$('html, body').animate({
		        scrollTop: $(".page-header").offset().top
		    }, 500);
		} else {
			var infoModal = $('#confirm-submit');
			var str = '';
			var i = 0;
			$("#questions").find('.form-group :input').each(function(){
				i++;
	            if ($(this).val() == '') {
	            	// $(this).closest('tr').attr('id').match(/\d+/)
	            	var j = $(this).closest('tr').attr('id').match(/\d+/);
            		str += j + ',';
	            }          
	        });

	        var radioArray = [];
			$("#questions").find('.radio :input').each(function(){
				if(jQuery.inArray($(this).attr('name'), radioArray)!==-1) {

				} else {
					radioArray.push($(this).attr('name'));
				}        
		    });
		    if (radioArray.length > 0) {
		    	$.each( radioArray, function( key, value ) {
				  	if (!$("input[name='"+value+"']:checked").val()) {
					   var j = $("input[name='"+value+"']").closest('tr').attr('id').match(/\d+/);
					   str += j + ',';
					}
				});
		    }

		    var checkboxArray = [];
			$("#questions").find('.checkbox :input').each(function(){
				if(jQuery.inArray($(this).attr('name'), checkboxArray)!==-1) {

				} else {
					checkboxArray.push($(this).attr('name'));
				}        
		    });
		    if (checkboxArray.length > 0) {
		    	$.each( checkboxArray, function( key, value ) {
				  	if (!$("input[name='"+value+"']:checked").val()) {
					   var j = $("input[name='"+value+"']").closest('tr').attr('id').match(/\d+/);
					   str += j + ',';
					}
				});
		    }

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