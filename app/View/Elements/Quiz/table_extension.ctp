<?php $othersQuestionType = array(6, 7, 8); // this categories for others type questions ?>
<div id="answer-table">
    <table class="table table-hover table-responsive table-striped">
        <thead>
            <tr>
                <th class="serial sortable"><?php echo __('&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;#&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'); ?></th>
                <th class="sortable"><?php echo __('Timestamp'); ?></th>
                <th class="sortable"><?php echo __('Name'); ?></th>
                <th class="sortable"><?php echo __('Class'); ?></th>
                <th class="sortable"><?php echo __('Total Points'); ?></th>
                <?php $i = 1; foreach ($quizDetails['Question'] as $question): ?>
                    <?php if (!in_array($question['question_type_id'], $othersQuestionType)) : ?>
                        <th class="question-collapse">
                            <?php echo $i; ?>
                            . &nbsp;
                            <?php echo $question['text']; ?>
                        </th>
                    <?php ++$i; endif; ?>
                <?php endforeach; ?>
            </tr>
        </thead>
        <tbody>
            <?php $sl = 0; if (!empty($quizDetails)) : ?>
                <?php foreach ($quizDetails['Student'] as $key1 => $value1) : $sl++;  ?>
                    <tr id="student-<?php echo $value1['id']; ?>">
                        <td class="serial">
                            <?php echo $sl; ?>
                            <button type="button" class="btn btn-danger btn-sm delete-answer" id="<?php echo $value1['id']; ?>" title="<?php echo __('Remove answer'); ?>">
                                <i class="glyphicon trash"></i>
                            </button>
                        </td>
                        <td><?php echo $value1['submitted'] ?></td>
                        <td>
                            <?php echo $value1['lname']; ?>
                            <?php echo $value1['fname']; ?> 
                        </td>
                        <td><?php echo $value1['class']; ?></td>
                        <?php foreach ($quizDetails['Ranking'] as $key2 => $value2) : ?>
                            <?php if ($value1['id'] == $value2['student_id']) : ?>
                                <td>
                                    <span id="studentscr1-<?php echo $value1['id']; ?>"><?php echo ($value2['score']+0); ?></span>/<?php echo ($value2['total']+0); ?>
                                </td>
                            <?php endif; ?>
                        <?php endforeach; ?>
                        <?php foreach ($quizDetails['Question'] as $key3 => $value3): ?>
                            <?php if (!in_array($value3['question_type_id'], $othersQuestionType)) : ?>
                                <td class="question-collapse">
                                    <?php echo $this->element('Quiz/table', array('value3' => $value3, 'value1' => $value1)); ?>
                                </td>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </tr>
                <?php endforeach; ?>
            <?php else : ?>
                <tr>
                    <td><?php echo __('Quiz not taken yet!'); ?></td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>