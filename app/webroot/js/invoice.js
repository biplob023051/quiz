// Require Bootstrap.js

(function ($) {
    var appData = $.parseJSON($("#app-data").text());

    $(document).on('click', '#send-invoice', function () {
        //console.profile("Sending invoice");
        $.ajax({
            url: appData.baseUrl + 'invoice/create',
            dataType: 'json',
            success: function (response)
            {
                if(response.success)
                {
                    $('#invoice-dialog').modal('hide');
                    $('#invoice-success-dialog').modal('show');
                }
            },
            error: function()
            {
                $('#invoice-dialog').modal('hide');
                $('#invoice-error-dialog').modal('show');
            }
        });
        //console.profileEnd();
    });

    $(document).on('change', '#quiz-filter select', function () {
        $('form#quiz-filter').submit();
    });

    $(document).on('click', 'button.delete-quiz', function () {
        var quiz_id = $(this).attr('quiz-id'),
            button_box = $(this); 
        var infoModal = $('#confirm-delete');
        $.ajax({
            data: {'quiz_id': quiz_id},
            type: 'post',
            url: appData.baseUrl + 'quiz/single',
            dataType: 'json',
            success: function (response)
            {
                var bodyData = 'There are ' + response.no_of_answers + ' answers, ' + response.no_of_students +
                ' students, and ' + response.no_of_questions + ' number of questions. This can not be undone. Are you sure want to delete?' ;
                var headerData = 'Delete quiz ' + response.quiz_name + '?';
                var link = appData.baseUrl + 'quiz/quizDelete/' + response.id;
                infoModal.find('.modal-body').html(bodyData);
                infoModal.find('.modal-header').html(headerData);
                infoModal.find('.modal-footer a').attr('href', link);
                infoModal.modal('show');
            }
        });   
        
    });

    $(document).on('click', 'button.active-quiz', function () {
        var quiz_id = $(this).attr('id'),
            status = $(this).attr('status'),
            button_box = $(this);
        $.ajax({
            data: {'quiz_id': quiz_id, 'status': status},
            type: 'post',
            url: appData.baseUrl + 'quiz/changeStatus',
            dataType: 'json',
            success: function (response)
            {
                if (response.result === 1)
                {
                    if ((response.filter == "1") || (response.filter == "0")) {
                        button_box.closest('tr').remove();
                    } else {
                        location.reload();
                    }
                    
                } else {
                    alert(response.message);
                }
            }
        });    
    });


})(jQuery);