<?php

class QuizHelper extends AppHelper {

	public $helpers = array('Time', 'Text');

	public function getHelpPicture($obj, $type, $thumb = false) {
		$prefix = '';
		if ($thumb)
			$prefix = 't_';

		if (!empty($obj['Help']['photo'])) {
			return $this->request->webroot . 'uploads/' . $type . '/' . $prefix . $obj['Help']['photo'];
		} else {
			return $this->request->webroot . 'img/' . $prefix . 'no-image-' . $type . '.png';
		}
	}

	public function getImageUtubeChoice($question_id) {
		App::import('Model', 'Choice');
        $choice = new Choice();
        $result = $choice->findByQuestionId($question_id, array('Choice.text'));
        return empty($result) ? '' : $result['Choice']['text'];
	}

}
