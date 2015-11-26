var debugVar;
(function ($) {
    var appData = $.parseJSON($("#app-data").text());
    var webQuizConfig = {
        "quizId": appData.quizId,
        "baseUrl": appData.baseUrl,
        "questionTypes": appData.questionTypes
    };

    webQuiz.init(webQuizConfig);
    webQuiz.addNewQuestion();

    
    
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
                                }
                        );
                        $("#q" + questionId).addClass("EditQuestionBorder");
                        webQuiz.lastEditQid = question.question_id;
                        webQuiz.currentEditQid = questionId;
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
                                }
                        );
                        $("#q" + questionId).addClass("EditQuestionBorder");
                        webQuiz.lastEditQid = question.question_id;
                        webQuiz.currentEditQid = questionId;
                    }
            );
        }       
        

        //console.profileEnd();
    });
    
    $("#add-question").on('click', function () {

        if ($('#QuestionText').val() == '') {
            var currentEditQid = $("#q" + webQuiz.currentEditQid),
            choiceContainer = currentEditQid.find("div.choices");
            if ($('.alert-danger').length){
                $('.alert-danger').remove();
            }
            choiceContainer.prepend('<div class="alert alert-danger">' + lang_strings['empty_question'] + '</div>');
            return;
        }
        var questionTypeId = $('#questions select.choice-type-selector').val();
        var validationError = webQuiz.dataValidation(
            questionTypeId
        );

        if (validationError == false) {

            webQuiz.addNewQuestion();
            // delete new empty question for add new question
            if ($('#QuestionText').val() == '') {
                $("#q-1").remove();
                // trigger click to add extra one choice more by default
                $('#questions button.add-choice').trigger('click');
                return;
            }
            var response = webQuiz.setToPreview(webQuiz.lastEditQid, $("#q" + webQuiz.lastEditQid), 'test');    
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
        
        if ($('#QuestionText').val() == '') {
            var currentEditQid = $("#q" + webQuiz.currentEditQid),
            choiceContainer = currentEditQid.find("div.choices");
            if ($('.alert-danger').length){
                $('.alert-danger').remove();
            }
            choiceContainer.prepend('<div class="alert alert-danger">' + lang_strings['empty_question'] + '</div>');
            return;
        }
        var questionTypeId = $('#questions select.choice-type-selector').val();
        var validationError = webQuiz.dataValidation(
            questionTypeId
        );

        if (validationError == false) {
            webQuiz.addNewQuestion();
            var response = webQuiz.setToPreview(webQuiz.lastEditQid, $("#q" + webQuiz.lastEditQid), 'test');    
        }
        
    });

    // settings show hide
    $(document).on('click', '#show-settings', function () {
        $('#settings-options').toggle();
    });

})(jQuery);