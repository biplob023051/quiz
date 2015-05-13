<div class="modal fade" id="confirm-submit" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <?php echo __('Turn in your quiz?'); ?>
            </div>
            <div class="modal-body">
                <?php echo __('All questions answered. Turn in your quiz?'); ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo __('Cancel'); ?></button>
                <button type="button" class="btn btn-danger btn-ok" id="confirmed"><?php echo __('Confirm'); ?></button>
            </div>
        </div>
    </div>
</div>