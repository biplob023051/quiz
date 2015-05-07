<div class="row">
    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
        <div class="well well-sm">
            <div class="row">
                <div class="col-xs-12 col-md-12">
                    <?php
                    echo $this->Form->input('Choice.{{id}}.text', array(
                        'default' => '{{text}}',
                        'div' => array('class' => 'form-group'),
                        'class' => 'form-control c-text',
                        'label' => false,
                        'placeholder' => __('Corect answers separated by comma')
                    ));
                    ?>
                </div>
            </div>
            <br />
            <div class="row">
                <div class="col-xs-7 col-md-4 col-xs-offset-5 col-md-offset-8">
                    <?php
                    echo $this->Form->input('Choice.{{id}}.points', array(
                        'default' => '{{points}}',
                        'div' => array('class' => 'form-group'),
                        'class' => 'form-control c-points',
                        'label' => false,
                        'placeholder' => __('Points')
                    ));
                    ?>
                </div>
            </div>
        </div>
    </div>
</div>