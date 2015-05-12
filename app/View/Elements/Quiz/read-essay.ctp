<div class="modal fade" id="read-essay" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <?php //foreach ($questions as $key_question => $question) : ?>
                    <?php echo $value3['text']; ?>
                <?php //endforeach; ?>
            </div>
            <div class="modal-body">
                <?php echo $value4['text']; ?>
            </div>
            <div class="modal-footer">
                <span>
                    <input 
            placeholder="Rate!" 
            type="number" 
            class="form-control update-score" 
            name="<?php echo $value1['id'] ?>"
            question="<?php echo $value3['id'] ?>"
            value="<?php echo empty($value4['score']) ? '' : $value4['score']; ?>"
            current-score="<?php echo empty($value4['score']) ? 0 : $value4['score']; ?>"
            max="<?php echo empty($value3['Choice'][0]['points']) ? $value3['QuestionType']['manual_scoring'] : $value3['Choice'][0]['points']; ?>"
            /> / <?php echo empty($value3['Choice'][0]['points']) ? $value3['QuestionType']['manual_scoring'] : $value3['Choice'][0]['points']; ?>
                </span>
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo __('Close'); ?></button>
            </div>
        </div>
    </div>
</div>