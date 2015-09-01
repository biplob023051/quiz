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

$(document).ready(function() 
    { 
        $(".table").tablesorter({ selectorHeaders: 'thead th.sortable' }); 
    } 
); 

(function ($) {

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
                $el.is(':input') && $el.on('keyup keypress mouseup',function(e){
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

    $('#answer-table input').donetyping(function(){
        if ($(this).val() == '' || $(this).val() == null) {
            return false;    
        }
        var marks = parseInt($(this).val());
        var max = parseInt($(this).attr("max"));
        var current_score = parseInt($(this).attr("current-score")); 
        $("#ajax-message").hide();

        if (marks < 0) {
            $("#ajax-message").removeClass('alert-success');
            $("#ajax-message").addClass('alert-danger');
            $("#ajax-message").show();
            $("#ajax-message").html(lang_strings['positive_number']);
            $('html, body').animate({
                scrollTop: $(".page-header").offset().top
            }, 500);
            return false;
        } else if (marks == current_score) {
            $("#ajax-message").removeClass('alert-success');
            $("#ajax-message").addClass('alert-danger');
            $("#ajax-message").show();
            $("#ajax-message").html(lang_strings['update_require']);
            $('html, body').animate({
                scrollTop: $(".page-header").offset().top
            }, 500);
            return false;
        } else if (marks > max) {
            $("#ajax-message").removeClass('alert-success');
            $("#ajax-message").addClass('alert-danger');
            $("#ajax-message").show();
            $("#ajax-message").html(lang_strings['more_point_1'] + max + lang_strings['more_point_2']);
            $('html, body').animate({
                scrollTop: $(".page-header").offset().top
            }, 500);
            return false;
        } 

        $(this).attr("current-score", marks);

        var std_id = parseInt($(this).attr("name"));
        var q_id = parseInt($(this).attr("question"));
        var inputField = $(this);


        $.ajax({
            dataType: 'json',
            url: appData.baseUrl + 'score/update',
            type: 'post',
            data: {'id': q_id, 'student_id': std_id, 'score': marks, 'current_score' : current_score, 'max' : max},
            success: function (response)
            {
                console.log(response);
                if (response.success || response.success === "true")
                {
                    $("#studentscr2-" + std_id).text(response.score);
                    $("#studentscr1-" + std_id).text(response.score);
                    var originalBackgroundColor = inputField.css('background-color'),
                        originalColor = inputField.css('color');
                    inputField.css({ 'background-color' : 'green', 'color' : 'white' });
                    setTimeout(function(){
                      inputField.css({ 'background-color' : originalBackgroundColor, 'color' : originalColor });
                    }, 1000);
                    if (inputField.parents('.read-essay').first().length > 0) {
                        inputField.parents('.read-essay').first().prev().children().text(marks);
                    }
                    //console.log(inputField.parents('.read-essay').first());
                } else {
                    alert('Something went wrong, try again later');
                }
            }
        });
    });

    var appData = $.parseJSON($("#app-data").text());

    $(document).on('click', '#answer-table-overview', function () {
        $("#ajax-message").hide();
        // tab information insert into cookie to keep tracking
        setCookie("tabInfo", "answer-table-overview", 1);
        if($(this).hasClass('active'))
            return;
        $(this).addClass('active');
        $('#answer-table-show').removeClass('active');
        $('.question-collapse').hide();
    });

    $(document).on('click', '#answer-table-show', function () {
        $("#ajax-message").hide();
        // tab information insert into cookie to keep tracking
        setCookie("tabInfo", "answer-table-show", 1);
        if($(this).hasClass('active'))
            return;
        $(this).addClass('active');
        $('#answer-table-overview').removeClass('active');
        $('.question-collapse').show();
    });

    $(document).on('change', '#answer-table-filter select', function () {
        $('form#answer-table-filter').submit();
    });

    $("#answer-table table").find('tr').each(function(){
        checkRow($(this));
    });
    
    // get tab information
    var currentTab = getCookie("tabInfo");
    if (currentTab == 'answer-table-show') {
        $('#answer-table-show').trigger('click');
    } else {
        $('#answer-table-overview').trigger('click');
    }

    // essay pop up modal
    $(document).on('click', 'button.read-essay', function () {
        $(this).next().next().modal('show');
    });

    // delete unwanted answer
    $(document).on('click', 'button.delete-answer', function () {
        var infoModal = $('#confirm-delete');
        var std_id = $(this).attr('id');
        $.ajax({
            dataType: 'json',
            url: appData.baseUrl + 'student/confirmDeleteStudent',
            type: 'post',
            data: {'student_id': std_id},
            success: function (response)
            {
                if (response.success || response.success === "true")
                {
                    var str = lang_strings['remove_question'] + response.student_full_name + ' (' + response.student_class + lang_strings['with_points'] + response.student_score + '?';
                    infoModal.find('.modal-body').html(str);
                    infoModal.find('.modal-footer button#confirmed').attr('value', response.student_id);
                    infoModal.modal('show');
                } else {
                    alert('Something went wrong!!! Please try again later');
                }
            }
        });
    });

    $(document).on('click', 'button#confirmed', function () {
       var std_id = $(this).attr('value');
       var infoModal = $('#confirm-delete');
       $.ajax({
            dataType: 'json',
            url: appData.baseUrl + 'student/deleteStudent',
            type: 'post',
            data: {'student_id': std_id},
            success: function (response)
            {
                infoModal.modal('hide');
                if (response.success || response.success === "true")
                {
                    $("#ajax-message").removeClass('alert-danger');
                    $("#ajax-message").addClass('alert-success');
                    $("button#" + std_id).closest('tr').remove();
                } else {
                    $("#ajax-message").removeClass('alert-success');
                    $("#ajax-message").addClass('alert-danger');
                }
                $("#ajax-message").show();
                $("#ajax-message").html(response.message);
            }
        });
    });

    interval = setInterval(getUpdated, 10000);

    function getUpdated() {
        var quizId = $("#quizId").text();
        $.ajax({
            type: "POST",
            url: appData.baseUrl + 'quiz/ajax_latest',
            data: {quizId:quizId},
            async: true,
            success: function(data) {
                if ($("#prev_data").html() == data) {
                    // do nothing
                } else {
                    clearInterval(interval);
                    $("#prev_data").html(data);
                    var openTab = getCookie("tabInfo");
                    $.ajax({
                        dataType: 'html',
                        type: "POST",
                        url: appData.baseUrl + 'quiz/ajax_update',
                        data: {quizId:quizId, currentTab:openTab},
                        async: true,
                        success: function(data) {
                            $(".panel").html(data);
                            interval = setInterval(getUpdated, 10000);
                        }
                    });
                }
            }
        });
    }

    $(document).on('click', 'button#print', function (e) {
        e.preventDefault();
        var quizId = parseInt($("#quizId").text());
        $.ajax({
            dataType: 'html',
            type: "POST",
            url: appData.baseUrl + 'quiz/ajax_print_answer',
            data: {quizId:quizId},
            async: true,
            success: function(data) {
                var WindowObject = window.open("", "PrintWindow", "width=750,height=650,top=50,left=50,toolbars=no,scrollbars=yes,status=no,resizable=yes");
                WindowObject.document.writeln(data);
                WindowObject.document.close();
                WindowObject.focus();
                WindowObject.print();
                WindowObject.close();
            }
        });
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
