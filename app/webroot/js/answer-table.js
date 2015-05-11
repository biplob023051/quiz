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
    
    // update essay score
    $('#details input').blur(function() {
        var marks = $(this).val();
        var max = $(this).attr("max");
        var current_score = $(this).attr("current-score"); 
        $("#error-message").hide();
        
        if (marks < 0) {
            $("#error-message").show();
            $("#error-message").html('Please Give a postive number!');
            $('html, body').animate({
                scrollTop: $(".page-header").offset().top
            }, 500);
            return false;
        } else if (marks == current_score) {
            $("#error-message").show();
            $("#error-message").html('You have not updated score yet!');
            $('html, body').animate({
                scrollTop: $(".page-header").offset().top
            }, 500);
            return false;
        } else if (marks > max) {
            $("#error-message").show();
            $("#error-message").html('Marks not allowed more than ' + max + ' value');
            $('html, body').animate({
                scrollTop: $(".page-header").offset().top
            }, 500);
            return false;
        } 

        $(this).attr("current-score", marks);

        var std_id = $(this).attr("name");
        var q_id = $(this).attr("question");


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
                } else {
                    alert('Something went wrong, try again later');
                }
            }
        });
    });

    var appData = $.parseJSON($("#app-data").text());

    $(document).on('click', '#answer-table-overview', function () {
        $("#error-message").hide();
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
        $("#error-message").hide();
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
