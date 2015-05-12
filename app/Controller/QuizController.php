<?php

class QuizController extends AppController {

    public $helpers = array('Html', 'Session', 'Form', 'Cache');
    public $uses = array('User', 'Quiz', 'QuestionType');

    public function beforeFilter() {
        parent::beforeFilter();
        $this->Auth->allow('present', 'live');
    }

    public function index() {

        $userId = $this->Auth->user('id');
        $quizTypes = $this->Quiz->quizTypes;
        $this->User->canCreateQuiz($userId);

        if ($this->request->is('post')) {
            $data = $this->request->data;
            if (isset($data['Quiz'])) {
                $filter = $data['Quiz']['status'];
                $this->Session->write('Quiz.status', $filter);
            }
        } else {
            if (!$this->Session->check('Quiz.status')) {
                $filter = 1;
                $this->Session->write('Quiz.status', $filter);
            } else {
                $filter = $this->Session->read('Quiz.status');
            }
        }

        if ($filter != 'all') {
            $options['Quiz.status'] = $filter;
            $orders = array();
        } else {
            $orders = array('Quiz.status DESC');
        }

        $options['Quiz.user_id'] = $userId;

        $quizzes = $this->Quiz->find('all', array(
            'conditions' => $options,
            'fields' => array(
                'Quiz.name',
                'Quiz.student_count', 'Quiz.id', 'Quiz.status'
            ),
            'order' => $orders,
            'recursive' => -1
        ));

        $this->User->id = $userId;
        $data = array(
            'quizzes' => $quizzes,
            'canCreateQuiz' => $this->User->canCreateQuiz()
        );
        
        $this->set(compact('data', 'quizTypes', 'filter'));
    }

    public function edit($quizId, $initial = '') {


        // Check permission
        $userId = $this->Auth->user('id');
        $result = $this->Quiz->find('count', array(
            'conditions' => array(
                'Quiz.id = ' => $quizId,
                'Quiz.user_id = ' => $userId
            )
        ));
        
        if ($result < 1)
            throw new ForbiddenException;
        
        if ($this->request->is('post')) {
            $data = $this->request->data;
            $this->Quiz->id = $quizId;
            $this->Quiz->set($data['Quiz']);
            if ($this->Quiz->validates()) {
                $this->Quiz->save();
                return $this->redirect(array('action' => 'index'));
            } else {
                $error = array();
                foreach ($this->Quiz->validationErrors as $_error) {
                    $error[] = $_error[0];
                }
                $this->Session->setFlash($error, 'error_form', array(), 'error');
                if (!empty($initial)) {
                    return $this->redirect(array('action' => 'edit', $quizId, $initial));
                } else {
                    return $this->redirect(array('action' => 'edit', $quizId));
                }
            }
        }

        $this->Quiz->Behaviors->load('Containable');
        $data = $this->Quiz->find('first', array(
            'conditions' => array(
                'id = ' => $quizId,
                'user_id = ' => $userId
            ),
            'contain' => array(
                'Question' => array(
                    'Choice',
                    'QuestionType' => array(
                        'fields' => array('template_name', 'id', 'multiple_choices')
                    )
                )
            )
        ));

        if (empty($data))
            throw new NotFoundException;

        $this->QuestionType->Behaviors->load('Containable');
        $this->QuestionType->contain();
        $data['QuestionTypes'] = $this->QuestionType->find('all', array(
            'fields' => array('name', 'template_name', 'multiple_choices', 'id')
        ));

        if (!empty($initial)) {
            $this->set(compact('initial'));
        }

        $this->set('data', $data);
    }

    public function add() {
        $userId = $this->Auth->user('id');
        if (!$this->User->canCreateQuiz($userId))
            return $this->redirect(array('action' => 'index'));

        $this->Quiz->create();
        $this->Quiz->save(array(
            'Quiz' => array(
                'name' => __('Name the quiz'),
                'user_id' => $userId
            )
        ));
        // save statistics data
        $this->loadModel('Statistic');
        $arrayToSave['Statistic']['logged_user_id'] = $this->Auth->user('id');
        $arrayToSave['Statistic']['type'] = 'quiz_create';
        $this->Statistic->save($arrayToSave);

        return $this->redirect(array(
                    'action' => 'edit',
                    $this->Quiz->id,
                    'initial'
        ));
    }

    public function present($id) {
        $data = $this->Quiz->find('count', array(
            'conditions' => array('Quiz.id' => $id)
        ));

        if ($data < 1)
            throw new NotFoundException;

        $this->set('id', $id);
    }

    public function live($quizId) {

        $this->Quiz->Behaviors->load('Containable');
        $data = $this->Quiz->find('first', array(
            'conditions' => array(
                'id = ' => $quizId,
                'Quiz.status' => 1
            ),
            'contain' => array(
                'Question' => array(
                    'Choice',
                    'QuestionType' => array(
                        'fields' => array('template_name', 'id', 'multiple_choices')
                    )
                )
            )
        ));

        if (empty($data))
            throw new NotFoundException;

        $this->disableCache();
        $this->set('data', $data);
    }

    public function finish() {
        
    }

    public function table($quizId) {
        if (empty($quizId)) {
            return $this->redirect('/');
        }


        // authenticate or not
        $checkPermission = $this->Quiz->checkPermission($quizId, $this->Auth->user('id'));
        if (empty($checkPermission)) {
            throw new ForbiddenException;
        }
        

        if ($this->request->is('post')) {
            $data = $this->request->data;
            if (isset($data['Filter'])) {
                $filter = array('class' => $data['Filter']['class'], 'daterange' => $data['Filter']['daterange']);
                $this->Session->write('Filter', $filter);
            }
        } else {
            if (!$this->Session->check('Filter')) {
                $filter = array('class' => 'all', 'daterange' => 'all');
                $this->Session->write('Filter', $filter);
            } else {
                $filter = $this->Session->read('Filter');
            }
        }

        $quizDetails = $this->Quiz->quizDetails($quizId, $filter);

        // pr($quizDetails);
        // exit;
        $classes = Hash::combine($checkPermission['Student'], '{n}.class', '{n}.class');
        $classes = Hash::merge(array('all' => __('All')), $classes);

        $this->set(compact('quizDetails', 'classes', 'filter'));
    }

    /*
    * active / deactive quiz
    */
    public function changeStatus() {

        $this->autoRender = false;
        $data = $this->request->data;
        // Check permission
        $userId = $this->Auth->user('id');
        $result = $this->Quiz->find('first', array(
            'conditions' => array(
                'Quiz.id = ' => $data['quiz_id'],
                'Quiz.user_id = ' => $userId
            ),
            'recursive' => -1
        ));
        

        if (empty($result)) {
            $response['result'] = 0;
            $response['message'] = __('You are not authorized to do this action');
            echo json_encode($response);
            exit;
        }

        $status = empty($data['status']) ? 1 : 0;
        $this->Quiz->id = $result['Quiz']['id'];
        if ($this->Quiz->saveField('status', $status)) {
             if ($this->Session->check('Quiz.status')) {
                $filter = $this->Session->read('Quiz.status');
            } else {
                $filter = 1;
            }
            $response['result'] = 1;
            $response['filter'] = $filter;
            $response['message'] = __('Operation Successfuly Done');
            echo json_encode($response);
        }
    }

    public function single() {
        $this->autoRender = false;
        $quizId = $this->request->data['quiz_id'];
        $quizInfo = $this->Quiz->find('first',
            array(
                'conditions' => array(
                    'Quiz.id' => $quizId
                    ),
                'recursive' => 2
            )
        );
        
        // response data
        $response['id'] = $quizInfo['Quiz']['id'];
        $response['quiz_name'] = $quizInfo['Quiz']['name'];
        $response['no_of_questions'] = count($quizInfo['Question']);
        $response['no_of_students'] = $quizInfo['Quiz']['student_count'];
        $answers = 0;
        foreach ($quizInfo['Question'] as $key => $value) {
            if (!empty($value['Answer'])) {
               $answers = $answers + count($value['Answer']);
            }
        }
        $response['no_of_answers'] = $answers;

        echo json_encode($response);
        exit;
    }

    public function quizDelete($quizId) {
        // authenticate or not
        $checkPermission = $this->Quiz->checkPermission($quizId, $this->Auth->user('id'));
        if (empty($checkPermission)) {
            throw new ForbiddenException;
        }
        $questionIds = $this->Quiz->Question->find('list', array('conditions' => array('Question.quiz_id' => $quizId), 'fields' => array('Question.id', 'Question.id')));
        $this->Quiz->Question->Choice->deleteAll(array('Choice.question_id' => $questionIds));
        $this->Quiz->Question->Answer->deleteAll(array('Answer.question_id' => $questionIds));
        $this->Quiz->Student->deleteAll(array('Student.quiz_id' => $quizId));
        $this->Quiz->Ranking->deleteAll(array('Ranking.quiz_id' => $quizId));
        $this->Quiz->Question->deleteAll(array('Question.quiz_id' => $quizId));
        if ($this->Quiz->delete($quizId)) {
            $this->Session->setFlash(__('You have Successfuly deleted quiz'), 'notification_form', array(), 'notification');
            return $this->redirect('/');
        }
    }

}
