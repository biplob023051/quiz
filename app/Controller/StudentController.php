<?php

App::uses('AppController', 'Controller');
App::uses('CakeEmail', 'Network/Email');
class StudentController extends AppController {

    public $helpers = array('Html');

    public function beforeFilter() {
        parent::beforeFilter();
        $this->Auth->allow('submit', 'success', 'update_student', 'update_answer');
    }

    // Update answer
    public function update_answer() {
        $this->autoRender = false;
        $response = array('success' => true);
        // pr($this->request->data);
        // exit;
        if (!$this->Session->check('student_id')) { // check student record
            // student record new entry
            $this->request->data['fname'] = '';
            $this->request->data['lname'] = '';
            $this->request->data['class'] = '';
            $response = $this->recordStudentData($this->request->data);
        } 

        $this->request->data['student_id'] = (int)$this->Session->read('student_id');
        $student = $this->Student->findById($this->request->data['student_id']);

        $checkbox_record_delete = $this->request->data['checkbox_record_delete'];
        $checkBox = $this->request->data['checkBox'];

        // pr($checkbox_record_delete);
        // exit;

        $data = array();
        $points = 0;
        $total_change = false;
        $ranking['Ranking'] = $student['Ranking'];

        if (!empty($student['Answer'])) { // Check if answer exist, then modify or delete
            foreach ($student['Answer'] as $key => $answer) {
                if (empty($checkBox)) { // if not check box
                    if ($answer['question_id'] == $this->request->data['question_id']) { // if question id exist
                        $data['Answer']['id'] = $answer['id'];
                        $points = empty($answer['score']) ? 0 : $answer['score']; // Need to deduct from ranking point
                    }
                } else {
                    if (($answer['question_id'] == $this->request->data['question_id']) && ($answer['text'] == $this->request->data['text'])) { // if question id exist
                        $data['Answer']['id'] = $answer['id'];
                        $points = empty($answer['score']) ? 0 : $answer['score']; // Need to deduct from ranking point
                    }
                }    
            }
            if ((!empty($checkbox_record_delete) && empty($checkBox))) {
                // Deduct point whatever it is
                $ranking['Ranking']['score'] = $ranking['Ranking']['score']-$points;
            } 
        } 


        if (empty($data)) { // New Answer
            $total_change = true;
        }

        // Compare with choice if its correct or not
        $this->loadModel('Choice');
        $choices = $this->Choice->find('all', array(
            'conditions' => array(
                'Choice.question_id' => (int)$this->request->data['question_id']
            )
        ));
        // pr($choices);
        // exit;
        $checkMax = 0;
        $correct_answer = 0;
        foreach ($choices as $key2 => $value2) {
            // get maxvalue as a total point increment
            if ($checkMax < $value2['Choice']['points']) {
                $checkMax = $value2['Choice']['points'];
            }

            if (($value2['Question']['question_type_id'] == 1) || 
                ($value2['Question']['question_type_id'] == 3)) {
                // multiple choice one or many
                if ($value2['Choice']['text'] == $this->request->data['text']) {
                    $data['Answer']['score'] = $value2['Choice']['points'];
                    if ((empty($checkbox_record_delete) && !empty($checkBox)) || (!empty($checkbox_record_delete) && empty($checkBox))) {
                        $correct_answer = $correct_answer + $value2['Choice']['points'];
                    } else {
                        $correct_answer = $correct_answer - $value2['Choice']['points'];
                    }
                } 


            } elseif ($value2['Question']['question_type_id'] == 2) { // short automatic point
                $student_answer = $this->request->data['text'];
                if (empty($this->request->data['case_sensitive'])) {
                    $student_answer = strtolower($student_answer);
                    $value2['Choice']['text'] = strtolower($value2['Choice']['text']);
                }
                $student_answer = preg_replace('/\s+/', ' ', trim($student_answer));
                $ans_string = preg_replace('/\s+/', ' ', trim($value2['Choice']['text']));

                if ($student_answer === $ans_string) { // Compare whole string
                    $data['Answer']['score'] = $value2['Choice']['points'];
                    $correct_answer = $correct_answer + $value2['Choice']['points'];
                } else {
                    $student_answer = preg_replace('/\s+/', '', $student_answer);
                    $ans_string = preg_replace('/\s+/', '', $value2['Choice']['text']);
                    $words = explode(';', $student_answer);
                    $matched_word = explode(';', $ans_string);
                    foreach ($words as $key => $value) {
                        //if (!empty($value) && (strpos(strtolower($value2['Choice']['text']), strtolower(trim($value))) !== false)) {
                        if (!empty($value) && (in_array($value, $matched_word))) {
                            $data['Answer']['score'] = $value2['Choice']['points'];
                            $correct_answer = $correct_answer + $value2['Choice']['points'];
                            break;
                        } else {
                            $data['Answer']['score'] = 0;
                        }
                    }
                }
                $data['Answer']['text'] = $this->request->data['text'];
            } elseif ($value2['Question']['question_type_id'] == 4) {
                // short manual point
                $data['Answer']['score'] = null;

            } else {
                $data['Answer']['score'] = null;
            }
        }
        // pr($checkbox_record_delete);
        // exit;

        if ((empty($checkbox_record_delete) && !empty($checkBox)) || (!empty($checkbox_record_delete) && empty($checkBox))) {
            $data['Answer']['text'] = $this->request->data['text'];
        } else {
            $data['Answer']['text'] = '';
        }

        $ranking['Ranking']['score'] = $ranking['Ranking']['score']+$correct_answer;
        
        // pr($data);
        // pr($ranking);
        // exit;

        if (empty($data['Answer']['text']) && !empty($data['Answer']['id'])) {
            // Deleted answer
            $this->Student->Answer->delete($data['Answer']['id']);
        } else { // Update or add new answer
            if (!empty($this->request->data['checkbox_record'])) {
                $data['Answer']['id'] = '';
            }
            $data['Answer']['question_id'] = (int) $this->request->data['question_id'];
            $data['Answer']['student_id'] = (int) $this->request->data['student_id'];
            $this->Student->Answer->save($data);
        }
        $this->Student->Ranking->save($ranking);

        echo json_encode($response);
        exit;
    }

    public function update_student() {
        $this->autoRender = false;
        $response = $this->recordStudentData($this->request->data);
        echo json_encode($response);
        exit;
    }

    // student information updating
    private function recordStudentData() {
        $response = array('success' => false);
        $this->loadModel('Quiz');
        if ($this->Session->check('student_id')) {
            // Update student information
            $data['Student']['id'] = (int) $this->Session->read('student_id');
        } else {
            // Find quiz id
            $quiz = $this->Quiz->findByRandomId((int)$this->request->data['random_id']);
            $questions = Hash::combine($quiz['Question'], '{n}.id', '{n}.id');
            $data['Student']['quiz_id'] = $quiz['Quiz']['id'];
            
            $total = 0;
            
            $this->loadModel('Choice');

            $choices = $this->Choice->find('all', array('conditions' => array('Choice.question_id' => $questions)));

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
            
            // save data in ranking table
            $data['Ranking']['quiz_id'] = $quiz['Quiz']['id'];
            $data['Ranking']['total'] = $total;
            $data['Ranking']['score'] = 0;
        }

        $data['Student']['fname'] = $this->request->data['fname'];
        $data['Student']['lname'] = $this->request->data['lname'];
        //$data['Student']['class'] = strtolower(preg_replace('/\s+/', '', $this->request->data['class']));
        $data['Student']['class'] = !empty($this->request->data['class']) ? strtolower(preg_replace('/\s+/', '', $this->request->data['class'])) : '';
        $data['Student']['submitted'] = date('Y-m-d H:i:s');

        $student = $this->Student->saveAssociated($data);
        if (!empty($student)) {
            // send email to the admin
            // first 3 students answer taken for any first quiz
            // access level should be free user
            if (!empty($quiz) && (empty($quiz['User']['account_level']) || ($quiz['User']['account_level'] == 22)) && ($quiz['Quiz']['student_count'] == 2)) {
                $user = $quiz['User'];
                $Email = new CakeEmail();
                $Email->viewVars(array('user' => $user));
                $Email->from(array('pietu.halonen@verkkotesti.fi' => 'WebQuiz.fi'));
                $Email->template('quiz_taken_started');
                $Email->emailFormat('html');
                $Email->to(Configure::read('AdminEmail'));
                $Email->subject(__('[Verkkotesti] Quiz given to students'));
                $Email->send();
            }
            if (!$this->Session->check('student_id')) {
                $this->Session->write('student_id', $this->Student->id);
            }
            $response['success'] = true;
            $response['student_id'] = $this->Student->id;

            $response['message'] = __('Student saved');
        } else {
            $response['message'] = __('Student saved failed');
        }
        return $response;
    }

    public function submit($quizRandomId) {

        // remove unwanted space and make uppercase for student class
        $this->request->data['Student']['class'] = !empty($this->request->data['Student']['class']) ? strtolower(preg_replace('/\s+/', '', $this->request->data['Student']['class'])) : '';
        $data = $this->request->data;
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
                        $quizRandomId
            ));
        }

        $this->loadModel('Quiz');
        $this->Quiz->unBindModelAll();
        $quiz = $this->Quiz->findByRandomId($quizRandomId);

        $this->request->data['Student']['status'] = 1;
        $this->request->data['Student']['submitted'] = date('Y-m-d H:i:s');
        unset($this->request->data['Answer']);
        
        $this->Student->save($this->request->data);
    
        // Delete session data for student quiz auto update
        $runningFor = $this->Session->read('started');
        $this->Session->delete($runningFor);
        $this->Session->delete('started');
        $this->Session->delete('student_id');

        // save std id
        if (!empty($quiz['Quiz']['show_result'])) {
            $this->Session->write('show_result', true);
            return $this->redirect(array('action' => 'success', $this->Student->id));
        } else {
            return $this->redirect(array('action' => 'success'));
        }
    }
    
    public function success($std_id = null) {
        if ($this->Session->check('show_result')) { // show result true
            $student_result = $this->Student->find('first', array(
                'conditions' => array('Student.id' => $std_id)
            ));
            $this->Student->Quiz->Behaviors->load('Containable');
            $quiz = $this->Student->Quiz->find('first', array(
                'conditions' => array(
                    'Quiz.id' => $student_result['Quiz']['id'],
                ),
                'contain' => array(
                    'Question' => array(
                        'order' => array('Question.weight DESC', 'Question.id ASC'),
                    ),
                )
            ));
            $this->set(compact('student_result', 'quiz'));
            $this->Session->delete('show_result');
        }
    }

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

    // Method of student name class update from answer table
    public function ajax_std_update() {
        $this->autoRender = false;
        $response = array('success' => false);
        $details = explode('-', $this->request->data['std_info']);
        $this->Student->Behaviors->load('Containable');
        $student_record = $this->Student->find('first', array(
            'conditions' => array(
                'Student.id' => $details[1]
            ),
            'contain' => array(
                'Quiz' => array(
                    'fields' => array('Quiz.id', 'Quiz.user_id')
                )
            ),
            'fields' => array('Student.id'),
            'recursive' => -1
        ));
        if (!empty($student_record) && ($student_record['Quiz']['user_id'] == $this->Auth->user('id'))) { // permission granted
            $this->Student->id = $student_record['Student']['id'];
            
            if ($details[0] == 'class') { // remove unwanted space and make lowercase
                $this->request->data['value_info'] = !empty($this->request->data['value_info']) ? strtolower(preg_replace('/\s+/', '', $this->request->data['value_info'])) : '';
            }

            if ($this->Student->saveField($details[0], $this->request->data['value_info'])) {
                $response['success'] = true;
                $response['changetext'] = $this->request->data['value_info'];
            } else {
                $response['message'] = __('Something wrong, please try again!');
            }
        } else {
            $response['message'] = __('Something wrong, please try again!');
        }
        echo json_encode($response);
        exit;
    }
}
