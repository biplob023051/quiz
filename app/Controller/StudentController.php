<?php

App::uses('AppController', 'Controller');

class StudentController extends AppController {

    public $helpers = array('Html');

    public function beforeFilter() {
        $this->Auth->allow();
    }

    public function submit($quizId) {

        $data = $this->request->data;

        $this->Student->set($data['Student']);
        if (!$this->Student->validates()) {
            $error = array();
            foreach ($this->Student->validationErrors as $_error) {
                $error[] = $_error[0];
            }
            $this->Session->write('FormData', $data);
            return $this->redirect(array(
                        'controller' => 'Quiz',
                        'action' => 'live',
                        $quizId
            ));
        }

        // if empty for multiple choice set empty text and 0 score
        foreach ($data['Answer'] as $key => $value) {
            if (!isset($value['text'])) {
                $data['Answer'][$key]['text'] = '';
                $data['Answer'][$key]['score'] = 0;
            }
        }

        $i = 0;
        $answers = array();
        foreach ($data['Answer'] as $answer) {
            if (!isset($answer['text']) or ! isset($answer['question_id']))
                continue;

            if (is_array($answer['text'])) {
                foreach ($answer['text'] as $text) {
                    $answers[$i++] = array(
                        'question_id' => $answer['question_id'],
                        'text' => $text
                    );
                }
            } else {
                $answers[$i++] = $answer;
            }
        }
        
        $data['Answer'] = $answers;
        $data['Student']['submitted'] = date('Y-m-d H:i:s');
        $data['Student']['quiz_id'] = $quizId;

        $this->loadModel('Quiz');

        $quiz = $this->Quiz->findById($quizId);

        $questions = Hash::combine($quiz['Question'], '{n}.id', '{n}.id');

        $this->loadModel('Choice');

        $choices = $this->Choice->find('all', array('conditions' => array('Choice.question_id' => $questions)));

        $correct_answer = 0;

        $this->loadModel('QuestionType');
        
        foreach ($data['Answer'] as $key1 => $value1) {
            foreach ($choices as $key2 => $value2) {
                if ($value2['Choice']['question_id'] == $value1['question_id']) {
                    if (($value2['Question']['question_type_id'] == 1) || 
                        ($value2['Question']['question_type_id'] == 3)) {
                        // multiple choice one or many
                        if ($value2['Choice']['text'] == $value1['text']) {
                            $data['Answer'][$key1]['score'] = $value2['Choice']['points'];
                            if ($value2['Choice']['points'] != 0) {
                                $correct_answer = $correct_answer + $value2['Choice']['points'];
                            }
                        }
                    } elseif ($value2['Question']['question_type_id'] == 2) {
                        // short automatic point
                        $words = explode(',', $value1['text']);
                        if (count($words) == 1) {
                            $words = explode(' ', $value1['text']);
                        }
                        
                        foreach ($words as $key => $value) {
                            if (!empty($value) && (strpos(strtolower($value2['Choice']['text']), strtolower($value)) !== false)) {
                                $data['Answer'][$key1]['score'] = $value2['Choice']['points'];
                                $correct_answer = $correct_answer + $value2['Choice']['points'];
                                break;
                            } else {
                                $data['Answer'][$key1]['score'] = 0;
                            }
                        }

                    } elseif ($value2['Question']['question_type_id'] == 4) {
                        // short manual point
                        $manual_scoring_short = $this->QuestionType->findById(4, array('QuestionType.manual_scoring'));
                        if (!empty($value1['text'])) {
                            $data['Answer'][$key1]['score'] = $manual_scoring_short['QuestionType']['manual_scoring'];
                            $correct_answer = $correct_answer + $manual_scoring_short['QuestionType']['manual_scoring'];
                        } else {
                            $data['Answer'][$key1]['score'] = 0;
                        }

                    } else {
                        $manual_scoring_essay = $this->QuestionType->findById(5, array('QuestionType.manual_scoring'));
                        if (!empty($value1['text'])) {
                            $data['Answer'][$key1]['score'] = $manual_scoring_essay['QuestionType']['manual_scoring'];
                            $correct_answer = $correct_answer + $manual_scoring_essay['QuestionType']['manual_scoring'];
                        } else {
                            $data['Answer'][$key1]['score'] = 0;
                        }
                    }
                } 
            }
        }

        $total = 0;
        foreach ($choices as $key => $value) {
            if (($value['Question']['question_type_id'] == 1) || 
                ($value['Question']['question_type_id'] == 3) || ($value['Question']['question_type_id'] == 2)) {
                if ($value['Choice']['points'] > 0) {
                    $total = $total + $value['Choice']['points'];    
                }
                    
            } elseif ($value['Question']['question_type_id'] == 4) {
                if (!empty($value['Choice']['points'])) {
                    $total = $total + $value['Choice']['points'];
                } else {
                    $total = $total + $manual_scoring_short['QuestionType']['manual_scoring'];
                }
            } else {
                if (!empty($value['Choice']['points'])) {
                    $total = $total + $value['Choice']['points'];
                } else {
                    $total = $total + $manual_scoring_essay['QuestionType']['manual_scoring'];
                }
            }
        }

        $correct_answer = $correct_answer < 0 ? 0 : $correct_answer;

        // pr($data);
        // exit;
        // save data in ranking table
        $data['Ranking']['quiz_id'] = $quizId;
        $data['Ranking']['total'] = $total;
        $data['Ranking']['score'] = $correct_answer;
        
        if ($this->Student->saveAssociated($data)) {
            return $this->redirect(array('action' => 'success'));
        } else {
            return $this->redirect(array('action' => 'failed'));
        }
    }
    
    public function success() {}
}
