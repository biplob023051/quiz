{{#if points}}
    <div class="row choice-<?php echo '{{id}}'; ?>">
        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
            <div class="well well-sm">
                <div class="row">
                    <div class="col-lg-1 col-md-1 col-sm-1 col-xs-1" >
                        <label>
                            <input type="checkbox"  disabled  />
                        </label>
                    </div>
                    <div class="col-lg-4 col-md-4 col-sm-4 col-xs-5">
                        <?php
                        echo $this->Form->input('Choice.{{id}}.text', array(
                            'default' => '{{text}}',
                            'class' => 'form-control c-text',
                            'label' => false,
                            'placeholder' => __('Choice')
                        ));
                        ?>
                    </div>
                    <div class="col-lg-3 col-md-3 col-sm-6 col-xs-5">
                        <?php
                        echo $this->Form->input('Choice.{{id}}.points', array(
                            'class' => 'form-control c-points',
                            'placeholder' => __('Points'),
                            'default' => '{{points}}',
                            'label' => false
                        ));
                        ?>
                    </div>
                    <?php
                    echo $this->Form->input('Choice.{{id}}.weight', array(
                        'class' => 'c-weight',
                        'type' => 'hidden',
                        'default' => '{{weight}}'
                    ));
                    ?>
                    <div class="col-lg-1 col-md-1 col-sm-2 col-xs-2">
                        <?php echo $this->Form->button('<i class="glyphicon close"></i>', array('type' => 'button', 'choice' => '{{id}}', 'class' => 'remove-choice', 'title' => __('Remove choice'))); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
{{else}}
    <div class="row choice-<?php echo '{{id}}'; ?>">
        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
            <div class="well well-sm">
                <div class="row">
                    <div class="col-lg-1 col-md-1 col-sm-1 col-xs-1" >
                        <label>
                            <input type="checkbox"  disabled  />
                        </label>
                    </div>
                    <div class="col-lg-4 col-md-4 col-sm-4 col-xs-5">
                        <?php
                        echo $this->Form->input('Choice.{{id}}.text', array(
                            'default' => '{{text}}',
                            'class' => 'form-control c-text',
                            'label' => false,
                            'placeholder' => __('Choice')
                        ));
                        ?>
                    </div>
                    <div class="col-lg-3 col-md-3 col-sm-6 col-xs-5">
                        <?php
                        echo $this->Form->input('Choice.{{id}}.points', array(
                            'class' => 'form-control c-points',
                            'placeholder' => __('Points'),
                            'default' => 0,
                            'label' => false
                        ));
                        ?>
                    </div>
                    <?php
                    echo $this->Form->input('Choice.{{id}}.weight', array(
                        'class' => 'c-weight',
                        'type' => 'hidden',
                        'default' => '{{weight}}'
                    ));
                    ?>
                    <div class="col-lg-1 col-md-1 col-sm-2 col-xs-2">
                        <?php echo $this->Form->button('<i class="glyphicon close"></i>', array('type' => 'button', 'choice' => '{{id}}', 'class' => 'remove-choice', 'title' => __('Remove choice'))); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
{{/if}}
