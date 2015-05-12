function checkRow(row) {
    var found = false;
    row.find('input.update-score').each(function(){
        if($(this).attr('value') === undefined)
            found = true;
            return;
    });
    
    if(found) {
        row.addClass('warning');
    } else {
        row.removeClass('warning');
    }
}

(function ($) {

    $('#overview').show();
    $('#details').hide();

    $.fn.extend({
        donetyping: function(callback,timeout){
            timeout = timeout || 2e3; // 1 second default timeout
            var timeoutReference,
                doneTyping = function(el){
                    if (!timeoutReference) return;
                    timeoutReference = null;
                    callback.call(el);
                };
            return this.each(function(i,el){
                var $el = $(el);
                // Chrome Fix (Use keyup over keypress to detect backspace)
                // thank you @palerdot
                $el.is(':input') && $el.on('keyup keypress',function(e){
                    // This catches the backspace button in chrome, but also prevents
                    // the event from triggering too premptively. Without this line,
                    // using tab/shift+tab will make the focused element fire the callback.
                    if (e.type=='keyup' && e.keyCode!=8) return;
                    
                    // Check if timeout has been set. If it has, "reset" the clock and
                    // start over again.
                    if (timeoutReference) clearTimeout(timeoutReference);
                    timeoutReference = setTimeout(function(){
                        // if we made it here, our timeout has elapsed. Fire the
                        // callback
                        doneTyping(el);
                    }, timeout);
                }).on('blur',function(){
                    // If we can, fire the event since we're leaving the field
                    doneTyping(el);
                });
            });
        }
    });

    $('#details input').donetyping(function(){
        var marks = parseInt($(this).val());
        var max = parseInt($(this).attr("max"));
        var current_score = parseInt($(this).attr("current-score")); 
        $("#ajax-message").hide();

        if (marks < 0) {
            $("#ajax-message").removeClass('alert-success');
            $("#ajax-message").addClass('alert-danger');
            $("#ajax-message").show();
            $("#ajax-message").html('Please Give a postive number!');
            $('html, body').animate({
                scrollTop: $(".page-header").offset().top
            }, 500);
            return false;
        } else if (marks == current_score) {
            $("#ajax-message").removeClass('alert-success');
            $("#ajax-message").addClass('alert-danger');
            $("#ajax-message").show();
            $("#ajax-message").html('You have not updated score yet!');
            $('html, body').animate({
                scrollTop: $(".page-header").offset().top
            }, 500);
            return false;
        } else if (marks > max) {
            $("#ajax-message").removeClass('alert-success');
            $("#ajax-message").addClass('alert-danger');
            $("#ajax-message").show();
            $("#ajax-message").html('Points not allowed more than ' + max + ' value');
            $('html, body').animate({
                scrollTop: $(".page-header").offset().top
            }, 500);
            return false;
        } 

        $(this).attr("current-score", marks);

        var std_id = parseInt($(this).attr("name"));
        var q_id = parseInt($(this).attr("question"));


        $.ajax({
            dataType: 'json',
            url: appData.baseUrl + '/score/update',
            type: 'post',
            data: {'id': q_id, 'student_id': std_id, 'score': marks, 'current_score' : current_score, 'max' : max},
            success: function (response)
            {
                console.log(response);
                if (response.success || response.success === "true")
                {
                    $("#studentscr2-" + std_id).text(response.score);
                    $("#studentscr1-" + std_id).text(response.score);
                    $("#ajax-message").removeClass('alert-danger');
                    $("#ajax-message").addClass('alert-success');
                    $("#ajax-message").show();
                    $("#ajax-message").html('Points updated successfully');
                } else {
                    alert('Something went wrong, try again later');
                }
            }
        });
    });

    
    // update essay score
    // $('#details input').blur(function() {
    //     var marks = $(this).val();
    //     var max = $(this).attr("max");
    //     var current_score = $(this).attr("current-score"); 
    //     $("#ajax-message").hide();
        
    //     if (marks < 0) {
    //         $("#ajax-message").removeClass('alert-success');
    //         $("#ajax-message").addClass('alert-danger');
    //         $("#ajax-message").show();
    //         $("#ajax-message").html('Please Give a postive number!');
    //         $('html, body').animate({
    //             scrollTop: $(".page-header").offset().top
    //         }, 500);
    //         return false;
    //     } else if (marks == current_score) {
    //         $("#ajax-message").removeClass('alert-success');
    //         $("#ajax-message").addClass('alert-danger');
    //         $("#ajax-message").show();
    //         $("#ajax-message").html('You have not updated score yet!');
    //         $('html, body').animate({
    //             scrollTop: $(".page-header").offset().top
    //         }, 500);
    //         return false;
    //     } else if (marks > max) {
    //         $("#ajax-message").removeClass('alert-success');
    //         $("#ajax-message").addClass('alert-danger');
    //         $("#ajax-message").show();
    //         $("#ajax-message").html('Points not allowed more than ' + max + ' value');
    //         $('html, body').animate({
    //             scrollTop: $(".page-header").offset().top
    //         }, 500);
    //         return false;
    //     } 

    //     $(this).attr("current-score", marks);

    //     var std_id = $(this).attr("name");
    //     var q_id = $(this).attr("question");


    //     $.ajax({
    //         dataType: 'json',
    //         url: appData.baseUrl + '/score/update',
    //         type: 'post',
    //         data: {'id': q_id, 'student_id': std_id, 'score': marks, 'current_score' : current_score, 'max' : max},
    //         success: function (response)
    //         {
    //             console.log(response);
    //             if (response.success || response.success === "true")
    //             {
    //                 $("#studentscr2-" + std_id).text(response.score);
    //                 $("#studentscr1-" + std_id).text(response.score);
    //                 $("#ajax-message").removeClass('alert-danger');
    //                 $("#ajax-message").addClass('alert-success');
    //                 $("#ajax-message").show();
    //                 $("#ajax-message").html('Points updated successfully');
    //             } else {
    //                 alert('Something went wrong, try again later');
    //             }
    //         }
    //     });
    // });

    var appData = $.parseJSON($("#app-data").text());

    $(document).on('click', '#answer-table-overview', function () {
        $("#ajax-message").hide();
        // tab information insert into cookie to keep tracking
        setCookie("tabInfo", "answer-table-overview", 1);
        if($(this).hasClass('active'))
            return;
        $(this).addClass('active');
        $('#answer-table-show').removeClass('active');
        $('#overview').show();
        $('#details').hide();
    });

    $(document).on('click', '#answer-table-show', function () {
        $("#ajax-message").hide();
        // tab information insert into cookie to keep tracking
        setCookie("tabInfo", "answer-table-show", 1);
        if($(this).hasClass('active'))
            return;
        $(this).addClass('active');
        $('#answer-table-overview').removeClass('active');
        $('#overview').hide();
        $('#details').show();
    });

    $(document).on('change', '#answer-table-filter select', function () {
        $('form#answer-table-filter').submit();
    });

    $("#answer-table table").find('tr').each(function(){
        checkRow($(this));
    });
    
    // get tab information
    var currentTab = getCookie("tabInfo");
    if (currentTab == 'answer-table-overview') {
        $('#answer-table-overview').trigger('click');
    } else {
        $('#answer-table-show').trigger('click');
    }

    // essay pop up modal
    $(document).on('click', 'button.read-essay', function () {
        $(this).next().modal('show');
    });
    
})(jQuery);


// javascript cookie functions
function setCookie(cname,cvalue,exdays) {
    var d = new Date();
    d.setTime(d.getTime() + (exdays*24*60*60*1000));
    var expires = "expires=" + d.toGMTString();
    document.cookie = cname+"="+cvalue+"; "+expires;
}

function getCookie(cname) {
    var name = cname + "=";
    var ca = document.cookie.split(';');
    for(var i=0; i<ca.length; i++) {
        var c = ca[i];
        while (c.charAt(0)==' ') c = c.substring(1);
        if (c.indexOf(name) == 0) {
            return c.substring(name.length, c.length);
        }
    }
    return "";
}

function checkCookie() {
    var user=getCookie("username");
    if (user != "") {
        alert("Welcome again " + user);
    } else {
       user = prompt("Please enter your name:","");
       if (user != "" && user != null) {
           setCookie("username", user, 30);
       }
    }
}
