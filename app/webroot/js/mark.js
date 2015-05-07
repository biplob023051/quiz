var answerTable = {
    'answerData'    : null,
    'questionData'  : null,
    'userAnswerData': null,
    'countScore'    : function(userId) {
        var userAnswersData = this.userAnswersData[userId], totalScore = 0;
        
        if(userAnswersData === null)
            throw new Exception("User does not exists");
        
        $.each(userAnswersData, function(index, value){
            totalScore += answerTable.markFunction[value.choice_type](answerTable.questionData[value.question_id], value.choice);
        });
        
        return totalScore;
        
        console.log(score);
    },
    
    
    'updateScore'  : function(answerId, score, callback)
    {
        $.ajax({
            dataType    : 'json',
            url         : '/cakephp/answer/update',
            data        : {'id' : answerId, 'score' : score},
            success     : function(response)
            {
                callback(response);
            }
        });
    }
};