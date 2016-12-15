<div class="row">
    <div class="col-xs-12 col-md-6">
        <div class="radio">
            <label>
                <input type="radio" class="form-input tick-mark" <?php if ($text == $given_answer) echo 'checked'; ?> value="<?php echo $text ?>" name="data[Answer][<?php echo $number ?>][text]" />
                <?php echo $text; ?>
            </label>
        </div>
    </div>
</div>