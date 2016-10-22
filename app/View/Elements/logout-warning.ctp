<div class="modal fade" id="logout-warn" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header" id="logout-warn-header">
                <?php echo __('You are going to be logout...?'); ?>
            </div>
            <div class="modal-body" id="logout-warn-body">
                <?php echo __('You haven\'t been active in 15 minutes. For your security you\'ll be logged out in') . ' '; ?><span class="text-danger" id="s_timer"></span>
            </div>
            <div class="modal-footer" id="logout-warn-footer">
                <!-- <button type="button" class="btn btn-default" data-dismiss="modal"><?php //echo __('Cancel'); ?></button> -->
                <button type="button" id="stay-signin" class="btn btn-success"><?php echo __('Stay Signin'); ?></button>
            </div>
        </div>
    </div>
</div>