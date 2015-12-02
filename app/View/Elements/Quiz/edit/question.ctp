<tr id="q<?php echo $id ?>">
<script type="application/json">
<?php
echo json_encode(array(
    'id' => $id,
    'text' => $text,
    'explanation' => $explanation,
    'QuestionType' => $QuestionType,
    'Choice' => $Choice
));
?>
</script>
<td>
    <div class="row">
        <div class="col-xs-12 col-md-6">            
            <p>
                <span class="h4"><?php echo '<span class="question_number">' . $number . '</span>. ' .  $text ?></span><br />
                <span class="text-muted"><?php echo $explanation ?></span>
            </p>
        </div>
        <div class="col-xs-12 col-md-3">
            <div class="btn-group preview-btn">
                <button type="button" class="btn btn-default btn-sm edit-question" id="edit-q<?php echo $id ?>" title="<?php echo __('Edit question'); ?>">
                    <i class="glyphicon pencil"></i>
                </button>
                <button type="button" class="btn btn-danger btn-sm delete-question" id="delete-q<?php echo $id ?>" title="<?php echo __('Remove question'); ?>">
                    <i class="glyphicon trash"></i>
                </button>
            </div>
        </div>
    </div>
    <div class="choices">
        <?php
        foreach ($Choice as $c) {
            echo $this->element("Quiz/edit/choice.{$QuestionType['template_name']}", $c);
        }
        ?>
    </div>  
</td>                      
</tr>