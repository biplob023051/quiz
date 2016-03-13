<?php foreach ($value3['Answer'] as $key4 => $value4) : ?>
    <?php if ($value1['id'] == $value4['student_id']) : ?>
        <?php if (($value3['QuestionType']['id'] == 1) || ($value3['QuestionType']['id'] == 3)) : ?>
            <?php if (empty($value4['text'])) : ?>
                <p class="text-danger"><span class="label"><?php echo __('Not Answered'); ?></span></p>
            <?php else : ?>
                <!-- check correct and incorrect -->
                <?php if ($value4['score'] > 0) : ?>
                    <p class="text-success"><?php echo $value4['text'] . ' <span class="score">' . ($value4['score']+0) . '</span><br/>'; ?></div>
                <?php elseif ($value4['score'] == 0) : ?>
                    <p class="text-warning"><?php echo $value4['text'] . ' <span class="score">' . ($value4['score']+0) . '</span><br/>'; ?></div>
                <?php else : ?>
                    <p class="text-danger"><?php echo $value4['text'] . ' <span class="score">' . ($value4['score']+0) . '</span><br/>'; ?></div>
                <?php endif; ?>     
            <?php endif; ?>
        <!-- automatic rating -->
        <?php elseif ($value3['QuestionType']['id'] == 2) : ?>
            <?php if (empty($value4['text'])) : ?>
                <p class="text-danger"><span class="label"><?php echo __('Not Answered'); ?></span></p>
            <?php else : ?>
                <!-- check correct and incorrect -->
                <?php if ($value4['score'] > 0) : ?>
                    <p class="text-success"><?php echo $value4['text'] . ' <span class="score automatic">' . ($value4['score']+0) . '</span><input type="number" class="form-control automatic_rating update-score" value="'. $value4['score'] .'" name="' . $value1['id'] . '" question="' . $value3['id'] . '" current-score="' . $value4['score'] . '" max="' . $value3['Choice'][0]['points'] . '"><br/>'; ?></div>
                <?php elseif ($value4['score'] == 0) : ?>
                    <p class="text-warning"><?php echo $value4['text'] . ' <span class="score automatic">' . ($value4['score']+0) . '</span><input type="number" class="form-control automatic_rating update-score" value="'. $value4['score'] .'" name="' . $value1['id'] . '" question="' . $value3['id'] . '" current-score="' . $value4['score'] . '" max="' . $value3['Choice'][0]['points'] . '"><br/>'; ?></div>
                <?php else : ?>
                    <p class="text-danger"><?php echo $value4['text'] . ' <span class="score automatic">' . ($value4['score']+0) . '</span><input type="number" class="form-control automatic_rating update-score" value="'. $value4['score'] .'" name="' . $value1['id'] . '" question="' . $value3['id'] . '" current-score="' . $value4['score'] . '" max="' . $value3['Choice'][0]['points'] . '"><br/>'; ?></div>
                <?php endif; ?>     
            <?php endif; ?> 
        <!-- short manual scoring -->
        <?php elseif ($value3['QuestionType']['id'] == 4) : ?>
            <?php if (empty($value4['text'])) : ?>
                <p class="text-danger"><span class="label"><?php echo __('Not Answered'); ?></span></p>
            <?php else : ?>
                <?php echo $value4['text'] . '<br />'; ?>
                <?php if (!empty($value3['Choice'][0]['points'])) : ?>
                    <input 
                placeholder="<?php echo __('Rate!'); ?>" 
                type="number" 
                class="form-control update-score" 
                name="<?php echo $value1['id'] ?>"
                question="<?php echo $value3['id'] ?>"
                value="<?php echo $value4['score']; ?>"
                current-score="<?php echo empty($value4['score']) ? '' : $value4['score']; ?>"
                max="<?php echo empty($value3['Choice'][0]['points']) ? $value3['QuestionType']['manual_scoring'] : $value3['Choice'][0]['points']; ?>"
                /> / <?php echo empty($value3['Choice'][0]['points']) ? $value3['QuestionType']['manual_scoring']+0 : ($value3['Choice'][0]['points']+0); ?>
                <?php endif; ?>
            <?php endif; ?>
        <?php else: ?>
            <!-- essay type -->
            <?php if (empty($value4['text'])) : ?>
                <p class="text-danger"><span class="label"><?php echo __('Not Answered'); ?></span></p>
            <?php else : ?>
                <button type="button" class="btn btn-danger btn-sm read-essay" quiz-id="">
                    <?php echo __('READ'); ?>
                </button>
                <span class="essay-points">
                    <?php echo ($value4['score'] == '') ? ' <span style="display: none;" class="score"></span>' : ' <span class="score">' . ($value4['score']+0) . '</span>'; ?>
                </span>
                <?php echo $this->element('Quiz/read-essay', array('value1' => $value1, 'value3' => $value3, 'value4' => $value4)); ?>
            <?php endif; ?>
        <?php endif; ?>
    <?php endif; ?>

<?php endforeach; ?>