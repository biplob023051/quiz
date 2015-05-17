<div class="modal fade" id="confirm-delete" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <?php echo __('Remove answer?'); ?>
            </div>
            <div class="modal-body">
                <?php echo __('Are you sure you want to remove John Smiths answer with points 0/30?'); ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo __('Cancel'); ?></button>
                <button type="button" class="btn btn-danger btn-ok" value"" id="confirmed"><?php echo __('Confirm'); ?></button>
            </div>
        </div>
    </div>
</div>