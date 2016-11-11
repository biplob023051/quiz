<?php
App::uses('CakeEmail', 'Network/Email');
class QuizController extends AppController {
    // Paginator property
    public $components = array('RequestHandler', 'Paginator', 'Email');
    public $paginate = array(
        'limit' => 4
    );
    // End of pagination

    public $helpers = array('Html', 'Session', 'Form', 'Cache', 'Js' => array('Jquery'));
    public $uses = array('User', 'Quiz', 'QuestionType');

    public function beforeFilter() {
        parent::beforeFilter();
        $this->Auth->allow('present', 'live', 'no_permission');
    }

    public function ajax_student_update() {
        $this->layout = 'ajax';
        $student = $this->Quiz->Student->findById($this->request->data['student_id']);
        $student['id'] = $student['Student']['id'];
        $student['fname'] = $student['Student']['fname'];
        $student['lname'] = $student['Student']['lname'];
        $student['class'] = $student['Student']['class'];
        $student['submitted'] = $student['Student']['submitted'];
        $student['quiz_id'] = $student['Student']['quiz_id'];
        $student['status'] = $student['Student']['status'];
        unset($student['Student']);
        
        $this->set('value1', $student);
        $filter = $this->Session->read('Filter');
        $quizDetails = $this->Quiz->quizDetails($student['quiz_id'], $filter);
        $sl = (int)$this->request->data['sl'];
        $this->set(compact('quizDetails','sl'));
    }

    public function index() {
        $userId = $this->Auth->user('id');
        $this->set('quizTypes', $this->Quiz->quizTypes);
        $this->set('quizSharedType', $this->Quiz->quizSharedType);

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

        $orders = array();

        switch ($filter) {
            case 'all':
                $orders = array('Quiz.status DESC');
                break;
            case 'shared':
                $options[] = array(
                    'Quiz.shared' => 1,
                    'Quiz.is_approve' => 1
                );
                break;
            case 'pending':
                $options[] = array(
                    'Quiz.shared' => 1,
                    'Quiz.is_approve' => NULL
                );
                break;
            case 'decline':
                $options[] = array(
                    'Quiz.shared' => 1,
                    'Quiz.is_approve' => 2
                );
                break;
            case 'private':
                $options[] = array(
                    'Quiz.shared' => NULL
                );
                break;
            default:
                $options['Quiz.status'] = $filter;
                break;
        }

        $options['Quiz.user_id'] = $userId;

        $this->Quiz->virtualFields = array(
            'question_count' => 'SELECT count(*) FROM questions as Question WHERE Question.quiz_id = Quiz.id'
        );

        $quizzes = $this->Quiz->find('all', array(
            'conditions' => $options,
            'fields' => array(
                'Quiz.name', 'Quiz.student_count', 'Quiz.id', 'Quiz.status', 'Quiz.shared', 'Quiz.is_approve', 'Quiz.question_count', 'Quiz.comment'
            ),
            'order' => $orders,
            'recursive' => -1
        ));
// pr($quizzes);
// exit;
        $this->User->id = $userId;
        $data = array(
            'quizzes' => $quizzes,
        );

        $this->set('quiz_created', $this->Quiz->findByUserId($this->Auth->user('id'), array('Quiz.id')));

        $lang_strings['delete_quiz_1'] = __('There are ');
        $lang_strings['delete_quiz_2'] = __(' answers, ');
        $lang_strings['delete_quiz_3'] = __(' students, and ');
        $lang_strings['delete_quiz_4'] = __(' number of questions. This can not be undone. Are you sure want to delete?');
        $lang_strings['delete_quiz_5'] = __('Delete quiz ');
        $lang_strings['request_sent'] = __('Upgrade Pending');
        $lang_strings['share_quiz'] = __('Share quiz');
        $lang_strings['share_quiz_question'] = __('Do you want to share the quiz?');
        $lang_strings['remove_share'] = __('Cancel share quiz');
        $lang_strings['remove_share_question'] = __('Do you want to remove sharing the quiz?');
        $lang_strings['remove_shared_quiz'] = __('Cancel share');
        $lang_strings['check_select'] = __('Please choose at least one quiz to import!');
        $lang_strings['import_success'] = __('Quiz imported successfully');

        $this->set(compact('data', 'filter', 'lang_strings'));
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
            if (!empty($this->request->data['Quiz']['subjects'])) {
                $this->request->data['Quiz']['subjects'] = json_encode($this->request->data['Quiz']['subjects'], true);
            }

            if (!empty($this->request->data['Quiz']['classes'])) {
                $this->request->data['Quiz']['classes'] = json_encode($this->request->data['Quiz']['classes'], true);
            }

            $data = $this->request->data;
            // pr($data);
            // exit;
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

        if (empty($data['Question'])) {
            $this->set('no_question', true);
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
        $lang_strings['image_exp_text'] = __('Image explanation text');
        $lang_strings['other_exp_text'] = __('Explanation text');
        $lang_strings['empty_header'] = __('Please enter Header text');

        // Load available classes (created by admin)
        $this->loadModel('Subject');
        $classOptions = $this->Subject->find('list', array(
            'conditions' => array(
                'Subject.isactive' => 1,
                'Subject.is_del' => NULL,
                'Subject.type' => 1
            ),
            'recursive' => -1
        ));

        $subject_cond[] = array(
            'Subject.isactive' => 1,
            'Subject.is_del' => NULL,
            'Subject.type' => NULL
        );
        $c_user = $this->Session->read('Auth.User');
        if (!empty($c_user['subjects'])) {
            $selectedSubjects = json_decode($c_user['subjects'], true);
            $subject_cond[] = array('Subject.id' => $selectedSubjects);
        }

        $subjectOptions = $this->Subject->find('list', array(
            'conditions' => $subject_cond,
            'recursive' => -1
        ));

        if (!empty($subjectOptions)) {
            $subjectOptions = array(0 => __('All Subjects')) + $subjectOptions;
        }
        
        if (!empty($classOptions)) {
            $classOptions = array(0 => __('All Classes')) + $classOptions;
        }

        // pr($this->Session->read('Auth.User'));
        // exit;

        $this->set('data', $data);
        $this->set(compact('lang_strings', 'classOptions', 'subjectOptions'));
    }

    public function add() {
        
        $this->accountStatus(); 

        $userId = $this->Auth->user('id');
        // if (!$this->User->canCreateQuiz($userId))
        //     return $this->redirect(array('action' => 'index'));  

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
        if (empty($user['account_level']) || ($user['account_level'] == 22)) { // if this is the free user
            // check if its first quiz
            $quiz_count = $this->User->Quiz->find('count', array(
                'conditions' => array(
                    'Quiz.user_id' => $this->Auth->user('id')
                )
            ));
            if ($quiz_count == 1) {
                $Email = new CakeEmail();
                $Email->viewVars(array('user' => $user));
                $Email->from(array('pietu.halonen@verkkotesti.fi' => 'WebQuiz.fi'));
                $Email->template('first_quiz_create');
                $Email->emailFormat('html');
                $Email->to(Configure::read('AdminEmail'));
                $Email->subject(__('[Verkkotesti] First quiz created'));
                $Email->send();
            } 
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

    // ajax_preview
    public function ajax_preview() {
        $this->layout = 'ajax';
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
                'Quiz.random_id = ' => $this->request->data['random_id'],
                'Quiz.shared' => 1,
                'Quiz.is_approve' => 1
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

        // pr($data);
        // exit;

        if (empty($data)) {
            $this->set('isError', true);
        } else {
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
            $this->set(compact('lang_strings', 'data'));
            $this->set('quizRandomId', $this->request->data['random_id']);

        }
    }

    public function live($quizRandomId) {

        // start session for examination
        if (!$this->Session->check('started')) {
            $this->Session->destroy();
            $randomString = $this->randText(10);
            $this->Session->write('started', $randomString);
            $this->Session->write('random_id', $quizRandomId); // Write random_id on session to keey track of online students
            $this->redirect(array('controller' => 'quiz', 'action' => 'live', $quizRandomId, '?' => array('runningFor' => $randomString)));
        }

        if (!empty($this->request->query['runningFor']) && ($this->request->query['runningFor'] == $this->Session->read('started'))) {
            // Do nothing
            //$this->Session->delete($this->request->query['runningFor']);
        } else {
            // remove session and start new
            $this->Session->delete('started');
            $this->Session->delete('student_id');
            $this->Session->delete('random_id');
            $this->Session->destroy();
            $randomString = $this->randText(10);
            $this->Session->write('started', $randomString);
            $this->Session->write('random_id', $quizRandomId);
            $this->redirect(array('controller' => 'quiz', 'action' => 'live', $quizRandomId, '?' => array('runningFor' => $randomString)));
        }

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

            // Check session if student page reloaded
            if ($this->Session->check('student_id')) {
                $student = $this->Quiz->Student->find('first', array(
                    'conditions' => array(
                        'Student.id' => (int) $this->Session->read('student_id')
                    ),
                    'contain' => array('Answer', 'Ranking')
                ));
                $this->request->data = $student;
                // pr($this->request->data);
                // exit;
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
            $this->set(compact('quizRandomId'));

        }
    }

    public function finish() {
        
    }

    public function check_diff_multi($array1, $array2){
        $result = array();
        foreach($array1 as $key => $val) {
             if(isset($array2[$key])){
               if(is_array($val) && $array2[$key]){
                   $result[$key] = $this->check_diff_multi($val, $array2[$key]);
               }
           } else {
               $result[$key] = $val;
           }
        }

        return $result;
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
                $filter = array('class' => !empty($data['Filter']['class']) ? $data['Filter']['class'] : 'all', 'daterange' => $data['Filter']['daterange']);
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
        $studentIds = array();
        foreach ($quizDetails['Student'] as $key1 => $value1) {
            $informations = array();
            $informations[] = array('fname' => $value1['fname'], 'lname' => $value1['lname'], 'class' => $value1['class']);
            $answers = array();
            foreach ($value1['Answer'] as $key2 => $value2) {
                $answers[$value2['question_id']] = $value2['text'];
            }
            $informations[] = $answers;
            $studentIds[$value1['id']] = $informations;
        }
    
        
        // find online students
        $onlineStds = $this->checkOnlineStudent($quizDetails['Quiz']['random_id']);
        $this->set(compact('onlineStds'));

        $studentIds = json_encode(array('studentIds' => $studentIds, 'onlineStds' => $onlineStds));

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
       
        // $class_ar = array();
        // foreach ($classes as $key => $ind_class) {
        //     $new_class = strtolower(preg_replace('/\s+/', '', $ind_class));
        //     if (!in_array($new_class, $class_ar)) {
        //         $class_ar[$new_class] = $new_class;
        //     }
        // }
        
        $classes = Hash::merge(array('all' => __('All Classes')), $classes);

        $lang_strings['remove_question'] = __('Are you sure you want to remove ');
        $lang_strings['with_points'] = __(') answer with points ');
        $lang_strings['positive_number'] = __('Please Give a postive number!');
        $lang_strings['update_require'] = __('You have not updated score yet!');
        $lang_strings['more_point_1'] = __('Points not allowed more than ');
        $lang_strings['more_point_2'] = __(' value');
        $lang_strings['online_warning'] = __('Student is online and giving test! ');

        $this->set(compact('quizDetails', 'classes', 'filter', 'studentIds', 'quizId', 'lang_strings'));
    }

    // Student online status
    public function checkOnlineStudent($random_id) {
        $onlineStds = array();
        //$time = time()+14400-60; // (14400 == 4 huors and 10 mins = 600)
        $time = time()+14400-60; // (14400 == 4 huors and 10 mins = 600)
        $sessions = $this->Quiz->query('SELECT data FROM ' . $this->Quiz->tablePrefix . 'cake_sessions WHERE expires > ' . $time . ' AND data LIKE ' . "'%" . '"' . $random_id . '"' . "%'");
        // pr($sessions);
        // exit;
        foreach ($sessions as $session) {
            $tmp = explode(';random_id|', $session[$this->Quiz->tablePrefix . 'cake_sessions']['data']);
            if (!empty($tmp[1]) && (strpos($tmp[1], 'student_id') !== false)) {
                $tmp = explode('student_id', $tmp[1]);
                $q_random_id = explode('"', $tmp[0]);
                $q_student_id = explode('"', $tmp[1]);
                if (!empty($q_random_id[1]) && ($q_random_id[1] == $random_id) && !empty($q_student_id[1])) {
                    $onlineStds[] = $q_student_id[1];
                }
            } 
        }
        return $onlineStds;
    }

    // Ajax latest
    public function ajax_latest() {
        $this->autoRender = false;
        if (!$this->Session->check('Filter')) {
            $filter = array('class' => 'all', 'daterange' => 'all');
            $this->Session->write('Filter', $filter);
        } else {
            $filter = $this->Session->read('Filter');
        }
        $quizDetails = $this->Quiz->quizDetails((int) $this->request->data['quizId'], $filter);
        $studentIds = array();
        foreach ($quizDetails['Student'] as $key1 => $value1) {
            $informations = array();
            $informations[] = array('fname' => $value1['fname'], 'lname' => $value1['lname'], 'class' => $value1['class']);
            $answers = array();
            foreach ($value1['Answer'] as $key2 => $value2) {
                $answers[$value2['question_id']] = $value2['text'];
            }
            $informations[] = $answers;
            $studentIds[$value1['id']] = $informations;
        }

        $onlineStds = $this->checkOnlineStudent($quizDetails['Quiz']['random_id']);
        // $oldOnlineStds = json_decode($this->request->data['onlineStds'], true);
        // $offlineStds = array_diff($oldOnlineStds, $onlineStds);

        echo json_encode(array('studentIds' => $studentIds, 'onlineStds' => $onlineStds));
    }

    public function ajax_update() {
        $this->autoRender = false;
        // authenticate or not
        $checkPermission = $this->Quiz->checkPermission((int)$this->request->data['quizId'], $this->Auth->user('id'));
        if (empty($checkPermission)) {
            throw new ForbiddenException;
        }

        // $old_data = json_decode($this->request->data['old_data'], true);
        // $new_data = json_decode($this->request->data['new_data'], true);
        $old_data = !empty($this->request->data['old_data']) ? $this->request->data['old_data'] : array();
        $new_data = $this->request->data['new_data'];

        $modified_ids = array();

        foreach ($new_data as $id1 => $dataset) {
            if (array_key_exists($id1, $old_data)) { // if old student
                $arraysAreEqual = ($dataset == $old_data[$id1]);
                if (empty($arraysAreEqual)) {
                    $modified_ids[] = $id1;
                }
            } else {
                $modified_ids[] = $id1; // new student found
            }
        }

        echo json_encode($modified_ids);
        exit;
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
            $this->Session->setFlash(__('You have Successfuly deleted quiz'), 'success_form', array(), 'success');
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

    // Duplicate quiz
    // Only quiz name, questions and choices will be duplicated
    public function duplicate() {
        $this->autoRender = false;

        $response['result'] = 0;

        $this->Quiz->Behaviors->load('Containable');
        $quizId = $this->request->data['quiz_id'];
        $quizInfo = $this->Quiz->find('first', array(
            'conditions' => array(
                'Quiz.id' => $quizId,
                'Quiz.user_id' => $this->Auth->user('id')
            ),
            'contain' => array(
                'Question' => array(
                    'Choice'
                )
            )
        ));

        if (!empty($quizInfo)) {
            $quizInfo['Quiz']['id'] = '';
            $quizInfo['Quiz']['name'] = __('Copy of:') . ' ' . $quizInfo['Quiz']['name'];
            unset($quizInfo['Quiz']['created']);
            unset($quizInfo['Quiz']['modified']);
            unset($quizInfo['Quiz']['student_count']);
            unset($quizInfo['Quiz']['random_id']);

            foreach ($quizInfo['Question'] as $key1 => $question) {
                $quizInfo['Question'][$key1]['id'] = '';
                $quizInfo['Question'][$key1]['quiz_id'] = '';
                unset($quizInfo['Question'][$key1]['created']);
                unset($quizInfo['Question'][$key1]['modified']);
                foreach ($question['Choice'] as $key2 => $choice) {
                    $quizInfo['Question'][$key1]['Choice'][$key2]['id'] = '';
                    $quizInfo['Question'][$key1]['Choice'][$key2]['question_id'] = '';
                }
            }

            if ($this->Quiz->saveAll($quizInfo, array('deep' => true))) {
                $random_id = $this->Quiz->id . $this->Quiz->randText(2);
                $this->Quiz->saveField('random_id', $random_id);
                $response['message'] = __('Duplicated Successfully');
                $response['result'] = 1;
                $response['id'] = $this->Quiz->id;
            } else {
                $response['message'] = __('Something went wrong, please try again later!');
            }
        } else {
            $response['message'] = __('Invalid Quiz!');
        }
        echo json_encode($response);
        exit;

    }

    // Method for sharing quiz
    public function share($quiz_id, $shared = null) {
        $this->autoRender = false;
        $this->accountStatus(); 
        // authenticate or not
        $quiz = $this->Quiz->getAQuizRel($quiz_id, $this->Auth->user('id'), array('User'));

        // pr($quiz);
        // exit;

        if (empty($quiz) || empty($quiz['Quiz']['question_count'])) {
            throw new ForbiddenException;
        }   

        if (empty($shared) && ($quiz['Quiz']['shared'] == 1) && empty($quiz['Quiz']['is_approve'])) {
            $message = __('You had already shared this quiz. Please wait for admin approval!');
        } else if (empty($shared) && ($quiz['Quiz']['shared'] == 1) && ($quiz['Quiz']['is_approve'] == 1)) {
            $message = __('Your quiz already shared and approved!');
        } else if (empty($shared) && ($quiz['Quiz']['shared'] == 1) && ($quiz['Quiz']['is_approve'] == 2)) {
            $message = __('Sorry, your sharing has been declined by admin.');
        } else {
            // Proceed to next
        }

        if (!empty($message)) {
            $this->Session->setFlash($message, 'error_form', array(), 'error');
            $this->redirect(array('controller' => 'quiz', 'action' => 'index'));
        }
        // pr($quiz);
        // exit;


        // Update shared field
        $this->Quiz->id = $quiz_id;
        if ($this->Quiz->saveField('shared', empty($shared) ? 1 : NULL)) {
            // Send email to the admin
            if (empty($shared)) {
                $subject = __('[Verkkotesti] A quiz has been shared!');
                $template = 'quiz_shared';
                $message = __('You have successfully shared the quiz. Please hold for admin approval.');

                $admin_email = $this->Email->sendMail(Configure::read('AdminEmail'), $subject, $quiz, $template);
                //$admin_email = $this->Email->sendMail('biplob.weblancer@gmail.com', $subject, $quiz, $template);

                // pr($admin_email);
                // exit;
            } else {
                $subject = __('Sharing removed!');
                $template = 'remove_sharing';
                $message = __('You have successfully remove sharing');
            }

            $this->Session->setFlash($message, 'success_form', array(), 'success');
        } else {
            $this->Session->setFlash(__('Something went wrong, please try again later!'), 'error_form', array(), 'error');
        }
        $this->redirect($this->referer());
    }

    // Method for listing all quizzes from quiz bank
    public function ajax_bank() {
        $this->temp_common();
    }

    public function temp_common() {
        $this->layout = 'ajax';
        $this->hasQuizBankAccess();
        $this->loadModel('Subject');
        $subjectOptions = $this->Subject->find('list', array(
            'conditions' => array(
                'Subject.type' => NULL,
                'Subject.isactive' => 1,
                'Subject.is_del' => NULL
            )
        ));

        $classOptions = $this->Subject->find('list', array(
            'conditions' => array(
                'Subject.type' => 1,
                'Subject.isactive' => 1,
                'Subject.is_del' => NULL
            )
        ));
        // Selected subjects
        $selectedSubjects = $this->Session->read('Auth.User.subjects');

        if (!empty($selectedSubjects)) {
            $selectedSubjects = json_decode($selectedSubjects, true);
        } else {
            $selectedSubjects = array();
        }

        // pr($selectedSubjects);
        // exit;
        $this->Paginator->settings = $this->paginate;
        foreach ($selectedSubjects as $term) {
            $term = trim($term);
            if (!empty($term)) {
                $this->Paginator->settings['conditions']['OR'][] = array('Quiz.subjects LIKE' => '%' . '"' . $term . '"' . '%');
            }
        }

        $user_id = $this->Auth->user('id');

        $this->Quiz->virtualFields['imported'] = 'SELECT count(*) FROM imported_quizzes AS ImportedQuiz WHERE (ImportedQuiz.user_id = '.$user_id.' AND ImportedQuiz.quiz_id = Quiz.id)';

        $this->Paginator->settings['recursive'] = -1;

        // Get pagination
        $this->Paginator->settings['conditions'][] = array(
            'Quiz.shared' => 1,
            'Quiz.is_approve' => 1,
            'Quiz.imported' => 0
        );

        // pr($this->Paginator->settings);
        // exit;

        $quizzes = $this->Paginator->paginate('Quiz');


        // pr($quizzes);
        // exit;

        if (!empty($subjectOptions)) {
            $subjectOptions = array(0 => __('All Subjects')) + $subjectOptions;
        }
        
        if (!empty($classOptions)) {
            $classOptions = array(0 => __('All Classes')) + $classOptions;
        }

        $this->set(compact('subjectOptions', 'classOptions', 'selectedSubjects', 'quizzes'));
    }

    // Method for quiz bank pagination
    public function quiz_bank_pagination() {
        $this->temp_common();
        $this->render('/Elements/Quiz/quiz_bank_pagination');
    }

    // Test method 
    public function test_link() {
        $this->layout = 'ajax';
        $this->hasQuizBankAccess();
        $this->loadModel('Subject');
        $subjectOptions = $this->Subject->find('list', array(
            'conditions' => array(
                'Subject.type' => NULL,
                'Subject.isactive' => 1,
                'Subject.is_del' => NULL
            )
        ));

        $classOptions = $this->Subject->find('list', array(
            'conditions' => array(
                'Subject.type' => 1,
                'Subject.isactive' => 1,
                'Subject.is_del' => NULL
            )
        ));

       
        $this->Paginator->settings = $this->paginate;

        if (empty($this->request->data['subject_list'])) {
            $selectedSubjects = $this->Session->read('Auth.User.subjects');
            if (!empty($selectedSubjects)) {
                $this->request->data['subject_list'] = json_decode($selectedSubjects, true);
            } 
        }

        if (empty($this->request->data['page_no'])) {
            $this->request->params['named']['page'] = 1;
        } else {
            $this->request->params['named']['page'] = $this->request->data['page_no'];
        }

        // pr($this->request->data);
        // exit;

        if (!empty($this->request->data['subject_list'])) {
            foreach ($this->request->data['subject_list'] as $term) {
                $term = trim($term);
                if (!empty($term)) {
                    $this->Paginator->settings['conditions'][0]['OR'][] = array('Quiz.subjects LIKE' => '%' . '"' . $term . '"' . '%');
                }
            }
        }

        if (!empty($this->request->data['class_list'])) {
            foreach ($this->request->data['class_list'] as $term) {
                $term = trim($term);
                if (!empty($term)) {
                    $this->Paginator->settings['conditions'][1]['OR'][] = array('Quiz.classes LIKE' => '%' . '"' . $term . '"' . '%');
                }
            }
        }

        $user_id = $this->Auth->user('id');
        $this->Quiz->virtualFields['imported'] = 'SELECT count(*) FROM imported_quizzes AS ImportedQuiz WHERE (ImportedQuiz.user_id = '.$user_id.' AND ImportedQuiz.quiz_id = Quiz.id)';

        // Get pagination
        $this->Paginator->settings['conditions'][2] = array(
            'Quiz.shared' => 1,
            'Quiz.is_approve' => 1,
            'Quiz.imported' => 0
        );

        $this->Paginator->settings['recursive'] = -1;

        if (!empty($this->request->data['order_type']) && !empty($this->request->data['order_field'])) {
            $this->Paginator->settings['order'] = 'Quiz.' . $this->request->data['order_field'] . ' ' . $this->request->data['order_type'];
            $order_type = ($this->request->data['order_type'] == 'asc') ? 'desc' : 'asc';
            $this->set('order_type', $order_type);
            $this->set('order_field', $this->request->data['order_field']);
        }

        // pr($this->Paginator->settings);
        // exit;

        try {
            $quizzes = $this->Paginator->paginate('Quiz');
        } catch (NotFoundException $e) { 
            $this->request->params['named']['page'] = 1;
            $quizzes = $this->Paginator->paginate('Quiz');
        }

        $this->set(compact('quizzes', 'subjectOptions', 'classOptions'));
        $this->render('/Elements/Quiz/quiz_bank_pagination');
    }

    // ajax duplicate
    // Duplicate quiz
    // Only quiz name, questions and choices will be duplicated
    public function ajax_import() {
        $this->autoRender = false;
        $this->accountStatus();
        $response['result'] = 0;

        // Check maximum import permission
        if ($this->Auth->user('account_level') == 22) {
            $imported_quiz_count = $this->Quiz->User->ImportedQuiz->find('count', array(
                'conditions' => array(
                    'ImportedQuiz.user_id' => $this->Auth->user('id')
                )
            ));
            if ($imported_quiz_count >= DOWNLOAD_LIMIT) {
                $response['message'] = __('Sorry, you have exceeded maximum limit of import quiz. Please upgrade your account to get unlimited access on quiz bank!');
            } else {
                if ((count($this->request->data['random_id'])+$imported_quiz_count) > DOWNLOAD_LIMIT) {
                    $response['message'] = __('Sorry, we can\'t process your request! You have left only import') . ' ' . (DOWNLOAD_LIMIT - $imported_quiz_count);
                }
            }
        }
        if (!empty($response['message'])) {
            echo json_encode($response);
            exit;
        }

        $this->Quiz->Behaviors->load('Containable');
        $quizInfo = $this->Quiz->find('all', array(
            'conditions' => array(
                'Quiz.random_id' => $this->request->data['random_id'],
                'Quiz.shared' => 1
            ),
            'contain' => array(
                'Question' => array(
                    'Choice'
                )
            )
        ));

        // pr($quizInfo);
        // exit;

        if (!empty($quizInfo) && (count($quizInfo) == count($this->request->data['random_id']))) {
            foreach ($quizInfo as $key => $quiz) {
                // Check if already imported
                $is_imported = $this->Quiz->User->ImportedQuiz->find('count', array(
                    'conditions' => array(
                        'ImportedQuiz.quiz_id' => $quiz['Quiz']['id'],
                        'ImportedQuiz.user_id' => $this->Auth->user('id')
                    )
                ));
                // End of checking
                if (empty($is_imported)) { // if Not imported before
                    // Imported quiz data
                    $saveImportedQuiz['ImportedQuiz']['user_id'] = $this->Auth->user('id');
                    $saveImportedQuiz['ImportedQuiz']['quiz_id'] = $quiz['Quiz']['id'];

                    $quiz['Quiz']['id'] = '';
                    $quiz['Quiz']['status'] = 1;
                    $quiz['Quiz']['shared'] = NULL;
                    $quiz['Quiz']['user_id'] = $this->Auth->user('id');
                    $quiz['Quiz']['name'] = __('Copy of:') . ' ' . $quiz['Quiz']['name'];
                    unset($quiz['Quiz']['created']);
                    unset($quiz['Quiz']['modified']);
                    unset($quiz['Quiz']['student_count']);
                    unset($quiz['Quiz']['random_id']);

                    foreach ($quiz['Question'] as $key1 => $question) {
                        $quiz['Question'][$key1]['id'] = '';
                        $quiz['Question'][$key1]['quiz_id'] = '';
                        unset($quiz['Question'][$key1]['created']);
                        unset($quiz['Question'][$key1]['modified']);
                        if (!empty($question['Choice'])) {
                            foreach ($question['Choice'] as $key2 => $choice) {
                                $quiz['Question'][$key1]['Choice'][$key2]['id'] = '';
                                $quiz['Question'][$key1]['Choice'][$key2]['question_id'] = '';
                            }
                        }
                    }

                    // pr($quiz);
                    // exit;

                    if ($this->Quiz->saveAll($quiz, array('deep' => true))) {
                        $random_id = $this->Quiz->id . $this->Quiz->randText(2);
                        $this->Quiz->saveField('random_id', $random_id);
                        $response['Quiz'][$key]['id'] = $this->Quiz->id;
                        $response['Quiz'][$key]['name'] = $quiz['Quiz']['name'];
                        $this->Quiz->User->ImportedQuiz->create();
                        $this->Quiz->User->ImportedQuiz->save($saveImportedQuiz); 
                    } else {
                        $response['message'] = __('Something went wrong, please try again later!');
                    }
                }
            }
            $response['message'] = __('Imported Successfully');
            $response['result'] = 1;
        } else {
            $response['message'] = __('Invalid Quiz!');
        }
        echo json_encode($response);
        exit;

    }

    // List of shared quiz
    public function admin_shared() {
        if ($this->Auth->user('account_level') != 51)
            throw new ForbiddenException;
        $this->set('title_for_layout',__('Shared Quiz List'));

        if ($this->request->is('post')) {
            $data = $this->request->data;
            if (isset($data['Quiz'])) {
                $filter = $data['Quiz']['is_approve'];
                $this->Session->write('Quiz.is_approve', $filter);
            }
            $this->redirect(array('controller' => 'quiz', 'action' => 'shared', 'admin' => true));
        } else {
            if (!$this->Session->check('Quiz.is_approve')) {
                $filter = 'all';
                $this->Session->write('Quiz.is_approve', $filter);
            } else {
                $filter = $this->Session->read('Quiz.is_approve');
            }
        }

        $this->set(compact('filter'));
        $this->Paginator->settings = $this->paginate;
        if ($filter == 'all') {
            // Do nothing
            $this->Paginator->settings['order'] = 'Quiz.is_approve ASC';
        } else {    
            // pr($filter);
            $this->Paginator->settings['conditions'][] = array(
                'Quiz.is_approve' => ($filter == 3) ? NULL : $filter,
            );
            $this->Paginator->settings['order'] = 'Quiz.created ASC';
        }

        $this->Quiz->Behaviors->load('Containable');

        $this->Paginator->settings['conditions'][] = array(
            'Quiz.shared' => 1,
        );

        $this->Paginator->settings['contain'] = array(
            'User'
        );

        // pr($this->Paginator->settings);

        // pr($this->Paginator->paginate('Quiz'));
        // exit;

        try {
            $this->set('quizzes', $this->Paginator->paginate('Quiz'));
        } catch (NotFoundException $e) { 
            // when pagination error found redirect to first page e.g. paging page not found
            return $this->redirect(array('controller' => 'quiz', 'action' => 'shared', 'admin' => true));
        }

        // Language strings
        $lang_strings['decline_question'] = __('Decline the quiz');
        $lang_strings['cancel'] = __('Cancel');
        $lang_strings['submit'] = __('Submit');
        $lang_strings['decline_reason'] = __('Enter decline reason if any!');
        $lang_strings['approve_question'] = __('Approve the quiz');
        $lang_strings['approve_body'] = __('If you approve the quiz, you have always option to decline sharing!');

        $this->set(compact('lang_strings'));

    }

    // Admin decline method
    public function admin_manage_share() {
        if ($this->Auth->user('account_level') != 51)
            throw new ForbiddenException;

        if ($this->request->is('post')) {
            $quiz = $this->Quiz->find('first', array(
                'conditions' => array(
                    'Quiz.random_id' => $this->request->data['Quiz']['random_id'],
                    'Quiz.shared' => 1
                ),
                'recursive' => -1,
                'fields' => array('Quiz.id')
            ));

            if (empty($quiz)) {
                $this->Session->setFlash(__('Something went wrong, please try again later!'), 'error_form', array(), 'error');
            } else {
                unset($this->request->data['Quiz']['random_id']);
                $this->request->data['Quiz']['id'] = $quiz['Quiz']['id'];
                $message = ($this->request->data['Quiz']['is_approve'] == 1) ? __('You have successfully approved!') : __('You have successfully declined!');
                // pr($this->request->data);
                // exit;
                $this->Quiz->validator()->remove('name');
                if ($this->Quiz->save($this->request->data)) {
                     $this->Session->setFlash($message, 'success_form', array(), 'success');
                } else {
                     $this->Session->setFlash(__('Something went wrong, please try again later!'), 'error_form', array(), 'error');
                }
            }
        }
        $this->redirect($this->referer());
    }

    // Admin preview quiz
    public function admin_preview($quiz_id = null) {
        if ($this->Auth->user('account_level') != 51)
            throw new ForbiddenException;
        if (empty($quiz_id)) {
            $this->Session->setFlash(__('No direct access to this location!'), 'error_form', array(), 'error');
            $this->redirect(array('controller' => 'quiz', 'action' => 'shared', 'admin' => true));
        }

        $this->Quiz->Behaviors->load('Containable');
        $data = $this->Quiz->find('first', array(
            'conditions' => array(
                'Quiz.id = ' => $quiz_id
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

        if (empty($data))
            throw new NotFoundException;

        $this->QuestionType->Behaviors->load('Containable');
        $this->QuestionType->contain();
        $data['QuestionTypes'] = $this->QuestionType->find('all', array(
            'fields' => array('name', 'template_name', 'multiple_choices', 'id', 'type')
        ));

        if (empty($data['Question'])) {
            $this->set('no_question', true);
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
        $lang_strings['image_exp_text'] = __('Image explanation text');
        $lang_strings['other_exp_text'] = __('Explanation text');
        $lang_strings['empty_header'] = __('Please enter Header text');

        // Load available classes (created by admin)
        $this->loadModel('Subject');
        $classOptions = $this->Subject->find('list', array(
            'conditions' => array(
                'Subject.isactive' => 1,
                'Subject.is_del' => NULL,
                'Subject.type' => 1
            ),
            'recursive' => -1
        ));

        $subject_cond[] = array(
            'Subject.isactive' => 1,
            'Subject.is_del' => NULL,
            'Subject.type' => NULL
        );

        if (!empty($data['User']['subjects'])) {
            $selectedSubjects = json_decode($data['User']['subjects'], true);
            $subject_cond[] = array('Subject.id' => $selectedSubjects);
        }

        $subjectOptions = $this->Subject->find('list', array(
            'conditions' => $subject_cond,
            'recursive' => -1
        ));

        if (!empty($subjectOptions)) {
            $subjectOptions = array(0 => __('All Subject')) + $subjectOptions;
        }
        
        if (!empty($classOptions)) {
            $classOptions = array(0 => __('All Class')) + $classOptions;
        }

        // pr($this->Session->read('Auth.User'));
        // exit;

        // pr($data);
        // exit;

        $this->set('data', $data);
        $this->set(compact('lang_strings', 'classOptions', 'subjectOptions'));
    }

}