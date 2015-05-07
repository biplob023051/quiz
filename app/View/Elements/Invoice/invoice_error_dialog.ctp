<!-- Modal -->
<div class="modal fade" id="invoice-error-dialog" tabindex="-1" role="dialog" aria-labelledby="invoice-error-dialog-title" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                <h4 class="modal-title" id="invoice-error-dialog-title"><?php echo __('Upgrade Account') ?></h4>
            </div>
            <div class="modal-body">
                <?php echo __('Send invoice failed, please try close this dialog and click Upgrade again.') ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-dismiss="modal"><?php echo __('Close') ?></button>
            </div>
        </div>
    </div>
</div>