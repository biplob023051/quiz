<?php 
    $templateOptions = array('header', 'youtube_video', 'image_url');
    $inlinePointTemplates = array('short_auto', 'short_manual', 'essay');
?>
<tr id="q<?php echo $id ?>"<?php if (in_array($QuestionType['template_name'], $templateOptions)) : ?> class="others_type<?php if ($QuestionType['template_name'] == 'header') : ?> header_type<?php endif; ?>"<?php endif; ?>>
<td>
    <div class="row">
        <div class="col-xs-12 col-md-6">            
            <p>
                <?php if ($QuestionType['template_name'] == 'header') : ?>
                    <span class="h4 header"><?php echo $text; ?></span>
                    <br />
                <?php elseif ($QuestionType['template_name'] == 'youtube_video') : ?>
                    
                <?php elseif ($QuestionType['template_name'] == 'image_url') : ?>
                    
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
                            <?php echo $max_allowed; ?>
                        </span>
                    </p>
                <?php endif; ?>
                <?php if (!empty($case_sensitive)) : ?>
                    <p>
                        <span class="text-muted">
                            <strong>
                                <?php echo __('Demand exact upper- and lowercase letters'); ?>
                            </strong>
                        </span>
                    </p>
                <?php endif; ?>
            </p>
        </div>
        <div class="col-xs-12 col-md-3">
            <?php 
                if (in_array($QuestionType['template_name'], $inlinePointTemplates)) :
                    echo !empty($Choice[0]['points']) ? $Choice[0]['points'] : '0.00';
                endif; 
            ?>
        </div>
    </div>
    <div class="preview"> <!-- before it was choices -->
        <?php
        foreach ($Choice as $c) {
            echo $this->element("Quiz/preview/choice.{$QuestionType['template_name']}", $c);
        }
        ?>
    </div>  
</td>                      
</tr>