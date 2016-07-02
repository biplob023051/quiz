<?php

$this->Html->script(array(
    'user-create'
    ), array('inline' => false)
);

$this->assign('title', __('Create Account'));
$form_data = $this->Session->read('UserCreateFormData');

echo $this->Session->flash('notification');
echo $this->Session->flash('error');
?>
<div class="row">
    <div class="col-lg-6 col-md-7 col-sm-12 col-xs-12">
        <div class="alert alert-danger" id="error-message" style="display : none;"></div>
        <div id="email-exist" style="display : none;">
            <div class="col-sm-4 col-xs-12"><?php echo __('Already Registered?'); ?></div>
            <div class="col-md-4 col-xs-12">
                <?php echo $this->Html->link(__('Login'), '/user/login'); ?>
            </div>
            <div class="col-md-4 col-xs-12">
                <?php echo $this->Html->link(__('Password Recover'), '/user/password_recover'); ?>
            </div>
        </div>
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

        echo $this->Form->input('name', array(
            'default' => $form_data['User']['name'],
            'placeholder' => __('Enter Your Name')
        ));

        echo $this->Form->input('email', array(
            'default' => $form_data['User']['email'],
            'placeholder' => __('Enter Valid Email'),
            'data-toggle' => 'tooltip',
            'data-placement' => 'bottom',
            'data-original-title' => __('We\'ll send a confirmation email there')
        ));

        echo $this->Form->input('password', array(
            'type' => 'password',
            'placeholder' => __('Enter Password'),
            'data-toggle' => 'tooltip',
            'data-placement' => 'bottom',
            'data-original-title' => __('Password must be 8 characters long')
        ));

        echo $this->Form->input('passwordVerify', array(
            'type' => 'password',
            'placeholder' => __('Password Verify')
        ));
        ?>

        <div class="form-group required">
            <label for="UserCaptcha" class="col-sm-4 control-label"><?php echo $captcha; ?></label>
            <div class="col-md-7 col-xs-12">
                <?php 
                    echo $this->Form->input('captcha', array(
                        'label' => false,
                        'placeholder' => __('Enter result')
                    )); 
                ?>
            </div>
        </div>
       
        <?php
        echo $this->Form->end(array(
            'label' => __("Create Account & Log In"),
            'div' => array('class' => 'col-md-7 col-md-offset-4 col-xs-12'),
            'before' => '<div class="form-group">',
            'after' => '</div>',
            'class' => 'btn btn-success btn-block btn-lg'
        ));
        ?>
    </div>
    <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
        <div class="text-center">
            <?php if (!empty($create_video)) : ?>
                <a href="javascript:void(0)" id="play_video"><img src="<?php echo $this->Quiz->getHelpPicture($create_video, 'videos'); ?>" class="img-responsive"></a>
                <?php echo $this->element('User/video_modal', array('create_video' => $create_video)); ?>
            <?php else: ?>
                <?php echo $this->Html->image('bg-moniter.png', array('class' => 'img-responsive')); ?>
            <?php endif; ?>
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
    <?php if (!empty($create_video)) : ?>
        var url_src = <?php echo json_encode($create_video['Help']['url_src']) ?>;
    <?php else: ?>
        var url_src = '';
    <?php endif; ?>

</script>

<style type="text/css">
    .form-group.required .control-label:after {
        content:"*";
        color:red;
    }
</style>