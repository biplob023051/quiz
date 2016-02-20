<?php echo $this->Form->hidden("Answer.{$number}.question_id", array("default" => $id)); ?>
<?php 
    $templateOptions = array('header', 'youtube_video', 'image_url');
?>
<tr id="q<?php if (!in_array($QuestionType['template_name'], $templateOptions)) echo $number; ?>"<?php if (in_array($QuestionType['template_name'], $templateOptions)) : ?> class="others_type"<?php endif; ?>>
    <td>                    
        <div class="row">
            <div class="col-xs-12 col-md-6">
                <p>
                    <?php if (($QuestionType['template_name'] == 'header')) : ?>
                        <span class="h4 header"><?php echo $text; ?></span>
                    <?php elseif (($QuestionType['template_name'] == 'youtube_video')) : ?>
                        <span class="h4 youtube"><?php echo $text; ?></span>
                    <?php elseif (($QuestionType['template_name'] == 'image_url')) : ?>
                        <span class="h4 image-url"><?php echo $text; ?></span>
                    <?php else : ?>
                        <span class="h4"><?php echo '<span class="question_number">' . $number . '</span>. ' .  $text; ?></span>
                    <?php endif; ?>
                    <br />
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