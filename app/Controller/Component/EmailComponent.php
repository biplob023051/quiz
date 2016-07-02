<?php
App::uses('Component', 'Controller');
App::uses('CakeEmail', 'Network/Email');
class EmailComponent extends Component {
    /**
	 * 
	 * Send a data to a user 
	 * @param string $email
	 * @param string $name
	 * @param string $data
	 */
	public function sendMail($to, $subject, $data, $template, $from = null) {
		if (empty($from)) {
			$from = array('pietu.halonen@verkkotesti.fi' => 'WebQuiz.fi');
		}		
		$Email = new CakeEmail();
		$Email->viewVars(array('data' => $data));
		$checkEmail = $Email->template($template)
			->emailFormat('html')
			->to($to)
			->subject($subject)
			->from($from)
			->send();  
		return $checkEmail;
	}
}

?>