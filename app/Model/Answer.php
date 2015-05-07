<?php

class Answer extends AppModel {

    public $belongsTo = array('Student', 'Question');

    public function getAnswerTable($quizId, $filter) {
        $sql = "
            SELECT       
            	Student.id,
                Student.fname,
                Student.lname,
                Student.submitted,
                Answer.id,
                Answer.text,
                Student.class,
                Answer.score, 
                Choice.points,
                Choice.text,
                Question.id,
                QuestionType.manual_scoring, 
                QuestionType.name
            FROM 
                answers Answer INNER JOIN choices Choice ON 
                (Answer.question_id = Choice.question_id) OR (Choice.text = null) 
    
                INNER JOIN questions Question ON
                Question.id = Choice.question_id
    
                INNER JOIN question_types QuestionType ON
                Question.question_type_id = QuestionType.id
    
                INNER JOIN students Student ON
                Answer.student_id = Student.id
            WHERE 
                Question.quiz_id = {$quizId} AND 
                (
                    (Choice.text = Answer.text) OR 
                    (Choice.text != Answer.text) OR
                    QuestionType.manual_scoring = 1
                )
             ";

        if (isset($filter['daterange']) and $filter['daterange'] !== 'all') {
            $sql .= 'AND ';
            switch ($filter['daterange']) {
                case 'today':
                    $sql .= "DAY(Student.submitted) = DAY(CURDATE())";
                    break;
                case 'this_week':
                    $sql .= "WEEK(Student.submitted) = WEEK(CURDATE())";
                    break;
                case 'this_month':
                    $sql .= "MONTH(Student.submitted) = MONTH(CURDATE())";
                    break;
                case 'this_year':
                    $sql .= "YEAR(Student.submitted) = YEAR(CURDATE())";
                    break;
            }
        }

        if (isset($filter['class']) and $filter['class'] !== 'all')
            $sql .= "AND Student.class = '{$filter['class']}'";


        $sql .= "       
            ORDER BY
                Question.id ASC";

        return $this->query($sql);
    }

    public function getMultipleChoiceAnswer($quizId, $filter) {
        $result = $this->find('all', array(
                'joins' => array( 
                    array( 
                        'table' => 'choices', 
                        'alias' => 'Choice', 
                        'type' => 'inner',  
                        'conditions'=> array(
                            'Choice.question_id = Question.id',
                            'Choice.points' => 1  
                        ) 
                    )
                ),
                'conditions' => array(
                    'Question.quiz_id' => $quizId,
                    'Question.question_type_id' => 3
                ),
                'fields' => array('DISTINCT Answer.*')
            )
        );
        return $result;
    }

    public function updateScore($questionId, $studentId, $score) {
        $sql = "UPDATE answers SET  score = {$score} WHERE question_id = {$questionId} AND student_id = {$studentId}";
        return $this->query($sql);
    }

}
