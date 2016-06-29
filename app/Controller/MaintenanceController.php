<?php
App::uses('CakeEmail', 'Network/Email');
class MaintenanceController extends AppController {

    public function beforeFilter() {
        parent::beforeFilter();
        $this->Auth->allow('notice');
    }

    // Method for site settings
    public function admin_settings() {
        if ($this->Auth->user('account_level') != 51)
            throw new ForbiddenException;
        if ($this->request->is(array('post','put'))) {
            $setting = $this->_getSettings();
            if (!isset($this->request->data['visible'])) {
                $this->request->data['visible'] = NULL;
            }
            if (!isset($this->request->data['offline_status'])) {
                $this->request->data['offline_status'] = NULL;
            } else {
                if ($this->Auth->user('id') != 1) {
                    $this->Session->setFlash('Sorry, you are not authorized to make the site offline!', 'error_form', array(), 'error');
                    $this->redirect($this->request->referer());
                }
            }
            App::uses('Sanitize', 'Utility');
            foreach ($this->request->data as $key => $value) {
                if (array_key_exists($key, $setting) && $setting[$key] != $value) {
                    $this->Setting->updateAll(array('value' => '"' . Sanitize::escape($value) . '"'), array('field' => $key));
                }
            }
            
            $this->Session->setFlash('Changes have been saved', 'success_form', array(), 'success');
            $this->redirect($this->request->referer());
        }
        $this->set('title_for_layout', 'System Settings');

    }

    public function notice() {
        // Remove maintenance mode
        $setting = $this->_getSettings();
        if (empty($setting['offline_status']))
        $this->redirect(array('controller' => 'quiz', 'action' => 'index'));
        $this->set('title_for_layout', __('Pardon for the dust!'));
        $this->render('/Elements/Maintenance/notice');
    }

    public function admin_import() {
        if ($this->Auth->user('account_level') != 51)
            throw new ForbiddenException;
        $this->set('title_for_layout',__('Import Demo Quiz'));

        if ($this->request->is('post')) {
            if (empty($this->request->data['Maintenance']['user_id'])) {
                $this->Session->setFlash(__('Please enter an user id'), 'error_form', array(), 'error');
            } elseif (!is_numeric($this->request->data['Maintenance']['user_id'])) {
                $this->Session->setFlash(__('Please enter a numeric id'), 'error_form', array(), 'error');
            } else {
                $this->importQuizzes($this->request->data['Maintenance']['user_id']);
                $this->Session->setFlash(__('Imported successfully'), 'notification_form', array(), 'notification');
            }
            $this->redirect($this->referer());
        }
    }

    public function sendEmail() {
        $Email = new CakeEmail();
        $Email->viewVars(array('user' => $this->Auth->user()));
        $Email->from(array('pietu.halonen@verkkotesti.fi' => 'WebQuiz.fi'));
        $Email->template('first_quiz_create');
        $Email->emailFormat('html');
        $Email->to(Configure::read('AdminEmail'));
        $Email->subject(__('[Verkkotesti] Demo quizzes loaded'));
        $Email->send();
    }

    public function load_dummy_data() {
        $this->autoRender = false;
        $this->loadModel('Quiz');
        $created_quiz = $this->Quiz->findByUserId($this->Auth->user('id'), array('Quiz.id'));
        if (!empty($created_quiz)) {
            $this->Session->setFlash(__('No direct access on this location'), 'error_form', array(), 'error');
            $this->redirect(array('controller' => 'quiz', 'action' => 'index'));
        }
        $this->importQuizzes($this->Auth->user('id'));
        $this->sendEmail();
        $this->Session->setFlash(__('Imported successfully'), 'notification_form', array(), 'notification');
        $this->redirect($this->referer());
    }

    public function importQuizzes($user_id) {
        $this->loadModel('Quiz');
        $quizes[0]['Quiz']['name'] = 'MALLITESTI: Fruits and vegetables';
        $quizes[0]['Quiz']['description'] = 'Tässä kokeessa testataan hedelmien ja vihannesten sanastoa englanniksi. (MALLITESTI: voit poistaa tämän testin kun et tarvitse sitä enää.)';
        $quizes[0]['Quiz']['student_count'] = 9;

        $quizes[1]['Quiz']['name'] = 'MALLITESTI: Musiikin kotitehtävä';
        $quizes[1]['Quiz']['description'] = 'Ennen seuraavaa tuntia, katso oheiset videot ja tee niihin liittyvät tehtävät. (MALLITESTI: voit poistaa tämän testin kun et tarvitse sitä enää.)';
        $quizes[1]['Quiz']['student_count'] = 9;
        $quizes[1]['Quiz']['show_result'] = 1;

        $quizes[2]['Quiz']['name'] = 'MALLITESTI: Itsearviolomake';
        $quizes[2]['Quiz']['description'] = 'Täytä oheinen itsearviolomake huolellisesti. (MALLITESTI: voit poistaa tämän testin kun et tarvitse sitä enää.)';
        $quizes[2]['Quiz']['student_count'] = 9;

        foreach ($quizes as $key1 => $quiz) {
            $quiz['Quiz']['user_id'] = $user_id;
            $this->Quiz->create();
            if ($this->Quiz->save($quiz)) { // Save Quiz
                
                $random_id = $this->Quiz->id . $this->Quiz->randText(2);
                $this->Quiz->saveField('random_id', $random_id);
                
                $questions = array(); // Prevent duplicate questions
                $question_ids = array(); // Question id array

                if ($key1 == 0) { // Quiz 1
                    $questions[0]['Question']['quiz_id'] = $this->Quiz->id;
                    $questions[0]['Question']['question_type_id'] = 2;
                    $questions[0]['Question']['text'] = 'Käännä englanniksi "omena".';
                    $questions[0]['Question']['explanation'] = 'Oikea vastaus +2p';
                    $questions[0]['Question']['weight'] = 3;
                    $questions[1]['Question']['quiz_id'] = $this->Quiz->id;
                    $questions[1]['Question']['question_type_id'] = 2;
                    $questions[1]['Question']['text'] = 'Käännä englanniksi "mansikka".';
                    $questions[1]['Question']['explanation'] = 'Oikea vastaus +2p';
                    $questions[1]['Question']['weight'] = 2;
                    $questions[2]['Question']['quiz_id'] = $this->Quiz->id;
                    $questions[2]['Question']['question_type_id'] = 2;
                    $questions[2]['Question']['text'] = 'Käännä englanniksi "porkkana.';
                    $questions[2]['Question']['explanation'] = 'Oikea vastaus +2p';
                    $questions[2]['Question']['weight'] = 1;

                    $questions[3]['Question']['quiz_id'] = $this->Quiz->id;
                    $questions[3]['Question']['question_type_id'] = 6;
                    $questions[3]['Question']['text'] = 'Käännössanat';
                    $questions[3]['Question']['weight'] = 4;

                    $questions[4]['Question']['quiz_id'] = $this->Quiz->id;
                    $questions[4]['Question']['question_type_id'] = 6;
                    $questions[4]['Question']['text'] = 'Monivalinnat';

                    $questions[5]['Question']['quiz_id'] = $this->Quiz->id;
                    $questions[5]['Question']['question_type_id'] = 3;
                    $questions[5]['Question']['text'] = 'Mitkä seuraavista ovat hedelmiä?';
                    $questions[5]['Question']['explanation'] = 'Oikeasta vastauksesta +1p';
                    $questions[5]['Question']['max_allowed'] = 3;

                    $questions[6]['Question']['quiz_id'] = $this->Quiz->id;
                    $questions[6]['Question']['question_type_id'] = 1;
                    $questions[6]['Question']['text'] = 'Mikä seuraavista on "ananas" englanniksi?';
                    $questions[6]['Question']['explanation'] = 'Oikeasta vastauksesta +1p';

                    $questions[7]['Question']['quiz_id'] = $this->Quiz->id;
                    $questions[7]['Question']['question_type_id'] = 6;
                    $questions[7]['Question']['text'] = 'Avoimet tehtävät';

                    $questions[8]['Question']['quiz_id'] = $this->Quiz->id;
                    $questions[8]['Question']['question_type_id'] = 5;
                    $questions[8]['Question']['text'] = 'Muodosta englanniksi lause, jossa käytät KAHTA hedelmää';
                    $questions[8]['Question']['explanation'] = 'Hedelmistä 0-2p, muusta lauseesta 0-4p.';

                    $questions[9]['Question']['quiz_id'] = $this->Quiz->id;
                    $questions[9]['Question']['question_type_id'] = 4;
                    $questions[9]['Question']['text'] = 'Nimeä englanniksi suosikkihedelmäsi.';
                    
                    foreach ($questions as $key2 => $question) {
                        $this->Quiz->Question->create();
                        if ($this->Quiz->Question->save($question)) { // save related question
                            $choices = array();
                            $question_ids[] = $this->Quiz->Question->id;
                            // save related choice
                            if ($key2 == 0) { // first question choices
                                $choice['Choice']['question_id'] = $this->Quiz->Question->id;
                                $choice['Choice']['text'] = 'apple';
                                $choice['Choice']['points'] = 2.00;
                                $choice['Choice']['weight'] = NULL;
                                $this->Quiz->Question->Choice->create();
                                $this->Quiz->Question->Choice->save($choice);
                            } elseif ($key2 == 1) { // first question choices
                                $choice['Choice']['question_id'] = $this->Quiz->Question->id;
                                $choice['Choice']['text'] = 'strawberry';
                                $choice['Choice']['points'] = 2.00;
                                $choice['Choice']['weight'] = NULL;
                                $this->Quiz->Question->Choice->create();
                                $this->Quiz->Question->Choice->save($choice);
                            } elseif ($key2 == 2) { // first question choices
                                $choice['Choice']['question_id'] = $this->Quiz->Question->id;
                                $choice['Choice']['text'] = 'carrot';
                                $choice['Choice']['points'] = 2.00;
                                $choice['Choice']['weight'] = NULL;
                                $this->Quiz->Question->Choice->create();
                                $this->Quiz->Question->Choice->save($choice);
                            } elseif ($key2 == 5) { // 3/4 question has not choice
                                $choices[0]['Choice']['question_id'] = $this->Quiz->Question->id;
                                $choices[0]['Choice']['text'] = 'Orange';
                                $choices[0]['Choice']['points'] = 1.00;
                                $choices[0]['Choice']['weight'] = NULL;

                                $choices[1]['Choice']['question_id'] = $this->Quiz->Question->id;
                                $choices[1]['Choice']['text'] = 'Cucumber';
                                $choices[1]['Choice']['points'] = 0.00;
                                $choices[1]['Choice']['weight'] = 5;

                                $choices[2]['Choice']['question_id'] = $this->Quiz->Question->id;
                                $choices[2]['Choice']['text'] = 'Banana';
                                $choices[2]['Choice']['points'] = 1.00;
                                $choices[2]['Choice']['weight'] = 4;

                                $choices[3]['Choice']['question_id'] = $this->Quiz->Question->id;
                                $choices[3]['Choice']['text'] = 'Grass';
                                $choices[3]['Choice']['points'] = 0.00;
                                $choices[3]['Choice']['weight'] = 3;

                                $choices[4]['Choice']['question_id'] = $this->Quiz->Question->id;
                                $choices[4]['Choice']['text'] = 'Pear';
                                $choices[4]['Choice']['points'] = 1.00;
                                $choices[4]['Choice']['weight'] = 1;

                                $choices[5]['Choice']['question_id'] = $this->Quiz->Question->id;
                                $choices[5]['Choice']['text'] = 'Birch';
                                $choices[5]['Choice']['points'] = 0.00;
                                $choices[5]['Choice']['weight'] = 2;

                                foreach ($choices as $key3 => $choice) {
                                    $this->Quiz->Question->Choice->create();
                                    $this->Quiz->Question->Choice->save($choice);
                                }
                            } elseif ($key2 == 6) { 
                                $choices[0]['Choice']['question_id'] = $this->Quiz->Question->id;
                                $choices[0]['Choice']['text'] = 'Ananas';
                                $choices[0]['Choice']['points'] = 0.00;
                                $choices[0]['Choice']['weight'] = NULL;

                                $choices[1]['Choice']['question_id'] = $this->Quiz->Question->id;
                                $choices[1]['Choice']['text'] = 'Birchorange';
                                $choices[1]['Choice']['points'] = 0.00;
                                $choices[1]['Choice']['weight'] = NULL;

                                $choices[2]['Choice']['question_id'] = $this->Quiz->Question->id;
                                $choices[2]['Choice']['text'] = 'Pineapple';
                                $choices[2]['Choice']['points'] = 1.00;
                                $choices[2]['Choice']['weight'] = NULL;

                                $choices[3]['Choice']['question_id'] = $this->Quiz->Question->id;
                                $choices[3]['Choice']['text'] = 'Oakfruit';
                                $choices[3]['Choice']['points'] = 0.00;
                                $choices[3]['Choice']['weight'] = NULL;

                                foreach ($choices as $key3 => $choice) {
                                    $this->Quiz->Question->Choice->create();
                                    $this->Quiz->Question->Choice->save($choice);
                                }
                            } elseif ($key2 == 8) { // 7 skipped first question choices
                                $choice['Choice']['question_id'] = $this->Quiz->Question->id;
                                $choice['Choice']['text'] = 'Essay';
                                $choice['Choice']['points'] = 6.00;
                                $choice['Choice']['weight'] = NULL;
                                $this->Quiz->Question->Choice->create();
                                $this->Quiz->Question->Choice->save($choice);
                            } elseif ($key2 == 9) { // first question choices
                                $choice['Choice']['question_id'] = $this->Quiz->Question->id;
                                $choice['Choice']['text'] = 'Short_manual';
                                $choice['Choice']['points'] = 3.00;
                                $choice['Choice']['weight'] = NULL;
                                $this->Quiz->Question->Choice->create();
                                $this->Quiz->Question->Choice->save($choice);
                            } else {
                                // do nothing
                            }


                        }
                    }
                    
                    // Save Student data for first Quiz
                    // first quiz students
                    $students = array();
                    $students[0]['Student']['quiz_id'] = $this->Quiz->id;
                    $students[0]['Student']['fname'] = 'Armi';
                    $students[0]['Student']['lname'] = 'Arvaaja';
                    $students[0]['Student']['class'] = '4a';
                    $students[0]['Student']['status'] = 1;
                    $students[0]['Student']['submitted'] = date('Y-m-d H:i:s');

                    $students[1]['Student']['quiz_id'] = $this->Quiz->id;
                    $students[1]['Student']['fname'] = 'Siiri';
                    $students[1]['Student']['lname'] = 'Sähäkkä';
                    $students[1]['Student']['class'] = '4a';
                    $students[1]['Student']['status'] = 1;
                    $students[1]['Student']['submitted'] = date('Y-m-d H:i:s');

                    $students[2]['Student']['quiz_id'] = $this->Quiz->id;
                    $students[2]['Student']['fname'] = 'Ossi';
                    $students[2]['Student']['lname'] = 'Osaaja';
                    $students[2]['Student']['class'] = '4a';
                    $students[2]['Student']['status'] = 1;
                    $students[2]['Student']['submitted'] = date('Y-m-d H:i:s');

                    $students[3]['Student']['quiz_id'] = $this->Quiz->id;
                    $students[3]['Student']['fname'] = 'Jaakko';
                    $students[3]['Student']['lname'] = 'Janoinen';
                    $students[3]['Student']['class'] = '4c';
                    $students[3]['Student']['status'] = 1;
                    $students[3]['Student']['submitted'] = date('Y-m-d H:i:s');

                    $students[4]['Student']['quiz_id'] = $this->Quiz->id;
                    $students[4]['Student']['fname'] = 'Veera';
                    $students[4]['Student']['lname'] = 'Vikkelä';
                    $students[4]['Student']['class'] = '4c';
                    $students[4]['Student']['status'] = 1;
                    $students[4]['Student']['submitted'] = date('Y-m-d H:i:s');

                    $students[5]['Student']['quiz_id'] = $this->Quiz->id;
                    $students[5]['Student']['fname'] = 'Kerttu';
                    $students[5]['Student']['lname'] = 'Kekseliäs';
                    $students[5]['Student']['class'] = '4c';
                    $students[5]['Student']['status'] = 1;
                    $students[5]['Student']['submitted'] = date('Y-m-d H:i:s');

                    $students[6]['Student']['quiz_id'] = $this->Quiz->id;
                    $students[6]['Student']['fname'] = 'Uuno';
                    $students[6]['Student']['lname'] = 'Uninen';
                    $students[6]['Student']['class'] = '4f';
                    $students[6]['Student']['status'] = 1;
                    $students[6]['Student']['submitted'] = date('Y-m-d H:i:s');

                    $students[7]['Student']['quiz_id'] = $this->Quiz->id;
                    $students[7]['Student']['fname'] = 'Leevi';
                    $students[7]['Student']['lname'] = 'Loistava';
                    $students[7]['Student']['class'] = '4f';
                    $students[7]['Student']['status'] = 1;
                    $students[7]['Student']['submitted'] = date('Y-m-d H:i:s');

                    $students[8]['Student']['quiz_id'] = $this->Quiz->id;
                    $students[8]['Student']['fname'] = 'Vertti';
                    $students[8]['Student']['lname'] = 'Vemmelsääri';
                    $students[8]['Student']['class'] = '4f';
                    $students[8]['Student']['status'] = 1;
                    $students[8]['Student']['submitted'] = date('Y-m-d H:i:s');

                    foreach ($students as $key4 => $student) { // Save all student of first quiz
                        $this->Quiz->Student->create();
                        if ($this->Quiz->Student->save($student)) { // Save student data
                            $answers = array();
                            if ($key4 == 0) { // First student answer/ranking save
                                $answers[0]['Answer']['question_id'] = $question_ids[0];
                                $answers[0]['Answer']['student_id'] = $this->Quiz->Student->id;
                                $answers[0]['Answer']['text'] = 'appel';
                                $answers[0]['Answer']['score'] = 0.00;

                                $answers[1]['Answer']['question_id'] = $question_ids[1];
                                $answers[1]['Answer']['student_id'] = $this->Quiz->Student->id;
                                $answers[1]['Answer']['text'] = 'strooberri';
                                $answers[1]['Answer']['score'] = 0.00;

                                $answers[2]['Answer']['question_id'] = $question_ids[2];
                                $answers[2]['Answer']['student_id'] = $this->Quiz->Student->id;
                                $answers[2]['Answer']['text'] = 'kärot';
                                $answers[2]['Answer']['score'] = 0.00;

                                // 2 skipped
                                $answers[3]['Answer']['question_id'] = $question_ids[5];
                                $answers[3]['Answer']['student_id'] = $this->Quiz->Student->id;
                                $answers[3]['Answer']['text'] = 'Banana';
                                $answers[3]['Answer']['score'] = 1.00;

                                $answers[4]['Answer']['question_id'] = $question_ids[5];
                                $answers[4]['Answer']['student_id'] = $this->Quiz->Student->id;
                                $answers[4]['Answer']['text'] = 'Pear';
                                $answers[4]['Answer']['score'] = 1.00;

                                $answers[5]['Answer']['question_id'] = $question_ids[5];
                                $answers[5]['Answer']['student_id'] = $this->Quiz->Student->id;
                                $answers[5]['Answer']['text'] = 'Cucumber';
                                $answers[5]['Answer']['score'] = 0.00;

            
                                $answers[6]['Answer']['question_id'] = $question_ids[6];
                                $answers[6]['Answer']['student_id'] = $this->Quiz->Student->id;
                                $answers[6]['Answer']['text'] = 'Ananas';
                                $answers[6]['Answer']['score'] = 0.00;

                                // 1 skipped
                                $answers[7]['Answer']['question_id'] = $question_ids[8];
                                $answers[7]['Answer']['student_id'] = $this->Quiz->Student->id;
                                $answers[7]['Answer']['text'] = 'I laik appel but I dont like banana.';
                                $answers[7]['Answer']['score'] = 2.00;

                                $answers[8]['Answer']['question_id'] = $question_ids[9];
                                $answers[8]['Answer']['student_id'] = $this->Quiz->Student->id;
                                $answers[8]['Answer']['text'] = 'banaana';
                                $answers[8]['Answer']['score'] = 1.00;

                                foreach ($answers as $key5 => $answer) { // save answers
                                    $this->Quiz->Student->Answer->create();
                                    $this->Quiz->Student->Answer->save($answer);
                                } // end of anwers saving

                                // Prepare ranking data
                                $ranking['Ranking']['quiz_id'] = $this->Quiz->id;
                                $ranking['Ranking']['student_id'] = $this->Quiz->Student->id;
                                $ranking['Ranking']['score'] = 5.00;
                                $ranking['Ranking']['total'] = 19.00; 

                                $this->Quiz->Ranking->create();
                                $this->Quiz->Ranking->save($ranking);
                            } // First student answers/ranking save end

                            if ($key4 == 1) { // Second student answer/ranking save
                                $answers[0]['Answer']['question_id'] = $question_ids[0];
                                $answers[0]['Answer']['student_id'] = $this->Quiz->Student->id;
                                $answers[0]['Answer']['text'] = 'apple';
                                $answers[0]['Answer']['score'] = 2.00;

                                $answers[1]['Answer']['question_id'] = $question_ids[1];
                                $answers[1]['Answer']['student_id'] = $this->Quiz->Student->id;
                                $answers[1]['Answer']['text'] = 'strooberri';
                                $answers[1]['Answer']['score'] = 0.00;

                                $answers[2]['Answer']['question_id'] = $question_ids[2];
                                $answers[2]['Answer']['student_id'] = $this->Quiz->Student->id;
                                $answers[2]['Answer']['text'] = 'carrot';
                                $answers[2]['Answer']['score'] = 2.00;

                                $answers[3]['Answer']['question_id'] = $question_ids[5];
                                $answers[3]['Answer']['student_id'] = $this->Quiz->Student->id;
                                $answers[3]['Answer']['text'] = 'Banana';
                                $answers[3]['Answer']['score'] = 1.00;

                                $answers[4]['Answer']['question_id'] = $question_ids[5];
                                $answers[4]['Answer']['student_id'] = $this->Quiz->Student->id;
                                $answers[4]['Answer']['text'] = 'Pear';
                                $answers[4]['Answer']['score'] = 1.00;

                                $answers[5]['Answer']['question_id'] = $question_ids[5];
                                $answers[5]['Answer']['student_id'] = $this->Quiz->Student->id;
                                $answers[5]['Answer']['text'] = 'Orange';
                                $answers[5]['Answer']['score'] = 1.00;

                                $answers[6]['Answer']['question_id'] = $question_ids[6];
                                $answers[6]['Answer']['student_id'] = $this->Quiz->Student->id;
                                $answers[6]['Answer']['text'] = 'Pineapple';
                                $answers[6]['Answer']['score'] = 1.00;

                                $answers[7]['Answer']['question_id'] = $question_ids[8];
                                $answers[7]['Answer']['student_id'] = $this->Quiz->Student->id;
                                $answers[7]['Answer']['text'] = 'My favourite fruits are banana and apple.';
                                $answers[7]['Answer']['score'] = 6.00;

                                $answers[8]['Answer']['question_id'] = $question_ids[9];
                                $answers[8]['Answer']['student_id'] = $this->Quiz->Student->id;
                                $answers[8]['Answer']['text'] = 'apple';
                                $answers[8]['Answer']['score'] = 3.00;

                                foreach ($answers as $key5 => $answer) { // save answers
                                    $this->Quiz->Student->Answer->create();
                                    $this->Quiz->Student->Answer->save($answer);
                                } // end of anwers saving

                                // Prepare ranking data
                                $ranking['Ranking']['quiz_id'] = $this->Quiz->id;
                                $ranking['Ranking']['student_id'] = $this->Quiz->Student->id;
                                $ranking['Ranking']['score'] = 17.00;
                                $ranking['Ranking']['total'] = 19.00; 

                                $this->Quiz->Ranking->create();
                                $this->Quiz->Ranking->save($ranking);
                            } // Second student answers/ranking save end

                            if ($key4 == 2) { // Third student answer/ranking save
                                $answers[0]['Answer']['question_id'] = $question_ids[0];
                                $answers[0]['Answer']['student_id'] = $this->Quiz->Student->id;
                                $answers[0]['Answer']['text'] = 'apple';
                                $answers[0]['Answer']['score'] = 2.00;

                                $answers[1]['Answer']['question_id'] = $question_ids[1];
                                $answers[1]['Answer']['student_id'] = $this->Quiz->Student->id;
                                $answers[1]['Answer']['text'] = 'strawberry';
                                $answers[1]['Answer']['score'] = 2.00;

                                $answers[2]['Answer']['question_id'] = $question_ids[2];
                                $answers[2]['Answer']['student_id'] = $this->Quiz->Student->id;
                                $answers[2]['Answer']['text'] = 'carrot';
                                $answers[2]['Answer']['score'] = 2.00;

                                $answers[3]['Answer']['question_id'] = $question_ids[5];
                                $answers[3]['Answer']['student_id'] = $this->Quiz->Student->id;
                                $answers[3]['Answer']['text'] = 'Banana';
                                $answers[3]['Answer']['score'] = 1.00;

                                $answers[4]['Answer']['question_id'] = $question_ids[5];
                                $answers[4]['Answer']['student_id'] = $this->Quiz->Student->id;
                                $answers[4]['Answer']['text'] = 'Pear';
                                $answers[4]['Answer']['score'] = 1.00;

                                $answers[5]['Answer']['question_id'] = $question_ids[5];
                                $answers[5]['Answer']['student_id'] = $this->Quiz->Student->id;
                                $answers[5]['Answer']['text'] = 'Orange';
                                $answers[5]['Answer']['score'] = 1.00;

                                $answers[6]['Answer']['question_id'] = $question_ids[6];
                                $answers[6]['Answer']['student_id'] = $this->Quiz->Student->id;
                                $answers[6]['Answer']['text'] = 'Pineapple';
                                $answers[6]['Answer']['score'] = 1.00;

                                $answers[7]['Answer']['question_id'] = $question_ids[8];
                                $answers[7]['Answer']['student_id'] = $this->Quiz->Student->id;
                                $answers[7]['Answer']['text'] = 'Fruit salad is the best when it has pineapple and banana.';
                                $answers[7]['Answer']['score'] = 6.00;

                                $answers[8]['Answer']['question_id'] = $question_ids[9];
                                $answers[8]['Answer']['student_id'] = $this->Quiz->Student->id;
                                $answers[8]['Answer']['text'] = 'pineapple';
                                $answers[8]['Answer']['score'] = 3.00;

                                foreach ($answers as $key5 => $answer) { // save answers
                                    $this->Quiz->Student->Answer->create();
                                    $this->Quiz->Student->Answer->save($answer);
                                } // end of anwers saving

                                // Prepare ranking data
                                $ranking['Ranking']['quiz_id'] = $this->Quiz->id;
                                $ranking['Ranking']['student_id'] = $this->Quiz->Student->id;
                                $ranking['Ranking']['score'] = 19.00;
                                $ranking['Ranking']['total'] = 19.00; 

                                $this->Quiz->Ranking->create();
                                $this->Quiz->Ranking->save($ranking);
                            } // Third student answers/ranking save end

                            if ($key4 == 3) { // Fourth student answer/ranking save
                                $answers[0]['Answer']['question_id'] = $question_ids[0];
                                $answers[0]['Answer']['student_id'] = $this->Quiz->Student->id;
                                $answers[0]['Answer']['text'] = 'apple';
                                $answers[0]['Answer']['score'] = 2.00;

                                $answers[1]['Answer']['question_id'] = $question_ids[1];
                                $answers[1]['Answer']['student_id'] = $this->Quiz->Student->id;
                                $answers[1]['Answer']['text'] = '-';
                                $answers[1]['Answer']['score'] = 0.00;

                                $answers[2]['Answer']['question_id'] = $question_ids[2];
                                $answers[2]['Answer']['student_id'] = $this->Quiz->Student->id;
                                $answers[2]['Answer']['text'] = 'carrot';
                                $answers[2]['Answer']['score'] = 2.00;

                                $answers[3]['Answer']['question_id'] = $question_ids[5];
                                $answers[3]['Answer']['student_id'] = $this->Quiz->Student->id;
                                $answers[3]['Answer']['text'] = 'Banana';
                                $answers[3]['Answer']['score'] = 1.00;

                                $answers[4]['Answer']['question_id'] = $question_ids[5];
                                $answers[4]['Answer']['student_id'] = $this->Quiz->Student->id;
                                $answers[4]['Answer']['text'] = 'Pear';
                                $answers[4]['Answer']['score'] = 1.00;

                                $answers[5]['Answer']['question_id'] = $question_ids[5];
                                $answers[5]['Answer']['student_id'] = $this->Quiz->Student->id;
                                $answers[5]['Answer']['text'] = 'Orange';
                                $answers[5]['Answer']['score'] = 1.00;

                                $answers[6]['Answer']['question_id'] = $question_ids[6];
                                $answers[6]['Answer']['student_id'] = $this->Quiz->Student->id;
                                $answers[6]['Answer']['text'] = 'Pineapple';
                                $answers[6]['Answer']['score'] = 1.00;

                                $answers[7]['Answer']['question_id'] = $question_ids[8];
                                $answers[7]['Answer']['student_id'] = $this->Quiz->Student->id;
                                $answers[7]['Answer']['text'] = 'I am allerkik to apple but not to banana.';
                                $answers[7]['Answer']['score'] = 5.00;

                                $answers[8]['Answer']['question_id'] = $question_ids[9];
                                $answers[8]['Answer']['student_id'] = $this->Quiz->Student->id;
                                $answers[8]['Answer']['text'] = 'banana';
                                $answers[8]['Answer']['score'] = 3.00;

                                foreach ($answers as $key5 => $answer) { // save answers
                                    $this->Quiz->Student->Answer->create();
                                    $this->Quiz->Student->Answer->save($answer);
                                } // end of anwers saving

                                // Prepare ranking data
                                $ranking['Ranking']['quiz_id'] = $this->Quiz->id;
                                $ranking['Ranking']['student_id'] = $this->Quiz->Student->id;
                                $ranking['Ranking']['score'] = 15.00;
                                $ranking['Ranking']['total'] = 19.00; 

                                $this->Quiz->Ranking->create();
                                $this->Quiz->Ranking->save($ranking);
                            } // Fourth student answers/ranking save end

                            if ($key4 == 4) { // 5th student answer/ranking save
                                $answers[0]['Answer']['question_id'] = $question_ids[0];
                                $answers[0]['Answer']['student_id'] = $this->Quiz->Student->id;
                                $answers[0]['Answer']['text'] = 'apple';
                                $answers[0]['Answer']['score'] = 2.00;

                                $answers[1]['Answer']['question_id'] = $question_ids[1];
                                $answers[1]['Answer']['student_id'] = $this->Quiz->Student->id;
                                $answers[1]['Answer']['text'] = 'strawberry';
                                $answers[1]['Answer']['score'] = 2.00;

                                $answers[2]['Answer']['question_id'] = $question_ids[2];
                                $answers[2]['Answer']['student_id'] = $this->Quiz->Student->id;
                                $answers[2]['Answer']['text'] = 'carrot';
                                $answers[2]['Answer']['score'] = 2.00;

                                $answers[3]['Answer']['question_id'] = $question_ids[5];
                                $answers[3]['Answer']['student_id'] = $this->Quiz->Student->id;
                                $answers[3]['Answer']['text'] = 'Banana';
                                $answers[3]['Answer']['score'] = 1.00;

                                $answers[4]['Answer']['question_id'] = $question_ids[5];
                                $answers[4]['Answer']['student_id'] = $this->Quiz->Student->id;
                                $answers[4]['Answer']['text'] = 'Pear';
                                $answers[4]['Answer']['score'] = 1.00;

                                $answers[5]['Answer']['question_id'] = $question_ids[5];
                                $answers[5]['Answer']['student_id'] = $this->Quiz->Student->id;
                                $answers[5]['Answer']['text'] = 'Orange';
                                $answers[5]['Answer']['score'] = 1.00;

                                $answers[6]['Answer']['question_id'] = $question_ids[6];
                                $answers[6]['Answer']['student_id'] = $this->Quiz->Student->id;
                                $answers[6]['Answer']['text'] = 'Ananas';
                                $answers[6]['Answer']['score'] = 0.00;

                                $answers[7]['Answer']['question_id'] = $question_ids[8];
                                $answers[7]['Answer']['student_id'] = $this->Quiz->Student->id;
                                $answers[7]['Answer']['text'] = 'In Spain I ate lots of oranges and bananas.';
                                $answers[7]['Answer']['score'] = 6.00;

                                $answers[8]['Answer']['question_id'] = $question_ids[9];
                                $answers[8]['Answer']['student_id'] = $this->Quiz->Student->id;
                                $answers[8]['Answer']['text'] = 'cherry';
                                $answers[8]['Answer']['score'] = 1.00;

                                foreach ($answers as $key5 => $answer) { // save answers
                                    $this->Quiz->Student->Answer->create();
                                    $this->Quiz->Student->Answer->save($answer);
                                } // end of anwers saving

                                // Prepare ranking data
                                $ranking['Ranking']['quiz_id'] = $this->Quiz->id;
                                $ranking['Ranking']['student_id'] = $this->Quiz->Student->id;
                                $ranking['Ranking']['score'] = 16.00;
                                $ranking['Ranking']['total'] = 19.00; 

                                $this->Quiz->Ranking->create();
                                $this->Quiz->Ranking->save($ranking);
                            } // 5th student answers/ranking save end

                            if ($key4 == 5) { // 6th student answer/ranking save
                                $answers[0]['Answer']['question_id'] = $question_ids[0];
                                $answers[0]['Answer']['student_id'] = $this->Quiz->Student->id;
                                $answers[0]['Answer']['text'] = 'apple';
                                $answers[0]['Answer']['score'] = 2.00;

                                $answers[1]['Answer']['question_id'] = $question_ids[1];
                                $answers[1]['Answer']['student_id'] = $this->Quiz->Student->id;
                                $answers[1]['Answer']['text'] = 'strawberry';
                                $answers[1]['Answer']['score'] = 2.00;

                                $answers[2]['Answer']['question_id'] = $question_ids[2];
                                $answers[2]['Answer']['student_id'] = $this->Quiz->Student->id;
                                $answers[2]['Answer']['text'] = 'carrot';
                                $answers[2]['Answer']['score'] = 2.00;

                                $answers[3]['Answer']['question_id'] = $question_ids[5];
                                $answers[3]['Answer']['student_id'] = $this->Quiz->Student->id;
                                $answers[3]['Answer']['text'] = 'Banana';
                                $answers[3]['Answer']['score'] = 1.00;

                                $answers[4]['Answer']['question_id'] = $question_ids[5];
                                $answers[4]['Answer']['student_id'] = $this->Quiz->Student->id;
                                $answers[4]['Answer']['text'] = 'Pear';
                                $answers[4]['Answer']['score'] = 1.00;

                                $answers[5]['Answer']['question_id'] = $question_ids[5];
                                $answers[5]['Answer']['student_id'] = $this->Quiz->Student->id;
                                $answers[5]['Answer']['text'] = 'Orange';
                                $answers[5]['Answer']['score'] = 1.00;

                                $answers[6]['Answer']['question_id'] = $question_ids[6];
                                $answers[6]['Answer']['student_id'] = $this->Quiz->Student->id;
                                $answers[6]['Answer']['text'] = 'Pineapple';
                                $answers[6]['Answer']['score'] = 1.00;

                                $answers[7]['Answer']['question_id'] = $question_ids[8];
                                $answers[7]['Answer']['student_id'] = $this->Quiz->Student->id;
                                $answers[7]['Answer']['text'] = 'Yesterday I ate pineapple and today I will eat pears.';
                                $answers[7]['Answer']['score'] = 6.00;

                                $answers[8]['Answer']['question_id'] = $question_ids[9];
                                $answers[8]['Answer']['student_id'] = $this->Quiz->Student->id;
                                $answers[8]['Answer']['text'] = 'apple';
                                $answers[8]['Answer']['score'] = 3.00;

                                foreach ($answers as $key5 => $answer) { // save answers
                                    $this->Quiz->Student->Answer->create();
                                    $this->Quiz->Student->Answer->save($answer);
                                } // end of anwers saving

                                // Prepare ranking data
                                $ranking['Ranking']['quiz_id'] = $this->Quiz->id;
                                $ranking['Ranking']['student_id'] = $this->Quiz->Student->id;
                                $ranking['Ranking']['score'] = 19.00;
                                $ranking['Ranking']['total'] = 19.00; 

                                $this->Quiz->Ranking->create();
                                $this->Quiz->Ranking->save($ranking);
                            } // 6th student answers/ranking save end

                            if ($key4 == 6) { // 7th student answer/ranking save
                                $answers[0]['Answer']['question_id'] = $question_ids[0];
                                $answers[0]['Answer']['student_id'] = $this->Quiz->Student->id;
                                $answers[0]['Answer']['text'] = 'en tiijä';
                                $answers[0]['Answer']['score'] = 0.00;

                                $answers[1]['Answer']['question_id'] = $question_ids[1];
                                $answers[1]['Answer']['student_id'] = $this->Quiz->Student->id;
                                $answers[1]['Answer']['text'] = 'mää';
                                $answers[1]['Answer']['score'] = 0.00;

                                $answers[2]['Answer']['question_id'] = $question_ids[2];
                                $answers[2]['Answer']['student_id'] = $this->Quiz->Student->id;
                                $answers[2]['Answer']['text'] = 'sdgfasd';
                                $answers[2]['Answer']['score'] = 0.00;

                                $answers[3]['Answer']['question_id'] = $question_ids[5];
                                $answers[3]['Answer']['student_id'] = $this->Quiz->Student->id;
                                $answers[3]['Answer']['text'] = 'Cucumber';
                                $answers[3]['Answer']['score'] = 0.00;

                                $answers[4]['Answer']['question_id'] = $question_ids[5];
                                $answers[4]['Answer']['student_id'] = $this->Quiz->Student->id;
                                $answers[4]['Answer']['text'] = 'Banana';
                                $answers[4]['Answer']['score'] = 1.00;

                                $answers[5]['Answer']['question_id'] = $question_ids[5];
                                $answers[5]['Answer']['student_id'] = $this->Quiz->Student->id;
                                $answers[5]['Answer']['text'] = 'Grass';
                                $answers[5]['Answer']['score'] = 0.00;

                                $answers[6]['Answer']['question_id'] = $question_ids[6];
                                $answers[6]['Answer']['student_id'] = $this->Quiz->Student->id;
                                $answers[6]['Answer']['text'] = 'Ananas';
                                $answers[6]['Answer']['score'] = 0.00;

                                $answers[7]['Answer']['question_id'] = $question_ids[8];
                                $answers[7]['Answer']['student_id'] = $this->Quiz->Student->id;
                                $answers[7]['Answer']['text'] = 'ananas and banana';
                                $answers[7]['Answer']['score'] = 1.00;

                                $answers[8]['Answer']['question_id'] = $question_ids[9];
                                $answers[8]['Answer']['student_id'] = $this->Quiz->Student->id;
                                $answers[8]['Answer']['text'] = 'ananas';
                                $answers[8]['Answer']['score'] = 0.00;

                                foreach ($answers as $key5 => $answer) { // save answers
                                    $this->Quiz->Student->Answer->create();
                                    $this->Quiz->Student->Answer->save($answer);
                                } // end of anwers saving

                                // Prepare ranking data
                                $ranking['Ranking']['quiz_id'] = $this->Quiz->id;
                                $ranking['Ranking']['student_id'] = $this->Quiz->Student->id;
                                $ranking['Ranking']['score'] = 2.00;
                                $ranking['Ranking']['total'] = 19.00; 

                                $this->Quiz->Ranking->create();
                                $this->Quiz->Ranking->save($ranking);
                            } // 7th student answers/ranking save end

                            if ($key4 == 7) { // 8th student answer/ranking save
                                $answers[0]['Answer']['question_id'] = $question_ids[0];
                                $answers[0]['Answer']['student_id'] = $this->Quiz->Student->id;
                                $answers[0]['Answer']['text'] = 'apple';
                                $answers[0]['Answer']['score'] = 2.00;

                                $answers[1]['Answer']['question_id'] = $question_ids[1];
                                $answers[1]['Answer']['student_id'] = $this->Quiz->Student->id;
                                $answers[1]['Answer']['text'] = 'stwarberry';
                                $answers[1]['Answer']['score'] = 1.00;

                                $answers[2]['Answer']['question_id'] = $question_ids[2];
                                $answers[2]['Answer']['student_id'] = $this->Quiz->Student->id;
                                $answers[2]['Answer']['text'] = 'carrot';
                                $answers[2]['Answer']['score'] = 2.00;

                                $answers[3]['Answer']['question_id'] = $question_ids[5];
                                $answers[3]['Answer']['student_id'] = $this->Quiz->Student->id;
                                $answers[3]['Answer']['text'] = 'Banana';
                                $answers[3]['Answer']['score'] = 1.00;

                                $answers[4]['Answer']['question_id'] = $question_ids[5];
                                $answers[4]['Answer']['student_id'] = $this->Quiz->Student->id;
                                $answers[4]['Answer']['text'] = 'Pear';
                                $answers[4]['Answer']['score'] = 1.00;

                                $answers[5]['Answer']['question_id'] = $question_ids[5];
                                $answers[5]['Answer']['student_id'] = $this->Quiz->Student->id;
                                $answers[5]['Answer']['text'] = 'Orange';
                                $answers[5]['Answer']['score'] = 1.00;

                                $answers[6]['Answer']['question_id'] = $question_ids[6];
                                $answers[6]['Answer']['student_id'] = $this->Quiz->Student->id;
                                $answers[6]['Answer']['text'] = 'Pineapple';
                                $answers[6]['Answer']['score'] = 1.00;

                                $answers[7]['Answer']['question_id'] = $question_ids[8];
                                $answers[7]['Answer']['student_id'] = $this->Quiz->Student->id;
                                $answers[7]['Answer']['text'] = 'Apple is the best and banana is second best.';
                                $answers[7]['Answer']['score'] = 5.00;

                                $answers[8]['Answer']['question_id'] = $question_ids[9];
                                $answers[8]['Answer']['student_id'] = $this->Quiz->Student->id;
                                $answers[8]['Answer']['text'] = 'banana';
                                $answers[8]['Answer']['score'] = 3.00;

                                foreach ($answers as $key5 => $answer) { // save answers
                                    $this->Quiz->Student->Answer->create();
                                    $this->Quiz->Student->Answer->save($answer);
                                } // end of anwers saving

                                // Prepare ranking data
                                $ranking['Ranking']['quiz_id'] = $this->Quiz->id;
                                $ranking['Ranking']['student_id'] = $this->Quiz->Student->id;
                                $ranking['Ranking']['score'] = 17.00;
                                $ranking['Ranking']['total'] = 19.00; 

                                $this->Quiz->Ranking->create();
                                $this->Quiz->Ranking->save($ranking);
                            } // 8th student answers/ranking save end

                            if ($key4 == 8) { // 9th student answer/ranking save
                                $answers[0]['Answer']['question_id'] = $question_ids[0];
                                $answers[0]['Answer']['student_id'] = $this->Quiz->Student->id;
                                $answers[0]['Answer']['text'] = 'apple';
                                $answers[0]['Answer']['score'] = 2.00;

                                $answers[1]['Answer']['question_id'] = $question_ids[1];
                                $answers[1]['Answer']['student_id'] = $this->Quiz->Student->id;
                                $answers[1]['Answer']['text'] = '-';
                                $answers[1]['Answer']['score'] = 0.00;

                                $answers[2]['Answer']['question_id'] = $question_ids[2];
                                $answers[2]['Answer']['student_id'] = $this->Quiz->Student->id;
                                $answers[2]['Answer']['text'] = 'carrot';
                                $answers[2]['Answer']['score'] = 2.00;

                                $answers[3]['Answer']['question_id'] = $question_ids[5];
                                $answers[3]['Answer']['student_id'] = $this->Quiz->Student->id;
                                $answers[3]['Answer']['text'] = 'Banana';
                                $answers[3]['Answer']['score'] = 1.00;

                                $answers[4]['Answer']['question_id'] = $question_ids[5];
                                $answers[4]['Answer']['student_id'] = $this->Quiz->Student->id;
                                $answers[4]['Answer']['text'] = 'Cucumber';
                                $answers[4]['Answer']['score'] = 0.00;

                                $answers[5]['Answer']['question_id'] = $question_ids[5];
                                $answers[5]['Answer']['student_id'] = $this->Quiz->Student->id;
                                $answers[5]['Answer']['text'] = 'Orange';
                                $answers[5]['Answer']['score'] = 1.00;

                                $answers[6]['Answer']['question_id'] = $question_ids[6];
                                $answers[6]['Answer']['student_id'] = $this->Quiz->Student->id;
                                $answers[6]['Answer']['text'] = 'Pineapple';
                                $answers[6]['Answer']['score'] = 1.00;

                                $answers[7]['Answer']['question_id'] = $question_ids[8];
                                $answers[7]['Answer']['student_id'] = $this->Quiz->Student->id;
                                $answers[7]['Answer']['text'] = 'I dont like apple but I like orange.';
                                $answers[7]['Answer']['score'] = 5.00;

                                $answers[8]['Answer']['question_id'] = $question_ids[9];
                                $answers[8]['Answer']['student_id'] = $this->Quiz->Student->id;
                                $answers[8]['Answer']['text'] = 'orange';
                                $answers[8]['Answer']['score'] = 3.00;

                                foreach ($answers as $key5 => $answer) { // save answers
                                    $this->Quiz->Student->Answer->create();
                                    $this->Quiz->Student->Answer->save($answer);
                                } // end of anwers saving

                                // Prepare ranking data
                                $ranking['Ranking']['quiz_id'] = $this->Quiz->id;
                                $ranking['Ranking']['student_id'] = $this->Quiz->Student->id;
                                $ranking['Ranking']['score'] = 15.00;
                                $ranking['Ranking']['total'] = 19.00; 

                                $this->Quiz->Ranking->create();
                                $this->Quiz->Ranking->save($ranking);
                            } // 9th student answers/ranking save end


                        } // if student data saved end
                    } // end of first quiz students 

                } // Quiz 1 end

                if ($key1 == 1) { // Quiz 2
                    $questions[0]['Question']['quiz_id'] = $this->Quiz->id;
                    $questions[0]['Question']['question_type_id'] = 6;
                    $questions[0]['Question']['text'] = 'Kapulaote';
                    $questions[0]['Question']['explanation'] = '';
                    $questions[0]['Question']['weight'] = NULL;
                    $questions[0]['Question']['max_allowed'] = NULL;

                    $questions[1]['Question']['quiz_id'] = $this->Quiz->id;
                    $questions[1]['Question']['question_type_id'] = 7;
                    $questions[1]['Question']['text'] = 'New Question';
                    $questions[1]['Question']['explanation'] = 'Katso video ja vastaa sen jälkeen alla oleviin kysymyksiin. Kaikkien kysymysten vastaukset löytyvät videosta.';
                    $questions[1]['Question']['weight'] = NULL;
                    $questions[1]['Question']['max_allowed'] = NULL;

                    $questions[2]['Question']['quiz_id'] = $this->Quiz->id;
                    $questions[2]['Question']['question_type_id'] = 3;
                    $questions[2]['Question']['text'] = 'Mitkä sormet puristavat rumpukapulaa?';
                    $questions[2]['Question']['explanation'] = 'Jokaisesta oikeasta valinnasta +1p.';
                    $questions[2]['Question']['weight'] = NULL;
                    $questions[2]['Question']['max_allowed'] = 2;

                    $questions[3]['Question']['quiz_id'] = $this->Quiz->id;
                    $questions[3]['Question']['question_type_id'] = 3;
                    $questions[3]['Question']['text'] = 'Mitkä kolme seuraavista ovat tyypillisiä virheitä rumpukapulaotteessa?';
                    $questions[3]['Question']['explanation'] = 'Jokaisesta oikeasta valinnasta +1p.';
                    $questions[3]['Question']['weight'] = NULL;
                    $questions[3]['Question']['max_allowed'] = 3;

                    $questions[4]['Question']['quiz_id'] = $this->Quiz->id;
                    $questions[4]['Question']['question_type_id'] = 6;
                    $questions[4]['Question']['text'] = 'Rumpusetin osat';
                    $questions[4]['Question']['explanation'] = '';
                    $questions[4]['Question']['weight'] = NULL;
                    $questions[4]['Question']['max_allowed'] = NULL;

                    $questions[5]['Question']['quiz_id'] = $this->Quiz->id;
                    $questions[5]['Question']['question_type_id'] = 7;
                    $questions[5]['Question']['text'] = 'Katso video ja vastaa sen jälkeen alla oleviin kysymyksiin. Kaikkien kysymysten vastaukset löytyvät videosta.';
                    $questions[5]['Question']['explanation'] = 'Katso video ja vastaa sen jälkeen alla oleviin kysymyksiin. Kaikkien kysymysten vastaukset löytyvät videosta.';
                    $questions[5]['Question']['weight'] = NULL;
                    $questions[5]['Question']['max_allowed'] = NULL;

                    $questions[6]['Question']['quiz_id'] = $this->Quiz->id;
                    $questions[6]['Question']['question_type_id'] = 1;
                    $questions[6]['Question']['text'] = 'Mitkä ovat kaksi bassorummun tavallista soittotekniikkaa?';
                    $questions[6]['Question']['explanation'] = 'Valitse mielestäsi sopivin vaihtoehto. Oikeasta vastauksesta +2p, väärästä -2p.';
                    $questions[6]['Question']['weight'] = NULL;
                    $questions[6]['Question']['max_allowed'] = NULL;

                    $questions[7]['Question']['quiz_id'] = $this->Quiz->id;
                    $questions[7]['Question']['question_type_id'] = 3;
                    $questions[7]['Question']['text'] = 'Mitkä kolme rumpusetin osaa tarvitaan lähes kaikkien rumpukomppien soittamiseen?';
                    $questions[7]['Question']['explanation'] = 'Jokaisesta oikeasta vastauksesta +1p.';
                    $questions[7]['Question']['weight'] = NULL;
                    $questions[7]['Question']['max_allowed'] = 3;

                    $questions[8]['Question']['quiz_id'] = $this->Quiz->id;
                    $questions[8]['Question']['question_type_id'] = 6;
                    $questions[8]['Question']['text'] = 'Bonus-video:';
                    $questions[8]['Question']['explanation'] = '';
                    $questions[8]['Question']['weight'] = NULL;
                    $questions[8]['Question']['max_allowed'] = NULL;

                    $questions[9]['Question']['quiz_id'] = $this->Quiz->id;
                    $questions[9]['Question']['question_type_id'] = 7;
                    $questions[9]['Question']['text'] = 'New Question';
                    $questions[9]['Question']['explanation'] = 'Seuraavalla tunnilla opettelemme beat-kompin. Voit tutustua siihen jo ennalta katsomalla oheisen videon.';
                    $questions[9]['Question']['weight'] = NULL;
                    $questions[9]['Question']['max_allowed'] = NULL;

                    foreach ($questions as $key2 => $question) {
                        $this->Quiz->Question->create();
                        if ($this->Quiz->Question->save($question)) { // save related question
                            $question_ids[] = $this->Quiz->Question->id;
                            $choices = array();
                            // save related choice
                            if ($key2 == 1) { // 0 skip first question choices
                                $choice['Choice']['question_id'] = $this->Quiz->Question->id;
                                $choice['Choice']['text'] = 'https://www.youtube.com/embed/dCLfOu-QT58';
                                $choice['Choice']['points'] = 0.00;
                                $choice['Choice']['weight'] = NULL;
                                $this->Quiz->Question->Choice->create();
                                $this->Quiz->Question->Choice->save($choice);
                            } elseif ($key2 == 2) { // first question choices
                                $choices[0]['Choice']['question_id'] = $this->Quiz->Question->id;
                                $choices[0]['Choice']['text'] = 'Peukalo';
                                $choices[0]['Choice']['points'] = 1.00;
                                $choices[0]['Choice']['weight'] = NULL;

                                $choices[1]['Choice']['question_id'] = $this->Quiz->Question->id;
                                $choices[1]['Choice']['text'] = 'Etusormi';
                                $choices[1]['Choice']['points'] = 1.00;
                                $choices[1]['Choice']['weight'] = NULL;

                                $choices[2]['Choice']['question_id'] = $this->Quiz->Question->id;
                                $choices[2]['Choice']['text'] = 'Keskisormi';
                                $choices[2]['Choice']['points'] = 0.00;
                                $choices[2]['Choice']['weight'] = NULL;

                                $choices[3]['Choice']['question_id'] = $this->Quiz->Question->id;
                                $choices[3]['Choice']['text'] = 'Nimetön';
                                $choices[3]['Choice']['points'] = 0.00;
                                $choices[3]['Choice']['weight'] = NULL;

                                $choices[4]['Choice']['question_id'] = $this->Quiz->Question->id;
                                $choices[4]['Choice']['text'] = 'Pikkusormi';
                                $choices[4]['Choice']['points'] = 0.00;
                                $choices[4]['Choice']['weight'] = NULL;

                                foreach ($choices as $key3 => $choice) {
                                    $this->Quiz->Question->Choice->create();
                                    $this->Quiz->Question->Choice->save($choice);
                                }
                            } elseif ($key2 == 3) { // first question choices
                                $choices[0]['Choice']['question_id'] = $this->Quiz->Question->id;
                                $choices[0]['Choice']['text'] = 'Ote liian keskeltä kapulaa';
                                $choices[0]['Choice']['points'] = 1.00;
                                $choices[0]['Choice']['weight'] = NULL;

                                $choices[1]['Choice']['question_id'] = $this->Quiz->Question->id;
                                $choices[1]['Choice']['text'] = 'Ote väärällä kädellä';
                                $choices[1]['Choice']['points'] = 0.00;
                                $choices[1]['Choice']['weight'] = NULL;

                                $choices[2]['Choice']['question_id'] = $this->Quiz->Question->id;
                                $choices[2]['Choice']['text'] = 'Käsi puristaa liian kovaa kapulasta';
                                $choices[2]['Choice']['points'] = 1.00;
                                $choices[2]['Choice']['weight'] = NULL;

                                $choices[3]['Choice']['question_id'] = $this->Quiz->Question->id;
                                $choices[3]['Choice']['text'] = 'Etusormi on kapulan yläpuolella';
                                $choices[3]['Choice']['points'] = 1.00;
                                $choices[3]['Choice']['weight'] = NULL;

                                $choices[4]['Choice']['question_id'] = $this->Quiz->Question->id;
                                $choices[4]['Choice']['text'] = 'Väärän muotoiset kapulat';
                                $choices[4]['Choice']['points'] = 0.00;
                                $choices[4]['Choice']['weight'] = NULL;

                                foreach ($choices as $key3 => $choice) {
                                    $this->Quiz->Question->Choice->create();
                                    $this->Quiz->Question->Choice->save($choice);
                                }
                            } elseif ($key2 == 5) { // 4 skip question has not choice
                                $choice['Choice']['question_id'] = $this->Quiz->Question->id;
                                $choice['Choice']['text'] = 'https://www.youtube.com/embed/0HBPjqY_sNE';
                                $choice['Choice']['points'] = 0.00;
                                $choice['Choice']['weight'] = NULL;
                                $this->Quiz->Question->Choice->create();
                                $this->Quiz->Question->Choice->save($choice);
                            } elseif ($key2 == 6) { 
                                $choices[0]['Choice']['question_id'] = $this->Quiz->Question->id;
                                $choices[0]['Choice']['text'] = 'Polkaistaan varpailla tai polkaistaan kynsipuolella.';
                                $choices[0]['Choice']['points'] = -2.00;
                                $choices[0]['Choice']['weight'] = NULL;

                                $choices[1]['Choice']['question_id'] = $this->Quiz->Question->id;
                                $choices[1]['Choice']['text'] = 'Kantapää on ilmassa tai kantapää on kiinni pedaalissa.';
                                $choices[1]['Choice']['points'] = 2.00;
                                $choices[1]['Choice']['weight'] = NULL;

                                $choices[2]['Choice']['question_id'] = $this->Quiz->Question->id;
                                $choices[2]['Choice']['text'] = 'Jalka on pedaalilla poikittain tai suorassa.';
                                $choices[2]['Choice']['points'] = -2.00;
                                $choices[2]['Choice']['weight'] = NULL;

                                $choices[3]['Choice']['question_id'] = $this->Quiz->Question->id;
                                $choices[3]['Choice']['text'] = 'Soitetaan sukka jalassa tai ilman sukkaa.';
                                $choices[3]['Choice']['points'] = -2.00;
                                $choices[3]['Choice']['weight'] = NULL;

                                foreach ($choices as $key3 => $choice) {
                                    $this->Quiz->Question->Choice->create();
                                    $this->Quiz->Question->Choice->save($choice);
                                }
                            } elseif ($key2 == 7) { // first question choices
                                $choices[0]['Choice']['question_id'] = $this->Quiz->Question->id;
                                $choices[0]['Choice']['text'] = 'Crash-pelti';
                                $choices[0]['Choice']['points'] = 0.00;
                                $choices[0]['Choice']['weight'] = NULL;

                                $choices[1]['Choice']['question_id'] = $this->Quiz->Question->id;
                                $choices[1]['Choice']['text'] = 'Bassorumpu';
                                $choices[1]['Choice']['points'] = 1.00;
                                $choices[1]['Choice']['weight'] = NULL;

                                $choices[2]['Choice']['question_id'] = $this->Quiz->Question->id;
                                $choices[2]['Choice']['text'] = 'Hi-hat';
                                $choices[2]['Choice']['points'] = 1.00;
                                $choices[2]['Choice']['weight'] = NULL;

                                $choices[3]['Choice']['question_id'] = $this->Quiz->Question->id;
                                $choices[3]['Choice']['text'] = 'Ride-pelti';
                                $choices[3]['Choice']['points'] = 0.00;
                                $choices[3]['Choice']['weight'] = NULL;

                                $choices[4]['Choice']['question_id'] = $this->Quiz->Question->id;
                                $choices[4]['Choice']['text'] = 'Virveli';
                                $choices[4]['Choice']['points'] = 1.00;
                                $choices[4]['Choice']['weight'] = NULL;

                                $choices[5]['Choice']['question_id'] = $this->Quiz->Question->id;
                                $choices[5]['Choice']['text'] = 'Tomit';
                                $choices[5]['Choice']['points'] = 0.00;
                                $choices[5]['Choice']['weight'] = NULL;

                                foreach ($choices as $key3 => $choice) {
                                    $this->Quiz->Question->Choice->create();
                                    $this->Quiz->Question->Choice->save($choice);
                                }
                            } elseif ($key2 == 9) { // 8 skip question has not choice
                                $choice['Choice']['question_id'] = $this->Quiz->Question->id;
                                $choice['Choice']['text'] = 'https://www.youtube.com/embed/OrTACJTB_Gs';
                                $choice['Choice']['points'] = 0.00;
                                $choice['Choice']['weight'] = NULL;
                                $this->Quiz->Question->Choice->create();
                                $this->Quiz->Question->Choice->save($choice);
                            } else {
                                // do nothing
                            }
                        }
                    }

                    // 2nd quiz students
                    $students = array();
                    $students[0]['Student']['quiz_id'] = $this->Quiz->id;
                    $students[0]['Student']['fname'] = 'Kalle';
                    $students[0]['Student']['lname'] = 'Koululainen';
                    $students[0]['Student']['class'] = '6a';
                    $students[0]['Student']['status'] = 1;
                    $students[0]['Student']['submitted'] = date('Y-m-d H:i:s');

                    $students[1]['Student']['quiz_id'] = $this->Quiz->id;
                    $students[1]['Student']['fname'] = 'Fanni';
                    $students[1]['Student']['lname'] = 'Fanittaja';
                    $students[1]['Student']['class'] = '6a';
                    $students[1]['Student']['status'] = 1;
                    $students[1]['Student']['submitted'] = date('Y-m-d H:i:s');

                    $students[2]['Student']['quiz_id'] = $this->Quiz->id;
                    $students[2]['Student']['fname'] = 'Aija';
                    $students[2]['Student']['lname'] = 'Avulias';
                    $students[2]['Student']['class'] = '6a';
                    $students[2]['Student']['status'] = 1;
                    $students[2]['Student']['submitted'] = date('Y-m-d H:i:s');

                    $students[3]['Student']['quiz_id'] = $this->Quiz->id;
                    $students[3]['Student']['fname'] = 'Jarkko';
                    $students[3]['Student']['lname'] = 'Jonottaja';
                    $students[3]['Student']['class'] = '6c';
                    $students[3]['Student']['status'] = 1;
                    $students[3]['Student']['submitted'] = date('Y-m-d H:i:s');

                    $students[4]['Student']['quiz_id'] = $this->Quiz->id;
                    $students[4]['Student']['fname'] = 'Anna';
                    $students[4]['Student']['lname'] = 'Arvoituksellinen';
                    $students[4]['Student']['class'] = '6c';
                    $students[4]['Student']['status'] = 1;
                    $students[4]['Student']['submitted'] = date('Y-m-d H:i:s');

                    $students[5]['Student']['quiz_id'] = $this->Quiz->id;
                    $students[5]['Student']['fname'] = 'Valtteri';
                    $students[5]['Student']['lname'] = 'Vaihtolämpöinen';
                    $students[5]['Student']['class'] = '6c';
                    $students[5]['Student']['status'] = 1;
                    $students[5]['Student']['submitted'] = date('Y-m-d H:i:s');

                    $students[6]['Student']['quiz_id'] = $this->Quiz->id;
                    $students[6]['Student']['fname'] = 'Lauri';
                    $students[6]['Student']['lname'] = 'Laurinpoika';
                    $students[6]['Student']['class'] = '6d';
                    $students[6]['Student']['status'] = 1;
                    $students[6]['Student']['submitted'] = date('Y-m-d H:i:s');

                    $students[7]['Student']['quiz_id'] = $this->Quiz->id;
                    $students[7]['Student']['fname'] = 'Sirkku';
                    $students[7]['Student']['lname'] = 'Sirkuttaja';
                    $students[7]['Student']['class'] = '6d';
                    $students[7]['Student']['status'] = 1;
                    $students[7]['Student']['submitted'] = date('Y-m-d H:i:s');

                    $students[8]['Student']['quiz_id'] = $this->Quiz->id;
                    $students[8]['Student']['fname'] = 'Jonna';
                    $students[8]['Student']['lname'] = 'Jouhea';
                    $students[8]['Student']['class'] = '6d';
                    $students[8]['Student']['status'] = 1;
                    $students[8]['Student']['submitted'] = date('Y-m-d H:i:s');
                    
                    foreach ($students as $key4 => $student) { // Save all student of first quiz
                        $this->Quiz->Student->create();
                        if ($this->Quiz->Student->save($student)) { // Save student data
                            $answers = array();
                            if ($key4 == 0) { // First student answer/ranking save
                                // 0, 1 skip
                                $answers[0]['Answer']['question_id'] = $question_ids[2];
                                $answers[0]['Answer']['student_id'] = $this->Quiz->Student->id;
                                $answers[0]['Answer']['text'] = 'Peukalo';
                                $answers[0]['Answer']['score'] = 1.00;

                                $answers[1]['Answer']['question_id'] = $question_ids[2];
                                $answers[1]['Answer']['student_id'] = $this->Quiz->Student->id;
                                $answers[1]['Answer']['text'] = 'Etusormi';
                                $answers[1]['Answer']['score'] = 1.00;

                                $answers[2]['Answer']['question_id'] = $question_ids[3];
                                $answers[2]['Answer']['student_id'] = $this->Quiz->Student->id;
                                $answers[2]['Answer']['text'] = 'Ote liian keskeltä kapulaa';
                                $answers[2]['Answer']['score'] = 1.00;

                                $answers[3]['Answer']['question_id'] = $question_ids[3];
                                $answers[3]['Answer']['student_id'] = $this->Quiz->Student->id;
                                $answers[3]['Answer']['text'] = 'Käsi puristaa liian kovaa kapulasta';
                                $answers[3]['Answer']['score'] = 1.00;

                                $answers[4]['Answer']['question_id'] = $question_ids[3];
                                $answers[4]['Answer']['student_id'] = $this->Quiz->Student->id;
                                $answers[4]['Answer']['text'] = 'Etusormi on kapulan yläpuolella';
                                $answers[4]['Answer']['score'] = 1.00;

                                // 4/5 skipped
                                $answers[5]['Answer']['question_id'] = $question_ids[6];
                                $answers[5]['Answer']['student_id'] = $this->Quiz->Student->id;
                                $answers[5]['Answer']['text'] = 'Kantapää on ilmassa tai kantapää on kiinni pedaalissa.';
                                $answers[5]['Answer']['score'] = 2.00;


                                $answers[6]['Answer']['question_id'] = $question_ids[7];
                                $answers[6]['Answer']['student_id'] = $this->Quiz->Student->id;
                                $answers[6]['Answer']['text'] = 'Bassorumpu';
                                $answers[6]['Answer']['score'] = 1.00;

                                $answers[7]['Answer']['question_id'] = $question_ids[7];
                                $answers[7]['Answer']['student_id'] = $this->Quiz->Student->id;
                                $answers[7]['Answer']['text'] = 'Hi-hat';
                                $answers[7]['Answer']['score'] = 1.00;

                                $answers[8]['Answer']['question_id'] = $question_ids[7];
                                $answers[8]['Answer']['student_id'] = $this->Quiz->Student->id;
                                $answers[8]['Answer']['text'] = 'Virveli';
                                $answers[8]['Answer']['score'] = 1.00;


                                foreach ($answers as $key5 => $answer) { // save answers
                                    $this->Quiz->Student->Answer->create();
                                    $this->Quiz->Student->Answer->save($answer);
                                } // end of anwers saving

                                // Prepare ranking data
                                $ranking['Ranking']['quiz_id'] = $this->Quiz->id;
                                $ranking['Ranking']['student_id'] = $this->Quiz->Student->id;
                                $ranking['Ranking']['score'] = 10.00;
                                $ranking['Ranking']['total'] = 10.00; 

                                $this->Quiz->Ranking->create();
                                $this->Quiz->Ranking->save($ranking);
                            } // First student answers/ranking save end

                            if ($key4 == 1) { // 2nd student answer/ranking save
                                // 0, 1 skip
                                $answers[0]['Answer']['question_id'] = $question_ids[2];
                                $answers[0]['Answer']['student_id'] = $this->Quiz->Student->id;
                                $answers[0]['Answer']['text'] = 'Peukalo';
                                $answers[0]['Answer']['score'] = 1.00;

                                $answers[1]['Answer']['question_id'] = $question_ids[2];
                                $answers[1]['Answer']['student_id'] = $this->Quiz->Student->id;
                                $answers[1]['Answer']['text'] = 'Etusormi';
                                $answers[1]['Answer']['score'] = 1.00;

                                $answers[2]['Answer']['question_id'] = $question_ids[3];
                                $answers[2]['Answer']['student_id'] = $this->Quiz->Student->id;
                                $answers[2]['Answer']['text'] = 'Ote liian keskeltä kapulaa';
                                $answers[2]['Answer']['score'] = 1.00;

                                $answers[3]['Answer']['question_id'] = $question_ids[3];
                                $answers[3]['Answer']['student_id'] = $this->Quiz->Student->id;
                                $answers[3]['Answer']['text'] = 'Käsi puristaa liian kovaa kapulasta';
                                $answers[3]['Answer']['score'] = 1.00;

                                $answers[4]['Answer']['question_id'] = $question_ids[3];
                                $answers[4]['Answer']['student_id'] = $this->Quiz->Student->id;
                                $answers[4]['Answer']['text'] = 'Etusormi on kapulan yläpuolella';
                                $answers[4]['Answer']['score'] = 1.00;

                                // 4/5 skipped
                                $answers[5]['Answer']['question_id'] = $question_ids[6];
                                $answers[5]['Answer']['student_id'] = $this->Quiz->Student->id;
                                $answers[5]['Answer']['text'] = 'Kantapää on ilmassa tai kantapää on kiinni pedaalissa.';
                                $answers[5]['Answer']['score'] = 2.00;


                                $answers[6]['Answer']['question_id'] = $question_ids[7];
                                $answers[6]['Answer']['student_id'] = $this->Quiz->Student->id;
                                $answers[6]['Answer']['text'] = 'Bassorumpu';
                                $answers[6]['Answer']['score'] = 1.00;

                                $answers[7]['Answer']['question_id'] = $question_ids[7];
                                $answers[7]['Answer']['student_id'] = $this->Quiz->Student->id;
                                $answers[7]['Answer']['text'] = 'Hi-hat';
                                $answers[7]['Answer']['score'] = 1.00;

                                $answers[8]['Answer']['question_id'] = $question_ids[7];
                                $answers[8]['Answer']['student_id'] = $this->Quiz->Student->id;
                                $answers[8]['Answer']['text'] = 'Virveli';
                                $answers[8]['Answer']['score'] = 1.00;


                                foreach ($answers as $key5 => $answer) { // save answers
                                    $this->Quiz->Student->Answer->create();
                                    $this->Quiz->Student->Answer->save($answer);
                                } // end of anwers saving

                                // Prepare ranking data
                                $ranking['Ranking']['quiz_id'] = $this->Quiz->id;
                                $ranking['Ranking']['student_id'] = $this->Quiz->Student->id;
                                $ranking['Ranking']['score'] = 10.00;
                                $ranking['Ranking']['total'] = 10.00; 

                                $this->Quiz->Ranking->create();
                                $this->Quiz->Ranking->save($ranking);
                            } // 2nd student answers/ranking save end

                            if ($key4 == 2) { // 3rd student answer/ranking save
                                // 0, 1 skip
                                $answers[0]['Answer']['question_id'] = $question_ids[2];
                                $answers[0]['Answer']['student_id'] = $this->Quiz->Student->id;
                                $answers[0]['Answer']['text'] = 'Peukalo';
                                $answers[0]['Answer']['score'] = 1.00;

                                $answers[1]['Answer']['question_id'] = $question_ids[2];
                                $answers[1]['Answer']['student_id'] = $this->Quiz->Student->id;
                                $answers[1]['Answer']['text'] = 'Etusormi';
                                $answers[1]['Answer']['score'] = 1.00;

                                $answers[2]['Answer']['question_id'] = $question_ids[3];
                                $answers[2]['Answer']['student_id'] = $this->Quiz->Student->id;
                                $answers[2]['Answer']['text'] = 'Ote liian keskeltä kapulaa';
                                $answers[2]['Answer']['score'] = 1.00;

                                $answers[3]['Answer']['question_id'] = $question_ids[3];
                                $answers[3]['Answer']['student_id'] = $this->Quiz->Student->id;
                                $answers[3]['Answer']['text'] = 'Ote väärällä kädellä';
                                $answers[3]['Answer']['score'] = 0.00;

                                $answers[4]['Answer']['question_id'] = $question_ids[3];
                                $answers[4]['Answer']['student_id'] = $this->Quiz->Student->id;
                                $answers[4]['Answer']['text'] = 'Etusormi on kapulan yläpuolella';
                                $answers[4]['Answer']['score'] = 1.00;

                                // 4/5 skipped
                                $answers[5]['Answer']['question_id'] = $question_ids[6];
                                $answers[5]['Answer']['student_id'] = $this->Quiz->Student->id;
                                $answers[5]['Answer']['text'] = 'Kantapää on ilmassa tai kantapää on kiinni pedaalissa.';
                                $answers[5]['Answer']['score'] = 2.00;


                                $answers[6]['Answer']['question_id'] = $question_ids[7];
                                $answers[6]['Answer']['student_id'] = $this->Quiz->Student->id;
                                $answers[6]['Answer']['text'] = 'Bassorumpu';
                                $answers[6]['Answer']['score'] = 1.00;

                                $answers[7]['Answer']['question_id'] = $question_ids[7];
                                $answers[7]['Answer']['student_id'] = $this->Quiz->Student->id;
                                $answers[7]['Answer']['text'] = 'Hi-hat';
                                $answers[7]['Answer']['score'] = 1.00;

                                $answers[8]['Answer']['question_id'] = $question_ids[7];
                                $answers[8]['Answer']['student_id'] = $this->Quiz->Student->id;
                                $answers[8]['Answer']['text'] = 'Virveli';
                                $answers[8]['Answer']['score'] = 1.00;


                                foreach ($answers as $key5 => $answer) { // save answers
                                    $this->Quiz->Student->Answer->create();
                                    $this->Quiz->Student->Answer->save($answer);
                                } // end of anwers saving

                                // Prepare ranking data
                                $ranking['Ranking']['quiz_id'] = $this->Quiz->id;
                                $ranking['Ranking']['student_id'] = $this->Quiz->Student->id;
                                $ranking['Ranking']['score'] = 9.00;
                                $ranking['Ranking']['total'] = 10.00; 

                                $this->Quiz->Ranking->create();
                                $this->Quiz->Ranking->save($ranking);
                            } // 3rd student answers/ranking save end

                            if ($key4 == 3) { // 4th student answer/ranking save
                                // 0, 1 skip
                                $answers[0]['Answer']['question_id'] = $question_ids[2];
                                $answers[0]['Answer']['student_id'] = $this->Quiz->Student->id;
                                $answers[0]['Answer']['text'] = 'Peukalo';
                                $answers[0]['Answer']['score'] = 1.00;

                                $answers[1]['Answer']['question_id'] = $question_ids[2];
                                $answers[1]['Answer']['student_id'] = $this->Quiz->Student->id;
                                $answers[1]['Answer']['text'] = 'Etusormi';
                                $answers[1]['Answer']['score'] = 1.00;

                                $answers[2]['Answer']['question_id'] = $question_ids[3];
                                $answers[2]['Answer']['student_id'] = $this->Quiz->Student->id;
                                $answers[2]['Answer']['text'] = 'Ote liian keskeltä kapulaa';
                                $answers[2]['Answer']['score'] = 1.00;

                                $answers[3]['Answer']['question_id'] = $question_ids[3];
                                $answers[3]['Answer']['student_id'] = $this->Quiz->Student->id;
                                $answers[3]['Answer']['text'] = 'Käsi puristaa liian kovaa kapulasta';
                                $answers[3]['Answer']['score'] = 1.00;

                                $answers[4]['Answer']['question_id'] = $question_ids[3];
                                $answers[4]['Answer']['student_id'] = $this->Quiz->Student->id;
                                $answers[4]['Answer']['text'] = 'Etusormi on kapulan yläpuolella';
                                $answers[4]['Answer']['score'] = 1.00;

                                // 4/5 skipped
                                $answers[5]['Answer']['question_id'] = $question_ids[6];
                                $answers[5]['Answer']['student_id'] = $this->Quiz->Student->id;
                                $answers[5]['Answer']['text'] = 'Kantapää on ilmassa tai kantapää on kiinni pedaalissa.';
                                $answers[5]['Answer']['score'] = 2.00;


                                $answers[6]['Answer']['question_id'] = $question_ids[7];
                                $answers[6]['Answer']['student_id'] = $this->Quiz->Student->id;
                                $answers[6]['Answer']['text'] = 'Bassorumpu';
                                $answers[6]['Answer']['score'] = 1.00;

                                $answers[7]['Answer']['question_id'] = $question_ids[7];
                                $answers[7]['Answer']['student_id'] = $this->Quiz->Student->id;
                                $answers[7]['Answer']['text'] = 'Hi-hat';
                                $answers[7]['Answer']['score'] = 1.00;

                                $answers[8]['Answer']['question_id'] = $question_ids[7];
                                $answers[8]['Answer']['student_id'] = $this->Quiz->Student->id;
                                $answers[8]['Answer']['text'] = 'Virveli';
                                $answers[8]['Answer']['score'] = 1.00;


                                foreach ($answers as $key5 => $answer) { // save answers
                                    $this->Quiz->Student->Answer->create();
                                    $this->Quiz->Student->Answer->save($answer);
                                } // end of anwers saving

                                // Prepare ranking data
                                $ranking['Ranking']['quiz_id'] = $this->Quiz->id;
                                $ranking['Ranking']['student_id'] = $this->Quiz->Student->id;
                                $ranking['Ranking']['score'] = 10.00;
                                $ranking['Ranking']['total'] = 10.00; 

                                $this->Quiz->Ranking->create();
                                $this->Quiz->Ranking->save($ranking);
                            } // 4th student answers/ranking save end

                            if ($key4 == 4) { // 5th student answer/ranking save
                                // 0, 1 skip
                                $answers[0]['Answer']['question_id'] = $question_ids[2];
                                $answers[0]['Answer']['student_id'] = $this->Quiz->Student->id;
                                $answers[0]['Answer']['text'] = 'Peukalo';
                                $answers[0]['Answer']['score'] = 1.00;

                                $answers[1]['Answer']['question_id'] = $question_ids[2];
                                $answers[1]['Answer']['student_id'] = $this->Quiz->Student->id;
                                $answers[1]['Answer']['text'] = 'Etusormi';
                                $answers[1]['Answer']['score'] = 1.00;

                                $answers[2]['Answer']['question_id'] = $question_ids[3];
                                $answers[2]['Answer']['student_id'] = $this->Quiz->Student->id;
                                $answers[2]['Answer']['text'] = 'Ote liian keskeltä kapulaa';
                                $answers[2]['Answer']['score'] = 1.00;

                                $answers[3]['Answer']['question_id'] = $question_ids[3];
                                $answers[3]['Answer']['student_id'] = $this->Quiz->Student->id;
                                $answers[3]['Answer']['text'] = 'Käsi puristaa liian kovaa kapulasta';
                                $answers[3]['Answer']['score'] = 1.00;

                                $answers[4]['Answer']['question_id'] = $question_ids[3];
                                $answers[4]['Answer']['student_id'] = $this->Quiz->Student->id;
                                $answers[4]['Answer']['text'] = 'Etusormi on kapulan yläpuolella';
                                $answers[4]['Answer']['score'] = 1.00;

                                // 4/5 skipped
                                $answers[5]['Answer']['question_id'] = $question_ids[6];
                                $answers[5]['Answer']['student_id'] = $this->Quiz->Student->id;
                                $answers[5]['Answer']['text'] = 'Kantapää on ilmassa tai kantapää on kiinni pedaalissa.';
                                $answers[5]['Answer']['score'] = 2.00;


                                $answers[6]['Answer']['question_id'] = $question_ids[7];
                                $answers[6]['Answer']['student_id'] = $this->Quiz->Student->id;
                                $answers[6]['Answer']['text'] = 'Bassorumpu';
                                $answers[6]['Answer']['score'] = 1.00;

                                $answers[7]['Answer']['question_id'] = $question_ids[7];
                                $answers[7]['Answer']['student_id'] = $this->Quiz->Student->id;
                                $answers[7]['Answer']['text'] = 'Hi-hat';
                                $answers[7]['Answer']['score'] = 1.00;

                                $answers[8]['Answer']['question_id'] = $question_ids[7];
                                $answers[8]['Answer']['student_id'] = $this->Quiz->Student->id;
                                $answers[8]['Answer']['text'] = 'Virveli';
                                $answers[8]['Answer']['score'] = 1.00;


                                foreach ($answers as $key5 => $answer) { // save answers
                                    $this->Quiz->Student->Answer->create();
                                    $this->Quiz->Student->Answer->save($answer);
                                } // end of anwers saving

                                // Prepare ranking data
                                $ranking['Ranking']['quiz_id'] = $this->Quiz->id;
                                $ranking['Ranking']['student_id'] = $this->Quiz->Student->id;
                                $ranking['Ranking']['score'] = 10.00;
                                $ranking['Ranking']['total'] = 10.00; 

                                $this->Quiz->Ranking->create();
                                $this->Quiz->Ranking->save($ranking);
                            } // 5th student answers/ranking save end

                            if ($key4 == 5) { // 6th student answer/ranking save
                                // 0, 1 skip
                                $answers[0]['Answer']['question_id'] = $question_ids[2];
                                $answers[0]['Answer']['student_id'] = $this->Quiz->Student->id;
                                $answers[0]['Answer']['text'] = 'Peukalo';
                                $answers[0]['Answer']['score'] = 1.00;

                                $answers[1]['Answer']['question_id'] = $question_ids[2];
                                $answers[1]['Answer']['student_id'] = $this->Quiz->Student->id;
                                $answers[1]['Answer']['text'] = 'Etusormi';
                                $answers[1]['Answer']['score'] = 1.00;

                                $answers[2]['Answer']['question_id'] = $question_ids[3];
                                $answers[2]['Answer']['student_id'] = $this->Quiz->Student->id;
                                $answers[2]['Answer']['text'] = 'Ote liian keskeltä kapulaa';
                                $answers[2]['Answer']['score'] = 1.00;

                                $answers[3]['Answer']['question_id'] = $question_ids[3];
                                $answers[3]['Answer']['student_id'] = $this->Quiz->Student->id;
                                $answers[3]['Answer']['text'] = 'Käsi puristaa liian kovaa kapulasta';
                                $answers[3]['Answer']['score'] = 1.00;

                                $answers[4]['Answer']['question_id'] = $question_ids[3];
                                $answers[4]['Answer']['student_id'] = $this->Quiz->Student->id;
                                $answers[4]['Answer']['text'] = 'Etusormi on kapulan yläpuolella';
                                $answers[4]['Answer']['score'] = 1.00;

                                // 4/5 skipped
                                $answers[5]['Answer']['question_id'] = $question_ids[6];
                                $answers[5]['Answer']['student_id'] = $this->Quiz->Student->id;
                                $answers[5]['Answer']['text'] = 'Kantapää on ilmassa tai kantapää on kiinni pedaalissa.';
                                $answers[5]['Answer']['score'] = 2.00;


                                $answers[6]['Answer']['question_id'] = $question_ids[7];
                                $answers[6]['Answer']['student_id'] = $this->Quiz->Student->id;
                                $answers[6]['Answer']['text'] = 'Bassorumpu';
                                $answers[6]['Answer']['score'] = 1.00;

                                $answers[7]['Answer']['question_id'] = $question_ids[7];
                                $answers[7]['Answer']['student_id'] = $this->Quiz->Student->id;
                                $answers[7]['Answer']['text'] = 'Hi-hat';
                                $answers[7]['Answer']['score'] = 1.00;

                                $answers[8]['Answer']['question_id'] = $question_ids[7];
                                $answers[8]['Answer']['student_id'] = $this->Quiz->Student->id;
                                $answers[8]['Answer']['text'] = 'Virveli';
                                $answers[8]['Answer']['score'] = 1.00;


                                foreach ($answers as $key5 => $answer) { // save answers
                                    $this->Quiz->Student->Answer->create();
                                    $this->Quiz->Student->Answer->save($answer);
                                } // end of anwers saving

                                // Prepare ranking data
                                $ranking['Ranking']['quiz_id'] = $this->Quiz->id;
                                $ranking['Ranking']['student_id'] = $this->Quiz->Student->id;
                                $ranking['Ranking']['score'] = 10.00;
                                $ranking['Ranking']['total'] = 10.00; 

                                $this->Quiz->Ranking->create();
                                $this->Quiz->Ranking->save($ranking);
                            } // 6th student answers/ranking save end

                            if ($key4 == 6) { // 6th student answer/ranking save
                                // 0, 1 skip
                                $answers[0]['Answer']['question_id'] = $question_ids[2];
                                $answers[0]['Answer']['student_id'] = $this->Quiz->Student->id;
                                $answers[0]['Answer']['text'] = 'Peukalo';
                                $answers[0]['Answer']['score'] = 1.00;

                                $answers[1]['Answer']['question_id'] = $question_ids[2];
                                $answers[1]['Answer']['student_id'] = $this->Quiz->Student->id;
                                $answers[1]['Answer']['text'] = 'Etusormi';
                                $answers[1]['Answer']['score'] = 1.00;

                                $answers[2]['Answer']['question_id'] = $question_ids[3];
                                $answers[2]['Answer']['student_id'] = $this->Quiz->Student->id;
                                $answers[2]['Answer']['text'] = 'Ote liian keskeltä kapulaa';
                                $answers[2]['Answer']['score'] = 1.00;

                                $answers[3]['Answer']['question_id'] = $question_ids[3];
                                $answers[3]['Answer']['student_id'] = $this->Quiz->Student->id;
                                $answers[3]['Answer']['text'] = 'Ote väärällä kädellä';
                                $answers[3]['Answer']['score'] = 0.00;

                                $answers[4]['Answer']['question_id'] = $question_ids[3];
                                $answers[4]['Answer']['student_id'] = $this->Quiz->Student->id;
                                $answers[4]['Answer']['text'] = 'Etusormi on kapulan yläpuolella';
                                $answers[4]['Answer']['score'] = 1.00;

                                // 4/5 skipped
                                $answers[5]['Answer']['question_id'] = $question_ids[6];
                                $answers[5]['Answer']['student_id'] = $this->Quiz->Student->id;
                                $answers[5]['Answer']['text'] = 'Polkaistaan varpailla tai polkaistaan kynsipuolella.';
                                $answers[5]['Answer']['score'] = -2.00;


                                $answers[6]['Answer']['question_id'] = $question_ids[7];
                                $answers[6]['Answer']['student_id'] = $this->Quiz->Student->id;
                                $answers[6]['Answer']['text'] = 'Crash-pelti';
                                $answers[6]['Answer']['score'] = 0.00;

                                $answers[7]['Answer']['question_id'] = $question_ids[7];
                                $answers[7]['Answer']['student_id'] = $this->Quiz->Student->id;
                                $answers[7]['Answer']['text'] = 'Hi-hat';
                                $answers[7]['Answer']['score'] = 1.00;

                                $answers[8]['Answer']['question_id'] = $question_ids[7];
                                $answers[8]['Answer']['student_id'] = $this->Quiz->Student->id;
                                $answers[8]['Answer']['text'] = 'Virveli';
                                $answers[8]['Answer']['score'] = 1.00;


                                foreach ($answers as $key5 => $answer) { // save answers
                                    $this->Quiz->Student->Answer->create();
                                    $this->Quiz->Student->Answer->save($answer);
                                } // end of anwers saving

                                // Prepare ranking data
                                $ranking['Ranking']['quiz_id'] = $this->Quiz->id;
                                $ranking['Ranking']['student_id'] = $this->Quiz->Student->id;
                                $ranking['Ranking']['score'] = 4.00;
                                $ranking['Ranking']['total'] = 10.00; 

                                $this->Quiz->Ranking->create();
                                $this->Quiz->Ranking->save($ranking);
                            } // 7th student answers/ranking save end

                            if ($key4 == 7) { // 8th student answer/ranking save
                                // 0, 1 skip
                                $answers[0]['Answer']['question_id'] = $question_ids[2];
                                $answers[0]['Answer']['student_id'] = $this->Quiz->Student->id;
                                $answers[0]['Answer']['text'] = 'Peukalo';
                                $answers[0]['Answer']['score'] = 1.00;

                                $answers[1]['Answer']['question_id'] = $question_ids[2];
                                $answers[1]['Answer']['student_id'] = $this->Quiz->Student->id;
                                $answers[1]['Answer']['text'] = 'Etusormi';
                                $answers[1]['Answer']['score'] = 1.00;

                                $answers[2]['Answer']['question_id'] = $question_ids[3];
                                $answers[2]['Answer']['student_id'] = $this->Quiz->Student->id;
                                $answers[2]['Answer']['text'] = 'Ote liian keskeltä kapulaa';
                                $answers[2]['Answer']['score'] = 1.00;

                                $answers[3]['Answer']['question_id'] = $question_ids[3];
                                $answers[3]['Answer']['student_id'] = $this->Quiz->Student->id;
                                $answers[3]['Answer']['text'] = 'Käsi puristaa liian kovaa kapulasta';
                                $answers[3]['Answer']['score'] = 1.00;

                                $answers[4]['Answer']['question_id'] = $question_ids[3];
                                $answers[4]['Answer']['student_id'] = $this->Quiz->Student->id;
                                $answers[4]['Answer']['text'] = 'Etusormi on kapulan yläpuolella';
                                $answers[4]['Answer']['score'] = 1.00;

                                // 4/5 skipped
                                $answers[5]['Answer']['question_id'] = $question_ids[6];
                                $answers[5]['Answer']['student_id'] = $this->Quiz->Student->id;
                                $answers[5]['Answer']['text'] = 'Kantapää on ilmassa tai kantapää on kiinni pedaalissa.';
                                $answers[5]['Answer']['score'] = 2.00;


                                $answers[6]['Answer']['question_id'] = $question_ids[7];
                                $answers[6]['Answer']['student_id'] = $this->Quiz->Student->id;
                                $answers[6]['Answer']['text'] = 'Bassorumpu';
                                $answers[6]['Answer']['score'] = 1.00;

                                $answers[7]['Answer']['question_id'] = $question_ids[7];
                                $answers[7]['Answer']['student_id'] = $this->Quiz->Student->id;
                                $answers[7]['Answer']['text'] = 'Hi-hat';
                                $answers[7]['Answer']['score'] = 1.00;

                                $answers[8]['Answer']['question_id'] = $question_ids[7];
                                $answers[8]['Answer']['student_id'] = $this->Quiz->Student->id;
                                $answers[8]['Answer']['text'] = 'Virveli';
                                $answers[8]['Answer']['score'] = 1.00;


                                foreach ($answers as $key5 => $answer) { // save answers
                                    $this->Quiz->Student->Answer->create();
                                    $this->Quiz->Student->Answer->save($answer);
                                } // end of anwers saving

                                // Prepare ranking data
                                $ranking['Ranking']['quiz_id'] = $this->Quiz->id;
                                $ranking['Ranking']['student_id'] = $this->Quiz->Student->id;
                                $ranking['Ranking']['score'] = 10.00;
                                $ranking['Ranking']['total'] = 10.00; 

                                $this->Quiz->Ranking->create();
                                $this->Quiz->Ranking->save($ranking);
                            } // 8th student answers/ranking save end

                            if ($key4 == 8) { // 9th student answer/ranking save
                                // 0, 1 skip
                                $answers[0]['Answer']['question_id'] = $question_ids[2];
                                $answers[0]['Answer']['student_id'] = $this->Quiz->Student->id;
                                $answers[0]['Answer']['text'] = 'Peukalo';
                                $answers[0]['Answer']['score'] = 1.00;

                                $answers[1]['Answer']['question_id'] = $question_ids[2];
                                $answers[1]['Answer']['student_id'] = $this->Quiz->Student->id;
                                $answers[1]['Answer']['text'] = 'Etusormi';
                                $answers[1]['Answer']['score'] = 1.00;

                                $answers[2]['Answer']['question_id'] = $question_ids[3];
                                $answers[2]['Answer']['student_id'] = $this->Quiz->Student->id;
                                $answers[2]['Answer']['text'] = 'Ote liian keskeltä kapulaa';
                                $answers[2]['Answer']['score'] = 1.00;

                                $answers[3]['Answer']['question_id'] = $question_ids[3];
                                $answers[3]['Answer']['student_id'] = $this->Quiz->Student->id;
                                $answers[3]['Answer']['text'] = 'Käsi puristaa liian kovaa kapulasta';
                                $answers[3]['Answer']['score'] = 1.00;

                                $answers[4]['Answer']['question_id'] = $question_ids[3];
                                $answers[4]['Answer']['student_id'] = $this->Quiz->Student->id;
                                $answers[4]['Answer']['text'] = 'Etusormi on kapulan yläpuolella';
                                $answers[4]['Answer']['score'] = 1.00;

                                // 4/5 skipped
                                $answers[5]['Answer']['question_id'] = $question_ids[6];
                                $answers[5]['Answer']['student_id'] = $this->Quiz->Student->id;
                                $answers[5]['Answer']['text'] = 'Kantapää on ilmassa tai kantapää on kiinni pedaalissa.';
                                $answers[5]['Answer']['score'] = 2.00;


                                $answers[6]['Answer']['question_id'] = $question_ids[7];
                                $answers[6]['Answer']['student_id'] = $this->Quiz->Student->id;
                                $answers[6]['Answer']['text'] = 'Bassorumpu';
                                $answers[6]['Answer']['score'] = 1.00;

                                $answers[7]['Answer']['question_id'] = $question_ids[7];
                                $answers[7]['Answer']['student_id'] = $this->Quiz->Student->id;
                                $answers[7]['Answer']['text'] = 'Hi-hat';
                                $answers[7]['Answer']['score'] = 1.00;

                                $answers[8]['Answer']['question_id'] = $question_ids[7];
                                $answers[8]['Answer']['student_id'] = $this->Quiz->Student->id;
                                $answers[8]['Answer']['text'] = 'Virveli';
                                $answers[8]['Answer']['score'] = 1.00;


                                foreach ($answers as $key5 => $answer) { // save answers
                                    $this->Quiz->Student->Answer->create();
                                    $this->Quiz->Student->Answer->save($answer);
                                } // end of anwers saving

                                // Prepare ranking data
                                $ranking['Ranking']['quiz_id'] = $this->Quiz->id;
                                $ranking['Ranking']['student_id'] = $this->Quiz->Student->id;
                                $ranking['Ranking']['score'] = 10.00;
                                $ranking['Ranking']['total'] = 10.00; 

                                $this->Quiz->Ranking->create();
                                $this->Quiz->Ranking->save($ranking);
                            } // 9th student answers/ranking save end
                        }
                    } // 2nd quiz student end

                } // Quiz 2 end

                if ($key1 == 2) { // Quiz 3
                    $questions[0]['Question']['quiz_id'] = $this->Quiz->id;
                    $questions[0]['Question']['question_type_id'] = 1;
                    $questions[0]['Question']['text'] = 'Keskityn opetukseen ja tehtäviin';
                    $questions[0]['Question']['explanation'] = '';
                    $questions[0]['Question']['weight'] = 12;
                    $questions[0]['Question']['max_allowed'] = NULL;

                    $questions[1]['Question']['quiz_id'] = $this->Quiz->id;
                    $questions[1]['Question']['question_type_id'] = 1;
                    $questions[1]['Question']['text'] = 'Viittaan kysymyksiin';
                    $questions[1]['Question']['explanation'] = '';
                    $questions[1]['Question']['weight'] = 11;
                    $questions[1]['Question']['max_allowed'] = NULL;

                    $questions[2]['Question']['quiz_id'] = $this->Quiz->id;
                    $questions[2]['Question']['question_type_id'] = 1;
                    $questions[2]['Question']['text'] = 'Haluan tutustua minulle vieraampiinkin oppikokonaisuuksiin';
                    $questions[2]['Question']['explanation'] = '';
                    $questions[2]['Question']['weight'] = 10;
                    $questions[2]['Question']['max_allowed'] = NULL;

                    $questions[3]['Question']['quiz_id'] = $this->Quiz->id;
                    $questions[3]['Question']['question_type_id'] = 6;
                    $questions[3]['Question']['text'] = 'Opiskelu';
                    $questions[3]['Question']['explanation'] = '';
                    $questions[3]['Question']['weight'] = 13;
                    $questions[3]['Question']['max_allowed'] = NULL;

                    $questions[4]['Question']['quiz_id'] = $this->Quiz->id;
                    $questions[4]['Question']['question_type_id'] = 6;
                    $questions[4]['Question']['text'] = 'Käytös ja huolellisuus';
                    $questions[4]['Question']['explanation'] = '';
                    $questions[4]['Question']['weight'] = 8;
                    $questions[4]['Question']['max_allowed'] = NULL;

                    $questions[5]['Question']['quiz_id'] = $this->Quiz->id;
                    $questions[5]['Question']['question_type_id'] = 1;
                    $questions[5]['Question']['text'] = 'Kannan osaltani vastuuta luokkatilan siisteydestä';
                    $questions[5]['Question']['explanation'] = '';
                    $questions[5]['Question']['weight'] = 7;
                    $questions[5]['Question']['max_allowed'] = NULL;

                    $questions[6]['Question']['quiz_id'] = $this->Quiz->id;
                    $questions[6]['Question']['question_type_id'] = 1;
                    $questions[6]['Question']['text'] = 'Käsittelen opetusvälineitä asiallisesti';
                    $questions[6]['Question']['explanation'] = '';
                    $questions[6]['Question']['weight'] = 6;
                    $questions[6]['Question']['max_allowed'] = NULL;

                    $questions[7]['Question']['quiz_id'] = $this->Quiz->id;
                    $questions[7]['Question']['question_type_id'] = 1;
                    $questions[7]['Question']['text'] = 'Saavun ajoissa tunnille';
                    $questions[7]['Question']['explanation'] = '';
                    $questions[7]['Question']['weight'] = 5;
                    $questions[7]['Question']['max_allowed'] = NULL;

                    $questions[8]['Question']['quiz_id'] = $this->Quiz->id;
                    $questions[8]['Question']['question_type_id'] = 1;
                    $questions[8]['Question']['text'] = 'Pyydän puheenvuoroa viittaamalla';
                    $questions[8]['Question']['explanation'] = '';
                    $questions[8]['Question']['weight'] = 4;
                    $questions[8]['Question']['max_allowed'] = NULL;

                    $questions[9]['Question']['quiz_id'] = $this->Quiz->id;
                    $questions[9]['Question']['question_type_id'] = 6;
                    $questions[9]['Question']['text'] = 'Ryhmässä toimiminen';
                    $questions[9]['Question']['explanation'] = '';
                    $questions[9]['Question']['weight'] = 3;
                    $questions[9]['Question']['max_allowed'] = NULL;

                    $questions[10]['Question']['quiz_id'] = $this->Quiz->id;
                    $questions[10]['Question']['question_type_id'] = 1;
                    $questions[10]['Question']['text'] = 'Autan ja neuvon luokkatovereita';
                    $questions[10]['Question']['explanation'] = '';
                    $questions[10]['Question']['weight'] = 2;
                    $questions[10]['Question']['max_allowed'] = NULL;

                    $questions[11]['Question']['quiz_id'] = $this->Quiz->id;
                    $questions[11]['Question']['question_type_id'] = 1;
                    $questions[11]['Question']['text'] = 'Toimin aktiivisesti osana ryhmää (esim. ryhmätöissä)';
                    $questions[11]['Question']['explanation'] = '';
                    $questions[11]['Question']['weight'] = 1;
                    $questions[11]['Question']['max_allowed'] = NULL;

                    $questions[12]['Question']['quiz_id'] = $this->Quiz->id;
                    $questions[12]['Question']['question_type_id'] = 1;
                    $questions[12]['Question']['text'] = 'Pyrin kehittymään pitkäjänteisesti';
                    $questions[12]['Question']['explanation'] = '';
                    $questions[12]['Question']['weight'] = 9;
                    $questions[12]['Question']['max_allowed'] = NULL;

                    $questions[13]['Question']['quiz_id'] = $this->Quiz->id;
                    $questions[13]['Question']['question_type_id'] = 1;
                    $questions[13]['Question']['text'] = 'Annan toisillekin työrauhan';
                    $questions[13]['Question']['explanation'] = '';
                    $questions[13]['Question']['weight'] = NULL;
                    $questions[13]['Question']['max_allowed'] = NULL;

                    $questions[14]['Question']['quiz_id'] = $this->Quiz->id;
                    $questions[14]['Question']['question_type_id'] = 6;
                    $questions[14]['Question']['text'] = 'Arvosana';
                    $questions[14]['Question']['explanation'] = '';
                    $questions[14]['Question']['weight'] = NULL;
                    $questions[14]['Question']['max_allowed'] = NULL;

                    $questions[15]['Question']['quiz_id'] = $this->Quiz->id;
                    $questions[15]['Question']['question_type_id'] = 1;
                    $questions[15]['Question']['text'] = 'Mielestäni oikea arvosana todistukseeni on';
                    $questions[15]['Question']['explanation'] = '';
                    $questions[15]['Question']['weight'] = NULL;
                    $questions[15]['Question']['max_allowed'] = NULL;

                    $questions[16]['Question']['quiz_id'] = $this->Quiz->id;
                    $questions[16]['Question']['question_type_id'] = 5;
                    $questions[16]['Question']['text'] = 'Vapaa sana';
                    $questions[16]['Question']['explanation'] = 'Alla olevaan tekstikenttään voit kertoa tarkemmin ajatuksiasi.';
                    $questions[16]['Question']['weight'] = NULL;
                    $questions[16]['Question']['max_allowed'] = NULL;

                    foreach ($questions as $key2 => $question) {
                        $this->Quiz->Question->create();
                        if ($this->Quiz->Question->save($question)) { // save related question
                            $question_ids[] = $this->Quiz->Question->id;
                            $choices = array();

                            // save related choice
                            if ($key2 == 0) { // first question choices # 231
                                $choices[0]['Choice']['question_id'] = $this->Quiz->Question->id;
                                $choices[0]['Choice']['text'] = 'Jatkuvasti';
                                $choices[0]['Choice']['points'] = 0.00;
                                $choices[0]['Choice']['weight'] = NULL;

                                $choices[1]['Choice']['question_id'] = $this->Quiz->Question->id;
                                $choices[1]['Choice']['text'] = 'Joskus';
                                $choices[1]['Choice']['points'] = 0.00;
                                $choices[1]['Choice']['weight'] = NULL;

                                $choices[2]['Choice']['question_id'] = $this->Quiz->Question->id;
                                $choices[2]['Choice']['text'] = 'Harvoin';
                                $choices[2]['Choice']['points'] = 0.00;
                                $choices[2]['Choice']['weight'] = NULL;

                                $choices[3]['Choice']['question_id'] = $this->Quiz->Question->id;
                                $choices[3]['Choice']['text'] = 'En koskaan';
                                $choices[3]['Choice']['points'] = 0.00;
                                $choices[3]['Choice']['weight'] = NULL;

                                foreach ($choices as $key3 => $choice) {
                                    $this->Quiz->Question->Choice->create();
                                    $this->Quiz->Question->Choice->save($choice);
                                }
                            } elseif ($key2 == 1) { // 2nd question choices #232
                                $choices[0]['Choice']['question_id'] = $this->Quiz->Question->id;
                                $choices[0]['Choice']['text'] = 'Jatkuvasti';
                                $choices[0]['Choice']['points'] = 0.00;
                                $choices[0]['Choice']['weight'] = NULL;

                                $choices[1]['Choice']['question_id'] = $this->Quiz->Question->id;
                                $choices[1]['Choice']['text'] = 'Joskus';
                                $choices[1]['Choice']['points'] = 0.00;
                                $choices[1]['Choice']['weight'] = NULL;

                                $choices[2]['Choice']['question_id'] = $this->Quiz->Question->id;
                                $choices[2]['Choice']['text'] = 'Harvoin';
                                $choices[2]['Choice']['points'] = 0.00;
                                $choices[2]['Choice']['weight'] = NULL;

                                $choices[3]['Choice']['question_id'] = $this->Quiz->Question->id;
                                $choices[3]['Choice']['text'] = 'En koskaan';
                                $choices[3]['Choice']['points'] = 0.00;
                                $choices[3]['Choice']['weight'] = NULL;

                                foreach ($choices as $key3 => $choice) {
                                    $this->Quiz->Question->Choice->create();
                                    $this->Quiz->Question->Choice->save($choice);
                                }
                            } elseif ($key2 == 2) { //third question choices
                                $choices[0]['Choice']['question_id'] = $this->Quiz->Question->id;
                                $choices[0]['Choice']['text'] = 'Jatkuvasti';
                                $choices[0]['Choice']['points'] = 0.00;
                                $choices[0]['Choice']['weight'] = NULL;

                                $choices[1]['Choice']['question_id'] = $this->Quiz->Question->id;
                                $choices[1]['Choice']['text'] = 'Joskus';
                                $choices[1]['Choice']['points'] = 0.00;
                                $choices[1]['Choice']['weight'] = NULL;

                                $choices[2]['Choice']['question_id'] = $this->Quiz->Question->id;
                                $choices[2]['Choice']['text'] = 'Harvoin';
                                $choices[2]['Choice']['points'] = 0.00;
                                $choices[2]['Choice']['weight'] = NULL;

                                $choices[3]['Choice']['question_id'] = $this->Quiz->Question->id;
                                $choices[3]['Choice']['text'] = 'En koskaan';
                                $choices[3]['Choice']['points'] = 0.00;
                                $choices[3]['Choice']['weight'] = NULL;

                                foreach ($choices as $key3 => $choice) {
                                    $this->Quiz->Question->Choice->create();
                                    $this->Quiz->Question->Choice->save($choice);
                                }
                            } elseif ($key2 == 5) { // 3/4 skip 6th question choices
                                $choices[0]['Choice']['question_id'] = $this->Quiz->Question->id;
                                $choices[0]['Choice']['text'] = 'Jatkuvasti';
                                $choices[0]['Choice']['points'] = 0.00;
                                $choices[0]['Choice']['weight'] = NULL;

                                $choices[1]['Choice']['question_id'] = $this->Quiz->Question->id;
                                $choices[1]['Choice']['text'] = 'Joskus';
                                $choices[1]['Choice']['points'] = 0.00;
                                $choices[1]['Choice']['weight'] = NULL;

                                $choices[2]['Choice']['question_id'] = $this->Quiz->Question->id;
                                $choices[2]['Choice']['text'] = 'Harvoin';
                                $choices[2]['Choice']['points'] = 0.00;
                                $choices[2]['Choice']['weight'] = NULL;

                                $choices[3]['Choice']['question_id'] = $this->Quiz->Question->id;
                                $choices[3]['Choice']['text'] = 'En koskaan';
                                $choices[3]['Choice']['points'] = 0.00;
                                $choices[3]['Choice']['weight'] = NULL;

                                foreach ($choices as $key3 => $choice) {
                                    $this->Quiz->Question->Choice->create();
                                    $this->Quiz->Question->Choice->save($choice);
                                }
                            } elseif ($key2 == 6) { // 7th question choices
                                $choices[0]['Choice']['question_id'] = $this->Quiz->Question->id;
                                $choices[0]['Choice']['text'] = 'Jatkuvasti';
                                $choices[0]['Choice']['points'] = 0.00;
                                $choices[0]['Choice']['weight'] = NULL;

                                $choices[1]['Choice']['question_id'] = $this->Quiz->Question->id;
                                $choices[1]['Choice']['text'] = 'Joskus';
                                $choices[1]['Choice']['points'] = 0.00;
                                $choices[1]['Choice']['weight'] = NULL;

                                $choices[2]['Choice']['question_id'] = $this->Quiz->Question->id;
                                $choices[2]['Choice']['text'] = 'Harvoin';
                                $choices[2]['Choice']['points'] = 0.00;
                                $choices[2]['Choice']['weight'] = NULL;

                                $choices[3]['Choice']['question_id'] = $this->Quiz->Question->id;
                                $choices[3]['Choice']['text'] = 'En koskaan';
                                $choices[3]['Choice']['points'] = 0.00;
                                $choices[3]['Choice']['weight'] = NULL;

                                foreach ($choices as $key3 => $choice) {
                                    $this->Quiz->Question->Choice->create();
                                    $this->Quiz->Question->Choice->save($choice);
                                }
                            } elseif ($key2 == 7) { // 3/4 skip 8th question choices
                                $choices[0]['Choice']['question_id'] = $this->Quiz->Question->id;
                                $choices[0]['Choice']['text'] = 'Jatkuvasti';
                                $choices[0]['Choice']['points'] = 0.00;
                                $choices[0]['Choice']['weight'] = NULL;

                                $choices[1]['Choice']['question_id'] = $this->Quiz->Question->id;
                                $choices[1]['Choice']['text'] = 'Joskus';
                                $choices[1]['Choice']['points'] = 0.00;
                                $choices[1]['Choice']['weight'] = NULL;

                                $choices[2]['Choice']['question_id'] = $this->Quiz->Question->id;
                                $choices[2]['Choice']['text'] = 'Harvoin';
                                $choices[2]['Choice']['points'] = 0.00;
                                $choices[2]['Choice']['weight'] = NULL;

                                $choices[3]['Choice']['question_id'] = $this->Quiz->Question->id;
                                $choices[3]['Choice']['text'] = 'En koskaan';
                                $choices[3]['Choice']['points'] = 0.00;
                                $choices[3]['Choice']['weight'] = NULL;

                                foreach ($choices as $key3 => $choice) {
                                    $this->Quiz->Question->Choice->create();
                                    $this->Quiz->Question->Choice->save($choice);
                                }
                            } elseif ($key2 == 8) { // 9th question choices
                                $choices[0]['Choice']['question_id'] = $this->Quiz->Question->id;
                                $choices[0]['Choice']['text'] = 'Jatkuvasti';
                                $choices[0]['Choice']['points'] = 0.00;
                                $choices[0]['Choice']['weight'] = NULL;

                                $choices[1]['Choice']['question_id'] = $this->Quiz->Question->id;
                                $choices[1]['Choice']['text'] = 'Joskus';
                                $choices[1]['Choice']['points'] = 0.00;
                                $choices[1]['Choice']['weight'] = NULL;

                                $choices[2]['Choice']['question_id'] = $this->Quiz->Question->id;
                                $choices[2]['Choice']['text'] = 'Harvoin';
                                $choices[2]['Choice']['points'] = 0.00;
                                $choices[2]['Choice']['weight'] = NULL;

                                $choices[3]['Choice']['question_id'] = $this->Quiz->Question->id;
                                $choices[3]['Choice']['text'] = 'En koskaan';
                                $choices[3]['Choice']['points'] = 0.00;
                                $choices[3]['Choice']['weight'] = NULL;

                                foreach ($choices as $key3 => $choice) {
                                    $this->Quiz->Question->Choice->create();
                                    $this->Quiz->Question->Choice->save($choice);
                                }
                            } elseif ($key2 == 10) { // 7th question choices
                                $choices[0]['Choice']['question_id'] = $this->Quiz->Question->id;
                                $choices[0]['Choice']['text'] = 'Jatkuvasti';
                                $choices[0]['Choice']['points'] = 0.00;
                                $choices[0]['Choice']['weight'] = NULL;

                                $choices[1]['Choice']['question_id'] = $this->Quiz->Question->id;
                                $choices[1]['Choice']['text'] = 'Joskus';
                                $choices[1]['Choice']['points'] = 0.00;
                                $choices[1]['Choice']['weight'] = NULL;

                                $choices[2]['Choice']['question_id'] = $this->Quiz->Question->id;
                                $choices[2]['Choice']['text'] = 'Harvoin';
                                $choices[2]['Choice']['points'] = 0.00;
                                $choices[2]['Choice']['weight'] = NULL;

                                $choices[3]['Choice']['question_id'] = $this->Quiz->Question->id;
                                $choices[3]['Choice']['text'] = 'En koskaan';
                                $choices[3]['Choice']['points'] = 0.00;
                                $choices[3]['Choice']['weight'] = NULL;

                                foreach ($choices as $key3 => $choice) {
                                    $this->Quiz->Question->Choice->create();
                                    $this->Quiz->Question->Choice->save($choice);
                                }
                            } elseif ($key2 == 11) { // 3/4 skip 8th question choices
                                $choices[0]['Choice']['question_id'] = $this->Quiz->Question->id;
                                $choices[0]['Choice']['text'] = 'Jatkuvasti';
                                $choices[0]['Choice']['points'] = 0.00;
                                $choices[0]['Choice']['weight'] = NULL;

                                $choices[1]['Choice']['question_id'] = $this->Quiz->Question->id;
                                $choices[1]['Choice']['text'] = 'Joskus';
                                $choices[1]['Choice']['points'] = 0.00;
                                $choices[1]['Choice']['weight'] = NULL;

                                $choices[2]['Choice']['question_id'] = $this->Quiz->Question->id;
                                $choices[2]['Choice']['text'] = 'Harvoin';
                                $choices[2]['Choice']['points'] = 0.00;
                                $choices[2]['Choice']['weight'] = NULL;

                                $choices[3]['Choice']['question_id'] = $this->Quiz->Question->id;
                                $choices[3]['Choice']['text'] = 'En koskaan';
                                $choices[3]['Choice']['points'] = 0.00;
                                $choices[3]['Choice']['weight'] = NULL;

                                foreach ($choices as $key3 => $choice) {
                                    $this->Quiz->Question->Choice->create();
                                    $this->Quiz->Question->Choice->save($choice);
                                }
                            } elseif ($key2 == 12) { // 9th question choices
                                $choices[0]['Choice']['question_id'] = $this->Quiz->Question->id;
                                $choices[0]['Choice']['text'] = 'Jatkuvasti';
                                $choices[0]['Choice']['points'] = 0.00;
                                $choices[0]['Choice']['weight'] = NULL;

                                $choices[1]['Choice']['question_id'] = $this->Quiz->Question->id;
                                $choices[1]['Choice']['text'] = 'Joskus';
                                $choices[1]['Choice']['points'] = 0.00;
                                $choices[1]['Choice']['weight'] = NULL;

                                $choices[2]['Choice']['question_id'] = $this->Quiz->Question->id;
                                $choices[2]['Choice']['text'] = 'Harvoin';
                                $choices[2]['Choice']['points'] = 0.00;
                                $choices[2]['Choice']['weight'] = NULL;

                                $choices[3]['Choice']['question_id'] = $this->Quiz->Question->id;
                                $choices[3]['Choice']['text'] = 'En koskaan';
                                $choices[3]['Choice']['points'] = 0.00;
                                $choices[3]['Choice']['weight'] = NULL;

                                foreach ($choices as $key3 => $choice) {
                                    $this->Quiz->Question->Choice->create();
                                    $this->Quiz->Question->Choice->save($choice);
                                }
                            } elseif ($key2 == 13) { // 9th question choices
                                $choices[0]['Choice']['question_id'] = $this->Quiz->Question->id;
                                $choices[0]['Choice']['text'] = 'Jatkuvasti';
                                $choices[0]['Choice']['points'] = 0.00;
                                $choices[0]['Choice']['weight'] = NULL;

                                $choices[1]['Choice']['question_id'] = $this->Quiz->Question->id;
                                $choices[1]['Choice']['text'] = 'Joskus';
                                $choices[1]['Choice']['points'] = 0.00;
                                $choices[1]['Choice']['weight'] = NULL;

                                $choices[2]['Choice']['question_id'] = $this->Quiz->Question->id;
                                $choices[2]['Choice']['text'] = 'Harvoin';
                                $choices[2]['Choice']['points'] = 0.00;
                                $choices[2]['Choice']['weight'] = NULL;

                                $choices[3]['Choice']['question_id'] = $this->Quiz->Question->id;
                                $choices[3]['Choice']['text'] = 'En koskaan';
                                $choices[3]['Choice']['points'] = 0.00;
                                $choices[3]['Choice']['weight'] = NULL;

                                foreach ($choices as $key3 => $choice) {
                                    $this->Quiz->Question->Choice->create();
                                    $this->Quiz->Question->Choice->save($choice);
                                }
                            } elseif ($key2 == 15) { // 9th question choices
                                $choices[0]['Choice']['question_id'] = $this->Quiz->Question->id;
                                $choices[0]['Choice']['text'] = '10';
                                $choices[0]['Choice']['points'] = 0.00;
                                $choices[0]['Choice']['weight'] = NULL;

                                $choices[1]['Choice']['question_id'] = $this->Quiz->Question->id;
                                $choices[1]['Choice']['text'] = '9';
                                $choices[1]['Choice']['points'] = 0.00;
                                $choices[1]['Choice']['weight'] = NULL;

                                $choices[2]['Choice']['question_id'] = $this->Quiz->Question->id;
                                $choices[2]['Choice']['text'] = '8';
                                $choices[2]['Choice']['points'] = 0.00;
                                $choices[2]['Choice']['weight'] = NULL;

                                $choices[3]['Choice']['question_id'] = $this->Quiz->Question->id;
                                $choices[3]['Choice']['text'] = '7';
                                $choices[3]['Choice']['points'] = 0.00;
                                $choices[3]['Choice']['weight'] = NULL;

                                $choices[1]['Choice']['question_id'] = $this->Quiz->Question->id;
                                $choices[1]['Choice']['text'] = '6';
                                $choices[1]['Choice']['points'] = 0.00;
                                $choices[1]['Choice']['weight'] = NULL;

                                $choices[2]['Choice']['question_id'] = $this->Quiz->Question->id;
                                $choices[2]['Choice']['text'] = '5';
                                $choices[2]['Choice']['points'] = 0.00;
                                $choices[2]['Choice']['weight'] = NULL;

                                $choices[3]['Choice']['question_id'] = $this->Quiz->Question->id;
                                $choices[3]['Choice']['text'] = '4';
                                $choices[3]['Choice']['points'] = 0.00;
                                $choices[3]['Choice']['weight'] = NULL;

                                foreach ($choices as $key3 => $choice) {
                                    $this->Quiz->Question->Choice->create();
                                    $this->Quiz->Question->Choice->save($choice);
                                }
                            } elseif ($key2 == 16) { // 0 skip first question choices
                                $choice['Choice']['question_id'] = $this->Quiz->Question->id;
                                $choice['Choice']['text'] = 'Essay';
                                $choice['Choice']['points'] = 0.00;
                                $choice['Choice']['weight'] = NULL;
                                $this->Quiz->Question->Choice->create();
                                $this->Quiz->Question->Choice->save($choice);
                            } else {
                                // do nothing
                            }
                        }
                    }

                    // 3rd quiz students
                    $students = array();
                    $students[0]['Student']['quiz_id'] = $this->Quiz->id;
                    $students[0]['Student']['fname'] = 'Aapo';
                    $students[0]['Student']['lname'] = 'Ahkera';
                    $students[0]['Student']['class'] = '7a';
                    $students[0]['Student']['status'] = 1;
                    $students[0]['Student']['submitted'] = date('Y-m-d H:i:s');

                    $students[1]['Student']['quiz_id'] = $this->Quiz->id;
                    $students[1]['Student']['fname'] = 'Kake';
                    $students[1]['Student']['lname'] = 'Kängsteri';
                    $students[1]['Student']['class'] = '7a';
                    $students[1]['Student']['status'] = 1;
                    $students[1]['Student']['submitted'] = date('Y-m-d H:i:s');

                    $students[2]['Student']['quiz_id'] = $this->Quiz->id;
                    $students[2]['Student']['fname'] = 'Tiina';
                    $students[2]['Student']['lname'] = 'Terävä';
                    $students[2]['Student']['class'] = '7a';
                    $students[2]['Student']['status'] = 1;
                    $students[2]['Student']['submitted'] = date('Y-m-d H:i:s');

                    $students[3]['Student']['quiz_id'] = $this->Quiz->id;
                    $students[3]['Student']['fname'] = 'Liisa';
                    $students[3]['Student']['lname'] = 'Lupsakka';
                    $students[3]['Student']['class'] = '7b';
                    $students[3]['Student']['status'] = 1;
                    $students[3]['Student']['submitted'] = date('Y-m-d H:i:s');

                    $students[4]['Student']['quiz_id'] = $this->Quiz->id;
                    $students[4]['Student']['fname'] = 'Jonne';
                    $students[4]['Student']['lname'] = 'Jopomies';
                    $students[4]['Student']['class'] = '7b';
                    $students[4]['Student']['status'] = 1;
                    $students[4]['Student']['submitted'] = date('Y-m-d H:i:s');

                    $students[5]['Student']['quiz_id'] = $this->Quiz->id;
                    $students[5]['Student']['fname'] = 'Kaija';
                    $students[5]['Student']['lname'] = 'Keskiverto';
                    $students[5]['Student']['class'] = '7b';
                    $students[5]['Student']['status'] = 1;
                    $students[5]['Student']['submitted'] = date('Y-m-d H:i:s');

                    $students[6]['Student']['quiz_id'] = $this->Quiz->id;
                    $students[6]['Student']['fname'] = 'Veeti';
                    $students[6]['Student']['lname'] = 'Verraton';
                    $students[6]['Student']['class'] = '7c';
                    $students[6]['Student']['status'] = 1;
                    $students[6]['Student']['submitted'] = date('Y-m-d H:i:s');

                    $students[7]['Student']['quiz_id'] = $this->Quiz->id;
                    $students[7]['Student']['fname'] = 'Sirpa';
                    $students[7]['Student']['lname'] = 'Sipakka';
                    $students[7]['Student']['class'] = '7c';
                    $students[7]['Student']['status'] = 1;
                    $students[7]['Student']['submitted'] = date('Y-m-d H:i:s');

                    $students[8]['Student']['quiz_id'] = $this->Quiz->id;
                    $students[8]['Student']['fname'] = 'Kiia';
                    $students[8]['Student']['lname'] = 'Ketterä';
                    $students[8]['Student']['class'] = '7c';
                    $students[8]['Student']['status'] = 1;
                    $students[8]['Student']['submitted'] = date('Y-m-d H:i:s');
                    
                    foreach ($students as $key4 => $student) { // Save all student of first quiz
                        $this->Quiz->Student->create();
                        if ($this->Quiz->Student->save($student)) { // Save student data
                            $answers = array();
                            if ($key4 == 0) { // First student answer/ranking save
                                $answers[0]['Answer']['question_id'] = $question_ids[0];
                                $answers[0]['Answer']['student_id'] = $this->Quiz->Student->id;
                                $answers[0]['Answer']['text'] = 'Jatkuvasti';
                                $answers[0]['Answer']['score'] = 0.00;

                                $answers[1]['Answer']['question_id'] = $question_ids[1];
                                $answers[1]['Answer']['student_id'] = $this->Quiz->Student->id;
                                $answers[1]['Answer']['text'] = 'Jatkuvasti';
                                $answers[1]['Answer']['score'] = 0.00;

                                $answers[2]['Answer']['question_id'] = $question_ids[2];
                                $answers[2]['Answer']['student_id'] = $this->Quiz->Student->id;
                                $answers[2]['Answer']['text'] = 'Jatkuvasti';
                                $answers[2]['Answer']['score'] = 0.00;

                                $answers[3]['Answer']['question_id'] = $question_ids[5];
                                $answers[3]['Answer']['student_id'] = $this->Quiz->Student->id;
                                $answers[3]['Answer']['text'] = 'Joskus';
                                $answers[3]['Answer']['score'] = 0.00;

                                $answers[4]['Answer']['question_id'] = $question_ids[6];
                                $answers[4]['Answer']['student_id'] = $this->Quiz->Student->id;
                                $answers[4]['Answer']['text'] = 'Joskus';
                                $answers[4]['Answer']['score'] = 0.00;

                                $answers[5]['Answer']['question_id'] = $question_ids[7];
                                $answers[5]['Answer']['student_id'] = $this->Quiz->Student->id;
                                $answers[5]['Answer']['text'] = 'Joskus';
                                $answers[5]['Answer']['score'] = 0.00;

                                $answers[6]['Answer']['question_id'] = $question_ids[7];
                                $answers[6]['Answer']['student_id'] = $this->Quiz->Student->id;
                                $answers[6]['Answer']['text'] = 'Jatkuvasti';
                                $answers[6]['Answer']['score'] = 0.00;

                                $answers[7]['Answer']['question_id'] = $question_ids[8];
                                $answers[7]['Answer']['student_id'] = $this->Quiz->Student->id;
                                $answers[7]['Answer']['text'] = 'Jatkuvasti';
                                $answers[7]['Answer']['score'] = 0.00;

                                $answers[8]['Answer']['question_id'] = $question_ids[10];
                                $answers[8]['Answer']['student_id'] = $this->Quiz->Student->id;
                                $answers[8]['Answer']['text'] = 'Jatkuvasti';
                                $answers[8]['Answer']['score'] = 0.00;

                                $answers[9]['Answer']['question_id'] = $question_ids[11];
                                $answers[9]['Answer']['student_id'] = $this->Quiz->Student->id;
                                $answers[9]['Answer']['text'] = 'Jatkuvasti';
                                $answers[9]['Answer']['score'] = 0.00;

                                $answers[10]['Answer']['question_id'] = $question_ids[12];
                                $answers[10]['Answer']['student_id'] = $this->Quiz->Student->id;
                                $answers[10]['Answer']['text'] = 'Jatkuvasti';
                                $answers[10]['Answer']['score'] = 0.00;

                                $answers[11]['Answer']['question_id'] = $question_ids[13];
                                $answers[11]['Answer']['student_id'] = $this->Quiz->Student->id;
                                $answers[11]['Answer']['text'] = 'Jatkuvasti';
                                $answers[11]['Answer']['score'] = 0.00;

                                $answers[12]['Answer']['question_id'] = $question_ids[15];
                                $answers[12]['Answer']['student_id'] = $this->Quiz->Student->id;
                                $answers[12]['Answer']['text'] = '9';
                                $answers[12]['Answer']['score'] = 0.00;

                                $answers[13]['Answer']['question_id'] = $question_ids[16];
                                $answers[13]['Answer']['student_id'] = $this->Quiz->Student->id;
                                $answers[13]['Answer']['text'] = 'Haaveilen kympistäkin, mutta en kehdannut pistää sitä tuohon ylös.';
                                $answers[13]['Answer']['score'] = NULL;

                                foreach ($answers as $key5 => $answer) { // save answers
                                    $this->Quiz->Student->Answer->create();
                                    $this->Quiz->Student->Answer->save($answer);
                                } // end of anwers saving

                                // Prepare ranking data
                                $ranking['Ranking']['quiz_id'] = $this->Quiz->id;
                                $ranking['Ranking']['student_id'] = $this->Quiz->Student->id;
                                $ranking['Ranking']['score'] = 0.00;
                                $ranking['Ranking']['total'] = 0.00; 

                                $this->Quiz->Ranking->create();
                                $this->Quiz->Ranking->save($ranking);
                            } // First student answers/ranking save end

                            if ($key4 == 1) { // 2nd student answer/ranking save
                                $answers[0]['Answer']['question_id'] = $question_ids[0];
                                $answers[0]['Answer']['student_id'] = $this->Quiz->Student->id;
                                $answers[0]['Answer']['text'] = 'Harvoin';
                                $answers[0]['Answer']['score'] = 0.00;

                                $answers[1]['Answer']['question_id'] = $question_ids[1];
                                $answers[1]['Answer']['student_id'] = $this->Quiz->Student->id;
                                $answers[1]['Answer']['text'] = 'En koskaan';
                                $answers[1]['Answer']['score'] = 0.00;

                                $answers[2]['Answer']['question_id'] = $question_ids[2];
                                $answers[2]['Answer']['student_id'] = $this->Quiz->Student->id;
                                $answers[2]['Answer']['text'] = 'Harvoin';
                                $answers[2]['Answer']['score'] = 0.00;

                                $answers[3]['Answer']['question_id'] = $question_ids[5];
                                $answers[3]['Answer']['student_id'] = $this->Quiz->Student->id;
                                $answers[3]['Answer']['text'] = 'Harvoin';
                                $answers[3]['Answer']['score'] = 0.00;

                                $answers[4]['Answer']['question_id'] = $question_ids[6];
                                $answers[4]['Answer']['student_id'] = $this->Quiz->Student->id;
                                $answers[4]['Answer']['text'] = 'Joskus';
                                $answers[4]['Answer']['score'] = 0.00;

                                $answers[5]['Answer']['question_id'] = $question_ids[7];
                                $answers[5]['Answer']['student_id'] = $this->Quiz->Student->id;
                                $answers[5]['Answer']['text'] = 'Harvoin';
                                $answers[5]['Answer']['score'] = 0.00;


                                $answers[6]['Answer']['question_id'] = $question_ids[8];
                                $answers[6]['Answer']['student_id'] = $this->Quiz->Student->id;
                                $answers[6]['Answer']['text'] = 'Harvoin';
                                $answers[6]['Answer']['score'] = 0.00;

                                $answers[7]['Answer']['question_id'] = $question_ids[10];
                                $answers[7]['Answer']['student_id'] = $this->Quiz->Student->id;
                                $answers[7]['Answer']['text'] = 'En koskaan';
                                $answers[7]['Answer']['score'] = 0.00;

                                $answers[8]['Answer']['question_id'] = $question_ids[11];
                                $answers[8]['Answer']['student_id'] = $this->Quiz->Student->id;
                                $answers[8]['Answer']['text'] = 'En koskaan';
                                $answers[8]['Answer']['score'] = 0.00;

                                $answers[9]['Answer']['question_id'] = $question_ids[12];
                                $answers[9]['Answer']['student_id'] = $this->Quiz->Student->id;
                                $answers[9]['Answer']['text'] = 'En koskaan';
                                $answers[9]['Answer']['score'] = 0.00;

                                $answers[10]['Answer']['question_id'] = $question_ids[13];
                                $answers[10]['Answer']['student_id'] = $this->Quiz->Student->id;
                                $answers[10]['Answer']['text'] = 'Harvoin';
                                $answers[10]['Answer']['score'] = 0.00;

                                $answers[11]['Answer']['question_id'] = $question_ids[15];
                                $answers[11]['Answer']['student_id'] = $this->Quiz->Student->id;
                                $answers[11]['Answer']['text'] = '7';
                                $answers[11]['Answer']['score'] = 0.00;

                                $answers[12]['Answer']['question_id'] = $question_ids[16];
                                $answers[12]['Answer']['student_id'] = $this->Quiz->Student->id;
                                $answers[12]['Answer']['text'] = 'Ei mitn';
                                $answers[12]['Answer']['score'] = NULL;

                                foreach ($answers as $key5 => $answer) { // save answers
                                    $this->Quiz->Student->Answer->create();
                                    $this->Quiz->Student->Answer->save($answer);
                                } // end of anwers saving

                                // Prepare ranking data
                                $ranking['Ranking']['quiz_id'] = $this->Quiz->id;
                                $ranking['Ranking']['student_id'] = $this->Quiz->Student->id;
                                $ranking['Ranking']['score'] = 0.00;
                                $ranking['Ranking']['total'] = 0.00; 

                                $this->Quiz->Ranking->create();
                                $this->Quiz->Ranking->save($ranking);
                            } // 2nd student answers/ranking save end

                            if ($key4 == 2) { // Third student answer/ranking save
                                $answers[0]['Answer']['question_id'] = $question_ids[0];
                                $answers[0]['Answer']['student_id'] = $this->Quiz->Student->id;
                                $answers[0]['Answer']['text'] = 'Jatkuvasti';
                                $answers[0]['Answer']['score'] = 0.00;

                                $answers[1]['Answer']['question_id'] = $question_ids[1];
                                $answers[1]['Answer']['student_id'] = $this->Quiz->Student->id;
                                $answers[1]['Answer']['text'] = 'Harvoin';
                                $answers[1]['Answer']['score'] = 0.00;

                                $answers[2]['Answer']['question_id'] = $question_ids[2];
                                $answers[2]['Answer']['student_id'] = $this->Quiz->Student->id;
                                $answers[2]['Answer']['text'] = 'Jatkuvasti';
                                $answers[2]['Answer']['score'] = 0.00;

                                $answers[3]['Answer']['question_id'] = $question_ids[5];
                                $answers[3]['Answer']['student_id'] = $this->Quiz->Student->id;
                                $answers[3]['Answer']['text'] = 'Joskus';
                                $answers[3]['Answer']['score'] = 0.00;

                                $answers[4]['Answer']['question_id'] = $question_ids[6];
                                $answers[4]['Answer']['student_id'] = $this->Quiz->Student->id;
                                $answers[4]['Answer']['text'] = 'Jatkuvasti';
                                $answers[4]['Answer']['score'] = 0.00;

                    
                                $answers[5]['Answer']['question_id'] = $question_ids[7];
                                $answers[5]['Answer']['student_id'] = $this->Quiz->Student->id;
                                $answers[5]['Answer']['text'] = 'Jatkuvasti';
                                $answers[5]['Answer']['score'] = 0.00;

                                $answers[6]['Answer']['question_id'] = $question_ids[8];
                                $answers[6]['Answer']['student_id'] = $this->Quiz->Student->id;
                                $answers[6]['Answer']['text'] = 'Harvoin';
                                $answers[6]['Answer']['score'] = 0.00;

                                $answers[7]['Answer']['question_id'] = $question_ids[10];
                                $answers[7]['Answer']['student_id'] = $this->Quiz->Student->id;
                                $answers[7]['Answer']['text'] = 'En koskaan';
                                $answers[7]['Answer']['score'] = 0.00;

                                $answers[8]['Answer']['question_id'] = $question_ids[11];
                                $answers[8]['Answer']['student_id'] = $this->Quiz->Student->id;
                                $answers[8]['Answer']['text'] = 'Joskus';
                                $answers[8]['Answer']['score'] = 0.00;

                                $answers[9]['Answer']['question_id'] = $question_ids[12];
                                $answers[9]['Answer']['student_id'] = $this->Quiz->Student->id;
                                $answers[9]['Answer']['text'] = 'Jatkuvasti';
                                $answers[9]['Answer']['score'] = 0.00;

                                $answers[10]['Answer']['question_id'] = $question_ids[13];
                                $answers[10]['Answer']['student_id'] = $this->Quiz->Student->id;
                                $answers[10]['Answer']['text'] = 'Jatkuvasti';
                                $answers[10]['Answer']['score'] = 0.00;

                                $answers[11]['Answer']['question_id'] = $question_ids[15];
                                $answers[11]['Answer']['student_id'] = $this->Quiz->Student->id;
                                $answers[11]['Answer']['text'] = '8';
                                $answers[11]['Answer']['score'] = 0.00;

                                $answers[12]['Answer']['question_id'] = $question_ids[16];
                                $answers[12]['Answer']['student_id'] = $this->Quiz->Student->id;
                                $answers[12]['Answer']['text'] = 'En kehtaa oikein puhua ääneen vaikka asia kiinnostaakin minua. Voisinkohan tehdä lisätehtäviä kirjallisesti niin saisin näyttää taitojani?';
                                $answers[12]['Answer']['score'] = NULL;

                                foreach ($answers as $key5 => $answer) { // save answers
                                    $this->Quiz->Student->Answer->create();
                                    $this->Quiz->Student->Answer->save($answer);
                                } // end of anwers saving

                                // Prepare ranking data
                                $ranking['Ranking']['quiz_id'] = $this->Quiz->id;
                                $ranking['Ranking']['student_id'] = $this->Quiz->Student->id;
                                $ranking['Ranking']['score'] = 0.00;
                                $ranking['Ranking']['total'] = 0.00; 

                                $this->Quiz->Ranking->create();
                                $this->Quiz->Ranking->save($ranking);
                            } // Third student answers/ranking save end

                            if ($key4 == 3) { // 4th student answer/ranking save
                                $answers[0]['Answer']['question_id'] = $question_ids[0];
                                $answers[0]['Answer']['student_id'] = $this->Quiz->Student->id;
                                $answers[0]['Answer']['text'] = 'Joskus';
                                $answers[0]['Answer']['score'] = 0.00;

                                $answers[1]['Answer']['question_id'] = $question_ids[1];
                                $answers[1]['Answer']['student_id'] = $this->Quiz->Student->id;
                                $answers[1]['Answer']['text'] = 'Harvoin';
                                $answers[1]['Answer']['score'] = 0.00;

                                $answers[2]['Answer']['question_id'] = $question_ids[2];
                                $answers[2]['Answer']['student_id'] = $this->Quiz->Student->id;
                                $answers[2]['Answer']['text'] = 'Harvoin';
                                $answers[2]['Answer']['score'] = 0.00;

                                $answers[3]['Answer']['question_id'] = $question_ids[5];
                                $answers[3]['Answer']['student_id'] = $this->Quiz->Student->id;
                                $answers[3]['Answer']['text'] = 'En koskaan';
                                $answers[3]['Answer']['score'] = 0.00;

                                $answers[4]['Answer']['question_id'] = $question_ids[6];
                                $answers[4]['Answer']['student_id'] = $this->Quiz->Student->id;
                                $answers[4]['Answer']['text'] = 'Jatkuvasti';
                                $answers[4]['Answer']['score'] = 0.00;

                                $answers[5]['Answer']['question_id'] = $question_ids[7];
                                $answers[5]['Answer']['student_id'] = $this->Quiz->Student->id;
                                $answers[5]['Answer']['text'] = 'Joskus';
                                $answers[5]['Answer']['score'] = 0.00;

                                $answers[6]['Answer']['question_id'] = $question_ids[8];
                                $answers[6]['Answer']['student_id'] = $this->Quiz->Student->id;
                                $answers[6]['Answer']['text'] = 'Harvoin';
                                $answers[6]['Answer']['score'] = 0.00;

                                $answers[7]['Answer']['question_id'] = $question_ids[10];
                                $answers[7]['Answer']['student_id'] = $this->Quiz->Student->id;
                                $answers[7]['Answer']['text'] = 'Joskus';
                                $answers[7]['Answer']['score'] = 0.00;

                                $answers[8]['Answer']['question_id'] = $question_ids[11];
                                $answers[8]['Answer']['student_id'] = $this->Quiz->Student->id;
                                $answers[8]['Answer']['text'] = 'Joskus';
                                $answers[8]['Answer']['score'] = 0.00;

                                $answers[9]['Answer']['question_id'] = $question_ids[12];
                                $answers[9]['Answer']['student_id'] = $this->Quiz->Student->id;
                                $answers[9]['Answer']['text'] = 'Harvoin';
                                $answers[9]['Answer']['score'] = 0.00;

                                $answers[10]['Answer']['question_id'] = $question_ids[13];
                                $answers[10]['Answer']['student_id'] = $this->Quiz->Student->id;
                                $answers[10]['Answer']['text'] = 'Jatkuvasti';
                                $answers[10]['Answer']['score'] = 0.00;

                                $answers[11]['Answer']['question_id'] = $question_ids[15];
                                $answers[11]['Answer']['student_id'] = $this->Quiz->Student->id;
                                $answers[11]['Answer']['text'] = '8';
                                $answers[11]['Answer']['score'] = 0.00;

                                $answers[12]['Answer']['question_id'] = $question_ids[16];
                                $answers[12]['Answer']['student_id'] = $this->Quiz->Student->id;
                                $answers[12]['Answer']['text'] = 'Tulee juteltua kavereiden kanssa ehkä vähän enemmän kuin pitäisi...';
                                $answers[12]['Answer']['score'] = NULL;

                                foreach ($answers as $key5 => $answer) { // save answers
                                    $this->Quiz->Student->Answer->create();
                                    $this->Quiz->Student->Answer->save($answer);
                                } // end of anwers saving

                                // Prepare ranking data
                                $ranking['Ranking']['quiz_id'] = $this->Quiz->id;
                                $ranking['Ranking']['student_id'] = $this->Quiz->Student->id;
                                $ranking['Ranking']['score'] = 0.00;
                                $ranking['Ranking']['total'] = 0.00; 

                                $this->Quiz->Ranking->create();
                                $this->Quiz->Ranking->save($ranking);
                            } // 4th student answers/ranking save end

                            if ($key4 == 4) { // 5th student answer/ranking save
                                $answers[0]['Answer']['question_id'] = $question_ids[0];
                                $answers[0]['Answer']['student_id'] = $this->Quiz->Student->id;
                                $answers[0]['Answer']['text'] = 'Joskus';
                                $answers[0]['Answer']['score'] = 0.00;

                                $answers[1]['Answer']['question_id'] = $question_ids[1];
                                $answers[1]['Answer']['student_id'] = $this->Quiz->Student->id;
                                $answers[1]['Answer']['text'] = 'Jatkuvasti';
                                $answers[1]['Answer']['score'] = 0.00;

                                $answers[2]['Answer']['question_id'] = $question_ids[2];
                                $answers[2]['Answer']['student_id'] = $this->Quiz->Student->id;
                                $answers[2]['Answer']['text'] = 'Joskus';
                                $answers[2]['Answer']['score'] = 0.00;

                                $answers[3]['Answer']['question_id'] = $question_ids[5];
                                $answers[3]['Answer']['student_id'] = $this->Quiz->Student->id;
                                $answers[3]['Answer']['text'] = 'Harvoin';
                                $answers[3]['Answer']['score'] = 0.00;

                                $answers[4]['Answer']['question_id'] = $question_ids[6];
                                $answers[4]['Answer']['student_id'] = $this->Quiz->Student->id;
                                $answers[4]['Answer']['text'] = 'Jatkuvasti';
                                $answers[4]['Answer']['score'] = 0.00;

                                $answers[5]['Answer']['question_id'] = $question_ids[7];
                                $answers[5]['Answer']['student_id'] = $this->Quiz->Student->id;
                                $answers[5]['Answer']['text'] = 'Jatkuvasti';
                                $answers[5]['Answer']['score'] = 0.00;

                                $answers[6]['Answer']['question_id'] = $question_ids[8];
                                $answers[6]['Answer']['student_id'] = $this->Quiz->Student->id;
                                $answers[6]['Answer']['text'] = 'Harvoin';
                                $answers[6]['Answer']['score'] = 0.00;

                                $answers[7]['Answer']['question_id'] = $question_ids[10];
                                $answers[7]['Answer']['student_id'] = $this->Quiz->Student->id;
                                $answers[7]['Answer']['text'] = 'Jatkuvasti';
                                $answers[7]['Answer']['score'] = 0.00;

                                $answers[8]['Answer']['question_id'] = $question_ids[11];
                                $answers[8]['Answer']['student_id'] = $this->Quiz->Student->id;
                                $answers[8]['Answer']['text'] = 'Jatkuvasti';
                                $answers[8]['Answer']['score'] = 0.00;

                                $answers[9]['Answer']['question_id'] = $question_ids[12];
                                $answers[9]['Answer']['student_id'] = $this->Quiz->Student->id;
                                $answers[9]['Answer']['text'] = 'Joskus';
                                $answers[9]['Answer']['score'] = 0.00;

                                $answers[10]['Answer']['question_id'] = $question_ids[13];
                                $answers[10]['Answer']['student_id'] = $this->Quiz->Student->id;
                                $answers[10]['Answer']['text'] = 'Joskus';
                                $answers[10]['Answer']['score'] = 0.00;

                                $answers[11]['Answer']['question_id'] = $question_ids[15];
                                $answers[11]['Answer']['student_id'] = $this->Quiz->Student->id;
                                $answers[11]['Answer']['text'] = '9';
                                $answers[11]['Answer']['score'] = 0.00;

                                $answers[12]['Answer']['question_id'] = $question_ids[16];
                                $answers[12]['Answer']['student_id'] = $this->Quiz->Student->id;
                                $answers[12]['Answer']['text'] = 'Osallistun mielellään tunnin keskusteluihin. Välillä ehkä meinaa lähteä lapasesta.';
                                $answers[12]['Answer']['score'] = NULL;

                                foreach ($answers as $key5 => $answer) { // save answers
                                    $this->Quiz->Student->Answer->create();
                                    $this->Quiz->Student->Answer->save($answer);
                                } // end of anwers saving

                                // Prepare ranking data
                                $ranking['Ranking']['quiz_id'] = $this->Quiz->id;
                                $ranking['Ranking']['student_id'] = $this->Quiz->Student->id;
                                $ranking['Ranking']['score'] = 0.00;
                                $ranking['Ranking']['total'] = 0.00; 

                                $this->Quiz->Ranking->create();
                                $this->Quiz->Ranking->save($ranking);
                            } // 5th student answers/ranking save end

                            if ($key4 == 5) { // 6th student answer/ranking save
                                $answers[0]['Answer']['question_id'] = $question_ids[0];
                                $answers[0]['Answer']['student_id'] = $this->Quiz->Student->id;
                                $answers[0]['Answer']['text'] = 'Joskus';
                                $answers[0]['Answer']['score'] = 0.00;

                                $answers[1]['Answer']['question_id'] = $question_ids[1];
                                $answers[1]['Answer']['student_id'] = $this->Quiz->Student->id;
                                $answers[1]['Answer']['text'] = 'Joskus';
                                $answers[1]['Answer']['score'] = 0.00;

                                $answers[2]['Answer']['question_id'] = $question_ids[2];
                                $answers[2]['Answer']['student_id'] = $this->Quiz->Student->id;
                                $answers[2]['Answer']['text'] = 'Joskus';
                                $answers[2]['Answer']['score'] = 0.00;

                                $answers[3]['Answer']['question_id'] = $question_ids[5];
                                $answers[3]['Answer']['student_id'] = $this->Quiz->Student->id;
                                $answers[3]['Answer']['text'] = 'Joskus';
                                $answers[3]['Answer']['score'] = 0.00;

                                $answers[4]['Answer']['question_id'] = $question_ids[6];
                                $answers[4]['Answer']['student_id'] = $this->Quiz->Student->id;
                                $answers[4]['Answer']['text'] = 'Joskus';
                                $answers[4]['Answer']['score'] = 0.00;

                                $answers[5]['Answer']['question_id'] = $question_ids[7];
                                $answers[5]['Answer']['student_id'] = $this->Quiz->Student->id;
                                $answers[5]['Answer']['text'] = 'Joskus';
                                $answers[5]['Answer']['score'] = 0.00;

                                $answers[6]['Answer']['question_id'] = $question_ids[8];
                                $answers[6]['Answer']['student_id'] = $this->Quiz->Student->id;
                                $answers[6]['Answer']['text'] = 'Joskus';
                                $answers[6]['Answer']['score'] = 0.00;

                                $answers[7]['Answer']['question_id'] = $question_ids[10];
                                $answers[7]['Answer']['student_id'] = $this->Quiz->Student->id;
                                $answers[7]['Answer']['text'] = 'Joskus';
                                $answers[7]['Answer']['score'] = 0.00;

                                $answers[8]['Answer']['question_id'] = $question_ids[11];
                                $answers[8]['Answer']['student_id'] = $this->Quiz->Student->id;
                                $answers[8]['Answer']['text'] = 'Joskus';
                                $answers[8]['Answer']['score'] = 0.00;

                                $answers[9]['Answer']['question_id'] = $question_ids[12];
                                $answers[9]['Answer']['student_id'] = $this->Quiz->Student->id;
                                $answers[9]['Answer']['text'] = 'Joskus';
                                $answers[9]['Answer']['score'] = 0.00;

                                $answers[10]['Answer']['question_id'] = $question_ids[13];
                                $answers[10]['Answer']['student_id'] = $this->Quiz->Student->id;
                                $answers[10]['Answer']['text'] = 'Joskus';
                                $answers[10]['Answer']['score'] = 0.00;

                                $answers[11]['Answer']['question_id'] = $question_ids[15];
                                $answers[11]['Answer']['student_id'] = $this->Quiz->Student->id;
                                $answers[11]['Answer']['text'] = '7';
                                $answers[11]['Answer']['score'] = 0.00;

                                $answers[12]['Answer']['question_id'] = $question_ids[16];
                                $answers[12]['Answer']['student_id'] = $this->Quiz->Student->id;
                                $answers[12]['Answer']['text'] = '-';
                                $answers[12]['Answer']['score'] = NULL;

                                foreach ($answers as $key5 => $answer) { // save answers
                                    $this->Quiz->Student->Answer->create();
                                    $this->Quiz->Student->Answer->save($answer);
                                } // end of anwers saving

                                // Prepare ranking data
                                $ranking['Ranking']['quiz_id'] = $this->Quiz->id;
                                $ranking['Ranking']['student_id'] = $this->Quiz->Student->id;
                                $ranking['Ranking']['score'] = 0.00;
                                $ranking['Ranking']['total'] = 0.00; 

                                $this->Quiz->Ranking->create();
                                $this->Quiz->Ranking->save($ranking);
                            } // 6th student answers/ranking save end

                            if ($key4 == 6) { // 7th student answer/ranking save
                                $answers[0]['Answer']['question_id'] = $question_ids[0];
                                $answers[0]['Answer']['student_id'] = $this->Quiz->Student->id;
                                $answers[0]['Answer']['text'] = 'Jatkuvasti';
                                $answers[0]['Answer']['score'] = 0.00;

                                $answers[1]['Answer']['question_id'] = $question_ids[1];
                                $answers[1]['Answer']['student_id'] = $this->Quiz->Student->id;
                                $answers[1]['Answer']['text'] = 'Jatkuvasti';
                                $answers[1]['Answer']['score'] = 0.00;

                                $answers[2]['Answer']['question_id'] = $question_ids[2];
                                $answers[2]['Answer']['student_id'] = $this->Quiz->Student->id;
                                $answers[2]['Answer']['text'] = 'Jatkuvasti';
                                $answers[2]['Answer']['score'] = 0.00;

                                $answers[3]['Answer']['question_id'] = $question_ids[5];
                                $answers[3]['Answer']['student_id'] = $this->Quiz->Student->id;
                                $answers[3]['Answer']['text'] = 'Jatkuvasti';
                                $answers[3]['Answer']['score'] = 0.00;

                                $answers[4]['Answer']['question_id'] = $question_ids[6];
                                $answers[4]['Answer']['student_id'] = $this->Quiz->Student->id;
                                $answers[4]['Answer']['text'] = 'Jatkuvasti';
                                $answers[4]['Answer']['score'] = 0.00;

                                $answers[5]['Answer']['question_id'] = $question_ids[7];
                                $answers[5]['Answer']['student_id'] = $this->Quiz->Student->id;
                                $answers[5]['Answer']['text'] = 'Jatkuvasti';
                                $answers[5]['Answer']['score'] = 0.00;

                                $answers[6]['Answer']['question_id'] = $question_ids[8];
                                $answers[6]['Answer']['student_id'] = $this->Quiz->Student->id;
                                $answers[6]['Answer']['text'] = 'Jatkuvasti';
                                $answers[6]['Answer']['score'] = 0.00;

                                $answers[7]['Answer']['question_id'] = $question_ids[10];
                                $answers[7]['Answer']['student_id'] = $this->Quiz->Student->id;
                                $answers[7]['Answer']['text'] = 'Jatkuvasti';
                                $answers[7]['Answer']['score'] = 0.00;

                                $answers[8]['Answer']['question_id'] = $question_ids[11];
                                $answers[8]['Answer']['student_id'] = $this->Quiz->Student->id;
                                $answers[8]['Answer']['text'] = 'Jatkuvasti';
                                $answers[8]['Answer']['score'] = 0.00;

                                $answers[9]['Answer']['question_id'] = $question_ids[12];
                                $answers[9]['Answer']['student_id'] = $this->Quiz->Student->id;
                                $answers[9]['Answer']['text'] = 'Jatkuvasti';
                                $answers[9]['Answer']['score'] = 0.00;

                                $answers[10]['Answer']['question_id'] = $question_ids[13];
                                $answers[10]['Answer']['student_id'] = $this->Quiz->Student->id;
                                $answers[10]['Answer']['text'] = 'Jatkuvasti';
                                $answers[10]['Answer']['score'] = 0.00;

                                $answers[11]['Answer']['question_id'] = $question_ids[15];
                                $answers[11]['Answer']['student_id'] = $this->Quiz->Student->id;
                                $answers[11]['Answer']['text'] = '10';
                                $answers[11]['Answer']['score'] = 0.00;

                                $answers[12]['Answer']['question_id'] = $question_ids[16];
                                $answers[12]['Answer']['student_id'] = $this->Quiz->Student->id;
                                $answers[12]['Answer']['text'] = 'Koen, että olen ansainnut parhaimman arvosanan näistä opinnoista.';
                                $answers[12]['Answer']['score'] = NULL;

                                foreach ($answers as $key5 => $answer) { // save answers
                                    $this->Quiz->Student->Answer->create();
                                    $this->Quiz->Student->Answer->save($answer);
                                } // end of anwers saving

                                // Prepare ranking data
                                $ranking['Ranking']['quiz_id'] = $this->Quiz->id;
                                $ranking['Ranking']['student_id'] = $this->Quiz->Student->id;
                                $ranking['Ranking']['score'] = 0.00;
                                $ranking['Ranking']['total'] = 0.00; 

                                $this->Quiz->Ranking->create();
                                $this->Quiz->Ranking->save($ranking);
                            } // 7th student answers/ranking save end

                            if ($key4 == 7) { // 8th student answer/ranking save
                                $answers[0]['Answer']['question_id'] = $question_ids[0];
                                $answers[0]['Answer']['student_id'] = $this->Quiz->Student->id;
                                $answers[0]['Answer']['text'] = 'Joskus';
                                $answers[0]['Answer']['score'] = 0.00;

                                $answers[1]['Answer']['question_id'] = $question_ids[1];
                                $answers[1]['Answer']['student_id'] = $this->Quiz->Student->id;
                                $answers[1]['Answer']['text'] = 'Harvoin';
                                $answers[1]['Answer']['score'] = 0.00;

                                $answers[2]['Answer']['question_id'] = $question_ids[2];
                                $answers[2]['Answer']['student_id'] = $this->Quiz->Student->id;
                                $answers[2]['Answer']['text'] = 'Joskus';
                                $answers[2]['Answer']['score'] = 0.00;

                                $answers[3]['Answer']['question_id'] = $question_ids[5];
                                $answers[3]['Answer']['student_id'] = $this->Quiz->Student->id;
                                $answers[3]['Answer']['text'] = 'Jatkuvasti';
                                $answers[3]['Answer']['score'] = 0.00;

                                $answers[4]['Answer']['question_id'] = $question_ids[6];
                                $answers[4]['Answer']['student_id'] = $this->Quiz->Student->id;
                                $answers[4]['Answer']['text'] = 'Jatkuvasti';
                                $answers[4]['Answer']['score'] = 0.00;

                                $answers[5]['Answer']['question_id'] = $question_ids[7];
                                $answers[5]['Answer']['student_id'] = $this->Quiz->Student->id;
                                $answers[5]['Answer']['text'] = 'Joskus';
                                $answers[5]['Answer']['score'] = 0.00;

                                $answers[6]['Answer']['question_id'] = $question_ids[8];
                                $answers[6]['Answer']['student_id'] = $this->Quiz->Student->id;
                                $answers[6]['Answer']['text'] = 'Harvoin';
                                $answers[6]['Answer']['score'] = 0.00;

                                $answers[7]['Answer']['question_id'] = $question_ids[10];
                                $answers[7]['Answer']['student_id'] = $this->Quiz->Student->id;
                                $answers[7]['Answer']['text'] = 'Harvoin';
                                $answers[7]['Answer']['score'] = 0.00;

                                $answers[8]['Answer']['question_id'] = $question_ids[11];
                                $answers[8]['Answer']['student_id'] = $this->Quiz->Student->id;
                                $answers[8]['Answer']['text'] = 'Joskus';
                                $answers[8]['Answer']['score'] = 0.00;

                                $answers[9]['Answer']['question_id'] = $question_ids[12];
                                $answers[9]['Answer']['student_id'] = $this->Quiz->Student->id;
                                $answers[9]['Answer']['text'] = 'Joskus';
                                $answers[9]['Answer']['score'] = 0.00;

                                $answers[10]['Answer']['question_id'] = $question_ids[13];
                                $answers[10]['Answer']['student_id'] = $this->Quiz->Student->id;
                                $answers[10]['Answer']['text'] = 'Joskus';
                                $answers[10]['Answer']['score'] = 0.00;

                                $answers[11]['Answer']['question_id'] = $question_ids[15];
                                $answers[11]['Answer']['student_id'] = $this->Quiz->Student->id;
                                $answers[11]['Answer']['text'] = '8';
                                $answers[11]['Answer']['score'] = 0.00;

                                $answers[12]['Answer']['question_id'] = $question_ids[16];
                                $answers[12]['Answer']['student_id'] = $this->Quiz->Student->id;
                                $answers[12]['Answer']['text'] = 'Ei lisättävää';
                                $answers[12]['Answer']['score'] = NULL;

                                foreach ($answers as $key5 => $answer) { // save answers
                                    $this->Quiz->Student->Answer->create();
                                    $this->Quiz->Student->Answer->save($answer);
                                } // end of anwers saving

                                // Prepare ranking data
                                $ranking['Ranking']['quiz_id'] = $this->Quiz->id;
                                $ranking['Ranking']['student_id'] = $this->Quiz->Student->id;
                                $ranking['Ranking']['score'] = 0.00;
                                $ranking['Ranking']['total'] = 0.00; 

                                $this->Quiz->Ranking->create();
                                $this->Quiz->Ranking->save($ranking);
                            } // 8th student answers/ranking save end

                            if ($key4 == 8) { // 9th student answer/ranking save
                                $answers[0]['Answer']['question_id'] = $question_ids[0];
                                $answers[0]['Answer']['student_id'] = $this->Quiz->Student->id;
                                $answers[0]['Answer']['text'] = 'Jatkuvasti';
                                $answers[0]['Answer']['score'] = 0.00;

                                $answers[1]['Answer']['question_id'] = $question_ids[1];
                                $answers[1]['Answer']['student_id'] = $this->Quiz->Student->id;
                                $answers[1]['Answer']['text'] = 'Jatkuvasti';
                                $answers[1]['Answer']['score'] = 0.00;

                                $answers[2]['Answer']['question_id'] = $question_ids[2];
                                $answers[2]['Answer']['student_id'] = $this->Quiz->Student->id;
                                $answers[2]['Answer']['text'] = 'Jatkuvasti';
                                $answers[2]['Answer']['score'] = 0.00;

                                $answers[3]['Answer']['question_id'] = $question_ids[5];
                                $answers[3]['Answer']['student_id'] = $this->Quiz->Student->id;
                                $answers[3]['Answer']['text'] = 'Harvoin';
                                $answers[3]['Answer']['score'] = 0.00;

                                $answers[4]['Answer']['question_id'] = $question_ids[6];
                                $answers[4]['Answer']['student_id'] = $this->Quiz->Student->id;
                                $answers[4]['Answer']['text'] = 'Jatkuvasti';
                                $answers[4]['Answer']['score'] = 0.00;


                                $answers[5]['Answer']['question_id'] = $question_ids[7];
                                $answers[5]['Answer']['student_id'] = $this->Quiz->Student->id;
                                $answers[5]['Answer']['text'] = 'Harvoin';
                                $answers[5]['Answer']['score'] = 0.00;

                                $answers[6]['Answer']['question_id'] = $question_ids[8];
                                $answers[6]['Answer']['student_id'] = $this->Quiz->Student->id;
                                $answers[6]['Answer']['text'] = 'Joskus';
                                $answers[6]['Answer']['score'] = 0.00;

                                $answers[7]['Answer']['question_id'] = $question_ids[10];
                                $answers[7]['Answer']['student_id'] = $this->Quiz->Student->id;
                                $answers[7]['Answer']['text'] = 'Joskus';
                                $answers[7]['Answer']['score'] = 0.00;

                                $answers[8]['Answer']['question_id'] = $question_ids[11];
                                $answers[8]['Answer']['student_id'] = $this->Quiz->Student->id;
                                $answers[8]['Answer']['text'] = 'Joskus';
                                $answers[8]['Answer']['score'] = 0.00;

                                $answers[9]['Answer']['question_id'] = $question_ids[12];
                                $answers[9]['Answer']['student_id'] = $this->Quiz->Student->id;
                                $answers[9]['Answer']['text'] = 'Joskus';
                                $answers[9]['Answer']['score'] = 0.00;

                                $answers[10]['Answer']['question_id'] = $question_ids[13];
                                $answers[10]['Answer']['student_id'] = $this->Quiz->Student->id;
                                $answers[10]['Answer']['text'] = 'Jatkuvasti';
                                $answers[10]['Answer']['score'] = 0.00;

                                $answers[11]['Answer']['question_id'] = $question_ids[15];
                                $answers[11]['Answer']['student_id'] = $this->Quiz->Student->id;
                                $answers[11]['Answer']['text'] = '9';
                                $answers[11]['Answer']['score'] = 0.00;

                                $answers[12]['Answer']['question_id'] = $question_ids[16];
                                $answers[12]['Answer']['student_id'] = $this->Quiz->Student->id;
                                $answers[12]['Answer']['text'] = 'Urheilutouhut vievät aikaa koulutyöltä.';
                                $answers[12]['Answer']['score'] = NULL;

                                foreach ($answers as $key5 => $answer) { // save answers
                                    $this->Quiz->Student->Answer->create();
                                    $this->Quiz->Student->Answer->save($answer);
                                } // end of anwers saving

                                // Prepare ranking data
                                $ranking['Ranking']['quiz_id'] = $this->Quiz->id;
                                $ranking['Ranking']['student_id'] = $this->Quiz->Student->id;
                                $ranking['Ranking']['score'] = 0.00;
                                $ranking['Ranking']['total'] = 0.00; 

                                $this->Quiz->Ranking->create();
                                $this->Quiz->Ranking->save($ranking);
                            } // 9th student answers/ranking save end



                        }
                    } // 3rd quiz student end

                } // Quiz 3 end

            } // Save Quiz end
        }
    }
    
}
