<?php
$this->Html->script('password-recover', array('inline' => false));
echo $this->Session->flash('notification');
echo $this->Session->flash('error');
?>
<div class="row">
    <div class="col-lg-6 col-md-7 col-sm-12 col-xs-12">
        <div class="alert alert-danger" id="error-message" style="display: none;"></div>
        <?php
            echo $this->Form->create('User', array(
                'class' => 'form-horizontal',
                'inputDefaults' => array(
                    'class' => 'form-control',
                    'div' => array('class' => 'form-group'),
                    'label' => array('class' => 'col-sm-4 control-label'),
                    'between' => '<div class="col-md-7 col-xs-12">',
                    'after' => '</div>'
                ),
                'novalidate' => 'novalidate'
            ));

            echo $this->Form->input('id');
            echo $this->Form->input('password', array(
                'placeholder' => __('Enter New Password'),
                'type' => 'password',
                'data-toggle' => 'tooltip',
                'data-placement' => 'bottom',
                'data-original-title' => __('Password must be 8 characters long')
            ));
            echo $this->Form->input('passwordVerify', array('placeholder' => __('New Password Verify'),'type'=>'password'));

            echo $this->Form->end(array(
                'label' => __("Reset"),
                'div' => array('class' => 'col-md-7 col-md-offset-4 col-xs-12'),
                'before' => '<div class="form-group">',
                'after' => '</div>',
                'class' => 'btn btn-success btn-block btn-lg'
            ));
        ?>
    </div>
    <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
        <div class="text-center">
            <?php echo $this->Html->image('bg-moniter.png', array('class' => 'img-responsive')); ?>
        </div>
    </div>
</div>

<script id="app-data" type="application/json">
    <?php
    echo json_encode(array(
        'baseUrl' => $this->Html->url('/', true)
    ));
    ?>
</script>

<script type="text/javascript">
    var lang_strings = <?php echo json_encode($lang_strings) ?>;
</script>