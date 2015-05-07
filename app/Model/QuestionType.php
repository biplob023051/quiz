<?php

class QuestionType extends AppModel {

    public $hasOne = array('Question');
    
    public function isMultipleChoice($questionTypeId) {
        $result = $this->find('first', array(
            'conditions' => array(
                'QuestionType.id = ' => $questionTypeId,
            ),
            'fields' => 'multiple_choices',
            'recursive' => -1
        ));
        
        if(empty($result))
            return NULL;
        
        return ($result['QuestionType']['multiple_choices']);
    }
    
}
