<div class="container">
    <div class="page-header">
        <h3><?php echo __('Quiz name: ') . $quizDetails['Quiz']['name']; ?></h3>
    </div>
    <?php foreach ($quizDetails['Student'] as $key1 => $value1) : ?>
        <div class="row" id="name_portion">
            <div class="col-md-3 col-xs-4">
                <?php echo '<span class="gray-color">' . __('Student name: ') . '</span>' . $value1['lname'] . ' ' . $value1['fname']; ?>
            </div>
            <div class="col-md-3 col-xs-4">
                <?php echo '<span class="gray-color">' . __('Quiz taken: ') . '</span>' . $value1['submitted']; ?>
            </div>
            <div class="col-md-3 col-xs-2">
                <?php echo '<span class="gray-color">' . __('Class: ') . '</span>' . $value1['class']; ?>
            </div>
            <div class="col-md-3 col-xs-2">
                <?php 
                    foreach ($quizDetails['Ranking'] as $key2 => $value2) { 
                        if ($value1['id'] == $value2['student_id']) {  
                        echo '<span class="gray-color">' . __('Total: ') . '</span>' . ($value2['score']+0) . '/' . ($value2['total']+0);  break; 
                        }  
                    } 
                ?>
            </div>
        </div>
        <div class="row" id="result">
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
                    <hr>
                    <div class="bottom-border"></div>
                <?php elseif (strlen($i . ') ' . $value3['text']) < 45 && strlen($answer) < 45) : ?>
                    <div class="col-md-12 col-xs-9">
                        <?php echo $i . ') ' . $value3['text']; ?>
                    </div>
                    <div class="col-md-12 col-xs-3" id="result-details">
                        <?php echo $this->element('Quiz/answer-print', array('value3' => $value3, 'value1' => $value1, 'inline' => true)); ?>
                    </div>
                    
                    <hr>
                    <div class="bottom-border"></div>
                <?php else: ?>
                    <div class="col-md-12 col-xs-12">
                        <?php echo $i . ') ' . $value3['text']; ?>
                    </div>
                    <div class="col-md-12 col-xs-12" id="result-details">
                        <?php echo $this->element('Quiz/answer-print', array('value3' => $value3, 'value1' => $value1)); ?>
                    </div>
                    
                    <hr>
                    <div class="bottom-border"></div>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>
        <hr>
    <?php endforeach; ?>
</div>

<style type="text/css">
@media print {
    body {
        -webkit-print-color-adjust:exact !important;
    }
    .page-header {
        padding: 0 !important;
        margin: 0 !important;
        border: none !important;
    }
    .page-header h3 {
        color: #1B75BB !important;
        font-size: 25px !important;
        font-weight: normal !important;
        text-align: center !important;
        border: none !important;
    }
    .container {
        padding: 0 !important;
        margin: -60px 0 0 0 !important;   
    }
    .gray-color {
        color: #6D6E70 !important;
    }
    #name_portion {
        background-color: #E6E7E8 !important;
    }
    .bottom-border {
        clear: both !important;
        /*border: .5px solid #D1D2D4 !important;*/ 
        /*border-width: 1px !important; border-style: solid !important; border-color: #D1D2D4 !important;*/
        padding: 1px !important;
    }
    .row {
        border: none !important;
    }
}
</style>