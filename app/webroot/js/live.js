(function($) {

	var appData = $.parseJSON($("#app-data").text());

	function checkNetConnection(){
		jQuery.ajaxSetup({async:false});
		re="";
		r=Math.round(Math.random() * 10000);
		$.get(appData.baseUrl + 'img/dot.png',{subins:r},function(d){
		re=true;
		}).error(function(){
		re=false;
		});
		return re;
	} 
   	
   	$("#StudentLiveForm").submit(function(event) {
		event.preventDefault();
		var netConnection = checkNetConnection();
		if (netConnection == false) {
			alert(lang_strings[0]);
			return;
		}
		$("#error-message").hide();
		var validateStdError = checkValidation();
		if (validateStdError == true) {
			$('html, body').animate({
		        scrollTop: $(".page-header").offset().top
		    }, 500);
		} else {
			// check alpha numeric last name, first name and class
			validateStdError = checkAlphaNumeric();
			if (validateStdError == true) {
				$('html, body').animate({
			        scrollTop: $(".page-header").offset().top
			    }, 500);
			} else {
				var infoModal = $('#confirm-submit');
				var numbers = [];
				var i = 0;
				$("#questions").find('.form-group :input').each(function(){
					i++;
		            if ($(this).val() == '') {
		            	// $(this).closest('tr').attr('id').match(/\d+/)
		            	var j = $(this).closest('tr').attr('id').match(/\d+/);
	            		numbers.push(j);
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
						   numbers.push(j);
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
						   numbers.push(j);
						}
					});
			    }

		        if (numbers.length > 0) {
		        	// array sorting
		        	numbers.sort(function(a,b) {
					  if (isNaN(a) || isNaN(b)) {
					    return a > b ? 1 : -1;
					  }
					  return a - b;
					});
					// array implode by delimiter ','
		        	var str = numbers.join();
		        	str = lang_strings[2] + str + lang_strings[3] + lang_strings[4];
		        } else {
		        	var str = lang_strings[1];
		        } 
		        infoModal.find('.modal-body').html(str);
		        infoModal.modal('show');
		        
		        $(document).on('click', 'button#confirmed', function () {
		        	$('#StudentLiveForm').unbind('submit').submit();
		        });
			}

		}

	});

	function checkValidation() {

		if ($("#StudentLname").val() == '') {
			$("#error-message").show();
			$("#error-message").html(lang_strings[6]);
			return true;
		}

		if ($("#StudentFname").val() == '') {
			$("#error-message").show();
			$("#error-message").html(lang_strings[5]);
			return true;
		}

		if ($("#StudentClass").val() == '') {
			$("#error-message").show();
			$("#error-message").html(lang_strings[7]);
			return true;
		}
		return false;
	}

	function checkAlphaNumeric () {
	    var pattern = /[a-zA-Z0-9]+/;
	    if (pattern.test($("#StudentLname").val()) == false) {
	    	$("#error-message").show();
			$("#error-message").html(lang_strings['last_name_invalid']);
	        return true;
	    }

	    if (pattern.test($("#StudentFname").val()) == false) {
	    	$("#error-message").show();
			$("#error-message").html(lang_strings['first_name_invalid']);
	        return true;
	    }

	    if (pattern.test($("#StudentClass").val()) == false) {
	    	$("#error-message").show();
			$("#error-message").html(lang_strings['class_invalid']);
	        return true;
	    }
	    return false;
	}
	
    document.getElementById("StudentLiveForm").reset();
})(jQuery);