<div class="modal fade" id="buy-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <?php echo __('Create Account And Buy'); ?>
            </div>
            <div class="modal-body">
                <div class="alert alert-danger" id="error-message" style="display: none;"></div>
                <div id="email-exist" style="display: none;">
                    <div class="col-sm-4 col-xs-12"><?php echo __('Already Registered?'); ?></div>
                    <div class="col-md-4 col-xs-12">
                        <?php echo $this->Html->link(__('Login and Buy'), '/user/login'); ?>
                    </div>
                    <div class="col-md-4 col-xs-12">
                        <?php echo $this->Html->link(__('Password Recover'), '/user/password_recover'); ?>
                    </div>
                </div>
                <br />
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
                        'novalidate' => 'novalidate',
                        'url' => array('controller'=>'user', 'action'=>'buy_create')
                    ));

                    echo $this->Form->input('name', array(
                        'placeholder' => __('Enter Your Name')
                    ));

                    echo $this->Form->input('email', array(
                        'placeholder' => __('Enter Valid Email')
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

                    echo $this->Form->hidden('package', array('value' => $package));
                ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo __('Cancel'); ?></button>
                <?php
                    echo $this->Form->end(array(
                        'label' => __('Submit'),
                        'class' => 'btn btn-danger btn-ok',
                        'div' => false
                    ));
                ?>
            </div>
        </div>
    </div>
</div>