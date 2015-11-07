

<div class="container">
    <div class="page-header">
        <h3><?php echo __('Quiz name: ') . $quizDetails['Quiz']['name']; ?></h3>
    </div>
    <?php foreach ($quizDetails['Student'] as $key1 => $value1) : ?>
        <div class="row" id="result">
            <div class="col-md-3 col-xs-4">
                <?php echo '<b>' . __('Student name: ') . '</b>' . $value1['lname'] . ' ' . $value1['fname']; ?>
            </div>
            <div class="col-md-3 col-xs-4">
                <?php echo '<b>' . __('Quiz taken: ') . '</b>' . $value1['submitted']; ?>
            </div>
            <div class="col-md-3 col-xs-2">
                <?php echo '<b>' . __('Class: ') . '</b>' . $value1['class']; ?>
            </div>
            <div class="col-md-3 col-xs-2">
                <?php 
                    foreach ($quizDetails['Ranking'] as $key2 => $value2) { 
                        if ($value1['id'] == $value2['student_id']) {  
                        echo '<b>' . __('Total: ') . '</b>' . $value2['score'] . '/' . $value2['total'];  break; 
                        }  
                    } 
                ?>
            </div>
            <?php $i = 0; foreach ($quizDetails['Question'] as $key3 => $value3): $i++; ?>
                <?php 
                    $answer = '';
                    foreach ($value3['Answer'] as $key4 => $value4) { 
                        if ($value1['id'] == $value4['student_id']) {
                            if (empty($value4['text'])) { 
                                $answer = 'Not Answered'; 
                            } else { 
                                $answer = $answer . ' ' . $value4['text']; 
                            }  
                        } 
                    }
                ?>
                <?php if (empty($answer)) : ?>
                    <div class="col-md-12 col-xs-12">
                        <?php echo $i . ') ' . $value3['text']; ?>
                    </div>
                <?php elseif (strlen($i . ') ' . $value3['text']) < 45 && strlen($answer) < 45) : ?>
                    <div class="col-md-12 col-xs-6">
                        <?php echo $i . ') ' . $value3['text']; ?>
                    </div>
                    <div class="col-md-12 col-xs-6" id="result-details">
                        <?php echo $this->element('Quiz/answer-print', array('value3' => $value3, 'value1' => $value1, 'inline' => true)); ?>
                    </div>
                <?php else: ?>
                    <div class="col-md-12 col-xs-12">
                        <?php echo $i . ') ' . $value3['text']; ?>
                    </div>
                    <div class="col-md-12 col-xs-12" id="result-details">
                        <?php echo $this->element('Quiz/answer-print', array('value3' => $value3, 'value1' => $value1)); ?>
                    </div>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>
        <hr>
    <?php endforeach; ?>
</div>