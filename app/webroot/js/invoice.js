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
                    $('#upgrade_account').attr('disabled', true);
                    $('span#btn_text').html(lang_strings['request_sent']);
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

    $('#invoice-success-dialog').on('hidden.bs.modal', function () {
        window.location.reload();
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
                var bodyData = lang_strings['delete_quiz_1'] + response.no_of_answers + lang_strings['delete_quiz_2'] + response.no_of_students +
                lang_strings['delete_quiz_3'] + response.no_of_questions + lang_strings['delete_quiz_4'];
                var headerData = lang_strings['delete_quiz_5'] + response.quiz_name + '?';
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

    $(document).on('click', 'button.duplicate-quiz', function () {
        var quiz_id = $(this).attr('quiz-id'),
            button_box = $(this);
        $.ajax({
            data: {'quiz_id': quiz_id},
            type: 'post',
            url: appData.baseUrl + 'quiz/duplicate',
            dataType: 'json',
            success: function (response)
            {
                if (response.result === 1)
                {
                    if (response.id != '') {
                        window.location.href = appData.baseUrl + 'quiz/edit/' + response.id;
                    } else {
                        location.reload();
                    } 
                } else {
                    alert(response.message);
                }
            }
        });    
    });

    // Share quiz
    $(document).on('click', 'button.share-quiz', function () {
        var quiz_id = $(this).attr('quiz-id'),
            quiz_name = $(this).attr('quiz-name');
        $('#confirm-delete').find('.modal-body').html(lang_strings['share_quiz_question']);
        $('#confirm-delete').find('.modal-header').html(lang_strings['share_quiz'] + ' ' + quiz_name + '?');
        $('#confirm-delete').find('.modal-footer a').attr('href', appData.baseUrl + 'quiz/share/' + quiz_id).removeClass('btn-danger').addClass('btn-success').html(lang_strings['share_quiz']);
        $('#confirm-delete').modal('show');
    });

    // Remove shared quiz
    $(document).on('click', 'button.remove-share', function () {
        var quiz_id = $(this).attr('quiz-id'),
            quiz_name = $(this).attr('quiz-name');
        $('#confirm-delete').find('.modal-body').html(lang_strings['remove_share_question']);
        $('#confirm-delete').find('.modal-header').html(lang_strings['remove_share'] + ' ' + quiz_name + '?');
        $('#confirm-delete').find('.modal-footer a').attr('href', appData.baseUrl + 'quiz/share/' + quiz_id + '/1').removeClass('btn-danger').addClass('btn-success').html(lang_strings['remove_shared_quiz']);
        $('#confirm-delete').modal('show');
    });


    // Quiz bank
    $(document).on('click', 'button.quiz-bank', function () {
        $.ajax({
            data: {},
            type: 'get',
            url: appData.baseUrl + 'quiz/ajax_bank',
            dataType: 'html',
            success: function (data)
            {
                $('#public-quiz').html(data).modal('show');
            }
        });
    });

    $(document).on('click', '#import', function () {
        window.location.assign(appData.baseUrl + "maintenance/load_dummy_data");  
    });


})(jQuery);