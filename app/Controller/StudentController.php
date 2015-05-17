<?php

App::uses('AppController', 'Controller');

class StudentController extends AppController {

    public $helpers = array('Html');

    public function beforeFilter() {
        $this->Auth->allow('submit', 'success');
    }

    public function submit($quizId) {

        $data = $this->request->data;
        
        // remove unwanted space and make uppercase for student class
        $data['Student']['class'] = strtoupper(preg_replace('/\s+/', '', $data['Student']['class']));

        $this->Student->set($data['Student']);
        if (!$this->Student->validates()) {
            $error = array();
            foreach ($this->Student->validationErrors as $_error) {
                $error[] = $_error[0];
            }
            $this->Session->write('FormData', $data);
            $this->Session->setFlash($error, 'error_form', array(), 'error');
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
                        $data['Answer'][$key1]['score'] = 0;

                    } else {
                        $manual_scoring_essay = $this->QuestionType->findById(5, array('QuestionType.manual_scoring'));
                        $data['Answer'][$key1]['score'] = 0;
                    }
                } 
            }
        }

        $total = 0;
        //pr($choices);
        $checkQuestion = array();
        foreach ($choices as $key => $value) {
            if ($value['Question']['question_type_id'] == 1) {
                if (!in_array($value['Question']['id'], $checkQuestion)) {
                    array_push($checkQuestion, $value['Question']['id']);
                    $checkMax = 0;
                    foreach ($choices as $key1 => $value1) {
                        if ($value1['Question']['question_type_id'] == 1) {
                            if ($value['Question']['id'] == $value1['Question']['id']) {
                                if ($value1['Choice']['points'] > 0) {
                                    $checkMax = $checkMax < $value1['Choice']['points'] ? $value1['Choice']['points'] : $checkMax;    
                                }
                            }
                        }
                    } 
                    $total = $total + $checkMax;   
                }
                    
            } elseif (($value['Question']['question_type_id'] == 3) || ($value['Question']['question_type_id'] == 2)) {
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

    public function deleteStudent() {
        $this->autoRender = false;
        $response = array('success' => false);
        $student_id = $this->request->data['student_id'];
        $studentInfo = $this->Student->find('first', array(
                'conditions' => array(
                    'Student.id' => $student_id
                )
            )
        );

        if ($studentInfo['Quiz']['user_id'] == $this->Auth->user('id')) {
            $answerIds = Hash::combine($studentInfo['Answer'], '{n}.id', '{n}.id');
            // delete ranking data
            $this->Student->Ranking->delete($studentInfo['Ranking']['id']);
            // delete answer data
            $this->Student->Answer->deleteAll(array('Answer.id' => $answerIds));

            if ($this->Student->delete($student_id)) {
                $response['success'] = true;
                $response['message'] = __('Successfully removed');
            }

        } else {
            $response['message'] = __('You are not authorized to continue this operation!');
        } 

        echo json_encode($response);
        exit;
    }

    public function confirmDeleteStudent() {
        $this->autoRender = false;
        $response = array('success' => false);
        $student_id = $this->request->data['student_id'];
        $studentInfo = $this->Student->find('first', array(
                'conditions' => array(
                    'Student.id' => $student_id
                )
            )
        );
        if (!empty($studentInfo)) {
            $response['success'] = true;
            $response['student_id'] = $studentInfo['Student']['id'];
            $response['student_full_name'] = $studentInfo['Student']['fname'] . ' ' . $studentInfo['Student']['lname'];
            $response['student_class'] = $studentInfo['Student']['class'];
            $response['student_score'] = $studentInfo['Ranking']['score'] . '/' . $studentInfo['Ranking']['total'];
        } 
        echo json_encode($response);
        exit;
    }
}
