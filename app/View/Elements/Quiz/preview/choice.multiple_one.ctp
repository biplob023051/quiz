<div class="row">
    <div class="col-xs-6 col-md-6">
        <div class="radio">
            <label>
                <input type="radio" disabled value="<?php echo $id ?>" name="questions[<?php echo $question_id ?>][correct_choice]" />
                <?php echo $text ?>
            </label>
        </div>
    </div>
    <div class="col-xs-6 col-md-6">
        <?php echo $points; ?>
    </div>
</div>