<?php echo $this->Form->hidden("Answer.{$number}.question_id", array("default" => $id)); ?>
<tr id="q<?php echo $number ?>">
    <td>                    
        <div class="row">
            <div class="col-xs-12 col-md-6">            
                <p>
                    <span class="h4"><?php echo $number ?>) <?php echo $text ?></span><br />
                    <span class="text-muted"><?php echo $explanation ?></span>
                </p>
            </div>
        </div>
        <div class="choices">
            <?php
            foreach ($Choice as $c) {
                $c['number'] = $number;
                echo $this->element("Quiz/live/choice.{$QuestionType['template_name']}", $c);
            }
            ?>
        </div>
    </td>
</tr>