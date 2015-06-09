<?php
$this->assign('title', __('Login'));
?>

<?php echo $this->Session->flash('notification'); ?>
<?php echo $this->Session->flash('error'); ?>


<div id="login-container">
    <?php
    echo $this->Form->create('User', array(
        'inputDefaults' => array(
            'div' => array('class' => 'form-group'),
            'class' => 'form-control',
        )
    ));
    ?>
    <div class="row">
        <div class="col-md-12 col-xs-12">
            <?php
            echo $this->Form->input('email', array(
                'label' => __("Email")
            ));
            ?>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12 col-xs-12">
            <?php
            echo $this->Form->input('password', array(
                'label' => __("Password")
            ));
            ?>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12 col-xs-12">
            <div class="form-group">
                <button type="submit" class="btn btn-success btn-lg btn-block"><?php echo __('Login') ?></button>
            </div>
        </div>
        <!--<div class="col-md-3 col-xs-12">
            <div class="form-group">
        <?php
        echo $this->Html->link(__('Register'), '/user/create', array(
            'class' => 'btn btn-primary btn-lg btn-block'
        ));
        ?>
            </div>
        </div>-->
    </div>

    <?php echo $this->Form->end(); ?>
</div>

<p class="text-center text-muted">
    <?php echo __('If you don’t have account?'); ?> 
    <?php echo $this->Html->link(__('Register Now!'), '/user/create'); ?>
</p>
<p class="text-center text-muted">
    <?php echo __('If you forgot password?'); ?> 
    <?php echo $this->Html->link(__('Password Recover'), '/user/password_recover'); ?>
</p>



