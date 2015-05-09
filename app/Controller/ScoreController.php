<?php

App::uses('AJAXController', 'Controller');

class ScoreController extends AJAXController {

    //public $components = array('Auth');
    public $uses = array('Answer', 'Choice');

    public function update() {
        $data = $this->request->data;

        $response = array('success' => false);

        if (!empty($data['score']) || (int) $data['score'] == 0) {
            
            $choice = $this->Choice->findByQuestionId($data['id']);
            if(empty($choice))
                return;
            
            if ($data['score'] > $choice['Choice']['points'])
                $data['score'] = $choice['Choice']['points'];
            
            $response['data'] = $this->Answer->updateScore($data['id'], $data['student_id'], $data['score']);
            if ($this->Answer->updateScore($data['id'], $data['student_id'], $data['score']))
            {
                // update ranking score as well
                $this->loadModel('Ranking');
                $ranking = $this->Ranking->findByStudentId($data['student_id']);
                $score = $ranking['Ranking']['score'] + $data['score'] - $data['current_score'];
                $this->Ranking->id = $ranking['Ranking']['id'];
                $this->Ranking->saveField('score', $score);
                $response['success'] = true;
                $response['score'] = $data['score'];
                $response['quiz_id'] = $ranking['Ranking']['quiz_id'];
            }
        }
        $this->set('data', $response);
    }

}
