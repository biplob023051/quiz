<div class="modal fade" id="confirm-submit" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog" style="width: 92%;">
        <div class="modal-content">
            <div class="modal-header" style="padding-top: 3px; padding-bottom: 3px;">
                <?php echo __('Turn in your quiz?'); ?>
            </div>
            <div class="modal-body" style="padding-top: 3px; padding-bottom: 3px;">
                <?php echo __('All questions answered. Turn in your quiz?'); ?>
            </div>
            <div class="modal-footer" style="padding-top: 3px; padding-bottom: 3px;">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo __('Cancel'); ?></button>
                <button type="button" class="btn btn-danger btn-ok" id="confirmed"><?php echo __('Confirm'); ?></button>
            </div>
        </div>
    </div>
</div>