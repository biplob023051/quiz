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
        var current_score = $(this).attr("value"); 
        
        if (marks < 0) {
            $("#error-message").show();
            $("#error-message").html('Please Give a postive number!');
            return false;
        } else if (marks == current_score) {
            $("#error-message").show();
            $("#error-message").html('You have not updated score yet!');
            return false;
        } else if (marks > max) {
            // alert("Marks not allowed more than " + max + " value");
            // return;
        } 

        var std_id = $(this).attr("name");
        var q_id = $(this).attr("question");


        $.ajax({
            dataType: 'json',
            url: appData.baseUrl + '/score/update',
            type: 'post',
            data: {'id': q_id, 'student_id': std_id, 'score': marks, 'current_score' : current_score},
            success: function (response)
            {
                if (response.success || response.success === "true")
                {
                    location.reload();
                }
            }
        });
    });

    var appData = $.parseJSON($("#app-data").text());

    $(document).on('click', '#answer-table-overview', function () {
        $("#error-message").hide();
        if($(this).hasClass('active'))
            return;
        $(this).addClass('active');
        $('#answer-table-show').removeClass('active');
        $('#overview').show();
        $('#details').hide();
    });

    $(document).on('click', '#answer-table-show', function () {
        $("#error-message").hide();
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

    if (answer_tab == true) {
        $('#answer-table-show').trigger('click');
    }
    
})(jQuery);