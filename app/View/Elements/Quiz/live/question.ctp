<?php 
    $templateOptions = array('header', 'youtube_video', 'image_url');
    if (!in_array($QuestionType['template_name'], $templateOptions))
    echo $this->Form->hidden("Answer.{$number}.question_id", array("value" => $id));
?>
<tr id="q<?php if (!in_array($QuestionType['template_name'], $templateOptions)) echo $number; ?>"<?php if (in_array($QuestionType['template_name'], $templateOptions)) : ?> class="others_type<?php if ($QuestionType['template_name'] == 'header') : ?> header_type<?php endif; ?>"<?php endif; ?>>
    <td>                    
        <div class="row">
            <div class="col-xs-12 col-md-6">
                <p>
                    <?php if (($QuestionType['template_name'] == 'header')) : ?>
                        <span class="h4 header"><?php echo $text; ?></span>
                        <br />
                    <?php elseif (($QuestionType['template_name'] == 'youtube_video')) : ?>
                        
                    <?php elseif (($QuestionType['template_name'] == 'image_url')) : ?>
                        
                    <?php else : ?>
                        <span class="h4"><?php echo '<span class="question_number">' . $number . '</span>. ' .  $text; ?></span>
                        <br />
                    <?php endif; ?>
                    <span class="text-muted"><?php echo $explanation ?></span>
                    <?php if (!empty($max_allowed)) : ?>
                        <p>
                            <span class="text-muted">
                                <strong>
                                    <?php echo __('Choose at most'); ?>
                                </strong>
                                <span class="max_allowed"><?php echo $max_allowed; ?></span>
                            </span>
                        </p>
                    <?php endif; ?>
                </p>
            </div>
        </div>
        <div class="choices">
            <?php
            foreach ($Choice as $c) {
                $c['number'] = $number;
                $c['given_answer'] = $given_answer;
                echo $this->element("Quiz/live/choice.{$QuestionType['template_name']}", $c);
            }
            ?>
        </div>
    </td>
</tr>