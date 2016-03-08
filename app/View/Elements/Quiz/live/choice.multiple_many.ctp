<div class="row">
    <div class="col-xs-12 col-md-6">
        <div class="checkbox">
            <label>
                <input type="checkbox" <?php if ((is_array($given_answer) && in_array($text, $given_answer)) || ($text == $given_answer)) echo 'checked'; ?> class="form-input" value="<?php echo $text ?>" name="data[Answer][<?php echo $number ?>][text][]" />
                <?php echo $text ?>
            </label>
        </div>
    </div>
</div>