<div class="modal fade" id="reason_<?php echo $quiz['Quiz']['id']; ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <?php echo __('Decline Reason for this sharing'); ?>
            </div>
            <div class="modal-body">
                <?php echo !empty($quiz['Quiz']['comment']) ? h($quiz['Quiz']['comment']) : __('No reason has been provided!'); ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo __('Close'); ?></button>
            </div>
        </div>
    </div>
</div>