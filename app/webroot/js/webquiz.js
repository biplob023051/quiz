// Requires: jQuery, Handlebars
var webQuiz = {
    quizId: null,
    questionData: [],
    containerDOM: null,
    choiceTpl: {},
    cPreviewTpl: {},
    qPreviewTpl: {},
    questionTpl: null,
    choiceTplCache: {},
    currentEditQid: null,
    lastEditQid: null,
    questionTypes: null,
    baseUrl: '',
    init: function (config)
    {
        if (config.questionTypes === undefined)
            throw new Exception("Must define question types!");

        this.questionTypes = config.questionTypes;
        this.previewCallback = config.previewCallback;
        this.quizId = config.quizId;
        this.baseUrl = config.baseUrl;

        this.questionTpl = Handlebars.compile(
                $("#question-edit-template").html()
                );

        this.qPreviewTpl = Handlebars.compile(
                $("#question-preview-template").html()
                );

        $.each(this.questionTypes, function (index, value)
        {
            webQuiz.questionTypes[index].QuestionType.id = parseInt(webQuiz.questionTypes[index].QuestionType.id);

            var tplName = value.QuestionType.template_name;

            webQuiz.choiceTpl[tplName] = Handlebars.compile(
                    $("#choice-" + tplName + "-edit-template").html()
                    );

            webQuiz.cPreviewTpl[tplName] = Handlebars.compile(
                    $("#choice-" + tplName + "-preview-template").html()
                    );

        });

        Handlebars.registerHelper('choice', function (items, config)
        {
            var output = [],
                    root = config.data.root,
                    tplName = root.QuestionType.template_name,
                    tpl;

            if (root.preview === true)
                tpl = webQuiz.cPreviewTpl[tplName];
            else
                tpl = webQuiz.choiceTpl[tplName];

            // If question only has single choice
            if (items.length === undefined)
            {
                output.push(tpl(items));
            }
            else
            {
                for (var i = 0; i < items.length; i++)
                {
                    // @TODO Find better way to inject parent template data
                    // @TODO Is it possible to cache a choice?

                    items[i].id = i;
                    items[i].question_id = root.question_id;

                    output.push(tpl(items[i]));
                }
            }

            return output.join('');
        });

        this.containerDOM = $("#questions tbody");
    },
    addNewQuestion: function ()
    {
        this.lastEditQid = this.currentEditQid;
        this.currentEditQid = -1;

        // @TODO: Find better way to set a default question
        this.addQuestion({
            id: -1,
            text: '',
            explanation: '',
            Choice: [{}],
            QuestionType: webQuiz.questionTypes[0].QuestionType,
            isNew: true,
            preview: false
        });
        return true;
    },
    addQuestion: function (question)
    {
        var html = this.questionTpl(question);
        this.questionData.push(question);
        this.containerDOM.append(html);
        console.log(html);
        return true;
    },
    deleteQuestion: function (questionId, questionContainer, onSuccessCallback)
    {
        $.ajax({
            data: {id: questionId},
            url: this.baseUrl + 'question/delete',
            dataType: 'json',
            type: 'post',
            success: function (response)
            {
                if (response.success === true)
                {
                    var question = webQuiz.getQuestion(questionId);
//                    if (question !== null)
//                        delete webQuiz.questionData[question.index];
                    questionContainer.remove();
                }
                else
                {
                    alert('Error! More detailed error is soon to be implemented\n\n');
                }

                if (onSuccessCallback !== undefined)
                    onSuccessCallback(response);
            }
        });
    },
    getQuestion: function (questionId)
    {
        questionId = parseInt(questionId);
        var question = null;
        $.each(this.questionData, function (index, value) {

            if (value.id === questionId)
            {
                question = {'index': index, 'value': value};
                return;
            }
        });

        return question;
    },
    getQuestionType: function (questionTypeId)
    {
        questionTypeId = parseInt(questionTypeId);
        var questionType = null;
        $.each(this.questionTypes, function (index, value) {

            if (value.QuestionType.id === questionTypeId)
            {
                questionType = {'index': index, 'value': value};
                return;
            }
        });

        return questionType;
    },
    setToPreview: function (questionId, questionContainer, onSuccessCallback, ajax_url)
    {
        questionId = parseInt(questionId);

        console.log("setToPreviewQid:", questionId);

        var question = webQuiz.getQuestion(questionId),
                _questionData = questionContainer.find('form').serializeJSON();

        if (question.value.preview === true)
            return;

        _questionData.data.isNew = question.value.isNew;
        _questionData.data.Question.quiz_id = webQuiz.quizId;

        console.log('test', _questionData);

        if (_questionData.data.isNew === true)
            delete _questionData['question_id'];

        ajax_url = typeof ajax_url !== 'undefined' ? ajax_url : 'question/save/';

        $.ajax({
            data: _questionData.data,
            url: webQuiz.baseUrl + ajax_url + questionId,
            dataType: 'json',
            type: 'post',
            success: function (response)
            {
                if (response.success === true)
                {
                    var tmp;

                    tmp = response.Question;
                    tmp.Choice = response.Choice;
                    tmp.QuestionType = webQuiz.getQuestionType(tmp.question_type_id).value.QuestionType;
                    tmp.id = parseInt(response.Question.id);
                    tmp.isNew = false;
                    tmp.preview = true;

                    webQuiz.questionData[question.index] = tmp;
                    console.log("setToPreviewSuccess:", tmp);

                    // remove last question if not save
                    if(typeof(response.dummy) != "undefined" && response.dummy !== null) {
                        if($("#q-1").length >= 0) {
                            $("#q-1").remove();
                        }
                    }

                    $(webQuiz.qPreviewTpl(tmp)).insertAfter(questionContainer);
                    questionContainer.remove();

                    if (onSuccessCallback !== undefined) {
                        if (onSuccessCallback == 'test') {
                            $('#questions button.add-choice').trigger('click');
                        } else {
                            onSuccessCallback(tmp);
                        }
                    }
                }
                else
                {
                    if (response.message != 'undefined' || response.message != null ) {
                        alert(response.message);
                    } else {
                        alert('Error! More detailed error is soon to be implemented\n\n');
                    }
                }
            }
        });
    },
    setToEdit: function (questionId, questionContainer, callback)
    {
        var question = this.getQuestion(questionId);

        // Try to lazy load question data from html
        //@TODO: Set the correct choice selector
        if (question === null)
        {
            var _question = $.parseJSON(questionContainer.find("script").text().trim());

            if (_question === null)
                return;

            _question.preview = false;
            _question.isNew = false;
            _question.id = parseInt(_question.id);

            webQuiz.questionData.push(_question);

            question = {
                index: this.questionData.length - 1,
                value: _question
            };

            console.log("setToEdit:", question);
        }
        else
        {
            // Return if question is not exists or already in preview mode
            if (question.value.preview === false)
                return;
            this.questionData[question.index].preview = false;
        }

        var html = webQuiz.questionTpl(question.value);
        $(html).insertAfter(questionContainer);

        questionContainer.remove();

        if (callback !== undefined)
            callback(question);

    },
    addChoice: function (questionId, choicesContainer)
    {
        var question = this.getQuestion(questionId);

        if (question === null)
            return;

        var question_value = question.value;

        if (question_value.QuestionType.multiple_choices === false)
            return false;

        var html = this.choiceTpl[question_value.QuestionType.template_name]({
                id : choicesContainer.children().length
            });

        choicesContainer.append(html);
    },
    removeChoice: function (question_id, choice, containerDOM)
    {
        $.ajax({
            data: {question_id : question_id, choice : choice},
            url: webQuiz.baseUrl + 'question/removeChoice',
            dataType: 'json',
            type: 'post',
            success: function (response)
            {
                if (response.success === true)
                {
                    console.log(response);
                    containerDOM.closest('.choice-' + choice).remove();
                }
            }
        });    
    },
    dataValidation: function (questionTypeId) {
        var validationError = false;

        var currentEditQid = $("#q" + webQuiz.currentEditQid),
        choiceContainer = currentEditQid.find("div.choices");
        
        if ((questionTypeId == 1) || (questionTypeId == 3)) {

            // choice validation
            validationError = webQuiz.choiceValidation(
                choiceContainer
            );

            // point validation for one correct
            if ((validationError == false) && (questionTypeId == 1)) {
                validationError = webQuiz.singlePointValidation(
                    choiceContainer
                );
            }

            // point validation for multi correct
            if ((validationError == false) && (questionTypeId == 3)) {
                validationError = webQuiz.multiPointValidation(
                    choiceContainer
                );
            }
        } else if (questionTypeId == 4) {
            validationError = webQuiz.manualRatingValidation(
                choiceContainer
            );
        } else if (questionTypeId == 5) {
            validationError = webQuiz.essayValidation(
                choiceContainer
            );
        }

        return validationError;
    },
    essayValidation: function (choiceContainer) 
    {
        var validationError = false;
        if ($("#ChoiceText").val() == '') {
            if ($('.alert-danger').length) {
                $('.alert-danger').remove();
            }
            validationError = true;
            choiceContainer.prepend('<div class="alert alert-danger">At least points should be greater than 0</div>');
        }
        return validationError;
    },
    manualRatingValidation: function (choiceContainer) 
    {
        var validationError = false;
        if ($("#ChoicePoints").val() == '') {
            if ($('.alert-danger').length) {
                $('.alert-danger').remove();
            }
            validationError = true;
            choiceContainer.prepend('<div class="alert alert-danger">At least points should be greater than 0</div>');
        }
        return validationError;
    },
    choiceValidation: function (choiceContainer)
    {
        var choiceArray = new Array();
        var validationError = false;   
        choiceContainer.find(':input[type="text"]').each(function(){
            // same choice not permit
            if (jQuery.inArray($(this).val(),choiceArray) == -1){
                choiceArray.push($(this).val());
            } else {
                if ($('.alert-danger').length){
                        $('.alert-danger').remove();
                }
                validationError = true;
                choiceContainer.prepend('<div class="alert alert-danger">Empty or Same Choices Are Not Permit</div>');
            }
            
        });
        return validationError;
    },
    singlePointValidation: function (choiceContainer)
    {
        var validationError = false;
        choiceContainer.find(':input[type="number"]').each(function(){
            if ($(this).val() > 0) {
                validationError = false;
                return false;
            } else {
                validationError = true;
            }
        });
        if (validationError == true) {
            if ($('.alert-danger').length){
                $('.alert-danger').remove();
            }
            choiceContainer.prepend('<div class="alert alert-danger">At least a point should be greater than 0</div>');
        }
        return validationError;
    },
    multiPointValidation: function (choiceContainer)
    {
        var validationError = false;
        var count = 0;
        choiceContainer.find(':input[type="number"]').each(function(){
            if ($(this).val() > 0) {
                count = count+1;
            }
        });
        if (count == 0) {
            if ($('.alert-danger').length){
                $('.alert-danger').remove();
            }
            choiceContainer.prepend('<div class="alert alert-danger">At least 2 points should be greater than 0</div>');
            validationError = true;   
        } else if(count == 1) {
            if ($('.alert-danger').length){
                $('.alert-danger').remove();
            }
            choiceContainer.prepend('<div class="alert alert-danger">You put only one correct answers, please choose another point greater than 0!!!</div>');
            validationError = true;
        } else {
            validationError = false;
        }

        return validationError;
    },
    changeChoiceType: function (questionId, questionTypeId, addChoiceBtnDOM)
    {
        var questionType = webQuiz.getQuestionType(questionTypeId).value.QuestionType,
                tplName = questionType.template_name;
        
        if (webQuiz.choiceTpl[tplName] === undefined)
            return;

        if (questionType.multiple_choices) {
            // If the tplName is multiple, enable the add button choice
            addChoiceBtnDOM.show();
        } else {
            addChoiceBtnDOM.hide();
        }
        
        // @TODO Not sure cache a question's choice is the best idea
        if (this.choiceTplCache[tplName + questionId] === undefined) {
            this.choiceTplCache[tplName + questionId] = webQuiz.choiceTpl[tplName]();
        }
        
        this.containerDOM.find("#q" + questionId + " div.choices").html(this.choiceTplCache[tplName + questionId]);

        $.each(this.questionData, function (index, value) {
            if (value.id === questionId) {
                webQuiz.questionData[index].QuestionType = questionType;
                webQuiz.questionData[index].Choice = [];    
                return;
            }
        });

    }
};