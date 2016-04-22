<?php $othersQuestionType = array(6, 7, 8); // this categories for others type questions ?>
<?php $question_count = count($quizDetails['Question']); ?>
<td class="serial">
    <span class="question-serial"><?php echo $sl; ?></span>
    <button type="button" class="btn btn-danger btn-sm delete-answer" id="<?php echo $value1['id']; ?>" title="<?php echo __('Remove answer'); ?>">
        <i class="glyphicon trash"></i>
    </button>
    <span class="ajax-loader"><img src="<?php echo $this->request->webroot; ?>img/ajax-loader.gif" /></span>
    <i class="glyphicon online"></i>
</td>
<td><?php echo $value1['submitted'] ?></td>
<?php if (empty($quizDetails['Quiz']['anonymous'])) : ?>
    <td>
        <?php echo $value1['lname']; ?>
        <?php echo $value1['fname']; ?> 
    </td>
    <td><?php echo $value1['class']; ?></td>
<?php endif; ?>
<?php foreach ($quizDetails['Ranking'] as $key2 => $value2) : ?>
    <?php if ($value1['id'] == $value2['student_id']) : ?>
        <td>
            <span id="studentscr1-<?php echo $value1['id']; ?>"><?php echo ($value2['score']+0); ?></span>/<?php echo ($value2['total']+0); ?>
        </td>
    <?php endif; ?>
<?php endforeach; ?>
<td>
    <?php
        $answer_array = array();
        $answer_count = 0;
        foreach ($value1['Answer'] as $answer) {
            if (!in_array($answer['question_id'], $answer_array)) {
                $answer_array[] = $answer['question_id'];
                $answer_count++;
            }
        }
        $progress = number_format((float)($answer_count/$question_count)*100, 2, '.', '')+0; 
    ?>
    <div class="progress">
        <div class="progress-bar" role="progressbar" aria-valuenow="<?php echo $progress; ?>" aria-valuemin="0" aria-valuemax="100" style="width:<?php echo $progress; ?>%">
            <span<?php if (empty($progress)) : ?> class="empty-progress-text"<?php endif; ?>><?php echo $progress; ?>%</span>
        </div>
    </div>
</td>
<?php foreach ($quizDetails['Question'] as $key3 => $value3): ?>
    <?php if (!in_array($value3['question_type_id'], $othersQuestionType)) : ?>
        <td class="question-collapse">
            <?php echo $this->element('Quiz/table', array('value3' => $value3, 'value1' => $value1)); ?>
        </td>
    <?php endif; ?>
<?php endforeach; ?>

<script type="text/javascript">
    if (getCookie("tabInfo") != 'answer-table-overview') {
        $('.question-collapse').show();
    }
</script>