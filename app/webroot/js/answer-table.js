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

var appData = $.parseJSON($("#app-data").text()); 

var interval;

function getUpdated() {
    var quizId = $("#quizId").text();
    $.ajax({
        type: "POST",
        url: appData.baseUrl + 'quiz/ajax_latest',
        data: {quizId:quizId},
        async: true,
        success: function(data) {
            var old_data = $.parseJSON($("#prev_data").html());
            var new_data = $.parseJSON(data);
            if (JSON.stringify(old_data.studentIds) == JSON.stringify(new_data.studentIds)) {
                // check offline students
                var offline = false;
                $.grep(old_data.onlineStds, function(el) {
                    if ($.inArray(el, new_data.onlineStds) == -1) {
                        offline = true;
                        $('tr#student-'+el).find('.online').remove();
                    } 
                });
                if (offline) {
                    $("#prev_data").html(data);
                }

            } else {
                clearInterval(interval);
                $("#prev_data").html(data);
                var openTab = getCookie("tabInfo");
                $.ajax({
                    dataType: 'JSON',
                    type: "POST",
                    url: appData.baseUrl + 'quiz/ajax_update',
                    data: {quizId:quizId, currentTab:openTab, old_data:old_data.studentIds, new_data:new_data.studentIds},
                    async: true,
                    success: function(data) {
                        $.each(data, function(index, value) {
                            updateIndividulaStudent(value);

                        })
                        interval = setInterval(getUpdated, 2000);
                        // $(".panel").html(data);
                        // testFunc();
                        // interval = setInterval(getUpdated, 2000);
                    }
                });
            }
        }
    });
}

function updateIndividulaStudent(student_id) {
    if ($("tr#student-" + student_id).length == 0) {
        var sl = parseInt($('#answer-table table tbody tr').length)+1;
    } else {
        $("tr#student-" + student_id).find(':input').attr('disabled', true);
        $("tr#student-" + student_id).find('.delete-answer').hide();
        $("tr#student-" + student_id).find('.ajax-loader').show();
        var sl = parseInt($("tr#student-" + student_id).find('.question-serial').text());
    }
    $.ajax({
        dataType: 'html',
        type: "POST",
        url: appData.baseUrl + 'quiz/ajax_student_update',
        data: {student_id:student_id, sl:sl},
        async: true,
        success: function(data) {
            if ($("tr#student-" + student_id).length == 0) {
                // New stduent
                var html = '<tr id="student-'+student_id+'">';
                html+=data;
                html+='</tr>';
                $('#answer-table table tbody').append(html);
            } else {
                $("tr#student-" + student_id).html(data);
                $("tr#student-" + student_id).find('.ajax-loader').hide();
                $("tr#student-" + student_id).find('.delete-answer').show();
                $("tr#student-" + student_id).find(':input').attr('disabled', false);
            }
            testFunc();
            $(".table").trigger("update");
            //$(".table").tablesorter({ selectorHeaders: 'thead th.sortable' });
        }
    });
}

$(document).ready(function(){ 
    interval = setInterval(getUpdated, 2000);
    testFunc();
    $(".table").tablesorter({ selectorHeaders: 'thead th.sortable' }); 
}); 

(function ($) {

    $.fn.extend({
        donetyping: function(callback,timeout){
            timeout = timeout || 1e3; // 1 second default timeout
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
        var std_online = $(this).closest('tr').find('.online').length;
        $.ajax({
            dataType: 'json',
            url: appData.baseUrl + 'student/confirmDeleteStudent',
            type: 'post',
            data: {'student_id': std_id},
            success: function (response)
            {
                if (response.success || response.success === "true")
                {
                    var str = '';
                    if (std_online == 1) {
                        str += lang_strings['online_warning'];
                    }
                    str += lang_strings['remove_question'] + response.student_full_name + ' (' + response.student_class + lang_strings['with_points'] + response.student_score + '?';
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
                    var re_index = 1;
                    $("#answer-table > table > tbody  > tr").each(function() { 
                        $(this).find('.question-serial').html(re_index);
                        re_index++;
                    });
                } else {
                    $("#ajax-message").removeClass('alert-success');
                    $("#ajax-message").addClass('alert-danger');
                }
                $("#ajax-message").show();
                $("#ajax-message").html(response.message);
            }
        });
    });

    

    $(document).on('click', 'button#print', function (e) {
        e.preventDefault();
        var quizId = parseInt($("#quizId").text());
        $('#print_div').html('');
        window.frames["print_frame"].document.body.innerHTML='';
        $.ajax({
            dataType: 'html',
            type: "POST",
            url: appData.baseUrl + 'quiz/ajax_print_answer',
            data: {quizId:quizId},
            async: true,
            success: function(data) {
                $('#print_div').html(data);
                //alert(print_div);
                printDiv();
                // var WindowObject = window.open('about:blank');
                // WindowObject.document.writeln(data);
                // WindowObject.document.close();
                // WindowObject.focus();
                // WindowObject.print();
                // WindowObject.close();
                // var WindowObject = window.open("", "PrintWindow", "width=750,height=650,top=50,left=50,toolbars=no,scrollbars=yes,status=no,resizable=yes");
                // WindowObject.document.writeln(data);
                // WindowObject.document.close();
                // WindowObject.focus();
                // WindowObject.print();
                // WindowObject.close();
            }
        });
    });
    printDivCSS = new String ('<link rel="stylesheet" href="'+appData.baseUrl+'css/print_test.css" type="text/css" media="print">');
    function printDiv() {
        window.frames["print_frame"].document.body.innerHTML=$('#print_div').html();
        window.frames["print_frame"].window.focus();
        window.frames["print_frame"].window.print();
    }

    $(document).on('click', '.automatic', function () {
        $('.automatic_rating').each(function(){
            $(this).hide();
            $(this).prev().show();
        });
        $(this).hide();
        $(this).next().show();
        var elem = $(this);
        setTimeout(function(){
            elem.next().hide();
            elem.show();
        }, 7000);
    });

    $(document).on('click', '.std-info', function () {
        $('.update-std').each(function(){
            $(this).hide();
            $(this).prev().show();
        });
        $(this).hide();
        $(this).next().show();
        var elem = $(this);
        setTimeout(function(){
            elem.next().hide();
            elem.show();
        }, 10000);
    });

    $('#answer-table input.update-std').donetyping(function(){
        var std_info = $(this).attr('data-rel');
        var value_info = $(this).val();
        var inputField = $(this);
        clearInterval(interval);
        $.ajax({
            dataType: 'json',
            url: appData.baseUrl + 'student/ajax_std_update',
            type: 'post',
            data: {'std_info' : std_info, 'value_info' : value_info},
            success: function (response)
            {
                if (response.success || response.success === "true")
                {
                    var result = std_info.split('-');
                    console.log(result[1]);
                    var old_data = $.parseJSON($("#prev_data").html());
                    old_data.studentIds[result[1]][0][result[0]] = response.changetext;
                    $("#prev_data").html(JSON.stringify(old_data));
                    inputField.hide();
                    inputField.prev().html(response.changetext + ' <i class="glyphicon pencil-small"></i>').show();
                } else {
                    alert(response.message);
                }
                clearInterval(interval);
                interval = setInterval(getUpdated, 2000);
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

function testFunc() {
    $('#answer-table input:not(.update-std)').donetyping(function(){
        $("#ajax-message").hide();
        var current_score = parseFloat($(this).attr("current-score"));
        if (($(this).val() == '' || $(this).val() == null) && isNaN(current_score)) {
            return false;    
        }

        var marks = $(this).val();
        if(isNaN(marks)) {
            marks = 'null';
        }

        var max = parseFloat($(this).attr("max")); 

        if (marks < 0) {
            $("#ajax-message").removeClass('alert-success');
            $("#ajax-message").addClass('alert-danger');
            $("#ajax-message").html(lang_strings['positive_number']);
            $("#ajax-message").show();
            $('html, body').animate({
                scrollTop: $(".page-header").offset().top
            }, 500);
            return false;
        } else if (marks == current_score) {
            // $("#ajax-message").removeClass('alert-success');
            // $("#ajax-message").addClass('alert-danger');
            // $("#ajax-message").html(lang_strings['update_require']);
            // $("#ajax-message").show();
            // $('html, body').animate({
            //     scrollTop: $(".page-header").offset().top
            // }, 500);
            return false;
        } else if (marks > max) {
            $("#ajax-message").removeClass('alert-success');
            $("#ajax-message").addClass('alert-danger');
            $("#ajax-message").html(lang_strings['more_point_1'] + max + lang_strings['more_point_2']);
            $("#ajax-message").show();
            $('html, body').animate({
                scrollTop: $(".page-header").offset().top
            }, 500);
            return false;
        } 

        $(this).attr("current-score", marks);

        var std_id = parseInt($(this).attr("name"));
        var q_id = parseInt($(this).attr("question"));
        var inputField = $(this);


        clearInterval(interval);

        $.ajax({
            dataType: 'json',
            url: appData.baseUrl + 'score/update',
            type: 'post',
            data: {'id': q_id, 'student_id': std_id, 'score': marks, 'current_score' : current_score, 'max' : max},
            success: function (response)
            {
                if (response.success || response.success === "true")
                {
                    $("#studentscr2-" + std_id).text(response.score);
                    $("#studentscr1-" + std_id).text(response.score);
                    if (inputField.hasClass('automatic_rating')) { // if automatic question update
                        inputField.hide();
                        inputField.prev().html(marks).show();
                    } else {
                        var originalBackgroundColor = inputField.css('background-color'),
                        originalColor = inputField.css('color');
                        inputField.css({ 'background-color' : 'green', 'color' : 'white' });
                        setTimeout(function(){
                          inputField.css({ 'background-color' : originalBackgroundColor, 'color' : originalColor });
                        }, 1000);
                    }
                    if (inputField.parents('.read-essay').first().length > 0) {
                        if (marks == 'null') {
                            inputField.parents('.read-essay').first().prev().children().hide();
                        } else {
                            inputField.parents('.read-essay').first().prev().children().show();
                        }
                        inputField.parents('.read-essay').first().prev().children().text(marks);
                    }
                } else {
                    alert('Something went wrong, try again later');
                }
                clearInterval(interval);
                interval = setInterval(getUpdated, 2000);
            }
        });
    });
}
