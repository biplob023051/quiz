<?php

class MaintenanceController extends AppController {

    public function beforeFilter() {
        parent::beforeFilter();
        $this->Auth->allow('notice');
    }

    public function notice() {
    	// Remove maintenance mode
    	$this->redirect(array('controller' => 'quiz', 'action' => 'index'));
        $this->set('title_for_layout', __('Pardon for the dust!'));
        $this->render('/Elements/Maintenance/notice');
    }

    public function load_dummy_data($user_id) {
        if (empty($user_id)) {
            $this->Session->setFlash(__('No direct access on this location'), 'error_form', array(), 'error');
            $this->redirect($this->referer());
        } else {
            if ($user_id != $this->Auth->user('id')) {
                $this->Session->setFlash(__('Permission denaid'), 'error_form', array(), 'error');
                $this->redirect($this->referer());
            }
        } 

        $this->autoRender = false;
        $this->loadModel('Quiz');
        $quizes[0]['Quiz']['name'] = 'MALLITESTI: Fruits and vegetables';
        $quizes[0]['Quiz']['description'] = 'TÃ¤ssÃ¤ kokeessa testataan hedelmien ja vihannesten sanastoa englanniksi. (MALLITESTI: voit poistaa tÃ¤mÃ¤n testi kun et tarvitse sitÃ¤ enÃ¤Ã¤)';
        $quizes[0]['Quiz']['student_count'] = 9;

        $quizes[1]['Quiz']['name'] = 'MALLITESTI: Musiikin kotitehtÃ¤vÃ¤';
        $quizes[1]['Quiz']['description'] = 'Ennen seuraavaa tuntia, katso oheiset videot ja tee niihin liittyvÃ¤t tehtÃ¤vÃ¤t. (MALLITESTI: voit poistaa tÃ¤mÃ¤n testi kun et tarvitse sitÃ¤ enÃ¤Ã¤)';
        $quizes[1]['Quiz']['student_count'] = 9;
        $quizes[1]['Quiz']['show_result'] = 1;

        $quizes[2]['Quiz']['name'] = 'MALLITESTI: Itsearviolomake';
        $quizes[2]['Quiz']['description'] = 'TÃ¤ytÃ¤ oheinen itsearviolomake huolellisesti. (MALLITESTI: voit poistaa tÃ¤mÃ¤n testi kun et tarvitse sitÃ¤ enÃ¤Ã¤)';
        $quizes[2]['Quiz']['student_count'] = 9;

        foreach ($quizes as $key1 => $quiz) {
            $quiz['Quiz']['user_id'] = $this->Auth->user('id');
            $this->Quiz->create();
            if ($this->Quiz->save($quiz)) {
                $random_id = $this->Quiz->id . $this->Quiz->randText(2);
                $this->Quiz->saveField('random_id', $random_id);
                
                $questions = array(); // Prevent duplicate questions

                if ($key1 == 2) { // Quiz 3
                    $questions[0]['Question']['quiz_id'] = $this->Quiz->id;
                    $questions[0]['Question']['question_type_id'] = 1;
                    $questions[0]['Question']['text'] = 'Keskityn opetukseen ja tehtÃ¤viin';
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
                    $questions[4]['Question']['text'] = 'KÃ¤ytÃ¶s ja huolellisuus';
                    $questions[4]['Question']['explanation'] = '';
                    $questions[4]['Question']['weight'] = 8;
                    $questions[4]['Question']['max_allowed'] = NULL;

                    $questions[5]['Question']['quiz_id'] = $this->Quiz->id;
                    $questions[5]['Question']['question_type_id'] = 1;
                    $questions[5]['Question']['text'] = 'Kannan osaltani vastuuta luokkatilan siisteydestÃ¤';
                    $questions[5]['Question']['explanation'] = '';
                    $questions[5]['Question']['weight'] = 7;
                    $questions[5]['Question']['max_allowed'] = NULL;

                    $questions[6]['Question']['quiz_id'] = $this->Quiz->id;
                    $questions[6]['Question']['question_type_id'] = 1;
                    $questions[6]['Question']['text'] = 'KÃ¤sittelen opetusvÃ¤lineitÃ¤ asiallisesti';
                    $questions[6]['Question']['explanation'] = '';
                    $questions[6]['Question']['weight'] = 6;
                    $questions[6]['Question']['max_allowed'] = NULL;

                    $questions[7]['Question']['quiz_id'] = $this->Quiz->id;
                    $questions[7]['Question']['question_type_id'] = 1;
                    $questions[7]['Question']['text'] = 'Saavun ajoissa tunnille';
                    $questions[7]['Question']['explanation'] = '';
                    $questions[7]['Question']['weight'] = 5;
                    $questions[7]['Question']['max_allowed'] = NULL;

                    $questions[9]['Question']['quiz_id'] = $this->Quiz->id;
                    $questions[9]['Question']['question_type_id'] = 1;
                    $questions[9]['Question']['text'] = 'PyydÃ¤n puheenvuoroa viittaamalla';
                    $questions[9]['Question']['explanation'] = '';
                    $questions[9]['Question']['weight'] = 4;
                    $questions[9]['Question']['max_allowed'] = NULL;

                    $questions[10]['Question']['quiz_id'] = $this->Quiz->id;
                    $questions[10]['Question']['question_type_id'] = 6;
                    $questions[10]['Question']['text'] = 'RyhmÃ¤ssÃ¤ toimiminen';
                    $questions[10]['Question']['explanation'] = '';
                    $questions[10]['Question']['weight'] = 3;
                    $questions[10]['Question']['max_allowed'] = NULL;

                    $questions[11]['Question']['quiz_id'] = $this->Quiz->id;
                    $questions[11]['Question']['question_type_id'] = 1;
                    $questions[11]['Question']['text'] = 'Autan ja neuvon luokkatovereita';
                    $questions[11]['Question']['explanation'] = '';
                    $questions[11]['Question']['weight'] = 2;
                    $questions[11]['Question']['max_allowed'] = NULL;

                    $questions[12]['Question']['quiz_id'] = $this->Quiz->id;
                    $questions[12]['Question']['question_type_id'] = 1;
                    $questions[12]['Question']['text'] = 'Toimin aktiivisesti osana ryhmÃ¤Ã¤ (esim. ryhmÃ¤tÃ¶issÃ¤)';
                    $questions[12]['Question']['explanation'] = '';
                    $questions[12]['Question']['weight'] = 1;
                    $questions[12]['Question']['max_allowed'] = NULL;

                    $questions[13]['Question']['quiz_id'] = $this->Quiz->id;
                    $questions[13]['Question']['question_type_id'] = 1;
                    $questions[13]['Question']['text'] = 'Pyrin kehittymÃ¤Ã¤n pitkÃ¤jÃ¤nteisesti';
                    $questions[13]['Question']['explanation'] = '';
                    $questions[13]['Question']['weight'] = 9;
                    $questions[13]['Question']['max_allowed'] = NULL;

                    $questions[14]['Question']['quiz_id'] = $this->Quiz->id;
                    $questions[14]['Question']['question_type_id'] = 1;
                    $questions[14]['Question']['text'] = 'Annan toisillekin tyÃ¶rauhan';
                    $questions[14]['Question']['explanation'] = '';
                    $questions[14]['Question']['weight'] = NULL;
                    $questions[14]['Question']['max_allowed'] = NULL;

                    $questions[15]['Question']['quiz_id'] = $this->Quiz->id;
                    $questions[15]['Question']['question_type_id'] = 6;
                    $questions[15]['Question']['text'] = 'Arvosana';
                    $questions[15]['Question']['explanation'] = '';
                    $questions[15]['Question']['weight'] = NULL;
                    $questions[15]['Question']['max_allowed'] = NULL;

                    $questions[16]['Question']['quiz_id'] = $this->Quiz->id;
                    $questions[16]['Question']['question_type_id'] = 1;
                    $questions[16]['Question']['text'] = 'MielestÃ¤ni oikea arvosana todistukseeni on';
                    $questions[16]['Question']['explanation'] = '';
                    $questions[16]['Question']['weight'] = NULL;
                    $questions[16]['Question']['max_allowed'] = NULL;

                    $questions[17]['Question']['quiz_id'] = $this->Quiz->id;
                    $questions[17]['Question']['question_type_id'] = 5;
                    $questions[17]['Question']['text'] = 'Vapaa sana';
                    $questions[17]['Question']['explanation'] = 'Alla olevaan tekstikenttÃ¤Ã¤n voit kertoa tarkemmin ajatuksiasi.';
                    $questions[17]['Question']['weight'] = NULL;
                    $questions[17]['Question']['max_allowed'] = NULL;

                    foreach ($questions as $key2 => $question) {
                        $this->Quiz->Question->create();
                        if ($this->Quiz->Question->save($question)) { // save related question
                            $choices = array();
                            // save related choice
                            if ($key2 == 0) { // first question choices
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
                            } elseif ($key2 == 1) { // 2nd question choices
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

                }

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
                    $questions[1]['Question']['explanation'] = 'Katso video ja vastaa sen jÃ¤lkeen alla oleviin kysymyksiin. Kaikkien kysymysten vastaukset lÃ¶ytyvÃ¤t videosta.';
                    $questions[1]['Question']['weight'] = NULL;
                    $questions[1]['Question']['max_allowed'] = NULL;

                    $questions[2]['Question']['quiz_id'] = $this->Quiz->id;
                    $questions[2]['Question']['question_type_id'] = 3;
                    $questions[2]['Question']['text'] = 'MitkÃ¤ sormet puristavat rumpukapulaa?';
                    $questions[2]['Question']['explanation'] = 'Jokaisesta oikeasta valinnasta +1p.';
                    $questions[2]['Question']['weight'] = NULL;
                    $questions[2]['Question']['max_allowed'] = 2;

                    $questions[3]['Question']['quiz_id'] = $this->Quiz->id;
                    $questions[3]['Question']['question_type_id'] = 3;
                    $questions[3]['Question']['text'] = 'MitkÃ¤ kolme seuraavista ovat tyypillisiÃ¤ virheitÃ¤ rumpukapulaotteessa?';
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
                    $questions[5]['Question']['text'] = 'Katso video ja vastaa sen jÃ¤lkeen alla oleviin kysymyksiin. Kaikkien kysymysten vastaukset lÃ¶ytyvÃ¤t videosta.';
                    $questions[5]['Question']['explanation'] = 'Katso video ja vastaa sen jÃ¤lkeen alla oleviin kysymyksiin. Kaikkien kysymysten vastaukset lÃ¶ytyvÃ¤t videosta.';
                    $questions[5]['Question']['weight'] = NULL;
                    $questions[5]['Question']['max_allowed'] = NULL;

                    $questions[6]['Question']['quiz_id'] = $this->Quiz->id;
                    $questions[6]['Question']['question_type_id'] = 1;
                    $questions[6]['Question']['text'] = 'MitkÃ¤ ovat kaksi bassorummun tavallista soittotekniikkaa?';
                    $questions[6]['Question']['explanation'] = 'Valitse mielestÃ¤si sopivin vaihtoehto. Oikeasta vastauksesta +2p, vÃ¤Ã¤rÃ¤stÃ¤ -2p.';
                    $questions[6]['Question']['weight'] = NULL;
                    $questions[6]['Question']['max_allowed'] = NULL;

                    $questions[7]['Question']['quiz_id'] = $this->Quiz->id;
                    $questions[7]['Question']['question_type_id'] = 3;
                    $questions[7]['Question']['text'] = 'MitkÃ¤ kolme rumpusetin osaa tarvitaan lÃ¤hes kaikkien rumpukomppien soittamiseen?';
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
                                $choices[3]['Choice']['text'] = 'NimetÃ¶n';
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
                                $choices[0]['Choice']['text'] = 'Ote liian keskeltÃ¤ kapulaa';
                                $choices[0]['Choice']['points'] = 1.00;
                                $choices[0]['Choice']['weight'] = NULL;

                                $choices[1]['Choice']['question_id'] = $this->Quiz->Question->id;
                                $choices[1]['Choice']['text'] = 'Ote vÃ¤Ã¤rÃ¤llÃ¤ kÃ¤dellÃ¤';
                                $choices[1]['Choice']['points'] = 0.00;
                                $choices[1]['Choice']['weight'] = NULL;

                                $choices[2]['Choice']['question_id'] = $this->Quiz->Question->id;
                                $choices[2]['Choice']['text'] = 'KÃ¤si puristaa liian kovaa kapulasta';
                                $choices[2]['Choice']['points'] = 1.00;
                                $choices[2]['Choice']['weight'] = NULL;

                                $choices[3]['Choice']['question_id'] = $this->Quiz->Question->id;
                                $choices[3]['Choice']['text'] = 'Etusormi on kapulan ylÃ¤puolella';
                                $choices[3]['Choice']['points'] = 1.00;
                                $choices[3]['Choice']['weight'] = NULL;

                                $choices[4]['Choice']['question_id'] = $this->Quiz->Question->id;
                                $choices[4]['Choice']['text'] = 'VÃ¤Ã¤rÃ¤n muotoiset kapulat';
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
                                $choices[1]['Choice']['text'] = 'KantapÃ¤Ã¤ on ilmassa tai kantapÃ¤Ã¤ on kiinni pedaalissa.';
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
                }

                if ($key1 == 0) { // Quiz 1
                    $questions[0]['Question']['quiz_id'] = $this->Quiz->id;
                    $questions[0]['Question']['question_type_id'] = 2;
                    $questions[0]['Question']['text'] = 'KÃ¤Ã¤nnÃ¤ englanniksi "omena".';
                    $questions[0]['Question']['explanation'] = 'Oikea vastaus +2p';
                    $questions[0]['Question']['weight'] = 3;

                    $questions[1]['Question']['quiz_id'] = $this->Quiz->id;
                    $questions[1]['Question']['question_type_id'] = 2;
                    $questions[1]['Question']['text'] = 'KÃ¤Ã¤nnÃ¤ englanniksi "mansikka".';
                    $questions[1]['Question']['explanation'] = 'Oikea vastaus +2p';
                    $questions[1]['Question']['weight'] = 2;

                    $questions[2]['Question']['quiz_id'] = $this->Quiz->id;
                    $questions[2]['Question']['question_type_id'] = 2;
                    $questions[2]['Question']['text'] = 'KÃ¤Ã¤nnÃ¤ englanniksi "porkkana.';
                    $questions[2]['Question']['explanation'] = 'Oikea vastaus +2p';
                    $questions[2]['Question']['weight'] = 1;

                    $questions[3]['Question']['quiz_id'] = $this->Quiz->id;
                    $questions[3]['Question']['question_type_id'] = 6;
                    $questions[3]['Question']['text'] = 'KÃ¤Ã¤nnÃ¶ssanat';
                    $questions[3]['Question']['weight'] = 4;

                    $questions[4]['Question']['quiz_id'] = $this->Quiz->id;
                    $questions[4]['Question']['question_type_id'] = 6;
                    $questions[4]['Question']['text'] = 'Monivalinnat';

                    $questions[5]['Question']['quiz_id'] = $this->Quiz->id;
                    $questions[5]['Question']['question_type_id'] = 3;
                    $questions[5]['Question']['text'] = 'MitkÃ¤ seuraavista ovat hedelmiÃ¤?';
                    $questions[5]['Question']['explanation'] = 'Oikeasta vastauksesta +1p';
                    $questions[5]['Question']['max_allowed'] = 3;

                    $questions[6]['Question']['quiz_id'] = $this->Quiz->id;
                    $questions[6]['Question']['question_type_id'] = 1;
                    $questions[6]['Question']['text'] = 'MikÃ¤ seuraavista on "ananas" englanniksi?';
                    $questions[6]['Question']['explanation'] = 'Oikeasta vastauksesta +1p';

                    $questions[7]['Question']['quiz_id'] = $this->Quiz->id;
                    $questions[7]['Question']['question_type_id'] = 6;
                    $questions[7]['Question']['text'] = 'Avoimet tehtÃ¤vÃ¤t';

                    $questions[8]['Question']['quiz_id'] = $this->Quiz->id;
                    $questions[8]['Question']['question_type_id'] = 5;
                    $questions[8]['Question']['text'] = 'Muodosta englanniksi lause, jossa kÃ¤ytÃ¤t KAHTA hedelmÃ¤Ã¤';
                    $questions[8]['Question']['explanation'] = 'HedelmistÃ¤ 0-2p, muusta lauseesta 0-4p.';

                    $questions[9]['Question']['quiz_id'] = $this->Quiz->id;
                    $questions[9]['Question']['question_type_id'] = 4;
                    $questions[9]['Question']['text'] = 'NimeÃ¤ englanniksi suosikkihedelmÃ¤si.';
                    
                    foreach ($questions as $key2 => $question) {
                        $this->Quiz->Question->create();
                        if ($this->Quiz->Question->save($question)) { // save related question
                            $choices = array();
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
                    
                } // Quiz 1 end

            }
        }
        $this->redirect($this->referer());
        //$quizes[]['Quiz']['random_id'] = 9;
    }
    
}
