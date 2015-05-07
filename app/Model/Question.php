<?php
class Question extends AppModel {
    
    
    public $belongsTo = array('Quiz', 'QuestionType');
    public $hasMany = array('Answer', 'Choice');
        
    public function saveQuestions($questions, $quizId)
    {
        $data = array();

        foreach($questions as $question)
        {
            $data[] = array(
                'Question' => array(
                    'choices' => serialize($question['choices']),
                    'text' => $question['txt'],
                    'explanation' => $question['expl'],
                    'quiz_id' => $quizId
                )
            );

        }
        return $this->saveMany($data);
    }

    public function getQuestionOwner($questionId, $owner_id) {
        // checking $questionId if $owner_id created that question
        // 
        $questionInfo = $this->find('first', array(
                'joins' => array( 
                    array( 
                        'table' => 'quizzes', 
                        'alias' => 'Quiz', 
                        'type' => 'inner',  
                        'conditions'=> array(
                            'Quiz.id = Question.quiz_id',
                            'Quiz.user_id' => $owner_id
                        ) 
                    )
                ),
                'conditions' => array('Question.id' => $questionId),
                'recursive' => -1
            )
        );
        // if questionId found by $owner_id set true, otherwise false
        if (!empty($questionInfo)) {
            return true;
        } else {
            return false;
        }
    }
   
}