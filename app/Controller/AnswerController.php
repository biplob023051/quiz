<?php

App::uses('AppController', 'Controller');

class AnswerController extends AppController {

    public $helpers = array('Html');
    public $uses = array('Answer', 'Student', 'Question', 'Quiz');

    public function beforeFilter() {
        parent::beforeFilter();
        $this->Auth->allow();
    }

    public function table($quizId) {

        $filter = array();
        $cacheKeyPrefix = "Quiz.{$quizId}";

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

        $data = $this->Answer->getAnswerTable($quizId, $filter);
        $getMutiAnswer = $this->Answer->getMultipleChoiceAnswer($quizId, $filter);
        $getMutiAnswer = Hash::map($getMutiAnswer,'{n}',function($data){
            $result [] = $data['Answer']['student_id'];
            $result[] = $data['Answer']['text'];
            return $result;
        });
            
        $mapped = array();
        $scores = array();
        $students = array();
        $choices = array();
        $classes = Cache::read("{$cacheKeyPrefix}.classes", 'long');

        if (!$classes) {
            $classes = array('all' => 'All');
            $classes_from_db = $this->Student->find('all', array(
                'fields' => 'Student.class',
                'group' => 'Student.class',
                'recursive' => -1,
                'conditions' => array(
                    'Student.quiz_id = ' => $quizId
                )
            ));
            foreach ($classes_from_db as $student_class) {
                $classes[(string) "{$student_class['Student']['class']}"] = $student_class['Student']['class'];
            }
            Cache::write("{$cacheKeyPrefix}.classes", $classes, 'long');
        }

        foreach ($data as $d) {
            if (!isset($students[$d['Student']['id']])) {
                $students[$d['Student']['id']] = $d['Student'];
            }

            $mapped[$d['Student']['id']][$d['Question']['id']] = array(
                'manual' => (bool) $d['QuestionType']['manual_scoring'],
                'answer' => $d['Answer']['text'],
                'score' => $d['Answer']['score'],
                'unmarked' => ($d['QuestionType']['manual_scoring'] == 1 and $d['Answer']['score'] == null),
                'points' => $d['Choice']['points'],
                'id' => $d['Answer']['id']
            );

            if (!isset($scores[$d['Student']['id']]))
                $scores[$d['Student']['id']] = 0;
            
            if (!isset($choices[$d['Question']['id']]))
                $choices[$d['Question']['id']] = $d['Choice'];
            
            if ((bool) $d['QuestionType']['manual_scoring']) {
                $scores[$d['Student']['id']] += (int) $d['Answer']['score'];
            } else {
                if($d['Answer']['text'] == $d['Choice']['text'])
                    $scores[$d['Student']['id']] += (int) $d['Choice']['points'];
            }
        }

        $questions = $this->Question->findAllByQuizId($quizId, array('recursive' => 0));
        $data['questions'] = $questions;
        $data['answers_table'] = $mapped;
        $data['scores'] = $scores;
        $data['students'] = $students;
        $data['classes'] = $classes;
        $data['choices'] = $choices;
        $data['max_score'] = $this->Quiz->getMaxScore($quizId);
        $data['filter'] = $filter;
        $this->set('data', $data);
        $this->set(compact('getMutiAnswer'));


//        $this->Quiz->Behaviors->load('Containable');
//        $test = $this->Quiz->find('first', array(
//            'conditions' => array(
//                'id = ' => $quizId
//            ),
//            'contain' => array(
//                'Question' => array(
//                    'fields' => array('text', 'id'),
//                    'Choice' => array(
//                        'fields' => array('points'),
//                        'conditions' => array(
//                            'Choice.text = Answer.text'
//                        )
//                    ),
//                    'Answer' => array(
//                        'fields' => array('id', 'text', 'score'),
//                        'Student'
//                    ),
//                    'QuestionType' => array(
//                        'fields' => array('manual_scoring', 'name')
//                    )
//                )
//            ),
//        ));
//
//        $this->set('test', $test);
    }

    public function update() {
        $post = $this->request->data;
        $data = $this->Answer->findById($post['id']);

        if (empty($data))
            throw new NotFoundException;

        $this->Answer->score = $post['score'];
        if ($this->Answer->save()) {
            echo json_encode(array(
                'success' => true,
                'score' => $score
            ));
        }
    }

    public function submit($quizId) {
        if (!$this->request->is('post'))
            throw new NotFoundException;

        $post = $this->request->data;

        $student = array(
            'Student' => array(
                'fname' => $post['fname'],
                'lname' => $post['lname'],
                'class' => $post['class'],
                'quiz_id' => $quizId
            )
        );

        if ($this->Student->save($student)) {
            $student_id = $this->Student->getLastInsertId();
            $answers = array();
            foreach ($post['answers'] as $id => $answer) {
                $answers[] = array(
                    'Answer' => array(
                        'question_id' => $id,
                        'student_id' => $student_id,
                        'answer' => is_array($answer) ? json_encode($answer) : $answer
                    )
                );
            }

            $this->Answer->saveMany($answers);
        }
    }

}
