<?php
App::uses('CakeEmail', 'Network/Email');
class QuizController extends AppController {

    public $helpers = array('Html', 'Session', 'Form', 'Cache');
    public $uses = array('User', 'Quiz', 'QuestionType');

    public function beforeFilter() {
        parent::beforeFilter();
        $this->Auth->allow('present', 'live', 'no_permission');
    }

    public function index() {

        $userId = $this->Auth->user('id');
        $quizTypes = $this->Quiz->quizTypes;

        $userPermissions = $this->userPermissions();
        $this->set(compact('userPermissions'));

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
        );

        $lang_strings['delete_quiz_1'] = __('There are ');
        $lang_strings['delete_quiz_2'] = __(' answers, ');
        $lang_strings['delete_quiz_3'] = __(' students, and ');
        $lang_strings['delete_quiz_4'] = __(' number of questions. This can not be undone. Are you sure want to delete?');
        $lang_strings['delete_quiz_5'] = __('Delete quiz ');
        $lang_strings['request_sent'] = __('Upgrade Pending');

        $this->set(compact('data', 'quizTypes', 'filter', 'lang_strings'));
    }

    public function edit($quizId, $initial = '') {

        $this->accountStatus();
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
                    'Choice' => array('order' => array('Choice.weight DESC', 'Choice.id ASC')),
                    'QuestionType' => array(
                        'fields' => array('template_name', 'id', 'multiple_choices')
                    ),
                    'order' => array('Question.weight DESC', 'Question.id ASC')
                )
            )
        ));

        if (empty($data))
            throw new NotFoundException;

        $this->QuestionType->Behaviors->load('Containable');
        $this->QuestionType->contain();
        $data['QuestionTypes'] = $this->QuestionType->find('all', array(
            'fields' => array('name', 'template_name', 'multiple_choices', 'id', 'type')
        ));

        if (!empty($initial)) {
            $this->set(compact('initial'));
        }

        $lang_strings['empty_question'] = __('Empty Question Is Not Permit');
        $lang_strings['same_choice'] = __('Empty or Same Choices Are Not Permit');
        $lang_strings['single_greater'] = __('At least a point should be greater than 0');
        $lang_strings['correct_answer'] = __('Enter correct answers, if multiple answers comma separated');
        $lang_strings['point_greater'] = __('At least point should be greater than 0');
        $lang_strings['two_greater'] = __('At least 2 points should be greater than 0');
        $lang_strings['insert_another'] = __('You put only one correct answers, please choose another point greater than 0!!!');
        $lang_strings['youtube_url'] = __('Please enter Youtube url');
        $lang_strings['image_url'] = __('Please enter image url');
        $lang_strings['header_q_title'] = __('Enter the header');
        $lang_strings['other_q_title'] = __('Enter the question');

        $lang_strings['youtube_exp_text'] = __('Video explanation text');
        $lang_strings['other_exp_text'] = __('Explanation text');
        $lang_strings['empty_header'] = __('Please enter Header text');


        $this->set('data', $data);
        $this->set(compact('lang_strings'));
    }

    public function add() {
        $this->accountStatus(); 

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
        // save random number as random_id
        $random_id = $this->Quiz->id . $this->Quiz->randText(2);
        $this->Quiz->saveField('random_id', $random_id);
        // save statistics data
        $this->loadModel('Statistic');
        $arrayToSave['Statistic']['user_id'] = $this->Auth->user('id');
        $arrayToSave['Statistic']['type'] = 'quiz_create';
        $this->Statistic->save($arrayToSave);

        // check if free user creating first quiz send email notification to admin
        $user = $this->Auth->user();
        if (empty($user['account_level'])) { // if this is the free user
            $Email = new CakeEmail();
            $Email->viewVars(array('user' => $user));
            $Email->from(array('pietu.halonen@verkkotesti.fi' => 'WebQuiz.fi'));
            $Email->template('first_quiz_create');
            $Email->emailFormat('html');
            $Email->to(Configure::read('AdminEmail'));
            $Email->subject(__('[Verkkotesti] First quiz created'));
            $Email->send();
        }

        return $this->redirect(array(
                    'action' => 'edit',
                    $this->Quiz->id,
                    'initial'
        ));
    }

    public function present($id) {
        $quiz = $this->Quiz->find('first', array(
            'conditions' => array('Quiz.id' => $id),
            'recursive' => -1
        ));

        if (empty($quiz))
            throw new NotFoundException;
        $this->set(compact('quiz', 'id'));
    }

    public function live($quizRandomId) {

        $this->Quiz->Behaviors->load('Containable');
        $this->Quiz->bindModel(
               array(
                 'belongsTo'=>array(
                     'User'=>array(
                       'className'  =>  'User',
                       'foreignKey' => 'user_id'
                   )          
               )
            ), false // Note the false here!
        );
        $data = $this->Quiz->find('first', array(
            'conditions' => array(
                'random_id = ' => $quizRandomId,
                'Quiz.status' => 1
            ),
            'contain' => array(
                'Question' => array(
                    'Choice' => array('order' => array('Choice.weight DESC', 'Choice.id ASC')),
                    'QuestionType' => array(
                        'fields' => array('template_name', 'id', 'multiple_choices')
                    ),
                    'order' => array('Question.weight DESC', 'Question.id ASC')
                ),
                'User'
            )
        ));

        if (empty($data)) {
            $this->set('title_for_layout', __('Closed'));
            $this->render('not_found');
        } else {
            // check user access level
            if ((($data['User']['account_level'] == 0) || 
                (($data['User']['account_level'] == 1) && (strtotime($data['User']['expired']) < time()))) 
                && ($data['Quiz']['student_count'] >= 40)) {
                $this->Session->setFlash(__('Sorry, only allow 40 students to take this quiz.'), 'error_form', array(), 'error');
                return $this->redirect(array('controller' => 'quiz', 'action' => 'no_permission'));
            }

            $lang_strings[0] = __('Internet connection has been lost, please try again later.');
            $lang_strings[1] = __('All questions answered. Turn in your quiz?');
            $lang_strings[2] = __('Questions ');
            $lang_strings[3] = __(' unanswered.');
            $lang_strings[4] = __(' Turn in your quiz?');
            $lang_strings[5] = __('First Name is Required');
            $lang_strings[6] = __('Last Name is Required');
            $lang_strings[7] = __('Class is Required');
            $lang_strings['last_name_invalid'] = __('Invalid Last Name');
            $lang_strings['first_name_invalid'] = __('Invalid First Name');
            $lang_strings['class_invalid'] = __('Invalid Class');
            $lang_strings['right_click_disabled'] = __('Sorry, right click disabled');
            $lang_strings['browser_switch'] = __('Sorry, you are not allowed to switch browser tab');
            $lang_strings['leave_quiz'] = __('Are you sure that you want to leave this quiz?');

            $this->disableCache();
            $this->set('data', $data);
            $this->set(compact('lang_strings'));
        }
    }

    public function finish() {
        
    }

    public function table($quizId) {
        if (empty($quizId)) {
            return $this->redirect('/');
        }
        $this->accountStatus();


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
        // get student id's for ajax auto checking
        $studentIds = Hash::combine($quizDetails['Student'], '{n}.id', '{n}.id');
        $studentIds = json_encode($studentIds);
        // get student classes
        $classes = Hash::combine($checkPermission['Student'], '{n}.class', '{n}.class');
        
        function cmp($a, $b) {
            if ($a == $b) {
                return 0;
            }
            return ($a < $b) ? -1 : 1;
        }
        uasort($classes, 'cmp');
        // classes merge with all class
        
        $classes = Hash::merge(array('all' => __('All Classes')), $classes);

        $lang_strings['remove_question'] = __('Are you sure you want to remove ');
        $lang_strings['with_points'] = __(') answer with points ');
        $lang_strings['positive_number'] = __('Please Give a postive number!');
        $lang_strings['update_require'] = __('You have not updated score yet!');
        $lang_strings['more_point_1'] = __('Points not allowed more than ');
        $lang_strings['more_point_2'] = __(' value');

        $this->set(compact('quizDetails', 'classes', 'filter', 'studentIds', 'quizId', 'lang_strings'));
    }

    public function ajax_latest() {
        $this->autoRender = false;
        if (!$this->Session->check('Filter')) {
            $filter = array('class' => 'all', 'daterange' => 'all');
            $this->Session->write('Filter', $filter);
        } else {
            $filter = $this->Session->read('Filter');
        }
        $quizDetails = $this->Quiz->quizDetails((int) $this->request->data['quizId'], $filter);
        // get student id's for ajax auto checking
        $studentIds = Hash::combine($quizDetails['Student'], '{n}.id', '{n}.id');
       
        echo json_encode($studentIds);
    }

    public function ajax_update() {
        // authenticate or not
        $checkPermission = $this->Quiz->checkPermission((int)$this->request->data['quizId'], $this->Auth->user('id'));
        if (empty($checkPermission)) {
            throw new ForbiddenException;
        }

        $currentTab = $this->request->data['currentTab'];

        if (!$this->Session->check('Filter')) {
            $filter = array('class' => 'all', 'daterange' => 'all');
            $this->Session->write('Filter', $filter);
        } else {
            $filter = $this->Session->read('Filter');
        }
        $quizDetails = $this->Quiz->quizDetails((int)$this->request->data['quizId'], $filter);
        

        $studentIds = Hash::combine($quizDetails['Student'], '{n}.id', '{n}.id');
        $studentIds = json_encode($studentIds);
        // get student classes
        $classes = Hash::combine($checkPermission['Student'], '{n}.class', '{n}.class');
        // classes merge with all class
        function cmp($a, $b) {
            if ($a == $b) {
                return 0;
            }
            return ($a < $b) ? -1 : 1;
        }
        uasort($classes, 'cmp');
        $classes = Hash::merge(array('all' => __('All Classes')), $classes);

        $this->set(compact('quizDetails', 'classes', 'filter', 'studentIds', 'quizId', 'currentTab'));
    }

    /*
    * active / deactive quiz
    */
    public function changeStatus() {
        $this->accountStatus(); 

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
        $this->accountStatus(); 
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

    public function no_permission() {
        $this->set('title_for_layout', __('No permission'));
    }

    // print quiz answer
    public function ajax_print_answer() {
        $this->accountStatus();
        $this->layout = "ajax";
        $quizId = $this->request->data['quizId'];
        // authenticate or not
        $checkPermission = $this->Quiz->checkPermission($quizId, $this->Auth->user('id'));
        if (empty($checkPermission)) {
            throw new ForbiddenException;
        }
        $filter = $this->Session->read('Filter');
        $quizDetails = $this->Quiz->quizDetails($quizId, $filter);
        $this->set(compact('quizDetails', 'quizId'));
    }

}
