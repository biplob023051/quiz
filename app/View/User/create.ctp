<?php

$this->assign('title', __('Create Account'));
$form_data = $this->Session->read('UserCreateFormData');

echo $this->Session->flash('notification');
echo $this->Session->flash('error');
?>
<div class="row">
    <div class="col-lg-6 col-md-7 col-sm-12 col-xs-12">
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
        ));

        echo $this->Form->input('name', array(
            'default' => $form_data['User']['name']
        ));

        echo $this->Form->input('email', array(
            'default' => $form_data['User']['email']
        ));

        echo $this->Form->input('password', array(
            'type' => 'password'
        ));

        echo $this->Form->input('passwordVerify', array(
            'type' => 'password'
        ));
        ?>

        <div class="form-group required">
            <label for="UserCaptcha" class="col-sm-4 control-label"><?php echo $captcha; ?></label>
            <div class="col-md-7 col-xs-12">
                <?php echo $this->Form->input('captcha', array('label' => false)); ?>
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
            <?php echo $this->Html->image('bg-moniter.png', array('class' => 'img-responsive')); ?>
        </div>
    </div>
</div>