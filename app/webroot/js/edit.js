var debugVar;
(function ($) {
    var appData = $.parseJSON($("#app-data").text());
    var webQuizConfig = {
        "quizId": appData.quizId,
        "baseUrl": appData.baseUrl,
        "questionTypes": appData.questionTypes
    };

    webQuiz.init(webQuizConfig);
    
    if (initial) { // Add New Question only if its quiz create 
        webQuiz.addNewQuestion(true); // Show Question 
    } else if(no_question) {
        webQuiz.addNewQuestion(true); // Show Question 
    } else {
        webQuiz.addNewQuestion(); // Don't Show Question 
    }
    
    $(document).on('click', '#questions button.add-choice', function () {
        //console.profile("Adding choice");
        var questionId = $(this).parent().parent().parent().attr('id'),
                choiceContainer = $('#' + questionId).find("div.choices");

        webQuiz.addChoice(
                parseInt(questionId.substr(1, questionId.length - 1)),
                choiceContainer
                );
        //console.profileEnd();
    });

    // biplob added for adding one more row on page load by default    
    $('#questions button.add-choice').trigger('click');

    $(document).on('change', '#questions select.choice-type-selector', function () {
        //console.profile("Change selector");  
        var element = $(this),
                questionId = element.attr('id');

        questionId = questionId.substr(3, questionId.length - 1);

        webQuiz.changeChoiceType(
                parseInt(questionId),
                element.val(),
                $("#q" + questionId).find("button.add-choice")
                );
        // biplob added for adding one more row on select change
        if (($(this).val() == 1) || ($(this).val() == 3)) {
           $('#questions button.add-choice').trigger('click'); 
        }

        webQuiz.questionOptions($(this).val());

        if ($(this).val() == 6) { // hide explanation input for header type question
            $('#QuestionText').attr('placeholder', lang_strings['header_q_title']); // change placeholder
            $('#QuestionExplanation').closest('.row').hide(); 
        } else {
            $('#QuestionText').attr('placeholder', lang_strings['other_q_title']); // change placeholder
            $('#QuestionExplanation').closest('.row').show();
        }

        if ($(this).val() == 7) { 
            // Change placeholder for explanation text if youtube
            $('#QuestionExplanation').attr('placeholder', lang_strings['youtube_exp_text']);
        } else if ($(this).val() == 8) { 
            // Change placeholder for explanation text if image
            $('#QuestionExplanation').attr('placeholder', lang_strings['image_exp_text']);
        } else {
            $('#QuestionExplanation').attr('placeholder', lang_strings['other_exp_text']);
        }

        if (($(this).val() == 7) || ($(this).val() == 8)) { // hide question for youtube or image type question
           $('#QuestionText').parent().hide(); 
        } else {
            $('#QuestionText').parent().show(); 
        }

        //console.profileEnd();        
    });

    $(document).on('click', '#questions tr td div.preview-btn button.edit-question', function () {
        //console.profile("Editing question");
        var blank_question = false;
        if ($('#QuestionText').val() == '') {
            blank_question = true;
        }

        var qidStr = $(this).attr('id'),
                questionId = parseInt($(this).attr('id').substr(6, qidStr.length - 1));
        // alert(qidStr);
        // alert(blank_question);
        if (blank_question == true) {
            webQuiz.setToPreview(
                webQuiz.currentEditQid,
                $("#q" + webQuiz.currentEditQid),
                function (question)
                {
                    webQuiz.setToEdit(
                            questionId,
                            $("#q" + questionId),
                            function (question)
                            {
                                var questionContainer = $("#q" + questionId);
                                questionContainer.find('select.choice-type-selector').val(question.value.QuestionType.id);

                                var isMultipleChoices = webQuiz
                                        .getQuestionType(question.value.QuestionType.id)
                                        .value.QuestionType.multiple_choices;

                                if (!isMultipleChoices)
                                    questionContainer.find("button.add-choice").hide();
                                if (question.value.QuestionType.id == 7 || question.value.QuestionType.id == 8) { // hide question text for youtube and image type question
                                     $('#QuestionText').parent().hide(); 
                                } else {
                                    $('#QuestionText').parent().show(); 
                                }

                                webQuiz.questionOptions(question.value.QuestionType.id); // show hide max_allowed

                                if (question.value.QuestionType.id == 6) { // if header type then hide explanation text
                                    $('#QuestionExplanation').closest('.row').hide();
                                } else {
                                    $('#QuestionExplanation').closest('.row').show();
                                }
                            }
                    );
                    $("#q" + questionId).show();
                    webQuiz.lastEditQid = question.question_id;
                    webQuiz.currentEditQid = questionId;
                    webQuiz.choiceSortable();
                },
                'question/setPreview/'
            );    
        } else {
            webQuiz.setToPreview(
                webQuiz.currentEditQid,
                $("#q" + webQuiz.currentEditQid),
                function (question)
                {
                    webQuiz.setToEdit(
                            questionId,
                            $("#q" + questionId),
                            function (question)
                            {
                                var questionContainer = $("#q" + questionId);
                                questionContainer.find('select.choice-type-selector').val(question.value.QuestionType.id);

                                var isMultipleChoices = webQuiz
                                        .getQuestionType(question.value.QuestionType.id)
                                        .value.QuestionType.multiple_choices;

                                if (!isMultipleChoices)
                                    questionContainer.find("button.add-choice").hide();
                                if (question.value.QuestionType.id == 7 || question.value.QuestionType.id == 8) { // hide question text for youtube and image type question
                                     $('#QuestionText').parent().hide(); 
                                } else {
                                    $('#QuestionText').parent().show(); 
                                }

                                webQuiz.questionOptions(question.value.QuestionType.id); // show hide max_allowed

                                if (question.value.QuestionType.id == 6) { // if header type then hide explanation text
                                    $('#QuestionExplanation').closest('.row').hide();
                                } else {
                                    $('#QuestionExplanation').closest('.row').show();
                                }
                            }
                    );
                    $("#q" + questionId).show();
                    webQuiz.lastEditQid = question.question_id;
                    webQuiz.currentEditQid = questionId;
                    webQuiz.choiceSortable();
                }
            );
        }     

        //console.profileEnd();
    });
    
    $("#add-question").on('click', function () {

        if(!$('#q-1').is(':visible')) {
            $('#q-1').show();
            return;
        }

        var question_number = $('#questions tbody').children('tr:not(.others_type)').length;
    
        var questionTypeId = $('#questions select.choice-type-selector').val();
        // current tr
        var currentTr = $('#q-1');

        if ((questionTypeId != 7) && (questionTypeId != 8)) { // don't validate empty question for youtube or image
            if ($('#QuestionText').val() == '') {       
                var currentEditQid = $("#q" + webQuiz.currentEditQid),
                choiceContainer = currentEditQid.find("div.choices");
                if ($('.alert-danger').length){
                    $('.alert-danger').remove();
                }
                if (questionTypeId == 6) {
                    choiceContainer.prepend('<div class="alert alert-danger">' + lang_strings['empty_header'] + '</div>');      
                } else {
                    choiceContainer.prepend('<div class="alert alert-danger">' + lang_strings['empty_question'] + '</div>');
                }
                
                return;        
            }
        }
        
        var validationError = webQuiz.dataValidation(
            questionTypeId
        );

        if (validationError == false) {
            webQuiz.addNewQuestion(true); // Show Question 
            // delete new empty question for add new question
            if ((questionTypeId != 7) && (questionTypeId != 8)) { // don't validate empty question for youtube or image
                if ($('#QuestionText').val() == '') {
                    $("#q-1").remove();
                    // trigger click to add extra one choice more by default
                    $('#questions button.add-choice').trigger('click');
                    return;
                }
            }
            var response = webQuiz.setToPreview(webQuiz.lastEditQid, $("#q" + webQuiz.lastEditQid), 'test', 'question/save/', question_number);    
        }

        
    });

    $(document).on('click', '#questions tr td div.preview-btn button.delete-question', function () {
        //console.profile("Delete question");
        var qidStr = $(this).attr('id'),
                questionId = parseInt($(this).attr('id').substr(8, qidStr.length - 1));
        console.log('delete', questionId);
        webQuiz.deleteQuestion(questionId, $("#q" + questionId));
        //console.profileEnd();
    });

    $('#submit-quiz').on('click', function () {
        if ($('#QuestionText').val() == '') {
            $("#QuizEditForm").submit();
            return;
        }

        var questionTypeId = $('#questions select.choice-type-selector').val();
        
        var validationError = webQuiz.dataValidation(
            questionTypeId
        );

        if (validationError == false) {
            webQuiz.setToPreview(
                webQuiz.currentEditQid,
                $("#q" + webQuiz.currentEditQid),
                function (question)
                {
                    webQuiz.lastEditQid = question.question_id;
                    //webQuiz.currentEditQid = questionId;
                   $("#QuizEditForm").submit();
                }
            );    
        }
        
    });

    $(document).on('click', '#questions button.remove-choice', function () {
        //alert('hi');
        var questionId = $(this).closest("tr").attr('id'),
                choice = $(this).attr('choice'),
                choiceContainer = $('#' + questionId).find("div.choices");
        // remove new additional choice from new question
        if (questionId == 'q-1') {
            $(this).closest('.well').parent().parent().remove();
            choiceContainer.append('<div class="row" style="display:none;"></div>');
            return;
        }

        // remove new additional choice from edit question
        if (questionId != 'q-1') {
            $(this).closest('.well').parent().parent().remove();
            choiceContainer.append('<div class="row" style="display:none;"></div>');
            return;
        }

        webQuiz.removeChoice(
                questionId.substr(1),
                choice,
                $(this)
                );
    });

    $(document).on('click', '#questions button.edit-done', function (e) {
        e.preventDefault();
        var questionTypeId = $('#questions select.choice-type-selector').val();
        
        if ((questionTypeId != 7) && (questionTypeId != 8)) { // don't validate empty question for youtube or image
            if ($('#QuestionText').val() == '') {
                var currentEditQid = $("#q" + webQuiz.currentEditQid),
                choiceContainer = currentEditQid.find("div.choices");
                if ($('.alert-danger').length){
                    $('.alert-danger').remove();
                }
                if (questionTypeId == 6) {
                    choiceContainer.prepend('<div class="alert alert-danger">' + lang_strings['empty_header'] + '</div>');      
                } else {
                    choiceContainer.prepend('<div class="alert alert-danger">' + lang_strings['empty_question'] + '</div>');
                }
                return;
            }
        }
        
        var validationError = webQuiz.dataValidation(
            questionTypeId
        );

        //var question_number = $(this).closest('tr').index()+1;
        var currentTr = $(this).closest('tr');

        var question_number = 1;
        $('#questions > tbody  > tr:not(.others_type)').each(function() {
            if ($(this).index() == currentTr.index()) {
                return false;
            } else {
                question_number++;
            }
        });

        if (validationError == false) {
            webQuiz.addNewQuestion();
            var response = webQuiz.setToPreview(webQuiz.lastEditQid, $("#q" + webQuiz.lastEditQid), 'test', 'question/save/', question_number);    
        }

    });

    // settings show hide
    $(document).on('click', '#show-settings', function () {
        $('#settings-options').toggle();
    });

    // Return a helper with preserved width of cells
    var fixHelper = function(e, ui) {
        ui.children().each(function() {
            $(this).width($(this).width());
        });
        return ui;
    };

    $("#questions tbody").sortable({
        items: 'tr:not(#q-1)',
        tolerance: 'pointer',
        revert: 'invalid',
        placeholder: 'well tile',
        forceHelperSize: true,
        helper: fixHelper,
        update: function( ) {
            webQuiz.reArrangeQuestionNumber();
        }
    });

})(jQuery);