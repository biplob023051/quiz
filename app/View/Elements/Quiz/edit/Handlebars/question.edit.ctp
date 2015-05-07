<tr id="q{{id}}">
    <td>      
        <?php echo $this->Form->create('Question'); ?>
        <div class="row">
            <div class="col-md-6 col-xs-12">
                <div class="form-group">
                    <?php
                    echo $this->Form->text('text', array(
                        'class' => 'form-control q-text',
                        'placeholder' => __('Enter the question'),
                        'value' => '{{text}}',
                        'label' => false,
                        'type' => 'text'
                    ));
                    ?>
                </div>
            </div>
            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                <div class="form-group">
                    <?php
                    $_data = array();
                    foreach ($data['QuestionTypes'] as $qt) {
                        $_data[$qt['QuestionType']['id']] = __($qt['QuestionType']['name']);
                    }
                    echo $this->Form->input('Question.question_type_id', array(
                        'options' => $_data,
                        'default' => $data['QuestionTypes'][0]['QuestionType']['id'],
                        'class' => 'form-control choice-type-selector',
                        'label' => false,
                        'id' => 'qs-{{id}}'
                    ));
                    ?>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6 col-xs-12">
                <div class="form-group">
                    <?php
                    echo $this->Form->input('explanation', array(
                        'class' => 'form-control q-explanation',
                        'placeholder' => __('Explanation text'),
                        'value' => '{{explanation}}',
                        'label' => false,
                        'type' => 'text'
                    ));
                    ?>
                </div>           
            </div>
        </div>
        <div class="choices">
            {{#choice Choice}}
            {{choice_tpl}}
            {{/choice}}
        </div>

        <button type="button" class="btn btn-success add-choice" style="margin-top:16px;"><?php echo __('Add Choice') ?></button>
        <?php echo $this->Form->end(); ?>
    </td>
</tr>