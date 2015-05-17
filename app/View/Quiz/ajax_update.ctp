<div id="overview" <?php if ($currentTab != 'answer-table-overview'): ?> style="display: none;" <?php endif; ?>>
    <table class="table table-hover table-responsive table-striped">
        <thead>
            <tr>
                <th><?php echo __('Timestamp'); ?></th>
                <th><?php echo __('Name'); ?></th>
                <th><?php echo __('Class'); ?></th>
                <th><?php echo __('Total Points'); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($quizDetails)) : ?>
                <?php foreach ($quizDetails['Student'] as $key1 => $value1) : ?>
                    <tr id="student-<?php echo $value1['id']; ?>">
                        <td><?php echo $value1['submitted'] ?></td>
                        <td>
                            <?php echo $value1['fname']; ?> 
                            <?php echo $value1['lname']; ?>
                        </td>
                        <td><?php echo $value1['class']; ?></td>
                        <?php foreach ($quizDetails['Ranking'] as $key2 => $value2) : ?>
                            <?php if ($value1['id'] == $value2['student_id']) : ?>
                                <td>
                                    <span id="studentscr1-<?php echo $value1['id']; ?>"><?php echo $value2['score']; ?></span>/<?php echo $value2['total']; ?>
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
<div id="details" <?php if($currentTab != 'answer-table-show'): ?> style="display: none;" <?php endif; ?>>
    <table class="table table-hover table-responsive table-striped">
        <thead>
            <tr>
                <th class="serial"><?php echo __('Sl'); ?></th>
                <th><?php echo __('Timestamp'); ?></th>
                <th><?php echo __('Name'); ?></th>
                <th><?php echo __('Class'); ?></th>
                <th><?php echo __('Total Points'); ?></th>
                <?php $i = 1; foreach ($quizDetails['Question'] as $question): ?>
                    <th>
                        <?php echo $i; ?>
                        . &nbsp;
                        <?php echo $question['text']; ?>
                    </th>
                <?php ++$i; endforeach; ?>
            </tr>
        </thead>
        <tbody>
            <?php $sl = 0; if (!empty($quizDetails)) : ?>
                <?php foreach ($quizDetails['Student'] as $key1 => $value1) : $sl++; ?>
                    <tr id="student-<?php echo $value1['id']; ?>">
                        <td class="serial">
                            <button type="button" class="btn btn-danger btn-sm delete-answer" std-id="<?php echo $value1['id']; ?>">
                                <i class="glyphicon trash"></i>
                            </button>
                            <?php echo $sl; ?>
                        </td>
                        <td><?php echo $value1['submitted'] ?></td>
                        <td>
                            <?php echo $value1['fname']; ?> 
                            <?php echo $value1['lname']; ?>
                        </td>
                        <td><?php echo $value1['class']; ?></td>
                        <?php foreach ($quizDetails['Ranking'] as $key2 => $value2) : ?>
                            <?php if ($value1['id'] == $value2['student_id']) : ?>
                                <td>
                                    <span id="studentscr2-<?php echo $value1['id']; ?>"><?php echo $value2['score']; ?></span>/<?php echo $value2['total']; ?>
                                </td>
                            <?php endif; ?>
                        <?php endforeach; ?>
                        <!-- question answer start -->
                        
                        <?php foreach ($quizDetails['Question'] as $key3 => $value3): ?>
                            <td>
                                <?php echo $this->element('Quiz/table', array('value3' => $value3, 'value1' => $value1)); ?>
                            </td>
                        <?php endforeach; ?>    

                        <!-- question answer end -->
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