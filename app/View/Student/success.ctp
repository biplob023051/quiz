<?php 
	$otherQuestionType = array(6,7,8);
	$this->assign('title', __('Success')); 
?>
<div class="jumbotron">
  <h1><?php echo __('Thank You!'); ?></h1>
  <p><?php echo __('Your answer(s) has been submitted successfully.'); ?></p>
</div>

<?php if (!empty($student_result)) : ?>
	<div class="row">
		<?php 
			$i = 1;
			$pending = 0;
			$result_html = '';
			foreach ($quiz['Question'] as $key => $question) { 
				$result_html = $result_html . '<div class="col-md-12">';
					if ($question['question_type_id'] == 6) { // for header type
						$result_html = $result_html . '<h3 class="header">' . $question['text'] . '</h3>';
					} elseif ($question['question_type_id'] == 7) { // for youtube type
						$result_html = $result_html . '<h3 class="youtube">' . $question['text'] . '</h3>';
						$result_html = $result_html . '<div class="row">
						    <div class="col-xs-12 col-md-6">
						        <iframe width="100%" height="315" src="' . $this->Quiz->getImageUtubeChoice($question['id']) . '" frameborder="0" allowfullscreen></iframe>
						    </div>
						</div>';
					} elseif ($question['question_type_id'] == 8) { // for image type
						$result_html = $result_html . '<h3 class="image-url">' . $question['text'] . '</h3>';
						$result_html = $result_html . '<div class="row">
						    <div class="col-xs-12 col-md-6">
						        <img class="img-responsive" src="' . $this->Quiz->getImageUtubeChoice($question['id']) . '" alt=""/>
						    </div>
						</div>';
					} else { // for actual questions
						$result_html = $result_html . '<h3>' . $i . ') ' . $question['text'] . '</h3>';
					}
					foreach ($student_result['Answer'] as $key => $answer) {
						if ($question['id'] == $answer['question_id']) {
							if (empty($answer['text'])) {
				                $result_html = $result_html . '<p class="text-danger">' . __('Not Answered') .'</p>';
				            } else { 
				                if ($answer['score'] > 0) {
				                    $result_html = $result_html . '<p class="text-success">' . $answer['text'] . ' <span class="score">' . ($answer['score']+0) . '</span><br/>';
				                } elseif ($answer['score'] == '') {
				                	$pending++;
				                    $result_html = $result_html . '<p>' . $answer['text'] . ' <span class="score">' . __('On hold') . '</span><br/>';
				                } elseif ($answer['score'] == 0) {
				                    $result_html = $result_html . '<p class="text-warning">' . $answer['text'] . ' <span class="score">' . ($answer['score']+0) . '</span><br/>';
				                } else {
				                    $result_html = $result_html . '<p class="text-danger">' . $answer['text'] . ' <span class="score">' . ($answer['score']+0) . '</span><br/>';
				                }    
				            } 
				        } 
				    }
				$result_html = $result_html . '</div>';
			 	if (!in_array($question['question_type_id'], $otherQuestionType)) {
					$i++; // increment only for actual question type questions		
				} 
			} 
		?>
		<div class="col-md-12">
			<h3><?php echo __('YOUR RESULTS'); ?></h3>
			<h2><?php echo __('Total') . ': ' . ($student_result['Ranking']['score']+0) . '/' . ($student_result['Ranking']['total']+0); ?><?php echo !empty($pending) ? ' (' . $pending . ' ' . __('YOUR ANSWER waiting for rating') . ')' : ''; ?></h2>
		</div>
		<?php echo $result_html; ?>
	</div>
<?php endif; ?>

<style type="text/css">
span.score {
    border-radius: 50%;
    behavior: url(PIE.htc);
    width: 16px;
    height: 16px;
    padding: 1px 4px;
    background: #fff;
    border: 2px solid #666;
    color: #666;
    text-align: center;
    font: 14px Arial, sans-serif;
    font-weight: bold;
}
</style>
