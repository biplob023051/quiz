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

	// Function to check if there has points or not in choice array
	// if point, return true otherwise return false
	public function checkPoint($choices = array()) {
		$points = false;
		foreach ($choices as $key => $choice) {
			if ($choice['points'] != '0.00') {
				$points = true;
				break;
			}
		}
		return $points;
	}

}
