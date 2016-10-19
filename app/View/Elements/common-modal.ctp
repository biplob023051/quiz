<div class="modal fade" id="confirmation" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <?php
            echo $this->Form->create('', array(
                'inputDefaults' => array(
                    'label' => array('class' => 'sr-only'),
                    'div' => array('class' => 'form-group'),
                    'class' => 'form-control',
                ),
                'url' => array('controller' => 'quiz', 'action' => 'decline_share', 'admin' => true),
                'id' => 'modal_form'
            ));
        ?>
            <div class="modal-content">
                <div class="modal-header" id="confirmation-header">
                    <?php echo __('Are you sure?'); ?>
                </div>
                <div class="modal-body" id="confirmation-body">
                    <?php echo __('Please submit to continue?'); ?>
                </div>
                <div class="modal-footer" id="confirmation-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo __('Cancel'); ?></button>
                </div>
            </div>
        <?php echo $this->Form->end(); ?>
    </div>
</div>