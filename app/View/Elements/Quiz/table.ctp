<?php foreach ($value3['Answer'] as $key4 => $value4) : ?>
    <?php if ($value1['id'] == $value4['student_id']) : ?>
        <?php if (($value3['QuestionType']['id'] == 1) || ($value3['QuestionType']['id'] == 2) || ($value3['QuestionType']['id'] == 3)) : ?>
            <?php if (empty($value4['text'])) : ?>
                <p class="text-danger"><span class="label"><?php echo __('Not Answered'); ?></span></p>
            <?php else : ?>
                <!-- check correct and incorrect -->
                <?php if ($value4['score'] > 0) : ?>
                    <p class="text-success"><?php echo $value4['text'] . ' ' . $value4['score'] . '<br/>'; ?></div>
                <?php else : ?>
                    <p class="text-danger"><?php echo $value4['text'] . ' ' . $value4['score'] . '<br/>'; ?></div>
                <?php endif; ?>     
            <?php endif; ?> 
        <!-- short manual scoring -->
        <?php elseif ($value3['QuestionType']['id'] == 4) : ?>
            <?php if (empty($value4['text'])) : ?>
                <p class="text-danger"><span class="label"><?php echo __('Not Answered'); ?></span></p>
            <?php else : ?>
                <?php echo $value4['text'] . '<br />'; ?>
                <input 
            placeholder="Rate!" 
            type="number" 
            class="form-control update-score" 
            name="<?php echo $value1['id'] ?>"
            question="<?php echo $value3['id'] ?>"
            value="<?php echo empty($value4['score']) ? $value3['QuestionType']['manual_scoring'] : $value4['score']; ?>"
            max="<?php echo empty($value3['Choice'][0]['points']) ? $value3['QuestionType']['manual_scoring'] : $value3['Choice'][0]['points']; ?>"
            /> / <?php echo empty($value3['Choice'][0]['points']) ? $value3['QuestionType']['manual_scoring'] : $value3['Choice'][0]['points']; ?>
                <!-- <input type="button" name="save-essay-score" class="save-essay-score" value="<?php echo __('Save'); ?>" /> -->
            <?php endif; ?>
        <?php else: ?>
            <!-- essay type -->
            <?php if (empty($value4['text'])) : ?>
                <p class="text-danger"><span class="label"><?php echo __('Not Answered'); ?></span></p>
            <?php else : ?>
                <?php echo $value4['text'] . '<br />'; ?>
                <span>
                <input 
            placeholder="Rate!" 
            type="number" 
            class="form-control update-score" 
            name="<?php echo $value1['id'] ?>"
            question="<?php echo $value3['id'] ?>"
            value="<?php echo empty($value4['score']) ? $value3['QuestionType']['manual_scoring'] : $value4['score']; ?>"
            max="<?php echo empty($value3['Choice'][0]['points']) ? $value3['QuestionType']['manual_scoring'] : $value3['Choice'][0]['points']; ?>"
            /> / <?php echo empty($value3['Choice'][0]['points']) ? $value3['QuestionType']['manual_scoring'] : $value3['Choice'][0]['points']; ?>
                <!-- <input type="button" name="save-essay-score" class="save-essay-score" value="<?php echo __('Save'); ?>" /> -->
                </span>
            <?php endif; ?>
        <?php endif; ?>
    <?php endif; ?>

<?php endforeach; ?>