<div class="row">
    <div class="col-xs-12 col-md-6">
        <div class="well well-sm">
            <div class="row">
                <div class="col-xs-12 col-md-12">
                    <?php
                    echo $this->Form->input('Choice.{{id}}.text', array(
                        'default' => '{{text}}',
                        'div' => array('class' => 'form-group'),
                        'class' => 'form-control youtube-url',
                        'label' => false,
                        'placeholder' => __('Enter youtube url')
                    ));
                    ?>
                </div>
            </div>
        </div>
    </div>
</div>