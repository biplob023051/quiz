<?php

class Quiz extends AppModel {

    // declaration of quizTypes constant
    public $quizTypes;
    
    public function __construct($id = false , $table = null , $ds = null ) {
        parent::__construct($id,$table,$ds);
        // initialize quizTypes constant
        $this->quizTypes = array('1' => __('Active Quizzes'), '0' => __('Archived Quizzes'), 'all' => __('All Quizzes'));
    }

    public $hasMany = array('Question', 'Student', 'Ranking');
    public $belongsTo = array('User');
    public $validate = array(
        'name' => array(
            'notempty' => array(
                'rule'       => 'notEmpty',
                'message'    => 'Quiz name is required',
                'allowEmpty' => false,
                'required'   => false,
            ),
            'alphaNumericWithSpace' => array(
                'rule' => array('custom', "/[a-zA-Z0-9]+/"),
                'required' => true,
                'message' => 'Quiz name must be alphanumeric'
            )
        )
    );

    public function getMaxScore($quizId) {
        $sql = "SELECT SUM(c.points) from questions q LEFT JOIN choices c ON c.question_id = q.id WHERE q.quiz_id = {$quizId} AND c.points > 0";
        $result = $this->query($sql);
        return (int) $result[0][0]['SUM(c.points)'];
    }

    public function getQuiz($quizId) {
        $sql = "SELECT * FROM questions WHERE id = {$quizId}";
        return $this->query($sql);
    }

    public function getQuestionsAndChoices($quizId) {
        $sql = "
            SELECT 
                Question.id, 
                Question.text,
                Question.explanation,
                Choice.id,
                Choice.question_id,
                Choice.text,
                Choice.points,
                QuestionType.name
            FROM 
                questions Question
            LEFT JOIN choices Choice ON Question.id = Choice.question_id
            LEFT JOIN question_types QuestionType ON QuestionType.id = Question.question_type_id
            WHERE 
                Question.quiz_id = {$quizId}";

        $results = $this->query($sql);
        $mapped = array();
        if (!empty($results)) {
            foreach ($results as $result) {
                if (!isset($mapped[$result['Question']['id']])) {
                    $mapped[$result['Question']['id']] = $result['Question'];
                    $mapped[$result['Question']['id']]['choices'] = array();
                    $mapped[$result['Question']['id']]['question_type'] = $result['QuestionType']['name'];
                }
                array_push($mapped[$result['Question']['id']]['choices'], $result['Choice']);
            }
        }

        return $mapped;
    }

    public function quizDetails($quizId, $filter) {
        $studentOptions = array();
        
        if (isset($filter['daterange']) && $filter['daterange'] !== 'all') {

            switch ($filter['daterange']) {
                case 'today':
                    if (isset($filter['class']) and $filter['class'] !== 'all') {
                        $studentOptions = array(
                            "DAY(Student.submitted) = DAY(CURDATE())",
                            "Student.class" => $filter['class']
                        );

                    } else {
                        $studentOptions = array(
                            "DAY(Student.submitted) = DAY(CURDATE())"
                        );       
                    }
                    break;
                case 'this_week':
                    if (isset($filter['class']) and $filter['class'] !== 'all') {
                        $studentOptions = array(
                            "WEEK(Student.submitted) = WEEK(CURDATE())",
                            "Student.class" => $filter['class']
                        );
                    } else {
                        $studentOptions = array(
                            "WEEK(Student.submitted) = WEEK(CURDATE())",
                        );
                    }
                    break;
                case 'this_month':
                    if (isset($filter['class']) and $filter['class'] !== 'all') {
                        $studentOptions = array(
                            "MONTH(Student.submitted) = MONTH(CURDATE())",
                            "Student.class" => $filter['class']
                        );
                    } else {
                        $studentOptions = array(
                            "MONTH(Student.submitted) = MONTH(CURDATE())",
                        );
                    }
                    break;
                case 'this_year':
                    if (isset($filter['class']) and $filter['class'] !== 'all') {
                        $studentOptions = array(
                            "YEAR(Student.submitted) = YEAR(CURDATE())",
                            "Student.class" => $filter['class']
                        );
                    } else {
                        $studentOptions = array(
                            "YEAR(Student.submitted) = YEAR(CURDATE())",
                        );
                    }
                    break;
            }
        } else {
            if (isset($filter['class']) and $filter['class'] !== 'all') {
                $studentOptions = array(
                    "Student.class" => $filter['class']
                );
            }
        }

        $this->Behaviors->load('Containable');
        $result = $this->find('first', array(
                'conditions' => array(
                    'Quiz.id' => $quizId,
                ),
                //'recursive' => 2
                'contain' => array(
                    'User', 
                    'Question' => array('Answer', 'Choice', 'QuestionType', 'order' => array('Question.weight DESC', 'Question.id ASC')), 
                    'Student' => array('conditions' => $studentOptions, 'Ranking', 'Answer'), 
                    'Ranking'
                )
            )
        );
        
        return $result;
    }

    public function checkPermission($quizId, $user_id) {
        $result = $this->findByIdAndUserId($quizId, $user_id);
        return $result;
    }

    //random text generator
    public function randText($length=40){
        $random= "";
        srand((double)microtime()*1000000);
        $strset = "1234567890";
        for($i = 0; $i < $length; $i++) {
            $random.= substr($strset,(rand()%(strlen($strset))), 1);
        }
        return $random;
    }

}
