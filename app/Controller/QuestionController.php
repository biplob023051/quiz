<?php

App::uses('AJAXController', 'Controller');

class QuestionController extends AJAXController {

    //public $helpers = array('Session');
    public $components = array('Auth', 'Session');
    public $uses = array('Quiz', 'Question', 'QuestionType');

    public function beforeFilter() {
        parent::beforeFilter();
    }

    private function isAuthorized($questionId) {

        if ($questionId == -1)
            return true;
        // get owner true or false
        $ownerId = $this->Question->getQuestionOwner($questionId, $this->Auth->user('id'));

        if (is_null($ownerId))
            return false;

        return $ownerId;
    }

    public function setPreview($questionId) {
        $this->Session->delete('Choice');
        $data = $this->request->data;
        $data['Question']['id'] = $questionId;
        $this->set('data', array(
            'success' => true,
            'Question' => $data['Question'],
            'Choice' => $data['Choice'],
            'dummy' => true
        ));
    }

    public function removeChoice() {
        $data = $this->request->data;
        if (!$this->isAuthorized($data['question_id']))
            throw new ForbiddenException;

        // keep track of choice number
        
        if ($this->Session->check('Choice.' . $data['question_id'])) {
            $choices = $this->Session->read('Choice.' . $data['question_id']);
        } else {
            $this->Session->delete('Choice');
            $choices = $this->Question->Choice->choicesByQuestionId($data['question_id']);
            $this->Session->write('Choice.' . $data['question_id'], $choices);
        }

        if ($this->Question->Choice->delete($choices[$data['choice']]['Choice']['id'])) {
            $data = $this->Question->findById($data['question_id']);
            $this->set('data', array(
                'success' => true,
                //'Question' => $data['Question'],
                'Choice' => $data['Choice']
            ));
        }
    }

    public function save($questionId) {
        $this->Session->delete('Choice');
        // If user is trying to update another user quiz, cancel.
        if (!$this->isAuthorized($questionId))
            throw new ForbiddenException;

        if (isset($this->request->data['Choice'])) {
            // reorder if order break
            $this->request->data['Choice'] = array_values($this->request->data['Choice']);
        }
        $data = $this->request->data;
// pr($data);
// exit;
        // if (empty($data['Question']['text'])) {   
        //     echo json_encode(array('success' => false, 'message' => 'Enter Question'));
        //     exit;
        // }

        if (($data['Question']['question_type_id'] == 1) || 
            ($data['Question']['question_type_id'] == 3)) {
            // multiple_one
            // multiple_many
            $isMultipleChoice = $this
                    ->Question
                    ->QuestionType
                    ->isMultipleChoice($data['Question']['question_type_id']);

            if (is_null($isMultipleChoice))
                throw new BadRequestException;

            $choiceCount = count($data['Choice']);
            if (!$isMultipleChoice) {
                for ($i = 1; $i < $choiceCount; ++$i) {
                    unset($data['Choice'][$i]);
                }
                $choiceCount = 1;
            }

            for ($i = 0; $i < $choiceCount; ++$i) {
                if (empty($data['Choice'][$i]['points']))
                    $data['Choice'][$i]['points'] = 0;

                if (empty($data['Choice'][$i]['text']))
                    $data['Choice'][$i]['text'] = __('Choice %d', $i);

                $data['Choice'][$i]['question_id'] = $questionId;
                unset($data['Choice'][$i]['id']);
            }
        } elseif($data['Question']['question_type_id'] == 2) {
            // short_auto
            if (empty($data['Choice'][0]['text'])) {   
                echo json_encode(array('success' => false, 'message' => 'Enter correct answers!'));
                exit;
            }
            if (empty($data['Choice'][0]['points'])) {   
                echo json_encode(array('success' => false, 'message' => 'Enter point!'));
                exit;
            }
        } elseif($data['Question']['question_type_id'] == 4) {
            // short_manual
            $data['Choice'][0]['text'] = 'Short_manual';
            $data['Choice'][0]['points'] = !empty($data['Choice'][0]['points']) ? $data['Choice'][0]['points'] : 0;
        } else {
            // essay
            if (!(isset($data['isNew']) && $data['isNew']) || $questionId != -1) {
                $data['Choice'][0]['points'] = $data['Choice'][0]['text'];
                $data['Choice'][0]['text'] = 'Essay';
            } else {
                $data['Choice'][0]['points'] = !empty($data['Choice'][0]['text']) ? $data['Choice'][0]['text'] : 0;
                $data['Choice'][0]['text'] = 'Essay';
            }
        }

        // If user leave form empty, set the default
        if (empty($data['Question']['text']))
            $data['Question']['text'] = __('New Question');

        // If we are editing a existing question, set the ID
        if (!(isset($data['isNew']) && $data['isNew']) || $questionId != -1) {
            $this->Question->Choice->deleteAll(array(
                'Choice.question_id' => $questionId
            ));

            $data['Question']['id'] = $questionId;
            $this->Question->id = $questionId;
        }

        if ($this->Question->saveAssociated($data)) {
            $data['Question']['id'] = $this->Question->id;
            if (isset($this->request->data['is_sort'])) { // if choice sorting exist then rearrange array by weight
                // sort by weight asc
                usort($data['Choice'], function($a, $b) {
                    return $a['weight'] - $b['weight'];
                });
                // weight desc
                $data['Choice'] = array_reverse($data['Choice']);
            }

            $this->set('data', array(
                'success' => true,
                'Question' => $data['Question'],
                'Choice' => $data['Choice']
            ));
        }
    }

    public function delete() {
        $questionId = $this->request->data['id'];

        // If user is trying to delete another user quiz, cancel.
        if (!$this->isAuthorized($questionId))
            throw new ForbiddenException;

        if ($this->Question->delete($questionId)) {
            // delete choices related to this question
            $this->Question->Choice->deleteAll(array('Choice.question_id' => $questionId));
            // delete answers related to this question
            $this->Question->Answer->deleteAll(array('Answer.question_id' => $questionId));
            $this->set('data', array(
                'success' => true
            ));
        }
    }

    // ajax sorting question on drag drop
    public function ajax_sort() {
        $question_ids = $this->request->data['question_ids'];
        $max_weight = count($question_ids);
        foreach ($question_ids as $key => $id) {
            $this->Question->id = $id;
            $this->Question->saveField('weight', $max_weight--);
        }
        $this->set('data', array(
            'success' => true,
            'no' => count($question_ids)
        ));
    }

}
