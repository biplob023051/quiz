<?php
class Choice extends AppModel{
    public $belongsTo = array('Question');

    public function choicesByQuestionId ($question_id) {
    	$choices = $this->find('all', array(
            'conditions' => array('Choice.question_id' => $question_id)
            ));
    	return $choices;
    }
}
