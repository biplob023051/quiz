<div class="modal fade read-essay" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <?php //foreach ($questions as $key_question => $question) : ?>
                    <?php echo '<b>' . $value3['text'] . '</b>'; ?>
                <?php //endforeach; ?>
            </div>
            <div class="modal-body">
                <?php echo $value4['text']; ?>
            </div>
            <div class="modal-footer">
                <span class="all-button">
                    <?php if ($value4['score'] == NULL) : ?>
                        <input 
            placeholder="<?php echo __('Rate!'); ?>" 
            type="number" 
            class="form-control update-score" 
            name="<?php echo $value1['id'] ?>"
            question="<?php echo $value3['id'] ?>"
            value=""
            current-score=""
            max="<?php echo empty($value3['Choice'][0]['points']) ? $value3['QuestionType']['manual_scoring'] : $value3['Choice'][0]['points']; ?>"
            /> / 
                    <?php else : ?>
                        <input 
            placeholder="<?php echo __('Rate!'); ?>" 
            type="number" 
            class="form-control update-score" 
            name="<?php echo $value1['id'] ?>"
            question="<?php echo $value3['id'] ?>"
            value="<?php echo empty($value4['score']) ? 0 : $value4['score']; ?>"
            current-score="<?php echo empty($value4['score']) ? 0 : $value4['score']; ?>"
            max="<?php echo empty($value3['Choice'][0]['points']) ? $value3['QuestionType']['manual_scoring'] : $value3['Choice'][0]['points']; ?>"
            /> / 
                    <?php endif; ?>
                    <?php echo empty($value3['Choice'][0]['points']) ? ($value3['QuestionType']['manual_scoring']+0) : ($value3['Choice'][0]['points']+0); ?>
                    <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo __('Close'); ?></button>
                </span>
            </div>
        </div>
    </div>
</div>